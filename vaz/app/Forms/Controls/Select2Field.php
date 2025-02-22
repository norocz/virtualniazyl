<?php

namespace App\Forms\Controls;

use Nette\Forms\Controls\SelectBox;

class Select2Field extends SelectBox
{
    public function __construct(string $label = null, array|string $dataSource = [])
    {
        parent::__construct($label);

        // Nastavení výchozích atributů pro Select2
        $this->setHtmlAttribute('class', 'select2');

        if (is_string($dataSource)) {
            // Pokud je zdroj string, předpokládáme AJAX URL
            $this->setHtmlAttribute('data-ajax-url', $dataSource);
        } elseif (is_array($dataSource)) {
            // Pokud je zdroj array, použijeme statická data
            $this->setItems($dataSource);
        }
    }
}
