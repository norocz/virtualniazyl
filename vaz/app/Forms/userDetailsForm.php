<?php
declare(strict_types=1);

namespace App\Forms;


use App\Model\Orm\Repository\CityRepository;
use App\Presenters\UserPresenter;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;

class userDetailsForm extends Form
{
    private CityRepository $cityRepository;
    private UserPresenter $userPresenter;

    public function __construct(CityRepository $cityRepository, UserPresenter $userPresenter)
    {
        parent::__construct();
        $this->cityRepository = $cityRepository;
        $this->userPresenter = $userPresenter;
    }

    /**
     * @throws InvalidLinkException
     */
    public function create(): Form
    {
        $form = new Form;
        $form->addText('firstName', 'Jméno')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Zadejte jméno');
        $form->addText('lastName', 'Příjmení')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Zadejte příjmení');
        $form->addTextArea('description', 'Popis')
            ->setOption('description', ' ')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('rows', 8)
            ->setHtmlAttribute('cols', 45);
        $form->addText('address', 'Adresa')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Zadejte adresu');

        $country = $form->addSelect('country', 'Země', $this->cityRepository->fetchCountries())
            ->setPrompt('Vyberte zemi')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Vyberte zemi');

        $region = $form ->addSelect('region', 'Region:')
            ->setHtmlAttribute('class', 'form-control')
            ->setPrompt('Vyberte region')
            ->setRequired('Vyberte region')
            ->setHtmlAttribute('data-depends', 'region')
            ->setHtmlAttribute('data-url', $this->userPresenter->link('Json:region', '#'));
        $form->onAnchor[] = fn() => $region->setItems($country->getValue() ? $this->cityRepository->findRegionByCountry($country->getValue()) : []);

        $city = $form->addSelect('city','Město:')
            ->setHtmlAttribute('class', 'form-control')
            ->setOption('description', ' ')
            ->setRequired('Zadejte město')
            ->setHtmlAttribute('data-depends', 'city')
            ->setHtmlAttribute('data-url', $this->userPresenter->link('Json:city', '#'));
        $form->onAnchor[] = fn() => $city->setItems($region->getValue() ? $this->cityRepository->findCityByCountry($region->getValue()) : []);

        $form->addSubmit('sendPersoneInfo', 'Uložit')
            ->setHtmlAttribute('class', 'btn btn-success form-control');

        return $form;
    }
}