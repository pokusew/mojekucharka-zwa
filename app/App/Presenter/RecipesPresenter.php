<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Repository\UsersRepository;

class RecipesPresenter extends BasePresenter
{

	/** @inject */
	public UsersRepository $usersRepository;

	public function __construct()
	{
		$this->view = null;
	}

	public function action(): void
	{
		dump($this->usersRepository->findOneByEmail('some@value'));
	}

}
