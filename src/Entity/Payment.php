<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Zahlungseingang
 *
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenancy")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tenancy;

    /**
     * @ORM\Column(type="date")
     */
    private $paymentDate;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\Positive
     */
    private $amount;

    /**
     * Verwendungszweck
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reference;

    /**
     * zugeordnete Sollstellung
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Charge")
     */
    private $charge;

    public function __construct()
    {
        $this->paymentDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTenancy(): ?Tenancy
    {
        return $this->tenancy;
    }

    public function setTenancy(?Tenancy $tenancy): self
    {
        $this->tenancy = $tenancy;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTimeInterface $paymentDate): self
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCharge(): ?Charge
    {
        return $this->charge;
    }

    public function setCharge(?Charge $charge): self
    {
        $this->charge = $charge;

        return $this;
    }
}
