<?php
require_once ('inc/database.php');

if($session->session_test() === true){

    //get user data
    $userid = (int)$_SESSION[$session_id];
    $current_user = $users->user_get_data($userid);
    //update user last online time
    $users->update_online_time($userid);

    require_once ('inc/header.php');

    function upload_avatar(){
    	$extensions = array("jpeg", "jpg", "png", "gif", "bmp");
		$max_file_size = 1050000; //1MB
		$path = "uploads/user_avatars/"; //dir name where to store avatar

		$file_name = random_string(15);
		$file_size = $_FILES['avatar']['size'];
		$file_tmp = $_FILES['avatar']['tmp_name'];
		$file_type = $_FILES['avatar']['type'];
		$file_ext = explode('.', $_FILES['avatar']['name']);
		$file_ext = end($file_ext);				

		if(in_array($file_ext, $extensions ) === false){
			echo '<script type="text/javascript">';
			echo 'setTimeout(function () { 
					swal("Greška", "Ekstenzija avatara nije dozvoljena.", "error");
				  }, 500);
				  setTimeout(function () { 
					window.location = "profile_settings.php";
				  }, 2000);';
			echo '</script>';
			die;
		} 
		if($file_size > $max_file_size){
			echo '<script type="text/javascript">';
			echo 'setTimeout(function () { 
					swal("Greška", "Max. veličina slike je 1MB", "error");
				  }, 500);
				  setTimeout(function () { 
					window.location = "profile_settings.php";
				  }, 2000);';
			echo '</script>';
			die;
		}
		if(!file_exists($path)){
            mkdir($path, 0777);
        }
        if(move_uploaded_file($file_tmp, $path.$file_name.".".$file_ext) && file_exists($path)){
            return $avatar = $path.$file_name.".".$file_ext;
        }
    }


	if($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['request'])){    
    	if(empty($_POST["full_name"]) || empty($_POST["email"]) || empty($_POST["phone_number"])){
			echo '<script type="text/javascript">';
			echo 'setTimeout(function () { 
					swal("Greška", "Sva polja osim avatara su obavezna.", "error");
				  }, 500);
				  setTimeout(function () { 
					window.location = "profile_settings.php";
				  }, 2000);';
			echo '</script>';
			die;	  		
	  	}
	    else if($_POST["password"] != $_POST["password_again"]){
			echo '<script type="text/javascript">';
			echo 'setTimeout(function () { 
					swal("Greška", "Unjete lozinke nisu identične.", "error");
				  }, 500);
				  setTimeout(function () { 
					window.location = "profile_settings.php";
				  }, 2000);';
			echo '</script>';
			die;
	    }
	    else{
	    	if(empty($_POST["password"]) && empty($_POST["password_again"])){
	    		$_POST["password"] = "-";
	    		$_POST["password_again"] = "-";					
	    	}

	    	// user avatar
	    	if(empty($current_user[0]['avatar']) || $current_user[0]['avatar'] == "-"){ //user nema avatar
	    		if(isset($_FILES["avatar"])){
	    			$avatar = upload_avatar();
	    		}
	    		else{
	    			$avatar = "-";
	    		}			
	    	}
	    	else{ //user već ima avatar
	    		if(isset($_FILES["avatar"]) && !isset($_POST["avatar_status"])){
	    			$avatar = upload_avatar();
	    		}
	    		else if(isset($_FILES["avatar"]) && isset($_POST["avatar_status"])){
	    			$avatar = $current_user[0]['avatar'];
	    		}
	    		else{
	    			$avatar = "-";
	    		}	
	    	}

		  	$result = $users->update_settings($_SERVER['REMOTE_ADDR'], $_POST["full_name"], $_POST["password"], 
		  								$_POST["password_again"], $_POST["email"], $_POST["phone_number"], $avatar);
		  	if($result === true){
				echo '<script type="text/javascript">';
				echo 'setTimeout(function () { 
						swal("Greška", "Profilno uspješno izmjenjen..", "success");
					  }, 500);
					  setTimeout(function () { 
						window.location = "profile_settings.php";
					  }, 2000);';
				echo '</script>';
				die;
		    }
		    else{
				echo '<script type="text/javascript">';
				echo 'setTimeout(function () { 
						swal("Greška", "Neki podaci nisu važeći ili poslani.", "error");
					  }, 500);
					  setTimeout(function () { 
						window.location = "profile_settings.php";
					  }, 2000);';
				echo '</script>';
				die;
		    }
		}
	}
?>
<body>
	<div id="backloader"></div>
	<div class="text-center">
        <h1 class="main-header">TVZBook</h1>
    </div>
    <section class="container-blank">
    	<div class="module form-module-wider">
		<div class=""></div>
		<div class="form">
			<h2>Izmjena postavki profila</h2>
			<form action="" method="POST" name="edit-settings" id="settings-edit-form" enctype="multipart/form-data" role="form">
		    	<input type="text" name="full_name" placeholder="Ime i prezime" value="<?php echo $current_user[0]['full_name']; ?>" required>
				<input type="password" name="password" placeholder="Nova lozinka" autocomplete="off">
				<input type="password" name="password_again" placeholder="Ponovi lozinku" autocomplete="off">
				<input type="email" name="email" placeholder="E-mail adresa" value="<?php echo $current_user[0]['email']; ?>" required>
				<input type="tel" name="phone_number" placeholder="Telefon/Mobitel" value="<?php echo $current_user[0]['phone_number']; ?>" required>

				<?php 
					if(!empty($current_user[0]['avatar']) && $current_user[0]['avatar'] != "-"){
						echo "<span class='notif'>Nova slika će pregaziti ovu - ostavi postojeću: </span>
								<input type='checkbox' name='avatar_status' value='1' checked>
								<img class='img-responsive thumbnail-image' src='".$current_user[0]['avatar']."' />";
					}
				?>
				<input type="file" id="avatar" name="avatar" accept="image/*">
				<input type="hidden" name="request" value="settings-edit">				

				<button class="main_button" id="edit-settings-button">Uredi profil <i class="fa fa-check"></i></button>
			</form>
		</div>

		<div id="notification-data" class="notification-container">
			<?php
				if(empty($errors) === false){
					foreach($errors as $error){
                        echo $error;
					}
				}
			?>
		</div>
	</div>

	<hr>
	<a href="index.php">
    	<button class="main_button" id="return-button">Povratak <i class="fa fa-home"></i></button>
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
