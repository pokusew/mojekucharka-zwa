<?php

declare(strict_types=1);

namespace Core\Http;

class HttpRequest
{

	public string $method;
	public bool $https;
	public string $host;
	public string $path;
	/** @var string[] */
	public array $query;
	/** @var mixed[] */
	public array $post;

	/**
	 * @param string $method
	 * @param bool $https
	 * @param string $host
	 * @param string $path
	 * @param string[] $query
	 * @param mixed[] $post
	 */
	public function __construct(string $method, bool $https, string $host, string $path, array $query, array $post)
	{
		$this->method = $method;
		$this->https = $https;
		$this->host = $host;
		$this->path = $path;
		$this->query = $query;
		$this->post = $post;
	}

}
