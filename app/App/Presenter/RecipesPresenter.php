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
		if ($this->isUserLoggedIn()) {
			dump($this->getUser());
		} else {
			dump('user not logged in');
		}
		dump($this->usersRepository->findOneByEmailOrUsername('pokusew@seznam.cz', [
			'id',
			'username',
			'registered_from_ip' => 'INET6_NTOA(registered_from_ip)',
		]));
	}

}
