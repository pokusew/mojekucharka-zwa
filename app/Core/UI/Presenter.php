<?php

declare(strict_types=1);

namespace Core\UI;

use Core\Assets;
use Core\Config;
use Core\Exceptions\AbortException;
use Core\Http\HttpRequest;
use Core\Http\HttpResponse;
use Core\Response\RedirectResponse;
use Core\Response\Response;
use Core\Response\TextResponse;
use Core\Routing\RouteMatch;
use Core\Routing\Router;
use Exception;
use InvalidArgumentException;
use ReflectionException;
use ReflectionMethod;

/**
 * Presenter processes the given request and generate a response.
 *
 * It represents the (page) controller in the MVC architecture.
 */
abstract class Presenter
{

	/** @inject */
	public Config $config;

	/** @inject */
	public Router $router;

	/** @inject */
	public Assets $assets;

	/** @inject */
	public HttpRequest $httpRequest;

	/** @inject */
	public HttpResponse $httpResponse;

	/**
	 * @var RouteMatch|null The route match that was passed in {@see Presenter::run()}.
	 *                      It is always non-null, unless {@see Presenter::$exception} is set.
	 */
	protected ?RouteMatch $routeMatch = null;

	/**
	 * @var Exception|null The exception or `null` that was passed in {@see Presenter::run()}.
	 *                     It is non-null only for the error presenter.
	 */
	protected ?Exception $exception = null;

	/**
	 * @var Response|null The response that will be returned from {@see Presenter::run()}.
	 */
	protected ?Response $response = null;

	/**
	 * @var string|null The directory where the layout and view templates are located.
	 *                  Must not contain the trailing slash.
	 * @see Presenter::render()
	 */
	protected ?string $templatesDir = null;
	/**
	 * @var string|null The name of the layout template. Without the .php extension.
	 * @see Presenter::render()
	 */
	protected ?string $layout = null;
	/**
	 * @var string|null The name of the layout template. Without the .php extension.
	 * @see Presenter::render()
	 */
	protected ?string $view = null;

	/**
	 * Generates the URL corresponding to the given logical address.
	 *
	 * It behaves almost the same as {@see Router::link()} but it supports the special
	 * logical address `this` that denotes the current presenter action combination.
	 *
	 * Note that 'this' is not supported in the error presenter.
	 *
	 * @param string $dest The logical address (Presenter:action) or `this`.
	 *                     Note that 'this' is not supported in the error presenter.
	 *
	 * @param array<string, mixed>|null $params When set to `null` while $dest === 'this'`,
	 *                                          the current parameters are used. Otherwise the given $params
	 *                                          are used.
	 *
	 * @param bool $fullUrl When `true`, the full URL (incl. scheme and host) is returned.
	 *
	 * @return string The URL corresponding to the given logical address
	 *                or `#invalid-link` when no route was found and app is NOT in the development mode.
	 *
	 * @throws InvalidArgumentException When no route was found and app is in the development mode.
	 */
	protected function link(string $dest, ?array $params = null, bool $fullUrl = false): string
	{
		// handle special presenter-only value this
		if ($dest === 'this') {

			if ($this->routeMatch === null) {

				if ($this->config->isModeDevelopment()) {
					throw new InvalidArgumentException(
						"Cannot create link 'this' because routeMatch is null. "
						. " Note that 'this' is not supported in the error presenter."
					);
				}

				return '#invalid-link';
			}

			return $this->router->link(
				$this->routeMatch->route->getLogicalAddress(),
				$params ?? $this->routeMatch->params,
				$fullUrl
			);
		}

		return $this->router->link($dest, $params ?? [], $fullUrl);
	}

	/**
	 * Checks whether the the given logical address is same as the actual logical address.
	 *
	 * @param string $dest The logical address (Presenter:action) or `this`.
	 *                     Note that 'this' is not supported in the error presenter.
	 *
	 * @param array<string, mixed>|null $params When set to `null` while $dest === 'this'`,
	 *                                          the current parameters are used. Otherwise the given $params
	 *                                          are used.
	 *
	 * @return bool `true` if the given logical address is same as the actual logical address
	 *               or `false` when no route was found and app is NOT in the development mode.
	 *
	 * @throws InvalidArgumentException When no route was found and app is in the development mode.
	 *
	 */
	protected function isLinkCurrent(string $dest, ?array $params = null): bool
	{
		$url = $this->link($dest, $params);
		return $url === $this->httpRequest->path;
	}

	/**
	 * Redirects to the given logical address.
	 *
	 * The HTTP status code is chosen automatically:
	 * 1. 303 if this is a POST request (see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/303)
	 * 2. 302 otherwise.
	 *
	 * @param string $dest The logical address (Presenter:action) or `this`.
	 *                     Note that 'this' is not supported in the error presenter.
	 *
	 * @param array<string, mixed>|null $params When set to `null` while $dest === 'this'`,
	 *                                          the current parameters are used. Otherwise the given $params
	 *                                          are used.
	 *
	 * @param bool $fullUrl When `true`, the full URL (incl. scheme and host) is returned.
	 *
	 * @return never always throws an AbortException
	 *
	 * @throws InvalidArgumentException When no route was found and app is in the development mode.
	 *
	 * @throws AbortException
	 */
	protected function redirect(string $dest, ?array $params = null, bool $fullUrl = false)
	{
		$code = $this->httpRequest->method === 'POST'
			? HttpResponse::S_303_SEE_OTHER // see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/303
			: HttpResponse::S_302_FOUND;
		$this->redirectWithCode($code, $dest, $params, $fullUrl);
	}

