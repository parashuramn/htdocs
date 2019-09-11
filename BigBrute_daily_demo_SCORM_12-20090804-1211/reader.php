<?php

// ------------------------------------------------------------------------------------
// Preparations
// ------------------------------------------------------------------------------------

// load the imsmanifest.xml file
$dom = new DomDocument;
$dom->preserveWhiteSpace = FALSE;
$dom->load('imsmanifest.xml');

// adlcp namespace
$manifest = $dom->getElementsByTagName('manifest');
$adlcp = $manifest->item(0)->getAttribute('xmlns:adlcp');

// ------------------------------------------------------------------------------------
// Read the Resources (Assets) List
// ------------------------------------------------------------------------------------

// output table header row

$resListTable  = "<table cellpadding=3 cellspacing=0 border=1>\n";
$resListTable .= "<tr>\n";
$resListTable .= "\t<td valign=top align=left><b>Identifier</b></td>\n";
$resListTable .= "\t<td valign=top align=left><b>Type</b></td>\n";
$resListTable .= "\t<td valign=top align=left><b>SCORMType</b></td>\n";
$resListTable .= "\t<td valign=top align=left><b>HREF</b></td>\n";
$resListTable .= "\t<td valign=top align=left><b>Files</b></td>\n";
$resListTable .= "</tr>\n";


// get the resources element
$resourcesList = $dom->getElementsByTagName('resources');

// iterate over each of the resources
foreach ($resourcesList as $resourcesListRow) {
  $resourceList = $resourcesListRow->getElementsByTagName('resource');
  foreach ($resourceList as $resourceListRow) {
 
    // decode the attributes
    // e.g. <resource identifier="A001" type="webcontent" adlcp:scormtype="sco" href="a001index.html">
    $identifier = $resourceListRow->getAttribute('identifier');
    $type = $resourceListRow->getAttribute('type');
    $scormtype = $resourceListRow->getAttribute('adlcp:scormtype');
    $href = $resourceListRow->getAttribute('href');

    // make safe for display
    $identifier = cleanVar($identifier);
    $type = cleanVar($type);
    $scormtype = cleanVar($scormtype);
    $href = cleanVar($href);
 
    // list of files
    $files = array();
    $fileList = $resourceListRow->getElementsByTagName('file');
    foreach ($fileList as $fileListRow) {
      $files[] = cleanVar($fileListRow->getAttribute('href'));
    }
    $filesText = implode('<br>',$files);

//resource table
    $resListTable .= "<tr>\n";
    $resListTable .= "\t<td valign=top align=left>$identifier</td>\n";
    $resListTable .= "\t<td valign=top align=left>$type</td>\n";
    $resListTable .= "\t<td valign=top align=left>$scormtype</td>\n";
    $resListTable .= "\t<td valign=top align=left>$href</td>\n";
    $resListTable .= "\t<td valign=top align=left>$filesText</td>\n";
    $resListTable .= "</tr>\n";

    // resource array
    $resource[$identifier]['type'] = $type;
    $resource[$identifier]['scormtype'] = $scormtype;
    $resource[$identifier]['href'] = $href;
  }
}

$resListTable .= "</table>\n";

// -----------------------------------------------------------------------------------
// Functions
// ------------------------------------------------------------------------------------
function cleanVar($value) {
  $value = (trim($value) == "") ? " " : htmlentities(trim($value));
  return $value;
}

//-------------------------------------------------------------------------------------
// Read the Organizations List
// ------------------------------------------------------------------------------------

// output table header row
$orgListTable = "<table cellpadding=3 cellspacing=0 border=1>\n";
$orgListTable .= "<tr>\n";
$orgListTable .= "\t<td valign=top align=left><b>Identifier</b></td>\n";
$orgListTable .= "\t<td valign=top align=left><b>Identifier Ref</b></td>\n";
$orgListTable .= "\t<td valign=top align=left><b>Title</b></td>\n";
//$orgListTable .= "\t<td valign=top align=left><b>MasteryScore</b></td>\n";
//$orgListTable .= "\t<td valign=top align=left><b>LaunchData</b></td>\n";
$orgListTable .= "</tr>\n";

// get the organizations element
$organizationsList = $dom->getElementsByTagName('organizations');

// iterate over each of the organizations

foreach ($organizationsList as $organizationsListRow) {
  $organizationList = $organizationsListRow->getElementsByTagName('organization');
  foreach ($organizationList as $organizationListRow) {
    $itemsList = $organizationListRow->getElementsByTagName('item');
    foreach ($itemsList as $itemsListRow) {
// decode the attributes
// e.g. <item identifier="I_A001" identifierref="A001">

      $identifier = $itemsListRow->getAttribute('identifier');
      $identifierref = $itemsListRow->getAttribute('identifierref');
      $titleTag = $itemsListRow->getElementsByTagName('title');
      $title = $titleTag->item(0)->nodeValue;
      $masteryscoreTag = $itemsListRow->getElementsByTagNameNS($adlcp,'masteryscore');
//      $masteryscore = $masteryscoreTag->item(0)->nodeValue;
      $launchdataTag = $itemsListRow->getElementsByTagNameNS($adlcp,'datafromlms');
//      $launchdata = $launchdataTag->item(0)->nodeValue;

// make safe for display

      $identifier = cleanVar($identifier);
      $identifierref = cleanVar($identifierref);
      $title = cleanVar($title);
//      $masteryscore = cleanVar($masteryscore);
//      $launchdata = cleanVar($launchdata);

// table row
      $orgListTable .= "<tr>\n";
      $orgListTable .= "\t<td valign=top align=left>$identifier</td>\n";
      $orgListTable .= "\t<td valign=top align=left>$identifierref</td>\n";
      $orgListTable .= "\t<td valign=top align=left>$title</td>\n";
//      $orgListTable .= "\t<td valign=top align=left>$masteryscore</td>\n";
//      $orgListTable .= "\t<td valign=top align=left>$launchdata</td>\n";
      $orgListTable .= "</tr>\n";
   }
 }
}
$orgListTable .= "</table>\n";

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
  h2 {
    font-weight: bold;
    font-size: 11pt;
  }
  a:link, a:active, a:visited {
    color: blue;
    text-decoration: none;
  }
  a:hover {
    color: blue;
    text-decoration: underline;
  }
  </style>
</head>
<body bgcolor="#ffffff">
<h2>Resources = Assets</h2>
<p><?php print $resListTable; ?>
<h2>Organizations</h2>
<p><?php print $orgListTable; ?>


</body>
</html>
