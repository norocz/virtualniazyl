<?php

declare(strict_types = 1);

namespace App\Model\Orm\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'paymentsIn')]

class PaymentsIn  //entita pro příchozí platby - naplníse ze staženého vypisu
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $accountId;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $datum;

    #[ORM\Column(type: 'float')]
    private float $objem;

    #[ORM\Column(type: 'string', length: 255)]
    private string $protiucet;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nazevProtiuctu;

    #[ORM\Column(type: 'string', length: 255)]
    private string $kodBanky;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $bankName = null;

    #[ORM\Column(type: "string", length: 4, nullable: true)]
    private ?string $ks = null;

    #[ORM\Column(type: "string", length: 10, nullable: true)]
    private ?string $vs = null;

    #[ORM\Column(type: "string", length: 10, nullable: true)]
    private ?string $ss = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $userIdentification = null;

    #[ORM\Column(type: "string", length: 140, nullable: true)]
    private ?string $recipientMessage = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $operationType;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $executedBy = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $details = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: "string", length: 11, nullable: true)]
    private ?string $bic = null;

    #[ORM\Column(type: "string", length: 12, nullable: true)]
    private ?string $orderId = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $payerReference = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): PaymentsIn
    {
        $this->id = $id;
        return $this;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function setAccountId(string $accountId): PaymentsIn
    {
        $this->accountId = $accountId;
        return $this;
    }

    public function getDatum(): DateTimeImmutable
    {
        return $this->datum;
    }

    public function setDatum(DateTimeImmutable $datum): PaymentsIn
    {
        $this->datum = $datum;
        return $this;
    }

    public function getObjem(): float
    {
        return $this->objem;
    }

    public function setObjem(float $objem): PaymentsIn
    {
        $this->objem = $objem;
        return $this;
    }

    public function getProtiucet(): string
    {
        return $this->protiucet;
    }

    public function setProtiucet(string $protiucet): PaymentsIn
    {
        $this->protiucet = $protiucet;
        return $this;
    }

    public function getNazevProtiuctu(): string
    {
        return $this->nazevProtiuctu;
    }

    public function setNazevProtiuctu(string $nazevProtiuctu): PaymentsIn
    {
        $this->nazevProtiuctu = $nazevProtiuctu;
        return $this;
    }

    public function getKodBanky(): string
    {
        return $this->kodBanky;
    }

    public function setKodBanky(string $kodBanky): PaymentsIn
    {
        $this->kodBanky = $kodBanky;
        return $this;
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(?string $bankName): PaymentsIn
    {
        $this->bankName = $bankName;
        return $this;
    }

    public function getKs(): ?string
    {
        return $this->ks;
    }

    public function setKs(?string $ks): PaymentsIn
    {
        $this->ks = $ks;
        return $this;
    }

    public function getVs(): ?string
    {
        return $this->vs;
    }

    public function setVs(?string $vs): PaymentsIn
    {
        $this->vs = $vs;
        return $this;
    }

    public function getSs(): ?string
    {
        return $this->ss;
    }

    public function setSs(?string $ss): PaymentsIn
    {
        $this->ss = $ss;
        return $this;
    }

    public function getUserIdentification(): ?string
    {
        return $this->userIdentification;
    }

    public function setUserIdentification(?string $userIdentification): PaymentsIn
    {
        $this->userIdentification = $userIdentification;
        return $this;
    }

    public function getRecipientMessage(): ?string
    {
        return $this->recipientMessage;
    }

    public function setRecipientMessage(?string $recipientMessage): PaymentsIn
    {
        $this->recipientMessage = $recipientMessage;
        return $this;
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    public function setOperationType(string $operationType): PaymentsIn
    {
        $this->operationType = $operationType;
        return $this;
    }

    public function getExecutedBy(): ?string
    {
        return $this->executedBy;
    }

    public function setExecutedBy(?string $executedBy): PaymentsIn
    {
        $this->executedBy = $executedBy;
        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): PaymentsIn
    {
        $this->details = $details;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): PaymentsIn
    {
        $this->comment = $comment;
        return $this;
    }

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function setBic(?string $bic): PaymentsIn
    {
        $this->bic = $bic;
        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(?string $orderId): PaymentsIn
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function getPayerReference(): ?string
    {
        return $this->payerReference;
    }

    public function setPayerReference(?string $payerReference): PaymentsIn
    {
        $this->payerReference = $payerReference;
        return $this;
    }


}