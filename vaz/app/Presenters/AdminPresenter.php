<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Components\Datagrids\AnimalsDatagridFactory;
use App\Components\Datagrids\CitysDatagridFactory;
use App\Components\Datagrids\PagesDatagridFactory;
use App\Components\Datagrids\UsersDatagridFactory;
use App\Components\Datagrids\SpeciesDatagridFactory;
use App\Forms\newsFormFactory;
use App\Forms\PageFormFactory;
use App\Forms\SpeciesFormFactory;
use App\Forms\PhotoUploadFormFactory;
use App\Forms\RegisterFormFactory;
use App\Forms\roleFormFactory;
use App\Forms\userDetailsFormFactory;
use App\Model\Orm\Entity\Pages;
use App\Model\Orm\Enums\RoleTypeEnum;
use App\Model\Orm\Repository\PageRepository;
use App\Model\Orm\Repository\UsersRepository;
use App\Model\Services\Menu;
use App\Repository\SpeciesRepository;
use App\Services\MessagesService;
use Contributte\Application\UI\BasePresenter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;
use App\Components\Datagrids\NewsDatagridFactory;
use App\Model\Orm\Entity\News;
use App\Model\Orm\Repository\NewsRepository;
use App\Model\Orm\Entity\Species;



class AdminPresenter extends BasePresenter
{

    private roleFormFactory $roleFormFactory;
    private UsersRepository $usersRepository;
    private EntityManagerInterface $entityManager;

    private PageFormFactory $pageFormFactory;
    private PageRepository $pageRepository;


    public function __construct(roleFormFactory                     $roleFormFactory,
                                UsersRepository                     $usersRepository,
                                PageRepository                      $pageRepository,
                                EntityManagerInterface              $entityManager,
                                PageFormFactory                     $pageFormFactory,
                                public UserDetailsFormFactory       $userDetailsFormFactory,
                                public registerFormFactory          $registerFormFactory,
                                public PhotoUploadFormFactory       $photoUploadFormFactory,
                                public UsersDatagridFactory         $usersDatagridFactory,
                                public CitysDatagridFactory         $citysDatagridFactory,
                                public PagesDatagridFactory         $pagesDatagridFactory,
                                public readonly newsFormFactory     $newsFormFactory,
                                public readonly speciesFormFactory  $speciesFormFactory,
                                public readonly newsDatagridFactory $newsDatagridFactory,
                                public readonly speciesDatagridFactory  $speciesDatagridFactory,
                                public newsRepository               $newsRepository,
                                public MessagesService              $messagesService,
                                public SpeciesRepository            $speciesRepository,
                                public AnimalsDatagridFactory      $animalsDatagridFactory)
    {
        parent::__construct();
        $this->roleFormFactory = $roleFormFactory;
        $this->usersRepository = $usersRepository;
        $this->entityManager = $entityManager;
        $this->photoUploadFormFactory = $photoUploadFormFactory;
        $this->pageFormFactory = $pageFormFactory;
        $this->pageRepository = $pageRepository;
        $this->usersDatagridFactory = $usersDatagridFactory;
        $this->citysDatagridFactory = $citysDatagridFactory;
        $this->pagesDatagridFactory = $pagesDatagridFactory;
        $this->newsRepository = $newsRepository;
        $this->messagesService = $messagesService;
        $this->speciesRepository = $speciesRepository;
        $this->animalsDatagridFactory = $animalsDatagridFactory;
        //$this->speciesFormFactory = $speciesFormFactory;
        //$this->speciesDatagridFactory = $speciesDatagridFactory;

    }

    public function startup()
    { parent::startup();
        if (!$this->getPresenter()->getUser()->isLoggedIn()) {
            $this->redirect('Home:SignIn');

        } else {
           $userData = $this->getPresenter()->getUser()->getIdentity()->getData();
            if (!$this->getPresenter()->getUser()->isInRole('admin') && !$this->getPresenter()->getUser()->isInRole('superadmin')) {
                $this->flashMessage('Nemáte dostatečná oprávnění pro tuto akci. Akce byla zalogována!', 'alert-danger');
                $this->redirect('Home:default');
            } else {

                $menu = new Menu();

            }
        }
        $this->getTemplate()->mainMenuItems = $menu->getMenu();
    }

    public function renderDefault(): void
    {
        $this->template->title = 'Admin';
        $this->template->newUsersCount = $this->usersRepository->CountNewUsers();
        $this->template->usersCount = $this->usersRepository->CountUsers();
        $this->template->azylsCount = $this->usersRepository->CountAzyls();
    }

