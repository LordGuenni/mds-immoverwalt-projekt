<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Objekt / Immobilie
 *
 * @ORM\Entity(repositoryClass="App\Repository\PropertyRepository")
 */
class Property
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     * @Assert\NotBlank
     */
    private $propertyNumber;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $houseNumber;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $zip;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Owner", inversedBy="properties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $constructionYear;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $managementStart;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $status = 'ACTIVE';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Building", mappedBy="property")
     * @ORM\OrderBy({"buildingNumber" = "ASC"})
     */
    private $buildings;

    public function __construct()
    {
        $this->buildings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPropertyNumber(): ?string
    {
        return $this->propertyNumber;
    }

    public function setPropertyNumber(string $propertyNumber): self
    {
        $this->propertyNumber = $propertyNumber;

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

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(string $houseNumber): self
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

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

    public function getConstructionYear(): ?int
    {
        return $this->constructionYear;
    }

    public function setConstructionYear(?int $constructionYear): self
    {
        $this->constructionYear = $constructionYear;

        return $this;
    }

    public function getManagementStart(): ?\DateTimeInterface
    {
        return $this->managementStart;
    }

    public function setManagementStart(?\DateTimeInterface $managementStart): self
    {
        $this->managementStart = $managementStart;

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

    /**
     * @return Collection|Building[]
     */
    public function getBuildings(): Collection
    {
        return $this->buildings;
    }

    public function getAddressLine(): string
    {
        return $this->street.' '.$this->houseNumber.', '.$this->zip.' '.$this->city;
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}
