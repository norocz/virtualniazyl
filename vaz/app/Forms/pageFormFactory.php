<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model\Orm\Entity\Pages;
use Nette\Application\UI\Form;

class PageFormFactory extends Form
{
    public function create(?Pages $page = null): Form
    {
        $page = $page === null ? new Pages() : $page;

        $form = new Form();
        $form->addText('title', 'Název')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Název stránky')
            ->setDefaultValue($page->getTitle());
        $form->addTextArea('content', 'Stránka')
            ->setHtmlAttribute('rows', 20)
            ->setHtmlAttribute('cols', 70)
            ->setHtmlAttribute('id', 'content')
            ->setDefaultValue($page->getContent());
        $form->addText('link', 'Odkaz')

            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Odkaz na stránku')
            //Odkaz na stránku - žádné mezery, diakritika, speciální znaky, pouze malá písmena a pomlčky.
            ->addRule(Form::PatternInsensitive, 'Odkaz na stránku - žádné mezery, diakritika, speciální znaky, pouze malá písmena a pomlčky.', '^[a-z0-9-]+$')
            ->setDefaultValue($page->getLink());

        $form->addDateTime('visibleFrom', 'Zobrazit od')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Zobrazit od')
            ->setDefaultValue($page->getVisibleFrom());
        $form->addCheckbox('important', ' Důležitá')
              ->setDefaultValue($page->getImportant());
        $form->addCheckbox('global', ' Globální')
              ->setDefaultValue($page->getGlobal());

        $form->addSubmit('send', 'Uložit stránku')
            ->setHtmlAttribute('class', 'btn btn-primary');
        return $form;
    }
}