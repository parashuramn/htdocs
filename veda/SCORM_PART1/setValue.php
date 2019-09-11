 <?php 

 static $scormvars = array(array(),array());
 print_r('Variables'.$scormvars);
 static $count=0;


/*

VS SCORM - setValue.php
Rev 1.0 - Wednesday, June 10, 2009
Copyright (C) 2009, Addison Robson LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor,
Boston, MA  02110-1301, USA.

*/

global $scormvars, $count;
$status=0;

// read GET and POST variables
$varname = $_REQUEST['varname'];
$varvalue = $_REQUEST['varvalue'];
;

// save data to the 'scormvars' table
for ($i=0;$i<count;$i++){
	if ($scormvars[i][0]==$varname){
		$scormvars[i][0]=	$varname;
		$scormvars[i][1]=	$varvalue;
		$status=1;
		break;
	}
}
if ($status==0){

$scormvars[$count][0]=$varname;
$scormvars[$count][1]=$varvalue;
$count++;

}
mysql_query("delete from scormvars where (varName='$safevarname')",$link);
mysql_query("insert into scormvars (varName,varValue) values ('$safevarname','$safevarvalue')",$link);

// return value to the calling program
print "true";

?>
