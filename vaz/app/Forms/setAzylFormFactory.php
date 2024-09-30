<?php

declare(strict_types=1);

namespace App\Forms;

use Nette\Application\UI\Form;
use App\Model\Orm\Repository\AzylRepository;

class setAzylFormFactory extends Form
{
    private AzylRepository $azylRepository;
    public function __construct(AzylRepository $azylRepository)
    {
        parent::__construct();
        $this->azylRepository = $azylRepository;
    }
    public function create(): Form
    {
        $form = new Form;
        $form->addSelect('azyl','Vyberte azyl', $this->azylRepository->fetchPairs());
        $form->addSubmit('send', 'Nastavit azyl');

            return $form;
    }
}