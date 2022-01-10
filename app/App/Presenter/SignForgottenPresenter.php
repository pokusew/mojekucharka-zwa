<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Service\UsersService;
use Core\Forms\Controls\TextInput;
use Core\Forms\Form;

class SignForgottenPresenter extends BasePresenter
{

	/** @inject */
	public UsersService $usersService;

	protected Form $signForgottenForm;

	public function action(): void
	{
		$this->view = 'signForgotten';

		$this->signForgottenForm = $this->createSignForgottenForm();

		$this->signForgottenForm->process($this->httpRequest);
	}

	public function actionSuccess(): void
	{
		$this->view = 'signForgotten-success';
	}

	private function createSignForgottenForm(): Form
	{
		$form = new Form('signForgotten');

		$form->setAction($this->link('this'));

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
		/** @var TextInput $email */
		$email = $form['email'];

		// we do not care about the result as we do not want to leak the info
		// if such an e-mail is associated to an existing user account
		$this->usersService->createAndSendPasswordResetKey($email->getValue(), $this->httpRequest->remoteAddress);

		$this->redirect('SignForgotten:success');
	}

}
