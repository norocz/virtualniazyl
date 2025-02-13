<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;

use App\Model\Orm\Enums\RoleTypeEnum;
use Brick\PhoneNumber\Doctrine\Types\PhoneNumberType;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
#[ORM\Table(name: 'users')]
class Users
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public int $id;

    #[ORM\Column(type: 'string', length: 255)]
    public string $userName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $firstName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lastName;

    #[ORM\Column(type: 'string', length: 255)]
    public string $email;

    #[ORM\Column(type:RoleTypeEnum::ROLE_TYPE_ENUM, length: 255)]
    private string $role;

    #[ORM\Column(type: 'string', length: 512)]
    private string $password;

    #[ORM\Column(type: 'string', length: 4096, nullable: true)]
    private ?string $phone;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $mailVerifyToken = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(targetEntity: "Users")]
    #[ORM\JoinColumn(name: "created_by", referencedColumnName: "id")]
    public ?Users $createdBy;

    #[ORM\ManyToOne(targetEntity: "Users")]
    #[ORM\JoinColumn(name: "updated_by", referencedColumnName: "id")]
    public ?Users $updatedBy = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $verified;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Photo::class)]
    public ?Collection $photos = null;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Adoption::class)]
    private ?Collection $adoptions;

    #[ORM\OneToMany(mappedBy: "sender", targetEntity: "Messages")]
    public ?Collection $sentMessages;

    #[ORM\OneToMany(mappedBy: "receiver", targetEntity: "Messages")]
    private ?Collection $receivedMessages;

    #[ORM\OneToMany(mappedBy: "collections", targetEntity: Collections::class)]
    private ?Collection $collections;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $messageAddress;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $deleted;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $baned;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $mailverified;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $phoneVerified;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: News::class)]
    private ?Collection $news;

    #[ORM\OneToMany(mappedBy: "author", targetEntity: "Pages")]
    public ?Collection $pages;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $adoptionVerification;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $legalTerms;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $city = null;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private ?string $houseNumber;
    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private ?string $orientationNumber;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $street;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private ?string $zipCode;

    #[ORM\OneToMany(mappedBy: "owner", targetEntity: Adoption::class)]
    private ?Collection $adoptionsAsOwner;

    #[ORM\OneToMany(mappedBy: "azyl", targetEntity: Adoption::class)]
    private ?Collection $adoptionsAsAzyl;

   #[ORM\Column(type: 'integer', length: 255, nullable: true)]
    private ?int $azyl = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $personalPhoto = null;

   #[ORM\Column(type: 'string', length: 2048, nullable: true)]
   private ?string $review = null;

   #[ORM\Column(type: 'integer', nullable: true)]
   private ?int $rating = null;

   #[ORM\OneToOne(inversedBy: "users", targetEntity: "Users")]
   private ?Users $reviewer = null;

   #[ORM\OneToMany(mappedBy: "user", targetEntity: "UsersRatings")]
   private ?Collection $userRatings = null;

   #[ORM\OneToMany(mappedBy: "reviewer", targetEntity: "UsersRatings")]
   private ?Collection $reviewerRatings = null;

    #[ORM\OneToMany(mappedBy: 'reviewer', targetEntity: UsersRatings::class)]
    private ?Collection $ratings;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;


    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->verified = false;
        $this->deleted = false;
        $this->baned = false;
        $this->mailverified = false;
        $this->phoneVerified = false;
        $this->adoptionVerification = false;
        $this->legalTerms = false;
        $this->photos = new ArrayCollection();
        $this->phone = null;
        $this->messageAddress = null;
        $this->reviewer = null;
        $this->azyl = null;
        $this->personalPhoto = null;
        $this->review = null;
        $this->rating = null;
        $this->reviewerRatings = new ArrayCollection();
        $this->userRatings = new ArrayCollection();
        $this->description = null;
        $this->city = null;
    }

    public function __toString(): string
    {
        return $this->userName;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }


    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }


    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    public function setBaned($baned): void
    {
        $this->baned = $baned;
    }


    public function setDeleted($deleted): void
    {
        $this->deleted = $deleted;
    }

    public function setVerified($verified): void
    {
        $this->verified = $verified;
    }

    public function getUsers(): ?Collection
    {
        return $this->getUsers();
    }

    public function getNews(): ?Collection
    {
        return $this->news;
    }

    public function getVerified(): bool
    {
        return $this->verified;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function isBaned(): bool
    {
        return $this->baned;
    }

    public function getAdoptions(): ?Collection
    {
        return $this->adoptions;
    }

    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(?string $houseNumber): Users
    {
        $this->houseNumber = $houseNumber;
        return $this;
    }

    public function getOrientationNumber(): ?string
    {
        return $this->orientationNumber;
    }

    public function setOrientationNumber(?string $orientationNumber): Users
    {
        $this->orientationNumber = $orientationNumber;
        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): Users
    {
        $this->street = $street;
        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): Users
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getAdoptionsAsOwner(): ?Collection
    {
        return $this->adoptionsAsOwner;
    }


    public function getAdoptionsAsAzyl(): ?Collection
    {
        return $this->adoptionsAsAzyl;
    }


    public function isMeilVerified(): bool
    {
        return $this->mailverified;
    }

    public function setMeilVerified(bool $meilVerified): void
    {
        $this->mailverified = $meilVerified;
    }

    public function isPhoneVerified(): bool
    {
        return $this->phoneVerified;
    }

    public function setPhoneVerified(bool $phoneVerified): void
    {
        $this->phoneVerified = $phoneVerified;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setCreatedBy(?Users $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function setCity(?int $city): void
    {
        $this->city = $city;

    }

    public function getCity(): ?int
    {
        return $this->city;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedBy(): ?Users
    {
        return $this?->updatedBy;
    }

    public function setUpdatedBy(?Users $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): Users
    {
        $this->role = $role;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): Users
    {
        $this->password = $password;
        return $this;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    public function setMessageAddress($messageAddress): void
    {
        $this->messageAddress = $messageAddress;
    }

    public function getMessageAddress(): ?string
    {
        return $this->messageAddress;
    }

    public function setReceivedMessages(Collection $receivedMessages): void
    {
        $this->receivedMessages = $receivedMessages;
    }

    public function getFirstName(): ?string
    {
        return $this?->firstName;
    }

    public function setFirstName(string $firstName): Users
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $lastName = $this->lastName;
    }

    public function setLastName(string $lastName): Users
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;

    }

    public function isMailverified(): bool
    {
        return $this->mailverified;
    }

    public function setMailverified(bool $mailverified): Users
    {
        $this->mailverified = $mailverified;
        return $this;
    }

    public function isAdoptionVerification(): bool
    {
        return $this->adoptionVerification;
    }

    public function setAdoptionVerification(bool $adoptionVerification): Users
    {
        $this->adoptionVerification = $adoptionVerification;
        return $this;
    }

    public function isLegalTerms(): bool
    {
        return $this->legalTerms;
    }

    public function setLegalTerms(bool $legalTerms): Users
    {
        $this->legalTerms = $legalTerms;
        return $this;
    }


    /**
     * @throws NumberParseException
     */
    public function getPhone(): ?String
    {
      return $this->phone;


    }

    /**
     * @throws NumberParseException
     */
    public function setPhone(?String $phone) : void
    {
        $this->phone = $phone;
    }

    public function getMailVerifyToken(): string
    {
        return $this->mailVerifyToken;
    }

    public function setMailVerifyToken(?string $mailVerifyToken): void
    {
        $this->mailVerifyToken = $mailVerifyToken;
    }

    public function setPhotos(?int $photos): void
    {
        $this->photos = $photos;
    }

    public function toArray()
    {
        return [
            'username' => $this->userName,
            'email' => $this->email,
            'phone' => $this->phone ?? null, //kontrola incializace
            'password' => '11223334445556677',
            'password2' => '11223334445556677'
        ];
    }

    public function setAzyl(?int $azyl):void
    {
        $this->azyl = $azyl;
    }

    public function getAzyl(): ?int
    {
        return $this->azyl;
    }

    public function getReviewer(): ?Users
    {
        return $this->reviewer;
    }

    public function setReviewer(?Users $reviewer): Users
    {
        $this->reviewer = $reviewer;
        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): Users
    {
        $this->rating = $rating;
        return $this;
    }

    public function getReview(): ?string
    {
        return $this->review;
    }

    public function setReview(?string $review): Users
    {
        $this->review = $review;
        return $this;
    }

    public function getPhotos(): ?Collection
    {
        return $this->photos;
    }

    public function getPersonalPhoto(): ?int
    {
        return $this->personalPhoto;
    }

    public function setPersonalPhoto(?int $personalPhoto): self
    {
        $this->personalPhoto = $personalPhoto;
        return $this;
    }

    public function getReviewerRatings(): ?Collection
    {
        return $this->reviewerRatings;
    }

    public function setReviewerRatings(?Collection $reviewerRatings): Users
    {
        $this->reviewerRatings = $reviewerRatings;
        return $this;
    }

    public function getUserRatings(): ?Collection
    {
        return $this->userRatings;
    }

    public function setUserRatings(?Collection $userRatings): Users
    {
        $this->userRatings = $userRatings;
        return $this;
    }

    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function setRatings(Collection $ratings): Users
    {
        $this->ratings = $ratings;
        return $this;
    }

    public function getPages(): ?Collection
    {
        return $this->pages;
    }

    public function setPages(?Collection $pages): Users
    {
        $this->pages = $pages;
        return $this;
    }

    public function getCollections(): ?Collection
    {
        return $this->collections;
    }

    public function setCollections(?Collection $collections): Users
    {
        $this->collections = $collections;
        return $this;
    }

}