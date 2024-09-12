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

    #[ORM\ManyToOne(targetEntity: "Users", inversedBy: "sentMessages")]
    #[ORM\JoinColumn(name: "sender_id", referencedColumnName: "id")]
    private Users $sender;

    #[ORM\ManyToOne(targetEntity: "Users", inversedBy: "receivedMessages")]
    #[ORM\JoinColumn(name: "receiver_id", referencedColumnName: "id")]
    private Users $receiver;

    #[ORM\Column(type: 'boolean')]
    private bool $readed;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $readedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $deletedAt;

    #[ORM\Column(type: MessageTypeEnum::MESSAGE_TYPE_ENUM, length: 255)]
    private string $type;

    public function getSender(): Users
    {
        return $this->sender;
    }

    public function getReceiver(): Users
    {
        return $this->receiver;
    }

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

    public function getTitle(): string
    {
        return isset($this->title) ? $this->title : "";

    }
    public function setSender(Users $sender): void
    {
        $this->sender = $sender;
    }
    public function setReceiver(Users $receiver): void
    {
        $this->receiver = $receiver;
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
}