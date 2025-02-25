<?php

namespace App\Forms;

use Nette\Application\UI\Form;

class azylSendMessageFormFactory
{

    public function create(): Form
    {
        $form = new Form();
        $form->addProtection();
        $form->addTextArea('message','Vzkaz')
            ->setHtmlAttribute('class', 'form-control')
            ->addRule($form::MinLength, 'Vzkaz by měl mít alespoň %d znaků', 10)
            ->setRequired('Nelze poslat vzkaz bez vzkazu ;-)');

        $form->addSubmit('send','Odeslat')
            ->setHtmlAttribute('class', 'btn btn-primary');

        return $form;
    }
}