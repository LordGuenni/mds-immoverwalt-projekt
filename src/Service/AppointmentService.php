<?php

namespace App\Service;

use App\Entity\Appointment;
use App\Entity\Handover;
use App\Entity\Tenancy;
use App\Repository\AppointmentRepository;
use Doctrine\ORM\EntityManagerInterface;

class AppointmentService
{
    private $em;
    private $appointmentRepository;

    public function __construct(EntityManagerInterface $em, AppointmentRepository $appointmentRepository)
    {
        $this->em = $em;
        $this->appointmentRepository = $appointmentRepository;
    }

    /**
     * Legt einen Übergabetermin für das Mietverhältnis an.
     * MOVE_OUT: am Vertragsende, MOVE_IN: am Vertragsbeginn.
     */
    public function createHandover(Tenancy $tenancy, string $kind = Handover::MOVE_OUT): Appointment
    {
        $date = $kind === Handover::MOVE_IN ? $tenancy->getStartDate() : $tenancy->getEndDate();
        if (!$date) {
            $date = new \DateTime('+14 days');
        }

        $startsAt = \DateTime::createFromFormat('Y-m-d H:i', $date->format('Y-m-d').' 10:00');

        $appointment = new Appointment();
        $appointment->setType('UEBERGABE');
        $appointment->setTitle(($kind === Handover::MOVE_IN ? 'Wohnungsübergabe (Einzug) ' : 'Wohnungsrückgabe (Auszug) ')
            .$tenancy->getUnit()->getUnitNumber());
        $appointment->setStartsAt($startsAt);
        $appointment->setEndsAt((clone $startsAt)->modify('+1 hour'));
        $appointment->setRefType('TENANCY');
        $appointment->setRefId($tenancy->getId());
        $appointment->setDescription('Mieter: '.$tenancy->getTenantNames());

        $this->em->persist($appointment);

        return $appointment;
    }

    /**
     * @return Appointment[]
     */
    public function findFor(string $refType, ?int $refId): array
    {
        if (!$refId) {
            return [];
        }

        return $this->appointmentRepository->findForRef($refType, $refId);
    }
}
