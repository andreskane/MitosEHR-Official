<?php
//--------------------------------------------------------------------------------------------------------------------------
// data_create.ejs.php
// v0.0.2
// Under GPLv3 License
//
// Integrated by: GI Technologies Inc. in 2011
//
// Remember, this file is called via the Framework Store, this is the AJAX thing.
//--------------------------------------------------------------------------------------------------------------------------

session_name ( "MitosEHR" );
session_start();
session_cache_limiter('private');

include_once($_SESSION['site']['root']."/library/dbHelper/dbHelper.inc.php");
include_once($_SESSION['site']['root']."/library/I18n/I18n.inc.php");
require_once($_SESSION['site']['root']."/repository/dataExchange/dataExchange.inc.php");

//******************************************************************************
// Reset session count 10 secs = 1 Flop
//******************************************************************************
$_SESSION['site']['flops'] = 0;

$mitos_db = new dbHelper();

// *************************************************************************************
// Parce the data generated by EXTJS witch is JSON
// *************************************************************************************
$data = json_decode ( $_REQUEST['row'] );

// *************************************************************************************
// Validate and pass the POST variables to an array
// This is the moment to validate the entered values from the user
// although Sencha EXTJS make good validation, we could check it again 
// just in case 
// *************************************************************************************
$row['id'] 					= trim($data->id);
$row['name'] 				= $data->name;
$row['phone'] 				= $data->phone;
$row['fax'] 				= $data->fax;
$row['street'] 				= $data->street;
$row['city'] 				= $data->city;
$row['state'] 				= $data->state;
$row['postal_code'] 		= $data->postal_code;
$row['country_code'] 		= $data->country_code;
$row['federal_ein'] 		= $data->federal_ein;
$row['service_location'] 	= ($data->service_location == 'on') ? 1 : 0;
$row['accepts_assignment'] 	= ($data->accepts_assignment == 'on') ? 1 : 0;
$row['billing_location'] 	= ($data->billing_location == 'on') ? 1 : 0;
$row['pos_code'] 			= $data->pos_code;
$row['domain_identifier'] 	= $data->domain_identifier;
$row['attn'] 				= $data->attn;
$row['tax_id_type'] 		= $data->tax_id_type;
$row['facility_npi'] 		= $data->facility_npi;

// *************************************************************************************
// Finally that validated POST variables is inserted to the database
// This one make the JOB of two, if it has an ID key run the UPDATE statement
// if not run the INSERT stament
// *************************************************************************************
$sql = $mitos_db->sqlBind($row, "facility", "U", "id='" . $row['id'] . "'");
$mitos_db->setSQL($sql);
$ret = $mitos_db->execLog();

if ( $ret == "" ){
	echo '{ success: false, errors: { reason: "'. $ret[2] .'" }}';
} else {
	echo "{ success: true }";
}

?>