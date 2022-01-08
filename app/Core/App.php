<?php

declare(strict_types=1);

namespace Core;

use Core\DI\Container;
use Core\Exceptions\BadRequestException;
use Core\Http\HttpRequest;
use Core\Http\HttpResponse;
use Core\Routing\Router;
use Core\UI\Presenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Front controller.
 */
class App
{

	private HttpRequest $httpRequest;
	private HttpResponse $httpResponse;
	private Router $router;
	private Container $container;
	private Config $config;

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

	/**
	 * Tries to create a new instance of the presenter by its short name.
	 *
	 * The FQN of the presenter is constructed as follows:
	 * `{\Core\Config::$presenterNamespace}\{$presenterName}Presenter`.
	 *
	 * @param string $presenterName the presenter class short name (without namespace and without `Presenter` suffix)
	 * @return Presenter a new instance of the presenter
	 * @see Config::$presenterNamespace
	 */
	protected function createPresenter(string $presenterName): Presenter
	{
		$presenterClassName = $this->config->presenterNamespace . '\\' . $presenterName . 'Presenter';

		// @phpstan-ignore-next-line
		$presenter = $this->container->createByType($presenterClassName);

		if (!($presenter instanceof Presenter)) {
			throw new \InvalidArgumentException(
				"'$presenterClassName' must be subclass of Core\UI\Presenter."
			);
		}

		return $presenter;
	}

	/**
	 * Tries to processes the HTTP request and send an HTTP response.
	 *
	 * First, it tries to match the HTTP request to a route using the router's {@see Router::match()}.
	 * Then, on successful match, it instantiate the corresponding presenter
	 * and calls its {@see Presenter::run()} method.
	 * If the presenter generated a non-null response {@see \Core\Response\Response}, it attempts to send it.
	 *
	 * It does not do any error handling (it is done by {@see App::run()}.
	 *
	 * @throws BadRequestException when there is no route for HTTP request (i.e. the router returns no match)
	 */
	protected function processRequest(): void
	{
		$match = $this->router->match($this->httpRequest->path);

		if ($match === null) {
			throw new BadRequestException('No route for HTTP request.', 404);
		}

		$presenter = $this->createPresenter($match->route->presenter);

		$response = $presenter->run($match, null);

		if ($response !== null) {
			$response->send($this->httpRequest, $this->httpResponse);
		}
	}

	/**
	 * Runs the app.
	 *
	 * It calls {@see processRequest()} which does the actual work.
	 *
	 * When an exception occurs during {@see processRequest()},
	 * it is automatically caught, and the ErrorPresenter
	 * is instantiated and the exception is handed to it.
	 *
	 * If another exception occurs during the exception processing in the error presenter,
	 * it is also automatically caught, it is logged using {@see Debugger::log()}
	 * with the {@see ILogger::CRITICAL} level, and then a generic HTTP 500 Server error response is sent.
	 *
	 * **NOTE:** In the development mode (i.e. {@see Config::isModeDevelopment()}),
	 * all the exceptions handling is disabled, so that we can leverage the Tracy debugger
	 * (and its built-in blue screen).
	 *
	 * @return void
	 */
	public function run(): void
	{
		// during the development, let the Tracy handle all exceptions (i.e. show the blue screen)
		if ($this->config->isModeDevelopment()) {
			$this->processRequest();
			return;
		}

		// in production, handle exceptions robustly (first via the Error presenter,
		// and if it also fails, log the exception and show a minimal error page and set code to 500)
		try {

			try {

				$this->processRequest();

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
