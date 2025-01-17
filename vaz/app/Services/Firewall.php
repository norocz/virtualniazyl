<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Model\Orm\Entity\FirewallLog;
use App\Model\Orm\Repository\FirewallLogsRepository;
use JetBrains\PhpStorm\NoReturn;
use Nette\Application\UI\Presenter;
use Nette\Http\Request;
use Nette\Http\Session;
use DateTimeImmutable;

class Firewall
{
    private $request;
    private Session $session;
    private FirewallLogsRepository $firewallLogsRepository;

    private Presenter $presenter;
    private $ufwPath;

    public function __construct(
        Request $request,
        Session $session,
        FirewallLogsRepository $firewallLogsRepository
    ) {
        $this->request = $request;
        $this->session = $session;
        $this->firewallLogsRepository = $firewallLogsRepository;
    }

    public function setPresenter(Presenter $presenter): void
    {
        $this->presenter = $presenter;
    }


    /**
     * Zaznamená neúspěšný pokus o přihlášení.
     */
    public function logFailedLogin(): void
    {
        $ipAddress = $this->request->getRemoteAddress();
        $sessionSection = $this->session->getSection('firewall');
        $attempts = $sessionSection->get('attempts');
        $sessionSection->set('attempts', ++$attempts);

        // Pokud uživatel překročil limit (3 pokusy), dočasně ho zablokujte
        if ($attempts === 3) {
            $sessionSection->set('blocked_until', new DateTimeImmutable('+30 seconds'));
            $this->addFirewallLog($ipAddress, $attempts, 'blocked', '3 neúspěšné pokusy o přihlášení');
            $this->presenter->flashMessage('Bacha! 3x špatné přihlášení. Počkejte 30 sekund.', 'warning');
        }

        // Pokud uživatel překročil vyšší limit (5 pokusů), zablokujte ho trvale
        if ($attempts >= 5) {
            $this->addFirewallLog($ipAddress, $attempts, 'blocked', '5 neúspěšných pokusů o přihlášení');
            $this->redirectToHomeWithMessage('Přístup zablokován. Kontaktujte admin@vaz.cz.');
        }
    }

    /**
     * Přidá záznam do firewall_logs.
     */
    private function addFirewallLog(string $ip, int $attempts, ?string $action, ?string $notes): void
    {
        $log = new FirewallLog();
        $log->setIp($ip);
        $log->setAttempts($attempts);
        $log->setAction($action);
        $log->setNotes($notes);
        $log->setCreatedAt(new DateTimeImmutable());

        $this->firewallLogsRepository->save($log);
    }

    /**
     * Kontroluje, zda je uživatel dočasně zablokován.
     */
    public function isUserTemporarilyBlocked(): bool
    {
        $sessionSection = $this->session->getSection('firewall');
        $blockedUntil = $sessionSection->get('blocked_until');

        return $blockedUntil && $blockedUntil > new DateTimeImmutable();
    }

    /**
     * Kontroluje, zda je uživatel trvale zablokován.
     */
    public function isUserPermanentlyBlocked(): bool
    {
        $ipAddress = $this->request->getRemoteAddress();
        $log = $this->firewallLogsRepository->findLastByIp($ipAddress);

        return $log && $log->getAction() === 'blocked';
    }

    /**
     * Přesměruje uživatele na homepage s chybovou zprávou.
     */
    #[NoReturn] private function redirectToHomeWithMessage(string $message): void
    {
        $this->presenter->flashMessage($message, 'error');
        $this->presenter->redirect('Home:default');
    }

    public function unBlockUser(): void
    {
       if($sessionSection = $this->session->getSection('firewall')) {
           $sessionSection->set('attempts', 0);
           $sessionSection->remove('blocked_until');
           if (($firewallLogs = $this->firewallLogsRepository->findLastByIp($this->request->getRemoteAddress())) && $firewallLogs->getAction() === 'blocked') {
               $firewallLogs->setAction('unblocked');
               $this->firewallLogsRepository->save($firewallLogs);
           }

       }
    }

    private function blockIpInUbuntuFirewall(string $ip): void
    {
        exec("sudo {$this->ufwPath} deny from $ip");
    }
}