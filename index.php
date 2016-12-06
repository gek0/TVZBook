<?php
require_once ('inc/database.php');

if($session->session_test() === true){
	if(isset($_GET["logout"]) && empty($_GET["logout"])){
		$users->logout();
	}

    //get user data
    $userid = (int)$_SESSION[$session_id];
    $current_user = $users->user_get_data($userid);
    //update user last online time
    $users->update_online_time($userid);

    //get all posts
    $posts_data = $posts->get_posts();

    //get user posts count
    $posts_count = $posts->post_count($userid);

    require_once ('inc/header.php');
?>
<body>
    <div id="backloader"></div>
    <div class="text-center">
        <h1 class="main-header">TVZBook</h1>
    </div>
    <section class="container">
        <div class="row">
            <div class="col-md-3 user-section text-center">
                <?php 
                    if(!empty($current_user[0]['avatar']) && $current_user[0]['avatar'] != "-"){
                        echo "<img class='img-responsive thumbnail-image' src='".$current_user[0]['avatar']."' />";
                    }
                    else{
                        echo "<img class='img-responsive thumbnail-image' src='images/no_image.jpg' />";    
                    }
                ?>
                <span title="Ime i prezime (korisničko ime)"><i class="fa fa-user"></i> <?php echo $current_user[0]['full_name']." (".$current_user[0]['username'].")"; ?></span><br>
                <span title="E-mail adresa"><i class="fa fa-envelope"></i> <?php echo $current_user[0]['email']; ?></span><br>
                <span title="Član od"><i class="fa fa-calendar"></i> <?php echo date("d.m.Y", strtotime($current_user[0]['registration_date'])); ?></span><br>
                <span title="Objavio postova"><i class="fa fa-pencil"></i> <?php echo $posts_count[0][0]; ?></span><br><hr>

                <a href="profile_settings.php">
                    <button class="main_button" id="view-settings-button">Uredi profil <i class="fa fa-cogs"></i></button>
                </a>
                <a href="index.php?logout">
                    <button class="main_button" id="logout-button">Odlogiraj se <i class="fa fa-sign-out"></i></button>
                </a>
            </div>
            <div class="col-md-9 posts-section">
                <div class="new-comment">
                    <form action="" method="POST" name="new-post" id="new-post-form" role="form">
                        <textarea name="post_text" placeholder="Na umu mi je..." required></textarea>

                        <div class="text-center">
                            <input type="radio" name="status" value="public" checked> JAVNI post (svi vide)<br>
                            <input type="radio" name="status" value="private"> PRIVATNI post (samo ja vidim)<br>
                        </div>
                        <input type="hidden" name="request" value="new-post">

                        <button class="main_button" id="new-post-button">Objavi <i class="fa fa-pencil"></i></button>
                    </form>

                    <div id="notification-data" class="notification-container"></div> 
                </div>

                <hr>
                
                <div class="comments-list">
                    <?php
                        foreach ($posts_data as $post) {
                            if($post['status'] == 'public'){
                                echo '<div class="row comment-container">
                                        <div class="col-md-3 right-border">';
                                            if(!empty($post['avatar']) && $post['avatar'] != "-")
                                                echo "<img class='img-responsive thumbnail-image' src='".$post['avatar']."' />";                                        
                                            else
                                                echo "<img class='img-responsive thumbnail-image' src='images/no_image.jpg' />";

                                                echo "<span class='notif-borderless'>".$post['full_name']."<br>
                                                        <i class='fa fa-eye' title='Post je vidljiv svima'></i>
                                                      </span>";
                                echo '  </div>
                                        <div class="col-md-9">
                                            <strong>Objavljeno: </strong>';
                                            echo date("d.m.Y H:m", strtotime($post['date_created']))."h<hr>";
                                            echo $post["post_text"];
                                echo '  </div>
                                      </div>'; 
                            }
                            else if($post['status'] == 'private' && $userid == $post['author_id']){
                                echo '<div class="row comment-container-private">
                                        <div class="col-md-3 right-border">';
                                            if(!empty($post['avatar']) && $post['avatar'] != "-")
                                                echo "<img class='img-responsive thumbnail-image' src='".$post['avatar']."' />";                                        
                                            else
                                                echo "<img class='img-responsive thumbnail-image' src='images/no_image.jpg' />";

                                                echo "<span class='notif-borderless'>".$post['full_name']."<br>
                                                        <i class='fa fa-eye-slash' title='Post je vidljiv samo tebi'></i>
                                                      </span>";
                                echo '  </div>
                                        <div class="col-md-9">
                                            <strong>Objavljeno: </strong>';
                                            echo date("d.m.Y H:m", strtotime($post['date_created']))."h<hr>";
                                            echo $post["post_text"];
                                echo '  </div>
                                      </div>';    
                            }  
                        }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <?php require_once ('inc/footer.php'); ?>
</body>
</html>
<?php
}
else{
	$session->session_false("login.php");
}
