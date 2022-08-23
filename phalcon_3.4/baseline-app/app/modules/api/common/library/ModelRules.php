<?php

namespace App\Modules\Api\Common\Library;

class ModelRules
{
	// Maps model source name to it's identity attribute
	public static $defaultRetrieveFilter = 
	[	
		'entity1' 													=> ['status' => ['active']]
	];

	public static $defaultRetrieveMultiQueryCondition = 
	[	'entity1'														=> [ 'conditions'	=> 'status = :status:', 'bind' => ['status' => 'active']	]
	];
																
	// Maps model source name to it's identity attribute
	public static function getDefaultDeleteOption($source)
	{
		$defaultDeleteOption = 
		[	
			'entity1'														=> [ 'type' => 'soft', 'update' => [ 'status' => 'deleted' ] ]
		];
		
		return $defaultDeleteOption[$source];
	}
}
