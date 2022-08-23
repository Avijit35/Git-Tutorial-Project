<?php

namespace App\Modules\Api\Controllers;

use App\Modules\Api\Rules\Entity1Rules as EntityRules;

use App\Modules\Api\Common\Library\RequestHandling as CommonRequestHandling;
use App\Modules\Api\Common\Library\ResponseHandling as CommonResponseHandling;
use App\Modules\Api\Common\Library\OutputHandling as CommonOutputHandling;

use App\Modules\Api\Representation\EntityCreateOutput as EntityCreateOutput;
use App\Modules\Api\Representation\EntityRetrieveOutput as EntityRetrieveOutput;
use App\Modules\Api\Representation\OutputBase as OutputBase;
use App\Modules\Api\Representation\EntityOutputBase as EntityOutputBase;
use App\Modules\Api\Representation\EntityOutput as EntityOutput;
use App\Modules\Api\Representation\EntityRetrieveListOutput as EntityRetrieveListOutput;
use App\Modules\Api\Representation\EntityUpdateOutput as EntityUpdateOutput;
use App\Modules\Api\Representation\EntityDeleteOutput as EntityDeleteOutput;

use App\Modules\Api\Representation\Entity1OutputAttributes as EntityOutputAttributes;
use App\Modules\Api\Representation\Entity1OutputReferences as EntityOutputReferences;

class Entity1Controller extends \Phalcon\Mvc\Controller
{
	private $moduleVersion;
	private	$urlParams;
	private $queryParams;
	private $entityBackend;
	private $entityId;
	private $entityRules;
	private $frontendOutputEntity;
		
