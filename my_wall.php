<?php
require_once ('inc/database.php');

if($session->session_test() === true){
	if(isset($_GET["logout"]) && empty($_GET["logout"])){
		$users->logout();
	}

    //get user data
    $userid = (int)$_SESSION[$session_id];
    $current_user = call_user_func_array('array_merge', $users->user_get_data($userid));

    //update user last online time
    $users->update_online_time($userid);

    //get posts
    $posts_data = $posts->get_posts_for_user($userid);

    //get user posts and comments count
    $posts_count = $posts->post_count($userid);
    $comments_count = $comments->comment_count($userid);

    require_once ('inc/header.php');
?>
<body>
    <div id="backloader"></div>
    <div class="text-center">
        <h1 class="main-header"><?php echo SITE_NAME; ?></h1>
    </div>
    <section class="container">
        <div class="row">
            <div class="col-md-3 user-section">
                <?php 
                    if(!empty($current_user['avatar']) && $current_user['avatar'] != "-"){
                        echo "<img class='img-responsive thumbnail-image' src='".$current_user['avatar']."' alt='Avatar korisnika ".$current_user['username']."' title='Avatar korisnika ".$current_user['username']."' />";
                    }
                    else{
                        echo "<img class='img-responsive thumbnail-image' src='images/no_image.jpg' alt='Nema avatara' title='Nema avatara' />";    
                    }
                ?>
                <table class="table">
                    <tr>
                        <td title="Ime i prezime"><i class="fa fa-user fa-big"></i></td>
                        <td><?php echo $current_user['full_name']." (".$current_user['username'].")"; ?></td>
                    </tr>
                    <tr>
                        <td title="E-mail adresa"><i class="fa fa-envelope fa-big"></i></td>
                        <td><?php echo $current_user['email']; ?></td>
                    </tr>
                    <tr>
                        <td title="Član od"><i class="fa fa-calendar fa-big"></i></td>
                        <td><?php echo date("d.m.Y.", strtotime($current_user['registration_date'])); ?></td>
                    </tr>
                    <tr>
                        <td title="Postova i komentara"><i class="fa fa-pencil fa-big"></i></td>
                        <td><?php echo "postova (".$posts_count[0][0].") | komentara (".$comments_count[0][0].")"; ?></td>
                    </tr>
                </table>
                <hr>

                <a href="index.php">
                    <button class="inverse_main" id="view-wall-button">Naslovnica <i class="fa fa-home"></i></button>
                </a>
                <a href="profile.php?user=<?php echo $current_user['slug']; ?>">
                    <button class="inverse_main" id="view-wall-button">Moj profil <i class="fa fa-user"></i></button>
                </a>                
                <a href="profile_settings.php">
                    <button class="inverse_main" id="view-settings-button">Uredi profil <i class="fa fa-cogs"></i></button>
                </a>
                <a href="index.php?logout">
                    <button class="inverse_main" id="logout-button">Odlogiraj se <i class="fa fa-sign-out"></i></button>
                </a>
            </div>
            <div class="col-md-9 posts-section">
                <h2 class="text-center">Lista mojih postova <?php echo "(".$posts_count[0][0].")"; ?></h2>

                <hr>
                
                <div class="posts-list">
                    <?php
                        foreach ($posts_data as $post) {
                            if($post['status'] == 'public'){
                                    echo '<div class="row post-container">
                                            <div class="col-md-3 right-border text-center">';
                                                if(!empty($post['avatar']))
                                                    echo "<img class='img-responsive thumbnail-image' src='".$post['avatar']."' />";                                        
                                                else
                                                    echo "<img class='img-responsive thumbnail-image' src='images/no_image.jpg' />";

                                                    echo "<a href='profile.php?user=".$post['slug']."'>".$post['full_name']."</a>";

                                                    echo "<br><i class='fa fa-heart' title='Broj sviđanja'></i> ".$post['like_number']." | ";
                                                    echo "<i class='fa fa-pencil' title='Broj komentara'></i> ".$post['comment_number'];
                                                    echo "<br><i class='fa fa-eye' title='Javna objava'></i>";
                                    echo '  </div>
                                                <div class="col-md-9">
                                                    <strong>Objavljeno: </strong>';
                                                    echo date("d.m.Y H:m", strtotime($post['date_created']))."h<hr>";
                                                    echo $post["post_text"];

                                                    echo "<div class='text-center'>
                                                                <a href='post.php?id=".$post[0]."'>
                                                                    <button class='inverse_main_small'>Pregledaj <i class='fa fa-eye'></i></button>
                                                                </a>
                                                            </div>";
                                    echo '  </div>
                                            </div>'; 
                            }
                            else if($post['status'] == 'private' && ($userid == $post['author_id'])){
                                    echo '<div class="row post-container post-private">
                                            <div class="col-md-3 right-border text-center">';
                                                if(!empty($post['avatar']))
                                                    echo "<img class='img-responsive thumbnail-image' src='".$post['avatar']."' />";                                        
                                                else
                                                    echo "<img class='img-responsive thumbnail-image' src='images/no_image.jpg' />";

                                                    echo "<a href='profile.php?user=".$post['slug']."'>".$post['full_name']."</a>";

                                                    echo "<br><i class='fa fa-heart' title='Broj sviđanja'></i> ".$post['like_number']." | ";
                                                    echo "<i class='fa fa-pencil' title='Broj komentara'></i> ".$post['comment_number'];
                                                    echo "<br><i class='fa fa-eye-slash' title='Privatna objava'></i> Privatna objava";
                                    echo '  </div>
                                                <div class="col-md-9">
                                                    <strong>Objavljeno: </strong>';
                                                    echo date("d.m.Y H:m", strtotime($post['date_created']))."h<hr>";
                                                    echo $post["post_text"];

                                                    echo "<div class='text-center'>
                                                                <a href='post.php?id=".$post[0]."'>
                                                                    <button class='inverse_main_small'>Pregledaj <i class='fa fa-eye'></i></button>
                                                                </a>
                                                            </div>";
                                    echo '  </div>
                                            </div>'; 
                                }
                                else{                                    
                                    continue;
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
