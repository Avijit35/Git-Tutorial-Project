<?php

namespace Service2\Library;

use Phalcon\Cache\Backend\File as BackendFile;
use Phalcon\Cache\Frontend\Data as FrontData;
use Phalcon\DI\FactoryDefault as Di;
use Entity2\Model\Entity2 as BackendEntity;

class Entity2Backend
{
  
	private $libDi;
	private $cacheNamespace = 'Service2';
	private $entityType = 'Entity2';
	private $backendEntity;
	private $entityIdAttr = 'entity_id';
				
	function __construct()
	{
		// Create an output cache
		$frontCache = new FrontData([
															'lifetime' => 172800,
													]);
		// Set the cache directory
		$backendOptions = [
											"cacheDir" => '../cache' . '/' . $cacheNamespace . '/' . $entityType,
									];

		// Create the File backend
		$this->cache = new BackendFile($frontCache, $backendOptions);

		$content = $this->cache->start($entityType);
		
		$this->libDi = new Di();
	}
	
	//the entity will be retrieved from cache//
	public function retrieveFromBackend($entityId)
	{
		$backendEntity = $this->cache->get($entityId);

		if ($backendEntity === null)
		{
			$backendEntity = BackendEntity::findFirst($entityId);
			
			$this->cache->save($backendEntity->$entityIdAttr, $backendEntity);
		}

		return $backendEntity;
	} 
				 
	// The data are created from cache //
	public function createInBackend($entity)
	{
		$backendEntity = new BackendEntity();
		
		foreach ($entity as $key => $value)
		{
			$backendEntity->$key = $entity[$key];
		}
		
		if ($backendEntity->create())
		{
			$this->cache->save($backendEntity->$entityIdAttr, $backendEntity);
			
			return true;
		}
		
		return false;
	} 	
				
	//the data are updated from cache//		
	public function updateInBackend($entityId, $entity)
	{	
		$backendEntity = BackendEntity::findFirst($entityId);
		
		if($backendEntity == null)
		{
			return false;
		}
		
		foreach ($entity as $key => $value)
		{
			$backendEntity->$key = $entity[$key];
		}
		
		if ($backendEntity->update())
		{
			$this->cache->save($backendEntity->$entityIdAttr, $backendEntity);
			
			return true;
		}
	 
	 	return false;
	}
	
	//the data are deleted from cache//
	public function deleteFromBackend($entityId)
	{
		$backendEntity = BackendEntity::findFirst($entityId);
		
	 	if($this->cache->delete($backendEntity->$entityIdAttr) && $backendEntity->delete())
		{
			return true;
		}
			
		return false;
	} 
}        
                    
