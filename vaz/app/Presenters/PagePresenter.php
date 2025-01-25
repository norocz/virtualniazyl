<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Model\Orm\Repository\PageRepository;
use App\Model\Services\Menu;
use Contributte\Application\UI\BasePresenter;
use App\Model\Orm\Repository\MessagesRepository;

class PagePresenter extends BasePresenter
{
    public PageRepository $PageRepository;


    public function __construct(PageRepository $PageRepository, private readonly MessagesRepository $messagesRepository)
    {
        parent::__construct();
        $this->PageRepository = $PageRepository;
    }

    public function startup(): void
    {

        parent::startup();
        $menu = new Menu();
        $this->getTemplate()->setFile(__DIR__ . '/templates/Page/default.latte');
        if ($this->getPresenter()->getUser()->isLoggedIn())
        {
            $this->getTemplate()->messagesCount = $this->messagesRepository->countUnreadMessages($this->getPresenter()->getUser()->getId());

        }
        $this->getTemplate()->mainMenuItems = $menu->getMenu();
    }

    public function renderDefault(): void
    {
        $this->getTemplate()->setFile(__DIR__ . '/templates/Page/default.latte');
        $this->template->kytka = 'error404-dino.jpeg';
        $this->template->content =  "404 - Stránka nebyla nalezena";
        $this->template->title = "404 - Stránka nebyla nalezena";
       // $this->getPresenter()->sendResponse('S404_NotFound ');
    }

    public function actionShow(string $link): void
    {
        $page = $this->PageRepository->findByLink($link);
        if(!$page){
            $this->getTemplate()->setFile(__DIR__ . '/templates/Page/default.latte');
            $this->template->content = "404 - Stránka nebyla nalezena";
            $this->template->kytka = 'kytka'.rand(1,4).'.jpeg';
        }
        else{
            $this->getTemplate()->setFile(__DIR__ . '/templates/Page/default.latte');
            $this->template->content = $page->getContent();
            $this->template->title = $page->getTitle();
            $this->template->kytka = 'kytka'.rand(1,4).'.jpeg';

        }
    }
}