<?php

namespace App\Presenter;

use App\Assets;
use App\Config;
use App\Router;

abstract class BasePresenter
{

	/** @inject */
	public Config $config;

	/** @inject */
	public Router $router;

	/** @inject */
	public Assets $assets;

	protected ?string $view = null;

	public function render()
	{

		// define variables so that they are available
		$config = $this->config;
		$router = $this->router;
		$assets = $this->assets;

		$layout = '_layout';

		ob_start();

		require __DIR__ . '/../../templates/' . $this->view . '.php';

		$page = ob_get_clean();

		require __DIR__ . '/../../templates/' . $layout . '.php';

	}

}
