<?php

declare(strict_types=1);

namespace Core\Response;

use Core\Http\HttpRequest;
use Core\Http\HttpResponse;

interface Response
{

	/**
	 * Sends response to output.
	 * @return void
	 */
	function send(HttpRequest $httpRequest, HttpResponse $httpResponse);

}
