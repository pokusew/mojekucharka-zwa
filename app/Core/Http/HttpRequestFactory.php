<?php

declare(strict_types=1);

namespace Core\Http;

class HttpRequestFactory
{

	public function createHttpRequest(): HttpRequest
	{
		$method = $_SERVER['REQUEST_METHOD'];
		$https = ($_SERVER['REQUEST_SCHEME'] ?? '') === 'https' || ($_SERVER['HTTPS'] ?? '') === 'on';
		$host = $_SERVER['HTTP_HOST'];

		$path = isset($_SERVER['QUERY_STRING']) && is_int($queryStringPos = strpos($_SERVER['REQUEST_URI'], '?'))
			? substr($_SERVER['REQUEST_URI'], $queryStringPos)
			: $_SERVER['REQUEST_URI'];

		return new HttpRequest(
			$method,
			$https,
			$host,
			$path,
			$_GET,
			$_POST,
			$_SERVER['REMOTE_ADDR'], // TODO: add configurable support for proxy (i.e. X-Forwarded-For header)
		);

	}

}
