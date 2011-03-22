<?php
/* setup class v0.0.1
 * Description: A Class to setup sites dir much easier. 
 * 
 * Author: Ernesto Rodriguez
 */
class SiteSetup {
	
	// Globals inside he Class
	var $siteName;
	var $siteDbPrefix;
	var $siteDbUser;
	var $siteDbPass;

	//*****************************************************************************************
	// ckeck cmod 777
	//*****************************************************************************************
	function check_perms($path){
	    clearstatcache();
    	$configmod = substr(sprintf('%o', fileperms($path)), -4);
		return true; 
	}

	//*****************************************************************************************
	// create site name folder inside /sites/
	//*****************************************************************************************
	function createFolders($siteName) {
		mkdir("sites/" . $siteName, 0777);
		return true;
	}

	//*****************************************************************************************
	// Connection error handeler
	//*****************************************************************************************
	function connError($conn) {
		$conn->errorInfo();
		$errorCode = $error[1];
		$errorMsg =  $error[2];
		echo "{success:false,errors:{reason:'Error:".$errorCode." - ".$errorMsg."'}}";
	 	return;
	}
	
	//*****************************************************************************************
	// Database connection and error handeler
	//*****************************************************************************************
	function databaseConn($db_server,$db_port,$db_name,$db_rootUser,$db_rootPass) {
		//-------------------------------------------------------------------------------------
		// connection to mysql w/o database
		//-------------------------------------------------------------------------------------
		$conn = new PDO("mysql:host=".$db_server.";port=".$db_port,$db_rootUser,$db_rootPass);
		//-------------------------------------------------------------------------------------
		// send error callback if conn can't be stablish
		//-------------------------------------------------------------------------------------
		if (!$conn){
			connError($conn);
		} else {
			return $conn;
		}
	}
	
	//*****************************************************************************************
	// create database
	//*****************************************************************************************
	function createDataBase($db_server,$db_port,$db_name,$db_rootUser,$db_rootPass) {
		//-------------------------------------------------------------------------------------
		// connection to mysql w/o database
		//-------------------------------------------------------------------------------------
		databaseConn($db_server,$db_port,$db_name,$db_rootUser,$db_rootPass);
		//-------------------------------------------------------------------------------------
		// if connection exist Create Databse and dump sql data
		//-------------------------------------------------------------------------------------
		if ($conn){
			$conn->exec("CREATE DATABASE ".$db_name."");
			//---------------------------------------------------------------------------------
			// lets check for conn errors
			//---------------------------------------------------------------------------------
			if (!$conn->errorInfo()) {
				//-----------------------------------------------------------------------------
				// if no errors found lets create the database user and give him all privileges
				//-----------------------------------------------------------------------------
				$conn->exec("GRANT ALL PRIVILEGES ON ".$db_name.".* 
							 					  TO '".$siteDbUser."'@'localhost'
	       					 		   IDENTIFIED BY '".$siteDbPass."' 
	       					 	   WITH GRANT OPTION;");
				//-----------------------------------------------------------------------------
				// lets check for conn errors
				//-----------------------------------------------------------------------------
				if (!$conn->errorInfo()) {
					//-------------------------------------------------------------------------
					// if no error found try to connect using new user 
					//-------------------------------------------------------------------------
					$conn = new PDO("mysql:host=".$db_server.";
									 port=".$db_port.";
									 dbname=".$db_name,$siteDbUser,$siteDbPass);
					//-------------------------------------------------------------------------
					// lets check for conn errors
					//-------------------------------------------------------------------------
					if (!$conn->errorInfo()) {
						//---------------------------------------------------------------------
						// if no error found look for sitesetup.qsl file
						//---------------------------------------------------------------------
						if (file_exists("sitesetup.sql")) {
							//-----------------------------------------------------------------
							// if sitesetup.sql found, open it
							//-----------------------------------------------------------------
							if ($sqlDump = fopen("sitesetup.sql", "r")) {
								//-------------------------------------------------------------
								// if was able to open, lets executed
								//-------------------------------------------------------------
								$conn->exec($sqlDump);
								//-------------------------------------------------------------
								// then close it
								//-------------------------------------------------------------
								fclose($sqlDump);
								//-------------------------------------------------------------
								// and check for errors
								//-------------------------------------------------------------
								if ($conn->errorInfo()) {
									connError($conn);
								} else {
									//---------------------------------------------------------
									// Grats! we made it! Database created
									//---------------------------------------------------------
									return $conn;
								}
							} else {
								//-------------------------------------------------------------
								// error if unable to open sitesetup.sql
								//-------------------------------------------------------------
								die("{success:false,errors:{reason:'Error: Unable to open sitesetup.sql'}}");
							}
						} else {
							//-----------------------------------------------------------------
							// error if sitesetup.sql not found
							//-----------------------------------------------------------------
							die("{success:false,errors:{reason:'Error: Unable to find sitesetup.sql'}}");
						}
					} else {
						//---------------------------------------------------------------------
						// if Can't stablish connection as user, send error
						//---------------------------------------------------------------------
						connError($conn);
					}
				} else {
					//-------------------------------------------------------------------------
					// if Can't create the user, send error
					//-------------------------------------------------------------------------
					connError($conn);
				}
			} else {
				//-----------------------------------------------------------------------------
				// if Can't create the user, send error
				//-----------------------------------------------------------------------------
				connError($conn);
			}
		} else {
			//---------------------------------------------------------------------------------
			// if no $conn just return false
			//---------------------------------------------------------------------------------
			return false;
		}
	} // end function createDataBase
	
	//*****************************************************************************************
	// set Default Language
	//*****************************************************************************************
	function defaultLanguage($langauge) {
	 	//-------------------------------------------------------------------------------------
	 	// ask Gino how we wanna do this!
	 	//-------------------------------------------------------------------------------------
		$recordset = $conn->query("");
		
	}
	
	//*****************************************************************************************
	// create site files
	//*****************************************************************************************
	function createSiteConf() {
		// create site conf.php inside current site folder
		$siteConf = tempnam("sites/".$siteName."conf.php");
		// change permission to 777
		chmod($siteConf, 0777);
		// open new site conf.php
		$handle = fopen($siteConf, "w");
		// write con.php with site configuration
		fwrite($handle, "writing to conf.php");
		// close file pointer
		fclose($handle);
		// change pernission back to 644
		chmod($siteConf, 0644);
		
	}
	
} // end class siteSetup

?>