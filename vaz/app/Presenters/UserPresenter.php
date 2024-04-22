<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\PhotoUploadFormFactory;
use App\Forms\RegisterFormFactory;
use App\Forms\roleFormFactory;
use App\Forms\userDetailsFormFactory;
use App\Model\Orm\Entity\Azyl;
use App\Model\Orm\Enums\RoleTypeEnum;
use App\Model\Orm\Repository\AzylRepository;
use App\Model\Orm\Repository\OwnersRepository;
use App\Model\Orm\Repository\UsersRepository;
use App\Model\Services\Menu;
use Contributte\Application\UI\BasePresenter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use App\Model\Orm\Entity\Owner;


class UserPresenter extends BasePresenter
{
    private roleFormFactory $roleFormFactory;
    private UsersRepository $usersRepository;
    private EntityManagerInterface $entityManager;
    private AzylRepository $azylRepository;

    public function __construct(roleFormFactory        $roleFormFactory,
                                UsersRepository        $usersRepository,
                                AzylRepository         $azylRepository,
                                EntityManagerInterface $entityManager,
                        private UserDetailsFormFactory $userDetailsFormFactory,
                        private registerFormFactory    $registerFormFactory,
                        private PhotoUploadFormFactory  $photoUploadFormFactory,
                        private OwnersRepository        $ownerRepository)
    {
        parent::__construct();
        $this->roleFormFactory = $roleFormFactory;
        $this->usersRepository = $usersRepository;
        $this->entityManager = $entityManager;
        $this->azylRepository = $azylRepository;
        $this->photoUploadFormFactory = $photoUploadFormFactory;
        $this->ownerRepository = $ownerRepository;
    }

    public function startup(): void
    {
        parent::startup();
        $menu = new Menu();
        $this->getTemplate()->messagesCount = 1;
        $this->getTemplate()->mainMenuItems = $menu->getMenu();
        if (!$this->getPresenter()->getUser()->isLoggedIn())
        {
            $this->redirect('Home:signIn');
        }
    }
public function actionDefault(): void
    {
        if ($this->getPresenter()->getUser()->isLoggedIn())
        {
            if ($this->getPresenter()->getUser()->isInRole(RoleTypeEnum::ROLE_USER))
            {
                $this->getPresenter()->redirect('User:first');
            }
            elseif ($this->getPresenter()->getUser()->isInRole(RoleTypeEnum::ROLE_AZYL))
            {
                $this->getPresenter()->redirect('Azyl:profil');
            }
            else
            {
                $this->getPresenter()->redirect('User:profil');
            }
        }
        else
        {
            $this->redirect('Home:signIn');
        }
    }

    public function renderDefault(): void
    {

        $this->template->title = 'Admin';
    }

    public function renderAnimals(): void
    {
        $this->template->title = 'Animals';
    }

    public function renderAzyls(): void
    {
        $this->template->title = 'Azyls';
    }

    public function renderNews(): void
    {
        $this->template->title = 'News';
    }

    public function renderProfil(): void
    {
        $this->template->title = 'Profil';
    }

    public function renderMessages(): void
    {
        $this->template->title = 'Zprávy';
    }

    public function renderAdoptions(): void
    {
        $this->template->title = 'Adoptions';
    }
    // Actions

    public function renderFirst()
    {
        $this->template->title = 'Vyberte si roli';

    }

    public function createComponentRoleForm(): Form
    {
        $form = $this->roleFormFactory->create();
        $form->onSuccess[] = [$this, 'roleFormSucceeded'];
        return $form;
    }

    public function createComponentUserDetailsForm(): Form
    {
        $form = $this->userDetailsFormFactory->create();
        $form->onSuccess[] = [$this, 'userDetailsFormSucceeded'];
        return $form;
    }

    public function userDetailsFormSucceeded(Form $form,  \stdClass $values) : void
    {
        $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
        $user->setUpdatedAt(new DateTimeImmutable());
        $user->setUpdatedBy($this->usersRepository->getUserById($this->getPresenter()->getUser()->getId()));
        $user->setFirstName($values->firstName);
        $user->setLastName($values->lastName);








        $this->getPresenter()->flashMessage('Detaily byly úspěšně uloženy!', 'alert-success');
        $this->getPresenter()->redirect('User:profil');
    }

    public function createComponentUserUpdateForm(): Form
    {
        $form = $this->registerFormFactory->create();
        //TODO: Upravit tlačíto do stejné podoby jako jinde už jen upravit šířku
        $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());

        $form->setDefaults($user->toArray());

        $form->removeComponent($form['adoptionVerification']);
        $form->removeComponent($form['legalTerms']);
        $form->removeComponent($form['username']);
        //TODO: Dodělat username jako text k formuláři
        $form['send']->setHtmlAttribute('class', 'btn btn-primary');
        $form['send']->setCaption('Uložit změny');

        $form->onSuccess[] = [$this, 'userUpdateFormSucceeded'];
        return $form;
    }

    public function createComponentOwnerPhotoUploadForm(): Form
    {
        $form = $this->photoUploadFormFactory->create();
        $form->onSuccess[] = [$this, 'ownerPhotoUploadFormSucceeded'];
        return $form;
    }

    public function ownerPhotoUploadFormSucceeded(Form $form, \stdClass $values): void
    {
        $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
        $user->setPhotos($values->photos);
        $this->usersRepository->addUser($user);
        $this->getPresenter()->flashMessage('Fotky byly úspěšně nahrány!', 'alert-success');
        $this->presenter->redrawControl('photos');
    }

    //TODO: Tady se musí dodělat vazba na ownera a Azyl kde má každý specifické údaje a je potřeba to rozdělit po výběru


    public function roleFormSucceeded(Form $form, \stdClass $values): void
    {
        if ($values->role === RoleTypeEnum::ROLE_AZYL)
        {
            $azyl = new Azyl();

            $users = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
            $users->setRole(RoleTypeEnum::ROLE_AZYL);
            $users->setUpdatedAt(new DateTimeImmutable());
            $users->setUpdatedBy($this->usersRepository->getUserById($this->getPresenter()->getUser()->getId()));

            bdump($azyl,'azyl');
            bdump($users,'users');
            $this->azylRepository->saveAzyl($azyl);
            $users->setAzyl($azyl->getId());
            $this->usersRepository->addUser($users);


            $this->getPresenter()->flashMessage('Od této chvíle jste v roli Azylu! Znovu se přihlašte!', 'alert-success');
            $this->getPresenter()->getUser()->logout();
            $this->redirect('Home:signIn');
        }
        elseif ($values->role === RoleTypeEnum::ROLE_OWNER)
        {

            $users = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
            $users->setRole(RoleTypeEnum::ROLE_OWNER);
            $users->setUpdatedAt(new DateTimeImmutable());
            $users->setUpdatedBy($this->usersRepository->getUserById($this->getPresenter()->getUser()->getId()));
            $owner = new Owner();
            $owner->setUser($users);
            $this->usersRepository->addUser($users);
            $this->getPresenter()->flashMessage('Od této chvíle jste běžný uživatel! Znovu se prosím přihlašte!', 'alert-success');
            $this->getPresenter()->getUser()->logout();
            $this->redirect('Home:signIn');

        }

    }
}