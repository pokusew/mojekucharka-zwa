<?php

declare(strict_types=1);

namespace App;

use Core\Config;
use Core\Routing\RegexRoute;
use Core\Routing\Router;
use Core\Routing\SimpleRoute;

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

		$router->addRoute(new SimpleRoute('/', 'Home'));
		$router->addRoute(new SimpleRoute('/recipes', 'Recipes'));
		$router->addRoute(new SimpleRoute('/recipe/:id', 'Recipe'));
		$router->addRoute(new SimpleRoute('/profile/:username', 'Profile'));
		$router->addRoute(new SimpleRoute('/sign/up', 'SignUp'));
		$router->addRoute(new RegexRoute(
			'#^/verify-email/(?<key>[^/]+)$#',
			function (callable $getParam) {
				$key = $getParam('key');
				return "/verify-email/$key";
			},
			'VerifyEmail',
		));
		$router->addRoute(new SimpleRoute('/sign/in', 'SignIn'));
		$router->addRoute(new SimpleRoute('/sign/forgotten', 'SignForgotten'));
		$router->addRoute(new SimpleRoute('/sign/out', 'SignOut'));
		$router->addRoute(new SimpleRoute('/settings', 'Settings'));

		return $router;
	}

}
