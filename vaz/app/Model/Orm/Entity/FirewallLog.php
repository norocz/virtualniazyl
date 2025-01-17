<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'firewall_logs')]
class FirewallLog
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type: 'string', length: 45)]
    private string $ip;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'integer')]
    private int $attempts;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $action; // Možné hodnoty: 'blocked', 'unblocked', 'firewall_blocked'

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $notes;


    // Getters and setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function setAttempts(int $attempts): self
    {
        $this->attempts = $attempts;
        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): self
    {
        $this->action = $action;
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
}