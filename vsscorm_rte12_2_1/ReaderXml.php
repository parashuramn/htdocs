<?php

$path_ar = ["..\Demo Captivate Quiz SCORM 1.2\\",
"..\iSpring Demo Course (SCORM 1.2)\\res\\",
"..\WWII_Sample_sco\course\\",
"..\BigBrute_daily_demo_SCORM_12-20090804-1211\course\\",
"\..\FMLA_Sample\course\\",
"..\Quadratic_sco\course\\",
"..\PuzzleQuizSCORMExport\course\\",
"..\Demo Captivate Slides Scorm 1.2\\",
"..\iSpring Demo Course scorm 1.2 new\\res\\",
"..\iSpring SCORM 1.2 Quiz with Survey\\res\\"
];
// str_replace('\\','/',__DIR__)
//echo $path = __DIR__.str_replace('\\','/',$path_ar[4]); 
print "PHP_VERSION:      ".PHP_VERSION."\n"."<br />";
print "LIBXML_VERSION:   ".LIBXML_VERSION."\n"."<br />";
print "LIBXML_VERSION:   ".LIBXML_DOTTED_VERSION."\n"."<br />";

print "LIBXML_NOXMLDECL: ".LIBXML_NOXMLDECL."\n"."<br />";
// try{
$xmlDoc = new DOMDocument();
$xmlDoc->preserveWhiteSpace = FALSE;
$xmlDoc->load( 'C:/Bitnami/wampstack-7.1.31-0/apache2/htdocs/FMLA_Sample/course/imsmanifest.xml' );
// $xsdpath = new DOMXPath($xmlDoc);
    // $attributeNodes =
    //           $xsdpath->
    //           query('//xs:simpleType[@name="attributeType"]')
    //           ->item(0);
    //           print_r($attributeNodes);

$searchNode = $xmlDoc->getElementsByTagName( "manifest" );
print_r($searchNode);

foreach( $searchNode as $searchNodeEl )
{
    //$valueID = $searchNode->getAttribute('ID');
    $metadata = $searchNodeEl->getElementsByTagName( "metadata" );
    foreach($metadata as $metadataEl){
        $schema = $metadataEl->getElementsByTagName( "schema" );
        print_r($schema->item(0)->textContent);
    }
    //$metadata_value = $metadata->item(0)->nodeValue;
     //print_r($metadata->item(0)->childNodes);
    // $xmlAuthorID = $searchNode->getElementsByTagName( "AuthorID" );
    // $valueAuthorID = $xmlAuthorID->item(0)->nodeValue;
   
    // echo "$valueID - $valueDate - $valueAuthorID\n";
    //echo "$metadata - $metadata_value\n";
}
// }catch(DOMException $dt){
// print_r( $dt->getMessage());
// }