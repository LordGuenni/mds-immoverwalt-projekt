<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sollstellung (Forderung an den Mieter)
 *
 * @ORM\Entity(repositoryClass="App\Repository\ChargeRepository")
 */
class Charge
{
    const POSITIONS = [
        'Kaltmiete' => 'COLD_RENT',
        'Nebenkostenvorauszahlung' => 'SERVICE_CHARGE',
        'Sonstiges' => 'OTHER',
    ];

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
     * Abrechnungsmonat im Format JJJJ-MM
     *
     * @ORM\Column(type="string", length=7)
     */
    private $month;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $position;

    /**
     * @ORM\Column(type="date")
     */
    private $dueDate;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $amount;

    /**
     * 'OPEN', 'PARTIAL', 'PAID'
     *
     * @ORM\Column(type="string", length=20)
     */
    private $status = 'OPEN';

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

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function setMonth(string $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getPositionLabel(): string
    {
        $labels = array_flip(self::POSITIONS);

        return $labels[$this->position] ?? (string) $this->position;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
