<?php

declare(strict_types = 1);

namespace App\Model\Orm\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'paymentsOut')]

class PaymentsOut  //entita pro odchozí platby, připravý se podle výpisu a seznamu připravených plateb tzn. spočítá se kolik se má poslat
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: "string", length: 16)]
    private string $accountFrom;

    #[ORM\Column(type: "string", length: 3)]
    private string $currency;

    #[ORM\Column(type: "decimal", precision: 18, scale: 2)]
    private float $amount;

    #[ORM\Column(type: "string", length: 16)]
    private string $accountTo;

    #[ORM\Column(type: "string", length: 4)]
    private string $bankCode;

    #[ORM\Column(type: "string", length: 4, nullable: true)]
    private ?string $ks = null;

    #[ORM\Column(type: "string", length: 10, nullable: true)]
    private ?string $vs = null;

    #[ORM\Column(type: "string", length: 10, nullable: true)]
    private ?string $ss = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $datum;

    #[ORM\Column(type: "string", length: 140, nullable: true)]
    private ?string $messageForRecipient = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: "string", length: 3, nullable: true)]
    private ?string $paymentReason = null;

    #[ORM\Column(type: "string", length: 6, nullable: true)]
    private ?string $paymentType = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): PaymentsOut
    {
        $this->id = $id;
        return $this;
    }

    public function getAccountFrom(): string
    {
        return $this->accountFrom;
    }

    public function setAccountFrom(string $accountFrom): PaymentsOut
    {
        $this->accountFrom = $accountFrom;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): PaymentsOut
    {
        $this->currency = $currency;
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): PaymentsOut
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAccountTo(): string
    {
        return $this->accountTo;
    }

    public function setAccountTo(string $accountTo): PaymentsOut
    {
        $this->accountTo = $accountTo;
        return $this;
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): PaymentsOut
    {
        $this->bankCode = $bankCode;
        return $this;
    }

    public function getKs(): ?string
    {
        return $this->ks;
    }

    public function setKs(?string $ks): PaymentsOut
    {
        $this->ks = $ks;
        return $this;
    }

    public function getVs(): ?string
    {
        return $this->vs;
    }

    public function setVs(?string $vs): PaymentsOut
    {
        $this->vs = $vs;
        return $this;
    }

    public function getSs(): ?string
    {
        return $this->ss;
    }

    public function setSs(?string $ss): PaymentsOut
    {
        $this->ss = $ss;
        return $this;
    }

    public function getDatum(): DateTimeImmutable
    {
        return $this->datum;
    }

    public function setDatum(DateTimeImmutable $datum): PaymentsOut
    {
        $this->datum = $datum;
        return $this;
    }

    public function getMessageForRecipient(): ?string
    {
        return $this->messageForRecipient;
    }

    public function setMessageForRecipient(?string $messageForRecipient): PaymentsOut
    {
        $this->messageForRecipient = $messageForRecipient;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): PaymentsOut
    {
        $this->comment = $comment;
        return $this;
    }

    public function getPaymentReason(): ?string
    {
        return $this->paymentReason;
    }

    public function setPaymentReason(?string $paymentReason): PaymentsOut
    {
        $this->paymentReason = $paymentReason;
        return $this;
    }

    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    public function setPaymentType(?string $paymentType): PaymentsOut
    {
        $this->paymentType = $paymentType;
        return $this;
    }



}