<?php
function readIMSManifestFile($manifestfile) {

//PREPARATIONS	

// central array for resource data
  global $resourceData;
 
// load the imsmanifest.xml file
  $xmlfile = new DomDocument;
  $xmlfile->preserveWhiteSpace = FALSE;
  $xmlfile->load($manifestfile);

// adlcp namespace
  $manifest = $xmlfile->getElementsByTagName('manifest');
  $adlcp = $manifest->item(0)->getAttribute('xmlns:adlcp');

// READ THE RESOURCES LIST
// array to store the results
  $resourceData = array();

// get the list of resource element
  $resourceList = $xmlfile->getElementsByTagName('resource');
  $r = 0;

  foreach ($resourceList as $rtemp) { 

    // decode the resource attributes

    $identifier = $resourceList->item($r)->getAttribute('identifier');
    $resourceData[$identifier]['type'] = $resourceList->item($r)->getAttribute('type');
    $resourceData[$identifier]['scormtype'] = $resourceList->item($r)->getAttribute('adlcp:scormtype');
    $resourceData[$identifier]['href'] = $resourceList->item($r)->getAttribute('href');
    


    // list of files
    $fileList = $resourceList->item($r)->getElementsByTagName('file');
 
    $f = 0;

    foreach ($fileList as $ftemp) {
      $resourceData[$identifier]['files'][$f] =  $fileList->item($f)->getAttribute('href');
      $f++;
    }

    // list of dependencies
    $dependencyList = $resourceList->item($r)->getElementsByTagName('dependency');
    $d = 0;
    foreach ($dependencyList as $dtemp) {
      $resourceData[$identifier]['dependencies'][$d] =  $dependencyList->item($d)->getAttribute('identifierref');
      $d++;
    }
    $r++;
  }

  // resolve resource dependencies to create the file lists for each resource
  foreach ($resourceData as $identifier => $resource) {
    $resourceData[$identifier]['files'] = resolveIMSManifestDependencies($identifier);
  }


  // READ THE ITEMS LIST


// array to store the results

$itemData = array();

// get the list of item elements
$itemList = $xmlfile->getElementsByTagName('item');

$i = 0;
foreach ($itemList as $itemp) {

  // decode the item attributes and sub-elements

  $identifier = $itemList->item($i)->getAttribute('identifier');
  $itemData[$identifier]['identifierref'] = $itemList->item($i)->getAttribute('identifierref');
  $itemData[$identifier]['parameters'] = $itemList->item($i)->getAttribute('parameters');

  $itemData[$identifier]['title'] = $itemList->item($i)->getElementsByTagName('title')->item(0)->nodeValue;
//  $itemData[$identifier]['masteryscore'] = $itemList->item($i)->getElementsByTagNameNS($adlcp,'masteryscore')->item(0)->nodeValue;
// $itemData[$identifier]['datafromlms'] = $itemList->item($i)->getElementsByTagNameNS($adlcp,'datafromlms')->item(0)->nodeValue;
  $i++;
}

  // PROCESS THE ITEMS LIST TO FIND SCOS

// array for the results
$SCOdata = array();

// loop through the list of items

foreach ($itemData as $identifier => $item) {

  // find the linked resource
  $identifierref = $item['identifierref'];

  // is the linked resource a SCO? if not, skip this item
if(isset($resourceData[$identifierref]['scormtype'])){
  if (strtolower($resourceData[$identifierref]['scormtype']) != 'sco') { continue; }

  // save data that we want to the output array

  $SCOdata[$identifier]['title'] = $item['title'];
//  $SCOdata[$identifier]['masteryscore'] = $item['masteryscore'];
//  $SCOdata[$identifier]['datafromlms'] = $item['datafromlms'];
   $SCOdata[$identifier]['href'] = $resourceData[$identifierref]['href'];
   if(isset($item['parameters'])) {
	$SCOdata[$identifier]['href'] = $SCOdata[$identifier]['href'].$item['parameters'];
    } 
  $SCOdata[$identifier]['files'] = $resourceData[$identifierref]['files'];
}
}

  // RETURN RESULTS
return $SCOdata;
}


function resolveIMSManifestDependencies($identifier) {

  global $resourceData;

   $files = $resourceData[$identifier]['files'];
  if(isset($resourceData[$identifier]['dependencies'])){ $dependencies = $resourceData[$identifier]['dependencies'];
  if (is_array($dependencies)) {
    foreach ($dependencies as $d => $dependencyidentifier) {
      $files = array_merge($files,resolveIMSManifestDependencies($dependencyidentifier));
      unset($resourceData[$identifier]['dependencies'][$d]);
    }
    $files = array_unique($files);
   }
  }
  return $files;

}

function getORGdata($manifestfile)
{
// ------------------------------------------------------------------------------------
// Preparations
// ------------------------------------------------------------------------------------

// load the imsmanifest.xml file
$dom = new DomDocument;
$dom->preserveWhiteSpace = FALSE;
$dom->load($manifestfile);

// adlcp namespace
$manifest = $dom->getElementsByTagName('manifest');
$adlcp = $manifest->item(0)->getAttribute('xmlns:adlcp');


// READ THE RESOURCES LIST

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
      $launchdataTag = $itemsListRow->getElementsByTagNameNS($adlcp,'datafromlms');

// table row
      $ORGdata[$identifier]['identifier'] = $identifier;
      $ORGdata[$identifier]['identifierref'] = $identifierref;
      $ORGdata[$identifier]['name'] =$title;;
   }
 }
}
return($ORGdata);
}


function getCourseTitle($manifestfile)
{
// load the imsmanifest.xml file
$dom = new DomDocument;
$dom->preserveWhiteSpace = FALSE;
$dom->load($manifestfile);

// adlcp namespace
$manifest = $dom->getElementsByTagName('manifest');
$adlcp = $manifest->item(0)->getAttribute('xmlns:adlcp');

// ------------------------------------------------------------------------------------
// Read the Course Title
// ------------------------------------------------------------------------------------

// get the organizations element
$organizationsList = $dom->getElementsByTagName('organizations');

// iterate over each of the organizations

foreach ($organizationsList as $organizationsListRow) {
 $titleTag= $organizationsListRow->getElementsByTagName('title');
 $TITLE  = $titleTag->item(0)->nodeValue;
}
return  $TITLE; 
}
?>
