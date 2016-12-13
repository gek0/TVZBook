<?php
/**
*	database credentials
*/

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "zezancija";
$dbname = "tvzbook";

session_start();
$session_id = "id";

//define global variables
date_default_timezone_set('Europe/Zagreb');
define('SITE_NAME', 'TVZBook', true);
define('START', 0, true);
define('LIMIT', 5, true);