    public function renderUpdateMessagesAddress(): void
    {
        $this->setView('default');
        $this->getTemplate()->title = 'Aktualizace a migrace';
        $this->template->newUsersCount = $this->usersRepository->CountNewUsers();
        $this->template->usersCount = $this->usersRepository->CountUsers();
        $this->template->azylsCount = $this->usersRepository->CountAzyls();
        $this->messagesService->UpdateMessages();
        $this->flashMessage('Aktualizace a migrace proběhly', 'alert-success');
    }
    public function renderAnimals(): void
    {
        $this->template->title = 'Animals';
    }

    public function renderSpecies(): void
    {
        $this->template->title = 'Species';
    }

    public function renderAzyls(): void
    {
        $this->template->title = 'Azyls';
    }

    public function renderOwner(): void
    {
        $this->template->title = 'Owner';
    }

    public function renderSendmessages(): void
    {
        $this->template->title = 'Sendmessage';
    }

    public function renderAdoptions(): void
    {
        $this->template->title = 'Adoptions';
    }

    public function renderCitys(): void
    {
        $this->template->title = 'Citys';
    }

    public function actionNews(?int $id): void
    {
        if ($id !== null) {
            $news = $this->newsRepository->findOneBy(['id' => $id]);
            if($news === null) {
                $this->flashMessage('Novinka nebyla nalezena.', 'alert-danger');
                $this->redirect('Admin:news');
            }
            $this->getTemplate()->title = 'Editace novinky'. $news->getTitle();
            $newsForm = $this->getComponent('newsForm');
            $newsForm->setDefaults($news->toArray());
        }

        $this->template->title = 'Novinky';
    }
    public function actionPage(?int $id): void
    {
        $this->getTemplate()->Title = 'Pages';
        if ($id !== null) {
            $page = $this->pageRepository->find($id);
            if ($page === null) {
                $this->flashMessage('Stránka nebyla nalezena.', 'alert-danger');
                $this->redirect('Admin:pages');
            }

            $pageForm = $this->getComponent('pageForm');
            $pageForm->setDefaults($page->toArray());



            /*
            $this['pageForm']['link']->setDefaults($page->getLink());
            $this['pageForm']['visibleFrom']->setDefaults($page->getVisibleFrom());
            $this['pageForm']['title']->setDefaults($page->getTitle());
            $this['pageForm']['content']->setDefaults($page->getContent());
            $this['pageForm']['important']->setDefaults($page->getImportant());
            $this['pageForm']['global']->setDefaults($page->getGlobal());
            */
        }

        $this->template->title = 'Pages';
    }


    // Actions


    // Handle

    public function handleNewsDelete(?int $id): void
    {
        $news = $this->newsRepository->findOneBy(['id' => $id]);
        if ($news === null) {
            $this->flashMessage('Novinka nebyla nalezena.', 'alert-warning');
            if ($this->isAjax()) {
                $this->redrawControl('flashes');
                $this['actionsGrid']->reload();

            } else {
                $this->redirect('Admin:news');
            }
        } else {

            $news->setDeleted(true);
            $this->newsRepository->save($news);
            $this->flashMessage('Novinka byla smazána.', 'alert-success');
            if ($this->isAjax()) {
                $this->redrawControl('flashes');
                $this['actionsGrid']->reload();

            } else {
                $this->redirect('Admin:news');
            }
        }
    }

    public function handleDelete(int $id): void
    {
        $animal = $this->animalsRepository->findById($id);
        $animal->setIsDeleted(true);
        $this->animalsRepository->saveAnimal($animal);
        $this->flashMessage('Zvířátko bylo smazáno.', 'alert-success');
        $this->redirect('this');
    }


    // Components
    public function createComponentPageForm(): Form
    {
        $form = $this->pageFormFactory->create();
        $form->onSuccess[] = [$this, 'pageFormSucceeded'];
        return $form;
    }

    public function pageFormSucceeded(Form $form, \stdClass $values): void
    {

        if ($this->getPresenter()->getParameter('id') !== null) {
            $page = $this->pageRepository->findOneBy(['id' => $this->getPresenter()->getParameter('id')]);


            if ($page) {
                $page->setLink($values->link);
                $page->setVisibleFrom($values->visibleFrom);
                $page->setTitle($values->title);
                $page->setContent($values->content);
                $page->setImportant($values->important);
                $page->setGlobal($values->global);
                $page->setUpdatedAt(new DateTimeImmutable());
                $this->pageRepository->save($page);
                $this->flashMessage('Stránka byla aktualizována.', 'alert-success');
                $this->redirect('Admin:pages');
            }
        } else {
            $page = new Pages();
            $page->setCreated(new DateTimeImmutable());
            $data = $this->getPresenter()->getUser()->getIdentity()->getData();
            $page->setAuthor($data['User']);
            $page->setLink($values->link);
            $page->setVisibleFrom($values->visibleFrom);
            $page->setTitle($values->title);
            $page->setContent($values->content);
            $page->setImportant($values->important);
            $page->setGlobal($values->global);
            $this->pageRepository->save($page);
            $this->flashMessage('Stránka byla uložena.', 'alert-success');
            $this->redirect('Admin:pages');
        }
    }