  public function indexAction()
  {
		$this->urlParams = $this->dispatcher->getParams(); // Obtain route params component
		$this->queryParams = $this->request->getQuery(); // Obtain query parameters from URL
		$entityIdUrlParam = ( ! empty($this->urlParams[0]) )?$this->urlParams[0]:null; // Set the entity id if present in the route
		$this->entityId = EntityRules::getEntityIdUrlParamAsNamedList($entityIdUrlParam);
		$this->moduleVersion = $this->dispatcher->getParam('version'); // Obtain the route version component
		$this->frontendOutputEntity = null; // Initialize the reponse content object
		
		$this->response->setStatusCode(403, 'Query parameter(s) not allowed');

		if($this->request->isPost() && empty($this->entityId)) // Process a POST request
		{
			$this->frontendOutputEntity = new EntityCreateOutput();
				
			$frontendInputSchemaUri = BASE_PATH . '/public/schemas/service/' . $this->router->getModuleName() . '/' . $this->moduleVersion . '/entity1.create.input.schema.json';
			
			$frontendInputEntity = $this->request->getJsonRawBody();
			
			if(empty($frontendInputEntity))
			{
				$this->sendErrorResponse(400, "Input is empty");
				return 1;
			}
			
			$frontendInputEntityValidator = CommonRequestHandling::validate($frontendInputEntity, $frontendInputSchemaUri);

			if ( ! $frontendInputEntityValidator->isValid() )
			{
				$this->sendErrorResponse(400, "Invalid input", 400, $frontendInputEntityValidator->getErrors());
				return 1;
			}
			
			try
			{
				if(	!empty($frontendInputEntity->__params) && isset($frontendInputEntity->__params->output->format) && ($frontendInputEntity->__params->output->format == 'dereference') )
				{
					$this->frontendOutputEntity = new EntityRetrieveOutput();
					
					$this->entityId = EntityRules::entityCreate($frontendInputEntity);
					
					if($this->entityId == null)
					{
						throw new \Exception('Could not create entity.');
					}
					
					$entity = EntityRules::entityRetrieve($this->entityId);
					
					EntityRules::processOutput($entity);
					
					CommonOutputHandling::formatFrontendOutputResource($this->getDI(), $this->entityId, $entity, $this->frontendOutputEntity->resource, $this->moduleVersion, new EntityOutputAttributes(), EntityOutputAttributes::map(), EntityOutputAttributes::profile('full'), new EntityOutputReferences(), EntityOutputReferences::map(), EntityOutputReferences::profile('full'));
				}
				else
				{
					$this->entityId = EntityRules::entityCreate($frontendInputEntity);
					
					if($this->entityId == null)
					{
						throw new \Exception('Could not create entity.');
					}
					
					$resourceId = $this->entityId;
				
					CommonOutputHandling::getEntityIdNamedListAsUrlParam($resourceId);
					
					$this->frontendOutputEntity->resource->id = $resourceId;
					$this->frontendOutputEntity->resource->link = CommonOutputHandling::genResourceUrl($this->getDI(), $this->moduleVersion, $this->router->getControllerName(), $resourceId);
				}
				
				$this->frontendOutputEntity->return_code = 0;
				$this->frontendOutputEntity->return_status = "success";
				
				$this->response->setStatusCode(201, $this->frontendOutputEntity->return_status);
			}
			catch(\Exception $e)
			{
				$this->sendErrorResponse(500, "Internal processing error", 500, $e->getMessage());
				return 1;  
	 		}
		}
		elseif($this->request->isGet() && !empty($this->entityId)) // Process a GET request with id
		{
		
			$this->frontendOutputEntity = new EntityRetrieveOutput();
				
			try 
			{
				$entity = EntityRules::entityRetrieve($this->entityId);
				
				if($entity == null)
				{
					$this->sendErrorResponse(404, "Resource not found");
					return 1;
				}
				
				EntityRules::processOutput($entity);
				
				CommonOutputHandling::formatFrontendOutputResource($this->getDI(), $this->entityId, $entity, $this->frontendOutputEntity->resource, $this->moduleVersion, new EntityOutputAttributes(), EntityOutputAttributes::map(), EntityOutputAttributes::profile('full'), new EntityOutputReferences(), EntityOutputReferences::map(), EntityOutputReferences::profile('full'));

				$this->frontendOutputEntity->return_code = 0;
				$this->frontendOutputEntity->return_status = "success";
				$this->response->setStatusCode(200, $this->frontendOutputEntity->return_status);
			}
			catch(\Exception $e)
			{
				$this->sendErrorResponse(500, "Internal processing error", 500, $e->getMessage());
				return 1;
			}
		}
		elseif($this->request->isGet() && empty($this->entityId) && ! empty($this->queryParams['action']) && $this->queryParams['action'] == 'filter') // Process a GET request to list entities
		{
			$this->frontendOutputEntity = new EntityRetrieveListOutput();
				
			$outputOptions = array();
			
			if( ! empty($this->queryParams['format']) && $this->queryParams['format'] == 'dereference' )
			{
				$outputOptions['format'] = 'dereference';
			}
			
			$queryFilterCondition = null;
			
			// Add query params based filter condition here
			
			if(empty($queryFilterCondition))
			{
				$this->sendErrorResponse(400, "Invalid query parameters", 400, "Invalid query parameters, no filter criteria provided");
				return 1;
			}
			
			try
			{
				$entityList = EntityRules::entityRetrieveList($outputOptions, $queryFilterCondition);
				
				if($entityList == null)
				{
					$this->sendErrorResponse(404, "Resource not found");
					return 1;
				}
				
				foreach($entityList as $entity)
				{
					EntityRules::processOutput($entity);
					
					if($entity == null)
					{
						continue;
					}
					
					$frontendEntity = new EntityOutputBase();
					
					$resourceId = $entity;
					
					CommonOutputHandling::getEntityIdNamedListAsUrlParam($resourceId);
					
					$frontendEntity->id = $resourceId;
					$frontendEntity->link = CommonOutputHandling::genResourceUrl($this->getDI(), $this->moduleVersion, $this->router->getControllerName(), $resourceId);
				
					if( ! empty($outputOptions['format']) && $outputOptions['format'] == 'dereference' )
					{
						$frontendEntity = new EntityOutput();
											
						CommonOutputHandling::formatFrontendOutputResource($this->getDI(), EntityRules::getEntityId($entity), $entity, $frontendEntity, $this->moduleVersion, new EntityOutputAttributes(), EntityOutputAttributes::map(), EntityOutputAttributes::profile());
					}
					
					$this->frontendOutputEntity->resource_list[] = $frontendEntity;
				}
				
				$this->frontendOutputEntity->return_code = 0;
				$this->frontendOutputEntity->return_status = "success";
				$this->response->setStatusCode(200, $this->frontendOutputEntity->return_status);
			}
			catch(\Exception $e)
			{
				$this->sendErrorResponse(500, "Internal processing error", 500, $e->getMessage());
				return 1;
			}
		}
    elseif($this->request->isPut() && !empty($this->entityId)) // Process a PUT request
    {
			$this->frontendOutputEntity = new EntityUpdateOutput();
				
		  $frontendInputSchemaUri = BASE_PATH . '/public/schemas/service/' . $this->router->getModuleName() . '/' . $this->moduleVersion . '/entity1.update.input.schema.json';
		  
		  $frontendInputEntity = $this->request->getJsonRawBody();
		  
			if(empty($frontendInputEntity))
			{
				$this->sendErrorResponse(400, "Input is empty");
				return 1;
			}

			$frontendInputEntityValidator = CommonRequestHandling::validate($frontendInputEntity, $frontendInputSchemaUri);
			
			if (! $frontendInputEntityValidator->isValid() )
			{
				$this->sendErrorResponse(400, "Invalid input", 400, $frontendInputEntityValidator->getErrors());
				return 1;
			}
			
			try
			{
				if(	!empty($frontendInputEntity->__params) && isset($frontendInputEntity->__params->output->format) && ($frontendInputEntity->__params->output->format == 'dereference') )
				{
					$this->frontendOutputEntity = new EntityRetrieveOutput();
					
					$returnValue = EntityRules::entityUpdate($this->entityId, $frontendInputEntity);
					
					if($returnValue === false)
					{
						throw new \Exception('Could not update entity.');
					}
					elseif($returnValue === null)
					{
						$this->sendErrorResponse(404, "Resource not found");
						return 1;
					}
					
					$entity = EntityRules::entityRetrieve($this->entityId);
					
					EntityRules::processOutput($entity);
					
					CommonOutputHandling::formatFrontendOutputResource($this->getDI(), $this->entityId, $entity, $this->frontendOutputEntity->resource, $this->moduleVersion, new EntityOutputAttributes(), EntityOutputAttributes::map(), EntityOutputAttributes::profile('full'), new EntityOutputReferences(), EntityOutputReferences::map(), EntityOutputReferences::profile('full'));
				}
				else
				{
					$returnValue = EntityRules::entityUpdate($this->entityId, $frontendInputEntity);
					
					if($returnValue === false)
					{
						throw new \Exception('Could not update entity.');
					}
					elseif($returnValue === null)
					{
						$this->sendErrorResponse(404, "Resource not found");
						return 1;
					}
				}
								
				$this->frontendOutputEntity->return_code = 0;
				$this->frontendOutputEntity->return_status = "success";
				$this->response->setStatusCode(200, $this->frontendOutputEntity->return_status);
			}
			catch(\Exception $e)
			{
				$this->sendErrorResponse(500, "Internal processing error", 500, $e->getMessage());
				return 1;
	 		}
		}
		elseif($this->request->isDelete() && !empty($this->entityId)) // Process a DELETE request
		{
			$this->frontendOutputEntity = new EntityDeleteOutput();
				
			try
			{
				$returnValue = EntityRules::entityDelete($this->entityId);
				
				if($returnValue === false)
				{
					throw new \Exception('Could not delete entity.');
				}
				elseif($returnValue === null)
				{
					$this->sendErrorResponse(404, "Resource not found");
					return 1;
				}
				
				$this->frontendOutputEntity->return_code = 0;
				$this->frontendOutputEntity->return_status = "success";
				$this->response->setStatusCode(200, $this->frontendOutputEntity->return_status);
			}
			catch(\Exception $e)
			{
				$this->sendErrorResponse(500, "Internal processing error", 500, $e->getMessage());
				return 1;  
	 		}
		}
		
		if( ! empty($this->frontendOutputEntity) )
		{
			$this->response->setContentType('application/json', 'UTF-8');
			$this->response->setContent(json_encode($this->frontendOutputEntity));
		}
		
		$this->response->send();
		
		return 0;
 	}
 	
