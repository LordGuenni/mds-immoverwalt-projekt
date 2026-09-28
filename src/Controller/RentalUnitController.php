<?php

namespace App\Controller;

use App\Entity\Building;
use App\Entity\RentalUnit;
use App\Form\RentalUnitType;
use App\Repository\ChargeRepository;
use App\Repository\IssueRepository;
use App\Repository\ProspectRepository;
use App\Repository\RentalUnitRepository;
use App\Service\AppointmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Mieteinheiten
 *
 * @Route("/stammdaten/einheiten")
 */
class RentalUnitController extends AbstractController
{
    /**
     * @Route("/", name="unit_index")
     */
    public function index(Request $request, RentalUnitRepository $repo): Response
    {
        $status = $request->query->get('status');

        return $this->render('unit/index.html.twig', [
            'units' => $repo->findForList($status ?: null),
            'status' => $status,
        ]);
    }

    /**
     * @Route("/neu/{building}", name="unit_new", methods={"GET","POST"})
     */
    public function new(Request $request, Building $building): Response
    {
        $unit = new RentalUnit();
        $unit->setBuilding($building);

        return $this->handleForm($request, $unit, 'Neue Mieteinheit');
    }

    /**
     * @Route("/{id}", name="unit_show", requirements={"id"="\d+"})
     */
    public function show(
        RentalUnit $unit,
        IssueRepository $issues,
        ProspectRepository $prospects,
        ChargeRepository $charges,
        AppointmentService $appointments
    ): Response {
        $current = $unit->getCurrentTenancy();

        return $this->render('unit/show.html.twig', [
            'unit' => $unit,
            'current' => $current,
            'issues' => $issues->findBy(['unit' => $unit], ['createdAt' => 'DESC']),
            'prospects' => $prospects->findBy(['unit' => $unit]),
            'charges' => $current ? $charges->findForTenancy($current) : [],
            'appointments' => $appointments->findFor('UNIT', $unit->getId()),
        ]);
    }

    /**
     * @Route("/{id}/bearbeiten", name="unit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RentalUnit $unit): Response
    {
        return $this->handleForm($request, $unit, 'Mieteinheit '.$unit->getUnitNumber().' bearbeiten');
    }

    private function handleForm(Request $request, RentalUnit $unit, string $title): Response
    {
        $form = $this->createForm(RentalUnitType::class, $unit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $unit->setLastModifiedBy($this->getUser()->getUsername());
            $em = $this->getDoctrine()->getManager();
            $em->persist($unit);
            $em->flush();
            $this->addFlash('success', 'Mieteinheit gespeichert.');

            return $this->redirectToRoute('unit_show', ['id' => $unit->getId()]);
        }

        return $this->render('form_page.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
            'back' => $unit->getId() ? $this->generateUrl('unit_show', ['id' => $unit->getId()]) : $this->generateUrl('unit_index'),
            'section' => 'stammdaten',
        ]);
    }
}
