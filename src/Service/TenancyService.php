<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Charge;
use App\Entity\Handover;
use App\Entity\RentalUnit;
use App\Entity\Tenancy;
use App\Entity\Tenant;
use App\Repository\ChargeRepository;
use App\Repository\RentalUnitRepository;
use App\Repository\TenancyRepository;
use App\Repository\TenantRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Mietverhältnisse: Anlage, Aktivierung, Mieter, Übergaben, Sollstellungen ...
 */
class TenancyService
{
    private $em;
    private $tenancyRepository;
    private $rentalUnitRepository;
    private $tenantRepository;
    private $chargeRepository;
    private $appointmentService;

    public function __construct(
        EntityManagerInterface $em,
        TenancyRepository $tenancyRepository,
        RentalUnitRepository $rentalUnitRepository,
        TenantRepository $tenantRepository,
        ChargeRepository $chargeRepository,
        AppointmentService $appointmentService
    ) {
        $this->em = $em;
        $this->tenancyRepository = $tenancyRepository;
        $this->rentalUnitRepository = $rentalUnitRepository;
        $this->tenantRepository = $tenantRepository;
        $this->chargeRepository = $chargeRepository;
        $this->appointmentService = $appointmentService;
    }

    /**
     * Neuen Mietvertrag (Entwurf) für eine Einheit anlegen.
     */
    public function createTenancy(Tenancy $tenancy): Tenancy
    {
        $unit = $tenancy->getUnit();

        // Einheit darf nur einmal vermietet sein
        if (count($this->tenancyRepository->findActiveForUnit($unit)) > 0) {
            throw new \RuntimeException('Für diese Einheit besteht bereits ein aktives Mietverhältnis.');
        }

        if (count($this->tenancyRepository->findOverlapping($unit, $tenancy->getStartDate(), $tenancy->getEndDate())) > 0) {
            throw new \RuntimeException('Der Vertragszeitraum überschneidet sich mit einem anderen Mietverhältnis.');
        }

        if ($tenancy->getEndDate() && $tenancy->getEndDate() < $tenancy->getStartDate()) {
            throw new \RuntimeException('Vertragsende liegt vor Vertragsbeginn.');
        }

        $tenancy->setContractNumber($this->tenancyRepository->nextContractNumber());

        // Mietwerte aus der Einheit übernehmen, falls nicht angegeben
        if (!$tenancy->getColdRent()) {
            $tenancy->setColdRent($unit->getColdRent());
        }
        if (!$tenancy->getServiceChargeAdvance()) {
            $tenancy->setServiceChargeAdvance($unit->getServiceChargeAdvance());
        }
        if (!$tenancy->getDeposit()) {
            $tenancy->setDeposit(round((float) $tenancy->getColdRent() * 3, 2));
        }

        $tenancy->setStatus(Tenancy::DRAFT);
        $unit->setStatus(RentalUnit::RESERVED);

        $this->em->persist($tenancy);
        $this->em->flush();

        return $tenancy;
    }

    /**
     * Vertrag unterschrieben: Mietverhältnis aktivieren.
     */
    public function activate(Tenancy $tenancy, ?string $username = null): void
    {
        if ($tenancy->getStatus() !== Tenancy::DRAFT) {
            throw new \RuntimeException('Nur Entwürfe können aktiviert werden.');
        }
        if ($tenancy->getTenants()->count() === 0) {
            throw new \RuntimeException('Mindestens ein Mieter muss eingetragen sein.');
        }

        $tenancy->setStatus(Tenancy::ACTIVE);

        $unit = $tenancy->getUnit();
        $unit->setStatus(RentalUnit::RENTED);
        $unit->setLastModifiedBy($username);

        $this->appointmentService->createHandover($tenancy, Handover::MOVE_IN);

        // erste Sollstellung für den Monat des Mietbeginns
        $this->createChargesForMonth($tenancy, $tenancy->getStartDate()->format('Y-m'));

        $this->em->flush();
    }

    public function addTenant(Tenancy $tenancy, Tenant $tenant): void
    {
        $tenancy->addTenant($tenant);
        $this->em->flush();
    }

