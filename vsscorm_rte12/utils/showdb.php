<?php 

/*

VS SCORM - showdb.php 
Rev 1.2 - Wednesday, August 12, 2009
Copyright (C) 2009, Addison Robson LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, 
Boston, MA	02110-1301, USA.

*/

//  database login information
require "../config.php";

// connect to the database
$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
// mysql_select_db($dbname,$link);

// read GET variables
$SCOInstanceID = $_REQUEST['SCOInstanceID'] * 1;

// create a table showing all variables
$output  = "<table cellpadding=3 cellspacing=0 border=1>\n";
$output .= "<tr>\n";
$output .= "\t<td valign=top ><b>SCOInstanceID</b></td>\n";
$output .= "\t<td valign=top ><b>varName</b></td>\n";
$output .= "\t<td valign=top ><b>varValue</b></td>\n";
$output .= "\t<td valign=top ><b>Content Provider</b></td>\n";
$output .= "\t<td valign=top ><b>UI Event</b></td>\n";
$output .= "\t<td valign=top ><b>Event Time</b></td>\n";
$output .= "\t<td valign=top ><b>Function Called</b></td>\n";
$output .= "\t<td valign=top ><b>Remark</b></td>\n";
$output .= "\t<td valign=top ><b>ModifiedAt</b></td>\n";

$output .= "</tr>\n";
$result = mysqli_query($link,"select * from scormvars where SCOInstanceID='$SCOInstanceID' order by SCOInstanceID,varName");
while (list($SCOInstanceID,$varName,$varValue,$Content,$orderNo,$uiEvent,$eventTime,$functionCalled,$Remark,$modifiedAt) = mysqli_fetch_row($result)) {

	// make safe for display
	$safeVarName = htmlentities($varName);
	$safeVarValue = ($varValue == "") ? '&nbsp;' : htmlentities($varValue);
if($safeVarName=='cmi.suspend_data'){$safeVarValue ='&nbsp;';}
	// table row
	$output .= "<tr>\n";
	$output .= "\t<td valign=top >$SCOInstanceID</td>\n";
	$output .= "\t<td valign=top >$safeVarName</td>\n";
	$output .= "\t<td valign=top  >$safeVarValue</td>\n";
	$output .= "\t<td valign=top  >$Content</td>\n";
	$output .= "\t<td valign=top >$uiEvent</td>\n";
	$output .= "\t<td valign=top >$eventTime</td>\n";
	$output .= "\t<td valign=top >$functionCalled</td>\n";
	$output .= "\t<td valign=top >$Remark</td>\n";
	$output .= "\t<td valign=top >$modifiedAt</td>\n";
	$output .= "</tr>\n";

}

$output .= "</table>\n";

?>
<html>
<head>
	<title></title>
	<style type="text/css">
	p,td,li,body,input,select,textarea {
		font-family: verdana, sans-serif;
		font-size: 10pt;
	}
	</style>
</head>
<body bgcolor="#ffffff">
<p><?php print $output; ?>
</body>
</html>