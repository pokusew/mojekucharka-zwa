<?php

declare(strict_types=1);

namespace Core\Exceptions;

/**
 * The exception that may be thrown by the {@see \Core\App} during request processing.
 *
 * For example, when there is no route for the request.
 * The {@see BadRequestException::$code} is a valid HTTP status code.
 */
class BadRequestException extends \RuntimeException
{

}
