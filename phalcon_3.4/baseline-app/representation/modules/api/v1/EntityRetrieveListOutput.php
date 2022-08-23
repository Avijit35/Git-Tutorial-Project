<?php

namespace App\Modules\Api\Representation;

/**
 * Entity
 * This description is related to Entity
 */
class EntityRetrieveListOutput extends OutputBase
{
	/** @var int The description is related to id of the entity */
	public $resource_list;
    
	function __construct()
	{
		$this->resource_list = array();
	}
}
