<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Service\UserRegistrationException;
use App\Service\UsersService;
use Core\Forms\Controls\TextInput;
use Core\Forms\Form;

class SignUpPresenter extends BasePresenter
{

	/** @inject */
	public UsersService $usersService;

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
		/** @var TextInput */
		$username = $form['username'];
		/** @var TextInput */
		$email = $form['email'];
		/** @var TextInput */
		$password = $form['password'];

		try {
			$this->usersService->registerUser(
				$username->getValue(),
				$email->getValue(),
				$password->getValue(),
				$this->httpRequest->remoteAddress,
			);
		} catch (UserRegistrationException $e) {

			if ($e->getCode() === UserRegistrationException::DUPLICATE_USERNAME) {
				$msg = 'Toto uživatelské jméno je již používá jiný uživatel. Prosím zvolte si jiné.';
				$username->setError($msg);
				$username->getElem()->attrs['data-invalid'] = $username->getValue();
				$username->getElem()->attrs['data-invalid-msg'] = $msg;
				return;
			}

			// should never happen, but if it did, the app error handler will handle it
			throw $e;

		}

		// TODO: show success page
		$this->redirect('Home');
	}

}
