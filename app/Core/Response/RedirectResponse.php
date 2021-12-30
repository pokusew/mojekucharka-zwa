<?php

declare(strict_types=1);

namespace Core\Response;

use Core\Http\HttpRequest;
use Core\Http\HttpResponse;

/**
 * Redirects to new URI.
 */
class RedirectResponse implements Response
{

	private string $url;
	private int $code;

	/**
	 * @param string $url
	 * @param int $code
	 */
	public function __construct(string $url, int $code)
	{
		$this->url = $url;
		$this->code = $code;
	}

	public function send(HttpRequest $httpRequest, HttpResponse $httpResponse)
	{
		$httpResponse->redirect($this->url, $this->code);
	}

}
