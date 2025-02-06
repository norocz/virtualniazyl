<?php
declare(strict_types=1);

namespace App\Forms;

use Nette\Application\UI\Form;
use Nette\Utils\Html;

class paymentFormFactory extends Form
{
    private int $minimalAmount=50; //minimální hodnota příhozu
    private ?int $collectionKey = null; //kam to má padat
    private string $currency = 'Kč'; //Jaká měna



    public function setMinimalAmount(int $minimalAmount): void
    {
        $this->minimalAmount = $minimalAmount;
    }
    private function getMinimalAmount(): int
    {
        return $this->minimalAmount;
    }

    public function setCollctionKey(int $collctionKey): void
    {
        $this->collectionKey += $collctionKey;
    }

    private function getCollctionKey(): ?int
    {
        return $this->collectionKey;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    private function getCurrency(): string
    {
        return $this->currency;
    }

    public function create(): Form
    {
        $form = new Form();
        $form->addProtection();
        $form->addInteger('pay',Html::el()->setHtml('Částka:<br> (minimální hodnota'.$this->getMinimalAmount().$this->getCurrency().'):'))
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired()
            ->addRule($form::Min($this->getMinimalAmount()));
        $form->addSubmit('send');
        $form->onSuccess[] = [$this, 'paymentFormSuccess', $this->getCollctionKey()];

        return $form;
    }


// Html::el()->setHtml('Obrázek do hlavičky<br> (ideální rozměr 1100x300px):')
}