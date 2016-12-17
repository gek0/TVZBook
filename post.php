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

            if($post_data['status'] == 'private' && ($userid != $post_data['author_id'])){
                echo '<script type="text/javascript">';
                echo 'setTimeout(function () { 
                        swal("Greška", "Ovaj post je privatan.", "error");
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

            <?php
                if($post_data['status'] == 'public'){
                    echo '<div class="row post-container-unbordered">
                            <div class="col-md-3 right-border text-center">';
                            if(!empty($post_data['avatar']))
                                echo "<img class='img-responsive thumbnail-image' src='".$post_data['avatar']."' />";                                        
                            else
                                echo "<img class='img-responsive thumbnail-image' src='images/no_image.jpg' />";

                                echo "<a href='profile.php?user=".$post_data['slug']."'>".$post_data['full_name']."</a>";

                                echo "<br><i class='fa fa-heart' title='Broj sviđanja'></i> ".$post_data['like_number']." | ";
                                echo "<i class='fa fa-pencil' title='Broj komentara'></i> ".$post_data['comment_number'];
                                echo "<br><i class='fa fa-eye' title='Javna objava'></i>";
                                echo '  </div>
                                        <div class="col-md-9">
                                            <strong>Objavljeno: </strong>';
                                            echo date("d.m.Y H:m", strtotime($post_data['date_created']))."h<hr>";
                                            echo $post_data["post_text"];
                                echo '  </div>
                                      </div>'; 
                            }
                            else if($post_data['status'] == 'private' && ($userid == $post_data['author_id'])){
                                echo '<div class="row post-container-unbordered post-private">
                                        <div class="col-md-3 right-border text-center">';
                                            if(!empty($post_data['avatar']))
                                                echo "<img class='img-responsive thumbnail-image' src='".$post_data['avatar']."' />";                                        
                                            else
                                                echo "<img class='img-responsive thumbnail-image' src='images/no_image.jpg' />";

                                                echo "<a href='profile.php?user=".$post_data['slug']."'>".$post_data['full_name']."</a>";

                                                echo "<br><i class='fa fa-heart' title='Broj sviđanja'></i> ".$post_data['like_number']." | ";
                                                echo "<i class='fa fa-pencil' title='Broj komentara'></i> ".$post_data['comment_number'];
                                                echo "<br><i class='fa fa-eye-slash' title='Privatna objava'></i> Privatna objava";
                                    echo '  </div>
                                                <div class="col-md-9">
                                                    <strong>Objavljeno: </strong>';
                                                    echo date("d.m.Y H:m", strtotime($post_data['date_created']))."h<hr>";
                                                    echo $post_data["post_text"];
                                    echo '  </div>
                                            </div>'; 
                    }
                ?>

                <div class="comments-list">

                </div>
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
