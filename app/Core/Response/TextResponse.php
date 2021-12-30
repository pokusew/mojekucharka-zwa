<?php

declare(strict_types=1);

namespace Core\Response;

use Core\Http\HttpRequest;
use Core\Http\HttpResponse;

/**
 * TODO
 */
class TextResponse implements Response
{

	protected string $text;

	/**
	 * @param string $text
	 */
	public function __construct(string $text)
	{
		$this->text = $text;
	}

	public function send(HttpRequest $httpRequest, HttpResponse $httpResponse)
	{
		echo $this->text;
	}

}
