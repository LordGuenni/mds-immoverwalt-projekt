<?php

namespace App\Controller;

use App\Entity\ServiceProvider;
use App\Form\ServiceProviderType;
use App\Repository\ServiceProviderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/stammdaten/dienstleister")
 */
class ServiceProviderController extends AbstractController
{
    /**
     * @Route("/", name="provider_index")
     */
    public function index(ServiceProviderRepository $repo): Response
    {
        return $this->render('provider/index.html.twig', ['providers' => $repo->findBy([], ['company' => 'ASC'])]);
    }

    /**
     * @Route("/neu", name="provider_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        return $this->handleForm($request, new ServiceProvider(), 'Neuer Dienstleister');
    }

    /**
     * @Route("/{id}/bearbeiten", name="provider_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ServiceProvider $provider): Response
    {
        return $this->handleForm($request, $provider, 'Dienstleister bearbeiten');
    }

    private function handleForm(Request $request, ServiceProvider $provider, string $title): Response
    {
        $form = $this->createForm(ServiceProviderType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($provider);
            $em->flush();
            $this->addFlash('success', 'Dienstleister gespeichert.');

            return $this->redirectToRoute('provider_index');
        }

        return $this->render('form_page.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
            'back' => $this->generateUrl('provider_index'),
            'section' => 'stammdaten',
        ]);
    }
}
