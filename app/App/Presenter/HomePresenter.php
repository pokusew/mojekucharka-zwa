<?php

declare(strict_types=1);

namespace App\Presenter;

class HomePresenter extends BasePresenter
{

	public function action(): void
	{
		$this->view = 'home';
	}

}
