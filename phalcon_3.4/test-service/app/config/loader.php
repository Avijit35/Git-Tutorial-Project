<?php

use Phalcon\Loader;

require_once __DIR__ . "/../common/library/vendor/autoload.php";

$loader = new Loader();

/**
 * Register Namespaces
 */
$loader->registerNamespaces([
    'Service\Models' => APP_PATH . '/common/models/',
    'Service\Library'        => APP_PATH . '/common/library/',
]);

/**
 * Register module classes
 */
$loader->registerClasses([
    'Service\Modules\Api\Module' => APP_PATH . '/modules/api/Module.php'
]);

$loader->register();
