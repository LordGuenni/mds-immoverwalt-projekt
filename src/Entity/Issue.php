<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Meldung zur Mietsache (Vorgang)
 *
 * @ORM\Entity(repositoryClass="App\Repository\IssueRepository")
 */
class Issue
{
    const STATUSES = [
        'Neu' => 'NEW',
        'In Prüfung' => 'IN_REVIEW',
        'In Bearbeitung' => 'IN_PROGRESS',
        'Erledigt' => 'RESOLVED',
        'Geschlossen' => 'CLOSED',
        'Abgelehnt' => 'REJECTED',
    ];

    const CATEGORIES = [
        'Heizung' => 'HEIZUNG',
        'Wasser / Sanitär' => 'SANITAER',
        'Elektrik / Licht' => 'ELEKTRO',
        'Fenster / Türen' => 'FENSTER',
        'Aufzug' => 'AUFZUG',
        'Schimmel / Feuchtigkeit' => 'SCHIMMEL',
        'Beschwerde' => 'BESCHWERDE',
        'Sonstiges' => 'SONSTIGES',
    ];

    const PRIORITIES = [
        'niedrig' => 1,
        'normal' => 2,
        'hoch' => 3,
        'dringend' => 4,
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
    private $issueNumber;

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
     * meldender Mieter (falls bekannt)
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenant")
     */
    private $tenant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $category = 'SONSTIGES';

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $priority = 2;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $status = 'NEW';

    /**
     * zuständiger Mitarbeiter
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $assignee;

    /**
     * Anhänge (Dateinamen, kommagetrennt)
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $attachments;

    /**
     * interne Notizen - nicht für Mieter bestimmt
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $internalNotes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Task", mappedBy="issue")
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\IssueStatusChange", mappedBy="issue")
     * @ORM\OrderBy({"changedAt" = "ASC"})
     */
    private $statusChanges;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->tasks = new ArrayCollection();
        $this->statusChanges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIssueNumber(): ?string
    {
        return $this->issueNumber;
    }

    public function setIssueNumber(string $issueNumber): self
    {
        $this->issueNumber = $issueNumber;

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

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function setTenant(?Tenant $tenant): self
    {
        $this->tenant = $tenant;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
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

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getPriorityLabel(): string
    {
        $labels = array_flip(self::PRIORITIES);

        return $labels[$this->priority] ?? '';
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

    public function getAssignee(): ?User
    {
        return $this->assignee;
    }

    public function setAssignee(?User $assignee): self
    {
        $this->assignee = $assignee;

        return $this;
    }

    public function getAttachments(): ?string
    {
        return $this->attachments;
    }

    public function setAttachments(?string $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function getInternalNotes(): ?string
    {
        return $this->internalNotes;
    }

    public function setInternalNotes(?string $internalNotes): self
    {
        $this->internalNotes = $internalNotes;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * @return Collection|IssueStatusChange[]
     */
    public function getStatusChanges(): Collection
    {
        return $this->statusChanges;
    }

    public function __toString()
    {
        return $this->issueNumber.' - '.mb_substr((string) $this->description, 0, 40);
    }
}
