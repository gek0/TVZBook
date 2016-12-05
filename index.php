<?php
require_once ('inc/database.php');

if($session->session_test() === true)
{
	if(isset($_GET["logout"]) && empty($_GET["logout"]))
	{
		$users->logout();
	}

    //get user data
    $userid = (int)$_SESSION['id'];
    $current_user = $users->user_get_data($userid);

    require_once ('inc/header.php');
?>

<body>
    Index fajl!
</body>
    <?php require_once ('inc/footer.php'); ?>
</html>

<?php
}
else
{
	$session->session_false("login.php");
}
