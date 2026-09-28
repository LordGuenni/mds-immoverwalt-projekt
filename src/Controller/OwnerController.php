<?php

namespace App\Controller;

use App\Entity\Owner;
use App\Form\OwnerType;
use App\Repository\OwnerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/stammdaten/eigentuemer")
 */
class OwnerController extends AbstractController
{
    /**
     * @Route("/", name="owner_index")
     */
    public function index(OwnerRepository $repo): Response
    {
        return $this->render('owner/index.html.twig', ['owners' => $repo->findBy([], ['name' => 'ASC'])]);
    }

    /**
     * @Route("/neu", name="owner_new", methods={"GET","POST"})
     */
    public function new(Request $request, OwnerRepository $repo): Response
    {
        $owner = new Owner();
        $owner->setOwnerNumber(sprintf('EI-%04d', count($repo->findAll()) + 1));

        return $this->handleForm($request, $owner, 'Neuer Eigentümer');
    }

    /**
     * @Route("/{id}", name="owner_show", requirements={"id"="\d+"})
     */
    public function show(Owner $owner): Response
    {
        return $this->render('owner/show.html.twig', ['owner' => $owner]);
    }

    /**
     * @Route("/{id}/bearbeiten", name="owner_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Owner $owner): Response
    {
        return $this->handleForm($request, $owner, 'Eigentümer bearbeiten');
    }

    private function handleForm(Request $request, Owner $owner, string $title): Response
    {
        $form = $this->createForm(OwnerType::class, $owner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($owner);
            $em->flush();
            $this->addFlash('success', 'Eigentümer gespeichert.');

            return $this->redirectToRoute('owner_show', ['id' => $owner->getId()]);
        }

        return $this->render('form_page.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
            'back' => $this->generateUrl('owner_index'),
            'section' => 'stammdaten',
        ]);
    }
}
