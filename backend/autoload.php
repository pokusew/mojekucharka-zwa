<?php

// simple class autoloader
// in the future, we will replace it with the one generated by Composer's

// https://www.php.net/autoload
// https://www.php.net/manual/en/function.spl-autoload-register.php
// https://getcomposer.org/doc/01-basic-usage.md
// https://www.php-fig.org/psr/psr-4/

spl_autoload_register(function ($class_name) {
	require_once $class_name . '.php';
});
