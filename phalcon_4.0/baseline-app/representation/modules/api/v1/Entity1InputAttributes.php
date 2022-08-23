<?php

namespace App\Modules\Api\Representation;


class Entity1InputAttributes
{
	public static function map()
	{
	  // Return attribute mapping from frontend resource => backend entity
		return array(
									'id' 	=> 'id',
									'entity_name' 	=> 'entity_name',
									'entity_attr' 	=> 'entity_attr',
									'entity_extra_attr1' => 'entity_extra_attr1',
									'entity_extra_attr2' => 'entity_extra_attr2',
									'entity_extra_attr3' => 'entity_extra_attr3',
									'entity_parent' 	=> 'entity_parent',
									'created_date' 	=> 'created_date',
									'updated_date' 	=> 'updated_date',
									'status'				=> 'status'
		            );
   }
}
