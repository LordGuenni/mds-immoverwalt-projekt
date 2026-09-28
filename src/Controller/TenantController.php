<?php

namespace App\Controller;

use App\Entity\Tenant;
use App\Form\TenantType;
use App\Repository\TenantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/stammdaten/mieter")
 */
class TenantController extends AbstractController
{
    /**
     * @Route("/", name="tenant_index")
     */
    public function index(Request $request, TenantRepository $repo): Response
    {
        $q = trim((string) $request->query->get('q'));
        $tenants = $repo->findBy([], ['lastName' => 'ASC', 'firstName' => 'ASC']);
        if ($q !== '') {
            $tenants = array_filter($tenants, function (Tenant $t) use ($q) {
                return stripos($t->getFullName(), $q) !== false;
            });
        }

        return $this->render('tenant/index.html.twig', ['tenants' => $tenants, 'q' => $q]);
    }

    /**
     * @Route("/neu", name="tenant_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        return $this->handleForm($request, new Tenant(), 'Neuer Mieter');
    }

    /**
     * @Route("/{id}", name="tenant_show", requirements={"id"="\d+"})
     */
    public function show(Tenant $tenant): Response
    {
        return $this->render('tenant/show.html.twig', ['tenant' => $tenant]);
    }

    /**
     * @Route("/{id}/bearbeiten", name="tenant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tenant $tenant): Response
    {
        return $this->handleForm($request, $tenant, 'Mieter bearbeiten');
    }

    private function handleForm(Request $request, Tenant $tenant, string $title): Response
    {
        $form = $this->createForm(TenantType::class, $tenant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tenant);
            $em->flush();
            $this->addFlash('success', 'Mieter gespeichert.');

            return $this->redirectToRoute('tenant_show', ['id' => $tenant->getId()]);
        }

        return $this->render('form_page.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
            'back' => $this->generateUrl('tenant_index'),
            'section' => 'stammdaten',
        ]);
    }
}
