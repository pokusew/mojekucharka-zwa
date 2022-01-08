<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Limits;
use App\Repository\UsersRepository;
use Core\Forms\Controls\TextInput;
use Core\Forms\Form;
use Core\Utils\Passwords;

class SignInPresenter extends BasePresenter
{

	/** @inject */
	public Passwords $passwords;

	/** @inject */
	public UsersRepository $usersRepository;

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

	public function actionEmailNotVerified(): void
	{
		$this->view = 'signIn-emailNotVerified';
	}

	private function createSignInForm(): Form
	{
		$form = new Form('signIn');

		$form->addText('usernameOrEmail', 'Uživatelské jméno nebo e-mail')
			->setRequired()
			// see https://stackoverflow.com/questions/53173806/what-should-be-correct-autocomplete-for-username-email
			->setAutocomplete('username')
			->setPlaceholder('Uživatelské jméno nebo e-mail')
			->setMaxLength(max(Limits::USERNAME_MAX_LENGTH, Limits::EMAIL_MAX_LENGTH));

		$form->addText('password', 'Heslo')
			->setType(TextInput::TYPE_PASSWORD)
			->setAutocomplete('current-password')
			->setPlaceholder('Heslo')
			->setRequired()
			->setMaxLength(Limits::PASSWORD_MAX_LENGTH);

		$form->addSubmit('submit', 'Přihlásit se');

		$form->onSuccess[] = function (Form $form) {
			$this->handleSignInFormSuccess($form);
		};

		return $form;
	}

	private function handleSignInFormSuccess(Form $form): void
	{
		/** @var TextInput */
		$usernameOrEmail = $form['usernameOrEmail'];
		/** @var TextInput */
		$password = $form['password'];

		$user = $this->usersRepository->findOneByEmailOrUsername($usernameOrEmail->getValue(), [
			'id',
			'name',
			'email_verified_at',
			'password',
		]);

		// TODO: add protection against timing attack
		//       (User could find out whether email/username is valid
		//        by measuring response times as password validation takes some measurable time.)

		if ($user === null) {
			$form->setError('Neplatné přihlašovácí údaje.');
			return;
		}

		if ($user['email_verified_at'] === null) {
			$this->redirect('SignIn:emailNotVerified');
		}

		if (!$this->passwords->verify($password->getValue(), $user['password'])) {
			$form->setError('Neplatné přihlašovácí údaje.');
			return;
		}

		// TODO: store user login in session

		$this->redirect('Recipes:');
	}

}
