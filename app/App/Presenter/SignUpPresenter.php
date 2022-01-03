<?php

declare(strict_types=1);

namespace App\Presenter;

use Core\Forms\Controls\TextInput;
use Core\Forms\Form;
use Nette\Mail\Mailer;
use Nette\Mail\Message;

class SignUpPresenter extends BasePresenter
{

	/** @inject */
	public Mailer $mailer;

	protected Form $signUpForm;

	public function __construct()
	{
		$this->view = 'signUp';
	}

	public function action(): void
	{
		$this->signUpForm = $this->createSignUpForm();

		$this->signUpForm->process($this->httpRequest);
	}

	private function createSignUpForm(): Form
	{
		$form = new Form('signUp');

		$form->addText('email', 'E-mail')
			->setType(TextInput::TYPE_EMAIL)
			->setRequired()
			// see https://stackoverflow.com/questions/53173806/what-should-be-correct-autocomplete-for-username-email
			->setAutocomplete('username')
			->setPlaceholder('E-mail');

		$form->addText('password', 'Heslo')
			->setType(TextInput::TYPE_PASSWORD)
			->setRequired()
			->setAutocomplete('new-password')
			->setPlaceholder('Heslo');

		$form->addText('passwordAgain', 'Heslo znovu')
			->setType(TextInput::TYPE_PASSWORD)
			->setRequired()
			->setAutocomplete('new-password')
			->setPlaceholder('Heslo');

		$form->addSubmit('submit', 'Registrovat se se');

		$form->onSuccess[] = function (Form $form) {
			$this->handleSignUpFormSuccess($form);
		};

		return $form;
	}

	private function handleSignUpFormSuccess(Form $form): void
	{
		$mail = new Message();
		$mail->setFrom($this->config->parameters['email.from'])
			->addTo($this->config->parameters['email.admin'])
			->setSubject('Registrace')
			->setBody('test');

		$this->mailer->send($mail);

		$this->redirect('Home');
	}

}
