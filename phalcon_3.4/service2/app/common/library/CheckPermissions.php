<?php

namespace Blive\Library;

use Phalcon\Cache\Backend\File as BackendFile;
use Phalcon\Cache\Frontend\Data as FrontData;
use Phalcon\DI\FactoryDefault as Di;
use Phalcon\Db as Db;
use Phalcon\Db\Column;

class CheckPermissions
  {
  
  	private $cache=null;
  	private $libDi;
  	private $member_role;
  	public  $connection;
  	
   	function __construct($connection)
			 {
				    $this->connection = $connection;
							 // Create an output cache
						$frontCache = new FrontData([
				    'lifetime' => 172800,
						]);
								// Set the cache directory
						$backendOptions = [
								"cacheDir" => '../cache',
						];

						// Create the File backend
						$this->cache = new BackendFile($frontCache, $backendOptions);

						$content = $this->cache->start("my-cache");
						
						$this->libDi = new Di();
						
			 }
			    ///Loading the roles///
   				public function loadMemberRoles($member_id)
     					{
     					   //The cachekey will be generated//
								 $cacheKey = 'member_roles' . '_' . $member_id;
							
								 $cache_data = $this->cache->get($cacheKey);
									
								 echo "Got beyond cache get for cache key: " . $cacheKey;
								/////////Checking the data in cache///////
									if ($cache_data == null)///to check whether the data exists in cache or not ...if not then it will go to db and then retrieve from it///////////
											 {
											 		echo "Before db call";
											 		////checking the data in db via procedure call, then we can fetch the data////
													$prepared_stmt = $this->connection->prepare("call retrieve_member_role(:member_id,@op_error_code, @op_error_text)");
													$db_resultset = $this->connection->executePrepared
													($prepared_stmt,
													 		['member_id' => $member_id], ['member_id' => Column::BIND_PARAM_INT]
													);

													$db_resultset->setFetchMode(Db::FETCH_ASSOC);
													$retrieved_data=$db_resultset->fetchAll();////retrieving the data from db

													$this->cache->save($cacheKey, $retrieved_data);//storing it in cache
											 }
								 else
											 {
													$retrieved_data = $cache_data;
											 }
	
	
	
	
	
							return $retrieved_data;
     			} 
            //Based on the roles loaded we will check whether the entity is allow to carry out that function//
     				public function isAllowed($requester_member_id,$entity_type,$urlparams,$operation)
 								{
									 echo 'inside isAllowed';
									 print_r($urlparams);
									 print_r($requester_member_id);
									 $this->member_role = $this->loadMemberRoles($requester_member_id);//roles are loaded
									 //if the entity_type is member then we will go to the operation 
									 if($entity_type == 'member')
										 	{
										 		switch($operation)
											 			 {
											 			   //if member_id is retrieved, then it will return true// 
															 case 'RETRIEVE':

																		 return true;
															
															//if the requester_member_id is empty,then it will return true//
															 case 'CREATE':
																 if(empty($requester_member_id))
																		 {
																			 return true;
																		 } /////via authentication/////
																//if the url parameter with index 0 is equal to requester_member_id, then it will return true//		 
															 case 'UPDATE':
																	 if($urlparams[0]== $requester_member_id)
																		 {
																			 return true;
																		 }
																//if the url parameter with index 0 is equal to requester_member_id, then it will return true//			 
															 case 'DELETE':
																	 if($urlparams[0]== $requester_member_id)      
																		 {
																			 return true;
																		 }
														 }  
											}
										 //if the entity_type is member_relation, then we will go to the operation 	
                     elseif($entity_type == 'member_relation')
   											{
													 switch($operation)
															 {
															  //if member_id is retrieved, then it will return true// 
																 case 'RETRIEVE':
																			 return true; 
																//if we create the member,then it will return true//		 
																 case 'CREATE':
																			 return true;
																			 
																 //if the url parameter with index 0 is equal to requester_member_id, then it will return true//			 
																 case 'UPDATE':
																	 if($urlparams[0] == $requester_member_id)
																		 {
																			 return true;
																		 }

																	 return false;
																	 
																	//if the url parameter with index 0 is equal to requester_member_id, then it will return true//	 	
																 case 'DELETE':
																			 return true;
																 }  
													}
										//if the entity_type is live_channel, then we will go to the operation			
     								elseif($entity_type == 'live_channel')
  											 {
														 $cache_manager=new BroadcastData();// a new broadcast is created in cache_manager
														 switch($operation)
														 {
														   //we want to fetch the broadcast,it will return true//
															 case 'RETRIEVE':
																		 return true;
																		 
												       //when we create the broadcast,then it will return true//		 
															 case 'CREATE':
																		 return true;
																		 
															 //	we will get the data from cache//			 
															 case 'UPDATE':
																 $all_results = $cache_manager->getFromCache($entity_type, $urlparams[0],$this->connection);
																 //if all_results with array [][] is equal to requested_member_id, then its true else false//
																 if($all_results[0]['member_id'] == $requester_member_id)
																	 {
																		 return true;
																	 }	
																	return false; 
																	
																//if all_results with array [][] is equal to requested_member_id, then its true else false//		
															 case 'DELETE':
																 $all_results = $cache_manager->getFromCache($entity_type, $urlparams[0],$this->connection);
																 if($all_results[0]['member_id'] == $requester_member_id)      
																	 {
																		 return true;
																	 }
																 return false;  
															 }
  											  } 
  										//if the entity_type is member_transaction, then we will go to the operation  
    								 elseif($entity_type == 'member_transaction')
   												{
															 switch($operation)
															 {
															  //if the url parameter with index 0 is not equal to requester_member_id, then it will return true//
																 case 'RETRIEVE':
																	 if($urlparams[0]!= $requester_member_id)
																		 {
																			 return true;
																		 } 
                                 
                                 //when we create the member_transaction,then it will return true//	
																 case 'CREATE':
																			 return true;
																		 /////via authentication/////
																
																 case 'UPDATE':
																 //if the url parameter with index 0 is equal to requester_member_id, then it will return true//
																	 if($urlparams[0] == $requester_member_id)
																		 {
																			 return true;
																		 }
																 
																 //if the url parameter with index 0 is equal to requester_member_id, then it will return true//
																 case 'DELETE':
																	 if($urlparams[0] == $requester_member_id)      
																		 {
																			 return true;
																		 }	
																} 
																return false; 
													 }              
					 /////get user roles///pass requester member_id/////
				 				} 	  
				 	
		}        
                    
