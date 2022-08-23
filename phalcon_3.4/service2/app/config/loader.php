<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * Register Namespaces
 */
$loader->registerNamespaces([
    'Blive\Models' => APP_PATH . '/common/models/',
    'Blive\Library'        => APP_PATH . '/common/library/',
]);

/**
 * Register module classes
 */
$loader->registerClasses([
    'Modules\Api\Module' => APP_PATH . '/modules/api/Module.php'
]);

$loader->register();
