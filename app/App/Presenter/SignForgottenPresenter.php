<?php

declare(strict_types=1);

namespace App\Presenter;

use Core\Forms\Controls\TextInput;
use Core\Forms\Form;

class SignForgottenPresenter extends BasePresenter
{

	protected Form $signForgottenForm;

	public function __construct()
	{
		$this->view = 'signForgotten';
	}

	public function action(): void
	{
		$this->signForgottenForm = $this->createSignForgottenForm();

		$this->signForgottenForm->process($this->httpRequest);
	}

	private function createSignForgottenForm(): Form
	{
		$form = new Form('signForgotten');

		$form->addText('email', 'E-mail')
			->setType(TextInput::TYPE_EMAIL)
			->setRequired()
			// see https://stackoverflow.com/questions/53173806/what-should-be-correct-autocomplete-for-username-email
			->setAutocomplete('username')
			->setPlaceholder('E-mail');

		$form->addSubmit('submit', 'Odeslat instrukce pro resetování hesla');

		$form->onSuccess[] = function (Form $form) {
			$this->handleSignForgottenFormSuccess($form);
		};

		return $form;
	}

	private function handleSignForgottenFormSuccess(Form $form): void
	{
		// dump('handleSignForgottenFormSuccess', $form);
		// exit(0);
		$this->redirect('Home');
	}

}
