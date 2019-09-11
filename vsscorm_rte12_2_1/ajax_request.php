<?php 
require 'config.php';
function dbConnect() {

	// database login details
	global $dbname;
	global $dbhost;
	global $dbuser;
	global $dbpass;

	// link
	global $link;

	// connect to the database
	$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
	//mysqli_select_db($dbname,$link);

}
dbConnect();
function writeFullElement() { 
    extract($_REQUEST);
	global $link;
$type = mysqli_escape_string($link,$type);
$params_key = mysqli_escape_string($link,$params_key);
$value = mysqli_escape_string($link,$value);
$timestamp = mysqli_escape_string($link,$timestamp);
$activity_id = mysqli_escape_string($link,$activity_id);
echo "INSERT INTO `scormvars_full_data` ( `type`, `params_key`, `value`,`timestamp`, `activity_id`, `event`) VALUES
         ( '$type', '$params_key', '$value','$timestamp' '$activity_id', '')";
         echo '<br /><br />';
		// mysqli_query($link,"INSERT INTO `scormvars_full_data` ( `type`, `params_key`, `value`,`timestamp`, `activity_id`, `event`) VALUES
        //  ( '$type', '$params_key', '$value','$timestamp', '$activity_id', '')") or die(mysqli_error($link));
	return;

}

writeFullElement();