<?php

namespace App\Modules\Api\Common\Library;


class OutputHandlingBase
{
	public static function getEntityIdNamedListAsUrlParam(&$entityId)
	{
		$tmpValue = $entityId;
		
		if(count($entityId) === 1)
		{
			foreach($entityId as $attr => $value)
			{
				$tmpValue = null;
				$tmpValue = $value;
				break;
			}
		}
		
		$entityId = $tmpValue;
		
		return;
	}
	
	public static function genResourceUrl($di, $moduleVersion, $resourceName, $entityId, $queryParams = null)
	{
		return RequestHandling::getBaseUri($di) . '/' . urlencode($di->getRouter()->getModuleName()) . '/' . urlencode($moduleVersion) . '/' . urlencode(strtolower($resourceName)) . '/' . urlencode((is_array($entityId))?json_encode($entityId):$entityId) . (!empty($queryParams)?'?' . $queryParams:null);
	}
	
	public static function formatFrontendOutputResource($di, $entityId, $sourceEntity, &$frontendEntity, $moduleVersion, $frontendAttributesEntity = null, $attributesMap = null, $frontendOutputAttributes = array(), $frontendReferencesEntity = null, $referencesMap = array(), $frontendOutputReferences = array(), $outputOptions = array())
	{
		if(empty($entityId))
		{
			$frontendEntity = null;
			return;
		}
		
		if(empty($sourceEntity) || empty($attributesMap) || empty($frontendOutputAttributes))
		{
			$frontendAttributesEntity = null;
		}
		
		if(empty($sourceEntity) || empty($referencesMap) || empty($frontendOutputReferences))
		{
			$frontendReferencesEntity = null;
		}
		
		foreach($attributesMap as $sourceKey => $frontendKey)
		{
			// Remove mapping if source property or frontend attribute property does not exist
			if( ! property_exists($sourceEntity, $sourceKey) || ! property_exists($frontendAttributesEntity, $frontendKey) )
			{
				unset($attributesMap[$sourceKey]);
				continue;
			}
			
			$frontendAttributesEntity->$frontendKey = $sourceEntity->$sourceKey;
		}
		
		foreach($frontendOutputAttributes as $key => $frontendKey)
		{
			// Remove from list if corresponding frontend attribute property does not exist
			if( ! property_exists($frontendAttributesEntity, $frontendKey) )
			{
				unset($frontendOutputAttributes[$key]);
			}
		}
		
		foreach ($frontendAttributesEntity as $property => $value)
		{
			// Remove properties from frontend entity attributes for which values are not mapped
			if( ! in_array($property, $attributesMap) )
			{
				unset($frontendAttributesEntity->$property);
			}
			
			// Remove properties from frontend entity attributes which do not need to part of output
			if( ! in_array($property, $frontendOutputAttributes) )
			{
				unset($frontendAttributesEntity->$property);
			}
		}
		
		foreach ($referencesMap as $referenceName => $frontendRelation)
		{
			// Remove mapping if frontend reference property corresponding to reference map does not exist
			if( ! property_exists($frontendReferencesEntity, $referenceName) )
			{
				unset($referencesMap[$referenceName]);
				continue;
			}
			
			$frontendRelationEntityAttributeList = $frontendRelation['entity']['attributeList'];
			
			$frontendRelationResourceName = $frontendRelation['resource']['name'];
			$frontendRelationResourceModelSourceName = $frontendRelation['resource']['modelSourceName'];
			$frontendRelationResourceAttributeList = $frontendRelation['resource']['attributeList'];
			
			$frontendRelationResourceId = array();
				
			foreach($frontendRelationResourceAttributeList as $index => $resourceIdAttr)
			{
				if( ! property_exists($sourceEntity, $frontendRelationEntityAttributeList[$index]) )
				{
					unset($referencesMap[$referenceName]);
					continue 2;
				}
				
				$frontendRelationResourceId[$resourceIdAttr] = $sourceEntity->{$frontendRelationEntityAttributeList[$index]};
			}
			
			self::getEntityIdNamedListAsUrlParam($frontendRelationResourceId);
			
			if(empty($frontendRelationResourceId))
			{
				$frontendReferencesEntity->$referenceName = null;
				continue;
			}
			
			$frontendReferencesEntity->$referenceName->id = $frontendRelationResourceId;
			$frontendReferencesEntity->$referenceName->link = self::genResourceUrl($di, $moduleVersion, $frontendRelationResourceName, $frontendRelationResourceId);
		}
		
		foreach ($frontendOutputReferences as $key => $referenceName)
		{
			// Remove from list if corresponding frontend reference property does not exist
			if( ! property_exists($frontendReferencesEntity, $referenceName) )
			{
				unset($frontendOutputReferences[$key]);
			}
		}
		
		foreach ($frontendReferencesEntity as $property => $value)
		{
			// Remove properties from frontend entity references for which values are not mapped
			if( ! array_key_exists($property, $referencesMap) )
			{
				unset($frontendReferencesEntity->$property);
			}
			
			// Remove properties from frontend entity references which do not need to part of output
			if( ! in_array($property, $frontendOutputReferences) )
			{
				unset($frontendReferencesEntity->$property);
			}
		}
		
		self::getEntityIdNamedListAsUrlParam($entityId);
		
		$frontendEntity->id = $entityId;
		$frontendEntity->link = self::genResourceUrl($di, $moduleVersion, $di->getRouter()->getControllerName(), $entityId);
		$frontendEntity->attributes = $frontendAttributesEntity;
		$frontendEntity->references = $frontendReferencesEntity;
		
		return;
	}
}
