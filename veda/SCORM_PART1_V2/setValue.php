<?php

//  database login information
require "config.php";

// connect to the database
$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
// mysql_select_db($dbname,$link);

// read GET and POST variables
$varname = $_REQUEST['varname'];
$varvalue = $_REQUEST['varvalue'];
// make safe for database
$safevarname = mysqli_escape_string($link,$varname);
$safevarvalue = mysqli_escape_string($link,$varvalue);
// save data to the 'scormvars' table
mysqli_query($link,"delete from scormvars where (varName='$safevarname')");
mysqli_query($link,"insert into scormvars (varName,varValue) values ('$safevarname','$safevarvalue')");
// return value to the calling program
print "true";
?>
