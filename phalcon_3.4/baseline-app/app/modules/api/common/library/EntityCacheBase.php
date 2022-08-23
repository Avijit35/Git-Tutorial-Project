<?php

namespace App\Modules\Api\Common\Library;

use Phalcon\Di as Di;
use Phalcon\Cache\Backend\File as CacheBackend;
use Phalcon\Cache\Frontend\Json as CacheFrontend;

class EntityCacheBase extends CacheBackend
{
	private $options;
	
	function __construct($modelEntityClass)
	{
		$sharedConfig = Di::getDefault()->getConfig();
		
		$this->options = $sharedConfig['module']['cache'];
		
		// Set frontend cache options, default cache life time is 10 mins (600 s)
		$frontCache = new CacheFrontend([
																			'lifetime' => (! empty($this->options['lifetime']) )?$this->options['lifetime']:600,
																		]);
													
		// Set the cache directory, it is important to keep the '/' at the end of the path
		$cacheDir = $sharedConfig['module']['cacheDir'] . '/' . (new $modelEntityClass())->getSource() . '/';
		
		$backendOptions = [
												"cacheDir" => $cacheDir
											];

		// Create the cache directory if it doesn't exist							
		if ( ! file_exists($cacheDir) )
		{
			@mkdir($cacheDir, 0755);
		}

		// Create the File backend
		parent::__construct($frontCache, $backendOptions);
	}
	
	public function get($keyName, $lifetime = NULL)
	{
		if( ! empty($this->options['noCache']) )
		{
			switch($this->options['noCache'])
			{
				case 'refresh': 
												if($this->exists($keyName))
												{
													$this->delete($keyName);
												}
												break;
			}
			
			return null;
		}
		
		return parent::get($keyName);	
	}
	
	public function save($keyName = NULL, $content = NULL, $lifetime = NULL, $stopBuffer = NULL)
	{
		if( ! empty($this->options['noCache']) )
		{
			switch($this->options['noCache'])
			{
				case 'enable': 
												if($this->exists($keyName))
												{
													$this->delete($keyName);
												}
												break;
			}
			
			return true;
		}
		
		return parent::save($keyName, $content);
	}
}
