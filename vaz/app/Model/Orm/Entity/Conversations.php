<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Random\RandomException;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: "conversations")]

class Conversations
{

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $id;

    /**
     * @throws RandomException
     */
    #[ORM\PrePersist]
    public function generateId(): void
    {
        if (!$this->id) { // Zajistíme, že id se nastaví pouze pokud není nastavené
            $this->id = hash('sha256', uniqid(random_bytes(32),true));
        }
    }

    #[ORM\Column(type: 'string', length: 256)]
    private ?string $comment;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $lastMessage;

    #[ORM\Column(type: 'boolean')]
    private bool $block;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'conversations')]
    private ?Users $user;

    #[ORM\ManyToOne(targetEntity: Azyl::class, inversedBy: 'conversations')]
    private ?Azyl $azyl;

    #[ORM\ManyToOne(targetEntity: Adoption::class, inversedBy: 'conversations')]
    private ?Adoption $adoption;


    /**
     * @throws RandomException
     */
    public function __construct()
    {
        $this->id = hash('sha256', uniqid(random_bytes(32),true));
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): Conversations
    {
        $this->user = $user;
        return $this;
    }

    public function getAzyl(): ?Azyl
    {
        return $this->azyl;
    }

    public function setAzyl(?Azyl $azyl): Conversations
    {
        $this->azyl = $azyl;
        return $this;
    }

    public function getAdoption(): ?Adoption
    {
        return $this->adoption;
    }

    public function setAdoption(?Adoption $adoption): Conversations
    {
        $this->adoption = $adoption;
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): Conversations
    {
        $this->comment = $comment;
        return $this;
    }

    public function getLastMessage(): ?DateTimeImmutable
    {
        return $this->lastMessage;
    }

    public function setLastMessage(?DateTimeImmutable $lastMessage): Conversations
    {
        $this->lastMessage = $lastMessage;
        return $this;
    }

    public function isBlock(): bool
    {
        return $this->block;
    }

    public function setBlock(bool $block): Conversations
    {
        $this->block = $block;
        return $this;
    }


}