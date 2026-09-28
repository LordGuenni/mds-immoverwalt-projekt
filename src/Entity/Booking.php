<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Buchung für die Eigentümerabrechnung (Einnahme oder Ausgabe)
 *
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */
class Booking
{
    const INCOME = 'INCOME';
    const EXPENSE = 'EXPENSE';

    const CATEGORIES = [
        'Mieteinnahme' => 'MIETE',
        'Nebenkostenvorauszahlung' => 'NK_VORAUSZAHLUNG',
        'Hausmeister' => 'HAUSMEISTER',
        'Reparatur' => 'REPARATUR',
        'Wartung' => 'WARTUNG',
        'Reinigung' => 'REINIGUNG',
        'Versicherung' => 'VERSICHERUNG',
        'Verwaltungsgebühr' => 'VERWALTUNG',
        'Sonstiges' => 'SONSTIGES',
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $type = self::EXPENSE;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Property")
     * @ORM\JoinColumn(nullable=false)
     */
    private $property;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RentalUnit")
     */
    private $unit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenancy")
     */
    private $tenancy;

    /**
     * wird beim Speichern aus dem Objekt übernommen
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Owner")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * immer positiv; Vorzeichen ergibt sich aus type
     *
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\Positive
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $category = 'SONSTIGES';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getProperty(): ?Property
    {
        return $this->property;
    }

    public function setProperty(?Property $property): self
    {
        $this->property = $property;

        return $this;
    }

    public function getUnit(): ?RentalUnit
    {
        return $this->unit;
    }

    public function setUnit(?RentalUnit $unit): self
    {
        $this->unit = $unit;

        return $this;
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

    public function getOwner(): ?Owner
    {
        return $this->owner;
    }

    public function setOwner(?Owner $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getSignedAmount(): float
    {
        return $this->type === self::INCOME ? (float) $this->amount : -1 * (float) $this->amount;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCategoryLabel(): string
    {
        $labels = array_flip(self::CATEGORIES);

        return $labels[$this->category] ?? (string) $this->category;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
