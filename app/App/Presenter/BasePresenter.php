<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Security\SessionUser;
use Core\Exceptions\InvalidStateException;
use Core\Http\Session;
use Core\UI\Presenter;

abstract class BasePresenter extends Presenter
{

	protected ?string $templatesDir = __DIR__ . '/../templates';
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

}
