<?php

namespace App;

use App\DI\Container;

class App
{

	private HttpRequest $httpRequest;
	private Router $router;
	private Container $container;

	/**
	 * @param Config $config
	 * @param Assets $assets
	 * @param HttpRequest $httpRequest
	 * @param Router $router
	 * @param Container $container
	 */
	public function __construct(
		Config $config,
		Assets $assets,
		HttpRequest $httpRequest,
		Router $router,
		Container $container)
	{
		$this->config = $config;
		$this->assets = $assets;
		$this->httpRequest = $httpRequest;
		$this->router = $router;
		$this->container = $container;
	}


	public function run()
	{
		// dump($this->httpRequest);

		$match = $this->router->match($this->httpRequest->path);

		// dump($match);

		if ($match === null) {
			// TODO: better 404
			http_response_code(404);
			return;
		}

		$presenterName = 'App\Presenter\\' . $match->route->presenter . 'Presenter';

		$presenter = $this->container->createByType($presenterName);

		$presenter->action();
		$presenter->render();

	}

}
