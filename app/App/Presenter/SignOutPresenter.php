<?php

declare(strict_types=1);

namespace App\Presenter;

use Core\Forms\Form;

class SignOutPresenter extends BasePresenter
{

	protected Form $signOutForm;

	public function action(): void
	{
		$this->view = 'signOut';

		$this->ensureUserLoggedIn();

		$this->signOutForm = $this->createSignOutForm();

		$this->signOutForm->process($this->httpRequest);
	}

}
