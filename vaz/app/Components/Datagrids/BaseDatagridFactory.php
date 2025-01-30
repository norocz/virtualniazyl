<?php

declare(strict_types=1);

namespace App\Components\Datagrids;

use Nette\Application\UI\Presenter;
use Ublaboo\DataGrid\DataGrid;

class BaseDatagridFactory extends DataGrid
{

    protected ?Presenter $presenter = null;

    public function setPresenter(Presenter $presenter): void
    {
        $this->presenter = $presenter;
    }

}