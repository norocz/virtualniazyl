<?php
declare(strict_types=1);

namespace App\Model\Orm\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use App\Model\Orm\Entity\Analytics;

class AnalyticsRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, Analytics $analytics, $class = Analytics::class)
    {
        parent::__construct($em, $em->getClassMetadata($class));

    }

    public function fetchAll(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();

    }

    public function findByDate(string $date): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.date = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

    public function findByIp(string $ip): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.ip = :ip')
            ->setParameter('ip', $ip)
            ->getQuery()
            ->getResult();
    }

    public function getAllVisits(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(Analytics $data): void
    {
        $this->getEntityManager()->persist($data);
        $this->getEntityManager()->flush();
    }

    public function countAll(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countUniqueVisitors(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(DISTINCT a.tempId)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByAction(): array
    {
        return $this->createQueryBuilder('a')
            ->select('a.action as action, COUNT(a.id) as count')
            ->groupBy('a.action')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countVisitsForAzyl(int $azylId): int
    {
        // Získej všechny záznamy pro akci 'azyl'
        $results = $this->createQueryBuilder('a') // alias 'a' je pro entitu
        ->where('a.action = :action')
            ->setParameter('action', 'azyl')
            ->getQuery()
            ->getResult();

        $count = 0;

        // Projdi všechny výsledky
        foreach ($results as $result) {
            // Předpokládám, že máš metodu getParams() pro získání JSON dat
            $params = json_decode($result->getParams(), true);

            // Zkontroluj, jestli je v parametrech správné 'id'
            if (isset($params['id']) && $params['id'] == $azylId) {
                $count++;
            }
        }

        return $count;
    }

    public function getVisitorsForAzyl(int $azylId, int $limit = 20): array
    {
        // Získej všechny záznamy pro akci 'azyl'
        $results = $this->createQueryBuilder('a')
            ->where('a.action = :action')
            ->setParameter('action', 'azyl')
            ->orderBy('a.date', 'DESC') // Seřaď podle data (pokud existuje sloupec createdAt)
            ->getQuery()
            ->getResult();

        $visitors = [];
        $uniqueTempIds = [];

        foreach ($results as $result) {
            $params = json_decode($result->getParams(), true);

            // Zkontroluj, zda se vztahuje k danému azylu
            if (isset($params['id']) && $params['id'] == $azylId) {
                $tempId = $result->getTempId(); // Předpokládám, že máš sloupec tempId
                $user = $result->getUser(); // Předpokládám, že máš vazbu na User entitu

                if ($user && !in_array($user->getId(), $uniqueTempIds, true)) {
                    // Registrovaný uživatel
                    $uniqueTempIds[] = $user->getId();
                    $visitors[] = [
                        'type' => 'registered',
                        'id' => $user->getId(),
                        'userName' => $user->getUserName(), // nebo jiný atribut
                        'email' => $user->getEmail(),
                    ];
                } elseif (!$user && !in_array($tempId, $uniqueTempIds, true)) {
                    // Neregistrovaný uživatel
                    $uniqueTempIds[] = $tempId;
                    $visitors[] = [
                        'type' => 'guest',
                        'id' => '',
                        'userName' => 'host-'.substr($tempId, 20),
                        'email' => ''
                    ];
                }

                // Omezení počtu návštěvníků
                if (count($visitors) >= $limit) {
                    break;
                }
            }
        }

        return $visitors;
    }

    public function countVisitsByIpPerDay(string $ip): array
    {
        return $this->createQueryBuilder('a')
            ->select("DATE_FORMAT(a.date, '%Y-%m-%d') as day, COUNT(a.id) as count")
            ->where('a.ipAdress = :ip')
            ->setParameter('ip', $ip)
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countVisitsByIpPerWeek(string $ip): array
    {
        return $this->createQueryBuilder('a')
            ->select("DATE_FORMAT(a.date, '%Y-%u') as week, COUNT(a.id) as count")
            ->where('a.ipAdress = :ip')
            ->setParameter('ip', $ip)
            ->groupBy('week')
            ->orderBy('week', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countVisitsByIpPerMonth(string $ip): array
    {
        return $this->createQueryBuilder('a')
            ->select("DATE_FORMAT(a.date, '%Y-%m') as month, COUNT(a.id) as count")
            ->where('a.ipAdress = :ip')
            ->setParameter('ip', $ip)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countVisitsByHostPerDay(string $host): array
    {
        return $this->createQueryBuilder('a')
            ->select("DATE_FORMAT(a.date, '%Y-%m-%d') as day, COUNT(a.id) as count")
            ->where('a.host = :host')
            ->setParameter('host', $host)
            ->groupBy('day')
            ->orderBy('day', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countVisitsByHostPerWeek(string $host): array
    {
        return $this->createQueryBuilder('a')
            ->select("DATE_FORMAT(a.date, '%Y-%u') as week, COUNT(a.id) as count")
            ->where('a.host = :host')
            ->setParameter('host', $host)
            ->groupBy('week')
            ->orderBy('week', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countVisitsByHostPerMonth(string $host): array
    {
        return $this->createQueryBuilder('a')
            ->select("DATE_FORMAT(a.date, '%Y-%m') as month, COUNT(a.id) as count")
            ->where('a.host = :host')
            ->setParameter('host', $host)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function countUniqueVisitsPerMonth(int $months = 6): array
    {
        $dateThreshold = (new \DateTime())->modify("-$months months")->format('Y-m-d');

        $sql = "
        SELECT DATE_FORMAT(a.date, '%Y-%m') as month, COUNT(DISTINCT a.temp_id) as count
        FROM analytics a
        WHERE a.date >= :dateThreshold
        GROUP BY month
        ORDER BY month ASC
    ";

        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($sql);
        $statement->executeStatement(['dateThreshold' => $dateThreshold]);

        return $statement->executeQuery()->fetchAllAssociative();
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function countUniqueVisitsPerWeek(int $weeks = 12): array
    {
        $dateThreshold = (new \DateTime())->modify("-$weeks weeks")->format('Y-m-d');

        $sql = "
        SELECT DATE_FORMAT(a.date, '%Y-%u') as week, COUNT(DISTINCT a.temp_id) as count
        FROM analytics a
        WHERE a.date >= :dateThreshold
        GROUP BY week
        ORDER BY week ASC
    ";

        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($sql);
        $statement->executeStatement(['dateThreshold' => $dateThreshold]);

        return $statement->executeQuery()->fetchAllAssociative();
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function countUniqueVisitsPerDay(int $days = 30): array
    {
     $dateThreshold = (new \DateTime())->modify("-$days days")->format('Y-m-d');

        $sql = "
        SELECT DATE_FORMAT(a.date, '%Y-%m-%d') as day, COUNT(DISTINCT a.temp_id) as count
        FROM analytics a
        WHERE a.date >= :dateThreshold
        GROUP BY day
        ORDER BY day ASC
    ";

        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($sql);
        $statement->executeStatement(['dateThreshold' => $dateThreshold]);

        return $statement->executeQuery()->fetchAllAssociative();
    }

    public function countVisitsPerDay(int $days = 30): array
    {
        $dateThreshold = (new \DateTime())->modify("-$days days")->format('Y-m-d');

        $sql = "
        SELECT DATE_FORMAT(a.date, '%Y-%m-%d') as day, COUNT(a.id) as count
        FROM analytics a
        WHERE a.date >= :dateThreshold
        GROUP BY day
        ORDER BY day ASC
    ";

        $connection = $this->getEntityManager()->getConnection();
        $statement = $connection->prepare($sql);
        $statement->executeStatement(['dateThreshold' => $dateThreshold]);

        return $statement->executeQuery()->fetchAllAssociative();
    }


}