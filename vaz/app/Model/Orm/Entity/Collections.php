<?php

declare(strict_types = 1);

namespace App\Model\Orm\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'collections')]

class Collections
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Azyl::class, cascade: ['persist'], inversedBy: "azyl")]
    private ?Azyl $azyl;

    #[ORM\ManyToOne(targetEntity: Users::class, cascade: ['persist'], inversedBy: 'users')]
    private ?Users $user;

    #[ORM\OneToMany(mappedBy: 'collection', targetEntity: Payments::class, cascade: ['persist'])]
    private ?Collection $payments;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $endingAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $startAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $extendTo = null;
    #[ORM\Column(type: 'integer', length: 10, unique: true, nullable: true)]
    private ?int $collectionKey; // variabilní symbol max 10 znaků a jen čísla 5 čísel se vygeneruje 5 čísel je ID azylu doplněné o nuly

    #[ORM\Column(type: 'string', length: 500, nullable: false)]
    private string $collectionName;

    #[ORM\Column(type: 'string', length: 4096, nullable: true)]
    private ?string $collectionDescription = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $minimalAmount = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $resultAmount = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $extendedAmount = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'boolean')]
    private bool $extend = false;

    #[ORM\Column(type: 'boolean')]
    private bool $approved = false;

    #[ORM\Column(type: 'string', options: ['czk' => 'Kč','eur' => 'EU', 'usd' => 'USD', 'ru' => 'Рубль (Rublʹ)', 'zl' => 'Złoty'])]
    private string $currency = 'czk';

    #[ORM\OneToOne(inversedBy: 'collections', targetEntity: Photo::class, cascade: ['persist'], fetch: "EAGER")]
    private ?Photo $photo;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Collections
    {
        $this->id = $id;
        return $this;
    }

    public function getAzyl(): ?Azyl
    {
        return $this->azyl;
    }

    public function setAzyl(?Azyl $azyl): Collections
    {
        $this->azyl = $azyl;
        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): Collections
    {
        $this->user = $user;
        return $this;
    }

    public function getPayments(): ?Collection
    {
        return $this->payments;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): Collections
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getEndingAt(): DateTimeImmutable
    {
        return $this->endingAt;
    }

    public function setEndingAt(DateTimeImmutable $endingAt): Collections
    {
        $this->endingAt = $endingAt;
        return $this;
    }

    public function getStartAt(): DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(DateTimeImmutable $startAt): Collections
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function getExtendTo(): ?DateTimeImmutable
    {
        return $this->extendTo;
    }

    public function setExtendTo(DateTimeImmutable $extendTo): Collections
    {
        $this->extendTo = $extendTo;
        return $this;
    }

    public function getCollectionKey(): ?int
    {
        return $this->collectionKey;
    }

    public function setCollectionKey(int $collectionKey): Collections
    {
        $this->collectionKey = $collectionKey;
        return $this;
    }

    public function getCollectionName(): string
    {
        return $this->collectionName;
    }

    public function setCollectionName(string $collectionName): Collections
    {
        $this->collectionName = $collectionName;
        return $this;
    }

    public function getCollectionDescription(): string
    {
        return $this->collectionDescription;
    }

    public function setCollectionDescription(string $collectionDescription): Collections
    {
        $this->collectionDescription = $collectionDescription;
        return $this;
    }

    public function getMinimalAmount(): int
    {
        return $this->minimalAmount;
    }

    public function setMinimalAmount(int $minimalAmount): Collections
    {
        $this->minimalAmount = $minimalAmount;
        return $this;
    }

    public function getResultAmount(): int
    {
        return $this->resultAmount;
    }

    public function setResultAmount(int $resultAmount): Collections
    {
        $this->resultAmount = $resultAmount;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): Collections
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function isExtend(): bool
    {
        return $this->extend;
    }

    public function setExtend(bool $extend): Collections
    {
        $this->extend = $extend;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): Collections
    {
        $this->currency = $currency;
        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto(?Photo $photo): Collections
    {
        $this->photo = $photo;
        return $this;
    }

    public function getExtendedAmount(): ?int
    {
        return $this->extendedAmount;
    }

    public function setExtendedAmount(?int $extendedAmount): Collections
    {
        $this->extendedAmount = $extendedAmount;
        return $this;
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): Collections
    {
        $this->approved = $approved;
        return $this;
    }

}