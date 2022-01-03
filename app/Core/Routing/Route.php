<?php

declare(strict_types=1);

namespace Core\Routing;

class Route
{

	/**
	 * With this flag set, the route is used only for route matching and is omitted from link generation.
	 * This allows adding two routes with different patterns but with the same logical address.
	 */
	public const ROUTE_ONE_WAY = 0b0000_0001;

	/**
	 * Enables parameters placeholders support in the pattern.
	 * TODO: implement
	 */
	public const ROUTE_WITH_PLACEHOLDERS = 0b0000_0010;

	/**
	 * @var string URL path pattern (by default compared using `===`).
	 *             Note that parameters placeholders support must by explicitly enabled
	 *             using the {@see Route::ROUTE_WITH_PLACEHOLDERS} flag.
	 */
	public string $pattern;

	// public string $regex;

	/**
	 * @var string name of the presenter, must correspond to the class with name:
	 *             `{Config::$presenterNamespace}\{$name}Presenter`
	 */
	public string $presenter;

	/**
	 * Flags modify behavior of the route.
	 * @see Route::ROUTE_ONE_WAY
	 * @see Route::ROUTE_WITH_PLACEHOLDERS
	 */
	public int $flags;

	public function __construct(string $pattern, string $presenter, int $flags = 0)
	{
		$this->pattern = $pattern;
		// $this->regex = $regex;
		$this->presenter = $presenter;
		$this->flags = $flags;
	}

	/**
	 * Tries to match the given URL path against this route.
	 * @param string $path URL path
	 * @return mixed[]|null the parameters with their values on match, `null` otherwise
	 */
	public function match(string $path): ?array
	{
		if ($this->pattern === $path) {
			return [];
		}
		return null;
	}

	/**
	 * Returns the URL for this route.
	 *
	 * In most cases, you want to use rather the {@see Router::link()} that takes into account the config
	 * and can return the full URL.
	 *
	 * @param mixed[] $parameters the parameters with their values for the link
	 */
	public function link(array $parameters = []): string
	{
		return $this->pattern;
	}

}
