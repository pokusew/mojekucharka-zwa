<?php

declare(strict_types=1);

namespace Core\Routing;

/**
 * A route that uses exact equality comparison for matching and does not support parameters.
 */
class SimpleRoute extends Route
{

	/**
	 * @var string the exact URL path to match (compared using `===`)
	 */
	public string $pattern;

	/**
	 * @param string $pattern
	 * @param string $presenter
	 * @param ?string $action
	 */
	public function __construct(
		string $pattern,
		string $presenter,
		?string $action = null
	)
	{
		$this->pattern = $pattern;
		$this->presenter = $presenter;
		$this->action = $action;
	}

	public function match(string $path): ?array
	{
		// simple exact equality match (default behavior)
		if ($this->pattern === $path) {
			// no parameters support in this mode
			return [];
		}

		// no match
		return null;
	}

	public function link(array $params = [], bool $throwOnMissingParams = false): string
	{
		return $this->pattern;
	}

}
