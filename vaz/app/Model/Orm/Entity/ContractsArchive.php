<?php

declare(strict_types=1);
namespace App\Model\Orm\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
#[ORM\Entity]
#[ORM\Table(name: "contracts_archive")]

class ContractsArchive
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type:'integer')]
    private int $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type:'json')]
    private array $contract;

    #[ORM\Column(type:'json')]
    private array $user;

    #[ORM\Column(type:'json')]
    private array $azyl;

    #[ORM\Column(type: "string", length: 255)]
    private string $fileName;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): ContractsArchive
    {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): ContractsArchive
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getContract(): array
    {
        return $this->contract;
    }

    public function setContract(array $contract): ContractsArchive
    {
        $this->contract = $contract;
        return $this;
    }

    public function getUser(): array
    {
        return $this->user;
    }

    public function setUser(array $user): ContractsArchive
    {
        $this->user = $user;
        return $this;
    }

    public function getAzyl(): array
    {
        return $this->azyl;
    }

    public function setAzyl(array $azyl): ContractsArchive
    {
        $this->azyl = $azyl;
        return $this;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): ContractsArchive
    {
        $this->fileName = $fileName;
        return $this;
    }

}