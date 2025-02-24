<?php
declare(strict_types=1);

namespace App\Forms;

use App\Model\Orm\Repository\CityRepository;
use App\Presenters\UserPresenter;
use Nepada\PhoneNumberInput\PhoneNumberInput;
use Nepada\Bridges\PhoneNumberInputForms;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;


class userDetailsFormFactory extends Form
{
    private CityRepository $cityRepository;
    public UserPresenter $presenter;
    public string $jsonLink; //předání odkazu pro json

    public function __construct(CityRepository $cityRepository)
    {
        parent::__construct();
        $this->cityRepository = $cityRepository;

    }

    public function setLink($link): void
    {
        $this->jsonLink = $link;
    }

    /**
     * @throws InvalidLinkException
     */
    public function create(): Form
    {
        $form = new Form;
        $form->addProtection('S formulářem nebo daty bylo manipulováno!');

        $form->addPhoneNumber('phone', 'Telefon:')
        //$form->addPhoneNumber('phone', 'Telefon:', 'CZ')
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(PhoneNumberInput::REGION, 'Prosím zadejte platný telefonní číslo. Pro ČR nebo SR začíná na +420 nebo +421.',['CZ', 'SK'])
            ->setCaption('Telefon:', 'form-control');

        $form->addText('firstName', 'Jméno:')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('lastName', 'Příjmení:')
            ->setHtmlAttribute('class', 'form-control');

        $form->addTextArea('description', 'Popis:')
            ->setOption('description', ' ')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('rows', 8)
            ->setHtmlAttribute('cols', 45);
        $form->addText('street', 'Ulice a číslo:')
            ->setHtmlAttribute('class', 'form-control form-inline');
        $form->addInteger('house', 'Čp:')
            ->setHtmlAttribute('class', 'form-control form-inline');
        $form->addText('orientation','Čo:')
            ->setHtmlAttribute('class', 'form-control form-inline');
        $form->addSelect('city','Město:')
            ->setPrompt('Vyber obec')
            ->setHtmlAttribute('class', 'form-control select2')
            ->setHtmlAttribute('id', 'citySelect')
            ->setHtmlAttribute('data-url', $this->jsonLink);

        $form->addSubmit('send', 'Uložit')
            ->setHtmlAttribute('class', 'btn btn-success form-control');

        return $form;
    }
}