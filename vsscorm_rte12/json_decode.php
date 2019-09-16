<?php

require 'config.php';
function dbConnect() {

	// database login details
	global $dbname;
	global $dbhost;
	global $dbuser;
	global $dbpass;

	// link
	global $link;

	// connect to the database
	$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
	//mysqli_select_db($dbname,$link);

}
dbConnect();

	global $link;

// echo "select value from  `scormvars_full_data` where params_key='Statement'";
//          echo '<br /><br />';
		$result = mysqli_query($link,"select id,value from  `scormvars_full_data` where params_key='Statement'") or die(mysqli_error($link));
        while(list($id,$value) = mysqli_fetch_row($result)){
            echo '<pre>';
            echo $id;
            echo trim($value,'"');
echo json_decode($value);
mysqli_query($link,"update  `scormvars_full_data` set value='".json_decode($value)."' where id =$id") or die(mysqli_error($link));
        }

