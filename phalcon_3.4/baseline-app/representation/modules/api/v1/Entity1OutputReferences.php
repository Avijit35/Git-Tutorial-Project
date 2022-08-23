<?php

namespace App\Modules\Api\Representation;

/**
 * Entity
 * This description is related to Entity
 */
class Entity1OutputReferences
{
	public $entity_parent;
	
	public static function map()
	{
		// Return reference mapping to a frontend resource
		return array( // Reference name  Backend or source entity attributes mapped to a frontend resource id attributes
									'entity_parent'	=> [
																			'entity' => [
																										'attributeList' => [
																																				'entity_parent'
																																	 		 ]
																									], 
																			'resource' => [
																											'name' 			=> 'entity1-parent', 
																											'modelSourceName' => 'entity1_parent',
																											'attributeList' => [
																																					'id'
																																	 		   ]
																										]
																		]
		            );
	 }
	 
	public static function profile($name = 'preview')
	{
		// List of frontend references as per profile
		$list = array();
		            
		if($name == 'full')
		{
			$list = array(
										'entity_parent'
									);      	
		}
			
		return $list;
	}
    
	function __construct()
	{
		foreach ($this as $property => $value)
		{
			$this->$property = new EntityOutputBase();
		}
	}
}
