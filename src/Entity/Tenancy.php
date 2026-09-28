<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mietverhältnis / Mietvertrag
 *
 * @ORM\Entity(repositoryClass="App\Repository\TenancyRepository")
 */
class Tenancy
{
    const DRAFT = 'DRAFT';
    const ACTIVE = 'ACTIVE';
    const NOTICE_GIVEN = 'NOTICE_GIVEN';
    const TERMINATED = 'TERMINATED';
    const ENDED = 'ENDED';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     */
    private $contractNumber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RentalUnit", inversedBy="tenancies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unit;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tenant", inversedBy="tenancies")
     * @ORM\JoinTable(name="tenancy_tenant")
     */
    private $tenants;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull
     */
    private $startDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $noticeDate;

    /**
     * Kaltmiete laut Vertrag (Kopie aus der Einheit bei Vertragsanlage)
     *
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $coldRent;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $serviceChargeAdvance;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $deposit;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $status = self::DRAFT;

    /**
     * Wurde nachträglich eingeführt (Ticket #213), wird parallel zu status gepflegt.
     *
     * @ORM\Column(type="boolean")
     */
    private $noticeGiven = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Handover", mappedBy="tenancy")
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $handovers;

    public function __construct()
    {
        $this->tenants = new ArrayCollection();
        $this->handovers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContractNumber(): ?string
    {
        return $this->contractNumber;
    }

    public function setContractNumber(string $contractNumber): self
    {
        $this->contractNumber = $contractNumber;

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

    /**
     * @return Collection|Tenant[]
     */
    public function getTenants(): Collection
    {
        return $this->tenants;
    }

    public function addTenant(Tenant $tenant): self
    {
        if (!$this->tenants->contains($tenant)) {
            $this->tenants[] = $tenant;
        }

        return $this;
    }

    public function removeTenant(Tenant $tenant): self
    {
        $this->tenants->removeElement($tenant);

        return $this;
    }

    public function getTenantNames(): string
    {
        $names = [];
        foreach ($this->tenants as $tenant) {
            $names[] = $tenant->getFullName();
        }

        return implode(', ', $names);
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getNoticeDate(): ?\DateTimeInterface
    {
        return $this->noticeDate;
    }

    public function setNoticeDate(?\DateTimeInterface $noticeDate): self
    {
        $this->noticeDate = $noticeDate;

        return $this;
    }

    public function getColdRent()
    {
        return $this->coldRent;
    }

    public function setColdRent($coldRent): self
    {
        $this->coldRent = $coldRent;

        return $this;
    }

    public function getServiceChargeAdvance()
    {
        return $this->serviceChargeAdvance;
    }

    public function setServiceChargeAdvance($serviceChargeAdvance): self
    {
        $this->serviceChargeAdvance = $serviceChargeAdvance;

        return $this;
    }

    public function getDeposit()
    {
        return $this->deposit;
    }

    public function setDeposit($deposit): self
    {
        $this->deposit = $deposit;

        return $this;
    }

    public function getTotalRent(): float
    {
        return (float) $this->coldRent + (float) $this->serviceChargeAdvance;
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

    public function isNoticeGiven(): bool
    {
        return (bool) $this->noticeGiven;
    }

    public function setNoticeGiven(bool $noticeGiven): self
    {
        $this->noticeGiven = $noticeGiven;

        return $this;
    }

    /**
     * @return Collection|Handover[]
     */
    public function getHandovers(): Collection
    {
        return $this->handovers;
    }

    public function __toString()
    {
        return $this->contractNumber.' ('.$this->getTenantNames().')';
    }
}
