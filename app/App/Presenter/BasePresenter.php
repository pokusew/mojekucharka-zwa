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

		$config = $this->config;
		$router = $this->router;
		$assets = $this->assets;

		if ($this->view !== null) {
			require __DIR__ . '/../../templates/' . $this->view . '.php';
		}

	}

}
