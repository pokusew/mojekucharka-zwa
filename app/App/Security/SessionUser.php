<?php

declare(strict_types=1);

namespace App\Security;

use DateTime;

/**
 * Represents data of logged in user in the session.
 */
class SessionUser
{

	private int $id;
	private string $username;
	private ?string $name;
	private string $email;

	private DateTime $loginDate;
	private string $loginIp;

	public function __construct(
		int $id,
		string $username,
		?string $name,
		string $email,
		DateTime $loginDate,
		string $loginIp)
	{
		$this->id = $id;
		$this->username = $username;
		$this->name = $name;
		$this->email = $email;
		$this->loginDate = $loginDate;
		$this->loginIp = $loginIp;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getUsername(): string
	{
		return $this->username;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function getLoginDate(): DateTime
	{
		return $this->loginDate;
	}

	public function getLoginIp(): string
	{
		return $this->loginIp;
	}

	public function getDisplayName(): string
	{
		return $this->name ?? $this->username;
	}

}
