<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Model\Orm\Repository\PageRepository;
use App\Model\Services\Menu;
use App\Model\VersionService;
use Contributte\Application\UI\BasePresenter;
use App\Model\Orm\Repository\MessagesRepository;
use Nette;

class PagePresenter extends BasePresenter
{
    public PageRepository $PageRepository;


    public function __construct(PageRepository $PageRepository,
                                private readonly MessagesRepository $messagesRepository,
                                private readonly VersionService $versionService )
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
            $this->getTemplate()->messagesCount = '' ;// $this->messagesRepository->countUnreadMessages($this->getPresenter()->getUser()->getId());

        }
        $this->getTemplate()->mainMenuItems = $menu->getMenu();
    }

    protected function beforeRender(): void
    {
        $this->getTemplate()->addFilter('safeHtml', function (string $html): string {
            $allowedTags = ['b', 'i', 'a'];
            $html = strip_tags($html, '<' . implode('><', $allowedTags) . '>');

            // Povolit pouze bezpečné atributy v <a>
            return preg_replace_callback('/<a\s+([^>]+)>/i', function ($matches) {
                if (preg_match('/href=["\'](.*?)["\']/', $matches[1], $hrefMatch)) {
                    return '<a href="' . htmlspecialchars($hrefMatch[1], ENT_QUOTES) . '">';
                }
                return '<a>';
            }, $html);
        });

        $this->getTemplate()->version = $this->versionService->getLastVersion();
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
        if (!$page) {
            throw new Nette\Application\BadRequestException("Tak tahle stránka tu není, možná je smazaný a možná tady nikdy nebyl", 404);
        }
            $this->getTemplate()->setFile(__DIR__ . '/templates/Page/default.latte');
            $this->getTemplate()->content = $page->getContent();
            $this->getTemplate()->title = $page->getTitle();
            $this->getTemplate()->kytka = 'kytka'.rand(1,4).'.jpeg';


    }
}