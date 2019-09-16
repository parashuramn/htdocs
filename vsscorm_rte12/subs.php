<?php

/*

VS SCORM 1.2 RTE - subs.php
Rev 2009-11-30-01
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
Boston, MA 02110-1301, USA.

*/

// ------------------------------------------------------------------------------------
// Database-specific code
// ------------------------------------------------------------------------------------

function dbConnect()
{

    // database login details
    global $dbname;
    global $dbhost;
    global $dbuser;
    global $dbpass;

    // link
    global $link;

    // connect to the database
    $link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
    //mysqli_select_db($dbname,$link);
}

function readElement($VarName)
{
    global $link;
    global $SCOInstanceID;

    $safeVarName = mysqli_escape_string($link, $VarName);
    $result = mysqli_query($link, "select VarValue from scormvars where ((SCOInstanceID=$SCOInstanceID) and (VarName='$safeVarName'))");
    list($value) = mysqli_fetch_row($result);

    return $value;
}

function writeElement($VarName, $VarValue, $Content='', $uiEvent='', $eventTime='', $functionCalled='', $Remark='')
{
    global $link;
    global $SCOInstanceID;

    $safeVarName = mysqli_escape_string($link, $VarName);
    $safeVarValue = mysqli_escape_string($link, $VarValue);
    $unique_fld = ['cmi.core._children','cmi.core.score._children','cmi.core.student_id',
    'cmi.core.student_name','cmi.core.score.raw','adlcp:masteryscore',
    'cmi.launch_data','cmi.suspend_data','cmi.core.lesson_location','cmi.core.credit',
    'cmi.core.lesson_status','cmi.core.entry','cmi.core.exit','cmi.core.total_time',
    'cmi.core.session_time','cmi.interactions._count'];
    
    if (in_array($safeVarName, $unique_fld)) {
        mysqli_query($link, "update scormvars set VarValue='$safeVarValue' where ((SCOInstanceID=$SCOInstanceID) and (VarName='$safeVarName'))");
    } else {
        mysqli_query($link, "insert into scormvars (SCOInstanceID,VarName,VarValue,Content,uiEvent,eventTime,
		functionCalled,Remark) 
		values ($SCOInstanceID,'$safeVarName','$safeVarValue','$Content','$uiEvent','$eventTime','$functionCalled','$Remark')") or die(mysqli_error($link));
    }
    return;
}

function initializeElement($VarName, $VarValue, $Content='Articulate 1.2', $uiEvent='On content launch', $eventTime='', $functionCalled='LMSInitialized', $Remark='')
{
    global $link;
    global $SCOInstanceID;
    $Content_ar = ['Captivate 1.2','Ispring 1.2','Articulate 1.2','Articulate 1.2'];
    $Content = $Content_ar[$SCOInstanceID-1];
    // make safe for the database
    $safeVarName = mysqli_escape_string($link, $VarName);
    $safeVarValue = mysqli_escape_string($link, $VarValue);

    // look for pre-existing values
    $result = mysqli_query($link, "select VarValue from scormvars where ((SCOInstanceID=$SCOInstanceID) and (VarName='$safeVarName'))");
    $eventTime=$eventTime =='' ? date('Y-m-d H:i:s') :$eventTime;
    // if nothing found ...
    if (! mysqli_num_rows($result)) {
        //echo 'dsfd';
        // 		echo "insert into scormvars (SCOInstanceID,VarName,VarValue,Content,uiEvent,eventTime,
        // 		functionCalled,Remark)
        // 		values ($SCOInstanceID,'$safeVarName','$safeVarValue','$Content','$uiEvent','$eventTime','$functionCalled','$Remark')".'/n';
        mysqli_query($link, "insert into scormvars (SCOInstanceID,VarName,VarValue,Content,uiEvent,eventTime,
		functionCalled,Remark) 
		values ($SCOInstanceID,'$safeVarName','$safeVarValue','$Content','$uiEvent','$eventTime','$functionCalled','$Remark')") or die(mysqli_error($link));
    }
}

