<?php

namespace Service\Modules\Api\V1\Rules;

class Entity1Rules
{
	function __construct()
	{
	}
	
	function process($entity)
	{
		return 0;
	}
	
	function processRules($frontendInputEntity)
	{
	  if(empty($frontendInputEntity->entityAttr))
	  {
	     $frontendInputEntity->entityStatus = 'PENDING';
	     return $frontendInputEntity;
	  }
	}
}        
                    
