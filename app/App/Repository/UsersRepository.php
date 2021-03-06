<?php

declare(strict_types=1);

namespace App\Repository;

use App\Limits;
use Core\Database\Repository;
use PDOException;

class UsersRepository extends Repository
{

	public const TABLE = 'users';

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
	 * @param array<string|int, string>|null $columns will use `*` if `null` is given, empty array not allows
	 * @return array<string, mixed>|null
	 */
	public function findOneByEmail(string $email, ?array $columns = null): ?array
	{
		return $this->findOne(['email' => $email], $columns);
	}

	/**
	 * @param string $username
	 * @param array<string|int, string>|null $columns will use `*` if `null` is given, empty array not allows
	 * @return array<string, mixed>|null
	 */
	public function findOneByUsername(string $username, ?array $columns = null): ?array
	{
		return $this->findOne(['username' => $username], $columns);
	}

	/**
	 * @param string $passwordResetKey
	 * @param array<string|int, string>|null $columns will use `*` if `null` is given, empty array not allows
	 * @return array<string, mixed>|null
	 */
	public function findOneByValidPasswordResetKey(string $passwordResetKey, ?array $columns = null): ?array
	{
		$expirationInterval = Limits::PASSWORD_RESET_KEY_EXPIRATION;

		$customWhere
			= "WHERE password_reset_key = :passwordResetKey"
			. " AND (NOW() < (password_reset_key_created_at + INTERVAL $expirationInterval))";

		$query = $this->getSqlBuilder()
			->select(self::TABLE, $columns)
			->append($customWhere)
			->limit(1)
			->getQuery();

		$params = [
			'passwordResetKey' => $passwordResetKey,
		];

		return $this->fetchOneAssoc($query, $params);
	}

	/**
	 * @param string $emailOrUsername
	 * @param array<string|int, string>|null $columns will use `*` if `null` is given, empty array not allows
	 * @return array<string, mixed>|null
	 */
	public function findOneByEmailOrUsername(string $emailOrUsername, ?array $columns = null): ?array
	{
		return $this->findOne(
			[
				'OR' => [
					'email' => $emailOrUsername,
					'username' => $emailOrUsername,
				],
			],
			$columns
		);
	}

	/**
	 * Tries to set the e-mail address of a user as verified using the given email verification key.
	 * @param string $emailVerificationKey the email verification key
	 * @param string $verificationIp the IPv4 or IPv6 address from which the request originated
	 * @return bool `true` if successful, `false` otherwise (typically invalid key)
	 */
	public function verifyEmail(string $emailVerificationKey, string $verificationIp): bool
	{
		$dbh = $this->connection->get();

		// TODO: maybe limit max email verification key age
		$sth = $dbh->prepare(<<<'SQL'
			UPDATE users SET
				email_verified_at = NOW(),
				email_verified_from_ip = INET6_ATON(:verificationIp),
				email_verification_key = NULL,
				email_verification_key_created_at = NULL
			WHERE email_verification_key = :emailVerificationKey LIMIT 1;
			SQL
		);

		$sth->execute([
			'emailVerificationKey' => $emailVerificationKey,
			'verificationIp' => $verificationIp
		]);

		return $sth->rowCount() === 1;
	}

	/**
	 * Tries to reset the password of a user using the given password reset key.
	 * @param string $passwordResetKey the password reset key
	 * @param string $requestIp the IPv4 or IPv6 address from which the request originated
	 * @return bool `true` if successful, `false` otherwise (typically invalid or expired key)
	 */
	public function resetPassword(string $passwordResetKey, string $newPasswordHash, string $requestIp): bool
	{
		$dbh = $this->connection->get();

		$expirationInterval = Limits::PASSWORD_RESET_KEY_EXPIRATION;

		$sth = $dbh->prepare(<<<SQL
			UPDATE users SET
				password = :newPasswordHash,
				password_changed_at = NOW(),
				password_changed_from_ip = INET6_ATON(:requestIp),
			    password_reset_key = NULL,
				password_reset_key_created_at = NULL
			WHERE
				password_reset_key = :passwordResetKey
				AND (NOW() < (password_reset_key_created_at + INTERVAL $expirationInterval))
			LIMIT 1;
			SQL
		);

		$sth->execute([
			'passwordResetKey' => $passwordResetKey,
			'newPasswordHash' => $newPasswordHash,
			'requestIp' => $requestIp
		]);

		return $sth->rowCount() === 1;
	}

	/**
	 * Changes the password of the user with the given id.
	 * @param int $id the user id
	 * @param string $newPasswordHash the hash of the new password
	 * @param string $requestIp the IPv4 or IPv6 address from which the request originated
	 * @return bool `true` if successful, `false` otherwise
	 */
	public function changePassword(int $id, string $newPasswordHash, string $requestIp): bool
	{
		$dbh = $this->connection->get();

		$sth = $dbh->prepare(<<<'SQL'
			UPDATE users SET
			    password = :newPasswordHash,
				password_changed_at = NOW(),
				password_changed_from_ip = INET6_ATON(:requestIp)
			WHERE id = :id LIMIT 1;
			SQL
		);

		$sth->execute([
			'id' => $id,
			'newPasswordHash' => $newPasswordHash,
			'requestIp' => $requestIp,
		]);

		return $sth->rowCount() === 1;
	}

	/**
	 * Sets the password reset key of the user with the given email.
	 * @param string $email the user's email
	 * @param string $passwordResetKey the new password reset key
	 * @param string $requestIp the IPv4 or IPv6 address from which the request originated
	 * @return bool `true` if successful, `false` otherwise
	 */
	public function setPasswordResetKey(string $email, string $passwordResetKey, string $requestIp): bool
	{
		$dbh = $this->connection->get();

		$sth = $dbh->prepare(<<<'SQL'
			UPDATE users SET
			    password_reset_key = :passwordResetKey,
				password_reset_key_created_at = NOW()
			WHERE email = :email LIMIT 1;
			SQL
		);

		$sth->execute([
			'email' => $email,
			'passwordResetKey' => $passwordResetKey,
			// 'requestIp' => $requestIp,
		]);

		return $sth->rowCount() === 1;
	}

}
