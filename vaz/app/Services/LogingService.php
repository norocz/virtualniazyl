<?php

namespace App\Services;

use App\Model\Orm\Entity\Loginout;
use App\Model\Orm\Entity\Users;
use App\Model\Orm\Repository\LoginoutRepository;
use Nette\Application\UI\Presenter;

class LogingService
{
    private string $description;
    private string $action;
    private ?string $ip;
    private ?string $hostName;
    private Users $user;
    public loginoutRepository $loginoutRepository;
    public function __construct(loginoutRepository $loginoutRepository)
    {

        $this->ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $this->hostName = $_SERVER['HTTP_HOST'] ?? null;

    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @param Users $user
     */
    public function setUser(Users $user): void
    {
        $this->user = $user;
    }

    /**
     * @param string $hostName
     */
    public function setHostName(string $hostName): void
    {
        $this->hostName = $hostName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getHostName(): string
    {
        return $this->hostName;
    }

    public function getUser(): Users
    {
        return $this->user;
    }

    public function saveLog()
    {
        $log = new Loginout();
        $log->setIp($this->ip);
        $log->setUser($this->user);
        $log->setHostName($this->hostName);
        $log->setDescription($this->description);
        $log->setLastlogin();
    $log = array([
        'action' => $this->action,
        'description' => $this->description,
        'ip' => $this->ip,
        'hostName' => $this->hostName,
        'user' => $this->user,

    ]);
    $this->loginoutRepository->saveLog($log);
    }


}