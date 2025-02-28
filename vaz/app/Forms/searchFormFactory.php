<?php

namespace App\Forms;

use Nette\Application\UI\Form;

class searchFormFactory
{

    public function create(): Form
    {
        $form = new Form();
        $form ->setHtmlAttribute('class', 'form-inline position-relative w-lg-50 ms-lg-4 ms-xl-9 mt-3 mt-lg-0');
        $form->addProtection();
        $form->addText('search')
            ->setHtmlAttribute('class', 'search fs-8 bg-transparent form-control');


        $form->addSubmit('send', 'Hledat');

        return $form;
    }

}