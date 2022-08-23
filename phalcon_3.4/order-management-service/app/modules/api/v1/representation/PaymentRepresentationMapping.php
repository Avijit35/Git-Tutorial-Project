<?php

namespace Service\Modules\Api\V1\Representation;


class PaymentRepresentationMapping
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
												'payment_id'=>'paymentId',
												'payment_type'=>'paymentType',
												'payment_method'=>'paymentMethod',
												'payment_amount'=>'paymentAmount',
												'order_id'=>'orderId'
                      );
         } 
 }                    
