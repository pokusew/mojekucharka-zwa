<?php

declare(strict_types=1);

namespace Core\Response;

use Core\Http\HttpRequest;
use Core\Http\HttpResponse;

/**
 * A text response.
 */
class TextResponse implements Response
{

	protected string $text;

	/**
	 * @param string $text text to send
	 */
	public function __construct(string $text)
	{
		$this->text = $text;
	}

	public function send(HttpRequest $httpRequest, HttpResponse $httpResponse): void
	{
		echo $this->text;
	}

}
