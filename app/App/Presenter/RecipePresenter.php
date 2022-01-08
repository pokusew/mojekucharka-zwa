<?php

declare(strict_types=1);

namespace App\Presenter;

class RecipePresenter extends BasePresenter
{

	public function __construct()
	{
		$this->view = null;
	}

	public function actionView(int $id): void
	{
		dump($id);
	}

}
