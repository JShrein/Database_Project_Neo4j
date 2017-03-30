<?php
include_once('header.php');
include_once('neodbhandler.php');
include_once('neo4j_func.php');
?>

<!DOCTYPE html>
<html>
<head>
	<title>Twitter Clone</title>
</head>

<body>
	<?php
	if(!isset($_SESSION['email'])) {
		header("Location: index.php");
	}
	?>
	<form action="logout.php">
		<input id="authbtn" type="submit" value="Logout">
	</form>

	<?php
	if(isset($_SESSION['message'])) {
		echo "<p id='phpmsg'>".$_SESSION['message']."</p>";
		unset($_SESSION['message']);
	}
	if(isset($_SESSION['posterr'])) {
		echo "<p id='phpmsg'>".$_SESSION['posterr']."</p>";
		unset($_SESSION['posterr']);
	}
	?>


	<h2>Following</h2>
	<?php
	$users = show_users($client, $_SESSION['email']);

	if(count($users)) {
		$followers = array();
		foreach($users as $user) {
			$followers[] = $user['email'];
		}
	} else {
		$followers = array();
	}

	$followers[] = $_SESSION['email'];

	?>
		<ul>
	<?php
		foreach($users as $user) {
			echo "<li>".$user['username']."</li>\n";
		}
	?>
		</ul>
	<?php
	if(count($followers) == 0) {
	?>
	<p><b>You're not following anyone!</b></p>
	<?php
	}
	?>


	<form method='post' action='add.php'>
	<p>Your status:</p>
	<textarea name='content' rows='5' cols='40' wrap=VIRTUAL></textarea>
	<p><input type='submit' value='submit' /></p
	</form>

	<?php
	$posts = show_posts($client, $followers, 15);

	if(count($posts)) {
	?>
		<table border='1' cellspacing='0' cellpadding='5' width='500'>
	<?php
		foreach ($posts as $key => $values) {
			echo "<tr valign='top'>\n";
			echo "<td>".$values['username'] ."</td>\n";
			echo "<td>".$values['content'] ."<br />\n";
			echo "<small>".$values['timestamp'] ."</small></td>\n";
			echo "</tr>\n";
		}
	?>
		</table>
	<?php
	} else {
	?>
	<p><b>You haven't made any posts!</b></p>
	<?php
	}
	?>

</body>
</html>