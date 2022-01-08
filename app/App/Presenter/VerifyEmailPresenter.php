<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Repository\UsersRepository;

class VerifyEmailPresenter extends BasePresenter
{

	/** @inject */
	public UsersRepository $usersRepository;

	protected bool $success = false;

	public function __construct()
	{
		$this->view = 'verifyEmail';
	}

	public function action(string $key): void
	{
		$this->success = $this->usersRepository->verifyEmail($key, $this->httpRequest->remoteAddress);
	}

}
