<?php

namespace Service\Modules\Api\V1\Controllers;

use Service\Modules\Api\V1\Backend\Entity1Backend as EntityBackend;
use Service\Modules\Api\V1\Representation\Entity1CreateOutput as EntityCreateOutput;
use Service\Modules\Api\V1\Representation\Entity1RetrieveOutput as EntityRetrieveOutput;
use Service\Modules\Api\V1\Representation\Entity1UpdateOutput as EntityUpdateOutput;
use Service\Modules\Api\V1\Representation\Entity1DeleteOutput as EntityDeleteOutput;
use Service\Modules\Api\V1\Representation\Entity1RepresentationMapping as EntityRepresentationMapping;
use Service\Modules\Api\V1\Rules\Entity1Rules as EntityRules;

class Entity1Controller extends \Phalcon\Mvc\Controller
{
	private $entityBackend;
	private $frontendOutputEntity;
		
  public function indexAction()
  {
		$router = $this->di->getShared('router'); // Obtain the shared router instance
		$urlParams = $router->getParams(); // Obtain the URL parameters
		$queryParams = $this->request->getQuery(); // Obtain any query parameters from URL
		$entityId = (!empty($urlParams[0]))?$urlParams[0]:null; // Set the entity id if present in the URL, POST method should not contain this parameter
		$this->entityBackend = new EntityBackend(); // Create an instance of entity storage
		$this->frontendOutputEntity = null;
		$entityRules = new EntityRules();

		if($this->request->isPost() && empty($entityId)) // Process a POST request
		{	
			$operation = 'CREATE';
			$frontendInputEntitySchemaURI = '/schemas/entity1.create.input.schema.json';
			
 			$frontendInputEntity = json_decode(file_get_contents('php://input'));
 			
 			if(empty($frontendInputEntity))
 			{
				$this->response->setStatusCode(500, 'Internal Error: Input json is invalid, please send a valid json.');
				$this->response->send();
				return 1;
			}

			// Validate
			$validator = new \JsonSchema\Validator;
			$validator->validate($frontendInputEntity, (object)['$ref' => $this->request->getScheme() . '://' . $this->request->getServerName() . $frontendInputEntitySchemaURI]);
			
			if (! $validator->isValid()) 
			{
					echo "JSON does not validate. Violations:\n";
					foreach ($validator->getErrors() as $error) {
							echo sprintf("[%s] %s\n", $error['property'], $error['message']);
				}
				
				$this->response->setStatusCode(500, 'Internal Error: Input json is invalid, please send a valid json.');
				$this->response->send();
				return 1;
			}
			
			$FrontendInputEntity = $entityRules->processRules($frontendInputEntity);
			
			$this->frontendOutputEntity = new EntityCreateOutput();
			
			try
			{
				$entityId = $this->entityBackend->create($frontendInputEntity);
				
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
			}
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
			$operation = 'RETRIEVE';
			
			$this->frontendOutputEntity = new EntityRetrieveOutput();
			
			try 
			{
				$backendEntity = $this->entityBackend->retrieve($entityId);
				
				if($backendEntity == null)
				{
					throw new \Exception('Could not retrieve entity.');
				}
				
				$entityMap = EntityRepresentationMapping::attributeMap();
				
				foreach ($entityMap as $key => $value)
				{
					$this->frontendOutputEntity->$value = $backendEntity->$key;
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
    elseif($this->request->isPut() && !empty($entityId)) // Process a PUT request
    {
      $operation = 'UPDATE';
      $frontendInputEntitySchemaURI = '/schemas/entity1.update.input.schema.json';
      
      $frontendInputEntity = json_decode(file_get_contents('php://input'));
      
 			if(empty($frontendInputEntity))
 			{
				$this->response->setStatusCode(500, 'Internal Error: Input json is invalid, please send a valid json.');
				$this->response->send();
				return 1;
			}

			// Validate
			$validator = new \JsonSchema\Validator;
			$validator->validate($frontendInputEntity, (object)['$ref' => $this->request->getScheme() . '://' . $this->request->getServerName() . $frontendInputEntitySchemaURI]);
			
			if (! $validator->isValid()) 
			{
					echo "JSON does not validate. Violations:\n";
					foreach ($validator->getErrors() as $error) {
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
}
