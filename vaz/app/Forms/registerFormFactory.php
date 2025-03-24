<?php
declare(strict_types=1);

namespace App\Forms;

use App\Model\Orm\Repository\UsersRepository;
use Nepada\PhoneNumberInput\PhoneNumberInput;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\Form;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Html;

class RegisterFormFactory extends Form
{
    public function __construct(protected UsersRepository $usersRepository, protected EntityManagerInterface $entityManager, private LinkGenerator $linkGenerator)
    {
        parent::__construct();
        $this->linkGenerator = $linkGenerator;
    }

    public function create(): Form
    {
        $termsLink = $this->linkGenerator->link('Page:terms');
        $form = new Form;
        $form->addText('username', 'Uživatelské jméno:')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Zadejte prosím uživatelské jméno.');

        $form->addPassword('password', 'Heslo:')
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(Form::Filled, 'Zadejte vaše  heslo.')
            ->setRequired('Zadejte prosím heslo.');

        $form->addPassword('password2', 'Heslo znovu:')
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(Form::Equal, 'Hesla se neshodují.', $form['password'])
            ->setRequired('Zadejte prosím heslo znovu.');

        $form->addEmail('email', 'Email:')
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(Form::Email, 'Prosím zadejte platný email.')
            ->addRule(function ($input) {
                return !$this->usersRepository->findOneBy(['email' => $input->getValue()]);
            }, 'Tento email je již registrován.')
            ->setRequired('Zadejte prosím email. Je důležitý pro přihlášení');


        $form->addText('phone', 'Telefon:')
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(PhoneNumberInput::REGION, 'Prosím zadejte platný telefonní číslo. Pro ČR nebo SR začíná na +420 nebo +421.',['CZ', 'SK'])
            ->setCaption('Telefonní číslo', 'form-control')
            ->setEmptyValue('+420')
            ->setRequired('Zadejte prosím platný telefon. Bude důležtý pro ověření.');

        $form->addCheckbox('legalTerms', Html::el()->setHtml(' Přečetl jsem si <a href="'.$termsLink.'">podmínky užití</a> souhlasím s nimi.'))
            ->setHtmlAttribute('class', 'form-check-input');

        $form->addCheckbox('adoptionVerification', ' Počítám s tím, že provozovatel serveru si před adopcí bude ověřovat mou totižnost a podmínky pro adopci.')
            ->setHtmlAttribute('class', 'form-check-input');

        $form->addSubmit('send', 'Registrovat se')
            ->setHtmlAttribute('class', 'btn btn-gradient');

        return $form;
    }
}