<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Payment;
use App\Entity\Tenancy;
use App\Form\BookingType;
use App\Form\PaymentType;
use App\Repository\BookingRepository;
use App\Repository\ChargeRepository;
use App\Repository\OwnerRepository;
use App\Repository\PaymentRepository;
use App\Repository\PropertyRepository;
use App\Repository\TenancyRepository;
use App\Service\AccountingService;
use App\Service\TenancyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/abrechnung")
 */
class AccountingController extends AbstractController
{
    /**
     * @Route("/sollstellungen", name="charge_index")
     */
    public function charges(Request $request, ChargeRepository $repo): Response
    {
        $month = $request->query->get('month', date('Y-m'));

        return $this->render('accounting/charges.html.twig', [
            'charges' => $repo->findBy(['month' => $month], ['tenancy' => 'ASC', 'position' => 'ASC']),
            'month' => $month,
        ]);
    }

    /**
     * @Route("/sollstellungen/erzeugen", name="charge_generate", methods={"POST"})
     */
    public function generate(Request $request, TenancyService $tenancyService): Response
    {
        $month = $request->request->get('month', date('Y-m'));
        $count = $tenancyService->generateMonthlyCharges($month);
        $this->addFlash('success', $count.' Sollstellungen für '.$month.' erzeugt.');

        return $this->redirectToRoute('charge_index', ['month' => $month]);
    }

    /**
     * @Route("/mieterkonten", name="account_index")
     */
    public function accounts(TenancyRepository $tenancies, AccountingService $accounting): Response
    {
        $rows = [];
        foreach ($tenancies->findCurrent() as $t) {
            $account = $accounting->getTenantAccount($t);
            $last = end($account);
            $rows[] = ['tenancy' => $t, 'balance' => $last ? $last['balance'] : 0];
        }

        return $this->render('accounting/accounts.html.twig', ['rows' => $rows]);
    }

    /**
     * @Route("/mieterkonten/{id}", name="account_show", methods={"GET","POST"})
     */
    public function account(Request $request, Tenancy $tenancy, AccountingService $accounting, ChargeRepository $charges, PaymentRepository $payments): Response
    {
        $payment = new Payment();
        $payment->setTenancy($tenancy);
        $payment->setAmount($tenancy->getTotalRent());
        $payment->setReference('Miete '.$tenancy->getContractNumber());

        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accounting->recordPayment($payment);
            $this->addFlash('success', 'Zahlung erfasst.');

            return $this->redirectToRoute('account_show', ['id' => $tenancy->getId()]);
        }

        return $this->render('accounting/account.html.twig', [
            'tenancy' => $tenancy,
            'account' => $accounting->getTenantAccount($tenancy),
            'charges' => $charges->findForTenancy($tenancy),
            'payments' => $payments->findForTenancy($tenancy),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/buchungen", name="booking_index")
     */
    public function bookings(BookingRepository $repo): Response
    {
        return $this->render('accounting/bookings.html.twig', ['bookings' => $repo->findLatest(200)]);
    }

    /**
     * @Route("/buchungen/neu", name="booking_new", methods={"GET","POST"})
     */
    public function newBooking(Request $request): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Eigentümer immer aus dem Objekt übernehmen
            $booking->setOwner($booking->getProperty()->getOwner());
            $em = $this->getDoctrine()->getManager();
            $em->persist($booking);
            $em->flush();
            $this->addFlash('success', 'Buchung gespeichert.');

            return $this->redirectToRoute('booking_index');
        }

        return $this->render('form_page.html.twig', [
            'title' => 'Neue Buchung',
            'form' => $form->createView(),
            'back' => $this->generateUrl('booking_index'),
            'section' => 'abrechnung',
        ]);
    }

    /**
     * @Route("/eigentuemerabrechnung", name="owner_statement")
     */
    public function statement(Request $request, OwnerRepository $owners, PropertyRepository $properties, AccountingService $accounting): Response
    {
        $propertyId = $request->query->get('property');
        $month = $request->query->get('month', date('Y-m', strtotime('first day of last month')));
        $statement = null;

        if ($propertyId) {
            $property = $properties->find($propertyId);
            if ($property) {
                $statement = $accounting->ownerStatement($property->getOwner(), $property, $month);
            }
        }

        return $this->render('accounting/statement.html.twig', [
            'properties' => $properties->findBy([], ['name' => 'ASC']),
            'propertyId' => $propertyId,
            'month' => $month,
            'statement' => $statement,
        ]);
    }
}
