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

            //get comments
            $comments_data = $comments->get_comments($post_id);
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

                                echo "<br><i class='fa fa-heart' title='Broj sviđanja'></i> <span id='like-number-container'>".$post_data['like_number']."</span> | ";
                                echo "<i class='fa fa-pencil' title='Broj komentara'></i> <span id='comment-number-container'>".$post_data['comment_number']."</span>";
                                echo "<br><i class='fa fa-eye' title='Javna objava'></i>";
                                echo "<hr><button class='inverse_main condensed-like' title='Sviđa mi se ova objava' id='give-like-button' data-value='".$post_id."' data-request-type='post-like'><i class='fa fa-heart'></i></button>";
                                echo '  </div>
                                        <div class="col-md-9 text-left">
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
                                                <div class="col-md-9 text-left">
                                                    <strong>Objavljeno: </strong>';
                                                    echo date("d.m.Y H:m", strtotime($post_data['date_created']))."h<hr>";
                                                    echo $post_data["post_text"];
                                    echo '  </div>
                                            </div>'; 
                    }
                ?>

                <div class="new-comment">
                    <form action="" method="POST" name="new-comment" id="new-comment-form" role="form">
                        <textarea name="comment_text" id="new-comment-textarea" placeholder="Želim ti reći..." required></textarea>
                        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                        <input type="hidden" name="request" value="new-comment">

                        <button class="inverse_main" id="new-comment-button">Objavi <i class="fa fa-pencil"></i></button>
                    </form>

                    <div id="notification-data" class="notification-container"></div> 
                </div>

                <hr>
                <div id="notification-data-load" class="notification-container"></div>
                <div class="comments-list">
                    <?php
                        if(!empty($comments_data)){
                            foreach ($comments_data as $comment) {
                                echo '<div class="row comment-container">
                                        <div class="col-md-3 right-border text-center">';
                                            if(!empty($comment['avatar']))
                                                echo "<img class='img-responsive thumbnail-image' src='".$comment['avatar']."' />";                                        
                                            else
                                                echo "<img class='img-responsive thumbnail-image' src='images/no_image.jpg' />";

                                                echo "<a href='profile.php?user=".$comment['slug']."'>".$comment['full_name']."</a>";
                                echo '  </div>
                                        <div class="col-md-9 text-left">
                                            <strong>Objavljeno: </strong>';
                                            echo date("d.m.Y H:m", strtotime($comment['date_created']))."h<hr>";
                                            echo $comment["comment_text"];
                                echo '  </div>
                                      </div>'; 
                            }
                        }
                        else{
                            echo "<h5 class='text-center' id='no-comments'>Objava nema komentara. <br>Zašto ne bi dodao/la jedan? :)</h5>";
                        }
                    ?>
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
