<?php

namespace App\Service;

use App\Entity\Issue;
use App\Entity\IssueStatusChange;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\AppointmentRepository;
use App\Repository\IssueRepository;
use App\Repository\RentalUnitRepository;
use App\Repository\ServiceProviderRepository;
use App\Repository\TaskRepository;
use App\Repository\TenancyRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Meldungen zur Mietsache
 */
class IssueService
{
    private $em;
    private $issueRepository;
    private $rentalUnitRepository;
    private $tenancyRepository;
    private $taskRepository;
    private $serviceProviderRepository;
    private $appointmentRepository;

    public function __construct(
        EntityManagerInterface $em,
        IssueRepository $issueRepository,
        RentalUnitRepository $rentalUnitRepository,
        TenancyRepository $tenancyRepository,
        TaskRepository $taskRepository,
        ServiceProviderRepository $serviceProviderRepository,
        AppointmentRepository $appointmentRepository
    ) {
        $this->em = $em;
        $this->issueRepository = $issueRepository;
        $this->rentalUnitRepository = $rentalUnitRepository;
        $this->tenancyRepository = $tenancyRepository;
        $this->taskRepository = $taskRepository;
        $this->serviceProviderRepository = $serviceProviderRepository;
        $this->appointmentRepository = $appointmentRepository;
    }

    public function createIssue(Issue $issue, User $user): Issue
    {
        $issue->setIssueNumber($this->issueRepository->nextNumber());

        if ($issue->getUnit()) {
            // Objekt immer aus der Einheit übernehmen
            $issue->setProperty($issue->getUnit()->getBuilding()->getProperty());

            // Mieter automatisch ermitteln, wenn nicht angegeben
            if (!$issue->getTenant()) {
                $active = $this->tenancyRepository->findActiveForUnit($issue->getUnit());
                if (count($active) > 0 && $active[0]->getTenants()->count() > 0) {
                    $issue->setTenant($active[0]->getTenants()->first());
                }
            }
        }

        if (!$issue->getAssignee()) {
            $issue->setAssignee($user);
        }

        // Dringende Meldungen sofort in Prüfung
        if ($issue->getPriority() >= 4) {
            $issue->setStatus('IN_REVIEW');
        }

        $this->em->persist($issue);
        $this->em->persist(new IssueStatusChange($issue, null, $issue->getStatus(), $user->getUsername(), 'Meldung erfasst'));
        $this->em->flush();

        return $issue;
    }

    /**
     * Meldung schließen. Nur möglich, wenn keine offenen Aufgaben mehr bestehen.
     */
    public function closeIssue(Issue $issue): void
    {
        if ($this->taskRepository->countOpenForIssue($issue) > 0) {
            throw new \RuntimeException('Die Meldung hat noch offene Aufgaben und kann nicht geschlossen werden.');
        }

        $issue->setStatus('CLOSED');
        $this->em->flush();
    }

    /**
     * Aufgabe zu einer Meldung anlegen. Der Dienstleister wird anhand der
     * Kategorie vorgeschlagen.
     */
    public function createTaskFromIssue(Issue $issue, string $title, User $responsible): Task
    {
        $task = new Task();
        $task->setTaskNumber($this->taskRepository->nextNumber());
        $task->setTitle($title);
        $task->setIssue($issue);
        $task->setProperty($issue->getProperty());
        $task->setUnit($issue->getUnit());
        $task->setResponsible($responsible);
        $task->setPriority($issue->getPriority());
        $task->setDueDate(new \DateTime($issue->getPriority() >= 3 ? '+2 days' : '+14 days'));

        $tradeMap = ['HEIZUNG' => 'HEIZUNG', 'SANITAER' => 'SANITAER', 'ELEKTRO' => 'ELEKTRO', 'AUFZUG' => 'AUFZUG'];
        if (isset($tradeMap[$issue->getCategory()])) {
            $provider = $this->serviceProviderRepository->findOneBy(['trade' => $tradeMap[$issue->getCategory()]]);
            if ($provider) {
                $task->setServiceProvider($provider);
                $task->setContactPerson($provider->getContactPerson());
                $task->setStatus('ASSIGNED');
            }
        }

        if ($issue->getStatus() === 'NEW' || $issue->getStatus() === 'IN_REVIEW') {
            $issue->setStatus('IN_PROGRESS');
        }

        $this->em->persist($task);
        $this->em->flush();

        return $task;
    }

    /**
     * Termine, die zur Meldung oder zur betroffenen Einheit gehören.
     */
    public function getRelatedAppointments(Issue $issue): array
    {
        $result = $this->appointmentRepository->findForRef('ISSUE', $issue->getId());
        if ($issue->getUnit()) {
            $result = array_merge($result, $this->appointmentRepository->findForRef('UNIT', $issue->getUnit()->getId()));
        }

        return $result;
    }

    /**
     * Aktuelles Mietverhältnis der betroffenen Einheit (für die Detailansicht).
     */
    public function getCurrentTenancy(Issue $issue)
    {
        if (!$issue->getUnit()) {
            return null;
        }
        $unit = $this->rentalUnitRepository->find($issue->getUnit()->getId());
        $active = $this->tenancyRepository->findActiveForUnit($unit);

        return $active[0] ?? null;
    }
}
