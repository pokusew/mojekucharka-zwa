<?php

declare(strict_types=1);

namespace Core\Http;

use Core\Config;

/**
 * Default {@see HttpResponse} factory that sets {@see HttpResponse::$cookiePath} to {@see Config::$basePath}.
 */
class HttpResponseFactory
{

	private Config $config;

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
