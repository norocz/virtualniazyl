<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;

use App\Model\Orm\Enums\RoleTypeEnum;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[ORM\MappedSuperclass]

class Users
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public int $id;

    #[ORM\Column(type: 'string', length: 255)]
    public string $userName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 255)]
    public string $email;

    #[ORM\Column(type:RoleTypeEnum::ROLE_TYPE_ENUM, length: 255)]
    private string $role;

    #[ORM\Column(type: 'string', length: 512)]
    private string $password;

    #[ORM\Column(type: 'string', length: 2048, nullable: true)]
    private string $phone;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $mailVerifyToken;

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

    #[ORM\OneToMany(mappedBy: "user", targetEntity: "Photo")]
    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $photos;

    #[ORM\OneToMany(mappedBy: "sender", targetEntity: "Messages")]
    public Collection $sentMessages;

    #[ORM\OneToMany(mappedBy: "receiver", targetEntity: "Messages")]
    private Collection $receivedMessages;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $deleted;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $baned;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $mailverified;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $phoneVerified;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: News::class)]
    public ?Collection $news;

    #[ORM\OneToMany(mappedBy: "author", targetEntity: "Pages")]
    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $pages;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $adoptionVerification;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $legalTerms;

    #[ORM\Column(type: 'integer', length: 255, nullable: true)]
    #[ORM\ManyToOne(targetEntity: "Citys", inversedBy: "cityCode")]
    #[ORM\JoinColumn(name: "city_code", referencedColumnName: "cityCode")]
    private ?int $cityCode;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private ?string $houseNumber;
    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private ?string $orientationNumber;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $street;

    #[ORM\Column(type: 'string', length: 6, nullable: true)]
    private ?string $zipCode;

    #[ORM\OneToMany(targetEntity: "Adoption", mappedBy: "owner")]
    private Collection $adoptionsAsOwner;

    #[ORM\OneToMany(targetEntity: "Adoption", mappedBy: "azyl")]
    private Collection $adoptionsAsAzyl;

    #[ORM\OneToMany(targetEntity: "AdoptionAction", mappedBy: "actinonsAsOwner")]
    private Collection $actionsAsOwner;

    #[ORM\OneToMany(targetEntity: "AdoptionAction", mappedBy: "actionsAsAzyl")]
    private Collection $actionsAsAzyl;

   #[ORM\Column(type: 'integer', length: 255, nullable: true)]
    private ?int $azyl = null;

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
        $this->photos = null;
    }

    public function __toString(): string
    {
        return $this->userName;
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

    public function getUsers(): Collection
    {
        return $this->getUsers();
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

    public function getPhone(): PhoneNumber
    {
        $phone = new PhoneNumber($this->phone);
        $phone->setRawInput($this->phone);

        return $phone;
    }
    public function setPhone($phone) : void
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
    public function getAdoptionsAsAzyl(): Collection
    {
        return $this->adoptionsAsAzyl;
    }

    public function getAdoptionsAsOwner(): Collection
    {
        return $this->adoptionsAsOwner;
    }

    public function setAdoptionsAsAzyl(Collection $adoptionsAsAzyl): Users
    {
        $this->adoptionsAsAzyl = $adoptionsAsAzyl;
        return $this;
    }

    public function setAdoptionsAsOwner(Collection $adoptionsAsOwner): Users
    {
        $this->adoptionsAsOwner = $adoptionsAsOwner;
        return $this;
    }


    public function getActionsAsAzyl(): Collection
    {
        return $this->actionsAsAzyl;
    }

    public function getActionsAsOwner(): Collection
    {
        return $this->actionsAsOwner;
    }


    public function toArray()
    {
        return [
            'username' => $this->userName,
            'email' => $this->email,
            'phone' => $this->phone,
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

}