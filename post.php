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

            //get likes
            $likes_data = array_merge($likes->get_likes($post_id));
            $users_liked = explode(" ", $likes_data[0]["users_list"]);
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

        // post and comment actions
        if(!empty($_GET['type']) && ($_GET['type'] == 'post')){
            if(!empty($_GET['mode']) && ($_GET['mode'] == 'delete') && ($userid == $post_data['author_id']) && $posts->post_exists($post_id)){
                echo '<script type="text/javascript">';
                echo 'setTimeout(function () {
                            swal({
                              title: "Jeste li sigurni?",
                              text: "Ova akcija je nepovratna!",
                              type: "warning",
                              animation: true,
                              allowOutsideClick: false,
                              allowEscapeKey: false,
                              showCancelButton: true,
                              confirmButtonText: "Da, obriši.",
                              cancelButtonText: "Ne, odustani!",
                              confirmButtonColor: "#12bc18",
                              cancelButtonColor: "#c60d2c"
                                }).then(function() {
                                    setTimeout(function () { 
                                        window.location = "ajax_functions.php?request=delete-post&post_id='.$post_id.'";
                                    }, 500);
                                }, function(dismiss) {
                                if (dismiss === "cancel") {
                                   swal("Odustanak", "Vaša objava je sigurna :)", "error");
                                   setTimeout(function () { 
                                       window.location = "post.php?id='.$post_id.'";
                                   }, 2000);
                                }
                            });
                      }, 500);';
                echo '</script>';
                die;  
            }
            else if(!empty($_GET['mode']) && ($_GET['mode'] == 'status-change') && ($userid == $post_data['author_id']) && $posts->post_exists($post_id)){
                echo '<script type="text/javascript">';
                echo 'setTimeout(function () {
                            swal({
                              title: "Mjenjanje statusa objave",
                              text: "Ovime mjenjate status objave iz javne u privatnu i obrnuto.",
                              type: "warning",
                              animation: true,
                              allowOutsideClick: false,
                              allowEscapeKey: false,
                              showCancelButton: true,
                              confirmButtonText: "Izmjeni <i class=\"fa fa-check\"></i>",
                              cancelButtonText: "Ne, odustani!",
                              confirmButtonColor: "#12bc18",
                              cancelButtonColor: "#c60d2c"
                                }).then(function() {
                                    setTimeout(function () { 
                                        window.location = "ajax_functions.php?request=status-change&post_id='.$post_id.'&post_status='.$post_data['status'].'";
                                    }, 500);
                                }, function(dismiss) {
                                if (dismiss === "cancel") {
                                   swal("Odustanak", "Status objave ostaje isti.", "warning");
                                   setTimeout(function () { 
                                       window.location = "post.php?id='.$post_id.'";
                                   }, 2000);
                                }
                            });
                      }, 500);';
                echo '</script>';
                die;  
            }            
            else if(!empty($_GET['mode']) && ($_GET['mode'] == 'edit') && ($userid == $post_data['author_id']) && $posts->post_exists($post_id)){
                echo '<script type="text/javascript">';
                echo 'setTimeout(function () {
                            swal({
                              title: "Izmjena objave",
                              text: "Sadašnja objava neće biti sačuvana ako ju izmjenite.",
                              input: "textarea",
                              animation: true,
                              allowOutsideClick: false,
                              allowEscapeKey: false,
                              showCancelButton: true,
                              inputPlaceholder: "Na umu mi je...",
                              inputAutoTrim: true,
                              inputValue: "'.js_string_escape($post_data['post_text']).'",
                              inputClass: "input-textarea",
                              confirmButtonText: "Uredi <i class=\"fa fa-pencil\"></i>",
                              cancelButtonText: "Ne, odustani!",
                              confirmButtonColor: "#12bc18",
                              cancelButtonColor: "#c60d2c"
                                }).then(function() {
                                    var postText = $(".swal2-textarea").val();
                                    $.ajax({
                                      type: "POST",
                                      dataType: "json",
                                      data: {
                                        "post_text": postText,
                                        "post_id": "'.$post_id.'",
                                        "request": "edit-post"
                                      },
                                      url: "ajax_functions.php",
                                      success: function(response, textStatus, jqXHR) {
                                        if(response.status == 0){
                                                swal("Uspjeh", response.message, "success");
                                                setTimeout(function () { 
                                                   window.location = "post.php?id='.$post_id.'";
                                               }, 2000);
                                        }
                                        else{
                                            swal("Greška", response, "warning");
                                            setTimeout(function () { 
                                                window.location = "post.php?id='.$post_id.'";
                                            }, 2000);
                                        }
                                      },
                                      error: function(response, textStatus, jqXHR) {
                                           swal("Greška", response, "error");
                                           setTimeout(function () { 
                                               window.location = "post.php?id='.$post_id.'";
                                           }, 2000);
                                      }
                                    });    
                                }, function(dismiss) {
                                if (dismiss === "cancel") {
                                   swal("Odustanak", "Vaša objava je ostala nepromjenjena.", "error");
                                   setTimeout(function () { 
                                       window.location = "post.php?id='.$post_id.'";
                                   }, 2000);
                                }
                            });
                      }, 500);';
                echo '</script>';
                die;  
            }            
            else {
                echo '<script type="text/javascript">';
                echo 'setTimeout(function () { 
                        swal("Greška", "Niste ovlašteni za ovu akciju ili post nije važeći.", "error");
                      }, 500);
                      setTimeout(function () { 
                        window.location = "post.php?id='.$post_id.'";
                      }, 3000);';
                echo '</script>';
                die;              
            }
        }
        else if (!empty($_GET['type']) && ($_GET['type'] == 'comment')){
            if(!empty($_GET['mode']) && ($_GET['mode'] == 'delete') && $comments->is_user_comment_author($_GET['comment_id'], $userid) && $comments->comment_exists($_GET['comment_id'])){
                echo '<script type="text/javascript">';
                echo 'setTimeout(function () {
                            swal({
                              title: "Jeste li sigurni?",
                              text: "Ova akcija je nepovratna!",
                              type: "warning",
                              animation: true,
                              allowOutsideClick: false,
                              allowEscapeKey: false,
                              showCancelButton: true,
                              confirmButtonText: "Obriši <i class=\"fa fa-trash\"></i>",
                              cancelButtonText: "Ne, odustani!",
                              confirmButtonColor: "#12bc18",
                              cancelButtonColor: "#c60d2c"
                                }).then(function() {
                                    setTimeout(function () { 
                                        window.location = "ajax_functions.php?request=delete-comment&comment_id='.$_GET['comment_id'].'&post_id='.$post_id.'";
                                    }, 500);
                                }, function(dismiss) {
                                if (dismiss === "cancel") {
                                   swal("Odustanak", "Vaš komentar je siguran :)", "warning");
                                   setTimeout(function () { 
                                       window.location = "post.php?id='.$post_id.'";
                                   }, 2000);
                                }
                            });
                      }, 500);';
                echo '</script>';
                die;  
            }   
            else if(!empty($_GET['mode']) && ($_GET['mode'] == 'edit') && $comments->is_user_comment_author($_GET['comment_id'], $userid) && $comments->comment_exists($_GET['comment_id']) && isset($_GET['comment_key'])){

                echo '<script type="text/javascript">';
                echo 'setTimeout(function () {
                            swal({
                              title: "Izmjena komentara",
                              text: "Sadašnji komentar neće biti sačuvan ako ga izmjenite.",
                              input: "textarea",
                              animation: true,
                              allowOutsideClick: false,
                              allowEscapeKey: false,
                              showCancelButton: true,
                              inputPlaceholder: "Želim ti reći...",
                              inputAutoTrim: true,
                              inputValue: "'.$comments_data[$_GET['comment_key']]['comment_text'].'",
                              inputClass: "input-textarea",
                              confirmButtonText: "Uredi <i class=\"fa fa-pencil\"></i>",
                              cancelButtonText: "Ne, odustani!",
                              confirmButtonColor: "#12bc18",
                              cancelButtonColor: "#c60d2c"
                                }).then(function() {
                                    var commentText = $(".swal2-textarea").val();
                                    $.ajax({
                                      type: "POST",
                                      dataType: "json",
                                      data: {
                                        "comment_text": commentText,
                                        "comment_id": "'.$_GET['comment_id'].'",
                                        "post_id": "'.$post_id.'",
                                        "request": "edit-comment"
                                      },
                                      url: "ajax_functions.php",
                                      success: function(response, textStatus, jqXHR) {
                                        if(response.status == 0){
                                                swal("Uspjeh", response.message, "success");
                                                setTimeout(function () { 
                                                   window.location = "post.php?id='.$post_id.'";
                                               }, 2000);
                                        }
                                        else{
                                            swal("Greška", response, "warning");
                                            setTimeout(function () { 
                                                window.location = "post.php?id='.$post_id.'";
                                            }, 2000);
                                        }
                                      },
                                      error: function(response, textStatus, jqXHR) {
                                           swal("Greška", response, "error");
                                           setTimeout(function () { 
                                               window.location = "post.php?id='.$post_id.'";
                                           }, 2000);
                                      }
                                    });    
                                }, function(dismiss) {
                                if (dismiss === "cancel") {
                                   swal("Odustanak", "Vaš komentar je ostao nepromjenjen.", "error");
                                   setTimeout(function () { 
                                       window.location = "post.php?id='.$post_id.'";
                                   }, 2000);
                                }
                            });
                      }, 500);';
                echo '</script>';
                die;  
            }                     
            else {
                echo '<script type="text/javascript">';
                echo 'setTimeout(function () { 
                        swal("Greška", "Niste ovlašteni za ovu akciju ili komentar nije važeći.", "error");
                      }, 500);
                      setTimeout(function () { 
                        window.location = "post.php?id='.$post_id.'";
                      }, 3000);';
                echo '</script>';
                die;              
            }
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

                                // author can like it  own post
                                if($userid == $post_data["author_id"]){
                                    echo "<hr><a href='post.php?id=".$post_id."&amp;type=post&amp;mode=delete' title='Brisanje'><button class='delete'><i class='fa fa-trash'></i></button></a>";
                                        echo "<a href='post.php?id=".$post_id."&amp;type=post&amp;mode=status-change' title='Prromjena statusa'><button class='status-change'><i class='fa fa-eye-slash'></i></button></a>";                                      
                                        echo "<a href='post.php?id=".$post_id."&amp;type=post&amp;mode=edit' title='Uređivanje'><button class='edit'><i class='fa fa-pencil'></i></button></a>";                                       
                                }
                                else{
                                    // has the user already liked the post
                                    if(array_search($userid, $users_liked)){
                                        echo "<hr><button class='inverse_main condensed-like' title='Ne sviđa mi se objava' id='remove-like-button' data-value='".$post_id."' data-request-type='post-like-remove'><i class='fa fa-heart-o'></i></button>";
                                    }
                                    else{
                                        echo "<hr><button class='inverse_main condensed-like' title='Sviđa mi se ova objava' id='give-like-button' data-value='".$post_id."' data-request-type='post-like'><i class='fa fa-heart'></i></button>";
                                    }
                                }

                                echo '  </div>
                                        <div class="col-md-9 text-left">
                                            <strong>Objavljeno: </strong>';
                                            echo date("d.m.Y H:m", strtotime($post_data['date_created']))."h";
                                            echo "<hr>".$post_data["post_text"];
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

                                                echo "<br><i class='fa fa-heart' title='Broj sviđanja'></i> <span id='like-number-container'>".$post_data['like_number']."</span> | ";
                                                echo "<i class='fa fa-pencil' title='Broj komentara'></i> <span id='comment-number-container'>".$post_data['comment_number']."</span>";
                                                echo "<br><i class='fa fa-eye-slash' title='Privatna objava'></i> Privatna objava";

                                                echo "<hr><a href='post.php?id=".$post_id."&amp;type=post&amp;mode=delete' title='Brisanje'><button class='delete'><i class='fa fa-trash'></i></button></a>";
                                                echo "<a href='post.php?id=".$post_id."&amp;type=post&amp;mode=status-change' title='Prromjena statusa'><button class='status-change'><i class='fa fa-eye'></i></button></a>";                                                  
                                                echo "<a href='post.php?id=".$post_id."&amp;type=post&amp;mode=edit' title='Uređivanje'><button class='edit'><i class='fa fa-pencil'></i></button></a>";                                                  
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
                            foreach ($comments_data as $key => $comment) {
                                echo '<div class="row comment-container">
                                        <div class="col-md-3 right-border text-center">';
                                            if(!empty($comment['avatar']))
                                                echo "<img class='img-responsive thumbnail-image' src='".$comment['avatar']."' />";                                        
                                            else
                                                echo "<img class='img-responsive thumbnail-image' src='images/no_image.jpg' />";

                                                echo "<a href='profile.php?user=".$comment['slug']."'>".$comment['full_name']."</a>";

                                            if($comment['author_id'] == $userid) {       
                                                echo "<hr><a href='post.php?id=".$post_id."&amp;type=comment&amp;mode=delete&amp;comment_id=".$comment[0]."' title='Brisanje'><button class='delete'><i class='fa fa-trash'></i></button></a>";
                                                echo "<a href='post.php?id=".$post_id."&amp;type=comment&amp;mode=edit&amp;comment_id=".$comment[0]."&amp;comment_key=".$key."' title='Uređivanje'><button class='edit'><i class='fa fa-pencil'></i></button></a>";    
                                            }                                        

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
