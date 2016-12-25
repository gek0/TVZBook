<?php
require_once ('inc/database.php');

if($session->session_test() === true){

    //get user data
    $userid = (int)$_SESSION[$session_id];
    $current_user = $users->user_get_data($userid);

    //update user last online time
    $users->update_online_time($userid);

    require_once ('inc/header.php');

    // grab stats
    $users_count = call_user_func_array('array_merge', $users->users_count());
    $first_user_date = call_user_func_array('array_merge', $users->get_first_registred_user());
    $last_user_date = call_user_func_array('array_merge', $users->get_last_registred_user());
    $posts_count = call_user_func_array('array_merge', $posts->post_count_site());
    $posts_count_public = call_user_func_array('array_merge', $posts->post_count_site_by_status("public"));
    $posts_count_private = call_user_func_array('array_merge', $posts->post_count_site_by_status("private"));

    $likes_data = $likes->count_likes();
    $likes_count = 0;
    $temp_like_arr = [];
    foreach($likes_data as $like){
        $temp_like_arr = explode(" ", $like["users_list"]);
        // search only posts with likes
        if(!empty($temp_like_arr[1])){
            $likes_count++;      
        }
    }

    $comments_count = call_user_func_array('array_merge', $comments->comment_count_site());
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
			<h2 class="text-center">Statistika stranice <br><span class="expand"><?php echo SITE_NAME; ?></span></h2>
                <?php 
                    echo "<br><i class='fa fa-3x fa-spin fa-pie-chart'></i></span><hr>";
                    echo "<p class='user-stats'>Registrirano je ukupno <strong>".$users_count['num_of_users']."</strong> korisnika.</p><br>";
                    echo "<p class='user-stats'>Prvi korisnik se registrirao <strong>".date("d.m.Y. \u H\h", strtotime($first_user_date['registration_date']))."</strong>.</p>";
                    echo "<p class='user-stats'>A zadnji nam se pridružio <strong>".date("d.m.Y. \u H\h", strtotime($last_user_date['registration_date']))."</strong>.</p><br>";
                    echo "<p class='user-stats'>Od otvorenja stranice, korisnici su napisali <strong>".$posts_count['num_of_posts']."</strong> objava.</p>";
                    echo "<p class='user-stats'>Od toga je javnih <strong>".$posts_count_public['num_of_posts']."</strong>, a privatnih <strong>".$posts_count_private['num_of_posts']."</strong> objava.</p><br>";

                    echo "<p class='user-stats'>Korisnicima se ukupno svidjelo <strong>".$likes_count."</strong> objava.</p>";
                    echo "<p class='user-stats'>Također, komentirali su <strong>".$comments_count['num_of_comments']."</strong> puta.</p>";
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
