<?php
include_once("header.php");
include_once("neodbhandler.php");
include_once("neo4j_func.php");

$email = $_GET['id'];
$action = $_GET['do'];
$uname = $_GET['uname'];

switch($action) {
	case "follow":
		follow_user($client, $_SESSION['email'], $email);
		$msg = "You are now following ".$uname."!";
		break;
	case "unfollow":
		unfollow_user($client, $_SESSION['email'], $email);
		$msg = "You stopped following ".$uname."!";
		break;
}
$_SESSION['message'] = $msg;

header("Location: home.php");
?>