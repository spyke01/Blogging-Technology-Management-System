<? 
/***************************************************************************
 *                               install.php
 *                            -------------------
 *   begin                : Tuseday, March 14, 2006
 *   copyright            : (C) 2006 Fast Track Sites
 *   email                : sales@fasttracksites.com
 *
 *
 ***************************************************************************/

/***************************************************************************
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of the <organization> nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 ***************************************************************************/
	ini_set('arg_separator.output','&amp;');
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');
	
	// Set up our installer
	define('UPDATER_SCRIPT_NAME', 'Blogging Technology System');
	
	// Inlcude the needed files
	include_once ('includes/constants.php');
	if (substr(phpversion(), 0, 1) == 5) { include_once ('includes/php5/pageclass.php'); }
	else { include_once ('includes/php4/pageclass.php'); }

	// Instantiate our page class
	$page = &new pageClass;

	// Handle our variables
	$requested_step = $_GET['step'];

	$actual_step = ($requested_step == "" || !isset($requested_step)) ? 1 : keepsafe($requested_step);
	$page_content = "";
	$failed = 0;
	$totalfailure = 0;
	$failed = array();
	$failedsql = array();
	$currentdate = time();

	
	//========================================
	// Custom Functions for this Page
	//========================================
	function keepsafe($makesafe) {
		$makesafe=strip_tags($makesafe); // strip away any dangerous tags
		$makesafe=str_replace(" ","",$makesafe); // remove spaces from variables
		$makesafe=str_replace("%20","",$makesafe); // remove escaped spaces
		$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127
		$makesafe = stripslashes($makesafe);
		
		return $makesafe;
	}
	
	function keeptasafe($makesafe) {
		$makesafe=strip_tags($makesafe); // strip away any dangerous tags
		$makesafe = trim(preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/e', '"&#".ord($0).";"', $makesafe)); //encodes all ascii items above #127
		$makesafe = stripslashes($makesafe);
		
		return $makesafe;
	}

	function checkresult($result, $sql, $table) {
		global $failed;
		global $failedsql;
		global $totalfailure;
		
		if (!$result || $result == "") {
			$failed[$table] = "failed";
			$failedsql[$table] = $sql;
			$totalfailure = 1;
		}  
		else {
			$failed[$table] = "succeeded";
			$failedsql[$table] = $sql;
		}	
	}
	
	//========================================
	// Build our Page
	//========================================
	switch ($actual_step) {
		case 1:
			$page->setTemplateVar("PageTitle", UPDATER_SCRIPT_NAME . " Step 1 - Update Database Connection");	
			
			// Print this page
			$page_content = "
					<h2>Welcome to the Fast Track Sites Script Updater</h2>
					Thank you for downloading the " . UPDATER_SCRIPT_NAME . " this page will walk you through the update procedure.
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/addDatabase_38px.png\" alt=\"Add Database\" /></span> <span class=\"iconText38px\">Update Your Database Connection</span></h2>
					Press Next to update the database connection.
					<br /><br />
					<a href=\"update.php?step=2\" class=\"button\">Next</a>";
			break;
		case 2:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 2 - Update Database Tables");	
			
			include('_db.php');
			
			$str = "<?PHP\n\n// Connect to the database\n\n\$server = \"" . $server . "\";\n\$dbuser = \"" . $dbuser . "\";\n\$dbpass = \"" . $dbpass . "\";\n\$dbname = \"" . $dbname . "\";\ndefine('DBTABLEPREFIX', '" . $DBTABLEPREFIX . "');\ndefine('USERSDBTABLEPREFIX', '" . $USERSDBTABLEPREFIX . "');\n\n\$connect = mysql_connect(\$server,\$dbuser,\$dbpass);\n\n//display error if connection fails\nif (\$connect==FALSE) {\n   print 'Unable to connect to database: '.mysql_error();\n   exit;\n}\n\nmysql_select_db(\$dbname); // select database\n\n?>";
		
			$fp=fopen("_db.php","w");
			$result = fwrite($fp,$str);
			fclose($fp);	
			
			// Print this page
			$page_content = "
					<h2>Database Connection Results</h2>";
			
			if (!$result || $result == "") {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create database connection file.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created database connection file.</span></span>";
			}
			
			$page_content .= "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/table_38px.png\" alt=\"Create Tables\" /></span> <span class=\"iconText38px\">Update Database Tables</span></h2>
					Press Next to update the database tables.
					<br /><br />
					<a href=\"update.php?step=3\" class=\"button\">Next</a>";
			break;
		case 3:
			$page->setTemplateVar("PageTitle", UPDATER_SCRIPT_NAME . " Step 3 - Finish");	
			
			include('_db.php');
			
			// Update our Database Tables	
			$sql = "ALTER TABLE `" . DBTABLEPREFIX . "categories` 
					CHANGE `cat_id` `id` MEDIUMINT( 8 ) NOT NULL AUTO_INCREMENT ,
					CHANGE `cat_name` `name` VARCHAR( 50 ) NOT NULL;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "categories");
			
			$sql = "ALTER TABLE `" . DBTABLEPREFIX . "comments` 
					CHANGE `comments_id` `id` MEDIUMINT( 8 ) NOT NULL AUTO_INCREMENT ,
					CHANGE `comments_entry_id` `entry_id` MEDIUMINT( 8 ) NOT NULL DEFAULT '0',
					CHANGE `comments_name` `name` VARCHAR( 50 ) NOT NULL DEFAULT '0',
					CHANGE `comments_user_id` `user_id` MEDIUMINT( 8 ) NOT NULL DEFAULT '0',
					CHANGE `comments_email` `email_address` VARCHAR( 100 ) NOT NULL ,
					CHANGE `comments_website` `website` VARCHAR( 100 ) NOT NULL ,
					CHANGE `comments_date` `datetimestamp` VARCHAR( 255 ) NOT NULL ,
					CHANGE `comments_comment` `comment` TEXT NOT NULL;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "comments");
			
			$sql = "ALTER TABLE `" . DBTABLEPREFIX . "config` 
					CHANGE `config_name` `name` VARCHAR( 255 ) NOT NULL ,
					CHANGE `config_value` `value` VARCHAR( 255 ) NOT NULL;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "config");
			
			$sql = "ALTER TABLE `" . DBTABLEPREFIX . "entries` 
					CHANGE `entries_id` `id` MEDIUMINT( 8 ) NOT NULL AUTO_INCREMENT ,
					CHANGE `entries_cat_id` `cat_id` MEDIUMINT( 8 ) NOT NULL DEFAULT '0',
					CHANGE `entries_title` `title` VARCHAR( 255 ) NOT NULL ,
					CHANGE `entries_content` `content` TEXT NOT NULL ,
					CHANGE `entries_password` `password` VARCHAR( 30 ) NOT NULL DEFAULT '1',
					CHANGE `entries_diggurl` `diggurl` VARCHAR( 200 ) NOT NULL ,
					CHANGE `entries_date` `datetimestamp` VARCHAR( 20 ) NOT NULL ,
					CHANGE `entries_user_id` `user_id` MEDIUMINT( 8 ) NOT NULL DEFAULT '0',
					CHANGE `entries_type` `type` TINYINT( 1 ) NOT NULL DEFAULT '1',
					CHANGE `entries_visible` `visible` TINYINT( 1 ) NOT NULL DEFAULT '1',
					CHANGE `entries_allow_comments` `allow_comments` TINYINT( 1 ) NOT NULL DEFAULT '1',
					CHANGE `entries_show_digg_button` `show_digg_button` TINYINT( 1 ) NOT NULL DEFAULT '1',
					CHANGE `entries_show_reddit_button` `show_reddit_button` TINYINT( 1 ) NOT NULL DEFAULT '1',
					CHANGE `entries_show_delicious_button` `show_delicious_button` TINYINT( 1 ) NOT NULL DEFAULT '1',
					CHANGE `entries_show_slashdot_button` `show_slashdot_button` TINYINT( 1 ) NOT NULL DEFAULT '1';";
			$result = mysql_query($sql);
			checkresult($result, $sql, "entries");
			
			$sql = "ALTER TABLE `" . DBTABLEPREFIX . "menus` 
					CHANGE `menus_id` `id` MEDIUMINT( 8 ) NOT NULL AUTO_INCREMENT ,
					CHANGE `menus_title` `title` VARCHAR( 200 ) NOT NULL ,
					CHANGE `menus_link` `link` VARCHAR( 255 ) NOT NULL ,
					CHANGE `menus_type` `type` TINYINT( 1 ) NOT NULL DEFAULT '1',
					CHANGE `menus_order` `order` MEDIUMINT( 8 ) NOT NULL DEFAULT '0';";
			$result = mysql_query($sql);
			checkresult($result, $sql, "menus");
			
			$sql = "ALTER TABLE `" . DBTABLEPREFIX . "modules` 
					CHANGE `modules_id` `id` MEDIUMINT( 8 ) NOT NULL AUTO_INCREMENT ,
					CHANGE `modules_name` `name` VARCHAR( 200 ) NOT NULL ,
					CHANGE `modules_active` `active` TINYINT( 1 ) NOT NULL DEFAULT '1';";
			$result = mysql_query($sql);
			checkresult($result, $sql, "modules");
			
			$sql = "ALTER TABLE `" . USERSDBTABLEPREFIX . "users` 
					CHANGE `users_id` `id` MEDIUMINT( 8 ) NOT NULL AUTO_INCREMENT ,
					CHANGE `users_username` `username` VARCHAR( 255 ) NOT NULL ,
					CHANGE `users_password` `password` VARCHAR( 255 ) NOT NULL ,
					CHANGE `users_full_name` `first_name` VARCHAR( 50 ) NOT NULL ,
					ADD `last_name` VARCHAR( 50 ) NOT NULL AFTER `first_name`,
					CHANGE `users_email_address` `email_address` VARCHAR( 255 ) NOT NULL ,
					CHANGE `users_website` `website` VARCHAR( 255 ) NOT NULL ,
					CHANGE `users_signup_date` `signup_date` INT( 11 ) NULL DEFAULT NULL ,
					CHANGE `users_notes` `notes` TEXT NOT NULL ,
					CHANGE `users_tech` `tech` TINYINT( 1 ) NOT NULL DEFAULT '0',
					CHANGE `users_user_level` `user_level` TINYINT( 1 ) NOT NULL DEFAULT '0',
					CHANGE `users_active` `active` TINYINT( 1 ) NOT NULL DEFAULT '1',
					CHANGE `users_language` `language` VARCHAR( 5 ) NOT NULL DEFAULT 'en';";
			$result = mysql_query($sql);
			checkresult($result, $sql, "users");
		
			// Print this page
			$page_content = "
					<h2>Update Database Tables Results</h2>";
			
			if ($totalfailure == 1) {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to update database tables.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully updated database tables.</span></span>";
			}
			
			$page_content .= "
					<br /><br />
					<h2>Finishing Up</h2>
					Update is now complete, before using the system please make sure and delete this file (update.php) so that it cannot be reused by someone else.
					<br /><br />
					<a href=\"index.php\" class=\"button\">Finish</a>";
			break;	
	}
	
	// Send out the content
	$page->setTemplateVar("PageContent", $page_content);	
	
	include "themes/installer/updatertemplate.php";
?>