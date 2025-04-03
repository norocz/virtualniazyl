<?php
declare(strict_types = 1);
namespace App\Forms;





use Nette;
use Nette\Application\UI\Form;
use App\Model\Orm\Enums\SexTypeEnum;

class speciesFormFactory extends Form
{
    private SexTypeEnum $sexTypeEnum;
    public function __construct(SexTypeEnum $sexTypeEnum)
    {
        parent::__construct();
        $this->sexTypeEnum = $sexTypeEnum;

    }

    public function create(): Form
    {
        $form = new Form;
        $form->addText('name', 'Druh')
             ->setHtmlAttribute('placeholder', 'Například fenka')
             ->setHtmlAttribute('class', 'form-control')
             ->setRequired('Zadejte jméno druhu');
        $form->addText('description', 'Popis')
            ->setHtmlAttribute('placeholder', 'Například  ')
            ->setHtmlAttribute('class', 'form-control');
        $form->addTextArea('tags', 'Tagy')
            ->setHtmlAttribute('placeholder', 'Tagy co se přidají takže různé verze toho   ')
            ->setHtmlAttribute('class', 'form-control');
        $form->addSelect('sex', 'Pohlaví', $this->sexTypeEnum->getSexTypesForm())
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Zadejte pohlaví');
        $form->addSubmit('send', 'Vložit');

        return $form;
    }
}
