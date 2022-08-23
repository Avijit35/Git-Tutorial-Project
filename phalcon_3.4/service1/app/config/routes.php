<?php

$uri_parts=explode('/', $_SERVER['REQUEST_URI']);
$uri_version=$uri_parts[2];

$router = $di->getRouter();

foreach ($application->getModules() as $key => $module) {
    $namespace = preg_replace('/Module$/', strtoupper($uri_version) . '\\' . 'Controllers', $module["className"]);
    /**$router->add('/'.$key.'/:namespace/:params', [
        'namespace' => $namespace,
        'module' => $key,
        'controller' => 'index',
        'action' => 'index',
        'params' => 2
    ])->setName($key);**/
    $router->add('/'.$key.'/:namespace/:controller/:params', [
        'namespace' => $namespace,
        'module' => $key,
        'controller' => 2,
        'action' => 'index',
        'params' => 3
    ]);
    /**$router->add('/'.$key.'/:namespace/:controller/:action/:params', [
        'namespace' => $namespace,
        'module' => $key,
        'controller' => 2,
        'action' => 3,
        'params' => 4
    ]);**/
$router->add('/'.$key.'/:namespace/:controller', [
        'namespace' => $namespace,
        'module' => $key,
        'controller' => 2,
        'action' => 'index'
    ]);
}
