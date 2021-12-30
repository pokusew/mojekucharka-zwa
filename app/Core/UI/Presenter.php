<?php

declare(strict_types=1);

namespace Core\UI;

use Core\AbortException;
use Core\Assets;
use Core\Config;
use Core\Http\HttpRequest;
use Core\Http\HttpResponse;
use Core\Response\RedirectResponse;
use Core\Response\Response;
use Core\Response\TextResponse;
use Core\Routing\RouteMatch;
use Core\Routing\Router;
use Exception;

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

	protected ?RouteMatch $routeMatch;
	protected ?Exception $exception;

	protected ?Response $response = null;

	protected ?string $templatesDir = null;
	protected ?string $layout = null;
	protected ?string $view = null;

	/**
	 * @param string $dest
	 * @param mixed[] $params
	 * @param bool $fullUrl
	 * @return string
	 */
	public function link(string $dest, array $params = [], bool $fullUrl = false): string
	{
		return $this->router->link($dest, $params, $fullUrl);
	}

	/**
	 * @param string $dest
	 * @param mixed[] $params
	 * @return bool
	 */
	public function isLinkCurrent(string $dest, array $params = []): bool
	{
		$url = $this->router->link($dest, $params);
		return $url === $this->httpRequest->path;
	}

	/**
	 * @param string $dest
	 * @param mixed[] $params
	 * @param bool $fullUrl
	 * @throws AbortException
	 * @return never always throws an AbortException
	 */
	public function redirect(string $dest, array $params = [], bool $fullUrl = false)
	{
		$code = $this->httpRequest->method === 'POST'
			? HttpResponse::S_303_SEE_OTHER // see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/303
			: HttpResponse::S_302_FOUND;
		$this->redirectWithCode($code, $dest, $params, $fullUrl);
	}

	/**
	 * @param int $code
	 * @param string $dest
	 * @param mixed[] $params
	 * @param bool $fullUrl
	 * @throws AbortException
	 * @return never always throws an AbortException
	 */
	public function redirectWithCode(int $code, string $dest, array $params = [], bool $fullUrl = false)
	{
		$url = $this->router->link($dest, $params, $fullUrl);
		$this->sendResponse(new RedirectResponse($url, $code));
	}

	/**
	 * Correctly terminates presenter by throwing an AbortException which is then caught in run
	 * @throws AbortException always
	 * @return never always throws an AbortException
	 */
	public function terminate()
	{
		throw new AbortException();
	}

	/**
	 * Sends response and terminates presenter.
	 * @throws AbortException
	 * @return never always throws an AbortException
	 */
	public function sendResponse(Response $response)
	{
		$this->response = $response;
		$this->terminate();
	}

	public function run(?RouteMatch $routeMatch, ?Exception $exception): ?Response
	{
		$this->routeMatch = $routeMatch;
		$this->exception = $exception;

		try {

			$this->action();

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

	public function action(): void {

	}

	public function render(): void
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

		if ($text) {
			$this->response = new TextResponse($text);
		}
	}

}
