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

}
