<?php

namespace App\Service;

use App\Entity\Appointment;
use App\Entity\Prospect;
use App\Entity\RentalUnit;
use App\Entity\Tenant;
use App\Entity\Viewing;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Vermietung: Interessenten und Besichtigungen
 */
class RentalService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function planViewing(Viewing $viewing): void
    {
        $viewing->getProspect()->setStatus('VIEWING_PLANNED');

        $appointment = new Appointment();
        $appointment->setType('BESICHTIGUNG');
        $appointment->setTitle('Besichtigung '.$viewing->getUnit()->getUnitNumber().' mit '.$viewing->getProspect()->getName());
        $appointment->setStartsAt($viewing->getDateTime());
        $appointment->setEndsAt((clone $viewing->getDateTime())->modify('+30 minutes'));
        $appointment->setRefType('UNIT');
        $appointment->setRefId($viewing->getUnit()->getId());

        $this->em->persist($viewing);
        $this->em->persist($appointment);
        $this->em->flush();
    }

    /**
     * Zusage an Interessenten: legt den Mieter-Datensatz an und reserviert die Einheit.
     */
    public function acceptProspect(Prospect $prospect): Tenant
    {
        $parts = explode(' ', trim($prospect->getName()));
        $lastName = array_pop($parts);

        $tenant = new Tenant();
        $tenant->setFirstName(implode(' ', $parts) ?: '-');
        $tenant->setLastName($lastName);
        $tenant->setEmail($prospect->getEmail());
        $tenant->setPhone($prospect->getPhone());
        $tenant->setNotes('übernommen aus Interessent #'.$prospect->getId());

        $prospect->setStatus('ACCEPTED');
        if ($prospect->getUnit() && $prospect->getUnit()->getStatus() === RentalUnit::AVAILABLE) {
            $prospect->getUnit()->setStatus(RentalUnit::RESERVED);
        }

        $this->em->persist($tenant);
        $this->em->flush();

        return $tenant;
    }
}
