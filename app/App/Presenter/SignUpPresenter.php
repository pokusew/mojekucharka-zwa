<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Limits;
use App\Service\UserRegistrationException;
use App\Service\UsersService;
use Core\Forms\Controls\TextInput;
use Core\Forms\Form;

class SignUpPresenter extends BasePresenter
{

	/** @inject */
	public UsersService $usersService;

	protected Form $signUpForm;

	public function action(): void
	{
		$this->view = 'signUp';

		$this->signUpForm = $this->createSignUpForm();

		$this->signUpForm->process($this->httpRequest);
	}

	public function actionSuccess(): void
	{
		$this->view = 'signUp-success';
	}

	private function createSignUpForm(): Form
	{
		$form = new Form('signUp');

		$form->setAction($this->link('this'));

		$form->addText('username', 'Uživatelské jméno')
			->setPlaceholder('Uživatelské jméno')
			->setRequired()
			->setMinLength(Limits::USERNAME_MIN_LENGTH)
			->setMaxLength(Limits::USERNAME_MAX_LENGTH)
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
			->setMaxLength(Limits::EMAIL_MAX_LENGTH);

		$this->addNewPasswordWithConfirmationToForm($form);

		$form->addSubmit('submit', 'Zaregistrovat se');

		$form->onSuccess[] = function (Form $form) {
			$this->handleSignUpFormSuccess($form);
		};

		return $form;
	}

	private function handleSignUpFormSuccess(Form $form): void
	{
		/** @var TextInput $username */
		$username = $form['username'];
		/** @var TextInput $email */
		$email = $form['email'];
		/** @var TextInput $password */
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

		$this->redirect('SignUp:success');
	}

}
