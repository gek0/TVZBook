<?php
/**
*   likes class:	initialize likes for post 
*					add like to post
*					likes for single post
*                   likes count for user
*
*/

class likes
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
     *	initialize likes on post
     */
	public function initialize_post_likes($post_id) 
	{
        $query = $this->db->prepare("INSERT INTO `likes` (`post_id`) VALUES (:post_id)");
        $query->bindParam(":post_id", $post_id, PDO::PARAM_INT);

        try {
            $query->execute();

            return true;

        } catch (PDOException $ex) {
            die($ex->getMessage());
        }

	}

    /**
     * @param $user_id
     * @param $post_id
     *	add like to post
     */
	public function add_like($user_id, $post_id) 
	{
        $query = $this->db->prepare("UPDATE `likes` SET `users_list` = CONCAT(IFNULL(`users_list`, ''), :user_id) WHERE `post_id` = :post_id");
        $query->bindParam(":user_id", $user_id, PDO::PARAM_STR);
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
     *	get likes for single post
     */
	public function get_likes($post_id) 
	{

        $query = $this->db->prepare("SELECT `users_list` FROM `likes` WHERE `post_id` = :post_id");
        $query->bindParam(":post_id", $post_id, PDO::PARAM_INT);
        
        try {       
            $query->execute();

            return $likes_data = $query->fetchAll();
            
        } catch(PDOException $ex) {
            die($ex->getMessage());
        }
    }

    /**
     * @param $user_id
     *  get likes count for user
     */
    public function count_likes_for_user($user_id) 
    {
        $query = $this->db->prepare("SELECT * FROM `likes`");
        
        try {       
            $query->execute();

            return $likes_data = array_merge($query->fetchAll());
            
        } catch(PDOException $ex) {
            die($ex->getMessage());
        }
    }
}