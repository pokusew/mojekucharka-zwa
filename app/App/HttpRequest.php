<?php

declare(strict_types=1);

namespace App;

class HttpRequest
{

	public string $method;
	public bool $https;
	public string $host;
	public string $path;
	public ?array $query;
	public ?array $post;

	/**
	 * @param string $method
	 * @param bool $https
	 * @param string $host
	 * @param string $path
	 * @param array $query
	 * @param array $post
	 */
	public function __construct(string $method, bool $https, string $host, string $path, array $query, array $post)
	{
		$this->method = $method;
		$this->https = $https;
		$this->host = $host;
		$this->path = $path;
		// consider pa
		$this->query = null;
		$this->post = null;
	}

	public static function fromSuperglobals(): HttpRequest
	{
		$method = $_SERVER['REQUEST_METHOD'];
		$https = ($_SERVER['REQUEST_SCHEME'] ?? '') === 'https' || ($_SERVER['HTTPS'] ?? '') === 'on';
		$host = $_SERVER['HTTP_HOST'];

		$path = isset($_SERVER['QUERY_STRING']) && is_int($queryStringPos = strpos($_SERVER['REQUEST_URI'], '?'))
			? substr($_SERVER['REQUEST_URI'], $queryStringPos)
			: $_SERVER['REQUEST_URI'];

		return new self(
			$method,
			$https,
			$host,
			$path,
			$_GET,
			$_POST,
		);

	}

}
