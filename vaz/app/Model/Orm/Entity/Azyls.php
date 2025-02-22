<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'azyls')]
#[\AllowDynamicProperties] //todo: tohle se musí vyřešit otázka je proč se to tu objevilo.
class Azyl
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public int $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $azylName;

    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $bankAccount;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $bankCode;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $bankSpecificCode;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $phoneNumber;

    #[ORM\OneToMany(mappedBy: "azyl", targetEntity: Animal::class)]
    private ?Collection $animals;

    #[ORM\OneToMany(mappedBy: "azyl", targetEntity: Adoption::class)]
    private ?Collection $adoptions;

    #[ORM\OneToMany(mappedBy: "payments", targetEntity: Payments::class)]
    private ?Collection $payments;

    #[ORM\OneToMany(mappedBy: "collections", targetEntity: Collections::class)]
    private ?Collection $collections;

    #[ORM\OneToMany(mappedBy: "azyl", targetEntity: News::class)]
    private ?Collection $news = null;

    #[ORM\OneToOne(targetEntity: Photo::class, fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Photo $mainPhoto;

    #[ORM\OneToMany(mappedBy: "azyl", targetEntity: "Photo")]
    public ?Collection $photos;

    #[ORM\OneToMany(mappedBy: 'reviewer', targetEntity: UsersRatings::class)]
    private ?Collection $reviewerRatings;

    #[ORM\Column(type: 'string', length: 34 ,nullable: true)]
    private ?string $random;

    #[ORM\OneToMany(mappedBy: "azyl", targetEntity: Contracts::class)]
    private ?Collection $contracts;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $messageAddress;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $city;

    #[ORM\Column(type: 'string', length: 512, nullable: true)]
    private ?string $web;

    #[ORM\Column(type: 'string', length: 512, nullable: true)]
    private ?string $email;

    public function __toString(): string
    {
        return (string)$this->id;  // nebo jiný identifikátor entity Azyl
    }
    public function __construct()
    {
        $this->animals = new ArrayCollection();
        $this->adoptions = new ArrayCollection();
        $this->news = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->reviewerRatings = new ArrayCollection();

    }
    public function toArray(): array
    {
        return ['azylName' => $this->azylName
        , 'description' => $this->description
        , 'bankAccount' => $this->bankAccount
        , 'bankCode' => $this->bankCode
        , 'bankSpecificCode' => $this->bankSpecificCode
        , 'phoneNumber' => $this->phoneNumber
        , 'mainPhoto' => $this->mainPhoto->toArray()]
            ;
    }

    public function getReviewerRatings(): Collection
    {
        return $this->reviewerRatings;
    }

    public function getAzylName(): ?string
    {
        return $this->azylName;
    }

    /**
     * @return mixed
     */
    public function getAnimals() : Collection
    {
        return $this->animals;
    }

    public function setAzylName(string $azylName): Azyl
    {
        $this->azylName = $azylName;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): Azyl
    {
        $this->description = $description;
        return $this;
    }

    public function getBankAccount(): ?string
    {
        return $this->bankAccount;
    }

    public function setBankAccount(string $bankAccount): Azyl
    {
        $this->bankAccount = $bankAccount;
        return $this;
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): Azyl
    {
        $this->bankCode = $bankCode;
        return $this;
    }

    public function getBankSpecificCode(): string
    {
        return $this->bankSpecificCode;
    }

    public function setBankSpecificCode(string $bankSpecificCode): Azyl
    {
        $this->bankSpecificCode = $bankSpecificCode;
        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): Azyl
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMainPhoto(): ?Photo
    {
        return $this->mainPhoto;
    }

    public function setMainPhoto(?Photo $mainPhoto): Azyl
    {
        $this->mainPhoto = $mainPhoto;
        return $this;
    }

    public function getNews(): ?Collection
    {
        return $this->news;
    }

    public function getAzylNews(): ?Collection
    {
        return $this->news->matching(Criteria::create()
            ->where(Criteria::expr()->eq("deleted", false))
            ->andWhere(Criteria::expr()->lte("visibleFrom", new \DateTimeImmutable('now')))
            ->orderBy(["createdAt" => 'DESC']));
    }

    public function getAdoptions(): ?Collection
    {
        return $this->adoptions;
    }

    public function setAdoptions(?Collection $adoptions): Azyl
    {
        $this->adoptions = $adoptions;
        return $this;
    }

    public function getPayments(): ?Collection
    {
        return $this->payments;
    }

    public function setPayments(?Collection $payments): Azyl
    {
        $this->payments = $payments;
        return $this;
    }

    public function getCollections(): ?Collection
    {
        return $this->collections;
    }

    public function setCollections(?Collection $collections): Azyl
    {
        $this->collections = $collections;
        return $this;
    }

    public function getPhotos(): ?Collection
    {
        return $this->photos;
    }

    public function setPhotos(?Collection $photos): Azyl
    {
        $this->photos = $photos;
        return $this;
    }

    public function getRandom(): ?string
    {
        return $this->random;
    }

    public function setRandom(?string $random): Azyl
    {
        $this->random = $random;
        return $this;
    }

    public function getContracts(): ?Collection
    {
        return $this->contracts;
    }

    public function setContracts(?Collection $contracts): Azyl
    {
        $this->contracts = $contracts;
        return $this;
    }

    public function getMessageAddress(): ?string
    {
        return $this->messageAddress;
    }

    public function setMessageAddress(?string $messageAddress): Azyl
    {
        $this->messageAddress = $messageAddress;
        return $this;
    }

    public function getCity(): ?int
    {
        return $this->city;
    }

    public function setCity(?int $city): Azyl
    {
        $this->city = $city;
        return $this;
    }

    public function getWeb(): ?string
    {
        return $this->web;
    }

    public function setWeb(?string $web): Azyl
    {
        $this->web = $web;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): Azyl
    {
        $this->email = $email;
        return $this;
    }

}