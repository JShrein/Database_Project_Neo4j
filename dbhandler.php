<?php
$SERVER = 'localhost';
$USER = 'COMP7115';
$PASS = 'ItsTrueHeReallyIs';
$DATABASE = 'comp7115_project';

$link = mysqli_connect($SERVER, $USER, $PASS, $DATABASE);

if(!$link) {
	echo "Error: Unable to connect to MySQL Database" . PHP_EOL;
	echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
	echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
}
?>
