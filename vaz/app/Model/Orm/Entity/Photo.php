<?php
// src/Entity/Photo.php

namespace App\Model\Orm\Entity;

use App\Model\Orm\Repository\PhotosRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Nette\Http\FileUpload;
use Nette\IOException;
use Nette\Utils\Random;

#[ORM\Entity(repositoryClass: PhotosRepository::class)]
#[ORM\Table(name: 'photos')]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $date;

    #[ORM\Column(type: 'string', length: 512)]
    private string $path;

    #[ORM\Column(type: 'string', length: 512)]
    private string $name;

    #[ORM\Column(type: 'string', length: 512)]
    private string $originalName;

    #[ORM\Column(type: 'boolean')]
    private bool $deleted = false;

    #[ORM\ManyToOne(targetEntity: "Animal", inversedBy: "photos")]
    #[ORM\JoinColumn(name: "animal_id", referencedColumnName: "id", nullable: true)]
    private ?Animal $animal;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "photos")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private ?Users $user = null;

    #[ORM\ManyToOne(targetEntity: "Owner", inversedBy: "photos")]
    #[ORM\JoinColumn(name: "owner_id", referencedColumnName: "id",nullable: true)]
    private ?Owner $owner;

    #[ORM\ManyToOne (targetEntity: "UsersRatings", inversedBy: "photos")]
    private UsersRatings $userRatings;

    #[ORM\ManyToOne(targetEntity: "Azyl", inversedBy: "photos")]
    #[ORM\JoinColumn(name: "azyl_id", referencedColumnName: "id", nullable: true)]
    private ?Azyl $azyl;



    const REAL_UPLOAD_PATH = '../../upload/photos/';
    const WWW_UPLOAD_PATH = '/upload/photos/';
    const UPLOAD_PATH = '/../../../../www' . self::WWW_UPLOAD_PATH;

    /**
     * @param Azyl $azyl
     */
    public function __construct()
    {
        $this->azyl = null;
    }


    public function uploadAzylPhoto(FileUpload $fileUpload) : void
    {


        if (!$fileUpload->isOk()) {
            throw new IOException('File is not OK!');
        }
        $this->originalName = $fileUpload->getSanitizedName();
        $array = explode('.', $this->originalName);
        $extension = array_pop($array);
        $this->name = Random::generate(39) . "." . $extension;

        $pathPart = self::REAL_UPLOAD_PATH."azyl/" .$this->getAzyl()->id.'/';
        $path = __DIR__ . self::UPLOAD_PATH . "azyl/" .$this->getAzyl()->id.'/';

        bdump($path);
        $this->setPath($pathPart);

        if (!file_exists($path)) {
            if (!mkdir($path, 0755, true)) {
                throw new IOException('Path creating error!');
            }
        }

        $fileUpload->move($path . $this->name);
    }

    public function uploadUserPhoto(FileUpload $fileUpload)
    {


        if (!$fileUpload->isOk()) {
            throw new IOException('File is not OK!');
        }
        $this->originalName = $fileUpload->getSanitizedName();
        $array = explode('.', $this->originalName);
        $extension = array_pop($array);
        $this->name = Random::generate(39) . "." . $extension;
        $pathPart = self::REAL_UPLOAD_PATH."user/" .$this->getUser()->getId().'/';
        $path = __DIR__ . self::UPLOAD_PATH . "user/" .$this->getUser()->getId().'/';
        $this->setPath($pathPart);

        if (!file_exists($path)) {
            if (!mkdir($path, 0755, true)) {
                throw new IOException('Path creating error!');
            }
        }

        $fileUpload->move($path . $this->name);
    }

    public function uploadOwnerPhoto(FileUpload $fileUpload)
    {


        if (!$fileUpload->isOk()) {
            throw new IOException('File is not OK!');
        }
        $this->originalName = $fileUpload->getSanitizedName();
        $array = explode('.', $this->originalName);
        $extension = array_pop($array);
        $this->name = Random::generate(39) . "." . $extension;
        $pathPart = self::REAL_UPLOAD_PATH."owner/" .$this->getOwner()->id.'/';
        $path = __DIR__ . self::UPLOAD_PATH . "owner/" .$this->getOwner()->id.'/';
        $this->setPath($pathPart);

        if (!file_exists($path)) {
            if (!mkdir($path, 0755, true)) {
                throw new IOException('Path creating error!');
            }
        }

        $fileUpload->move($path . $this->name);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Photo
    {
        $this->id = $id;
        return $this;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): Photo
    {
        $this->date = $date;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): Photo
    {
        $this->path = $path;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Photo
    {
        $this->name = $name;
        return $this;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): void
    {
        $this->originalName = $originalName;

    }

    public function getAnimal(): Animal
    {
        return $this->animal;
    }

    public function setAnimal(Animal $animal): void
    {
        $this->animal = $animal;

    }

    public function getUser(): Users
    {
        return $this->user;
    }

    public function setUser(Users $user): Users
    {
        return $this->user = $user;
    }

    public function getOwner(): Owner
    {
        return $this->owner;
    }

    public function setOwner(Owner $owner): Owner
    {
        return $this->owner = $owner;
    }

    public function getAzyl(): ?Azyl
    {
        return $this->azyl;
    }

    public function setAzyl(Azyl $azyl): void
    {
        $this->azyl = $azyl;

    }

    public function toArray(): array
    {
        return ['id' => $this->id
        , 'date' => $this->date
        , 'path' => $this->path
        , 'name' => $this->name
        , 'originalName' => $this->originalName];
    }
}
