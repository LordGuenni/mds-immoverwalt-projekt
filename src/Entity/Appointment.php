<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Termin / Information
 *
 * Ein Termin kann sich auf unterschiedliche Ebenen beziehen (Bestand, Objekt,
 * Gebäude, Einheit, Mietverhältnis, Meldung, Aufgabe). Der Bezug wird über
 * refType + refId gespeichert.
 *
 * @ORM\Entity(repositoryClass="App\Repository\AppointmentRepository")
 */
class Appointment
{
    const TYPES = [
        'Rauchmelderprüfung' => 'RAUCHMELDER',
        'Heizungswartung' => 'HEIZUNG',
        'Zählerablesung' => 'ZAEHLER',
        'Aufzugswartung' => 'AUFZUG',
        'Besichtigung' => 'BESICHTIGUNG',
        'Wohnungsübergabe' => 'UEBERGABE',
        'Handwerkertermin' => 'HANDWERKER',
        'Wasserabschaltung' => 'WASSER',
        'Sonstiges' => 'SONSTIGES',
    ];

    const REF_TYPES = [
        'gesamter Bestand' => 'ALL',
        'Objekt' => 'PROPERTY',
        'Gebäude' => 'BUILDING',
        'Mieteinheit' => 'UNIT',
        'Mietverhältnis' => 'TENANCY',
        'Meldung' => 'ISSUE',
        'Aufgabe' => 'TASK',
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
    private $title;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $type = 'SONSTIGES';

    /**
     * @ORM\Column(type="datetime")
     */
    private $startsAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endsAt;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $refType = 'ALL';

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $refId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getTypeLabel(): string
    {
        $labels = array_flip(self::TYPES);

        return $labels[$this->type] ?? (string) $this->type;
    }

    public function getStartsAt(): ?\DateTimeInterface
    {
        return $this->startsAt;
    }

    public function setStartsAt(\DateTimeInterface $startsAt): self
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getEndsAt(): ?\DateTimeInterface
    {
        return $this->endsAt;
    }

    public function setEndsAt(?\DateTimeInterface $endsAt): self
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    public function getRefType(): ?string
    {
        return $this->refType;
    }

    public function setRefType(string $refType): self
    {
        $this->refType = $refType;

        return $this;
    }

    public function getRefTypeLabel(): string
    {
        $labels = array_flip(self::REF_TYPES);

        return $labels[$this->refType] ?? (string) $this->refType;
    }

    public function getRefId(): ?int
    {
        return $this->refId;
    }

    public function setRefId(?int $refId): self
    {
        $this->refId = $refId;

        return $this;
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
