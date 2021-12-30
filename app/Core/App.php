<?php

declare(strict_types=1);

namespace Core;

use Core\DI\Container;
use Core\Http\HttpRequest;
use Core\Http\HttpResponse;
use Core\Routing\Router;
use Core\UI\Presenter;
use Tracy\Debugger;
use Tracy\ILogger;

class App
{

	private HttpRequest $httpRequest;
	private HttpResponse $httpResponse;
	private Router $router;
	private Container $container;
	private Config $config;

	/**
	 * @param HttpRequest $httpRequest
	 * @param HttpResponse $httpResponse
	 * @param Router $router
	 * @param Container $container
	 * @param Config $config
	 */
	public function __construct(
		HttpRequest $httpRequest,
		HttpResponse $httpResponse,
		Router $router,
		Container $container,
		Config $config
	)
	{
		$this->httpRequest = $httpRequest;
		$this->httpResponse = $httpResponse;
		$this->router = $router;
		$this->container = $container;
		$this->config = $config;
	}

	protected function createPresenter(string $presenterName): Presenter {

		$presenterClassName = $this->config->presenterNamespace . '\\' . $presenterName . 'Presenter';

		$presenter = $this->container->createByType($presenterClassName);

		if (!($presenter instanceof Presenter)) {
			throw new \InvalidArgumentException(
				"'$presenterClassName' must be subclass of Core\UI\Presenter."
			);
		}

		return $presenter;

	}

	public function run()
	{
		// dump($this->httpRequest);

		try {

			try {

				$match = $this->router->match($this->httpRequest->path);

				if ($match === null) {
					throw new BadRequestException('No route for HTTP request.', 404);
				}

				$presenter = $this->createPresenter($match->route->presenter);

				$response = $presenter->run($match, null);

				if ($response !== null) {
					$response->send($this->httpRequest, $this->httpResponse);
				}

			} catch (\Exception $e) {

				// Error presenter
				$presenter = $this->createPresenter('Error');

				$response = $presenter->run(null, $e);

				if ($response !== null) {
					$response->send($this->httpRequest, $this->httpResponse);
				}

			}

		} catch (\Exception $e) {
			// fatal exception
			Debugger::log($e, ILogger::CRITICAL);
			http_response_code(500);
			require __DIR__ . '/error.template.php';
			exit(1);
		}

	}

}
