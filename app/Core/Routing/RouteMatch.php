<?php

declare(strict_types=1);

namespace Core\Routing;

/**
 * Represents a matched route with with its parameters' values.
 */
class RouteMatch
{

	public Route $route;
	/** @var array<string, mixed> */
	public array $params;

	/**
	 * @param Route $route
	 * @param array<string, mixed> $params
	 */
	public function __construct(Route $route, array $params)
	{
		$this->route = $route;
		$this->params = $params;
	}

	/**
	 * Generates the link for this route match.
	 *
	 * A shortcut for: `$match->route->link($match->params)`
	 *
	 * @return string URL
	 */
	public function link(): string
	{
		return $this->route->link($this->params);
	}

}
