<?php

require "subs.php";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Path for SCORM bundles
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////                                           
$bgpath='course/shared/';   //Background image
$path = 'course/'; 

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Get Course Title
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$TITLE= getCourseTitle($path.'imsmanifest.xml');
echo "<html>";
echo "<body><h1>".$TITLE."</h></body>";
echo "</html>";

?>