<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Limits;
use App\RecipesFilter;
use App\Security\SessionUser;
use Core\Exceptions\InvalidStateException;
use Core\Forms\Controls\TextInput;
use Core\Forms\Form;
use Core\Http\Session;
use Core\UI\Presenter;

abstract class BasePresenter extends Presenter
{

	protected ?string $templatesDir = __DIR__ . '/templates';
	protected ?string $layout = '_layout';

	/** @inject */
	public Session $session;

	/**
	 * Checks if a user is logged in
	 */
	protected function isUserLoggedIn(): bool
	{
		return $this->session->has('user');
	}

	/**
	 * Ensures a user is logged in.
	 *
	 * If no user is logged in, this method will not return
	 * and instead it will do redirect to the sign in page.
	 */
	protected function ensureUserLoggedIn(): void
	{
		if (!$this->isUserLoggedIn()) {
			// TODO: add reason, backUrl
			$this->redirect('SignIn:');
		}
	}

	/**
	 * Gets the currently logged in user.
	 *
	 * If no user is logged in, this method will not return
	 * and instead it will do redirect to the sign in page.
	 *
	 * @return SessionUser always returns the instance
	 */
	protected function getUser(): SessionUser
	{
		$this->ensureUserLoggedIn();

		$user = $this->session->get('user');

		if (!($user instanceof SessionUser)) {
			$type = gettype($user);
			$expected = SessionUser::class;
			throw new InvalidStateException(
				"Unexpected value of type '$type' for user key in session. Expected type '$expected'."
			);
		}

		return $user;
	}

	protected function createSignOutForm(): Form
	{
		$form = new Form('signOut');

		$form->setAction($this->link('SignOut:'));

		$form->addSubmit('submit', 'Odhlásit se');

		$form->onSuccess[] = function (Form $form) {
			$this->handleSignOutFormSuccess($form);
		};

		return $form;
	}

	private function handleSignOutFormSuccess(Form $form): void
	{
		$this->session->delete('user');
		// TODO: add message (successful logout)
		$this->redirect('SignIn:');
	}

	/**
	 * Adds standard new password fields with full-featured validation.
	 *
	 * Designed to be uses throughout the app in signUp, passwordReset, and passwordChange forms.
	 *
	 * @param Form $form
	 * @param string $passwordName
	 * @param string $passwordAgainName
	 * @return void
	 */
	public function addNewPasswordWithConfirmationToForm(
		Form $form,
		string $passwordName = 'password',
		string $passwordAgainName = 'passwordAgain'
	)
	{
		$password = $form->addText($passwordName, 'Heslo')
			->setType(TextInput::TYPE_PASSWORD)
			->setAutocomplete('new-password')
			->setPlaceholder('Heslo')
			->setRequired()
			->setMinLength(Limits::PASSWORD_MIN_LENGTH)
			->setMaxLength(Limits::PASSWORD_MAX_LENGTH)
			->addPattern('[0-9]', 'Heslo musí obsahovat alespoň jedno číslo.')
			->addPattern('\p{L}', 'Heslo musí obsahovat alespoň jedno písmeno.');

		$passwordAgain = $form->addText($passwordAgainName, 'Heslo znovu pro kontrolu')
			->setType(TextInput::TYPE_PASSWORD)
			->setAutocomplete('new-password')
			->setPlaceholder('Heslo');

		$password->getElem()
			->setAttribute('data-trigger-validation', $passwordAgain->getElem()->name);

		$passwordAgain->getElem()
			->setAttribute('data-equal-to', $password->getElem()->name)
			->setAttribute('data-equal-to-msg', 'Zadaná hesla se neshodují.');

		$form->customValidators[] = function (Form $form) use ($passwordName, $passwordAgainName) {
			/** @var TextInput $password */
			$password = $form[$passwordName];
			/** @var TextInput $passwordAgain */
			$passwordAgain = $form[$passwordAgainName];

			if ($password->getValue() !== $passwordAgain->getValue()) {
				$passwordAgain->setError('Zadaná hesla se neshodují.');
				return false;
			}

			return true;
		};
	}

	public function defaultRecipesLink(): string
	{
		if ($this->isUserLoggedIn()) {
			return $this->link(
				'Recipes:',
				null,
				false,
				RecipesFilter::DEFAULT_LOGGED_IN_QUERY,
			);
		} else {
			return $this->link(
				'Recipes:',
				null,
				false,
				RecipesFilter::DEFAULT_NOT_LOGGED_IN_QUERY,
			);
		}
	}

	/**
	 * @return never
	 */
	public function defaultRecipesRedirect()
	{
		if ($this->isUserLoggedIn()) {
			$this->redirect(
				'Recipes:',
				null,
				false,
				RecipesFilter::DEFAULT_LOGGED_IN_QUERY,
			);
		} else {
			$this->redirect(
				'Recipes:',
				null,
				false,
				RecipesFilter::DEFAULT_NOT_LOGGED_IN_QUERY,
			);
		}
	}

}