function initializeSCO()
{
    global $link;
    global $SCOInstanceID;
    // echo $SCOInstanceID;
    // has the SCO previously been initialized?
    $result = mysqli_query($link, "select count(VarName) from scormvars where (SCOInstanceID=$SCOInstanceID)");
    list($count) = mysqli_fetch_row($result);
    // echo $count; exit;
    // not yet initialized - initialize all elements
    if (! $count) {

        // elements that tell the SCO which other elements are supported by this API
        initializeElement('cmi.core._children', 'student_id,student_name,lesson_location,credit,lesson_status,entry,score,total_time,exit,session_time');
        initializeElement('cmi.core.score._children', 'raw');

        // student information
        initializeElement('cmi.core.student_name', getFromLMS('cmi.core.student_name'));
        initializeElement('cmi.core.student_id', getFromLMS('cmi.core.student_id'));

        // test score
        initializeElement('cmi.core.score.raw', '');
        initializeElement('adlcp:masteryscore', getFromLMS('adlcp:masteryscore'));

        // SCO launch and suspend data
        initializeElement('cmi.launch_data', getFromLMS('cmi.launch_data'));
        initializeElement('cmi.suspend_data', '');

        // progress and completion tracking
        initializeElement('cmi.core.lesson_location', '');
        initializeElement('cmi.core.credit', 'credit');
        initializeElement('cmi.core.lesson_status', 'not attempted');
        initializeElement('cmi.core.entry', 'ab-initio');
        initializeElement('cmi.core.exit', '');

        // seat time
        initializeElement('cmi.core.total_time', '0000:00:00');
        initializeElement('cmi.core.session_time', '');
        // 		initializeElement('xapi_content','');
        initializeElement('cmi.interactions._count', '0');
    }

    // new session so clear pre-existing session time
    writeElement('cmi.core.session_time', '');

    // create the javascript code that will be used to set up the javascript cache,
    $initializeCache = "var cache = new Object();\n";

    $result = mysqli_query($link, "select VarName,VarValue from scormvars where (SCOInstanceID=$SCOInstanceID)");
    while (list($varname, $varvalue) = mysqli_fetch_row($result)) {
    
        // make the value safe by escaping quotes and special characters
        $jvarvalue = addslashes($varvalue);

        // javascript to set the initial cache value
        $initializeCache .= "cache['$varname'] = '$jvarvalue';\n";
    }

    // return javascript for cache initialization to the calling program
    return $initializeCache;
}

// ------------------------------------------------------------------------------------
// LMS-specific code
// ------------------------------------------------------------------------------------
function setInLMS($varname, $varvalue)
{
    return "OK";
}

function getFromLMS($varname)
{
	global $SCOInstanceID;
    switch ($varname) {

        case 'cmi.core.student_name':
            $varvalue = "Addison, Steve";
            break;

        case 'cmi.core.student_id':
            $varvalue = "007";
            break;

		case 'adlcp:masteryscore':
			//$varvalue = 50;
			$path_ar = ["../Demo Captivate Quiz SCORM 1.2/",
			"../iSpring Demo Course (SCORM 1.2)/res/",
			"../WWII_Sample_sco/course/",
			"../BigBrute_daily_demo_SCORM_12-20090804-1211/course/",
			"../FMLA_Sample/course/",
			"../Quadratic_sco/course/",
			"../PuzzleQuizSCORMExport/course/",
			"../Demo Captivate Slides Scorm 1.2/",
			"../iSpring Demo Course scorm 1.2 new/res/",
			"../iSpring SCORM 1.2 Quiz with Survey/res/"
			];
			$path = $path_ar[$SCOInstanceID-1];
			$varvalue = getMasteryScore($path.'imsmanifest.xml');
			break;

        case 'cmi.launch_data':
            $varvalue = "";
            break;

        default:
            $varvalue = '';

    }

    return $varvalue;
}
//start reading imsmanifest file

