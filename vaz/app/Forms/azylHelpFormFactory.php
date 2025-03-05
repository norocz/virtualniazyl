<?php

namespace App\Forms;

use Nette\Application\UI\Form;

class azylHelpFormFactory extends Form
{
    public function create(): Form
    {
        $form = new Form();
        $form->addText('name','Jméno')
            ->setHtmlAttribute('class', 'form-control');
        $form->addTextArea('quest','quest')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Poslat dotaz bez dotazu... asi nejde ;-)')
            ->addRule($form::MinLength, 'Dotaz by měl mít alespoň %d znaků', 20);
        $form->addSubmit('question','Odeslat')
            ->setHtmlAttribute('class', 'btn btn-primary');
        return $form;
    }
}