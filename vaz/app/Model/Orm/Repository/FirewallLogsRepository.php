<?php
declare(strict_types=1);
namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\FirewallLog;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class FirewallLogsRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $class = FirewallLog::class)
    {
        parent::__construct($em, $em->getClassMetadata($class));
    }

    /**
     * Vrátí všechny záznamy v tabulce firewall_logs.
     */
    public function findAll(): array
    {
        return $this->findBy([], ['createdAt' => 'DESC']);
    }

    /**
     * Vrátí záznamy pro konkrétní IP adresu.
     */
    public function findByIp(string $ip): array
    {
        return $this->findBy(['ip' => $ip], ['createdAt' => 'DESC']);
    }

    public function findOneByIp(string $ip): ?FirewallLog
    {
        return $this->findOneBy(['ip' => $ip]);
    }

    /**
     * Vrátí poslední záznam pro konkrétní IP adresu.
     */
    public function findLastByIp(string $ip): ?FirewallLog
    {
        return $this->findOneBy(['ip' => $ip], ['createdAt' => 'DESC']);
    }

    /**
     * Uloží záznam do tabulky firewall_logs.
     */
    public function save(FirewallLog $firewallLog): void
    {
        $this->getEntityManager()->persist($firewallLog);
        $this->getEntityManager()->flush();
    }

    /**
     * Smaže záznam z tabulky firewall_logs.
     */
    public function delete(FirewallLog $firewallLog): void
    {
        $this->getEntityManager()->remove($firewallLog);
        $this->getEntityManager()->flush();
    }

    /**
     * Vrátí počet záznamů pro konkrétní IP adresu.
     */
    public function countByIp(string $ip): int
    {
        return $this->count(['ip' => $ip]);
    }

    /**
     * Vrátí záznamy, které mají určitou akci (např. 'blocked').
     */
    public function findByAction(string $action): array
    {
        return $this->findBy(['action' => $action], ['createdAt' => 'DESC']);
    }

    /**
     * Vrátí záznamy, které byly vytvořeny po určitém datu.
     */
    public function findCreatedAfter(\DateTimeImmutable $date): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.createdAt > :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

    /**
     * Vrátí záznamy, které byly vytvořeny před určitým datu.
     */
    public function findCreatedBefore(\DateTimeImmutable $date): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.createdAt < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

    /**
     * Vrátí záznamy, které mají určitý počet pokusů.
     */
    public function findByAttempts(int $attempts): array
    {
        return $this->findBy(['attempts' => $attempts], ['createdAt' => 'DESC']);
    }

    /**
     * Vrátí záznamy, které mají více než určitý počet pokusů.
     */
    public function findByAttemptsGreaterThan(int $attempts): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.attempts > :attempts')
            ->setParameter('attempts', $attempts)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): ?FirewallLog
    {
        return $this->createQueryBuilder('f')
        ->where('f.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();

    }
}