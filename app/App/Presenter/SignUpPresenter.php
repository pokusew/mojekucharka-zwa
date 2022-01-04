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

		$form->addText('username', 'Uživatelské jméno')
			->setPlaceholder('Uživatelské jméno')
			->setRequired()
			->setMinLength(4)
			->setMaxLength(15)
			->addPattern(
				'^[A-Za-z0-9_]+$',
				'Uživatelské jméno může obsahovat jenom písmena (bez diakritiky), číslice a podtržítko ( _ ).',
			);

		$form->addText('email', 'E-mail')
			->setType(TextInput::TYPE_EMAIL)
			// see https://stackoverflow.com/questions/53173806/what-should-be-correct-autocomplete-for-username-email
			->setAutocomplete('email')
			->setPlaceholder('E-mail')
			->setRequired()
			// see https://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
			->setMaxLength(254);

		$form->addText('password', 'Heslo')
			->setType(TextInput::TYPE_PASSWORD)
			->setAutocomplete('new-password')
			->setPlaceholder('Heslo')
			->setRequired()
			->setMinLength(8)
			->setMaxLength(64)
			->addPattern('[0-9]', 'Heslo musí obsahovat alespoň jedno číslo.')
			->addPattern('\p{L}', 'Heslo musí obsahovat alespoň jedno písmeno.');

		// TODO: check that password === passwordAgain
		// Zadaná hesla se neshodují.
		// Hesla se neshodují.
		$form->addText('passwordAgain', 'Heslo znovu pro kontrolu')
			->setType(TextInput::TYPE_PASSWORD)
			->setAutocomplete('new-password')
			->setPlaceholder('Heslo');

		$form->addSubmit('submit', 'Zaregistrovat se');

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
