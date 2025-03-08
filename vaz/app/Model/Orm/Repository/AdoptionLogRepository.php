<?php
declare(strict_types=1);

namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\Adoption;
use App\Model\Orm\Entity\AdoptionLog;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class AdoptionLogRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $class = AdoptionLog::class)
    {
        parent::__construct($em, $em->getClassMetadata($class));
    }

    public function findByAdoption(Adoption $adoption): ?array
    {
        return $this->findBy(['adoption' => $adoption]);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) : ?array
    {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, array $orderBy = null): ?AdoptionLog
    {
        return $this->findOneBy($criteria, $orderBy);
    }

    public function save(AdoptionLog $adoptionLog): void
    {
        $this->getEntityManager()->persist($adoptionLog);
        $this->getEntityManager()->flush();
    }
    public function delete(AdoptionLog $adoptionLog): void
    {
        $this->getEntityManager()->remove($adoptionLog);
        $this->getEntityManager()->flush();

    }
}