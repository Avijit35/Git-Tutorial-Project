<?php

namespace App\Modules\Api\Representation;

/**
 * Entity
 * This description is related to Entity
 */
class Entity1OutputAttributes
{
	public $entity_name;
	public $entity_attr;
	public $entity_extra_attr1;
	public $entity_extra_attr2;
	public $entity_extra_attr3;
	public $created_date;
	public $updated_date;
	public $status;

	public static function map()
	{
		// This returns attribute mapping from backend or source entity => frontend resource
		return array(
									'entity_name' 	=> 'entity_name',
									'entity_attr' 	=> 'entity_attr',
									'entity_extra_attr1' => 'entity_extra_attr1',
									'entity_extra_attr2' => 'entity_extra_attr2',
									'entity_extra_attr3' => 'entity_extra_attr3',
									'created_date' 	=> 'created_date',
									'updated_date' 	=> 'updated_date',
									'status'				=> 'status'
		            );
	 }
	 
	public static function profile($name = 'preview')
	{
		// List of frontend attributes as per profile
		$list = array(
									'entity_name',
									'entity_attr',
									'created_date',
									'updated_date',
									'status'
		            );
		            
			if($name == 'full')
			{
				$list = array(
											'entity_name',
											'entity_attr',
											'entity_extra_attr1',
											'entity_extra_attr2',
											'entity_extra_attr3',
											'created_date',
											'updated_date',
											'status'
								    );      	
			}
			
			return $list;
	}
}
