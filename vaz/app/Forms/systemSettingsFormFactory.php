<?php

namespace App\Forms;

use Nette\Application\UI\Form;

class systemSettingsFormFactory extends Form
{
    public function create(): Form
    {
        $form = new Form();
        $form->addProtection();
        $form->addDateTime('relevantFrom','Platí od:')
            ->setDefaultValue(date('Y-m-d H:i:s'))
            ->setHtmlAttribute('class', 'form-control');

        $form->addInteger('fee','% poplatků:')
            ->setHtmlAttribute('class', 'form-control');

        $form->addInteger('dph','% dph:')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSelect('language','Jazyk:', ['cz' =>'Česky', 'sk' => 'Slovenčina', 'en' => 'English', 'ddr' => 'Deutsch', 'fr' => 'Français', 'ru' => 'Русский (Russkiy)', 'pl' => 'Polski'])
            ->setHtmlAttribute('class', 'form-control');

        $form->addSelect('payOutInterval', 'Interval výplat (v dnech):',[1 => 1, 14 => 14, 30 => 39, 60 => 60, 120 => 120])
            ->setHtmlAttribute('class', 'form-control');

        $form->addCheckbox('depricated',' - Neplatné')
            ->setHtmlAttribute('class', 'form-inline');

        $form->addCheckbox('cron',' - Cron pracuje')
            ->setHtmlAttribute('class', 'form-inline');

        $form->addCheckbox('analyticsGarbage', ' - Mazat analytics')
            ->setHtmlAttribute('class', 'form-inline');

        $form->addCheckbox('databaseClear', ' - Čistit DB')
            ->setHtmlAttribute('class', 'form-inline');

        $form->addCheckbox('dphUse',' - Počítat s DPH')
            ->setHtmlAttribute('class', 'form-inline');

        $form->addDateTime('nextPayOut','Další platby:')
            ->setHtmlAttribute('class', 'form-control');

        $form->addDateTime('lastPayOut','Poslední paltby:')
            ->setHtmlAttribute('class', 'form-control')
            ->setDisabled(true);

        $form->addSubmit('send','Uložit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        return $form;
    }

}