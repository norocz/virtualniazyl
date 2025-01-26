<?php
declare(strict_types=1);

namespace App\Forms;

use App\Repository\SpeciesRepository;
use Nette\Application\UI\Form;

class adoptionFormFactory extends Form
{

    public function create(): Form
    {
        $form = new Form();
        $form->addProtection();
        $form->addCheckbox('info',' Souhlas s prověřením informací')
            ->setRequired('Je potřeba souhlasit s prověřením informací!')
            ->setHtmlAttribute('class', 'checkbox form-inline');
        $form->addCheckbox('souhlas',' Souhlas s zpracováním údajů')
            ->setRequired('Je potřeba souhlasit se zpracováním údajů!')
            ->setHtmlAttribute('class', 'checkbox form-inline');
        $form->addTextArea('description','Info pro azyl')
            ->setMaxLength(2048)
            ->setHtmlAttribute('rows', 5)
            ->setHtmlAttribute('cols', 80)
             ->setHtmlAttribute('class', 'form-control form-textarea');
        $form->addInteger('howMuch','Kolik zvířat chcete adoptovat')
             ->setHtmlAttribute('class', 'form-control form-textarea');
        $form->addSubmit('sendAdoption','Nabídnout domov')
        ->setHtmlAttribute('class', 'btn btn-primary')  ;


        return $form;
    }



}