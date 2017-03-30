<?php
include_once('header.php');
include_once('neodbhandler.php');
include_once('neo4j_func.php');

$first = $_POST['first'];
$last = $_POST['last'];
$email = $_POST['email'];
$uname = $_POST['uname'];
$pass = $_POST['pass'];
$status = "active";

if($first == "" || $last == "" || $email == "" || $uname == "" || $pass == "") {
	$_SESSION['regerr'] = "Please complete the registration form.";
	header("Location: registration.php");
} else {
	add_user($client, $first, $last, $uname, $pass, $email, $status);
}
?>