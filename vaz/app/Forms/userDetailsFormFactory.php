<?php
declare(strict_types=1);

namespace App\Forms;

use App\Model\Orm\Repository\CityRepository;
use App\Presenters\UserPresenter;
use Nepada\PhoneNumberInput\PhoneNumberInput;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;


class userDetailsFormFactory extends Form
{
    private CityRepository $cityRepository;
    public UserPresenter $presenter;

    public function __construct(CityRepository $cityRepository)
    {
        parent::__construct();
        $this->cityRepository = $cityRepository;



    }

    /**
     * @throws InvalidLinkException
     */
    public function create(UserPresenter $userPresenter): Form
    {
        $form = new Form;
        $form->addProtection('S formulářem nebo daty bylo manipulováno!');

        $form->addText('phone', 'Telefon:')
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(PhoneNumberInput::REGION, 'Prosím zadejte platný telefonní číslo. Pro ČR nebo SR začíná na +420 nebo +421.',['CZ', 'SK'])
            ->setCaption('Telefon:', 'form-control');

        $form->addText('firstName', 'Jméno:')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('lastName', 'Příjmení:')
            ->setHtmlAttribute('class', 'form-control');

        $form->addTextArea('description', 'Popis:')
            ->setOption('description', ' ')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('rows', 8)
            ->setHtmlAttribute('cols', 45);
        $form->addText('street', 'Ulice a číslo:')
            ->setHtmlAttribute('class', 'form-control');


        $country = $form->addSelect('country', 'Země:', $this->cityRepository->fetchCountries())
            ->setPrompt('Vyberte zemi')
            ->setHtmlAttribute('class', 'form-control ajax');

        $region = $form ->addSelect('region', 'Region:')
            ->setHtmlAttribute('class', 'form-control ajax')
            ->setPrompt('Vyberte region')

            ->setHtmlAttribute('data-depends', $country->getHtmlName())
            ->setHtmlAttribute('data-url', $userPresenter->link('Json:region', '#'));
        $form->onAnchor[] = fn() => $region->setItems($country->getValue() ? $this->cityRepository->findRegionByCountry($country->getValue()['0']) : []);

        $city = $form->addSelect('city','Město:')
            ->setHtmlAttribute('class', 'form-control ajax')
            ->setOption('description', ' ')
            ->setPrompt('Vyberte obec')

            ->setHtmlAttribute('data-depends', $region->getHtmlName())
            ->setHtmlAttribute('data-url', $userPresenter->link('Json:city', '#'));
        $form->onAnchor[] = fn() => $city->setItems($region->getValue() ? $this->cityRepository->findCityByRegion($region->getValue()['0']) : []);

        $form->addSubmit('send', 'Uložit')
            ->setHtmlAttribute('class', 'btn btn-success form-control');

        return $form;
    }
}