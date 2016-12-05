<?php
require_once ('inc/database.php');
	
//if user already authenticated
$session->session_true("index.php");

if($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['login'])){
	if(empty($_POST["username"]) || empty($_POST["password"])){
		$errors[] = "Sva polja su obavezna za prijavu!";
	} 
	else{
		$ip = $_SERVER['REMOTE_ADDR'];
		$username = $_POST["username"];
		$password = $_POST["password"];
		
		$login = $users->login($username, $password, $ip);

		if($login === false){
			$errors[] = "Pogrešno korisničko ime ili lozinka!";
		} 
		else{
			$_SESSION[$session_id] = $login;
			$_SESSION['username'] = htmlspecialchars($username);
			header("Location: ");
			exit();
		}			
	}		
}
if($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['register'])){
	if(empty($_POST["full_name"]) || empty($_POST["username"]) || empty($_POST["password"]) || 
		empty($_POST["password_again"]) || empty($_POST["email"]) || empty($_POST["phone_number"])){
		$errors[] = "Sva polja su obavezna za registraciju!";
	} 
	else if($_POST["password"] != $_POST["password_again"]){
		$errors[] = "Lozinke nisu iste!";
	}
	else if($users->user_exists($_POST["username"]) === true){
		$errors[] = "Korisnik s tim korisničkim imenom već postoji!";
	}
	else{
		$ip = $_SERVER['REMOTE_ADDR'];
		$full_name = $_POST["full_name"];
		$username = $_POST["username"];
		$password = $_POST["password"];
		$password_again = $_POST["password_again"];
		$email = $_POST["email"];
		$phone_number = $_POST["phone_number"];
		
		$register = $users->register($ip, $full_name, $username, $password, $password_again, $email, $phone_number);

		if($register === false){
			$errors[] = "Registracija nije uspješna!";
		} 
		else{
			$announces[] = "Registracija uspješno obavljena!";
			header("Location: ");
			exit();
		}	
	}	
}

require_once ('inc/header.php');
?>

<body>
	<div class="pen-title">
	  <h1>TVZBook</h1>
	</div>
	<div class="module form-module">
		<div class="toggle" title="Zamjeni formu"><i class="fa fa-times fa-pencil"></i></div>
		<div class="form">
			<h2>Prijava</h2>
			<form action="" method="POST" name="login">
				<input type="text" name="username" placeholder="Korisničko ime" required>
				<input type="password" name="password" placeholder="Lozinka" required>
				<input type="hidden" name="login" value="true">

				<button>Prijavi se <i class="fa fa-check"></i></button>
			</form>
		</div>
		<div class="form">
			<h2>Registracija</h2>
			<form action="" method="POST" name="register">
		    	<input type="text" name="full_name" placeholder="Ime i prezime" required>
				<input type="text" name="username" placeholder="Korisničko ime" required>
				<input type="password" name="password" placeholder="Lozinka" autocomplete="off" required>
				<input type="password" name="password_again" placeholder="Ponovi lozinku" autocomplete="off" required>
				<input type="email" name="email" placeholder="E-mail adresa" required>
				<input type="tel" name="phone_number" placeholder="Telefon/Mobitel" required>
				<input type="hidden" name="register" value="true">

				<button>Registriraj se <i class="fa fa-check"></i></button>
		    </form>
		</div>

		<?php
	        if(empty($errors) === false){
	        	echo '<div class="notification-container">';
		        	foreach($errors as $error){
		             	echo $error;
		        	}
	        	echo '</div>';
	        }
	        if(empty($announces) === false){
	        	echo '<div class="notification-container">';
		            foreach($announces as $announce){
		                echo $announce;
		            }
	            echo '</div>';
	        }
        ?>
	</div>

	<?php require_once ('inc/footer.php'); ?>
	<script src="js/login-register.js"></script>
</body>
</html>