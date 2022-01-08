<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\Config;
use InvalidArgumentException;

/**
 * Simple two-way router implementation with a sequential route-matching table.
 */
class Router
{

	private Config $config;

	/** @var string without trailing slash */
	private string $pathPrefix;
	/** @var string without trailing slash */
	private string $fullUrlPrefix;

	/**
	 * Ordered array of routes.
	 * @var Route[]
	 */
	private array $routes = [];

	/**
	 * Routes indexed by their logical addresses.
	 * @var array<string, Route>
	 */
	private array $logicalToUrl = [];

	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->pathPrefix = substr($config->basePath, 0, -1); // strip trailing slash
		$this->fullUrlPrefix = substr($config->getBaseUrl(), 0, -1); // strip trailing slash
	}

	/**
	 * Tries to match the given URL path against the routing table.
	 * @param string $path URL path
	 * @return RouteMatch|null the route match or `null` if no route was matched
	 */
	public function match(string $path): ?RouteMatch
	{
		// check that the path starts with the basePath
		// TODO: replace with str_starts_with($path, $this->config->basePath) once we run on PHP 8
		if (substr_compare($path, $this->config->basePath, 0, strlen($this->config->basePath)) !== 0) {
			return null;
		}

		// strip leading basePath
		$path = substr($path, strlen($this->config->basePath) - 1);

		// try to find the first matching route (traverses sequentially)
		foreach ($this->routes as $route) {

			$params = $route->match($path);

			if ($params !== null) {
				return new RouteMatch($route, $params);
			}

		}

		return null;
	}

	/**
	 * Generates the URL corresponding to the given logical address.
	 * @param string $logical the logical address (Presenter:action)
	 * @param array<string, mixed> $params
	 * @param bool $fullUrl when `true`, the full URL (incl. scheme and host) is returned
	 * @return string the URL corresponding to the given logical address
	 *                or `#invalid-link` when no route was found and app is NOT in the development mode
	 * @throws InvalidArgumentException when no route was found and app is in the development mode
	 */
	public function link(string $logical, array $params = [], bool $fullUrl = false): string
	{
		if (!isset($this->logicalToUrl[$logical])) {

			if ($this->config->isModeDevelopment()) {
				throw new InvalidArgumentException("Invalid link '$logical'.");
			}

			return '#invalid-link';
		}

		$prefix = $fullUrl ? $this->fullUrlPrefix : $this->pathPrefix;

		return $prefix . $this->logicalToUrl[$logical]->link($params, $this->config->isModeDevelopment());
	}

	/**
	 * Shortcut for {@see Router::link()} with `$fullUrl = true`.
	 * @param string $logical
	 * @param array<string, mixed> $params
	 * @return string
	 */
	public function fullLink(string $logical, array $params = []): string
	{
		return $this->link($logical, $params, true);
	}

	/**
	 * Adds the given route at the end of the routing table.
	 *
	 * It the route does NOT have the {@see Route::ROUTE_ONE_WAY} flag,
	 * it is also added to the reverse table (i.e. link-generation table).
	 *
	 * @param Route $route the route to add
	 * @param bool $matchOnly When set to `true`, the route is used only for route matching and is omitted
	 *                        from link generation. This allows adding two routes with different patterns
	 *                        but with the same logical address (i.e. Presenter:action)
	 * @return void
	 */
	public function addRoute(Route $route, bool $matchOnly = false): void
	{
		$this->routes[] = $route;

		if (!$matchOnly) {

			$logical = $route->getLogicalAddress();

			if ($this->config->isModeDevelopment() && isset($this->logicalToUrl[$logical])) {
				throw new InvalidArgumentException(
					"Route with logical address '$logical' already defined."
					. ' If you want match only route, call this method with $matchOnly = false'
				);
			}

			$this->logicalToUrl[$logical] = $route;

		}
	}

}
