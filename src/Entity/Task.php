<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Interne Aufgabe (z. B. aus einer Meldung entstanden)
 *
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    const STATUSES = [
        'Offen' => 'OPEN',
        'Zugewiesen' => 'ASSIGNED',
        'In Bearbeitung' => 'IN_PROGRESS',
        'Wartet' => 'WAITING',
        'Erledigt' => 'DONE',
        'Abgebrochen' => 'CANCELLED',
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     */
    private $taskNumber;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

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
     * optionaler Bezug zu einer Meldung
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Issue", inversedBy="tasks")
     */
    private $issue;

    /**
     * Verantwortlicher (intern)
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $responsible;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ServiceProvider")
     */
    private $serviceProvider;

    /**
     * Ansprechpartner beim Dienstleister
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $contactPerson;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dueDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $priority = 2;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $status = 'OPEN';

    /**
     * vereinbarter Termin (z. B. Handwerker vor Ort)
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $scheduledAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskNumber(): ?string
    {
        return $this->taskNumber;
    }

    public function setTaskNumber(string $taskNumber): self
    {
        $this->taskNumber = $taskNumber;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getIssue(): ?Issue
    {
        return $this->issue;
    }

    public function setIssue(?Issue $issue): self
    {
        $this->issue = $issue;

        return $this;
    }

    public function getResponsible(): ?User
    {
        return $this->responsible;
    }

    public function setResponsible(?User $responsible): self
    {
        $this->responsible = $responsible;

        return $this;
    }

    public function getServiceProvider(): ?ServiceProvider
    {
        return $this->serviceProvider;
    }

    public function setServiceProvider(?ServiceProvider $serviceProvider): self
    {
        $this->serviceProvider = $serviceProvider;

        return $this;
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

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

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

    public function isOpen(): bool
    {
        return !in_array($this->status, ['DONE', 'CANCELLED']);
    }

    public function getScheduledAt(): ?\DateTimeInterface
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(?\DateTimeInterface $scheduledAt): self
    {
        $this->scheduledAt = $scheduledAt;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function __toString()
    {
        return $this->taskNumber.' - '.$this->title;
    }
}
