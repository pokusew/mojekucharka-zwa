<?php

declare(strict_types=1);

namespace Core\Response;

use Core\Http\HttpRequest;
use Core\Http\HttpResponse;

/**
 * Redirects to new URI.
 *
 * Uses {@see HttpResponse::redirect()}.
 */
class RedirectResponse implements Response
{

	private string $url;
	private int $code;

	/**
	 * @param string $url a URL where to redirect
	 * @param int $code a valid HTTP status code
	 */
	public function __construct(string $url, int $code)
	{
		$this->url = $url;
		$this->code = $code;
	}

	public function send(HttpRequest $httpRequest, HttpResponse $httpResponse): void
	{
		$httpResponse->redirect($this->url, $this->code);
	}

}
