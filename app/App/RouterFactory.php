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

		// /recipe/:id
		$router->addRoute(new RegexRoute(
			'#^/recipe/(?<id>[1-9][0-9]{0,9})$#',
			function (callable $getParam) {
				$id = (string) $getParam('id');
				return "/recipe/$id";
			},
			'Recipe',
			'view',
			[
				'id' => function ($value) {
					return (int) $value;
				},
			],
		));

		// /profile/:username
		$usernameLimit = '{' . Limits::USERNAME_MIN_LENGTH . ',' . Limits::USERNAME_MAX_LENGTH . '}';
		$router->addRoute(new RegexRoute(
			"#^/profile/(?<username>[^/]$usernameLimit)$#",
			function (callable $getParam) {
				$username = $getParam('username');
				return "/profile/$username";
			},
			'Profile',
			'view',
		));

		$router->addRoute(new SimpleRoute('/sign/up', 'SignUp'));
		$router->addRoute(new SimpleRoute('/sign/up/success', 'SignUp', 'success'));

		// /verify-email/:key
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
