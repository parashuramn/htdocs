<?php

require "subs.php";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Path for SCORM bundles
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$path = 'course/'; 
$bgpath='course/shared/';   //Background image

$i=0;	

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Get Course Title
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$handle=fopen('heading.html','w');
fwrite($handle, '<html>'."\n");
fwrite($handle, '<body background="'.$bgpath.'background.jpg">'."\n");
$TITLE= getCourseTitle($path.'imsmanifest.xml');
fwrite($handle, '<h1>'.$TITLE.'</h>'."\n");
fwrite($handle, '</body>'."\n".'</html>'."\n");
fclose($handle);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Prepare the list of course content
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$handle=fopen('list.html','w');
fwrite($handle, '<html>'."\n");


$SCOdata = readIMSManifestFile($path.'imsmanifest.xml');
$ORGdata=getORGdata($path.'imsmanifest.xml');

foreach ($SCOdata as $identifier => $SCO)
{
	$page[$i] = $path.cleanVar($SCO['href']);
	$i++;
}

foreach ($ORGdata as $identifier => $ORG)
{
if ($ORG['identifierref']==''){fwrite($handle, '<h3>'.$ORG['name'].'</h3>'."\n");}
else{           
	$key_ref=0;
	foreach ($SCOdata as $identifier_temp => $SCO)	{
		if ($identifier_temp==$identifier )
		{break;}
		else {$key_ref++;}
	}
	if ($key_ref>=0){
		fwrite($handle, '<h5><a href="'.$page[$key_ref].'" target="course">'.$ORG['name'].'</a></h5>'."\n");
	}
	else{ echo "Invalid Data in - imsmanifest.xml. Check the file and try again"; return;}
     }
}
fwrite($handle, '</html>'."\n");
fclose($handle);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  Make variable safe to display
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function cleanVar($value) {
  $value = (trim($value) == "") ? " " : htmlentities(trim($value));
  return $value;
}
?>

<html>
<head> 
    <title><?php echo $TITLE?> </title>
</head>

</body>

<frameset rows="10%, 90%" frameborder="0">
 <frame src="heading.html" name="heading" >

<frameset cols="20%, 80%" frameborder="0">
 <frame src="list.html" name="list">
 <frame src="<?php echo $page[0]?>"  name="course" >
 <frame src="api.html" name="API" noresize>

</frameset>

</frameset>
</body>
</html>



