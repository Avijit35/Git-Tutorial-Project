<?php

use Phalcon\Loader;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlResolver;


/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include BASE_PATH . "/config/app/config.php";
});

/**
 * Registering a router
 */
$di->setShared('router', function () {
		$router = new Router(false);

		$router->notFound(
												[
													'namespace' => 'App\Common\Library',
													'controller' => 'Routenotdefined',
													'action' => 'index'
												]
										);

		$router->setDefaultModule('api');

		return $router;
});

/**
* Set the default namespace for dispatcher
*/
$di->setShared('dispatcher', function() {
		$dispatcher = new Dispatcher();
		$dispatcher->setDefaultNamespace('App\Modules\Api\Controllers');
		return $dispatcher;
});

/**
 * The URL component is used to generate all kinds of URLs in the application
 */
$di->setShared('url', function () {
		$config = $this->getConfig();

		$url = new UrlResolver();
		$url->setBaseUri($config->application->baseUri);

		return $url;
});
