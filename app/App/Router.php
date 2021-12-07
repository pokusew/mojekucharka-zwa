<?php

namespace App;

class Router
{

	private Config $config;

	/**
	 * @param Config $config
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	public function getUrl($url): string
	{
		// strip leading slash in $url
		if (strlen($url) >= 1 && $url[0] == '/') {
			$url = substr($url, 1);
		}

		return $this->config->basePath . $url;
	}

}
