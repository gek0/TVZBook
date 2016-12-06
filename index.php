<?php
require_once ('inc/database.php');

if($session->session_test() === true){
	if(isset($_GET["logout"]) && empty($_GET["logout"])){
		$users->logout();
	}

    //get user data
    $userid = (int)$_SESSION[$session_id];
    $current_user = $users->user_get_data($userid);

    require_once ('inc/header.php');
?>
<body>
    <div id="backloader"></div>
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
                <span title="Član od"><i class="fa fa-calendar"></i> <?php echo date("d.m.Y", strtotime($current_user[0]['registration_date'])); ?></span><br><hr>

                <a href="profile_settings.php">
                    <button class="main_button" id="view-settings-button">Uredi profil <i class="fa fa-cogs"></i></button>
                </a>
                <a href="index.php?logout">
                    <button class="main_button" id="logout-button">Odlogiraj se <i class="fa fa-sign-out"></i></button>
                </a>
            </div>
            <div class="col-md-9 posts-section">
                POSTS
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
