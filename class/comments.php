<?php
/**
*   comments class: comment count for certain user
*					get comments for single post
*					add new comment
*					get last comment
*                   delete comments for post
*
*/

class comments
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
     * get comments count for user
     */
    public function comment_count($userid)
	{
		$query = $this->db->prepare("SELECT COUNT(*) FROM `comments` WHERE `author_id` = :userid");
		$query->bindParam(":userid", $userid, PDO::PARAM_INT);
		
		try	{		
			$query->execute();

			if($query->rowCount() == 1)	{
                return $comments_count = $query->fetchAll();
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
     * get last comment
     */
    public function get_last_comment()
    {
        $query = $this->db->prepare("SELECT `comments`.*, `users`.id, `users`.slug, `users`.full_name, `users`.avatar FROM `comments` 
        								INNER JOIN `users` ON `comments`.author_id = `users`.id 
                                        ORDER BY `comments`.id DESC LIMIT 1");  

        try {       
            $query->execute();

            return $comment_data = $query->fetchAll();
            
        } catch(PDOException $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * @param $post_id   
     * get comments for single post
     */
    public function get_comments($post_id)
    {
        $query = $this->db->prepare("SELECT `comments`.*, `users`.id, `users`.slug, `users`.full_name, `users`.avatar FROM `comments`
        								INNER JOIN `users` ON `comments`.author_id = `users`.id
        								WHERE `comments`.post_id = :post_id
                                        ORDER BY `comments`.id DESC");
        $query->bindParam(":post_id", $post_id, PDO::PARAM_INT);
        
        try {       
            $query->execute();

            return $comments_data = $query->fetchAll();
            
        } catch(PDOException $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * @param $author_id
     * @param $post_id
     * @param $comment_text
     * add new comment
     */
    public function new_comment($author_id, $post_id, $comment_text)
    {
    	$comment_text = htmlspecialchars($comment_text, ENT_QUOTES, "UTF-8");
		$date_created = date("y-m-d H:i:s");

        $query = $this->db->prepare("INSERT INTO `comments` (`author_id`, `post_id`, `comment_text`, `date_created`) 
                                        VALUES (:author_id, :post_id, :comment_text, :date_created)");
        $query->bindParam(":author_id", $author_id, PDO::PARAM_INT);
        $query->bindParam(":post_id", $post_id, PDO::PARAM_INT);
        $query->bindParam(":comment_text", $comment_text, PDO::PARAM_STR);
        $query->bindParam(":date_created", $date_created, PDO::PARAM_STR);

        try {
            $query->execute();

            return $this->get_last_comment();

        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * @param $post_id
     * delete comments for post
     */
    public function post_comments_delete($post_id)
    {
        $query = $this->db->prepare("DELETE FROM `comments` WHERE `post_id` = :post_id");
        $query->bindParam("post_id", $post_id, PDO::PARAM_INT);

        try{
            $query->execute();
            return true;

        } catch(PDOException $ex){
            die($ex->getMessage());
        }
    }
}
