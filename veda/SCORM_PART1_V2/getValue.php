<?php

//  database login information
require "config.php";

// connect to the database
$link = mysql_connect($dbhost,$dbuser,$dbpass);
mysql_select_db($dbname,$link);

// read GET variable
$varname = $_REQUEST['varname'];

// make safe for database
$safevarname = mysql_escape_string($varname);
$varvalue = "";
// read data from the 'scormvars' table
$result = mysql_query("select varValue from scormvars where (varName='$safevarname')" ,$link);
list($varvalue) = mysql_fetch_row($result);
// return value to the calling program
print $varvalue;

?>
