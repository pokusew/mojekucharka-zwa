<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\UserCreationException;
use PDO;
use PDOException;

class UsersRepository extends Repository
{

	/**
	 * Tries to create a new user with the given parameters.
	 * @param string $registrationIp the IPv4 or IPv6 address from which the registration request originated
	 * @return int id of the created user
	 * @throws UserCreationException when the user could not be created due to the
	 *                               {@see UserCreationException::DUPLICATE_USERNAME}
	 *                               or {@see UserCreationException::DUPLICATE_EMAIL}
	 */
	public function createUser(
		string $username,
		string $email,
		string $emailVerificationKey,
		string $passwordHash,
		string $registrationIp
	): int
	{
		try {

			$dbh = $this->connection->get();

			$sth = $dbh->prepare(<<<'SQL'
				INSERT INTO users (
					username,
					email,
					email_verification_key,
					email_verification_key_created_at,
					password,
					registered_at,
					registered_from_ip
				)
				VALUES
					(
						:username,
						:email,
						:emailVerificationKey,
						NOW(),
						:password,
						NOW(),
						INET6_ATON(:registrationIp)
					)
				;
			SQL
			);

			$sth->execute([
				'username' => $username,
				'email' => $email,
				'emailVerificationKey' => $emailVerificationKey,
				'password' => $passwordHash,
				'registrationIp' => $registrationIp,
			]);

			return (int) $dbh->lastInsertId();

		} catch (PDOException $e) {

			// see https://dev.mysql.com/doc/connector-j/8.0/en/connector-j-reference-error-sqlstates.html
			// SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'someValue' for key 'users.username'
			// SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'some@value' for key 'users.email'

			if ($e->errorInfo[0] === '23000' && strpos($e->errorInfo[2], "for key 'users.username'") !== false) {
				throw new UserCreationException(
					"The username '$username' is already taken.",
					UserCreationException::DUPLICATE_USERNAME,
				);
			} else if ($e->errorInfo[0] === '23000' && strpos($e->errorInfo[2], "for key 'users.email'") !== false) {
				throw new UserCreationException(
					"The e-mail '$email' is already used by another user.",
					UserCreationException::DUPLICATE_EMAIL,
				);
			} else {
				// let the core app handle the exception
				throw $e;
			}

		}
	}

	/**
	 * @param string $email
	 * @return array<string, mixed>|null
	 */
	public function findOneByEmail(string $email): ?array
	{

		$dbh = $this->connection->get();

		$sth = $dbh->prepare(<<<'SQL'
			SELECT * FROM users WHERE email = :email LIMIT 1
		SQL
		);

		$sth->execute([
			'email' => $email,
		]);

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		if ($result === false) {
			return null;
		}

		return $result;

	}

	/**
	 * @param string $username
	 * @return array<string, mixed>|null
	 */
	public function findOneByUsername(string $username): ?array
	{

		$dbh = $this->connection->get();

		$sth = $dbh->prepare(<<<'SQL'
			SELECT * FROM users WHERE username = :username LIMIT 1
		SQL
		);

		$sth->execute([
			'username' => $username,
		]);

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		if ($result === false) {
			return null;
		}

		return $result;

	}

}
