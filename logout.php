<?php
	include_once("header.php");
	session_destroy();
	header("Location:index.php");
?>