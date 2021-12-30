<?php

declare(strict_types=1);

namespace Core\Routing;

class Route
{

	public string $pattern;
	public string $regex;
	public string $presenter;

	// @phpstan-ignore-next-line
	public function __construct(string $pattern, string $presenter, bool $placeholders = false)
	{
		$this->pattern = $pattern;
		// $this->regex = $regex;
		$this->presenter = $presenter;
	}

	/**
	 * @param string $path
	 * @return mixed[]|null
	 */
	public function match(string $path): ?array
	{
		if ($this->pattern === $path) {
			return [];
		}
		return null;
	}

	/**
	 * @param mixed[] $parameters
	 */
	public function link(array $parameters = []): string
	{
		return $this->pattern;
	}

}
