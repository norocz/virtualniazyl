<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;

use App\Model\Orm\Enums\MessageTypeEnum;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'messages')]
class Messages
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $title;

    #[ORM\Column(type: 'string', length: 4096)]
    private string $message;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "messages")] //odesilatel
    #[ORM\JoinColumn(name: "user", referencedColumnName: "id")]
    private ?Users $user;

    #[ORM\ManyToOne(targetEntity: Azyl::class, inversedBy: "messages")]
    #[ORM\JoinColumn(name: "azyl", referencedColumnName: "id")]
    private ?Azyl $azyl;

    #[ORM\ManyToOne(targetEntity: Conversations::class, cascade: ["persist", "remove"], inversedBy: "messages")]
    private Conversations $conversation;


    #[ORM\Column(type: 'boolean')]
    private bool $readed;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $readedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $deletedAt;

    #[ORM\Column(type: MessageTypeEnum::MESSAGE_TYPE_ENUM, length: 255)]
    private string $type;

    #[ORM\ManyToOne(targetEntity: "Adoption", inversedBy: "Messages")]
    private ?Adoption $adoption;

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function getReadedAt(): ?DateTimeImmutable
    {
        return $this->readedAt;
    }

    public function getReaded(): bool
    {
        return $this->readed;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return isset($this->title) ? $this->title : "";

    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setDeletedAt(DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @param bool $readed
     */
    public function setReaded(bool $readed): void
    {
        $this->readed = is_null($readed) ? false : $readed;
    }

    public function setReadedAt(DateTimeImmutable $readedAt): void
    {
        $this->readedAt = $readedAt;
    }

    public function getAdoption(): ?Adoption
    {
        return $this->adoption;
    }

    public function setAdoption(?Adoption $adoption): Messages
    {
        $this->adoption = $adoption;
        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): Messages
    {
        $this->user = $user;
        return $this;
    }

    public function getAzyl(): ?Azyl
    {
        return $this->azyl;
    }

    public function setAzyl(?Azyl $azyl): Messages
    {
        $this->azyl = $azyl;
        return $this;
    }

    public function getConversation(): Conversations
    {
        return $this->conversation;
    }

    public function setConversation(Conversations $conversation): Messages
    {
        $this->conversation = $conversation;
        return $this;
    }
}