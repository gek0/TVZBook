<?php
require_once ('inc/database.php');	

if($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['request'])){
	$request = trim($_POST['request']);
	$result = false;

	switch($request) {
	  case 'login':
	  	if(empty($_POST["username"]) || empty($_POST["password"])){
	  		echo json_encode(array('status' => 1, 'message' => 'Sva polja su obavezna.'));	
	  	}
	  	else{
		    $result = $users->login($_POST["username"], $_POST["password"], $_SERVER['REMOTE_ADDR']);
		    if($result === true){
		    	echo json_encode(array('status' => 0, 'message' => 'Uspješno ste ulogirani...'));
		    }
		    else{
				echo json_encode(array('status' => 1, 'message' => 'Pogrešno korisničko ime ili lozinka.'));
		    }	
	    }    
	    break;
	case 'register':
	  	if(empty($_POST["full_name"]) || empty($_POST["username"]) || empty($_POST["password"]) || 
	  		empty($_POST["password_again"]) || empty($_POST["email"]) || empty($_POST["phone_number"])){
	  		echo json_encode(array('status' => 1, 'message' => 'Sva polja su obavezna.'));	
	  	}
	    else if($users->user_exists($_POST["username"]) === true){
	    	echo json_encode(array('status' => 1, 'message' => 'Korisnik s tim korisničkim imenom već postoji.'));
	    }
	    else if($_POST["password"] != $_POST["password_again"]){
	    	echo json_encode(array('status' => 1, 'message' => 'Unjete lozinke nisu iste.'));	
	    }
	    else{
		  	$result = $users->register($_SERVER['REMOTE_ADDR'], $_POST["full_name"], $_POST["username"], 
										$_POST["password"], $_POST["password_again"], $_POST["email"], $_POST["phone_number"]);
		  	if($result === true){
		    	echo json_encode(array('status' => 0, 'message' => 'Uspješno ste registrirani...'));
		    }
		    else{
				echo json_encode(array('status' => 1, 'message' => 'Neki od podatka nisu važeći ili poslani.'));
		    }
		}
	    break;
	default:
	    return false;
	}
}
else{
	header("Location: index.php");
	exit();
}