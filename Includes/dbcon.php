<?php
	$host = "localhost";
	$user = "root";
	$pass = "";
	$db = "rhmsystem";
	
	$conn = new mysqli($host, $user, $pass, $db);
	if($conn->connect_error){
		echo "Echec de se connecter à la base de données:" . $conn->connect_error;
	}
?>