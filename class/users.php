<?php
/**
*	users class: login/logout
 *               check user credentials
 *               change user credentials
*/

class users
{	
	protected $db;

    /**
     * @param $database
     */
    public function __construct($database)
	{
	    $this->db = $database;
	}

    /**
     * @param $username
     * check if user exists
     */
    public function user_exists($username)
    {
        $query = $this->db->prepare("SELECT `id` FROM `users` WHERE `username` = :username LIMIT 1");
        $query->bindParam(":username", $username, PDO::PARAM_STR);

        try {
            $query->execute();
            if ($query->rowCount() == 1) {
                return true;
            } else {
                return false;
            }

        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }	


    /**
     * @param $id
     * get current user data
     */
    public function user_get_data($id)
	{
		$query = $this->db->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
		$query->bindParam(":id", $id, PDO::PARAM_INT);
		
		try
		{		
			$query->execute();

			if($query->rowCount() == 1)
			{
                $user_data = $query->fetchAll();
				return $user_data;
			} 
			else 
			{
				return false;
			}
			
		} 
		catch(PDOException $ex)
		{
			die($ex->getMessage());
		}
	}

    /**
     * @param $username
     * @param $password
     * @param $ip
     * main login function
     */
    public function login($username, $password, $ip)
	{
		$username = htmlspecialchars($username, ENT_QUOTES, "UTF-8");
		$last_online = date("y-m-d H:i:s");
		$password = sha1($password);
		
		$query = $this->db->prepare("SELECT `id` FROM `users` WHERE username = :username AND password = :password");
		$query->bindParam(":username", $username, PDO::PARAM_STR);
		$query->bindParam(":password", $password, PDO::PARAM_STR);

		try
		{		
			$query->execute();
			
			if($query->rowCount() == 1)
			{
				$sql_update = $this->db->prepare("UPDATE `users` SET `ip` = :ip, `last_online` = :last_online WHERE `username` = :username");
				$sql_update->bindParam(":ip", $ip, PDO::PARAM_STR);
				$sql_update->bindParam(":last_online", $last_online, PDO::PARAM_STR);
				$sql_update->bindParam(":username", $username, PDO::PARAM_STR);
				$sql_update->execute();
				
				return $query->fetchColumn(0);	
			} 
			else 
			{
				return false;
			}
			
		} catch(PDOException $ex){
			die($ex->getMessage());
		}	
		
	}

    /**
     * @param $ip
     * @param $full_name
     * @param $username
     * @param $password
     * @param $password_again
     * @param $email
     * @param $phone_number
     * register user
     */
    public function register($ip, $full_name, $username, $password, $password_again, $email, $phone_number)
    {

    	$ip = htmlspecialchars($ip, ENT_QUOTES, "UTF-8");
    	$full_name = htmlspecialchars($full_name, ENT_QUOTES, "UTF-8");
        $username = htmlspecialchars($username, ENT_QUOTES, "UTF-8");		
		$password = sha1($password);
		$email = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
		$phone_number = htmlspecialchars($phone_number, ENT_QUOTES, "UTF-8");
		$registration_date = date("y-m-d H:i:s");
		$last_online = date("y-m-d H:i:s");

        $query = $this->db->prepare("INSERT INTO `users` (`username`, `password`, `full_name`, `phone_number`, `email`, `ip`, `registration_date`, `last_online`) VALUES (:username, :password, :full_name, :phone_number, :email, :ip, :registration_date, :last_online)");
        $query->bindParam(":username", $username, PDO::PARAM_STR);
        $query->bindParam(":password", $password, PDO::PARAM_STR);
        $query->bindParam(":full_name", $full_name, PDO::PARAM_STR);
        $query->bindParam(":phone_number", $phone_number, PDO::PARAM_STR);
        $query->bindParam(":email", $email, PDO::PARAM_STR);
        $query->bindParam(":ip", $ip, PDO::PARAM_STR);
        $query->bindParam(":registration_date", $registration_date, PDO::PARAM_STR);
        $query->bindParam(":last_online", $last_online, PDO::PARAM_STR);

        try {
            $query->execute();
            return true;

        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * @param void
     * logout current user
     */
    public function logout()
    {
		session_start();
		session_destroy();
		header("Location: ");
		exit();
	}	
}

?>