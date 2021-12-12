<?php

declare(strict_types=1);

namespace App\Presenter;

use Core\Forms\Form;

class SignInPresenter extends BasePresenter
{

	private Form $signInForm;

	public function __construct()
	{
		$this->view = 'signIn';
	}

	public function action()
	{
		$this->signInForm = $this->createSignInForm();

		if ($this->signInForm->isSubmitted($this->httpRequest)) {
			$this->signInForm->process($this->httpRequest);
			dump($this->signInForm);
			exit(0);
		}

	}

	private function createSignInForm(): Form
	{
		$form = new Form();
		$form->addText('email');
		$form->onSuccess[] = [$this, 'handleSignInFormSuccess'];
		return $form;
	}

	private function handleSignInFormSuccess(Form $form)
	{

	}

}
