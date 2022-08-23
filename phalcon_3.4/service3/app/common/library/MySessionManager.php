<?php

namespace Service1\Library;

use Phalcon\Session\Adapter\Files as Session;
use Phalcon\DI\FactoryDefault as Di;

class MySessionManager
{

	private $mySessionDi;
	
	function __construct()
	{
		$this->mySessionDi = new Di();
		
		if ( ! empty($_COOKIE[session_name()]) && ! $this->mySessionDi->get('session')->isStarted() )
		{
			$this->mySessionDi->get('session')->start();
		}
	}
	
	public function isUserAuthenticated()
	{
		echo "Checking if session started<br/>";
		
		if($this->mySessionDi->get('session')->isStarted() && $this->mySessionDi->get('session')->has('member_id'))
		{
			echo "Session resumed for member_id: " . $this->mySessionDi->get('session')->get('member_id', '<error>') . '<br/>';
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function newUserSession($member_id)
	{
		echo "Starting session<br/>";
		
		$this->mySessionDi->get('session')->start();
		
		if ($this->mySessionDi->get('session')->has('member_id'))
		{
			$this->mySessionDi->get('session')->regenerateId();
		}
		
		$this->mySessionDi->get('session')->set('member_id', $member_id);
		
		echo "Session object: " . print_r($_SESSION, TRUE) . "<br/>";
		
		return TRUE;
	}
	
	public function closeUserSession()
	{
		echo "Closing session<br/>";
		
		if($this->mySessionDi->get('session')->isStarted())
		{
			$this->mySessionDi->get('session')->destroy();
		}
		
		if(!empty($_COOKIE[session_name()]))
		{
			$this->mySessionDi->get('cookies')->get(session_name())->delete();
		}
		
		echo "User session destroyed.<br/>";
		
		return TRUE;
	}
}

?>
