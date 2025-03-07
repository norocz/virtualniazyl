<?php

namespace App\Forms;

use Nette\Application\UI\Form;

class contractSignFormFactory extends Form
{
    public function create()
    {
        $form = new Form();
        $form->addProtection();
        $form->addCheckbox('signTerms',' Potvrzuji že jsem si přečetl a souhlasím s adopčními podmínkami')
            ->setHtmlAttribute('class','form-check-input')
            ->setRequired(true,'Je potřeba souhlasit s adopčními podmínkami.');
        $form->addCheckbox('signContract',' Potvrzuji že jsem si přečetl a souhlasím s adopční smlouvou')
            ->setHtmlAttribute('class','form-check-input')
            ->setRequired(true,'Je potřeba souhlasit s adopční smlouvou.');
        $form->addSubmit('send','Potvrdit');

        return $form;
    }

}