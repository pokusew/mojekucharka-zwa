<?php

declare(strict_types=1);

namespace Core\Exceptions;

/**
 * The exception that is thrown by the {@see \Core\DI\Container DI Container} when a value cannot
 * be determined using the autowiring algorithm.
 * @see \Core\DI\Container::autowireFunctionParameter()
 * @see \Core\DI\Container::autowireInstanceProperties()
 */
class AutowireException extends \Exception
{

}
