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
	 * Routes indexed by their names.
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
	 * @param string $presenter
	 * @param mixed[] $parameters
	 * @param bool $fullUrl
	 * @return string
	 */
	public function link(string $presenter, array $parameters = [], bool $fullUrl = false): string
	{
		if (!isset($this->logicalToUrl[$presenter])) {

			if ($this->config->isModeDevelopment()) {
				throw new InvalidArgumentException("Invalid link '$presenter'.");
			}

			return '#invalid-link';
		}

		$prefix = $fullUrl ? $this->fullUrlPrefix : $this->pathPrefix;

		return $prefix . $this->logicalToUrl[$presenter]->link($parameters);
	}

	/**
	 * Adds the given route at the end of the routing table.
	 *
	 * It the route does NOT have the {@see Route::ROUTE_ONE_WAY} flag,
	 * it is also added to the reverse table (i.e. link-generation table).
	 *
	 * @param Route $route the route to add
	 * @return void
	 */
	public function addRoute(Route $route): void
	{
		$this->routes[] = $route;
		if (!($route->flags & Route::ROUTE_ONE_WAY)) {
			$this->logicalToUrl[$route->presenter] = $route;
		}
	}

}
