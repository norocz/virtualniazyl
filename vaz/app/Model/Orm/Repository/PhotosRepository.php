<?php
declare(strict_types=1);

namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\Photo;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class PhotosRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $class = Photo::class)
    {
        parent::__construct($em, $em->getClassMetadata($class));
    }

    public function fetchAll(): array
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult();
    }

    public function addPhoto(Photo $photo): void
    {
        $this->getEntityManager()->persist($photo);
        $this->getEntityManager()->flush();
    }

    public function findOneBy(array $criteria, ?array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    public function remove(Photo $photo): void
    {
        $this->getEntityManager()->remove($photo);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function delete(Photo $photo): void
    {
        $this->getEntityManager()->setDeleted($photo);
    }

    public function save(Photo $photoUpload): void
    {
        $this->getEntityManager()->persist($photoUpload);
        $this->getEntityManager()->flush();
    }

}