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

		$form->addText('usernameOrEmail', 'Uživatelské jméno nebo e-mail')
			->setRequired()
			// see https://stackoverflow.com/questions/53173806/what-should-be-correct-autocomplete-for-username-email
			->setAutocomplete('username')
			->setPlaceholder('Uživatelské jméno nebo e-mail')
			// see https://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
			->setMaxLength(254);

		$form->addText('password', 'Heslo')
			->setType(TextInput::TYPE_PASSWORD)
			->setAutocomplete('current-password')
			->setPlaceholder('Heslo')
			->setRequired()
			->setMaxLength(64);

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
