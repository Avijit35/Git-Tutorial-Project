<?php

namespace Service\Modules\Api\V1\Representation;


class OrderRepresentationMapping
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
												'order_id'=>'orderId',
												'order_price'=>'orderPrice',
												'order_quantity'=>'orderQuantity',
												'order_status'=>'orderStatus',
												'order_date'=>'orderDate'
                      );
         } 
 }                    
