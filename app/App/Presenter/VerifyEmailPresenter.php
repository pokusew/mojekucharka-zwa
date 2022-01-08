<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Service\UsersService;

class VerifyEmailPresenter extends BasePresenter
{

	/** @inject */
	public UsersService $usersService;

	public function __construct()
	{
		$this->view = null;
	}

	public function action(string $key): void
	{
		dump($key);
	}

}
