<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Dienstleister (Handwerker, Hausmeister, Reinigung ...)
 *
 * @ORM\Entity(repositoryClass="App\Repository\ServiceProviderRepository")
 */
class ServiceProvider
{
    const TRADES = [
        'Sanitär' => 'SANITAER',
        'Heizung' => 'HEIZUNG',
        'Elektro' => 'ELEKTRO',
        'Maler' => 'MALER',
        'Hausmeister' => 'HAUSMEISTER',
        'Gebäudereinigung' => 'REINIGUNG',
        'Aufzug' => 'AUFZUG',
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank
     */
    private $company;

    /**
     * Gewerk
     *
     * @ORM\Column(type="string", length=30)
     */
    private $trade;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $contactPerson;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * 'ACTIVE' / 'INACTIVE'
     *
     * @ORM\Column(type="string", length=20)
     */
    private $status = 'ACTIVE';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getTrade(): ?string
    {
        return $this->trade;
    }

    public function setTrade(string $trade): self
    {
        $this->trade = $trade;

        return $this;
    }

    public function getTradeLabel(): string
    {
        $labels = array_flip(self::TRADES);

        return $labels[$this->trade] ?? (string) $this->trade;
    }

    public function getContactPerson(): ?string
    {
        return $this->contactPerson;
    }

    public function setContactPerson(?string $contactPerson): self
    {
        $this->contactPerson = $contactPerson;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function __toString()
    {
        return $this->company.' ('.$this->getTradeLabel().')';
    }
}
