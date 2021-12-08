<?php

namespace App;

class RouteMatch
{

	public Route $route;
	public array $params;

	/**
	 * @param Route $route
	 * @param array $params
	 */
	public function __construct(Route $route, array $params)
	{
		$this->route = $route;
		$this->params = $params;
	}

}
