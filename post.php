<?php
require_once ('inc/database.php');

if($session->session_test() === true){

    //get user data
    $userid = (int)$_SESSION[$session_id];
    $current_user = $users->user_get_data($userid);

    //update user last online time
    $users->update_online_time($userid);

    require_once ('inc/header.php');

    if($_SERVER["REQUEST_METHOD"] == 'GET' && !empty($_GET['id'])){
    	$post_id = $_GET['id'];

    	if($posts->post_exists($post_id)){
    		$post_data = call_user_func_array('array_merge', $posts->get_post($post_id));

    	}
    	else{
			echo '<script type="text/javascript">';
			echo 'setTimeout(function () { 
					swal("Greška", "Post ne postoji.", "error");
				  }, 500);
				  setTimeout(function () { 
					window.location = "index.php";
				  }, 3000);';
			echo '</script>';
			die;      		
    	}
    }
    else{
		echo '<script type="text/javascript">';
		echo 'setTimeout(function () { 
				swal("Greška", "Nevažeća akcija", "error");
			  }, 500);
			  setTimeout(function () { 
				window.location = "index.php";
			  }, 3000);';
		echo '</script>';
		die;    	
    }
?>

<body>
	<div id="backloader"></div>
	<div class="text-center">
        <h1 class="main-header"><?php echo SITE_NAME; ?></h1>
    </div>
    <section class="container-blank">
    	<div class="module form-module-wider extended-wide text-center">
		<div class=""></div>
		<div class="form">
                ---

		</div>
	</div>

	<hr>
	<a href="index.php">
    	<button class="inverse_main" id="return-button">Povratak <i class="fa fa-home"></i></button>
    </a>
    </section>

    <?php require_once ('inc/footer.php'); ?>
</body>
</html>
<?php
}
else{
	$session->session_false("login.php");
}
