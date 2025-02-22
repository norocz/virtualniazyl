<?php
declare(strict_types=1);

namespace App\Forms;

use Nette\Application\UI\Form;


class PhotoUploadFormFactory
{
    public function create(): Form
    {
        $form = new Form;
        $form->addMultiUpload('photos', 'Vyberte fotografie: ')
            ->setHtmlAttribute('class', 'form-control btn btn-success')
            ->setRequired('Vyberte alespoň jednu fotku')
            ->setHtmlAttribute('accept', 'image/*')
            ->addRule($form::MaxLength, 'Maximálně lze nahrát %d souborů', 10)
            ->setHtmlAttribute('multiple');


        $form->addSubmit('send', 'Nahrát')
            ->setHtmlAttribute('class', 'btn btn-success form-control');

        return $form;
    }

}