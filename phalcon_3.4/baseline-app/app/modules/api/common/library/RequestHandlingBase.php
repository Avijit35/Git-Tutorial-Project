<?php

namespace App\Modules\Api\Common\Library;

use JsonSchema\Validator as Validator;

class RequestHandlingBase
{
	public static function getBaseUri($di)
	{
		return $di->getRequest()->getScheme() . '://' . $di->getRequest()->getServerName();
	}

	public static function validate($frontendInputEntity, $frontendInputSchemaUri)
	{
		$validator = new Validator;
		
		$validator->check($frontendInputEntity, json_decode(file_get_contents($frontendInputSchemaUri)));
		
		return $validator;
	}

	public static function authenticate($credentials)
	{
		return array('returnCode' => 0, 'returnStatus' => null, 'authenticatedEntity' => array());
	}
	
	public static function authorize($authPayload = null, $acl = null)
	{
		return true;
	}
	
	public static function getEntityIdUrlParamAsNamedList($entityIdUrlParam, $entityIdAttr)
	{
		$entityId = null;
		
		if( ! empty($entityIdUrlParam) )
		{
			$entityId = json_decode($entityIdUrlParam, true);
			
			if( ! is_array($entityId) )
			{
				$entityId = array();
				
				$entityId[$entityIdAttr[0]] = $entityIdUrlParam;
			}
		}
		
		return $entityId;
	}
}
