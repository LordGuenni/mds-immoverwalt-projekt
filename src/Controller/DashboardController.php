<?php

namespace App\Controller;

use App\Entity\RentalUnit;
use App\Repository\AppointmentRepository;
use App\Repository\IssueRepository;
use App\Repository\RentalUnitRepository;
use App\Repository\TaskRepository;
use App\Repository\TenancyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index(
        RentalUnitRepository $units,
        TenancyRepository $tenancies,
        IssueRepository $issues,
        TaskRepository $tasks,
        AppointmentRepository $appointments
    ): Response {
        $allUnits = $units->findAll();
        $counts = [];
        foreach ($allUnits as $u) {
            $counts[$u->getStatus()] = ($counts[$u->getStatus()] ?? 0) + 1;
        }

        return $this->render('dashboard/index.html.twig', [
            'unitCount' => count($allUnits),
            'available' => $counts[RentalUnit::AVAILABLE] ?? 0,
            'noticeGiven' => $counts[RentalUnit::NOTICE_GIVEN] ?? 0,
            'tenancyCount' => count($tenancies->findCurrent()),
            'openIssues' => $issues->findByStatusFilter('open'),
            'openTasks' => array_slice($tasks->findOpen(), 0, 10),
            'appointments' => $appointments->findUpcoming(10),
        ]);
    }
}
