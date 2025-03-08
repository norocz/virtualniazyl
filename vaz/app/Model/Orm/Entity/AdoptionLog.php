<?php

namespace App\Model\Orm\Entity;


use App\Model\Orm\Enums\ActionTypeEnum;
use Doctrine\ORM\Mapping as ORM;

#[AllowDynamicProperties] #[ORM\Entity]
#[ORM\Table(name: 'adoption_log')]

class AdoptionLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Adoption::class, inversedBy: 'logs')]
    private Adoption $adoption;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: ActionTypeEnum::ACTION_TYPE_ENUM, length: 255)]
    private ?string $actionType;

    #[ORM\Column(type: 'string', length: 1024)]
    private string $comment;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return AdoptionLog
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getAdoption(): Adoption
    {
        return $this->adoption;
    }

    public function setAdoption(Adoption $adoption): AdoptionLog
    {
        $this->adoption = $adoption;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return AdoptionLog
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getActionType(): ?string
    {
        return $this->actionType;
    }

    public function setActionType(?string $actionType): AdoptionLog
    {
        $this->actionType = $actionType;
        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): AdoptionLog
    {
        $this->comment = $comment;
        return $this;
    }


}