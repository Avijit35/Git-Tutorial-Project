<?php

namespace Service\Modules\Api\V1\Backend;

use Phalcon\Cache\Backend\File as CacheBackendFile;
use Phalcon\Cache\Frontend\Data as CacheFrontData;
use Phalcon\DI\FactoryDefault as Di;
use Service\Modules\Api\V1\Models\Order as PersistedEntity;
use Service\Modules\Api\V1\Representation\OrderRepresentationMapping as EntityRepresentationMapping;

class OrderBackend
{
  
	private $libDi;
	private $cacheNamespace;
	private $entityType;
	private $entityIdAttr;
				
	function __construct()
	{
	
		$this->cacheNamespace = 'Service1';
		$this->entityType = 'Order';
		$this->entityIdAttr = 'order_id';
	
		// Create an output cache	
		$frontCache = new CacheFrontData([
															'lifetime' => 172800,
													]);
		// Set the cache directory
		$backendOptions = [
											"cacheDir" => __DIR__ . '/../cache/' . $this->entityType . '/',
									];

		// Create the File backend
		$this->cache = new CacheBackendFile($frontCache, $backendOptions);

		$this->libDi = new Di();
	}
	
	// The data are created from cache //
	public function create($frontendInputEntity)
	{
		$backendEntity = new PersistedEntity();
		$entityMap =  EntityRepresentationMapping::attributeMap();
		$entityIdAttr = $this->entityIdAttr;
		
		foreach ($entityMap as $key => $value)
		{
			if (!empty($frontendInputEntity->$value))
			{
				$backendEntity->$key = $frontendInputEntity->$value;
			}
		}
		
		if ($backendEntity->create())
		{
			return $backendEntity->$entityIdAttr;
		}
		
		return null;
	} 	
	
	//the entity will be retrieved from cache//
	public function retrieve($entityId = null, $refresh = false)
	{
		$backendEntity = null;
		$entityIdAttr = $this->entityIdAttr;
				
		$backendEntity = $this->cache->get($entityId);

		if ($backendEntity == null || $refresh == true)
		{
			$backendEntity = PersistedEntity::findFirst($entityIdAttr . " = " . $entityId);
			
			$this->cache->save($backendEntity->$entityIdAttr, $backendEntity);
		}
		
		return $backendEntity;
	}
	
	public function retrieveMulti($filter = null)
	{
		$backendEntityIds = null;
		
		foreach (PersistedEntity::find($filter) as $backendEntity)
		{
			$backendEntityIds[] = $backendEntity->$entityIdAttr;
		}

		return $backendEntityIds;
	}
				
	//the data are updated from cache//
	public function update($entityId, $frontendInputEntity)
	{	
		$entityIdAttr = $this->entityIdAttr;
		$backendEntity = PersistedEntity::findFirst($entityIdAttr . " = " . $entityId);
		
		if($backendEntity == null)
		{
			return false;
		}
		
		$entityMap =  EntityRepresentationMapping::attributeMap();
		
		foreach ($entityMap as $key => $value)
		{
			if (!empty($frontendInputEntity->$value))
			{
				$backendEntity->$key = $frontendInputEntity->$value;
			}
		}
		
	 	if($this->cache->exists($backendEntity->$entityIdAttr))
	 	{
	 	
		 	if(!$this->cache->delete($backendEntity->$entityIdAttr))
			{
				return false;
			}
	 	}
		
		if ($backendEntity->update())
		{
			return true;
		}
	 
	 	return false;
	}
	
	//the data are deleted from cache//
	public function delete($entityId)
	{
		$entityIdAttr = $this->entityIdAttr;
		
	 	if($this->cache->exists($entityId))
	 	{
		 	if(!$this->cache->delete($entityId))
			{
				return false;
			}
	 	}
		
		$backendEntity = PersistedEntity::findFirst($entityIdAttr . " = " . $entityId);
		
		if($backendEntity == null)
		{
			return false;
		}
	 	
		if($backendEntity->delete())
		{
			return true;
		}
			
		return false;
	}
}
