<?php

require "subs.php";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Path for SCORM bundles
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$path = 'course/'; 
$i=0;	

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the list of course content
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$SCOdata = readIMSManifestFile($path.'imsmanifest.xml');
$ORGdata=getORGdata($path.'imsmanifest.xml');
echo "<html>\n";

foreach ($SCOdata as $identifier => $SCO)
{
	$page[$i] = $path.cleanVar($SCO['href']);
	$i++;
}
print_r($SCOdata);
foreach ($ORGdata as $identifier => $ORG)
{
if ($ORG['identifierref']==''){  
echo "<h3>".$ORG['name']."</h3>\n";
}
else{           
	$key_ref=0;
	foreach ($SCOdata as $identifier_temp => $SCO)	{
		if ($identifier_temp==$identifier )
		{break;}
		else {$key_ref++;}
	}
	if ($key_ref>=0){	
echo "<h5><a href=".$page[$key_ref]."  target='course'>".$ORG['name']."</a></h5>\n";
	}
	else{ echo "Invalid Data in - imsmanifest.xml. Check the file and try again"; return;}
     }
}

echo "</html>\n";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  Make variable safe to display
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function cleanVar($value) {
  $value = (trim($value) == "") ? " " : htmlentities(trim($value));
  return $value;
}
?>