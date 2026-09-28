<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Owner;
use App\Entity\Payment;
use App\Entity\Property;
use App\Entity\Tenancy;
use App\Repository\BookingRepository;
use App\Repository\ChargeRepository;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Mieterkonto und Eigentümerabrechnung (vereinfacht, keine Finanzbuchhaltung)
 */
class AccountingService
{
    private $em;
    private $chargeRepository;
    private $paymentRepository;
    private $bookingRepository;
    private $managementFeePercent;

    public function __construct(
        EntityManagerInterface $em,
        ChargeRepository $chargeRepository,
        PaymentRepository $paymentRepository,
        BookingRepository $bookingRepository,
        $managementFeePercent
    ) {
        $this->em = $em;
        $this->chargeRepository = $chargeRepository;
        $this->paymentRepository = $paymentRepository;
        $this->bookingRepository = $bookingRepository;
        $this->managementFeePercent = (float) $managementFeePercent;
    }

    /**
     * Mieterkonto: Soll und Zahlung je Monat mit laufendem Saldo.
     */
    public function getTenantAccount(Tenancy $tenancy): array
    {
        $rows = [];
        foreach ($this->chargeRepository->findForTenancy($tenancy) as $charge) {
            $m = $charge->getMonth();
            $rows[$m] = $rows[$m] ?? ['month' => $m, 'debit' => 0.0, 'credit' => 0.0];
            $rows[$m]['debit'] += (float) $charge->getAmount();
        }
        foreach ($this->paymentRepository->findForTenancy($tenancy) as $payment) {
            $m = $payment->getPaymentDate()->format('Y-m');
            $rows[$m] = $rows[$m] ?? ['month' => $m, 'debit' => 0.0, 'credit' => 0.0];
            $rows[$m]['credit'] += (float) $payment->getAmount();
        }
        ksort($rows);

        return self::withBalance(array_values($rows));
    }

    /**
     * Laufenden offenen Saldo berechnen (positiv = Mieter schuldet).
     */
    public static function withBalance(array $rows): array
    {
        $balance = 0.0;
        foreach ($rows as $i => $row) {
            $balance += $row['debit'] - $row['credit'];
            $rows[$i]['balance'] = round($balance, 2);
        }

        return $rows;
    }

    /**
     * Zahlung erfassen: den ältesten offenen Sollstellungen zuordnen und
     * als Mieteinnahme für den Eigentümer verbuchen.
     */
    public function recordPayment(Payment $payment): void
    {
        $tenancy = $payment->getTenancy();

        $open = $this->chargeRepository->findOpenForTenancy($tenancy);
        if (count($open) > 0) {
            $payment->setCharge($open[0]);
        }
        $this->em->persist($payment);
        $this->em->flush();

        $this->allocatePayments($tenancy);

        $property = $tenancy->getUnit()->getBuilding()->getProperty();
        $booking = new Booking();
        $booking->setType(Booking::INCOME)
            ->setProperty($property)
            ->setOwner($property->getOwner())
            ->setUnit($tenancy->getUnit())
            ->setTenancy($tenancy)
            ->setDate($payment->getPaymentDate())
            ->setAmount($payment->getAmount())
            ->setCategory('MIETE')
            ->setDescription('Mietzahlung '.$tenancy->getContractNumber());

        $this->em->persist($payment);
        $this->em->persist($booking);
        $this->em->flush();
    }

    /**
     * Zahlungen den Sollstellungen in Fälligkeitsreihenfolge zuordnen
     * (älteste Forderung zuerst) und den Status neu setzen.
     */
    private function allocatePayments(Tenancy $tenancy): void
    {
        $paid = 0.0;
        foreach ($this->paymentRepository->findForTenancy($tenancy) as $p) {
            $paid += (float) $p->getAmount();
        }

        foreach ($this->chargeRepository->findForTenancy($tenancy) as $charge) {
            $amount = (float) $charge->getAmount();
            if ($paid >= $amount - 0.005) {
                $charge->setStatus('PAID');
                $paid -= $amount;
            } elseif ($paid > 0.005) {
                $charge->setStatus('PARTIAL');
                $paid = 0.0;
            } else {
                $charge->setStatus('OPEN');
            }
        }
    }

    /**
     * Eigentümerabrechnung für ein Objekt und einen Monat (JJJJ-MM).
     */
    public function ownerStatement(Owner $owner, Property $property, string $month): array
    {
        $from = \DateTime::createFromFormat('Y-m-d H:i:s', $month.'-01 00:00:00');
        $to = (clone $from)->modify('last day of this month');

        $bookings = $this->bookingRepository->findForStatement($owner, $property, $from, $to);

        $income = 0.0;
        $expenses = 0.0;
        $hasFee = false;
        foreach ($bookings as $b) {
            if ($b->getType() === Booking::INCOME) {
                $income += (float) $b->getAmount();
            } else {
                $expenses += (float) $b->getAmount();
                if ($b->getCategory() === 'VERWALTUNG') {
                    $hasFee = true;
                }
            }
        }

        // Verwaltungsgebühr berechnen, wenn nicht manuell gebucht
        $fee = 0.0;
        if (!$hasFee) {
            $fee = round($income * $this->managementFeePercent / 100, 2);
            $expenses += $fee;
        }

        return [
            'owner' => $owner,
            'property' => $property,
            'month' => $month,
            'bookings' => $bookings,
            'income' => $income,
            'expenses' => $expenses,
            'calculatedFee' => $fee,
            'result' => round($income - $expenses, 2),
        ];
    }
}
