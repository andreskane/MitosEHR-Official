<?php
//--------------------------------------------------------------------------------------------------------------------------
// data_create.ejs.php
// v0.0.2
// Under GPLv3 License
//
// Integrated by: Ernesto Rodriguez in 2011
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

//------------------------------------------
// Database class instance
//------------------------------------------
$mitos_db = new dbHelper();

// *************************************************************************************
// Parce the data generated by EXTJS witch is JSON
// *************************************************************************************
$data = json_decode ( $_POST['row'], true );

// *************************************************************************************
// Validate and pass the POST variables to an array
// This is the moment to validate the entered values from the user
// although Sencha EXTJS make good validation, we could check again 
// just in case 
// *************************************************************************************
$row['id'] = trim($data['id']);

// general info
$row['abook_type']        = dataEncode($data['abook_type']);
$row['title']             = dataEncode($data['title']);
$row['fname']             = dataEncode($data['fname']);
$row['mname']             = dataEncode($data['mname']);
$row['lname']             = dataEncode($data['lname']);
$row['specialty']         = dataEncode($data['specialty']);
$row['organization']      = dataEncode($data['organization']);
$row['valedictory']       = dataEncode($data['valedictory']);
// primary address
$row['street']            = dataEncode($data['street']);
$row['streetb']           = dataEncode($data['streetb']);
$row['city']              = dataEncode($data['city']);
$row['state']             = dataEncode($data['state']);
$row['zip']               = dataEncode($data['zip']);
// secondary address
$row['street2']           = dataEncode($data['street2']);
$row['streetb2']          = dataEncode($data['streetb2']);
$row['city2']             = dataEncode($data['city2']);
$row['state2']            = dataEncode($data['state2']);
$row['zip2']              = dataEncode($data['zip2']);
// phones
$row['phone']             = dataEncode($data['phone']);
$row['phonew1']           = dataEncode($data['phonew1']);
$row['phonew2']           = dataEncode($data['phonew2']);
$row['phonecell']         = dataEncode($data['phonecell']);
$row['fax']               = dataEncode($data['fax']);
//additional info
$row['email']             = dataEncode($data['email']);
$row['assistant']         = dataEncode($data['assistant']);
$row['url']               = dataEncode($data['url']);

$row['upin']              = dataEncode($data['upin']);
$row['npi']               = dataEncode($data['npi']);
$row['federaltaxid']      = dataEncode($data['federaltaxid']);
$row['taxonomy']          = dataEncode($data['taxonomy']);
$row['notes']             = dataEncode($data['notes']);

// *************************************************************************************
// Finally that validated POST variables is inserted to the database
// This one make the JOB of two, if it has an ID key run the UPDATE statement
// if not run the INSERT stament
// *************************************************************************************
$mitos_db->setSQL("UPDATE users 
				      SET id                = '" . $row['id'] . "', " . "
				          abook_type        = '" . $row['abook_type'] . "', " . "
     				      title             = '" . $row['title'] . "', " . "
				          fname             = '" . $row['fname'] . "', " . "
				          mname             = '" . $row['mname'] . "', " . "
				          lname             = '" . $row['lname'] . "', " . "
				          specialty         = '" . $row['specialty'] . "', " . "
				          organization      = '" . $row['organization'] . "', " . "
				          valedictory       = '" . $row['valedictory'] . "', " . "
				          street            = '" . $row['street'] . "', " . "
				          streetb           = '" . $row['streetb'] . "', " . "
				          city              = '" . $row['city'] . "', " . "
				          state             = '" . $row['state'] . "', " . "
				          zip               = '" . $row['zip'] . "', " . "
				          street2           = '" . $row['street2'] . "', " . "
				          streetb2          = '" . $row['streetb2'] . "', " . "
				          city2             = '" . $row['city2'] . "', " . "
				          state2            = '" . $row['state2'] . "', " . "
				          zip2              = '" . $row['zip2'] . "', " . "
				          phone             = '" . $row['phone'] . "', " . "
				          phonew1           = '" . $row['phonew1'] . "', " . "
				          phonew2           = '" . $row['phonew2'] . "', " . "
				          phonecell         = '" . $row['phonecell'] . "', " . "
				          fax               = '" . $row['fax'] . "', " . "
				          email             = '" . $row['email'] . "', " . "
				          assistant         = '" . $row['assistant'] . "', " . "
				          url               = '" . $row['url'] . "', " . "
				          upin              = '" . $row['upin'] . "', " . "
				          npi               = '" . $row['npi'] . "', " . "
				          federaltaxid      = '" . $row['federaltaxid'] . "', " . "
				          taxonomy          = '" . $row['taxonomy'] . "', " . "
				          notes             = '" . $row['notes'] . "' " . " 
				    WHERE id 				= '" . $row['id'] . "'");
$mitos_db->execLog();
echo "{ success: true }";
?>