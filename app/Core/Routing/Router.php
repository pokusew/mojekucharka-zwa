<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\Config;

class Router
{

	private Config $config;

	private string $pathPrefix;

	/**
	 * @var Route[]
	 */
	private array $routes = [];

	/**
	 * @var Route[]
	 */
	private array $logicalToUrl = [];

	/**
	 * @param Config $config
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->pathPrefix = substr($config->basePath, 0, -1);
	}

	public function getUrl($url): string
	{
		// strip leading slash in $url
		if (strlen($url) >= 1 && $url[0] == '/') {
			$url = substr($url, 1);
		}

		return $this->config->basePath . $url;
	}

	public function match(string $path): ?RouteMatch
	{
		// check that the path starts with the basePath
		// TODO: replace with str_starts_with($path, $this->config->basePath) once we run on PHP 8
		if (substr_compare($path, $this->config->basePath, 0, strlen($this->config->basePath)) !== 0) {
			return null;
		}

		// strip leading basePath
		$path = substr($path, strlen($this->config->basePath) - 1);

		foreach ($this->routes as $route) {

			$params = $route->match($path);

			if ($params !== null) {
				return new RouteMatch($route, $params);
			}

		}

		return null;
	}

	public function link(string $presenter, array $parameters = [], bool $fullUrl = false): string
	{
		if (!isset($this->logicalToUrl[$presenter])) {
			return '#invalid-link';
		}

		return $this->pathPrefix . $this->logicalToUrl[$presenter]->link($parameters);
	}

	public function addRoute(Route $route): void
	{
		$this->routes[] = $route;
		$this->logicalToUrl[$route->presenter] = $route;
	}

}
