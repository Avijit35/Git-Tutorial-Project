<?php

namespace App\Modules\Api\Common\Library;

use Phalcon\Db\Column as DbColumn;

class EntityBackendBase
{
	private $persistedEntityClass;
	private $persistedEntityIdAttr;
	private $cache;

	function __construct($modelEntityClass)
	{
		$this->persistedEntityClass = $modelEntityClass;
		$this->persistedEntityIdAttr = ModelsMetaData::$identityAttribute[(new $modelEntityClass())->getSource()];
		$this->cache = new EntityCache($modelEntityClass);
	}
	
	private function getPersistedEntityFilterById($entityId = array())
	{
		if( ! $this->isValidEntityId($entityId) )
		{
			throw \Exception('Entity id is invalid.');
			
			return null;
		}
		
		$conditions = "";
		$bind = array();
		
		foreach($entityId as $attr => $value)
		{
			if(empty($conditions))
			{
				$conditions = $attr . " = :" . $attr . ":";
			}
			else
			{
				$conditions = $conditions . " AND " . $attr . " = :" . $attr . ":";
			}
			
			$bind[$attr] = $value;
		}
		
		return ['conditions' => $conditions, 'bind' => $bind];
	}
	
	private function getCacheKey($entityId = array())
	{
		if( ! $this->isValidEntityId($entityId) )
		{
			throw \Exception('Entity id is invalid.');
			
			return null;
		}
		
		$serializedAttrList = "";
		$serializedValueList = "";
		
		foreach($this->persistedEntityIdAttr as $attr)
		{
			if(empty($serializedAttrList))
			{
				$serializedAttrList = base64_encode($attr);
			}
			else
			{
				$serializedAttrList = $serializedAttrList . "," . base64_encode($attr);
			}
			
			if(empty($serializedValueList))
			{
				$serializedValueList = base64_encode($entityId[$attr]);
			}
			else
			{
				$serializedValueList = $serializedValueList . "," . base64_encode($entityId[$attr]);
			}
		}
		
		return urlencode($serializedAttrList) . "." . urlencode($serializedValueList);
	}
	
	private static function mapInput($inputEntity, &$persistedEntity)
	{
		foreach($inputEntity as $inputEntityAttribute => $value)
		{
			if ( property_exists($persistedEntity, $inputEntityAttribute) )
			{
				$persistedEntity->$inputEntityAttribute = $value;
			}
		}
	}
	
	private function mapOutput($persistedEntity)
	{
		$backendOutputEntity = self::getBackendOutputEntity();
		
		foreach($persistedEntity as $persistedEntityAttribute => $value)
		{
			$backendOutputEntity->$persistedEntityAttribute = $value;
		}
		
		return $backendOutputEntity;
	}
	
	private static function filteredPersistedEntity($persistedEntity, $persistedEntityClass, $filter)
	{
		if(empty($filter))
		{
			$filter = ModelRules::$defaultRetrieveFilter[(new $persistedEntityClass())->getSource()];
		}
		
		foreach((array)$filter as $attr => $listOfValues)
		{
			if( property_exists($persistedEntity, $attr) )
			{
				if( ! $listOfValues == null && ! in_array($persistedEntity->$attr, (array)$listOfValues) )
				{
					return false;
				}
			}
		}
		
		return true;
	}
	
	public static function getBackendInputEntity()
	{
		return new \stdClass();
	}
	
	private static function getBackendOutputEntity()
	{
		return new \stdClass();
	}
	
	public function getEntityId($backendEntity)
	{
		$entityId = array();
		
		foreach($this->persistedEntityIdAttr as $idAttr)
		{
			if( ! property_exists($backendEntity, $idAttr) )
			{
				throw \Exception('Entity id attributes defined in backend entity meta data does not match backend entity.');
				
				return null;
			}
			
			$entityId[$idAttr] = $backendEntity->$idAttr;
		}
		
		return $entityId;
	}
	
	private function isValidEntityId($entityId = array())
	{
		foreach($this->persistedEntityIdAttr as $idAttr)
		{
			if( ! array_key_exists($idAttr, $entityId) )
			{
				return false;
			}
		}
		
		return true;
	}
	
