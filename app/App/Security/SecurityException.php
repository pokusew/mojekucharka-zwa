<?php

declare(strict_types=1);

namespace App\Security;

use App\RecipesFilter;

/**
 * An exception that can occur during {@see RecipesFilter::getWhere()}.
 */
class SecurityException extends \Exception
{

}
