<?php

namespace App\Forms;

use Nette\Application\UI\Form;

class userScoreFormFactory extends Form
{
    public function create(): Form
    {
        $form = new Form();
        $form->addHidden('review');
        $form->addTextArea('comment','Komentář');
        $form->addSubmit('1','1')
            ->setHtmlAttribute('class', 'btn btn-success');
        $form->addSubmit('2','2')
            ->setHtmlAttribute('class', 'btn btn-success');
        $form->addSubmit('3','3')
            ->setHtmlAttribute('class', 'btn btn-warning');
        $form->addSubmit('4','4')
            ->setHtmlAttribute('class', 'btn btn-warning');
        $form->addSubmit('5','5')
            ->setHtmlAttribute('class', 'btn btn-danger');

        return $form;
    }
}