<?php

// TODO: require autoload

// see https://www.php.net/manual/en/features.commandline.webserver.php

// $_SERVER['DOCUMENT_ROOT']
// $_SERVER['REMOTE_ADDR']
// $_SERVER['REQUEST_METHOD']
// $_SERVER['REQUEST_URI']

// TODO: define DEBUG true or false
// TODO: load config
// TODO: load assets.json

echo "<pre>";
var_dump(php_sapi_name());
var_dump($_SERVER);
var_dump($_GET);
var_dump($_POST);
var_dump($_REQUEST);
echo "</pre>";
