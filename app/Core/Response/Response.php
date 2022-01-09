<?php

declare(strict_types=1);

namespace Core\Response;

use Core\Http\HttpRequest;
use Core\Http\HttpResponse;

/**
 * Interface that any presenter-generated response must implement.
 */
interface Response
{

	/**
	 * Sends response to output.
	 */
	function send(HttpRequest $httpRequest, HttpResponse $httpResponse): void;

}
