<?php
include_once('header.php');
include_once('neodbhandler.php');
include_once('neo4j_func.php');

$email = $_POST['email'];
$pass = $_POST['pass'];

if($email == "" || $pass == "") {
	$_SESSION['regerr'] = "Please complete the login form.";
	header("Location: index.php");
} else {
	login($client, $email, $pass);
}
?>