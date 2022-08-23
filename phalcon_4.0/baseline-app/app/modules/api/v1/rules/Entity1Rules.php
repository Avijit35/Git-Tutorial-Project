<?php

namespace App\Modules\Api\Rules;

use App\Modules\Api\Common\Library\ModelsMetaData as ModelsMetaData;
use App\Modules\Api\Common\Library\EntityBackend as EntityBackend;
use App\Modules\Api\Common\Library\RequestHandling as CommonRequestHandling;

use App\Modules\Api\Representation\Entity1InputAttributes as EntityInputAttributes;

use App\Modules\Api\Models\Entity1 as EntityModel;


class Entity1Rules
{
	public static function processInputCreate(&$frontendInputEntity)
	{
		return 0;
	}
	
	public static function processInputUpdate(&$frontendInputEntity)
	{
		return 0;
	}
	
	public static function processOutput(&$entity)
	{
		return 0;
	}
	
	private static function mapFrontendInput($frontendInputEntity)
	{
		$backendInputEntity = EntityBackend::getBackendInputEntity();
		$entityModel = EntityModel::class;
		
		foreach (EntityInputAttributes::map() as $frontendKey => $backendKey)
		{
			if ( property_exists($frontendInputEntity, $frontendKey) && property_exists($entityModel, $backendKey) )
			{
				$backendInputEntity->$backendKey = $frontendInputEntity->$frontendKey;
			}
		}
		
		return $backendInputEntity;
	}
	
	public static function getEntityIdUrlParamAsNamedList($entityIdUrlParam)
	{
		return CommonRequestHandling::getEntityIdUrlParamAsNamedList($entityIdUrlParam, ModelsMetaData::$identityAttribute[(new EntityModel())->getSource()]);
	}
	
 	public static function getEntityBackend()
	{
		return new EntityBackend(EntityModel::class);
	}
	
	public static function getEntityId($entity)
	{
		return self::getEntityBackend()->getEntityId($entity);
	}
	
 	public static function entityCreate($frontendInputEntity)
	{
		self::processInputCreate($frontendInputEntity);
		
		return self::getEntityBackend()->create(self::mapFrontendInput($frontendInputEntity));
	}
	
 	public static function entityRetrieve($entityId, $filter = null)
	{
		return self::getEntityBackend()->retrieve($entityId, $filter);
	}
	
 	public static function entityRetrieveList($outputOptions = null, $queryFilterCondition = null)
	{
		return self::getEntityBackend()->retrieveList($outputOptions, $queryFilterCondition);
	}
	
 	public static function entityUpdate($entityId, $frontendInputEntity, $filter = null)
	{
		self::processInputUpdate($frontendInputEntity);
		
		return self::getEntityBackend()->update($entityId, self::mapFrontendInput($frontendInputEntity), $filter);
	}
	
 	public static function entityDelete($entityId, $options = null, $filter = null)
	{
		return self::getEntityBackend()->delete($entityId, $options, $filter);
	}
}
