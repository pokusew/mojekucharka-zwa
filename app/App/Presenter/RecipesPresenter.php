<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Repository\CategoriesRepository;
use App\Repository\UsersRepository;

class RecipesPresenter extends BasePresenter
{

	/** @inject */
	public UsersRepository $usersRepository;

	/** @inject */
	public CategoriesRepository $categoriesRepository;

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
		dump(
			$this->categoriesRepository->findAllNested()
		);
	}

}
