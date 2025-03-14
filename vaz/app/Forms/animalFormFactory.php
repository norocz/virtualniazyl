<?php
declare(strict_types=1);

namespace App\Forms;

use App\Repository\SpeciesRepository;
use Nette\Application\UI\Form;
use App\Model\Orm\Enums\AdoptionsTypeEnum;


class animalFormFactory extends Form
{
    private SpeciesRepository $speciesRepository;
    private AdoptionsTypeEnum $adoptionTypeEnum;

    public function __construct(SpeciesRepository $speciesRepository, AdoptionsTypeEnum $adoptionTypeEnum)
    {
        parent::__construct();
        $this->speciesRepository = $speciesRepository;
        $this->adoptionTypeEnum = $adoptionTypeEnum;

    }
    public function create(): Form
    {
        //$species = ['pes' => 'Pes', 'kočka' => 'Kočka', 'pták' => 'Pták', 'hlodavec' => 'Hlodavec', 'plaz' => 'Plaz', 'ryba' => 'Ryba', 'jiné' => 'Jiné'];
        $signType =  ['' => 'Neznámé','no' => 'Žádné', 'chip' => 'Čip','tattoo' => 'Tetování', 'both' => 'Čip i tetování'];
        $species = $this->speciesRepository->fetchPairs();
        $adoptionType = $this->adoptionTypeEnum->getAdoptionsTypesForm();

        $form = new Form;

        $renderer = $form->getRenderer();
        $renderer->wrappers['error']['container'] = 'ul class="text-warning"'; // Celý kontejner chyb
        $renderer->wrappers['error']['item'] = 'li'; // Každá chyba jako <li>

        $form->addText('name', 'Jméno zvířete:')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Zadejte prosím jméno.');
        $form->addTextArea('description', 'Popis:')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('rows', '7')
            ->setHtmlAttribute('cols', '20')
            ->setRequired('Zadejte prosím popis.');
        $form->addFloat('height','Výška (cm):')
            ->setHtmlAttribute('class', 'form-control');
        $form->addFloat('weight','Hmotnost (Kg):')
            ->setHtmlAttribute('class', 'form-control');
        $form->addSelect('species', 'Druh:', $species)
            ->setCaption('Druh:')
            ->setPrompt('Vyberte prosím druh')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Vyberte prosím druh.');
        $form->addDate('birthDate', 'Datum narození:')
            ->setHtmlAttribute('class', 'form-control');

        $form->addDate('reception', 'Datum přijetí:')
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(function ($reception) use ($form) {
                $values = $form->getUntrustedValues();

                if (!$values->birthDate || !$values->reception) {
                    return true; // Pokud některá z hodnot není nastavena, validaci přeskočíme
                }

                return $values->reception >= $values->birthDate;
            }, 'Datum přijetí nemůže být menší než datum narození.');

                $form->addText('breed', 'Plemeno:')
            ->setHtmlAttribute('class', 'form-control');
        $form->addMultiUpload('photos', 'Fotografie:')
            ->setHtmlAttribute('class', 'form-control');
        $form->addCheckbox('toAdoption', 'K adopci')
            ->setHtmlAttribute('class', 'form-check-input')
            ->setHtmlAttribute('id', 'toAdoptionCheckbox'); // Přidání ID pro JavaScript
        $form->addSelect('signed','Označení:', $signType)
            ->setCaption('Označení:')
            ->setHtmlAttribute('class', 'form-control');
        $form->addSelect('adoptionType', 'Typ adopce:', $adoptionType)
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('id', 'adoptionTypeSelect')
            ->setHtmlAttribute('disabled', true);

        $form->addCheckbox('multiAdoption','Přiznat více zvířat')
            ->setHtmlAttribute('class', 'form-check-input')
            ->setHtmlAttribute('id', 'multiAdoption'); // Přidání ID pro JavaScript

        $form->addInteger('howMuch','Kolik je zvířat k adopci:')
            ->setHtmlAttribute('class', 'form-control form-textarea')
            ->setDefaultValue('1')
            ->setNullable(false);


        $form->addSubmit('send', 'Uložit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        return $form;

    }
}