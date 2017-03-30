<?php
	include_once('header.php');
	$thisPage="Registration";
	include("navigation.php");
?>

<!DOCTYPE html>
<html>
	<head>
		<title>User Login</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<div class="content-wrapper">
			<div class="form-wrapper">
				<div id="authform" class="form form_register">
					<form action="register.php" method="POST">
						<div class="form-row">
							<input id="authinput" class="input-txt" type="text" name="first" placeholder="First Name"><br>
						</div>
						<div class="form-row">
							<input id="authinput" class="input-txt" type="text" name="last" placeholder="Last Name"><br>
						</div>
						<div class="form-row">
							<input id="authinput" class="input-txt" type="text" name="email" placeholder="Email Address"><br>
							</div>
						<div class="form-row">
							<input id="authinput" class="input-txt" type="text" name="uname" placeholder="Username"><br>
						</div>
						<div class="form-row">
							<input id="authinput" class="input-txt" type="password" name="pass" placeholder="Password"><br>
						</div>
						<div class="form-row">
							<input id="authbtn" class="btn btn-primary" type="submit" value="Register"><br>
						</div>
					</form>
				</div>
			</div>
		</div>
		<?php
			if(isset($_SESSION['regerr'])) {
				echo "<p><b>".$_SESSION['regerr']."</b></p>";
				unset($_SESSION['regerr']);
			} else
			{
				echo "<p><b>session err not set</b></p>";
			}
		?>
	</body>
</html>