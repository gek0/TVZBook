<?php
require_once ('inc/database.php');

if($session->session_test() === true){

    //get user data
    $userid = (int)$_SESSION[$session_id];
    $current_user = $users->user_get_data($userid);

    //update user last online time
    $users->update_online_time($userid);

    require_once ('inc/header.php');

    if($_SERVER["REQUEST_METHOD"] == 'GET' && !empty($_GET['user'])){
    	$user_slug = $_GET['user'];

    	if($users->user_exists_slug($user_slug)){
    		$user_data = call_user_func_array('array_merge', $users->get_user($user_slug));

    		//get user posts and comments count
		    $posts_count = $posts->post_count($user_data['id']);
		    $comments_count = $comments->comment_count($user_data['id']);
            
            //get user likes count            
            $likes_data = $likes->count_likes_for_user($user_data['id']);
            $likes_count = 0;
            $temp_like_arr = [];

            foreach($likes_data as $like){
                $temp_like_arr = explode(" ", $like["users_list"]);

                // search only posts with likes
                if(!empty($temp_like_arr[1])){
                    if(array_search($user_data['id'], $temp_like_arr)){
                        $likes_count++;      
                    }
                    else{
                        continue;
                    }
                }
            }
    	}
    	else{
			echo '<script type="text/javascript">';
			echo 'setTimeout(function () { 
					swal("Greška", "Korisnik ne postoji.", "error");
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
    	<div class="module form-module-wider extended text-center">
		<div class=""></div>
		<div class="form">
			<h2 class="text-center">Profil korisnika <br><span class="expand"><?php echo $user_data['full_name']; ?></span></h2>
                <?php 
                    if(!empty($user_data['avatar']) && $user_data['avatar'] != "-"){
                        echo "<img class='img-responsive thumbnail-image' src='".$user_data['avatar']."' alt='Avatar korisnika ".$user_data['username']."' title='Avatar korisnika ".$user_data['username']."' />";
                    }
                    else{
                        echo "<img class='img-responsive thumbnail-image' src='images/no_image.jpg' alt='Nema avatara' title='Nema avatara' />";    
                    }

                    echo "<hr><span><i class='fa fa-envelope'></i> | <i class='fa fa-calendar'></i> | <i class='fa fa-pencil'></i></span>";
                    echo "<p class='user_stats'>Korisnika se može kontaktirati na e-mail adresu <a href='mailto:".$user_data['email']."'><strong>".$user_data['email']."</strong></a>.</p>";
                    echo "<p class='user_stats'>Registrirao se na ".SITE_NAME." <strong>".date("d.m.Y.", strtotime($user_data['registration_date']))."</strong></p>";
                    echo "<p class='user_stats'>Za to vrijeme je uspio napisati <strong>".$posts_count[0][0]."</strong> postova i <strong>".$comments_count[0][0]."</strong> komentara.</p>";
                    echo "<p class='user_stats'>Svidjelo mu se <strong>".$likes_count."</strong> objava.</p>";                    
                    echo "<p class='user_stats'>Zadnje je viđen online <strong>".date("d.m.Y. \u H:i:s", strtotime($user_data['last_online']))."</strong></p>";
                ?>

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
