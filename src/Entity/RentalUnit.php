<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mieteinheit (Wohnung oder Gewerbeeinheit)
 *
 * @ORM\Entity(repositoryClass="App\Repository\RentalUnitRepository")
 * @ORM\Table(name="rental_unit")
 * @ORM\HasLifecycleCallbacks
 */
class RentalUnit
{
    const AVAILABLE = 'AVAILABLE';
    const RESERVED = 'RESERVED';
    const RENTED = 'RENTED';
    const NOTICE_GIVEN = 'NOTICE_GIVEN';
    const RENOVATION = 'RENOVATION';
    const BLOCKED = 'BLOCKED';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Version
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank
     */
    private $unitNumber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Building", inversedBy="units")
     * @ORM\JoinColumn(nullable=false)
     */
    private $building;

    /**
     * "WHG" = Wohnung, "GEW" = Gewerbe
     *
     * @ORM\Column(type="string", length=3)
     */
    private $type = 'WHG';

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $floor;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=2, nullable=true)
     */
    private $area;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $coldRent;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $serviceChargeAdvance;

    /**
     * 'AVAILABLE', 'RESERVED', 'RENTED', 'NOTICE_GIVEN', 'RENOVATION', 'BLOCKED'
     *
     * @ORM\Column(type="string", length=20)
     */
    private $status = self::AVAILABLE;

    /**
     * @ORM\Column(type="boolean")
     */
    private $showInList = true;

    /**
     * Statusfarbe in der Liste
     *
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $cssClass;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tenancy", mappedBy="unit")
     * @ORM\OrderBy({"startDate" = "DESC"})
     */
    private $tenancies;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastModified;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $lastModifiedBy;

    public function __construct()
    {
        $this->tenancies = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function touch(): void
    {
        $this->lastModified = new \DateTime();

        // Statusfarbe für die Liste
        switch ($this->status) {
            case self::AVAILABLE:
                $this->cssClass = 'status-green';
                break;
            case self::RENTED:
                $this->cssClass = 'status-default';
                break;
            case self::NOTICE_GIVEN:
            case self::RESERVED:
                $this->cssClass = 'status-orange';
                break;
            default:
                $this->cssClass = 'status-red';
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function getUnitNumber(): ?string
    {
        return $this->unitNumber;
    }

    public function setUnitNumber(string $unitNumber): self
    {
        $this->unitNumber = $unitNumber;

        return $this;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): self
    {
        $this->building = $building;

        return $this;
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

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(?int $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function getArea()
    {
        return $this->area;
    }

    public function setArea($area): self
    {
        $this->area = $area;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function isShowInList(): bool
    {
        return (bool) $this->showInList;
    }

    public function setShowInList(bool $showInList): self
    {
        $this->showInList = $showInList;

        return $this;
    }

    public function getCssClass(): ?string
    {
        return $this->cssClass;
    }

    public function setCssClass(?string $cssClass): self
    {
        $this->cssClass = $cssClass;

        return $this;
    }

    /**
     * @return Collection|Tenancy[]
     */
    public function getTenancies(): Collection
    {
        return $this->tenancies;
    }

    /**
     * Aktuelles Mietverhältnis (ACTIVE oder NOTICE_GIVEN).
     */
    public function getCurrentTenancy(): ?Tenancy
    {
        foreach ($this->tenancies as $tenancy) {
            if (in_array($tenancy->getStatus(), [Tenancy::ACTIVE, Tenancy::NOTICE_GIVEN])) {
                return $tenancy;
            }
        }

        return null;
    }

    public function getLastModified(): ?\DateTimeInterface
    {
        return $this->lastModified;
    }

    public function getLastModifiedBy(): ?string
    {
        return $this->lastModifiedBy;
    }

    public function setLastModifiedBy(?string $lastModifiedBy): self
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    public function getTypeLabel(): string
    {
        return $this->type === 'GEW' ? 'Gewerbe' : 'Wohnung';
    }

    public function __toString()
    {
        $building = $this->building ? $this->building->getProperty()->getName().', ' : '';

        return $building.$this->unitNumber;
    }
}
