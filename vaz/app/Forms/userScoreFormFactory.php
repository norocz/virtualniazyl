<?php

namespace App\Forms;

use Nette\Application\UI\Form;

class userScoreFormFactory extends Form
{
    public function create(): Form
    {
        $form = new Form();
        $score = ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5'];
        $form->addRadioList('score','Hodnoceni:', $score);
        $form->addHidden('review');
        $form->addTextArea('comment','Komentář');
        $form->onSubmit[] = [$this, 'rewiev'];
        return $form;
    }
}