function readIMSManifestFile($manifestfile)
{

//PREPARATIONS

    // central array for resource data
    global $resourceData;
 
    // load the imsmanifest.xml file
    $xmlfile = new DomDocument;
    $xmlfile->preserveWhiteSpace = false;
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
        if (isset($resourceData[$identifierref]['scormtype'])) {
            if (strtolower($resourceData[$identifierref]['scormtype']) != 'sco') {
                continue;
            }

            // save data that we want to the output array

            $SCOdata[$identifier]['title'] = $item['title'];
            //  $SCOdata[$identifier]['masteryscore'] = $item['masteryscore'];
            //  $SCOdata[$identifier]['datafromlms'] = $item['datafromlms'];
            $SCOdata[$identifier]['href'] = $resourceData[$identifierref]['href'];
            if (isset($item['parameters'])) {
                $SCOdata[$identifier]['href'] = $SCOdata[$identifier]['href'].$item['parameters'];
            }
            $SCOdata[$identifier]['files'] = $resourceData[$identifierref]['files'];
        }
    }

    // RETURN RESULTS
    return $SCOdata;
}


function resolveIMSManifestDependencies($identifier)
{
    global $resourceData;

    $files = $resourceData[$identifier]['files'];
    if (isset($resourceData[$identifier]['dependencies'])) {
        $dependencies = $resourceData[$identifier]['dependencies'];
        if (is_array($dependencies)) {
            foreach ($dependencies as $d => $dependencyidentifier) {
                $files = array_merge($files, resolveIMSManifestDependencies($dependencyidentifier));
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
    $dom->preserveWhiteSpace = false;
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
                $masteryscoreTag = $itemsListRow->getElementsByTagNameNS($adlcp, 'masteryscore');
                $launchdataTag = $itemsListRow->getElementsByTagNameNS($adlcp, 'datafromlms');

                // table row
                $ORGdata[$identifier]['identifier'] = $identifier;
                $ORGdata[$identifier]['identifierref'] = $identifierref;
                $ORGdata[$identifier]['name'] =$title;
                ;
            }
        }
    }
    return($ORGdata);
}
function all_tag($xml)
{
    $i=0;
    $name = "";
    foreach ($xml as $k) {
        $tag = $k->getName();
        $tag_value = $xml->$tag;
        if ($name == $tag) {
            $i++;
        }
        $name = $tag;
        echo $tag .' '.$tag_value[$i].'<br />';
        // recursive
        all_tag($xml->$tag->children());
    }
}
function getScormVersion($manifestfile)
{
    // load the imsmanifest.xml file
    $dom = new DomDocument;
    $dom->preserveWhiteSpace = false;
    $dom->load($manifestfile);
    // //print_r($dom->getElementsByTagName('schemaversion'));
    // // adlcp namespace
    $manifest = $dom->getElementsByTagName('manifest');
    $adlcp = $manifest->item(0)->getAttribute('xmlns:adlcp');
    // //print_r($manifest->item(0)->getElementsByTagName('metadata'));
    // //print_r($manifest->baseURI);
    // foreach($manifest as $manifestChild){
    // 	print_r($manifestChild->getElementsByTagName('schema')->nodeValue);
    // }
                
    //$adlcp = $manifest->item(0)->getAttribute('xmlns:adlcp');
    // get the metadata element
    //$metadata = $manifest->item(0)->getElementsByTagName('metadata');
    //print_r($metadata);
    foreach ($manifest as $manifestEl) {
        $metadata = $manifestEl->getElementsByTagName("metadata");
        if ($metadata->item(0)->nodeValue !='') {
            foreach ($metadata as $metadataEl) {
                $schema = $metadataEl->getElementsByTagName("schema");
                $schemaversion = $metadataEl->getElementsByTagName("schemaversion");
                $adlcplocation = $metadataEl->getElementsByTagNameNS($adlcp, "location");
                $scorm_version['schema'] = $schema->item(0)->textContent;
                $scorm_version['schemaversion'] = $schemaversion->item(0)->textContent;
                $scorm_version['adlcplocation'] = @$adlcplocation->item(0)->textContent;
            }
        } else {
            // adlcp namespace
            $adlcp = $manifest->item(0)->getAttribute('xmlns:adlcp');
            // get the organizations element
            $organizationsList = $manifestEl->getElementsByTagName('organizations');
            foreach ($organizationsList as $organizationsListRow) {
                $organizationList = $organizationsListRow->getElementsByTagName('organization');
                foreach ($organizationList as $organizationListRow) {
                    $metadata = $organizationListRow->getElementsByTagName('metadata');
                    foreach ($metadata as $metadataEl) {
                        $schema = $metadataEl->getElementsByTagName("schema");
                        $schemaversion = $metadataEl->getElementsByTagName("schemaversion");
                        $adlcplocation = $metadataEl->getElementsByTagNameNS($adlcp, "location");
                        $scorm_version['schema'] = $schema->item(0)->textContent;
                        $scorm_version['schemaversion'] = $schemaversion->item(0)->textContent;
                        $scorm_version['adlcplocation'] = $adlcplocation->item(0)->textContent;
                    }
                }
            }
        }
    }
                
    // //exit;
    // $scorm_version = [];
    // $dom = simplexml_load_file($manifestfile,null, LIBXML_NOCDATA);
    // $json_string = json_encode($dom);
    // $result_array = json_decode($json_string, TRUE);
    // foreach($result_array as $val_m){
    // 	print_r($val_m);
    // }
    return $scorm_version;
}
function getMasteryScore($manifestfile)
{
    //load the imsmanifest.xml file
    $dom = new DomDocument;
    $dom->preserveWhiteSpace = false;
    $dom->load($manifestfile);
    // adlcp namespace
    $manifest = $dom->getElementsByTagName('manifest');
    $adlcp = $manifest->item(0)->getAttribute('xmlns:adlcp');
	$master_score= 50;
	//echo $manifestfile; exit;
	if($manifestfile == '../Quadratic_sco/course/imsmanifest.xml'){
		$master_score= 3;
	}
	if($manifestfile == '../WWII_Sample_sco/course/imsmanifest.xml'){
		$master_score= 3;
	}
    foreach ($manifest as $manifestEl) {
        // get the organizations element
        $organizationsList = $manifestEl->getElementsByTagName('organizations');
        foreach ($organizationsList as $organizationsListRow) {
            $organizationList = $organizationsListRow->getElementsByTagName('organization');
            foreach ($organizationList as $organizationListRow) {
                $itemNode=$organizationListRow->getElementsByTagName('item');
                //print_r($itemNode->item(0)->nodeName); exit;
                foreach ($itemNode as $itemNodeEl) {
                    $master_scoreNode=$itemNodeEl->getElementsByTagNameNS($adlcp, 'masteryscore');
                    if (@$master_scoreNode->item(0)->textContent !='') {
                        $master_score = $master_scoreNode->item(0)->textContent;
                    }
                }
            }
        }
    }
    // $xpath = new DOMXPath($dom);
    // foreach ($xpath->evaluate('//*[count(*) = 0]') as $node) {
    // 	if($node->nodeName=='adlcp:masteryscore'){
    // 		$master_score =$node->nodeValue;
    // 	}
    //   //var_dump($node->nodeName);
    // }
    // $x = $manifest->documentElement;
    // foreach ($x->childNodes AS $item) {
    // 	$xy = $item->documentElement;
    //   foreach($xy->childNodes as $sub_item){
    // 	print $sub_item->nodeName . " = " . $sub_item->nodeValue . "<br>";
    //   }
    //   if($item->nodeName =='organizations '){
    // 	  foreach($item->childNodes as $organisations){
    // 		  echo $organisations->nodeName. "<br>";
    // 		  if($organisations->nodeName=='organization'){
    // 			foreach($organisations->childNodes as $organisation){
    // 				if($organisation->nodeName=='item'){
    // 					foreach($organisation->childNodes as $items){
    // 						print_r($items->nodeValue);
    // 					}
    // 				}
    // 		  }
    // 	  }
    //   }
    // }
	// }
    return $master_score;
}

function getCourseTitle($manifestfile)
{
    // load the imsmanifest.xml file
    $dom = new DomDocument;
    $dom->preserveWhiteSpace = false;
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
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  Make variable safe to display
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function cleanVar($value)
{
    $value = (trim($value) == "") ? " " : htmlentities(trim($value));
    return $value;
}
