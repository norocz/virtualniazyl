<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Model\Services\Menu;
use Nette;
use App\Model\VersionService;


/**
 * Handles 4xx HTTP error responses.
 */
final class Error4xxPresenter extends Nette\Application\UI\Presenter
{
    private VersionService $versionService;

    public function __construct(VersionService $versionService)
    {
        parent::__construct();
        $this->versionService = $versionService;

    }

    protected function checkHttpMethod(): void
	{
		// allow access via all HTTP methods and ensure the request is a forward (internal redirect)
		if (!$this->getRequest()->isMethod(Nette\Application\Request::FORWARD)) {
			$this->error();
		}
	}


	public function renderDefault(Nette\Application\BadRequestException $exception): void
	{
        $menu = new Menu();
		// renders the appropriate error template based on the HTTP status code
		$code = $exception->getCode();
        $this->getTemplate()->version = $this->versionService->getLastVersion();
		$file = is_file($file = __DIR__ . "/templates/Error/$code.latte")
			? $file
			: __DIR__ . '/templates/Error/4xx.latte';
		$this->getTemplate()->httpCode = $code;
		$this->getTemplate()->setFile($file);
        $this->getTemplate()->mainMenuItems = $menu->getMenu();
	}
}
