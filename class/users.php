<?php
/**
*   users class:        login
*                       register
*                       get current user data
*                       get single user data
*                       check if users exist by username
*                       check if users exist by slug
*                       update login time
*                       change user credentials
*                       logout
*                       get users count
*                       get first registration date
*                       get last registration date                       
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
     * @param $slug
     * check if user exists by slug
     */
    public function user_exists_slug($slug)
    {
        $query = $this->db->prepare("SELECT `id` FROM `users` WHERE `slug` = :slug LIMIT 1");
        $query->bindParam(":slug", $slug, PDO::PARAM_STR);

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
		
		try	{		
			$query->execute();

			if($query->rowCount() == 1)	{
                return $user_data = $query->fetchAll();
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
     * @param $slug
     * get user data
     */
    public function get_user($slug)
    {
        $query = $this->db->prepare("SELECT * FROM `users` WHERE `slug` = :slug LIMIT 1");
        $query->bindParam(":slug", $slug, PDO::PARAM_STR);
        
        try {       
            $query->execute();

            if($query->rowCount() == 1) {
                return $user_data = $query->fetchAll();
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
     * @param $id
     * update online time of user
     */
    public function update_online_time($id)
    {
        $last_online = date("y-m-d H:i:s");
        
        $query = $this->db->prepare("UPDATE `users` SET `last_online` = :last_online WHERE `id` = :id");
        $query->bindParam(":last_online", $last_online, PDO::PARAM_STR);
        $query->bindParam(":id", $id, PDO::PARAM_INT);

        try {       
            $query->execute();

            return true;

        } catch(PDOException $ex){
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

		try	{		
			$query->execute();
			
			if($query->rowCount() == 1)	{
				$sql_update = $this->db->prepare("UPDATE `users` SET `ip` = :ip, `last_online` = :last_online WHERE `username` = :username");
				$sql_update->bindParam(":ip", $ip, PDO::PARAM_STR);
				$sql_update->bindParam(":last_online", $last_online, PDO::PARAM_STR);
				$sql_update->bindParam(":username", $username, PDO::PARAM_STR);
				$sql_update->execute();
				
				$_SESSION["id"] = $query->fetchColumn(0);
                $_SESSION['username'] = $username;

                return true;
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
        $slug = safe_name($full_name);
        $username = htmlspecialchars($username, ENT_QUOTES, "UTF-8");		
		$password = sha1($password);
		$email = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
		$phone_number = htmlspecialchars($phone_number, ENT_QUOTES, "UTF-8");
		$registration_date = date("y-m-d H:i:s");
		$last_online = date("y-m-d H:i:s");

        $query = $this->db->prepare("INSERT INTO `users` (`username`, `password`, `full_name`, `slug`, `phone_number`, `email`, `ip`, `registration_date`, `last_online`) VALUES (:username, :password, :full_name, :slug, :phone_number, :email, :ip, :registration_date, :last_online)");
        $query->bindParam(":username", $username, PDO::PARAM_STR);
        $query->bindParam(":password", $password, PDO::PARAM_STR);
        $query->bindParam(":full_name", $full_name, PDO::PARAM_STR);
        $query->bindParam(":slug", $slug, PDO::PARAM_STR);
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
     * @param $ip
     * @param $full_name
     * @param $password
     * @param $password_again
     * @param $email
     * @param $phone_number
     * @param $avatar
     * edit user settings
     */
    public function update_settings($ip, $full_name, $password, $password_again, $email, $phone_number, $avatar)
    {
        $ip = htmlspecialchars($ip, ENT_QUOTES, "UTF-8");
        $full_name = htmlspecialchars($full_name, ENT_QUOTES, "UTF-8");
        $slug = safe_name($full_name);      
        $email = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
        $phone_number = htmlspecialchars($phone_number, ENT_QUOTES, "UTF-8");

        if($password == "-"){
            $query = $this->db->prepare("UPDATE `users` SET `avatar` = :avatar, `full_name` = :full_name, `slug` = :slug, `phone_number` = :phone_number, 
                                        `email` = :email,`ip` = :ip WHERE `id` = :id");
            $query->bindParam(":avatar", $avatar, PDO::PARAM_STR);
            $query->bindParam(":full_name", $full_name, PDO::PARAM_STR);
            $query->bindParam(":slug", $slug, PDO::PARAM_STR);
            $query->bindParam(":phone_number", $phone_number, PDO::PARAM_STR);
            $query->bindParam(":email", $email, PDO::PARAM_STR);
            $query->bindParam(":ip", $ip, PDO::PARAM_STR);
            $query->bindParam(":id", $_SESSION["id"], PDO::PARAM_INT);
        }
        else{
            $password = sha1($password);
            $query = $this->db->prepare("UPDATE `users` SET `avatar` = :avatar, `password` = :password, `full_name` = :full_name, `slug` = :slug, `phone_number` = :phone_number, 
                                        `email` = :email, `ip` = :ip WHERE `id` = :id");
            $query->bindParam(":avatar", $avatar, PDO::PARAM_STR);
            $query->bindParam(":password", $password, PDO::PARAM_STR);
            $query->bindParam(":full_name", $full_name, PDO::PARAM_STR);
            $query->bindParam(":slug", $slug, PDO::PARAM_STR);
            $query->bindParam(":phone_number", $phone_number, PDO::PARAM_STR);
            $query->bindParam(":email", $email, PDO::PARAM_STR);
            $query->bindParam(":ip", $ip, PDO::PARAM_STR);
            $query->bindParam(":id", $_SESSION["id"], PDO::PARAM_INT);
        }

        try {
            $query->execute();

            return true;

        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }    

    /**
     * @param null
     * logout current user
     */
    public function logout()
    {
		session_start();
		session_destroy();
		header("Location: ");
		exit();
	}

    /**
     * @param NULL
     * count all users
     */
    public function users_count()
    {
        $query = $this->db->prepare("SELECT COUNT(*) AS `num_of_users` FROM `users`");
        
        try {       
            $query->execute();

            if($query->rowCount() == 1) {
                return $users_count = $query->fetchAll();
            } 
            else {
                return false;
            }
            
        } catch(PDOException $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * @param NULL
     * get first user registred date
     */
    public function get_first_registred_user()
    {
        $query = $this->db->prepare("SELECT `registration_date` FROM `users` ORDER BY `id` ASC LIMIT 1");
        
        try {       
            $query->execute();

            if($query->rowCount() == 1) {
                return $user_data = $query->fetchAll();
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
     * @param NULL
     * get last user registred data
     */
    public function get_last_registred_user()
    {
        $query = $this->db->prepare("SELECT `registration_date` FROM `users` ORDER BY `id` DESC LIMIT 1");
        
        try {       
            $query->execute();

            if($query->rowCount() == 1) {
                return $user_data = $query->fetchAll();
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
}