<?php

namespace Service2\Modules\Api\V1\Controllers;

use Service2\Library\Entity2Backend as EntityBackend;

class Entity2Controller extends \Phalcon\Mvc\Controller
{
	private $entityBackend = null;
		
  public function indexAction()
  {
		$router = $this->di->getShared('router'); // Obtain the shared router instance
		$urlParams = $router->getParams(); // Obtain the URL parameters
		$queryParams = $this->request->getQuery(); // Obtain any query parameters from URL
		$entityId = $urlParams[0]; // Set the entity id if present in the URL, POST method should not contain this parameter
		$entityBackend = new EntityBackend(); // Create an instance of entity storage

		if($this->request->isGet() && !empty($entityId)) // Process a GET request
		{             
			$operation = 'RETRIEVE';
			
			try 
			{
				$backendEntity = $this->entityBackend->retrieve($entityId);
				
				// Code to transform the backend entity into a response entity object can be put here
				// Code to clean up the response entity object before sending it can be put here
				
				$this->response->setContentType('application/json', 'UTF-8');
				$this->response->setContent(json_encode($entityBackend));
				$this->response->send();
				return 0;
			}
			catch(Exception $e)
			{
				$errorText=$e->getmessage();
				$this->response->setStatusCode(500, 'Internal Error: ' . $errorText);
				$this->response->send();
				return 1;
			}
		}
		elseif($this->request->isPost() && empty($entityId)) // Process a POST request
		{	
			$operation = 'CREATE';
			
			$postParameters = $this->request->getPost();
			
			try
			{
				if($this->entityBackend->create($this->entity_type, $postParameters))
				{
					$responseObjectJson = array (
																				'status' => 'success'
													 					  );
				}
				else
				{
					$responseObjectJson = array (
																'status' => 'failure'
									 					  );
				}
				
				$this->response->setContentType('application/json', 'UTF-8');
				$this->response->setContent(json_encode($responseObjectJson));
				$this->response->send();
				return 0;
			}
			catch(Exception $e)
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
      
      $putParameters = $this->request->getPut();
        
		  try
		  {
				if($this->entityBackend->update($putParameters))
				{
					$responseObjectJson = array (
																				'status' => 'success'
													 					  );
				}
				else
				{
					$responseObjectJson = array (
																'status' => 'failure'
									 					  );
				}
				
				$this->response->setContentType('application/json', 'UTF-8');
				$this->response->setContent(json_encode($responseObjectJson));
				$this->response->send();
				return 0;
			}
			catch(Exception $e)
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
			
			try
		  {
				if($this->entityBackend->delete($entityId))
				{
					$responseObjectJson = array (
																				'status' => 'success'
													 					  );
				}
				else
				{
					$responseObjectJson = array (
																'status' => 'failure'
									 					  );
				}
				
				$this->response->setContentType('application/json', 'UTF-8');
				$this->response->setContent(json_encode($responseObjectJson));
				$this->response->send();
				return 0;
			}
			catch(Exception $e)
			{
				$errorText=$e->getmessage();
				$this->response->setStatusCode(500, 'Internal Error: ' . $errorText);
				$this->response->send();
				return 1;  
			}
		}
 	}
}