    public function removeTenant(Tenancy $tenancy, Tenant $tenant): void
    {
        if ($tenancy->getTenants()->count() <= 1 && $tenancy->getStatus() === Tenancy::ACTIVE) {
            throw new \RuntimeException('Der letzte Mieter eines aktiven Vertrags kann nicht entfernt werden.');
        }
        $tenancy->removeTenant($tenant);
        $this->em->flush();
    }

    /**
     * Übergabe bzw. Rückgabe dokumentieren. Bei Rückgabe wird das
     * Mietverhältnis beendet und die Einheit wieder freigegeben.
     */
    public function recordHandover(Tenancy $tenancy, Handover $handover, ?string $username = null): void
    {
        $handover->setTenancy($tenancy);
        $this->em->persist($handover);

        if ($handover->getType() === Handover::MOVE_OUT) {
            $tenancy->setStatus(Tenancy::ENDED);
            if (!$tenancy->getEndDate()) {
                $tenancy->setEndDate($handover->getDate());
            }
            $unit = $tenancy->getUnit();
            // mit Mängeln geht die Einheit in die Renovierung
            $unit->setStatus($handover->getDefects() ? RentalUnit::RENOVATION : RentalUnit::AVAILABLE);
            $unit->setLastModifiedBy($username);
        }

        $this->em->flush();
    }

    /**
     * Mietverhältnis außerordentlich beenden (Aufhebung / fristlose Kündigung).
     */
    public function terminateImmediately(Tenancy $tenancy, \DateTimeInterface $endDate): void
    {
        $tenancy->setStatus(Tenancy::TERMINATED);
        $tenancy->setEndDate($endDate);
        $tenancy->setNoticeGiven(true);
        $tenancy->getUnit()->setStatus(RentalUnit::NOTICE_GIVEN);
        $this->appointmentService->createHandover($tenancy);
        $this->em->flush();
    }

    /**
     * Sollstellungen für alle laufenden Verträge eines Monats erzeugen.
     *
     * @return int Anzahl neu erzeugter Sollstellungen
     */
    public function generateMonthlyCharges(string $month): int
    {
        $count = 0;
        foreach ($this->tenancyRepository->findCurrent() as $tenancy) {
            if ($tenancy->getStartDate()->format('Y-m') > $month) {
                continue;
            }
            if ($tenancy->getEndDate() && $tenancy->getEndDate()->format('Y-m') < $month) {
                continue;
            }
            if ($this->chargeRepository->existsForMonth($tenancy, $month)) {
                continue;
            }
            $count += $this->createChargesForMonth($tenancy, $month);
        }
        $this->em->flush();

        return $count;
    }

    private function createChargesForMonth(Tenancy $tenancy, string $month): int
    {
        $due = \DateTime::createFromFormat('Y-m-d', $month.'-03');

        $cold = new Charge();
        $cold->setTenancy($tenancy)->setMonth($month)->setPosition('COLD_RENT')
            ->setDueDate($due)->setAmount($tenancy->getColdRent());
        $this->em->persist($cold);

        $advance = new Charge();
        $advance->setTenancy($tenancy)->setMonth($month)->setPosition('SERVICE_CHARGE')
            ->setDueDate(clone $due)->setAmount($tenancy->getServiceChargeAdvance());
        $this->em->persist($advance);

        return 2;
    }

    /**
     * Kaution als Einnahme für den Eigentümer verbuchen.
     * (Vereinfachung - eigentlich Treuhandkonto, siehe Ticket #88)
     */
    public function bookDeposit(Tenancy $tenancy): void
    {
        $property = $tenancy->getUnit()->getBuilding()->getProperty();

        $booking = new Booking();
        $booking->setType(Booking::INCOME)
            ->setProperty($property)
            ->setOwner($property->getOwner())
            ->setUnit($tenancy->getUnit())
            ->setTenancy($tenancy)
            ->setAmount($tenancy->getDeposit())
            ->setCategory('SONSTIGES')
            ->setDescription('Kaution '.$tenancy->getContractNumber());
        $this->em->persist($booking);
        $this->em->flush();
    }
}
