<?php

namespace App\Modules\Api;

use Phalcon\Di\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Config;
use Phalcon\Mvc\Model\Metadata\Memory as ModelMetaDataAdapter;
use Phalcon\Mvc\View;
use Phalcon\Cache\Backend\File as CacheBackend;
use Phalcon\Cache\Frontend\Igbinary as CacheFrontend;
use Phalcon\Logger\Adapter\File as FileLogAdapter;
use Phalcon\Security as Security;
use Phalcon\Security\Random as Random;

class Module implements ModuleDefinitionInterface
{
    /**
     * Registers an autoloader related to the module
     *
     * @param DiInterface $di
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
        if($di->getRouter()->wasMatched())
        {
		      $moduleVersion = explode('/', $di->getRequest()->getURI(), 5)[2];
        }
        
        if( ! empty($moduleVersion) )
        {
		      $loader = new Loader();

		      $loader->registerNamespaces([
							'App\Modules\Api\Models'      		=> BASE_PATH . '/models/modules/' . $di->getRouter()->getModuleName() . '/',
		          'App\Modules\Api\Representation'	=> BASE_PATH . '/representation/modules/' . $di->getRouter()->getModuleName() . '/'  . $moduleVersion . '/',
		          'App\Modules\Api\Common\Library'	=> __DIR__ . '/common/library/',
		          'App\Modules\Api\Controllers'			=> __DIR__ . '/' . $moduleVersion . '/controllers/',
		          'App\Modules\Api\Rules'      			=> __DIR__ . '/' . $moduleVersion . '/rules/',
		          'App\Modules\Api\Backend'      		=> __DIR__ . '/' . $moduleVersion . '/backend/'
		      ]);
		      
		      $loader->register();
        }
    }

    /**
     * Registers services related to the module
     *
     * @param DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {
			$module_name = $di->getRouter()->getModuleName();
			/**
			 * Try to load local configuration
			 */
			if (file_exists(BASE_PATH . '/config/modules/' . $module_name . '/config.php')) {
					
					$config = $di['config'];
					
					$override = new Config(include BASE_PATH . '/config/modules/' . $module_name . '/config.php');

					if ($config instanceof Config) {
						  $config->merge($override);
					} else {
						  $config = $override;
					}
			}

			/**
			 * Database connection is created based in the parameters defined in the configuration file
			 */
			 
			/**
			 * Database connection is created based in the parameters defined in the configuration file
			 */
			$di->set('db', function () {
					$config = $this->getConfig();

					$class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
					$params = [
							'host'     => $config->database->host,
							'username' => $config->database->username,
							'password' => $config->database->password,
							'dbname'   => $config->database->dbname,
							'charset'  => $config->database->charset,
							'options'  => [
						  								\PDO::ATTR_EMULATE_PREPARES => false
        										]
					];

					if ($config->database->adapter == 'Postgresql') {
							unset($params['charset']);
					}

					$connection = new $class($params);

					return $connection;
			});
			
			/**
			 * If the configuration specify the use of metadata adapter use it or use memory otherwise
			 */
			$di->set('modelsMetadata', function () 
			{
				return new ModelMetaDataAdapter();
			});

			/**
			 * Setting up the view component
			 */
			$di->set('view', function () {
					$view = new View();
					$view->setDI($this);

					return $view;
			});
			
			$di->set('logger', function () 
			{
				return FileLogAdapter($this->getConfig()['module']['logDir'] . '/' . 'messages');
			});
			
			/**
			 * Setting up the security component
			 */
			$di->set(
					'security',
					function () {
						  $security = new Security();

						  // Set the password hashing factor to 12 rounds
						  // $security->setWorkFactor(12);

						  return $security;
					}
			);

			/**
			 * Setting up the random component
			 */			 
			$di->set(
					'random',
					function () {
						  return new Random();
					}
			);
    }
}
