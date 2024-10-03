<?php
declare(strict_types=1);

namespace App\Forms;


use Nette\Application\UI\Form;

class messagesFormFactory extends Form
{
    public function create(): Form
    {
        $form = new Form;
        $form->addHidden('address')
            ->setRequired();
        $form->addTextArea('message', 'Zpráva:')
            ->setRequired('Prosím zadejte zprávu.')
            ->setHtmlAttribute('class', 'form-control msg-textarea');

        $form->addSubmit('send', 'Odeslat')
        ->setHtmlAttribute('class', 'btn btn-primary msg-submit');

        return $form;
    }

}