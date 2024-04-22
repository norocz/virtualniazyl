<?php
declare(strict_types=1);

namespace App\Forms;

use App\Model\Orm\Entity\Citys;
use App\Model\Orm\Repository\CityRepository;
use App\Presenters\JsonPresenter;
use Nette\Application\UI\Form;

class userDetailsFormFactory extends Form
{

    private CityRepository $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        parent::__construct();

        $this->cityRepository = $cityRepository;

    }

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
        $form->addSelect('country', 'Země', $this->cityRepository->fetchCountries())
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Vyberte zemi');
        $form->addSelect('region', 'Kraj', ['Hlavní město Praha', 'Jihočeský', 'Jihomoravský', 'Karlovarský', 'Královéhradecký', 'Liberecký', 'Moravskoslezský', 'Olomoucký', 'Pardubický', 'Plzeňský', 'Středočeský', 'Ústecký', 'Vysočina', 'Zlínský'])
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Zadejte kraj');
        $form->addSelect('city','Město', ['Praha', 'Brno', 'Ostrava', 'Plzeň', 'Liberec', 'Olomouc', 'České Budějovice', 'Hradec Králové', 'Ústí nad Labem', 'Pardubice', 'Zlín', 'Hradec Králové', 'Karlovy Vary', 'Jihlava', 'Tábor', 'Žďár nad Sázavou', 'Jindřichův Hradec', 'Příbram', 'Kolín', 'Kutná Hora', 'Pelhřimov', 'Třebíč'])
            ->setHtmlAttribute('class', 'form-control')
            ->setOption('description', ' ')
            ->setRequired('Zadejte město');

        $form->addSubmit('sendPersoneInfo', 'Uložit')
            ->setHtmlAttribute('class', 'btn btn-success form-control');
        return $form;
    }

    /*
     * Tady je to vyhazování měst obcí a podobně.
     * $city = $form->addSelect('city', 'Obec:') //$city  z $region

             ->setRequired()
             ->setPrompt('Nejprve vyberte okres')
             ->setHtmlAttribute('class', 'form-control col-md-3 form-select')
             ->setHtmlAttribute('data-depends', 'region')
             ->setHtmlAttribute('data-url', $this->link('home:cities', '#'));
        $form ->onAnchor[] = fn() => $city->setItems($region->getValue()
               ? $this->locale->getCityFromRegionForm($region->getValue())
               : []);
     *
     *
     *
     *
     */
}