<?php

namespace App\Controller;

use App\Entity\Prospect;
use App\Entity\Viewing;
use App\Form\ProspectType;
use App\Form\ViewingType;
use App\Repository\ProspectRepository;
use App\Repository\ViewingRepository;
use App\Service\RentalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Vermietung: Interessenten und Besichtigungen
 *
 * @Route("/vermietung")
 */
class LettingController extends AbstractController
{
    /**
     * @Route("/interessenten", name="prospect_index")
     */
    public function prospects(ProspectRepository $repo): Response
    {
        return $this->render('letting/prospects.html.twig', [
            'prospects' => $repo->findBy([], ['requestDate' => 'DESC']),
        ]);
    }

    /**
     * @Route("/interessenten/neu", name="prospect_new", methods={"GET","POST"})
     */
    public function newProspect(Request $request): Response
    {
        return $this->handleProspectForm($request, new Prospect(), 'Neuer Interessent');
    }

    /**
     * @Route("/interessenten/{id}/bearbeiten", name="prospect_edit", methods={"GET","POST"})
     */
    public function editProspect(Request $request, Prospect $prospect): Response
    {
        return $this->handleProspectForm($request, $prospect, 'Interessent bearbeiten');
    }

    /**
     * Zusage: Mieter anlegen und direkt zum Vertrag weiterleiten.
     *
     * @Route("/interessenten/{id}/zusage", name="prospect_accept", methods={"POST"})
     */
    public function accept(Prospect $prospect, RentalService $rentalService): Response
    {
        $tenant = $rentalService->acceptProspect($prospect);
        $this->addFlash('success', 'Zusage erteilt, Mieter '.$tenant->getFullName().' angelegt.');

        if ($prospect->getUnit()) {
            return $this->redirectToRoute('tenancy_new', ['unit' => $prospect->getUnit()->getId(), 'tenant' => $tenant->getId()]);
        }

        return $this->redirectToRoute('tenant_show', ['id' => $tenant->getId()]);
    }

    /**
     * @Route("/besichtigungen", name="viewing_index")
     */
    public function viewings(ViewingRepository $repo): Response
    {
        return $this->render('letting/viewings.html.twig', [
            'viewings' => $repo->findBy([], ['dateTime' => 'DESC']),
        ]);
    }

    /**
     * @Route("/besichtigungen/neu", name="viewing_new", methods={"GET","POST"})
     */
    public function newViewing(Request $request, RentalService $rentalService, ProspectRepository $prospects): Response
    {
        $viewing = new Viewing();
        $viewing->setDateTime(new \DateTime('tomorrow 17:00'));
        $viewing->setEmployee($this->getUser());
        if ($request->query->get('prospect')) {
            $prospect = $prospects->find($request->query->get('prospect'));
            if ($prospect) {
                $viewing->setProspect($prospect);
                $viewing->setUnit($prospect->getUnit());
            }
        }

        $form = $this->createForm(ViewingType::class, $viewing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rentalService->planViewing($viewing);
            $this->addFlash('success', 'Besichtigung geplant.');

            return $this->redirectToRoute('viewing_index');
        }

        return $this->render('form_page.html.twig', [
            'title' => 'Besichtigung planen',
            'form' => $form->createView(),
            'back' => $this->generateUrl('viewing_index'),
            'section' => 'vermietung',
        ]);
    }

    private function handleProspectForm(Request $request, Prospect $prospect, string $title): Response
    {
        $form = $this->createForm(ProspectType::class, $prospect);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($prospect);
            $em->flush();
            $this->addFlash('success', 'Interessent gespeichert.');

            return $this->redirectToRoute('prospect_index');
        }

        return $this->render('form_page.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
            'back' => $this->generateUrl('prospect_index'),
            'section' => 'vermietung',
        ]);
    }
}
