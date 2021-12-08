<?php

namespace App;

class Router
{

	private Config $config;

	/**
	 * @var Route[]
	 */
	private array $routes;

	/**
	 * @param Config $config
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->routes = [
			new Route('/', 'Home'),
			new Route('/recipes', 'Recipes'),
			new Route('/recipe/:id', 'Recipe'),
			new Route('/profile/:username', 'Profile'),
			new Route('/sign/up', 'SignUp'),
			new Route('/sign/in', 'SignIn'),
			new Route('/sign/forgotten', 'SignForgotten'),
			new Route('/sign/out', 'SignOut'),
			new Route('/settings', 'Settings'),
		];
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

		foreach ($this->routes as $route) {

			$params = $route->match($path);

			if ($params !== null) {
				return new RouteMatch($route, $params);
			}

		}

		return null;

	}

}
