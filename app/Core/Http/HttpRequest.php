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
	/** @var string the IPv4 or IPv6 address of the user */
	public string $remoteAddress;

	/**
	 * @param string $method
	 * @param bool $https
	 * @param string $host
	 * @param string $path
	 * @param string[] $query
	 * @param mixed[] $post
	 * @param string $remoteAddress the IPv4 or IPv6 address of the user
	 */
	public function __construct(
		string $method,
		bool $https,
		string $host,
		string $path,
		array $query,
		array $post,
		string $remoteAddress
	)
	{
		$this->method = $method;
		$this->https = $https;
		$this->host = $host;
		$this->path = $path;
		$this->query = $query;
		$this->post = $post;
		$this->remoteAddress = $remoteAddress;
	}

}
