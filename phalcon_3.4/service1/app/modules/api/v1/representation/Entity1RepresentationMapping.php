<?php

namespace Service\Modules\Api\V1\Representation;


class Entity1RepresentationMapping
 {
   /**
     * Independent Column Mapping.
     * Keys are the real names in the table and the values their names in the application
     *
     * @return array
     */
   		public static function attributeMap()
				{
				  return array(
												'entity_id'=>'entityId',
												'entity_name'=>'entityName',
												'entity_attr'=>'entityAttr',
												'created_date'=>'createdDate',
												'updated_date'=>'updatedDate',
												'status'=>'entityStatus'
                      );
         } 
 }                    
