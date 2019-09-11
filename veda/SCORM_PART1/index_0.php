<?php

/*

VS SCORM - CAM - index.php
Rev 2.0 - Monday, October 12, 2009
Copyright (C) 2009, Addison Robson LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor,
Boston, MA  02110-1301, USA.

*/

require_once(dirname(__FILE__).'/subs.php');
$SCOdata = readIMSManifestFile(dirname(__FILE__).'/course/imsmanifest.xml');
print_r($SCOdata);
exit;
// ------------------------------------------------------------------------------------
// Process the Items List to Find SCOs
// ------------------------------------------------------------------------------------
/*
// output table header row
$SCOListTable .= ("<table cellpadding=3 cellspacing=0 border=1>\n");
$SCOListTable .= ("<tr>\n");
$SCOListTable .= ("\t<td valign=top align=left><b>Identifier</b></td>\n");
$SCOListTable .= ("\t<td valign=top align=left><b>Title</b></td>\n");
$SCOListTable .= ("\t<td valign=top align=left><b>MasteryScore</b></td>\n");
$SCOListTable .= ("\t<td valign=top align=left><b>LaunchData</b></td>\n");
$SCOListTable .= ("\t<td valign=top align=left><b>SCO Entry Point</b></td>\n");
$SCOListTable .= ("\t<td valign=top align=left><b>Required Files</b></td>\n");
$SCOListTable .= ("</tr>\n");

// loop through the list of items
foreach($SCOdata as $identifier => $SCO)
{

    // data that we want
    $SCOListTable .= ("<tr>\n");
    $SCOListTable .= ("\t<td valign=top align=left>".cleanVar($identifier)."</td>\n");
    $SCOListTable .= ("\t<td valign=top align=left>".cleanVar($SCO['title'])."</td>\n");
    $SCOListTable .= ("\t<td valign=top align=left>".cleanVar($SCO['masteryscore'])."</td>\n");
    $SCOListTable .= ("\t<td valign=top align=left>".cleanVar($SCO['datafromlms'])."</td>\n");
    $SCOListTable .= ("\t<td valign=top align=left>".cleanVar($SCO['href'])."</td>\n");
    $SCOListTable .= ("\t<td valign=top align=left>".implode('<br>',$SCO['files']).")</td>\n";
    $SCOListTable .= ("</tr>\n");

}

$SCOListTable .= "</table>\n";

// function to clean data for display
function cleanVar($value) {
  $value = (trim($value) == "") ? " " : htmlentities(trim($value));
  return $value;
}
*/
?>
<html>
<head>
  <title></title>
  <style type="text/css">
  p,td,li,body,input,select,textarea {
    font-family: verdana, sans-serif;
    font-size: 10pt;
  }
  h1 {
    font-weight: bold;
    font-size: 12pt;
  }
  </style>
</head>
<body bgcolor="#ffffff">

<h1>SCO Data</h1>
<p><?php print_r($SCOListTable); ?>

<body>
</html>
