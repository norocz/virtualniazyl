<?php
declare(strict_types=1);
namespace App\Forms;
use App\Model\Orm\Repository\UsersRepository;
use Nepada\PhoneNumberInput\PhoneNumberInput;
use Nette\Application\UI\Form;
use Doctrine\ORM\EntityManagerInterface;


class RegisterFormFactory extends Form
{
    public function __construct(protected UsersRepository $usersRepository, protected EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    public function create(): Form
    {
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

        $form->addText('email', 'Email:')
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(Form::Email, 'Prosím zadejte platný email.')
            ->setRequired('Zadejte prosím email. Je důležitý pro přihlášení');


        $form->addText('phone', 'Telefon:')
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(PhoneNumberInput::REGION, 'Prosím zadejte platný telefonní číslo. Pro ČR nebo SR začíná na +420 nebo +421.',['CZ', 'SK'])
            ->setCaption('Telefonní číslo', 'form-control')
            ->setEmptyValue('+420')
            ->setRequired('Zadejte prosím platný telefon. Bude důležtý pro ověření.');

        $form->addCheckbox('legalTerms', ' Přečetl jsem si podmínky užití a souhlasím s nimi.')
            ->setHtmlAttribute('class', 'form-check-input');

        $form->addCheckbox('adoptionVerification', ' Počítám s tím, že provozovatel serveru si před adopcí bude ověřovat mou totižnost a podmínky pro adopci.')
            ->setHtmlAttribute('class', 'form-check-input');

        $form->addSubmit('send', 'Registrovat se')
            ->setHtmlAttribute('class', 'btn btn-gradient');

        return $form;
    }
}