<?php

declare(strict_types=1);

namespace Core\Response;

use Core\Http\HttpRequest;
use Core\Http\HttpResponse;

/**
 * Redirects to new URI.
 */
class JsonResponse implements Response
{

	public function send(HttpRequest $httpRequest, HttpResponse $httpResponse)
	{
		// TODO: Implement send() method.
	}

}
