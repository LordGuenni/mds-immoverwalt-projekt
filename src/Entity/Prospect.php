<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Mietinteressent
 *
 * @ORM\Entity(repositoryClass="App\Repository\ProspectRepository")
 */
class Prospect
{
    const STATUSES = [
        'Neu' => 'NEW',
        'Besichtigung geplant' => 'VIEWING_PLANNED',
        'Bewerbung eingegangen' => 'APPLIED',
        'Zusage' => 'ACCEPTED',
        'Absage' => 'REJECTED',
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
    private $name;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $phone;

    /**
     * gewünschte Einheit
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\RentalUnit")
     */
    private $unit;

    /**
     * @ORM\Column(type="date")
     */
    private $requestDate;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $status = 'NEW';

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $remark;

    public function __construct()
    {
        $this->requestDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getUnit(): ?RentalUnit
    {
        return $this->unit;
    }

    public function setUnit(?RentalUnit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getRequestDate(): ?\DateTimeInterface
    {
        return $this->requestDate;
    }

    public function setRequestDate(\DateTimeInterface $requestDate): self
    {
        $this->requestDate = $requestDate;

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

    public function getStatusLabel(): string
    {
        $labels = array_flip(self::STATUSES);

        return $labels[$this->status] ?? (string) $this->status;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): self
    {
        $this->remark = $remark;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}
