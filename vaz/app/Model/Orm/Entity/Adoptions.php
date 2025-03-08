<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;

use AllowDynamicProperties;
use App\Model\Orm\Enums\ActionTypeEnum;
use App\Model\Orm\Enums\AdoptionsTypeEnum;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use DateTimeImmutable;

#[AllowDynamicProperties] #[ORM\Entity]
#[ORM\Table(name: 'adoptions')]

class Adoption
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $adoptionKey;

    #[ORM\Column(type: 'string', length: 2048)]
    private string $description;

    #[ORM\Column(type: 'string')]
    private ?string $setings;

    #[ORM\ManyToOne(targetEntity: Animal::class, inversedBy: 'adoptions')]
    #[ORM\JoinColumn(name: "animal_id", referencedColumnName: "id")]
    private Animal $animal;
   
    #[ORM\ManyToOne(targetEntity: Azyl::class, inversedBy: "adoptions")]
    private Azyl $azyl;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "adoptions")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private Users $user;

    #[ORM\OneToMany(mappedBy: 'adoption', targetEntity: Conversations::class)]
    private ?Collection $conversations;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'boolean')]
    private bool $deleted;

    #[ORM\Column(type: 'boolean')]
    private bool $confirmed;

    #[ORM\Column(type: 'boolean')]
    private bool $canceled;

    #[ORM\Column(type: AdoptionsTypeEnum::ADOPTION_TYPE_ENUM, length: 255)]
    private string $adoptionType;

    #[ORM\Column(type: ActionTypeEnum::ACTION_TYPE_ENUM, length: 255)]
    private ?string $actionType;

    #[ORM\OneToMany(mappedBy: 'adoption', targetEntity: Messages::class)]
    private ?Collection $messages;

    #[ORM\Column(type: 'integer', length: 255)]
    private int $howMuch;

    #[ORM\OneToMany(mappedBy: 'adoption', targetEntity: AdoptionLog::class)]
    private ?Collection $logs;

    public function setAdoptionKey(string $adoptionKey): Adoption
    {
        $this->adoptionKey = $adoptionKey;
        return $this;
    }

    public function getAdoptionKey(): string
    {
        return $this->adoptionKey;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Adoption
    {
        $this->description = $description;
        return $this;
    }

    public function getSetings(): ?string
    {
        return $this->setings;
    }

    public function setSetings(?string $setings): Adoption
    {
        $this->setings = $setings;
        return $this;
    }

    public function getUser(): Users
    {
        return $this->user;
    }

    public function setUser(Users $user): Adoption
    {
        $this->user = $user;
        return $this;
    }

    public function getAdoptionType(): string
    {
        return $this->adoptionType;
    }

    public function setAdoptionType(string $adoptionType): Adoption
    {
        $this->adoptionType = $adoptionType;
        return $this;
    }

    public function getActionType(): ?string
    {
        return $this->actionType;
    }

    public function setActionType(?string $actionType): Adoption
    {
        $this->actionType = $actionType;
        return $this;
    }

    public function getOwner(): Users
    {
        return $this->owner;
    }

    public function setOwner(Users $owner): Adoption
    {
        $this->owner = $owner;
        return $this;
    }

    public function getAzyl(): Azyl
    {
        return $this->azyl;
    }

    public function setAzyl(Azyl $azyl): Adoption
    {
        $this->azyl = $azyl;
        return $this;
    }

    public function getAnimal(): Animal
    {
        return $this->animal;
    }

    public function setAnimal(Animal $animal): Adoption
    {
        $this->animal = $animal;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): Adoption
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): Adoption
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): Adoption
    {
        $this->deleted = $deleted;
        return $this;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): Adoption
    {
        $this->confirmed = $confirmed;
        return $this;
    }

    public function isCanceled(): bool
    {
        return $this->canceled;
    }

    public function setCanceled(bool $canceled): Adoption
    {
        $this->canceled = $canceled;
        return $this;
    }

    public function getAdoptionActions(): AdoptionAction
    {
        return $this->adoptionActions;
    }

    public function setAdoptionActions(AdoptionAction $adoptionActions): Adoption
    {
        $this->adoptionActions = $adoptionActions;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMessages(): ?Collection
    {
        return $this->messages;
    }

    public function setMessages(?Collection $messages): Adoption
    {
        $this->messages = $messages;
        return $this;
    }

    public function getHowMuch(): int
    {
        return $this->howMuch;
    }

    public function setHowMuch(int $howMuch): Adoption
    {
        $this->howMuch = $howMuch;
        return $this;
    }

    public function getConversations(): ?Collection
    {
        return $this->conversations;
    }

    public function getLogs(): ?Collection
    {
        return $this->logs;
    }

    public function setLogs(?Collection $logs): Adoption
    {
        $this->logs = $logs;
        return $this;
    }



}