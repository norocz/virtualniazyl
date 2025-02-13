<?php

declare(strict_types = 1);

namespace App\Model\Orm\Entity;

use App\Model\Orm\Enums\PaymentStatusEnum;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'payments')]

class Payments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer', length: 255)]
    private int $pay;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $payedAt = null;

    #[ORM\Column(type: 'integer', length: 10)]
    private int $variableSymbol;

    #[ORM\Column(type: 'string', length: 255)]
    private string $comment;

    #[ORM\Column(type: 'string', length: 6)]
    private string $currency;

    #[ORM\ManyToOne(targetEntity: Collections::class, inversedBy: 'payments')]
    private ?Collections $collections;

    #[ORM\ManyToOne(targetEntity: Azyl::class, cascade: ['persist'], inversedBy: "azyl")]
    private ?Azyl $azyl;

    #[ORM\ManyToOne(targetEntity: Adoption::class, cascade: ['persist'], inversedBy: "adoption")]
    private ?Adoption $adoption;

    #[ORM\Column(type: 'float', length: 10, nullable: true)]
    private ?float $fee;

    #[ORM\Column(type: Types::STRING, enumType: PaymentStatusEnum::class)]
    private PaymentStatusEnum $paymentStatus;


    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Payments
    {
        $this->id = $id;
        return $this;
    }

    public function getPay(): int
    {
        return $this->pay;
    }

    public function setPay(int $pay): Payments
    {
        $this->pay = $pay;
        return $this;
    }

    public function getPaymentStatus(): PaymentStatusEnum
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(PaymentStatusEnum $paymentStatus): Payments
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }


    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): Payments
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getPayedAt(): ?DateTimeImmutable
    {
        return $this->payedAt;
    }

    public function setPayedAt(?DateTimeImmutable $payedAt): Payments
    {
        $this->payedAt = $payedAt;
        return $this;
    }

    public function getVariableSymbol(): int
    {
        return $this->variableSymbol;
    }

    public function setVariableSymbol(int $variableSymbol): Payments
    {
        $this->variableSymbol = $variableSymbol;
        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): Payments
    {
        $this->comment = $comment;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): Payments
    {
        $this->currency = $currency;
        return $this;
    }

    public function getCollections(): ?Collections
    {
        return $this->collections;
    }

    public function setCollections(?Collections $collections): Payments
    {
        $this->collections = $collections;
        return $this;
    }

    public function getAzyl(): ?Azyl
    {
        return $this->azyl;
    }

    public function setAzyl(?Azyl $azyl): Payments
    {
        $this->azyl = $azyl;
        return $this;
    }

    public function getAdoption(): ?Adoption
    {
        return $this->adoption;
    }

    public function setAdoption(?Adoption $adoption): Payments
    {
        $this->adoption = $adoption;
        return $this;
    }

    public function getFee(): ?float
    {
        return $this->fee;
    }

    public function setFee(?float $fee): Payments
    {
        $this->fee = $fee;
        return $this;
    }




}