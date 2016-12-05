<?php
/**
*	session class: session checks and redirects
*/

class session
{
	//check session ID
	public function session_test()
	{
		global $session_id;

		if(isset($_SESSION[$session_id]))
		{
			return true;
		} 
		else 
		{
			return false;
		}
	}
	
	//valid session
	public function session_true($location)
	{
		if($this->session_test() === true)
		{
			header("Location: ".$location);
			exit();
		}
	}
	
	//invalid session
	public function session_false($location){
		if($this->session_test() === false)
		{
			header("Location: ".$location);
			exit();
		}
	}		
}

?>