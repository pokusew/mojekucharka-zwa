<?php

namespace Core;

use Core\DI\Container;
use Core\Http\HttpRequest;
use Core\Routing\Router;
use Core\UI\Presenter;

class App
{

	private HttpRequest $httpRequest;
	private Router $router;
	private Container $container;
	private Config $config;

	/**
	 * @param HttpRequest $httpRequest
	 * @param Router $router
	 * @param Container $container
	 * @param Config $config
	 */
	public function __construct(
		HttpRequest $httpRequest,
		Router $router,
		Container $container,
		Config $config
	)
	{
		$this->httpRequest = $httpRequest;
		$this->router = $router;
		$this->container = $container;
		$this->config = $config;
	}


	public function run()
	{
		// dump($this->httpRequest);

		$match = $this->router->match($this->httpRequest->path);

		// dump($match);

		if ($match !== null) {
			$presenterName = $match->route->presenter;
		} else {
			// TODO: better 404
			http_response_code(404);
			$presenterName = 'Error';
		}

		$presenterClassName = $this->config->presenterNamespace . '\\' . $presenterName . 'Presenter';

		$presenter = $this->container->createByType($presenterClassName);

		if (!($presenter instanceof Presenter)) {
			throw new \InvalidArgumentException(
				"'$presenterClassName' must be subclass of Core\UI\Presenter."
			);
		}

		$presenter->run($this->httpRequest, $match);

	}

}
