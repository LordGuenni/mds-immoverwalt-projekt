<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Historie der Statusänderungen einer Meldung
 *
 * @ORM\Entity
 */
class IssueStatusChange
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Issue", inversedBy="statusChanges")
     * @ORM\JoinColumn(nullable=false)
     */
    private $issue;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $oldStatus;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $newStatus;

    /**
     * @ORM\Column(type="datetime")
     */
    private $changedAt;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $changedBy;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    public function __construct(Issue $issue, ?string $oldStatus, string $newStatus, ?string $changedBy, ?string $comment = null)
    {
        $this->issue = $issue;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->changedBy = $changedBy;
        $this->comment = $comment;
        $this->changedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIssue(): Issue
    {
        return $this->issue;
    }

    public function getOldStatus(): ?string
    {
        return $this->oldStatus;
    }

    public function getNewStatus(): string
    {
        return $this->newStatus;
    }

    public function getChangedAt(): \DateTimeInterface
    {
        return $this->changedAt;
    }

    public function setChangedAt(\DateTimeInterface $changedAt): self
    {
        $this->changedAt = $changedAt;

        return $this;
    }

    public function getChangedBy(): ?string
    {
        return $this->changedBy;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
