<?php

namespace App\Controller;

use App\Entity\Handover;
use App\Entity\RentalUnit;
use App\Entity\Tenancy;
use App\Entity\Tenant;
use App\Form\HandoverType;
use App\Form\TenancyType;
use App\Repository\ChargeRepository;
use App\Repository\TenancyRepository;
use App\Service\AccountingService;
use App\Service\AppointmentService;
use App\Service\TenancyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TenancyController extends AbstractController
{
    private $appointments;
    private $tenancyService;

    public function __construct(AppointmentService $appointments, TenancyService $tenancyService)
    {
        $this->appointments = $appointments;
        $this->tenancyService = $tenancyService;
    }

    /**
     * @Route("/vermietung/mietverhaeltnisse", name="tenancy_index")
     */
    public function index(Request $request, TenancyRepository $repo): Response
    {
        $filter = $request->query->get('filter', 'current');
        if ($filter === 'all') {
            $tenancies = $repo->findBy([], ['startDate' => 'DESC']);
        } elseif ($filter === 'current') {
            $tenancies = $repo->findCurrent();
        } else {
            $tenancies = $repo->findBy(['status' => $filter], ['startDate' => 'DESC']);
        }

        return $this->render('tenancy/index.html.twig', ['tenancies' => $tenancies, 'filter' => $filter]);
    }

    /**
     * @Route("/vermietung/mietverhaeltnisse/neu/{unit}", name="tenancy_new", methods={"GET","POST"})
     */
    public function new(Request $request, RentalUnit $unit): Response
    {
        if ($unit->getStatus() !== RentalUnit::AVAILABLE && $unit->getStatus() !== RentalUnit::RESERVED) {
            $this->addFlash('error', 'Die Einheit ist nicht frei.');

            return $this->redirectToRoute('unit_show', ['id' => $unit->getId()]);
        }

        $tenancy = new Tenancy();
        $tenancy->setUnit($unit);
        $tenancy->setStartDate(new \DateTime('first day of next month'));
        $tenancy->setColdRent($unit->getColdRent());
        $tenancy->setServiceChargeAdvance($unit->getServiceChargeAdvance());

        if ($request->query->get('tenant')) {
            $tenant = $this->getDoctrine()->getRepository(Tenant::class)->find($request->query->get('tenant'));
            if ($tenant) {
                $tenancy->addTenant($tenant);
            }
        }

        $form = $this->createForm(TenancyType::class, $tenancy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->tenancyService->createTenancy($tenancy);
                $this->addFlash('success', 'Mietvertrag '.$tenancy->getContractNumber().' als Entwurf angelegt.');

                return $this->redirectToRoute('tenancy_show', ['id' => $tenancy->getId()]);
            } catch (\RuntimeException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('form_page.html.twig', [
            'title' => 'Mietvertrag anlegen - '.$unit,
            'form' => $form->createView(),
            'back' => $this->generateUrl('unit_show', ['id' => $unit->getId()]),
            'section' => 'vermietung',
        ]);
    }

    /**
     * @Route("/vermietung/mietverhaeltnisse/{id}", name="tenancy_show", requirements={"id"="\d+"})
     */
    public function show(Tenancy $tenancy, ChargeRepository $charges, AccountingService $accounting): Response
    {
        return $this->render('tenancy/show.html.twig', [
            'tenancy' => $tenancy,
            'account' => $accounting->getTenantAccount($tenancy),
            'appointments' => $this->appointments->findFor('TENANCY', $tenancy->getId()),
            'allTenants' => $this->getDoctrine()->getRepository(Tenant::class)->findBy([], ['lastName' => 'ASC']),
        ]);
    }

    /**
     * @Route("/vermietung/mietverhaeltnisse/{id}/aktivieren", name="tenancy_activate", methods={"POST"})
     */
    public function activate(Tenancy $tenancy): Response
    {
        try {
            $this->tenancyService->activate($tenancy, $this->getUser()->getUsername());
            $this->addFlash('success', 'Mietverhältnis ist jetzt aktiv.');
        } catch (\RuntimeException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('tenancy_show', ['id' => $tenancy->getId()]);
    }

    /**
     * @Route("/vermietung/mietverhaeltnisse/{id}/mieter", name="tenancy_tenant", methods={"POST"})
     */
    public function tenants(Request $request, Tenancy $tenancy): Response
    {
        $tenant = $this->getDoctrine()->getRepository(Tenant::class)->find($request->request->get('tenant'));
        if ($tenant) {
            try {
                if ($request->request->get('action') === 'remove') {
                    $this->tenancyService->removeTenant($tenancy, $tenant);
                } else {
                    $this->tenancyService->addTenant($tenancy, $tenant);
                }
            } catch (\RuntimeException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->redirectToRoute('tenancy_show', ['id' => $tenancy->getId()]);
    }

    /**
     * @Route("/tenancies/{id}/terminate", name="tenancy_terminate", methods={"POST"})
     */
    public function terminate(Request $req, Tenancy $t)
    {
        if ($t->getStatus() !== Tenancy::ACTIVE) {
            $this->addFlash('error', 'Nicht aktiv');
            return $this->redirectToRoute('tenancy_show',
                ['id' => $t->getId()]);
        }
        $notice = new \DateTime($req->get('noticeDate'));
        $t->setNoticeDate($notice);
        $t->setEndDate((clone $notice)->modify('+3 months'));
        $t->setStatus(Tenancy::NOTICE_GIVEN);
        $unit = $t->getUnit();
        $unit->setStatus(RentalUnit::NOTICE_GIVEN);
        $this->appointments->createHandover($t);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('unit_show',
            ['id' => $unit->getId()]);
    }

    /**
     * @Route("/vermietung/mietverhaeltnisse/{id}/aufheben", name="tenancy_terminate_immediately", methods={"POST"})
     */
    public function terminateImmediately(Request $request, Tenancy $tenancy): Response
    {
        $end = new \DateTime($request->request->get('endDate') ?: 'today');
        $this->tenancyService->terminateImmediately($tenancy, $end);
        $this->addFlash('success', 'Mietverhältnis aufgehoben zum '.$end->format('d.m.Y').'.');

        return $this->redirectToRoute('tenancy_show', ['id' => $tenancy->getId()]);
    }

    /**
     * @Route("/vermietung/mietverhaeltnisse/{id}/uebergabe", name="handover_new", methods={"GET","POST"})
     */
    public function handover(Request $request, Tenancy $tenancy): Response
    {
        $handover = new Handover();
        $handover->setDate(new \DateTime());
        $handover->setType(in_array($tenancy->getStatus(), [Tenancy::NOTICE_GIVEN, Tenancy::TERMINATED]) ? Handover::MOVE_OUT : Handover::MOVE_IN);

        $form = $this->createForm(HandoverType::class, $handover);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tenancyService->recordHandover($tenancy, $handover, $this->getUser()->getUsername());
            $this->addFlash('success', 'Übergabe dokumentiert.');

            return $this->redirectToRoute('tenancy_show', ['id' => $tenancy->getId()]);
        }

        return $this->render('form_page.html.twig', [
            'title' => 'Übergabe / Rückgabe - '.$tenancy->getContractNumber(),
            'form' => $form->createView(),
            'back' => $this->generateUrl('tenancy_show', ['id' => $tenancy->getId()]),
            'section' => 'vermietung',
        ]);
    }
}
