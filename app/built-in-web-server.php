<?php

/**
 * Returns true if this request should be handled by the PHP built-in web server.
 * True is returned if all these conditions are met:
 * - The script is running inside the PHP built-in web server (i.e. `php_sapi_name() === 'cli-server'`).
 * - The request has no query string ($_SERVER['QUERY_STRING'] is unset).
 * - The $_SERVER['REQUEST_URI'] does not end with '/' (i.e. does not correspond to a dir).
 * @see https://www.php.net/manual/en/features.commandline.webserver.php
 * @return bool true if this request should be handled by the PHP built-in web server
 */
function should_skip_this_request(): bool
{

	if (php_sapi_name() !== 'cli-server') {
		return false;
	}

	if (isset($_SERVER['QUERY_STRING'])) {
		return false;
	}

	// skip the request if it is not a directory and the file exists
	return strlen($_SERVER['REQUEST_URI']) >= 2 && $_SERVER['REQUEST_URI'][-1] !== '/'
		&& file_exists($_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI']);

}
