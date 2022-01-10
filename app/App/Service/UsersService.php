<?php

declare(strict_types=1);

namespace App\Service;

use App\Mail\MailGenerator;
use App\Repository\UserCreationException;
use App\Repository\UsersRepository;
use Core\Utils\Passwords;
use Core\Utils\Random;
use Nette\Mail\Mailer;

/**
 * A that handles user-related tasks, such as registration.
 */
class UsersService
{

	private UsersRepository $usersRepository;
	private Passwords $passwords;
	private MailGenerator $mailGenerator;
	private Mailer $mailer;

	public function __construct(
		UsersRepository $usersRepository,
		Passwords $passwords,
		MailGenerator $mailGenerator,
		Mailer $mailer
	)
	{
		$this->usersRepository = $usersRepository;
		$this->passwords = $passwords;
		$this->mailGenerator = $mailGenerator;
		$this->mailer = $mailer;
	}


	/**
	 * Registers a new user with the given parameters.
	 *
	 * Handles the complete registration flow.
	 * @param string $username
	 * @param string $email
	 * @param string $password raw password (hash will be calculated within this function)
	 * @param string $registrationIp the IPv4 or IPv6 address from which the registration request originated
	 * @throws UserRegistrationException
	 */
	public function registerUser(string $username, string $email, string $password, string $registrationIp): void
	{
		$passwordHash = $this->passwords->hash($password);
		$emailVerificationKey = Random::generateHexString();

		try {

			$this->usersRepository->createUser(
				$username,
				$email,
				$emailVerificationKey,
				$passwordHash,
				$registrationIp
			);

		} catch (UserCreationException $e) {

			if ($e->getCode() === UserCreationException::DUPLICATE_EMAIL) {
				$this->handleDuplicateEmailDuringRegistration($email);
				return;
			}

			if ($e->getCode() === UserCreationException::DUPLICATE_USERNAME) {
				throw new UserRegistrationException(
					$e->getMessage(),
					UserRegistrationException::DUPLICATE_USERNAME,
					$e,
				);
			}

			throw $e;

		}

		$mail = $this->mailGenerator
			->createFromTemplate('signUpSuccess', [
				'key' => $emailVerificationKey,
			])
			->addTo($email);

		$this->mailer->send($mail);
	}

	private function handleDuplicateEmailDuringRegistration(string $email): void
	{
		$user = $this->usersRepository->findOneByEmail($email, ['id', 'email_verification_key']);

		if ($user === null) {
			// this should not normally happen
			return;
		}

		$mail = $this->mailGenerator
			->createFromTemplate('signUpDuplicateEmail', [
				'key' => $user['email_verification_key'],
			])
			->addTo($email);

		$this->mailer->send($mail);
	}

	/**
	 * Changes user's password.
	 *
	 * @param int $userId
	 * @param string $currentPassword raw password (hash will be calculated within this function)
	 * @param string $newPassword raw password (hash will be calculated within this function)
	 * @param string $requestIp the IPv4 or IPv6 address from which the request originated
	 * @throws UserChangePasswordException
	 */
	public function changeUserPassword(
		int $userId,
		string $currentPassword,
		string $newPassword,
		string $requestIp
	): void
	{
		$user = $this->usersRepository->findOne(['id' => $userId], [
			'id',
			'email',
			'password',
		]);

		if ($user === null) {
			throw new UserChangePasswordException(
				"User with id '$user' not found.",
				UserChangePasswordException::USER_NOT_FOUND,
			);
		}

		if (!$this->passwords->verify($currentPassword, $user['password'])) {
			throw new UserChangePasswordException(
				"Invalid current password.",
				UserChangePasswordException::INVALID_CURRENT_PASSWORD,
			);
		}

		$newPasswordHash = $this->passwords->hash($newPassword);

		$this->usersRepository->changePassword($userId, $newPasswordHash, $requestIp);

		$mail = $this->mailGenerator
			->createFromTemplate('passwordChangedNotification', [])
			->addTo($user['email']);

		$this->mailer->send($mail);
	}

	/**
	 * Generates a new password reset key for a user with the given email.
	 *
	 * @param string $email the user's email
	 * @param string $requestIp the IPv4 or IPv6 address from which the request originated
	 * @return bool
	 */
	public function createAndSendPasswordResetKey(
		string $email,
		string $requestIp
	): bool
	{
		$passwordResetKey = Random::generateHexString();

		if (!$this->usersRepository->setPasswordResetKey($email, $passwordResetKey, $requestIp)) {
			return false;
		}

		$mail = $this->mailGenerator
			->createFromTemplate('passwordReset', [
				'key' => $passwordResetKey,
			])
			->addTo($email);

		$this->mailer->send($mail);

		return true;
	}

	/**
	 * Resets user's password.
	 *
	 * @param string $email the user's email address (so we don't need to issue another SELECT)
	 * @param string $passwordResetKey
	 * @param string $newPassword raw password (hash will be calculated within this function)
	 * @param string $requestIp the IPv4 or IPv6 address from which the request originated
	 * @return bool `true` on success, `false` otherwise
	 */
	public function resetUserPassword(
		string $email,
		string $passwordResetKey,
		string $newPassword,
		string $requestIp
	): bool
	{
		$newPasswordHash = $this->passwords->hash($newPassword);

		if (!$this->usersRepository->resetPassword($passwordResetKey, $newPasswordHash, $requestIp)) {
			return false;
		}

		$mail = $this->mailGenerator
			->createFromTemplate('passwordChangedNotification', [])
			->addTo($email);

		$this->mailer->send($mail);

		return true;
	}

}
