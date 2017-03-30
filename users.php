<?php
include_once('header.php');
include_once('neodbhandler.php');
include_once('neo4j_func.php');
?>

<!DOCTYPE html>
<html>
<head>
	<title>Users</title>
</head>

<body>
	<?php
		if(!isset($_SESSION['email'])) {
			$_SESSION['autherr'] = "You must be signed in to view this page";
			header("Location: index.php");
		}
		if(isset($_SESSION['dberr'])) {
			echo $_SESSION['dberr'];
			unset($_SESSION['dberr']);
		}
	?>
	<h1>User List</h1>
	<?php
		$users = show_users($client);
		$following = following($client, $_SESSION['email']);
		if(count($users)) {
	?>
	<table border='1' cellspacing='0' cellpadding='0' width='500'>
		<?php
			foreach($users as $user) {

				echo "<tr valign='top'>\n";
				echo "<td>".$user['username'] ."</td>\n";
				echo "<td>".$user['email'] ."</td>\n";
				echo "<td>";
				if(in_array($user['email'], $following)) {
					echo "<small><a href='action.php?id=".$user['email']."&uname=".$user['username']."&do=unfollow'>Unfollow</a></small>\n";
				} else {
					echo "<small><a href='action.php?id=".$user['email']."&uname=".$user['username']."&do=follow'>Follow</a></small>\n";
				}
				echo "</td>\n";
				echo "</tr>\n";
			}
		?>
	</table>
	<?php
		} else {
	?>
	<p><b>No users exist!</b></p>
	<?php
		}
	?>
</body>
</html>