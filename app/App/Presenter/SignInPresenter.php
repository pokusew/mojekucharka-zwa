<?php

declare(strict_types=1);

namespace App\Presenter;

use Core\Forms\Controls\TextInput;
use Core\Forms\Form;

class SignInPresenter extends BasePresenter
{

	protected Form $signInForm;

	public function __construct()
	{
		$this->view = 'signIn';
	}

	public function action(): void
	{

		$this->signInForm = $this->createSignInForm();

		$this->signInForm->process($this->httpRequest);

	}

	private function createSignInForm(): Form
	{
		$form = new Form('signIn');

		$form->getElem()->data('validation', true);

		$form->addText('email', 'E-mail')
			->setType(TextInput::TYPE_EMAIL)
			->setRequired()
			// see https://stackoverflow.com/questions/53173806/what-should-be-correct-autocomplete-for-username-email
			->setAutocomplete('username')
			->setPlaceholder('E-mail');

		$form->addText('password', 'Heslo')
			->setType(TextInput::TYPE_PASSWORD)
			->setRequired()
			->setAutocomplete('current-password')
			->setPlaceholder('Heslo');

		$form->addSubmit('submit', 'Přihlásit se');

		$form->onSuccess[] = function (Form $form) {
			$this->handleSignInFormSuccess($form);
		};

		return $form;
	}

	private function handleSignInFormSuccess(Form $form): void
	{
		// dump('handleSignInFormSuccess', $form);
		// exit(0);
		$this->redirect('Home');
	}

}
