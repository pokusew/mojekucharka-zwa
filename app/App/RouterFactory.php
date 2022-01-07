<?php

declare(strict_types=1);

namespace App;

use Core\Config;
use Core\Routing\Route;
use Core\Routing\Router;

class RouterFactory
{

	private Config $config;

	/**
	 * @param Config $config
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	public function createRouter(): Router
	{
		$router = new Router($this->config);

		$router->addRoute(new Route('/', 'Home'));
		$router->addRoute(new Route('/recipes', 'Recipes'));
		$router->addRoute(new Route('/recipe/:id', 'Recipe'));
		$router->addRoute(new Route('/profile/:username', 'Profile'));
		$router->addRoute(new Route('/sign/up', 'SignUp'));
		$router->addRoute(new Route('/verify-email', 'VerifyEmail'));
		$router->addRoute(new Route('/sign/in', 'SignIn'));
		$router->addRoute(new Route('/sign/forgotten', 'SignForgotten'));
		$router->addRoute(new Route('/sign/out', 'SignOut'));
		$router->addRoute(new Route('/settings', 'Settings'));

		return $router;
	}

}
