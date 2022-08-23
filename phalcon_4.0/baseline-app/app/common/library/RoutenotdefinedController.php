<?php

namespace App\Common\Library;

class RoutenotdefinedController extends \Phalcon\Mvc\Controller
{
  public function indexAction()
  {
		$this->response->setStatusCode(404, 'Route not defined');
		$this->response->send();
		return 0;
 	}
}
