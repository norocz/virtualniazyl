<?php
declare(strict_types=1);

namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\Collections;
use App\Model\Orm\Entity\Payments;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class PaymentsRepository extends EntityRepository
{

    public function __construct(EntityManagerInterface $em, string $class = Payments::class)
    {
        parent::__construct($em, $em->getClassMetadata($class));

    }

    private function getTotalPayResult($query): int
    {
        return (int) ($query->getSingleScalarResult() ?? 0);
    }


    public function findOneByCollectionKey(int $collectionKey): Payments
    {
        return $this->findOneBy(['variableSymbol' => $collectionKey]);

    }


    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getTotalPayByCollectionKey(int $collectionKey): ?int
    {
        $query = $this->createQueryBuilder('p')
            ->select('SUM(p.pay)')
            ->where('p.variableSymbol = :variableSymbol')
            ->setParameter('variableSymbol', $collectionKey)
            ->getQuery();
            return $this->getTotalPayResult($query);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getTotalPayByCollection(int $collectionId): ?int
    {
        $query = $this->createQueryBuilder('p')
            ->select('SUM(p.pay)')
            ->where('p.collections = :collectionId')
            ->setParameter('collectionId', $collectionId)
            ->getQuery();
             return $this->getTotalPayResult($query);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getTotalPayByCollectionKeyAndDate(int $collectionKey, \DateTimeImmutable $date): ?int
    {
        $query = $this->createQueryBuilder('p')
            ->select('SUM(p.pay)')
            ->where('p.variableSymbol = :variableSymbol')
            ->andWhere('p.payedAt BETWEEN :startOfDay AND :endOfDay')
            ->setParameter('variableSymbol', $collectionKey)
            ->setParameter('startOfDay', $date->setTime(0, 0, 0))
            ->setParameter('endOfDay', $date->setTime(23, 59, 59))
            ->getQuery();
             return $this->getTotalPayResult($query);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getTotalPayByAzyl(int $azylId): ?int
    {
        $query = $this->createQueryBuilder('p')
            ->select('SUM(p.pay)')
            ->where('p.azyl = :azylId')
            ->setParameter('azylId', $azylId)
            ->getQuery();
             return $this->getTotalPayResult($query);
    }

    public function save(Payments $payments): void
    {
        $this->getEntityManager()->persist($payments);
        $this->getEntityManager()->flush();

    }

    public function delete(Payments $payments): void
    {
        $this->getEntityManager()->remove($payments);
    }

    /*
     * $total = $paymentsRepository->getTotalPayByVariableSymbol(123456);
$totalForDay = $paymentsRepository->getTotalPayByVariableSymbolAndDate(123456, new \DateTimeImmutable('today'));
$totalForCollection = $paymentsRepository->getTotalPayByCollection(1);
$totalForAzyl = $paymentsRepository->getTotalPayByAzyl(5);
     */

}