    /**
     * @throws DataGridException
     */
    public function createComponentUsersDatagrid(): DataGrid
    {
        $grid = $this->usersDatagridFactory->create();
        return $grid;
    }

    public function createComponentAzylsDatagrid(): DataGrid
    {
        $grid = $this->usersDatagridFactory->create();
        $grid->setDataSource($this->usersRepository->findBy(['role' => RoleTypeEnum::ROLE_AZYL]));
        return $grid;
    }

    public function createComponentOwnersDatagrid(): DataGrid
    {
        $grid = $this->usersDatagridFactory->create();
        $grid->setDataSource($this->usersRepository->fetchAll());
        return $grid;
    }

    public function createComponentCitysDatagrid(): DataGrid
    {
        $grid = $this->citysDatagridFactory->create();
        return $grid;
    }

    public function createComponentPagesDatagrid(): DataGrid
    {
        $grid = $this->pagesDatagridFactory->create();
        return $grid;
    }

    public function createComponentSpeciesDatagrid(): DataGrid
    {
        $grid = $this->speciesDatagridFactory->create();
        return $grid;
    }

    public function createComponentSpeciesForm(): Form
    {
        $form = $this->speciesFormFactory->create();
        $form->onSuccess[] = [$this, 'speciesFormSucceeded'];
        return $form;
    }



    public function speciesFormSucceeded(Form $form, \stdClass $values): void
    {
        bdump($values);
        if ($this->getPresenter()->getParameter('id') !== null) {
            $species = $this->speciesRepository->findOneBy(['id' => $this->getPresenter()->getParameter('id')]);
            if ($species) {
                $species->setSpecies($values->species);
                $species->setBreed($values->breed);
                $species->setAzyl($values->azyl);
                $this->speciesRepository->save($species);
                $this->flashMessage('Druh byl aktualizován.', 'alert-success');
                $this->redirect('Admin:species');
            }
        } else {
            $species = new Species();
            $species->setName($values->name);
            $species->setSex($values->sex);
            $species->setDescription($values->description);
            $this->speciesRepository->save($species);
            $this->flashMessage('Druh byl uložen.', 'alert-success');
            $this->redirect('Admin:species');
        }
    }

    public function createComponentNewsForm(): Form
    {
        $form = $this->newsFormFactory->create();
        $form->onSuccess[] = [$this, 'newsFormSucceeded'];
        return $form;
    }

    public function newsFormSucceeded(Form $form, \stdClass $values): void
    {
        if ($this->getPresenter()->getParameter('id') !== null) {
            $news = $this->newsRepository->findOneBy(['id' => $this->getPresenter()->getParameter('id')]);
            if ($news) {
                $news->setTitle($values->title);
                $news->setContent($values->content);
                $news->setGlobal($values->global);
                $news->setVisibleFrom($values->visibleFrom);
                $news->setUpdatedAt(new DateTimeImmutable());
                $news->setDeleted(false);
                $news->setImportant($values->important);
                $this->newsRepository->save($news);
                $this->flashMessage('Novinka byla aktualizována.', 'alert-success');
                $this->redirect('Admin:news');
            }
        } else {

            $news = new News();
            $author = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getIdentity()->getId());
            $news->setAuthor($author);
            $news->setTitle($values->title);
            $news->setContent($values->content);
            $news->setGlobal($values->global);
            $news->setVisibleFrom($values->visibleFrom);
            $news->setImportant($values->important);
            $news->setCreatedAt(new DateTimeImmutable());
            $news->setDeleted(false);
            $this->newsRepository->save($news);
            $this->flashMessage('Novinka byla uložena.', 'alert-success');
            $this->redirect('Admin:news');
        }
    }

    public function createComponentNewsDatagrid(): DataGrid
    {
        $grid = $this->newsDatagridFactory->create();
        $grid->setDataSource($this->newsRepository->findAllVisible($this->getPresenter()->getUser()->id));
        return $grid;
    }

    public function createComponentAdoptionsDatagrid(): DataGrid
    {
        $grid = $this->adoptionsDatagridFactory->create();
        return $grid;
    }

    public function createComponentAnimalsAdminDatagrid(): DataGrid
    {
        $grid = $this->animalsDatagridFactory->create();
        return $grid;
    }

}