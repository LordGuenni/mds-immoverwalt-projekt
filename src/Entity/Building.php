<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Gebäude
 *
 * @ORM\Entity(repositoryClass="App\Repository\BuildingRepository")
 */
class Building
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $buildingNumber;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $floors;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $constructionYear;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Property", inversedBy="buildings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $property;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RentalUnit", mappedBy="building")
     * @ORM\OrderBy({"unitNumber" = "ASC"})
     */
    private $units;

    public function __construct()
    {
        $this->units = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuildingNumber(): ?string
    {
        return $this->buildingNumber;
    }

    public function setBuildingNumber(string $buildingNumber): self
    {
        $this->buildingNumber = $buildingNumber;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getFloors(): ?int
    {
        return $this->floors;
    }

    public function setFloors(?int $floors): self
    {
        $this->floors = $floors;

        return $this;
    }

    public function getConstructionYear(): ?int
    {
        return $this->constructionYear;
    }

    public function setConstructionYear(?int $constructionYear): self
    {
        $this->constructionYear = $constructionYear;

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

    /**
     * @return Collection|RentalUnit[]
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function __toString()
    {
        return $this->property ? $this->property->getName().' / '.$this->name : (string) $this->name;
    }
}
