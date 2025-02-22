<?php

declare(strict_types=1);

namespace App\Forms;

use Nepada\PhoneNumberInput\PhoneNumberInput;
use Nette\Application\UI\Form;

class AzylSetingsFormFactory extends Form
{
    public string $jsonLink; //předání odkazu pro json
    public function setLink($link): void
    {
        $this->jsonLink = $link;
    }
    public function create(): Form
    {
        $form = new Form;
        $form->addHidden('id');
        $form->addText('azylName', 'Jméno azylu')
            ->setRequired('Zadejte jméno azylu')
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(Form::MaxLength, 'Jméno azylu může mít maximálně %d znaků', 255);
        $form->addTextArea('description', 'Popis azylu')
            ->setHtmlAttribute('id', 'azylDescription')
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(Form::MaxLength, 'Info o azylu může mít maximálně %d znaků', 2048);
        $form->addText('bankAccount', 'Bankovní účet')
            ->setHtmlAttribute('class', 'form-control')      ;
        $form->addText('bankCode', 'Kód banky')
            ->setHtmlAttribute('class', 'form-control');
        $form->addText('bankSpecificCode', 'Variabilní symbol')
             ->setDefaultValue('269')
             ->setHtmlAttribute('class', 'form-control');
        $form->addText('phoneNumber', 'Telefonní číslo azylu')
            ->setRequired('Zadejte telefonní číslo')
            ->addRule(PhoneNumberInput::REGION, 'Prosím zadejte platný telefonní číslo. Pro ČR nebo SR začíná na +420 nebo +421.',['CZ', 'SK'])
            ->setDefaultValue('+420')
            ->setHtmlAttribute('class', 'form-control');
        $form->addEmail('email', 'E-mail')
        ->setHtmlAttribute('class', 'form-control');
        $form->addText('web', 'web')
               ->setHtmlAttribute('class', 'form-control')
                ->setHtmlAttribute('placeholder', 'https://');
        $form->addSelect('city','Město', [])
             ->setHtmlAttribute('class', 'form-control select2')
             ->setHtmlAttribute('id', 'citySelect')
             ->setHtmlAttribute('data-url', $this->jsonLink);
        $form->addSubmit('sendAzylSettings', 'Uložit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        return $form;
    }

}