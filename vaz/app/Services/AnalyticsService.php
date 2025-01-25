<?php
declare(strict_types=1);

namespace App\Services;

use App\Model\Orm\Entity\Analytics;
use App\Model\Orm\Repository\AnalyticsRepository;
use App\Model\Orm\Repository\UsersRepository;
use Nette\Application\UI\Presenter;
use DateTimeImmutable;

class AnalyticsService
{
    private AnalyticsRepository $analyticsRepository;
    private UsersRepository $usersRepository;
    private ?Presenter $presenter;

    private string $comment;

    public function __construct(AnalyticsRepository $analyticsRepository
    , UsersRepository $usersRepository)
    {
        $this->analyticsRepository = $analyticsRepository;
        $this->usersRepository = $usersRepository;
        $this->presenter = null;
    }

    public function setPresenter(Presenter $presenter): void
    {
        $this->presenter = $presenter;
    }

    public function setComment(string $comment = 'Komentář není nastaven'): void
    {
        $this->comment = $comment;
    }

    public function logVisit(): void
    {
        if (!$this->presenter) {
            throw new \LogicException('Presenter is not set in AnalyticsService.');
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $host = $_SERVER['REMOTE_HOST'] ?? gethostbyaddr($ip);
        $presenterName = $this->presenter->getName();
        $actionName = $this->presenter->getAction();
        $userId = $this->presenter->getUser()->isLoggedIn()
            ? $this->presenter->getUser()->getId()
            : null;

        $session = $this->presenter->getSession();
        //$session->setExpiration('10 minutes');
        $section = $session->getSection('Analytics');
        $section->setExpiration('10 minutes');
        if (!$section->get('tempId')) {
            $tempId = sha1($host . random_int(10, 99886644));
            $section->set('tempId', $tempId);
        }
        else
        {
            $tempId = $section->get('tempId');
        }

        $analytics = New Analytics();
        $analytics->setIpAdress($ip);
        $analytics->setHost($host);

        if($userId !== null)
        {
            $analytics->setUser($this->usersRepository->getUserById($userId));
            $analytics->setTempId('');
        }
        else
        {
            $analytics->setUser(null);
            $analytics->setTempId($tempId);
        }

        $analytics->setAction($actionName);
        $analytics->setName($presenterName);
        $analytics->setDate(new DateTimeImmutable());
        $analytics->setComment($this->comment);
        $analytics->setParams(json_encode($this->presenter->getParameters()));

        $this->analyticsRepository->save($analytics);

    }

    public function getAnalyticsStats(): array
    {
        $totalRecords = $this->analyticsRepository->countAll();
        $uniqueVisitors = $this->analyticsRepository->countUniqueVisitors();
        $byAction = $this->analyticsRepository->countByAction();

        return [
            'totalRecords' => $totalRecords,
            'uniqueVisitors' => $uniqueVisitors,
            'byAction' => $byAction,
        ];
    }

    /*
        //počet návštěv azylu
       $azylId = 1; // ID konkrétního azylu
       $visits = $this->analyticsRepository->countVisitsForAzyl($azylId);

    $azylId = 1; // ID konkrétního azylu
    $limit = 10; // Počet posledních unikátních uživatelů

    $lastVisitors = $this->analyticsRepository->findLastVisitorsForAzyl($azylId, $limit);
    foreach ($lastVisitors as $visitor) {
    echo 'Uživatel: ' . $visitor['username'] . ', Navštíveno: ' . $visitor['createdAt'] . PHP_EOL;
    }

     */
}
