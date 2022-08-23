<?php

namespace App\Modules\Api\Representation;

/**
 * Entity
 * This description is related to Entity
 */
class EntityCreateOutput extends OutputBase
{
	public $resource;

	function __construct()
	{
		$this->resource = new EntityOutputBase();
	}
}
