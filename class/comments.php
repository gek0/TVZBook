<?php
/**
*   comments class: comment count for certain user
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

}