<?php

namespace App\Forms;

use App\Forms\Controls\Select2Field;
use Nette\Application\UI\Form;

trait Select2Trait
{
    public function addSelect2(string $name, string $label = null, array|string $dataSource = []): Select2Field
    {
        $control = new Select2Field($label, $dataSource);
        $this->addComponent($control, $name);
        return $control;
    }
}