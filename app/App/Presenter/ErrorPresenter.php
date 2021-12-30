<?php

declare(strict_types=1);

namespace App\Presenter;

class ErrorPresenter extends BasePresenter
{

	public function __construct()
	{
		$this->view = 'error-404';
	}

	public function action(): void
	{
		// TODO: Implement action() method.
	}

}
