<?php

declare(strict_types=1);

namespace Core\Exceptions;

/**
 * The exception that is thrown by the {@see \Core\DI\Container DI Container} when an instance
 * of the request type cannot be created (due to the class being not found, autowiring failure, etc.).
 * @see \Core\DI\Container::createByType()
 */
class InstanceCreationException extends \RuntimeException
{

}
