<?php

namespace Service\Modules\Api\V1\Representation;


class UserRepresentationMapping
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
												'user_id'=>'userId',
												'username'=>'userName',
												'userpass'=>'userPass',	
												'profile_name'=>'profileName',
												'first_name'=>'firstName',
												'last_name'=>'lastName',
												'email'=>'emailId',
												'sex'=>'gender',
												'dob'=>'birthDate',
												'date_of_joining'=>'joiningDate',
												'status'=>'userStatus',
												'created_date'=>'createdDate',
												'updated_date'=>'updatedDate'
                      );
         } 
 }                    
