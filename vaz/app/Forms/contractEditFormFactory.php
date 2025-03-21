<?php

namespace App\Forms;

use Nette\Application\UI\Form;

class contractEditFormFactory extends Form
{
    public function create(): Form
    {
        $form = new Form();

        $form->addText('name', 'Jmeno smlouvy:')
            ->setHtmlAttribute('class', 'form-control');
        $form->addDate('closedAt','Platnost do:')
            ->setHtmlAttribute('class', 'form-control');

        $form->addTextArea('content', 'Smlouva:')
            ->setHtmlAttribute('class', 'form-control editor') // Skryjeme původní textarea
            ->setHtmlAttribute('rows', '15')
            ->setHtmlAttribute('cols', '120')
            ->setHtmlAttribute('id','editor')
            ->setHtmlAttribute('data-editor', 'true');  // Identifikátor pro JS

        $form->addProtection();
        $form->addSubmit('save', 'Uložit');


        return $form;
    }
}