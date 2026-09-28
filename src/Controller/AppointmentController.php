<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/vorgaenge/termine")
 */
class AppointmentController extends AbstractController
{
    /**
     * @Route("/", name="appointment_index")
     */
    public function index(Request $request, AppointmentRepository $repo): Response
    {
        $all = $request->query->get('filter') === 'all';

        return $this->render('appointment/index.html.twig', [
            'appointments' => $all ? $repo->findBy([], ['startsAt' => 'DESC']) : $repo->findUpcoming(200),
            'all' => $all,
        ]);
    }

    /**
     * @Route("/neu", name="appointment_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $appointment = new Appointment();
        $appointment->setStartsAt(new \DateTime('tomorrow 09:00'));
        if ($request->query->get('refType')) {
            $appointment->setRefType($request->query->get('refType'));
            $appointment->setRefId((int) $request->query->get('refId'));
        }

        return $this->handleForm($request, $appointment, 'Neuer Termin');
    }

    /**
     * @Route("/{id}/bearbeiten", name="appointment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Appointment $appointment): Response
    {
        return $this->handleForm($request, $appointment, 'Termin bearbeiten');
    }

    private function handleForm(Request $request, Appointment $appointment, string $title): Response
    {
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($appointment);
            $em->flush();
            $this->addFlash('success', 'Termin gespeichert.');

            return $this->redirectToRoute('appointment_index');
        }

        return $this->render('form_page.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
            'back' => $this->generateUrl('appointment_index'),
            'section' => 'vorgaenge',
        ]);
    }
}
