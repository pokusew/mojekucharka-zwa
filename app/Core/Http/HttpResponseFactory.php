<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Config;

class HttpResponseFactory
{

	private Config $config;

	/**
	 * @param Config $config
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	public function createHttpResponse(): HttpResponse
	{
		$httpResponse = new HttpResponse();

		$httpResponse->cookiePath = $this->config->basePath;

		return $httpResponse;
	}

}
