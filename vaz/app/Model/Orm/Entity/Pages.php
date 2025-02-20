<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
#[ORM\Table(name: 'pages')]

class Pages
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $link;
    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text', length: 512000)]
    private string $content;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $visibleFrom;

    #[ORM\ManyToOne(targetEntity: "Users", cascade: ["persist"], inversedBy: "pages")]
    #[ORM\JoinColumn(name: "author_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $author;

    #[ORM\Column(type: 'boolean')]
    private $deleted;

    #[ORM\Column(type: 'boolean')]
    private $global;
    #[ORM\Column(type: 'boolean')]
    private $important;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->visibleFrom = new DateTimeImmutable();
        $this->content = '';
        $this->title = '';
        $this->link = '';
        $this->deleted = false;
        $this->global = false;
        $this->important = false;



    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'visibleFrom' => $this->visibleFrom,
            'author' => $this->author,
            'deleted' => $this->deleted,
            'global' => $this->global,
            'important' => $this->important,
            'link' => $this->link,
        ];
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getTitle(): ?string
    {
        return $this->title ;
    }

    public function setTitle(string $title): Pages
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Pages
    {
        $this->content = $content;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): Pages
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): Pages
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getVisibleFrom(): DateTimeImmutable
    {
        return $this->visibleFrom;
    }

    public function setVisibleFrom(DateTimeImmutable $visibleFrom): Pages
    {
        $this->visibleFrom = $visibleFrom;
        return $this;
    }

    public function getAuthor(): Users
    {
        return $this->author;
    }

    public function setAuthor(Users $author): Pages
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     * @return Pages
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGlobal()
    {
        return $this->global;
    }

    /**
     * @param mixed $global
     * @return Pages
     */
    public function setGlobal($global)
    {
        $this->global = $global;
        return $this;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): Pages
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImportant()
    {
        return $this->important;
    }

    /**
     * @param mixed $important
     * @return Pages
     */
    public function setImportant($important)
    {
        $this->important = $important;
        return $this;
    }

    public function setCreated(DateTimeImmutable $param)
    {
        $this->createdAt = $param;
        return $this;
    }
}