<?php
// This is the frontent for searching
// See search.php for backend
include_once('header.php');
include_once('dbhandler.php');
include_once('mysql_func.php');

if(isset($_POST['submit'])) {
	$query = $_POST['searchterm'];
	$users = search_users($link, $query);
} else {
	$users = array();
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Search</title>
</head>

<body>
	<h1>User Search</h1>
	<p>Use username or email to search</p>
	<form action="" method="POST">
		<input type="text" name="searchterm">
		<input type="submit" name="submit" value="Search">
	</form>
	<?php
		if(count($users)) {
	?>
	<table border='1' cellspacing='0' cellpadding='0' width='500'>
		<?php
			foreach($users as $user) {

				echo "<tr valign='top'>\n";
				echo "<td>".$user['username'] ."</td>\n";
				echo "<td>".$user['email'] ."</td>\n";
				echo "<td><small><a href='#'>Follow</a></small></td>\n";
				echo "</tr>\n";
			}
		?>
	</table>
	<?php
		} else {
			if(isset($_SESSION['searcherr'])) {
				echo "<p><b>".$_SESSION['searcherr']."</b></p>";
			}
		}
	?>
</body>
</html>