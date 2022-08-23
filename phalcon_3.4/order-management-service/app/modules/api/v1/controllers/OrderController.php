<?php

namespace Service\Modules\Api\V1\Controllers;

use Service\Modules\Api\V1\Backend\OrderBackend as EntityBackend;
use Service\Modules\Api\V1\Representation\OrderCreateOutput as EntityCreateOutput;
use Service\Modules\Api\V1\Representation\OrderRetrieveOutput as EntityRetrieveOutput;
use Service\Modules\Api\V1\Representation\OrderUpdateOutput as EntityUpdateOutput;
use Service\Modules\Api\V1\Representation\OrderDeleteOutput as EntityDeleteOutput;
use Service\Modules\Api\V1\Representation\OrderRepresentationMapping as EntityRepresentationMapping;
use Service\Modules\Api\V1\Models\OrderProduct as MappedEntity;
//use Service\Modules\Api\V1\Rules\UserRules as EntityRules;

class OrderController extends \Phalcon\Mvc\Controller
{
	private $entityBackend;
	private $frontendOutputEntity;
	
  public function indexAction()
  {
		$router = $this->di->getShared('router'); // Obtain the shared
		///print_r($router);
		$urlParams = $router->getParams();
		print_r($urlParams);
		$queryParams = $this->request->getQuery(); // Obtain any query parameters from URL
		//print_r($queryParams);
		$entityId = (!empty($urlParams[0]))?$urlParams[0]:null; // Set the entity id if present in the URL, POST method should not contain this parameter
		print_r($entityId);
		$entityProduct = (!empty($urlParams[1]))?$urlParams[1]:null;
		print_r($entityProduct);
		$entityProductId = (!empty($urlParams[2]))?$urlParams[2]:null;
		print_r($entityProductId);
		$this->entityBackend = new EntityBackend(); // Create an instance of entity storage
		//print_r($this->entityBackend);
		//$this->frontendOutputEntity = null;
		//$entityRules = new EntityRules();
		
		echo 'calling of orderproductrelation';
		if($entityProduct == 'product')
		{
		   $this->orderProductRelation($entityId,$entityProductId);
		   
		   return 0;
		}  
		   echo 'inside order flow'; 		
			 if($this->request->isPost() && empty($entityId)) // Process a POST request
					{	
						$operation = 'CREATE';
						$frontendInputEntitySchemaURI = '/schemas/order.create.input.schema.json';
						//print_r($frontendInputEntitySchemaURI);
			 			$frontendInputEntity = json_decode(file_get_contents('php://input'));
			 			//print_r($frontendInputEntity);
			 			if(empty($frontendInputEntity))
			 			{
							$this->response->setStatusCode(500, 'Internal Error: Input json is invalid, please send a valid json.');
							$this->response->send();	
							return 1;
						}
            
						 //Validate
						$validator = new \JsonSchema\Validator;
						$validator->validate($frontendInputEntity, (object)['$ref' => $this->request->getScheme() . '://' . $this->request->getServerName() . $frontendInputEntitySchemaURI]);
						
						if (! $validator->isValid()) 
						{
								echo "JSON does not validate. Violations:\n";    
								foreach ($validator->getErrors() as $error)
								 {   
										echo sprintf("[%s] %s\n", $error['property'], $error['message']);
								 }
							
							$this->response->setStatusCode(500, 'Internal Error: Input json is invalid, please send a valid json.');
							$this->response->send();
							return 1;
						}  
						
						$FrontendInputEntity = $entityRules->processRules($frontendInputEntity);
						///print_r($FrontendInputEntity);
						$this->frontendOutputEntity = new EntityCreateOutput();
						///print_r($this->frontendOutputEntity);
						try
						{
							$entityId = $this->entityBackend->create($frontendInputEntity);
							//print_r($entityId);
							if($entityId != null)
							{
								$this->frontendOutputEntity->returnCode = 0;
								$this->frontendOutputEntity->returnStatus = "Success";
								$this->frontendOutputEntity->entityId = $entityId;
							}
							else
							{
								throw new \Exception('Could not create entity.');
							}
				     
				//foreach($frontendInputEntity->productDetails as $key => $value)
					//{
							$mappedEntity = new MappedEntity();
							$mappedEntity->order_id=$entityId;
							////print_r($mappedEntity->order_id);

								//if (!empty($value))
								//{
										//$mappedEntity->product_id = $value->productId;
										
								//}
									if (!$mappedEntity->save())
										{
											throw new \Exception('Could not create entity relation');	      
										}
			      }
				//relationship set..of order and product..foreach 
				//creation of order product model object
				//set and then save

			catch(\Exception $e)
			{
				$errorText=$e->getmessage();
				$this->response->setStatusCode(500, 'Internal Error: ' . $errorText);
				$this->response->send();
				return 1;  
	 		}
	 	}	
		elseif($this->request->isGet() && !empty($entityId)) // Process a GET request
		{             
			//$operation = 'RETRIEVE';
			
			//$this->frontendOutputEntity = new EntityRetrieveOutput();

			
			try 
			{
				$backendEntity = $this->entityBackend->retrieve($entityId);
				//print_r($backendEntity);
		
				if($backendEntity == null)
				{
					throw new \Exception('Could not retrieve entity.');
				}
				
				$entityMap = EntityRepresentationMapping::attributeMap();

				/*foreach ($entityMap as $key => $value)
				{
					$this->frontendOutputEntity->$value = $backendEntity->$key;
					print_r($this->frontendOutputEntity);
			 	}
				*/
		    $productEntityIds = null;
		    echo 'inside product';
        $productIds = MappedEntity::find("order_id = " . $entityId);
	      print_r($productIds);
	      ///foreach($productIds as $key => $value)
	      ///{
	       //$this->frontendOutputEntity->productDetails[] = $value->product_id;
	       //print_r($this->frontendOutputEntity->productDetails[]);
         $mappedEntity->order_id=$entityId;
         //print_r($mappedEntity->order_id);
         /////print_r($productIds);
	     /// }
	       //$this->frontendOutputEntity->productDetails->productId = $mappedEntity->product_id->$key;	
	       //print_r($this->frontendOutputEntity->$value->productId);
			}
			catch(\Exception $e)
			{
				$errorText=$e->getmessage();
				$this->response->setStatusCode(500, 'Internal Error: ' . $errorText);
				$this->response->send();
				return 1;
			}
		}
    elseif($this->request->isPut() && !empty($entityId)) // Process a PUT request
    {
      $operation = 'UPDATE';
      $frontendInputEntitySchemaURI = '/schemas/order.update.input.schema.json';
      
      $frontendInputEntity = json_decode(file_get_contents('php://input'));
      
 			if(empty($frontendInputEntity))
 			{
				$this->response->setStatusCode(500, 'Internal Error: Input json is invalid, please send a valid json.');
				$this->response->send();
				return 1;
			}

			 //Validate
			$validator = new \JsonSchema\Validator;
			$validator->validate($frontendInputEntity, (object)['$ref' => $this->request->getScheme() . '://' . $this->request->getServerName() . $frontendInputEntitySchemaURI]);
			
			if (! $validator->isValid()) 
			{
					echo "JSON does not validate. Violations:\n";
					foreach ($validator->getErrors() as $error)
				  {
					echo sprintf("[%s] %s\n", $error['property'], $error['message']);
		       }
				
				$this->response->setStatusCode(500, 'Internal Error: Input json is invalid, please send a valid json.');
				$this->response->send();
				return 1;
			}
        
			$this->frontendOutputEntity = new EntityUpdateOutput();
			
			try
			{
				if($this->entityBackend->update($entityId, $frontendInputEntity))
				{
					$this->frontendOutputEntity->returnCode = 0;
					$this->frontendOutputEntity->returnStatus = "Success";
				}
				else
				{
					throw new \Exception('Could not update entity.');
				}
				
			$productIds = MappedEntity::find("order_id = " . $entityId);
				//foreach($productIds as $key => $value)
				//{

				 // if(!empty($value))
				 // {
				   //  $value->delete();
				 
						//foreach($frontendInputEntity->productDetails as $key => $value)
					 // {
							$mappedEntity = new MappedEntity();
							$mappedEntity->order_id=$entityId;

								//if (!empty($value))
								//{
									//	$mappedEntity->product_id //= $value->productId;
										
								//}
									if (!$mappedEntity->save())
										{
											throw new \Exception('Could not create entity relation');	      
										}
			
	           }
			catch(\Exception $e)
			{
				$errorText=$e->getmessage();
				$this->response->setStatusCode(500, 'Internal Error: ' . $errorText);
				$this->response->send();
				return 1;  
	 		}
		}
		
		elseif($this->request->isDelete() && !empty($entityId)) // Process a DELETE request
		{
			$operation = 'DELETE';
			
			$this->frontendOutputEntity = new EntityDeleteOutput();
			
			try
		  {
				if($this->entityBackend->delete($entityId))
				{
					$this->frontendOutputEntity->returnCode = 0;
					$this->frontendOutputEntity->returnStatus = "Success";
				}
				else
				{
					throw new \Exception('Could not delete entity.');
				}
				
				$productIds = MappedEntity::find("order_id = " . $entityId);
				//foreach($productIds as $key => $value)
				///{
				 // if(!empty($value))
				//  {
				   //  $value->delete();
				 // }
				//}
				//$mappedResult = MappedEntity::find("product_id = " . $entityId);
				//foreach($frontendInputEntity->productDetails as $key => $value)
	      //{
	      // if (!empty($value))
								//{
									//$this->frontendOutputEntity->$value = $mappedResult->$key;	 
										
								//}
				}			
			catch(\Exception $e)
			{
				$errorText=$e->getmessage();
				$this->response->setStatusCode(500, 'Internal Error: ' . $errorText);
				$this->response->send();
				return 1;  
	 		}
		}
		$this->response->setContentType('application/json', 'UTF-8');
		$this->response->setContent(json_encode($this->frontendOutputEntity));
		$this->response->send();
		return 0;
 	}
 	

