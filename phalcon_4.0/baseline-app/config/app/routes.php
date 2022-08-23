<?php

$router = $di->getRouter();

foreach ($application->getModules() as $key => $module) 
{
	$namespace = preg_replace('/Module$/', 'Controllers', $module["className"]);
	
	if ($key == 'api')
	{
		$routableModuleControllers['v1'] = ['entity1'];
		
		foreach ($routableModuleControllers as $moduleVersion => $moduleControllers)
		{
			foreach ($moduleControllers as $moduleController)
			{
				$router->add('/' . $key . '/' . $moduleVersion . '/' . $moduleController, 
				[
					'module' => $key,
					'namespace' => $namespace,
					'version' => $moduleVersion,
					'controller' => $moduleController,
					'action' => 'index'
				])->via
				(
					[
						'POST',
						'GET'
					]
				);
				
				$router->add('/' . $key . '/' . $moduleVersion . '/' . $moduleController . '/(.+)', 
				[
					'module' => $key,
					'namespace' => $namespace,
					'version' => $moduleVersion,
					'controller' => $moduleController,
					'params' => 1,
					'action' => 'index'
				])->via
				(
					[
						'GET',
						'PUT',
						'DELETE'
					]
				);
			}
		}
	}
}
