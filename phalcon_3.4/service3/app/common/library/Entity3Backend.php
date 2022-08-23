<?php

namespace Service3\Library;

use Phalcon\Cache\Backend\File as CacheBackendFile;
use Phalcon\Cache\Frontend\Data as CacheFrontData;
use Phalcon\DI\FactoryDefault as Di;
use Service3\Model\Entity3 as PersistedEntity;

class Entity3Backend
{
  
	private $libDi;
	private $cacheNamespace = 'Service3';
	private $entityType = 'Entity3';
	private $entityIdAttr = 'entity_id';
				
	function __construct()
	{
		// Create an output cache
		$frontCache = new CacheFrontData([
															'lifetime' => 172800,
													]);
		// Set the cache directory
		$backendOptions = [
											"cacheDir" => '../cache' . '/' . $cacheNamespace . '/' . $entityType,
									];

		// Create the File backend
		$this->cache = new CacheBackendFile($frontCache, $backendOptions);

		$this->libDi = new Di();
	}
	
	//the entity will be retrieved from cache//
	public function retrieve($entityId = null, $refresh = false)
	{
		$backendEntity = null;
				
		$backendEntity = $this->cache->get($entityId);

		if ($backendEntity == null || $refresh == true)
		{
			$backendEntity = PersistedEntity::findFirst($backendEntity->$entityIdAttr . " = " . $entityId);
			
			$this->cache->save($backendEntity->$entityIdAttr, $backendEntity);
		}

		return $backendEntity;
	}
	
	public function retrieveMulti($filter = null)
	{
		foreach (PersistedEntity::find($filter) as $backendEntity)
		{
			$backendEntityIds[] = $backendEntity->$entityIdAttr;
			
			$this->cache->save($backendEntity->$entityIdAttr, $backendEntity);
		}

		return $backendEntityIds;
	}
				 
	// The data are created from cache //
	public function create($entity)
	{
		$backendEntity = new PersistedEntity();
		
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
	public function update($entityId, $entity)
	{	
		$backendEntity = PersistedEntity::findFirst($backendEntity->$entityIdAttr . " = " . $entityId);
		
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
	public function delete($entityId)
	{
		$backendEntity = PersistedEntity::findFirst($backendEntity->$entityIdAttr . " = " . $entityId);
		
	 	if($this->cache->delete($backendEntity->$entityIdAttr) && $backendEntity->delete())
		{
			return true;
		}
			
		return false;
	} 
}        
                    
