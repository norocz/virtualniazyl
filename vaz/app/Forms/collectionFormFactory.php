<?php
declare(strict_types=1);

namespace App\Forms;

use Nette\Application\UI\Form;
use Nette\Utils\Html;

class CollectionFormFactory
{
    public function create(): Form
    {
        $form = new Form();
        $form->addProtection();
        $form->addText('collectionName', 'Název:')
            ->setHtmlAttribute('class', 'form-control');
        $form->addTextArea('collectionDescription', 'Podrobnosti:')
            ->setHtmlAttribute('rows', 10)
            ->setHtmlAttribute('cols', 70)
            ->setHtmlAttribute('id', 'collectionDescription');
        $form->addInteger('minimalAmount','Minimální výběr:')
            ->setHtmlAttribute('class', 'form-inline form-control');
        $form->addInteger('resultAmount', 'Cílová částka:')
            ->setHtmlAttribute('class', 'form-inline form-control');
        $form->addInteger('extendedAmount', 'Nová cílová částka:')
            ->setHtmlAttribute('class', 'form-inline form-control');
        $form->addDate('startAt','Začátek sbírky:')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Začátek sbírky');
        $form->addDate('endingAt', 'Konec sbírky:')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Konec sbírky sbírky');
        $form->addDate('extendTo','Prodloužit do:')
            ->setHtmlAttribute('class', 'form-control');
        $form->addSelect('currency','Měna:', ['czk' => 'Kč','eur' => 'EU', 'usd' => 'USD', 'ru' => 'Рубль (Rublʹ)', 'zl' => 'Złoty'])
        ->setHtmlAttribute('class', 'form-control')
        ->setDefaultValue('czk');
        $form->addCheckbox('extend','  Prodloužit?')
            ->setHtmlAttribute('role', 'switch')
        ->setHtmlAttribute('class', 'form-check-input');
        $form->addCheckbox('isActive','  Aktivní?')
            ->setHtmlAttribute('class', 'form-check-input')
            ->setHtmlAttribute('role', 'switch')
        ->setDefaultValue(true);
        $form->addUpload('headline',Html::el()->setHtml('Obrázek do hlavičky<br> (ideální rozměr 1100x300px):'))
        ->setHtmlAttribute('class', 'form-control');
        $form->addSubmit('send','Přidat sbírku');

        return $form;
    }
}