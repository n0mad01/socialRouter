<?php

include('app/classes/controller.php');

class Home extends Controller {

/**
 *	Method 'allowed' stores all the Methods of 
 *	this Class which are allowed to be accessed through REST
 */
	//public function allowed() {

		//$this->allowed = array('test','index','login','logout','register');
	//}

/**
 *	Method 'authExceptions' stores the Methods where the user doesn't need to 
 *	be logged in to access through REST
 *	(Automaticly redirection to login if not in list)
 */
	public function authExceptions()
    {
		$this->authExceptions = array('index');
	}

	/**
	 *	default method
	 */
	public function index()
    {
		//dumper($_SERVER);
	}
}
