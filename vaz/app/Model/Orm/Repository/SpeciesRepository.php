<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Orm\Entity\Species;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class SpeciesRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $class = Species::class)
    {
        parent::__construct($em, $em->getClassMetadata($class));
    }
    public function findAll(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    public function fetchPairs(): array
    {
        $result = [];
        foreach ($this->findAll() as $species) {
            $result[$species->getId()] = $species->getName();
        }
        return $result;
    }

    public function fetchAll(): array
    {
        return $this->createQueryBuilder('s')
            ->getQuery()
            ->getResult();
    }

    public function findByName(string $name): ?Species
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function findOneById(int $id)
    {
        return parent::findOneBy(['id'=>$id]);
    }

    public function findBySex(string $sex): array
    {
        return $this->findBy(['sex'=>$sex]);

    }

    public function save(Species $species): void
    {
        $this->getEntityManager()->persist($species);
        $this->getEntityManager()->flush();
    }

}