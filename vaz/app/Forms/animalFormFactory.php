<?php
declare(strict_types=1);

namespace App\Forms;

use App\Repository\SpeciesRepository;
use Nette\Application\UI\Form;


class animalFormFactory extends Form
{
    private SpeciesRepository $speciesRepository;

    public function __construct(SpeciesRepository $speciesRepository)
    {
        parent::__construct();
        $this->speciesRepository = $speciesRepository;
    }
    public function create(): Form
    {
        //$species = ['pes' => 'Pes', 'kočka' => 'Kočka', 'pták' => 'Pták', 'hlodavec' => 'Hlodavec', 'plaz' => 'Plaz', 'ryba' => 'Ryba', 'jiné' => 'Jiné'];

        $species = $this->speciesRepository->fetchPairs();

        $form = new Form;
        $form->addText('name', 'Jméno:')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Zadejte prosím jméno.');
        $form->addTextArea('description', 'Popis:')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('rows', '7')
            ->setHtmlAttribute('cols', '20')
            ->setRequired('Zadejte prosím popis.');
        $form->addSelect('species', 'Druh:', $species)
            ->setCaption('Druh:')
            ->setPrompt('Vyberte prosím druh')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Vyberte prosím druh.');
        $form->addDate('birthDate', 'Datum narození:')
            ->setHtmlAttribute('class', 'form-control');
        $form->addText('breed', 'Plemeno:')
            ->setHtmlAttribute('class', 'form-control');
        $form->addMultiUpload('photos', 'Fotografie:')
            ->setHtmlAttribute('class', 'form-control');
        $form->addCheckbox('toAdoption', ': k adopci')
            ->setHtmlAttribute('class', 'form-check-input');

        $form->addSubmit('send', 'Uložit')
            ->setHtmlAttribute('class', 'btn btn-primary');

        return $form;

    }
}