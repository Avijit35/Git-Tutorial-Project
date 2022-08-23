<?php

namespace App\Modules\Api\Common\Library;

use Phalcon\Di as Di;
use Phalcon\Cache\Adapter\Stream as Stream;
use Phalcon\Storage\SerializerFactory;

class EntityCacheBase extends Stream
{
	private $cacheConfig;
	
	function __construct($modelEntityClass)
	{
		$sharedConfig = Di::getDefault()->getConfig();
		
		$this->cacheConfig = $sharedConfig['module']['cache'];
		
		// Set the cache directory, it is important to keep the '/' at the end of the path
		$cacheDir = $sharedConfig['module']['cacheDir'] . '/' . (new $modelEntityClass())->getSource() . '/';
		
		// Create the cache directory if it doesn't exist							
		if ( ! file_exists($cacheDir) )
		{
			@mkdir($cacheDir, 0755);
		}

		$serializerFactory = new SerializerFactory();

    $adapterOptions = [
                    'defaultSerializer' => 'Json',
                    'lifetime'          => (! empty($this->cacheConfig['lifetime']) )?$this->cacheConfig['lifetime']:600,
                    'storageDir'        => $cacheDir,
               ];
               
		parent::__construct($serializerFactory, $adapterOptions);
	}
	
	public function get($key, $defaultValue = null)
	{
		if( ! empty($this->cacheConfig['noCache']) )
		{
			switch($this->cacheConfig['noCache'])
			{
				case 'refresh': 
												if($this->has($key))
												{
													$this->delete($key);
												}
												break;
			}
			
			return null;
		}
		
		return parent::get($key);	
	}
	
	public function set($key, $value, $ttl = NULL): bool
	{
		if( ! empty($this->cacheConfig['noCache']) )
		{
			switch($this->cacheConfig['noCache'])
			{
				case 'enable': 
												if($this->has($key))
												{
													$this->delete($key);
												}
												break;
			}
			
			return true;
		}
		
		return parent::set($key, $value);
	}
}
