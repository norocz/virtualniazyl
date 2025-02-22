<?php

namespace App\Model\Orm\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
#[ORM\Table(name: "system_settings")]

class SystemSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $relevantFrom;

    #[ORM\Column(type: 'float')]
    private float $fee; //procento poplatek z platby kvůli platební bráně

    #[ORM\Column(type: 'integer')]
    private int $dph; //ppřípadné DPH na služby

    #[ORM\Column(type: 'string', options: ['cz' =>'Česky', 'sk' => 'Slovenčina', 'en' => 'English', 'ddr' => 'Deutsch', 'fr' => 'Français', 'ru' => 'Русский (Russkiy)', 'pl' => 'Polski'])]
    private ?string $language = 'cz';

    #[ORM\Column(type: 'integer', options: [1 => 1, 14 => 14, 30 => 39, 60 => 60, 120 => 120])]
    private ?int $payOutInterval = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $depricated = false;

    #[ORM\Column(type: 'boolean')]
    private bool $cron = true;

    #[ORM\Column(type: 'boolean')]
    private bool $analyticsGarbage = false;

    #[ORM\Column(type: 'boolean')]
    private bool $databaseClear = false;

    #[ORM\Column(type: 'boolean')]
    private bool $dphUse = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $nextPayOut;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $lastPayOut;

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): SystemSettings
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getRelevantFrom(): DateTimeImmutable
    {
        return $this->relevantFrom;
    }

    public function setRelevantFrom(DateTimeImmutable $relevantFrom): SystemSettings
    {
        $this->relevantFrom = $relevantFrom;
        return $this;
    }

    public function getFee(): float
    {
        return $this->fee;
    }

    public function setFee(float $fee): SystemSettings
    {
        $this->fee = $fee;
        return $this;
    }

    public function getDph(): int
    {
        return $this->dph;
    }

    public function setDph(int $dph): SystemSettings
    {
        $this->dph = $dph;
        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setlanguage(?string $language): SystemSettings
    {
        $this->language = $language;
        return $this;
    }

    public function getPayOutInterval(): ?int
    {
        return $this->payOutInterval;
    }

    public function setPayOutInterval(?int $payOutInterval): SystemSettings
    {
        $this->payOutInterval = $payOutInterval;
        return $this;
    }

    public function getDepricated(): ?bool
    {
        return $this->depricated;
    }

    public function setDepricated(?bool $depricated): SystemSettings
    {
        $this->depricated = $depricated;
        return $this;
    }

    public function isCron(): bool
    {
        return $this->cron;
    }

    public function setCron(bool $cron): SystemSettings
    {
        $this->cron = $cron;
        return $this;
    }

    public function isAnalyticsGarbage(): bool
    {
        return $this->analyticsGarbage;
    }

    public function setAnalyticsGarbage(bool $analyticsGarbage): SystemSettings
    {
        $this->analyticsGarbage = $analyticsGarbage;
        return $this;
    }

    public function isDatabaseClear(): bool
    {
        return $this->databaseClear;
    }

    public function setDatabaseClear(bool $databaseClear): SystemSettings
    {
        $this->databaseClear = $databaseClear;
        return $this;
    }

    public function isDphUse(): bool
    {
        return $this->dphUse;
    }

    public function setDphUse(bool $dphUse): SystemSettings
    {
        $this->dphUse = $dphUse;
        return $this;
    }

    public function getNextPayOut(): DateTimeImmutable
    {
        return $this->nextPayOut;
    }

    public function setNextPayOut(DateTimeImmutable $nextPayOut): SystemSettings
    {
        $this->nextPayOut = $nextPayOut;
        return $this;
    }

    public function getLastPayOut(): ?DateTimeImmutable
    {
        return $this->lastPayOut;
    }

    public function setLastPayOut(?DateTimeImmutable $lastPayOut): SystemSettings
    {
        $this->lastPayOut = $lastPayOut;
        return $this;
    }



}