	/**
	 * Redirects to the given logical address.
	 *
	 * The HTTP status code is chosen automatically:
	 * 1. 303 if this is a POST request (see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/303)
	 * 2. 302 otherwise.
	 *
	 * @param int $code A redirect HTTP status code (e.g. {@see HttpResponse::S_302_FOUND},
	 *                  {@see HttpResponse::S_301_FOUND}, {@see HttpResponse::S_303_FOUND},
	 *                  {@see HttpResponse::S_307_TEMPORARY_REDIRECT}, {@see HttpResponse::S_308_PERMANENT_REDIRECT}).
	 *
	 * @param string $dest The logical address (Presenter:action) or `this`.
	 *                     Note that 'this' is not supported in the error presenter.
	 *
	 * @param array<string, mixed>|null $params When set to `null` while $dest === 'this'`,
	 *                                          the current parameters are used. Otherwise the given $params
	 *                                          are used.
	 *
	 * @param bool $fullUrl When `true`, the full URL (incl. scheme and host) is returned.
	 *
	 * @return never always throws an AbortException
	 *
	 * @throws InvalidArgumentException When no route was found and app is in the development mode.
	 *
	 * @throws AbortException
	 */
	protected function redirectWithCode(int $code, string $dest, ?array $params = null, bool $fullUrl = false)
	{
		$url = $this->link($dest, $params, $fullUrl);
		$this->sendResponse(new RedirectResponse($url, $code));
	}

	/**
	 * Correctly terminates presenter by throwing an AbortException which is then caught in {@see Presenter::run()}
	 * @return never always throws an AbortException
	 * @throws AbortException always
	 */
	protected function terminate()
	{
		throw new AbortException();
	}

	/**
	 * Sends response and terminates presenter.
	 * @return never always throws an AbortException
	 * @throws AbortException
	 */
	protected function sendResponse(Response $response)
	{
		$this->response = $response;
		$this->terminate();
	}

	/**
	 * Invokes the correct action with correct arguments based on the route match.
	 */
	protected function invokeAction(?RouteMatch $routeMatch): void
	{
		$actionMethodName = $routeMatch !== null && $routeMatch->route->action !== null
			? 'action' . ucfirst($routeMatch->route->action)
			: 'action';

		try {
			$actionMethod = new ReflectionMethod($this, $actionMethodName);
		} catch (ReflectionException $e) {
			$fullActionMethodName = get_class($this) . '::' . $actionMethodName;
			throw new InvalidArgumentException(
				"Missing action method '$fullActionMethodName'.",
				0,
				$e,
			);
		}

		$actionMethodArgs = [];

		if ($routeMatch !== null) {

			$actionMethodParams = $actionMethod->getParameters();

			foreach ($actionMethodParams as $actionMethodParam) {

				if (isset($routeMatch->params[$actionMethodParam->name])) {
					$actionMethodArgs[] = $routeMatch->params[$actionMethodParam->name];
					continue;
				}

				if ($actionMethodParam->isDefaultValueAvailable()) {
					$actionMethodArgs[] = $actionMethodParam->getDefaultValue();
					continue;
				}

				$fullActionMethodName = get_class($this) . '::' . $actionMethodName;
				throw new InvalidArgumentException(
					"Could not determine value for the '$actionMethodParam' parameter"
					. " of action method '$fullActionMethodName'."
				);

			}

		}

		try {

			// invoke the action method
			$actionMethod->invokeArgs($this, $actionMethodArgs);

		} catch (ReflectionException $e) {
			$fullActionMethodName = $actionMethod->getName();
			throw new InvalidArgumentException(
				"Failed invocation of action method '$fullActionMethodName'.",
				0,
				$e,
			);
		}
	}

	/**
	 * Runs the presenter and generate a response.
	 * @param RouteMatch|null $routeMatch The route match. Contains action name and router parameters' values.
	 *                                    Should be `null` only if `$exception !== null`
	 *                                    and this is an error presenter.
	 * @param Exception|null $exception Should be set only in case `$routeMatch === null`
	 *                                  and this is an error presenter.
	 * @return Response|null a response, may be null if there is no response
	 */
	public function run(?RouteMatch $routeMatch, ?Exception $exception): ?Response
	{
		$this->routeMatch = $routeMatch;
		$this->exception = $exception;

		try {

			$this->invokeAction($this->routeMatch);

			// see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/HEAD
			if ($this->httpRequest->method === 'HEAD') {
				$this->terminate();
			}

			$this->render();

		} catch (AbortException $e) {

			return $this->response;

		}

		return $this->response;
	}

	/**
	 * Renders the template specified in {@see Presenter::$view} to string and sets it as the response.
	 *
	 * The {@see Presenter::$templatesDir} and {@see Presenter::$view} must be both non-null.
	 *
	 * Optionally, if {@see Presenter::$layout} is set (non-null), it is rendered
	 * with the `$page` variable that contains the rendered view template (as string).
	 * Other variables that may be set during the rendering of the view template are also available
	 * in the layout template.
	 */
	protected function render(): void
	{
		// define variables so that they are available
		$config = $this->config;
		$router = $this->router;
		$assets = $this->assets;

		if ($this->templatesDir === null || $this->view == null) {
			return;
		}

		ob_start();
		require $this->templatesDir . '/' . $this->view . '.php';
		if ($this->layout !== null) {
			$page = ob_get_clean();
			ob_start();
			require $this->templatesDir . '/' . $this->layout . '.php';
		}
		$text = ob_get_clean();

		if ($text !== false) {
			$this->response = new TextResponse($text);
		}
	}

}
