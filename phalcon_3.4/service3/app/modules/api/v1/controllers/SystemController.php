<?php

namespace Modules\Api\V1\Controllers;
use Blive\Library\MySessionManager as MySessionManager;

class SystemController extends \Phalcon\Mvc\Controller
{

    public function loginAction()
    {
			$member_id=rand(1000,9999);
			
			echo "I am in system controller<br/>";

    	$session_object= new MySessionManager();
    	
    	if($session_object->newUserSession($member_id))
    	{
    		echo "User authenticated successfully.<br/>New user session started.<br/>";
    	}
    	else
    	{
    		$response = new Response(); 
        $response->setStatusCode(401, 'Bearer token not valid');
        $response->send();
    		
    	}

    }
    
		public function logoutAction()
    {
			echo "I am in system controller<br/>";

    	$session_object= new MySessionManager();
    	
    	if($session_object->closeUserSession())
    	{
    		echo "User is logged out.<br/>";
    	}
    	else
    	{
    		echo "ERROR! User could not be logged out.<br/>";
    		
    	}

    }

}

