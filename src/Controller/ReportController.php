<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use App\Repository\UserRepository;
use App\Service\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    /**
     * @Route("/berichte/leerstand", name="report_vacancy")
     */
    public function vacancy(PropertyRepository $properties, PropertyService $propertyService): Response
    {
        $rows = [];
        foreach ($properties->findBy([], ['name' => 'ASC']) as $p) {
            $rows[] = ['property' => $p, 'stats' => $propertyService->getStatistics($p)];
        }

        return $this->render('report/vacancy.html.twig', ['rows' => $rows]);
    }

    /**
     * @Route("/verwaltung/benutzer", name="admin_users")
     */
    public function users(UserRepository $users): Response
    {
        return $this->render('report/users.html.twig', ['users' => $users->findBy([], ['username' => 'ASC'])]);
    }
}
