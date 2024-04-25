<?php

declare(strict_types=1);

namespace App\Forms;

use Nette\Application\UI\Form;

class PhotosFormFactory extends Form
{
    public function create(): Form
    {
        $form = new Form;
        $form->addMultiUpload('photos', 'Fotografie:')
            ->setHtmlAttribute('class', 'form-control');
        $form->addSubmit('send', 'Uložit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        return $form;
    }
}