 	private function orderProductRelation($entityId,$entityProductId)
	          {
							if($this->request->isPost() && !empty($entityProductId))
						 		{
							     print_r($entityId);
							     print_r($entityProductId);
									 //$operation = 'CREATE';
									 //$frontendInputEntitySchemaURI = '/schemas/order.create.input.schema.json';
								
					 				 //$frontendInputEntity = json_decode(file_get_contents('php://input'));
					 			
						 			    /* if(empty($frontendInputEntity))
							 			{
											$this->response->setStatusCode(500, 'Internal Error: Input json is invalid, please send a valid json.');
											$this->response->send();	
											return 1;
										}
										$this->frontendOutputEntity = new EntityCreateOutput();
						   */
										try
										{
												/*$entityId = $this->entityBackend->create($frontendInputEntity);
												
												if($entityId != null)
												{
													$this->frontendOutputEntity->returnCode = 0;
													$this->frontendOutputEntity->returnStatus = "Success";
													$this->frontendOutputEntity->entityId = $entityId;
												}
												else
												{
													throw new \Exception('Could not create entity.');
												}	
											*/	
										$mappedEntity = new MappedEntity();
										$mappedEntity->order_id=$entityId;
										$mappedEntity->product_id=$entityProductId;
									  if(!$mappedEntity->create())
									   {
									     echo 'could not create';
									   }
										//print_r($entityProductId);
										//print_r($mappedEntity);
										//print_r($mappedEntity->product_id);
										}
									
										catch(\Exception $e)
										{                      
											$errorText=$e->getmessage();
											$this->response->setStatusCode(500, 'Internal Error: ' . $errorText);
											$this->response->send();
											return 1;  
						        }	
						      }  				
			elseif($this->request->isGet() && empty($entityProductId)) // Process a GET request
							{             
								//$operation = 'RETRIEVE';
								
								//$this->frontendOutputEntity = new EntityRetrieveOutput();

								
								try 
								{
									$backendEntity = $this->entityBackend->retrieve($entityId);
									//print_r($backendEntity);
							
									if($backendEntity == null)
									{
										throw new \Exception('Could not retrieve entity.');
									}
									
									$entityMap = EntityRepresentationMapping::attributeMap();
									
									//$productEntityIds = null;
									//echo 'inside product';
									$productIds = MappedEntity::find("order_id = " . $entityId);
									//print_r($productIds);
									 //$this->frontendOutputEntity->productDetails->productId = $mappedEntity->product_id->$key;	
									 //print_r($this->frontendOutputEntity->$value->productId);
								}
								catch(\Exception $e)
								{
									$errorText=$e->getmessage();
									$this->response->setStatusCode(500, 'Internal Error: ' . $errorText);
									$this->response->send();
									return 1;
								}
						 }
	 
	 elseif($this->request->isDelete() && !empty($entityProductId)) // Process a DELETE request
					{
						//$operation = 'DELETE';
						
						//$this->frontendOutputEntity = new EntityDeleteOutput();
						
						try
						{
							/*if($this->entityBackend->delete($entityId))
							{
								$this->frontendOutputEntity->returnCode = 0;
								$this->frontendOutputEntity->returnStatus = "Success";
							}
							else
							{
								throw new \Exception('Could not delete entity.');
							}
							*/
							$productIds = MappedEntity::find("order_id = " . $entityId . " and product_id = " . $entityProductId);
							
							$mappedEntity->delete();
							
						}
						catch(\Exception $e)
						{
							$errorText=$e->getmessage();
							$this->response->setStatusCode(500, 'Internal Error: ' . $errorText);
							$this->response->send();
							return 1;  
				 		}	
				 	}
				 }	
		}	 
		 
