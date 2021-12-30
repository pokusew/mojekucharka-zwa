<?php

declare(strict_types=1);

namespace Core\Routing;

class RouteMatch
{

	public Route $route;
	/** @var mixed[] */
	public array $params;

	/**
	 * @param Route $route
	 * @param mixed[] $params
	 */
	public function __construct(Route $route, array $params)
	{
		$this->route = $route;
		$this->params = $params;
	}

}
