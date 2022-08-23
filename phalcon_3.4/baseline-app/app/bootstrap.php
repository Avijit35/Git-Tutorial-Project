<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;

// This a change

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {

    /**
     * The FactoryDefault Dependency Injector automatically registers the services that
     * provide a full stack framework. These default services can be overidden with custom ones.
     */
    $di = new FactoryDefaultNew();

    /**
     * Include environment specific services
     */
    require BASE_PATH . '/config/app/services.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include BASE_PATH . '/config/app/loader.php';

    /**
     * Handle the request
     */
    $application = new Application($di);

    /**
     * Register application modules sjdkfjs;
     */
    $application->registerModules([
        'api' => ['className' => 'App\Modules\Api\Module']
    ]);

    /**
     * Include routes
     */
    require BASE_PATH . '/config/app/routes.php';

    echo str_replace(["\n","\r","\t"], '', $application->handle()->getContent());

} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
