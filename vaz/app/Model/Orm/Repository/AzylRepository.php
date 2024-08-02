<?php
declare(strict_types=1);

namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\Azyl;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class AzylRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $class = Azyl::class)
    {
        parent::__construct($em, $em->getClassMetadata($class));
    }

    public function fetchAll(): array
    {
        return $this->createQueryBuilder('a')
            ->getQuery()
            ->getResult();
    }

    public function fetchLast(): ?array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(15)
            ->getQuery()
            ->getResult();
    }

    public function saveAzyl(Azyl $azyl): void
    {
        $this->getEntityManager()->persist($azyl);
        $this->getEntityManager()->flush();
    }

    public function findByName(string $name): ?Azyl
    {
        return $this->findOneBy(['azylName' => $name]);
    }

    public function findById(int $id): ?Azyl
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function deleteAzyl(Azyl $azyl): void
    {
        $this->getEntityManager()->remove($azyl);
        $this->getEntityManager()->flush();
    }

    public function persist(Azyl $azyl):void
    {
        $this->getEntityManager()->persist($azyl);
    }

    public function getAzyl($id): ?Azyl
    {
    return $this->findOneBy(['id' => $id]);

    }

}