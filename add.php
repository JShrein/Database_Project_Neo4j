<?php
include_once('header.php');
include_once('neodbhandler.php');
include_once('neo4j_func.php');

$email = $_SESSION['email'];
$content = substr($_POST['content'], 0, 150);

add_post($client, $email, $content);
$_SESSION['message'] = "Your post was added!";

header("Location:home.php");
?>