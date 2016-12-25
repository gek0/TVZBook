<?php
/**
*   posts class:    get posts
*                   get posts by offset
*					add new post
*                   get last post
*                   get post
*                   check if post exists
*                   post count for certain user
*                   update comment count on post - increment
*                   update comment count on post - decrement
*                   update like count on post - increment
*                   update like count on post - decrement
*                   delete post
*                   check if user is post author
*                   edit post
?                   change post status
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
     * @param $post_id
     * check if post exists
     */
    public function post_exists($post_id)
    {
        $query = $this->db->prepare("SELECT `id` FROM `posts` WHERE `id` = :post_id LIMIT 1");
        $query->bindParam(":post_id", $post_id, PDO::PARAM_INT);

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
     * @param $post_id
     * check if user is post author
     */
    public function is_user_post_author($post_id, $author_id)
    {
        $query = $this->db->prepare("SELECT `id` FROM `posts` WHERE `id` = :post_id  AND `author_id` = :author_id LIMIT 1");
        $query->bindParam(":post_id", $post_id, PDO::PARAM_INT);
        $query->bindParam(":author_id", $author_id, PDO::PARAM_INT);

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
     * @param $userid
     * get post count for user
     */
    public function post_count($userid)
    {
        $query = $this->db->prepare("SELECT COUNT(*) FROM `posts` WHERE `author_id` = :userid");
        $query->bindParam(":userid", $userid, PDO::PARAM_INT);
        
        try {       
            $query->execute();

            if($query->rowCount() == 1) {
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
     * @param $userid   
     * get posts for user
     */
    public function get_posts_for_user($userid)
    {
        $query = $this->db->prepare("SELECT `posts`.*, `users`.id, `users`.slug, `users`.full_name, `users`.avatar FROM `posts` 
                                        INNER JOIN `users` ON `posts`.author_id = `users`.id 
                                        WHERE `users`.id = :userid 
                                        ORDER BY `posts`.id DESC");
        $query->bindParam(":userid", $userid, PDO::PARAM_INT);
        
        try {       
            $query->execute();

            return $post_data = $query->fetchAll();
            
        } catch(PDOException $ex) {
            die($ex->getMessage());
        }
    }


    /**
     * @param null   
     * get last post
     */
    public function get_last_post()
    {
        $query = $this->db->prepare("SELECT `posts`.*, `users`.id, `users`.slug, `users`.full_name, `users`.avatar FROM `posts` 
                                        INNER JOIN `users` ON `posts`.author_id = `users`.id 
                                        ORDER BY `posts`.id DESC LIMIT 1");
        
        try {       
            $query->execute();

            return $post_data = $query->fetchAll();
            
        } catch(PDOException $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * @param $post_id   
     * get post
     */
    public function get_post($post_id)
    {
        $query = $this->db->prepare("SELECT `posts`.*, `users`.id, `users`.slug, `users`.full_name, `users`.avatar FROM `posts` 
                                        INNER JOIN `users` ON `posts`.author_id = `users`.id 
                                        WHERE `posts`.id = :post_id
                                        ORDER BY `posts`.id DESC LIMIT 1");
        $query->bindParam(":post_id", $post_id, PDO::PARAM_INT);
        
        try {       
            $query->execute();

            return $post_data = $query->fetchAll();
            
        } catch(PDOException $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * @param $start, limit   
     * get posts by offset
     */
    public function get_posts_by_offset($start, $limit)
    {
        $query = $this->db->prepare("SELECT * FROM `posts` INNER JOIN `users` ON `posts`.author_id = `users`.id
                                        ORDER BY `posts`.id 
                                        DESC LIMIT :limit OFFSET :start");
        $query->bindParam(":limit", intval($limit, 10), PDO::PARAM_INT);
        $query->bindParam(":start", intval($start, 10), PDO::PARAM_INT);
        
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

        $query = $this->db->prepare("INSERT INTO `posts` (`author_id`, `post_text`, `status`, `date_created`) 
                                        VALUES (:author_id, :post_text, :status, :date_created)");
        $query->bindParam(":author_id", $author_id, PDO::PARAM_INT);
        $query->bindParam(":post_text", $post_text, PDO::PARAM_STR);
        $query->bindParam(":status", $status, PDO::PARAM_STR);
        $query->bindParam(":date_created", $date_created, PDO::PARAM_STR);

        try {
            $query->execute();

            return $this->get_last_post();

        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * @param $post_id
     * update comment counter for post - increment
     */
    public function update_comments_num($post_id)
    {
        $query = $this->db->prepare("UPDATE `posts` SET `comment_number` = `comment_number` + 1 WHERE `id` = :post_id");
        $query->bindParam(":post_id", $post_id, PDO::PARAM_INT);

        try {       
            $query->execute();

            return true;

        } catch(PDOException $ex){
            die($ex->getMessage());
        }       
    }

    /**
     * @param $post_id
     * update comment counter for post - decrement
     */
    public function update_comments_num_decrement($post_id)
    {
        $query = $this->db->prepare("UPDATE `posts` SET `comment_number` = `comment_number` - 1 WHERE `id` = :post_id");
        $query->bindParam(":post_id", $post_id, PDO::PARAM_INT);

        try {       
            $query->execute();

            return true;

        } catch(PDOException $ex){
            die($ex->getMessage());
        }       
    }    

    /**
     * @param $post_id
     * update like counter for post - increment
     */
    public function update_likes_num_decrement($post_id)
    {
        $query = $this->db->prepare("UPDATE `posts` SET `like_number` = `like_number` - 1 WHERE `id` = :post_id");
        $query->bindParam(":post_id", $post_id, PDO::PARAM_INT);

        try {       
            $query->execute();

            return true;

        } catch(PDOException $ex){
            die($ex->getMessage());
        }       
    }

    /**
     * @param $post_id
     * update like counter for post
     */
    public function update_likes_num($post_id)
    {
        $query = $this->db->prepare("UPDATE `posts` SET `like_number` = `like_number` + 1 WHERE `id` = :post_id");
        $query->bindParam(":post_id", $post_id, PDO::PARAM_INT);

        try {       
            $query->execute();

            return true;

        } catch(PDOException $ex){
            die($ex->getMessage());
        }       
    }

    /**
     * @param $post_id
     * delete post
     */
    public function post_delete($post_id)
    {
        $query = $this->db->prepare("DELETE FROM `posts` WHERE `id` = :post_id");
        $query->bindParam("post_id", $post_id, PDO::PARAM_INT);

        try{
            $query->execute();
            return true;

        } catch(PDOException $ex){
            die($ex->getMessage());
        }
    }

    /**
     * @param $post_id
     * @param $post_text     
     * edit post
     */
    public function post_edit($post_id, $post_text)
    {
        $post_text = htmlspecialchars($post_text, ENT_QUOTES, "UTF-8");

        $query = $this->db->prepare("UPDATE `posts` SET `posts`.post_text = :post_text WHERE `id` = :post_id");
        $query->bindParam("post_text", $post_text, PDO::PARAM_STR);
        $query->bindParam("post_id", $post_id, PDO::PARAM_INT);

        try{
            $query->execute();
            return true;

        } catch(PDOException $ex){
            die($ex->getMessage());
        }
    }

    /**
     * @param $post_id
     * @param $post_status    
     * edit post status
     */
    public function post_status_edit($post_id, $post_status)
    {
        $query = $this->db->prepare("UPDATE `posts` SET `posts`.status = :post_status WHERE `id` = :post_id");
        $query->bindParam("post_status", $post_status, PDO::PARAM_STR);
        $query->bindParam("post_id", $post_id, PDO::PARAM_INT);

        try{
            $query->execute();
            return true;

        } catch(PDOException $ex){
            die($ex->getMessage());
        }
    } 
}