  private function sendErrorResponse($responseCode, $responseStatus, $messageCode = null, $messageStatus = null)
  {
		$processedResponse = CommonResponseHandling::processErrorResponse(array('code' => $responseCode, 'status' => $responseStatus));
		
		if(empty($processedResponse))
		{
			$processedResponse = array('code' => $responseCode, 'status' => $responseStatus);
		}

		if( $processedResponse['code'] == 200 )
		{
			if($this->frontendOutputEntity == null)
			{
				$this->frontendOutputEntity = new OutputBase();
			}
		
			if(property_exists($this->frontendOutputEntity, 'resource'))
			{
				$this->frontendOutputEntity->resource = null;
			}
		}
		else
		{
			$this->frontendOutputEntity = new OutputBase();		
		}
		
		if(!empty($messageCode))
		{
			$this->frontendOutputEntity->return_code = $messageCode;
			$this->frontendOutputEntity->return_status = $messageStatus;
		}
		else
		{
			$this->frontendOutputEntity->return_code = $responseCode;
			$this->frontendOutputEntity->return_status = $responseStatus;
		}
		
		$this->response->setStatusCode($processedResponse['code'], $processedResponse['status']);
		$this->response->setContentType('application/json', 'UTF-8');
		$this->response->setContent(json_encode($this->frontendOutputEntity));
		
		$this->response->send();
		
		return 0;
  }
}
