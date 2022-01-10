<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Repository\UsersRepository;

class VerifyEmailPresenter extends BasePresenter
{

	/** @inject */
	public UsersRepository $usersRepository;

	protected bool $success = false;

	public function action(string $key): void
	{
		$this->view = 'verifyEmail';

		$this->success = $this->usersRepository->verifyEmail($key, $this->httpRequest->remoteAddress);
	}

}
