<?php
declare(strict_types=1);

namespace App\Forms;

use Nette\Application\UI\Form;

class newsFormFactory extends Form
{
    public function create(): Form
    {
        $form = new Form;
        $form->addText('title', 'Title:')
            ->setRequired('Nadpis.')
            ->setHtmlAttribute('class','form-control');
        $form->addTextArea('content', 'Content:')
            ->setMaxLength(2048)
            ->setHtmlAttribute('rows', 5)
            ->setHtmlAttribute('cols', 80)
            ->setHtmlAttribute('class','form-control')
            ->setHtmlAttribute('id', 'content');

        $form->addCheckbox('global', 'Globální')
            ->setHtmlAttribute('class','form-check-input');
        $form->addCheckbox('important', 'Důležité')
            ->setHtmlAttribute('class','form-check-input');
        $form->addDateTime('visibleFrom', 'Viditelné od:')
            ->setHtmlAttribute('class','form-control')
            ->setDefaultValue(date('d.m.Y H:i'))
            ->setRequired('Datum viditelnosti.');
        $form->addSubmit('send', 'Uložit')
            ->setHtmlAttribute('class', 'btn btn-primary');
        return $form;
    }
}