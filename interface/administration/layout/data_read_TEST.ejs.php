<?php
//--------------------------------------------------------------------------------------------------------------------------
// data_read.ejs.php
// Desc: Read all the data related to the layout, in this case the fields and groups
// v0.0.1
// Under GPLv3 License
//
// Integrated by: Gi Technologies. in 2011
//
// Remember, this file is called via the Framework Store, this is the AJAX thing.
//--------------------------------------------------------------------------------------------------------------------------

session_name ( "MitosEHR" );
session_start();
session_cache_limiter('private');

include_once("../../../library/dbHelper/dbHelper.inc.php");
include_once("../../../library/I18n/I18n.inc.php");

// **************************************************************************************
// Reset session count 10 secs = 1 Flop
// **************************************************************************************
$_SESSION['site']['flops'] = 0;

// **************************************************************************************
// Database class instance
// **************************************************************************************
$mitos_db = new dbHelper();

// **************************************************************************************
// Verify if a $_GET['id'] has passed to select a facility.
// and execute the apropriate SQL statement
// **************************************************************************************
if(!$_REQUEST['form_id']){
	$sql = "SELECT * FROM layout_options WHERE form_id='Demographics' ORDER BY seq";
} else {
	$sql = "SELECT * FROM layout_options WHERE form_id='". $_REQUEST['form_id'] . "' ORDER BY seq";
}

$mitos_db->setSQL($sql);
//---------------------------------------------------------------------------------------
// catch the total records
//---------------------------------------------------------------------------------------
$total = $mitos_db->rowCount();
//---------------------------------------------------------------------------------------
// start the array
//---------------------------------------------------------------------------------------
$rows = array();
foreach($mitos_db->execStatement() as $row){

	//-----------------------------------------------------------------------------------
	// add something to the array example
	//
	// $row['fullname'] =  $row['lastname'].', '.$row['middlename'].' '.$row['firstname'];
	//-----------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------
	// push the user inside the $users array
	//-----------------------------------------------------------------------------------
	array_push($rows, $row);
}
//---------------------------------------------------------------------------------------
// here we are adding "totals" and the root "row" for sencha use 
//---------------------------------------------------------------------------------------
print_r(json_encode(array('totals'=>$total,'row'=>$rows)));
?>