	public function create($inputEntity)
	{
		if(empty($inputEntity))
		{
			return null;
		}
		
		$persistedEntity = new $this->persistedEntityClass();
		
		self::mapInput($inputEntity, $persistedEntity);
		
		if($persistedEntity->create())
		{
			$entityId = $this->getEntityId($this->mapOutput($persistedEntity));
			$dbColumnDataTypes = $persistedEntity->getModelsMetaData()->getDataTypes($persistedEntity);
			
			foreach($entityId as $attr => $value)
			{
				if(in_array($dbColumnDataTypes[$attr], [DbColumn::TYPE_INTEGER, DbColumn::TYPE_BIGINTEGER]) && Utility::isInteger($value))
				{
					$entityId[$attr] = (integer)$value;
				}
			}
			
			if(empty($entityId))
			{
				return null;
			}
			
			if($this->cache->has($this->getCacheKey($entityId)))
			{
				$this->cache->delete($this->getCacheKey($entityId));
			}
			
			return $entityId;
		}
		
		return null;
	} 	
	
	public function retrieve($entityId = null, $filter = null)
	{
		if( ! $this->isValidEntityId($entityId) )
		{
			return null;
		}
		
		$persistedEntity = $this->cache->get($this->getCacheKey($entityId));

		if($persistedEntity == null)
		{
			$persistedEntity = $this->persistedEntityClass::findFirst(self::getPersistedEntityFilterById($entityId));
			
			if( ! empty($persistedEntity) )
			{
				$this->cache->set($this->getCacheKey($entityId), $persistedEntity);
			}
			else
			{
				return null;
			}
		}
		
		if( ! self::filteredPersistedEntity($persistedEntity, $this->persistedEntityClass, $filter) )
		{
			return null;
		}
		
		return $this->mapOutput($persistedEntity);
	}
	
	public function retrieveList($outputOptions = null, $filterCondition = null)
	{
		$backendEntities = null;
		
		if(empty($filterCondition))
		{
			$filterCondition = ModelRules::$defaultRetrieveMultiQueryCondition[(new $this->persistedEntityClass())->getSource()];
		}
		
		foreach($this->persistedEntityClass::find($filterCondition) as $persistedEntity)
		{
			$backendEntity = $this->mapOutput($persistedEntity);
			$entityId = $this->getEntityId($backendEntity);
			
			$tmpEntity = $entityId;
			
			if( ! empty($outputOptions) )
			{
				if( ! empty($outputOptions['format']) && $outputOptions['format'] == 'dereference')
				{
					$tmpEntity = $backendEntity;
				}
			}
			
			$backendEntities[] = $tmpEntity;
		}

		return $backendEntities;
	}
				
	public function update($entityId, $inputEntity, $filter = null)
	{
		if( ! $this->isValidEntityId($entityId) || empty($inputEntity) )
		{
			return null;
		}
		
		$persistedEntity = $this->persistedEntityClass::findFirst(self::getPersistedEntityFilterById($entityId));
		
		if(empty($persistedEntity))
		{
			return null;
		}
		
	 	if($this->cache->has($this->getCacheKey($entityId)))
	 	{
		 	if(!$this->cache->delete($this->getCacheKey($entityId)))
			{
				return false;
			}
	 	}
	 	
		if( ! self::filteredPersistedEntity($persistedEntity, $this->persistedEntityClass, $filter) )
		{
			return null;
		}
		
		foreach($this->persistedEntityIdAttr as $idAttr)
		{
			unset($inputEntity->$idAttr);
		}
		
		self::mapInput($inputEntity, $persistedEntity);
		
		if ($persistedEntity->update())
		{
			return true;
		}
	 
	 	return false;
	}
	
	public function delete($entityId, $options = null, $filter = null)
	{
		if( ! $this->isValidEntityId($entityId) )
		{
			return null;
		}
		
		if(empty($options))
		{
			$options = (new ModelRules())->getDefaultDeleteOption((new $this->persistedEntityClass())->getSource());
		}
			
		$persistedEntity = $this->persistedEntityClass::findFirst(self::getPersistedEntityFilterById($entityId));
		
		if(empty($persistedEntity))
		{
			return null;
		}
		
	 	if($this->cache->has($this->getCacheKey($entityId)))
	 	{
		 	if(!$this->cache->delete($this->getCacheKey($entityId)))
			{
				return false;
			}
	 	}
	 	
		if( ! self::filteredPersistedEntity($persistedEntity, $this->persistedEntityClass, $filter) )
		{
			return null;
		}
	 	
	 	if( ! empty($options['type']) )
	 	{
		 	if( $options['type'] == 'soft' )
		 	{
	 			foreach($options['update'] as $attr => $value)
				{
					if( property_exists($persistedEntity, $attr) )
					{
						$persistedEntity->$attr = $value;
					}
				}
				
				if ($persistedEntity->update())
				{
					return true;
				}
		 	}
	 	}
		
		if($persistedEntity->delete())
		{
			return true;
		}
			
		return false;
	}
}
