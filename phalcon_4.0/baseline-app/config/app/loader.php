<?php

use Phalcon\Loader;

require_once "/usr/share/php/JsonSchema/autoload.php";
require_once BASE_PATH . "/lib/composer/vendor/autoload.php";

$loader = new Loader();

/**
 * Register Namespaces
 */
$loader->registerNamespaces([
    'App\Common\Library'        => APP_PATH . '/common/library/',
]);

/**
 * Register module classes
 */
$loader->registerClasses([
    'App\Modules\Api\Module' => APP_PATH . '/modules/api/Module.php'
]);

$loader->register();
