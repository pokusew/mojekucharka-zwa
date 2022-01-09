<?php

declare(strict_types=1);

namespace App\Presenter;

use Core\Forms\Form;

class SignOutPresenter extends BasePresenter
{

	protected Form $signOutForm;

	public function __construct()
	{
		$this->view = 'signOut';
	}

	public function action(): void
	{
		$this->ensureUserLoggedIn();

		$this->signOutForm = $this->createSignOutForm();

		$this->signOutForm->process($this->httpRequest);
	}

	private function createSignOutForm(): Form
	{
		$form = new Form('signOut');

		$form->setAction($this->link('this'));

		$form->addSubmit('submit', 'Odhlásit se');

		$form->onSuccess[] = function (Form $form) {
			$this->handleSignOutFormSuccess($form);
		};

		return $form;
	}

	private function handleSignOutFormSuccess(Form $form): void
	{
		$this->session->delete('user');
		// TODO: add message (successful logout)
		$this->redirect('SignIn:');
	}

}
