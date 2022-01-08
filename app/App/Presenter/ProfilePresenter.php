<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Repository\UsersRepository;
use Core\Exceptions\BadRequestException;

class ProfilePresenter extends BasePresenter
{

	/** @inject */
	public UsersRepository $usersRepository;

	/**
	 * @var array<string, mixed>
	 */
	protected $user;

	public function __construct()
	{
		$this->view = 'profile';
	}

	public function actionView(string $username): void
	{
		$user = $this->usersRepository->findOneByUsername($username);

		if ($user === null) {
			throw new BadRequestException("User with username '$username' not found.", 404);
		}

		$this->user = $user;
	}

}
