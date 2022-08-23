<?php

namespace App\Modules\Api\Representation;

use App\Modules\Api\Representation\EntityOutput;

/**
 * Entity
 * This description is related to Entity
 */
class EntityRetrieveOutput extends OutputBase
{
	public $resource;

	function __construct()
	{
		$this->resource = new EntityOutput();
	}
}
