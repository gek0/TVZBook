<?php
require_once ('inc/database.php');
	
//if user already authenticated
$session->session_true("index.php");

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
			<form action="" method="POST" name="login" id="login-form" role="form">
				<input type="text" name="username" placeholder="Korisničko ime" required>
				<input type="password" name="password" placeholder="Lozinka" required>
				<input type="hidden" name="request" value="login">

				<button class="inverse_main" id="login-button">Prijavi se <i class="fa fa-check"></i></button>
			</form>
		</div>
		<div class="form">
			<h2>Registracija</h2>
			<form action="" method="POST" name="register" id="register-form" role="form">
		    	<input type="text" name="full_name" placeholder="Ime i prezime" required>
				<input type="text" name="username" placeholder="Korisničko ime" required>
				<input type="password" name="password" placeholder="Lozinka" autocomplete="off" required>
				<input type="password" name="password_again" placeholder="Ponovi lozinku" autocomplete="off" required>
				<input type="email" name="email" placeholder="E-mail adresa" required>
				<input type="tel" name="phone_number" placeholder="Telefon/Mobitel" required>
				<input type="hidden" name="request" value="register">

				<button class="inverse_main" id="register-button">Registriraj se <i class="fa fa-check"></i></button>
		    </form>
		</div>

		<div id="notification-data" class="notification-container"></div>
	</div>

	<?php require_once ('inc/footer.php'); ?>
	<script src="js/login-register.js"></script>
</body>
</html>