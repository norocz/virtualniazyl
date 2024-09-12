<?php

declare(strict_types=1);

namespace App\Components\Messenger;

use Doctrine\ORM\Mapping\PostPersist;
use Nette\Application\UI\Control;
use Nette\Application\Attributes\Persistent;

class messengerControlFactory extends Control
    {

        #[PostPersist]
        public int $id; // ID odesilatele
        public function __construct()
        {


        }

    public function render(): void
        {

        }

    }