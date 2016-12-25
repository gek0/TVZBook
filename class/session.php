<?php
/**
*	session class: session checks and redirects
*/

class session
{
    /**
     * @param null   
     * check session id
     */
	public function session_test()
	{
		global $session_id;

		if(isset($_SESSION[$session_id])){
			return true;
		} 
		else {
			return false;
		}
	}
	
    /**
     * @param $location   
     * if session is valid
     */
	public function session_true($location)
	{
		if($this->session_test() === true){
			header("Location: ".$location);
			exit();
		}
	}
	
    /**
     * @param $location   
     * if session is invalid
     */
	public function session_false($location)
	{
		if($this->session_test() === false){
			header("Location: ".$location);
			exit();
		}
	}		
}