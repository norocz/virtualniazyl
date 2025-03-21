<?php

declare(strict_types=1);

namespace App\Model\Orm\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: "contract_parts")]

class ContractParts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255 ,nullable: true)]
     private string $name;

    #[ORM\Column(type: 'string', length: 512000 ,nullable: true)]
    private string $content;

    #[ORM\Column(type: 'integer', length: 255 ,nullable: true)]
    private int $partNumber;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool $inUsage = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $closedAt;

    #[ORM\ManyToMany(targetEntity: Contracts::class, mappedBy: "contractParts")]
    private Collection $contracts;

    #[ORM\OneToOne(targetEntity: ContractParts::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?ContractParts $oldVersion = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): ContractParts
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ContractParts
    {
        $this->name = $name;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): ContractParts
    {
        $this->content = $content;
        return $this;
    }

    public function getPartNumber(): int
    {
        return $this->partNumber;
    }

    public function setPartNumber(int $partNumber): ContractParts
    {
        $this->partNumber = $partNumber;
        return $this;
    }

    public function isInUsage(): bool
    {
        return $this->inUsage;
    }

    public function setInUsage(bool $inUsage): ContractParts
    {
        $this->inUsage = $inUsage;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): ContractParts
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?DateTimeImmutable $closedAt): ContractParts
    {
        $this->closedAt = $closedAt;
        return $this;
    }

    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    public function setContracts(Collection $contracts): ContractParts
    {
        $this->contracts = $contracts;
        return $this;
    }

    public function getOldVersion(): ?ContractParts
    {
        return $this->oldVersion;
    }

    public function setOldVersion(?ContractParts $oldVersion): ContractParts
    {
        $this->oldVersion = $oldVersion;
        return $this;
    }
}