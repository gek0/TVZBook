<?php
/**
*   database connection object and included files
*/
require_once ('inc/config.php');
require_once ('inc/functions.php');
require_once ('class/session.php');
require_once ('class/users.php');
require_once ('class/posts.php');
require_once ('class/comments.php');

try{
	$db = new PDO("mysql:host={$dbhost};dbname={$dbname};charset=utf8", $dbuser, $dbpass);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 
catch(PDOException $ex){
	die($ex->getMessage());
}

//initialize objects
$users = new users($db);
$posts = new posts($db);
$comments = new comments($db);
$session = new session();

//error/announce arrays
$errors = array();
$announces = array();
