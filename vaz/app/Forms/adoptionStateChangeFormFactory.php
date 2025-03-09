<?php

namespace App\Forms;

use Nette\Application\UI\Form;

class adoptionStateChangeFormFactory extends Form
{
    public function create(): Form
    {
        $form = new Form();
        $form->addHidden('review');
        $form->addTextArea('commentText','Komentář');
        $form->addSubmit('comment','Jen Přidat poznámku')
            ->setHtmlAttribute('class', 'btn btn-success');
        $form->addSubmit('writ','Písemný kontakt')
            ->setHtmlAttribute('class', 'btn btn-success');
        $form->addSubmit('phon','Telefonát')
            ->setHtmlAttribute('class', 'btn btn-success');
        $form->addSubmit('pers','Osobní kontakt')
            ->setHtmlAttribute('class', 'btn btn-warning');
        $form->addSubmit('pre','Předschválit adopci')
            ->setHtmlAttribute('class', 'btn btn-warning');
        $form->addSubmit('ok','Zvíře bylo adoptováno')
            ->setHtmlAttribute('class', 'btn btn-danger');
        $form->addSubmit('stop','Přerušit adopci')
            ->setHtmlAttribute('class', 'btn btn-danger');

        $form->onSubmit[] = [$this, 'rewiev'];
        return $form;
    }
}