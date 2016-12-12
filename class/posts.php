<?php
/**
*   posts class:    get posts
*					add new post
*                   post count for certain user
*
*/

class posts
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
     * @param $userid
     * get post count for user
     */
    public function post_count($userid)
	{
		$query = $this->db->prepare("SELECT COUNT(*) FROM `posts` WHERE `author_id` = :userid");
		$query->bindParam(":userid", $userid, PDO::PARAM_INT);
		
		try	{		
			$query->execute();

			if($query->rowCount() == 1)	{
                return $posts_count = $query->fetchAll();
			} 
			else {
				return false;
			}
			
		} catch(PDOException $ex) {
			die($ex->getMessage());
		}
	}

    /**
     * @param null   
     * get posts
     */
    public function get_posts()
    {
        $query = $this->db->prepare("SELECT * FROM `posts` INNER JOIN `users` ON `posts`.author_id = `users`.id ORDER BY `posts`.id DESC");
        
        try {       
            $query->execute();

            return $post_data = $query->fetchAll();
            
        } catch(PDOException $ex) {
            die($ex->getMessage());
        }
    }    

    /**
     * @param $author_id
     * @param $post_text
     * @param $status
     * add new post
     */
    public function new_post($author_id, $post_text, $status)
    {
    	$post_text = htmlspecialchars($post_text, ENT_QUOTES, "UTF-8");
    	$status = htmlspecialchars($status, ENT_QUOTES, "UTF-8");
		$date_created = date("y-m-d H:i:s");

        $query = $this->db->prepare("INSERT INTO `posts` (`author_id`, `post_text`, `status`, `date_created`) VALUES (:author_id, :post_text, :status, :date_created)");
        $query->bindParam(":author_id", $author_id, PDO::PARAM_INT);
        $query->bindParam(":post_text", $post_text, PDO::PARAM_STR);
        $query->bindParam(":status", $status, PDO::PARAM_STR);
        $query->bindParam(":date_created", $date_created, PDO::PARAM_STR);

        try {
            $query->execute();

            return true;

        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

}