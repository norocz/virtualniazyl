<?php

namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\ContractParts;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class ContractPartsRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $class = ContractParts::class)
    {
        parent::__construct($em, $em->getClassMetadata($class));
    }

    public function save(ContractParts $contractParts): void
    {
        $this->getEntityManager()->persist($contractParts);
        $this->getEntityManager()->flush();
    }

    public function remove(ContractParts $contractParts): void
    {
        $this->getEntityManager()->remove($contractParts);
        $this->getEntityManager()->flush();
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?ContractParts
    {
        return $this->findOneBy($criteria, $orderBy);
    }

    public function fetchAll()
    {
        return $this->fetchAll();
    }


}