<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Übergabe / Rückgabe einer Mieteinheit
 *
 * @ORM\Entity(repositoryClass="App\Repository\HandoverRepository")
 */
class Handover
{
    const MOVE_IN = 'MOVE_IN';
    const MOVE_OUT = 'MOVE_OUT';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tenancy", inversedBy="handovers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tenancy;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $type = self::MOVE_IN;

    /**
     * Zählerstände als Freitext, z. B. "Strom 12345, Wasser 234"
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $meterReadings;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $keyCount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $remarks;

    /**
     * festgestellte Mängel
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $defects;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTenancy(): ?Tenancy
    {
        return $this->tenancy;
    }

    public function setTenancy(?Tenancy $tenancy): self
    {
        $this->tenancy = $tenancy;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getMeterReadings(): ?string
    {
        return $this->meterReadings;
    }

    public function setMeterReadings(?string $meterReadings): self
    {
        $this->meterReadings = $meterReadings;

        return $this;
    }

    public function getKeyCount(): ?int
    {
        return $this->keyCount;
    }

    public function setKeyCount(?int $keyCount): self
    {
        $this->keyCount = $keyCount;

        return $this;
    }

    public function getRemarks(): ?string
    {
        return $this->remarks;
    }

    public function setRemarks(?string $remarks): self
    {
        $this->remarks = $remarks;

        return $this;
    }

    public function getDefects(): ?string
    {
        return $this->defects;
    }

    public function setDefects(?string $defects): self
    {
        $this->defects = $defects;

        return $this;
    }
}
