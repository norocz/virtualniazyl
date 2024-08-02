<?php
declare(strict_types=1);

namespace App\Forms;

use App\Presenters\UserPresenter;

interface userDetailsFormFactory
{
 public function create(UserPresenter $userPresenter): userDetailsForm;
}
