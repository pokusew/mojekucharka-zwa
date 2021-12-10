<?php

namespace Core\UI;

use Core\Assets;
use Core\Config;
use Core\Http\HttpRequest;
use Core\Routing\RouteMatch;
use Core\Routing\Router;

abstract class Presenter
{

	/** @inject */
	public Config $config;

	/** @inject */
	public Router $router;

	/** @inject */
	public Assets $assets;

	protected HttpRequest $httpRequest;
	protected ?RouteMatch $routeMatch;

	protected ?string $templatesDir = null;
	protected ?string $layout = null;
	protected ?string $view = null;

	public function link(string $dest, array $params = [], bool $fullUrl = false): string
	{
		return $this->router->link($dest, $params, $fullUrl);
	}

	public function isLinkCurrent(string $dest, array $params = []): bool
	{
		// TODO
		return false;
	}

	public function run(HttpRequest $httpRequest, ?RouteMatch $routeMatch)
	{
		$this->httpRequest = $httpRequest;
		$this->routeMatch = $routeMatch;

		$this->action();
		$this->render();
	}

	public function action()
	{

	}

	public function render()
	{

		// define variables so that they are available
		$config = $this->config;
		$router = $this->router;
		$assets = $this->assets;

		if ($this->templatesDir === null || $this->view == null) {
			return;
		}

		if ($this->layout !== null) {

			ob_start();
			require $this->templatesDir . '/' . $this->view . '.php';
			$page = ob_get_clean();

			require $this->templatesDir . '/' . $this->layout . '.php';

		} else {
			require $this->templatesDir . '/' . $this->view . '.php';
		}

	}

}
