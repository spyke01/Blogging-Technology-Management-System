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
	define('INSTALLER_SCRIPT_NAME', 'Blogging Technology System');
	define('INSTALLER_SCRIPT_DESC', 'Designed to allow people to create their own custom blogs, the FTSBTS allows for easy addition and maintainance of entries, categories, and more.');
	define('INSTALLER_SCRIPT_IS_PROFESSIONAL_VERSION', 1);
	define('INSTALLER_SCRIPT_DB_PREFIX', 'BTS_');
	
	// Inlcude the needed files
	include_once ('includes/constants.php');
	if (substr(phpversion(), 0, 1) == 5) { include_once ('includes/classes/php5/pageclass.php'); }
	else { include_once ('includes/classes/php4/pageclass.php'); }

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
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 1 - Introduction");	
			
			// Print this page
			$page_content = "
					<h2>Welcome to the Fast Track Sites Script Installer</h2>
					Thank you for downloading the " . INSTALLER_SCRIPT_NAME . " this page will walk you through the setup procedure.
					<br /><br />
					" . INSTALLER_SCRIPT_DESC . "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/paperAndPencil_38px.png\" alt=\"License Agreement\" /></span> <span class=\"iconText38px\">License Agreement</span></h2>
					By installing this application you are agreeing to all the terms and conditions stated in the <a href=\"http://www.fasttracksites.com/ftspl\">Fast Track Sites Program License</a>.
					<br /><br />";
					
			if (INSTALLER_SCRIPT_IS_PROFESSIONAL_VERSION) {
				$page_content .= "
					Please enter your registration information below, failure to do so can result in your application being disabled.
					<br /><br />
					<form id=\"licenseInformationForm\" action=\"install.php?step=2\" method=\"post\">
						<label for=\"serialNumber\">Serial Number</label> <input type=\"text\" name=\"serialNumber\" id=\"serialNumber\" class=\"required\" />
						<label for=\"registeredTo\">Registered To</label> <input type=\"text\" name=\"registeredTo\" id=\"registeredTo\" class=\"required\" />
						<input type=\"submit\" name=\"submit\" class=\"button\" value=\"Next\" />
					</form>
					<script type=\"text/javascript\">
						var valid = new Validation('licenseInformationForm', {immediate : true, useTitles:true});
					</script>";
			}
			else {
				$page_content .= "
					<a href=\"install.php?step=2\" class=\"button\">I Agree</a>";			
			}
			break;
		case 2:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 2 - Database Connection");	
			
			// Create our license file
			if (INSTALLER_SCRIPT_IS_PROFESSIONAL_VERSION) {
				$serialNumber = keepsafe($_POST['serialNumber']);
				$registeredTo = keeptasafe($_POST['registeredTo']);
			}
			else {
				$serialNumber = "Free Edition";
				$registeredTo = "Fast Track Sites";
			}
			
			$str = "<?PHP\n\n\$A_License = \"" . $serialNumber . "\";\n\$A_Licensed_To = \"" . $registeredTo . "\";\n\n?>";
		
			$fp=fopen("_license.php","w");
			$result = fwrite($fp,$str);
			fclose($fp);	
			
			// Print this page
			$page_content = "
					<h2>License File Results</h2>";
			
			if (!$result || $result == "") {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create license file.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created license file.</span></span>";
			}
			
			$page_content .= "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/addDatabase_38px.png\" alt=\"Add Database\" /></span> <span class=\"iconText38px\">Configure Your Database Connection</span></h2>
					Please enter your database information below:
					<br /><br />
					<form id=\"databaseConnectionForm\" action=\"install.php?step=3\" method=\"post\">
						<label for=\"dbServer\">Server</label> <input type=\"text\" name=\"dbServer\" id=\"dbServer\" class=\"required\" />
						<label for=\"dbName\">Database Name</label> <input type=\"text\" name=\"dbName\" id=\"dbName\" class=\"required\" />
						<label for=\"dbUsername\">Username</label> <input type=\"text\" name=\"dbUsername\" id=\"dbUsername\" class=\"required\" />
						<label for=\"dbPassword\">Password</label> <input type=\"password\" name=\"dbPassword\" id=\"dbPassword\" class=\"required\" />
						<label for=\"dbTablePrefix\">Table Prefix</label> <input type=\"text\" name=\"dbTablePrefix\" id=\"dbTablePrefix\" class=\"required\" value=\"" . INSTALLER_SCRIPT_DB_PREFIX . "\" />
						<input type=\"submit\" name=\"submit\" class=\"button\" value=\"Next\" />
					</form>
					<script type=\"text/javascript\">
						var valid = new Validation('databaseConnectionForm', {immediate : true, useTitles:true});
					</script>";
			break;
		case 3:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 3 - Create database Tables");	
			
			// Create our database connection file
			$dbServer = keepsafe($_POST['dbServer']); 
			$dbName = keepsafe($_POST['dbName']); 
			$dbUsername = keepsafe($_POST['dbUsername']); 
			$dbPassword = keepsafe($_POST['dbPassword']); 
			$DBTABLEPREFIX = keepsafe($_POST['dbTablePrefix']); 
			
			$str = "<?PHP\n\n// Connect to the database\n\n\$server = \"" . $dbServer . "\";\n\$dbuser = \"" . $dbUsername . "\";\n\$dbpass = \"" . $dbPassword . "\";\n\$dbname = \"" . $dbName . "\";\ndefine('DBTABLEPREFIX', '" . $DBTABLEPREFIX . "');\ndefine('USERSDBTABLEPREFIX', '" . $DBTABLEPREFIX . "');\n\n\$connect = mysql_connect(\$server,\$dbuser,\$dbpass);\n\n//display error if connection fails\nif (\$connect==FALSE) {\n   print 'Unable to connect to database: '.mysql_error();\n   exit;\n}\n\nmysql_select_db(\$dbname); // select database\n\n?>";
		
			$fp=fopen("_db.php","w");
			$result = fwrite($fp,$str);
			fclose($fp);	
	
			// Create our .htaccess file
			$str = "RewriteEngine On\n
					RewriteBase " . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "\n
					Options +FollowSymLinks -Multiviews\n
					\n
					# Rules to ensure that normal content gets through\n
					RewriteRule themes/(.*) themes/$1 [L]\n
					RewriteRule themes/images/(.*) themes/images/$1 [L]\n
					RewriteRule images/(.*) images/$1 [L]\n
					\n
					# General\n
					RewriteRule ^login$ index.php?p=login [QSA,L]\n
					RewriteRule ^login/$ index.php?p=login [QSA,L]\n
					RewriteRule ^logout$ index.php?p=logout [QSA,L]\n
					RewriteRule ^logout/$ index.php?p=logout [QSA,L]\n
					RewriteRule ^register$ index.php?p=register [QSA,L]\n
					RewriteRule ^register/$ index.php?p=register [QSA,L]\n
					\n
					# Admin Panel\n
					RewriteRule ^admin$ index.php?p=admin [QSA,L]\n
					RewriteRule ^admin/$ index.php?p=admin [QSA,L]\n
					RewriteRule ^admin/([a-z0-9]+)$ index.php?p=admin&s=$1 [QSA,L]\n
					RewriteRule ^admin/([a-z0-9]+)/$ index.php?p=admin&s=$1 [QSA,L]\n
					\n
					# View Entry\n
					RewriteRule ^viewentry$ index.php?p=viewentry [QSA,L]\n
					RewriteRule ^viewentry/$ index.php?p=viewentry [QSA,L]\n
					RewriteRule ^viewentry/([a-z0-9]+)$ index.php?p=viewentry&s=$1 [QSA,L]\n
					RewriteRule ^viewentry/([a-z0-9]+)/$ index.php?p=viewentry&s=$1 [QSA,L]\n
					\n
					# View Category\n
					RewriteRule ^viewcategory$ index.php?p=viewcategory [QSA,L]\n
					RewriteRule ^viewcategory/$ index.php?p=viewcategory [QSA,L]\n
					RewriteRule ^viewcategory/([a-z0-9]+)$ index.php?p=viewcategory&s=$1 [QSA,L]\n
					RewriteRule ^viewcategory/([a-z0-9]+)/$ index.php?p=viewcategory&s=$1 [QSA,L]\n";
			
			$fp=fopen(".htaccess","w");
			//$htaccessresult = fwrite($fp,$str);
			fclose($fp);		
			checkresult($result, "The installation program failed to create the .htaccess file, you will need to do this manually. Please see the readme file for more information.", "dbconnection");
		
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
			/*
			$page_content .= "
					<br />";
					
			if (!$htaccessresult || $htaccessresult == "") {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create .htaccess file.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created .htaccess file.</span></span>";
			}
			*/
			$page_content .= "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/table_38px.png\" alt=\"Create Tables\" /></span> <span class=\"iconText38px\">Create database Tables</span></h2>
					Press Next to create the database tables.
					<br /><br />
					<a href=\"install.php?step=4\" class=\"button\">Next</a>";
			break;
		case 4:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 4 - Create Admin Account");	
			
			include('_db.php');
			
			// Create our Database Tables
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "config` (
					`name` varchar(255) NOT NULL default '',
					`value` varchar(255) NOT NULL default ''
					) TYPE=MyISAM;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "config");
	  		
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "comments` (
					`id` mediumint(8) NOT NULL auto_increment,
					`entry_id` mediumint(8) NOT NULL default '0',
					`name` varchar(50) NOT NULL default '0',
					`user_id` mediumint(8) NOT NULL default '0',
					`email_address` varchar(100) NOT NULL default '',
					`website` varchar(100) NOT NULL default '',
					`datetimestamp` varchar(255) NOT NULL default '',
					`comment` text NOT NULL,
				  	PRIMARY KEY  (`id`)
					) TYPE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "comments");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "categories` (
					`id` mediumint(8) NOT NULL auto_increment,
					`name` varchar(50) NOT NULL default '',
					PRIMARY KEY  (`id`)
					) TYPE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "categories");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "entries` (
					`id` mediumint(8) NOT NULL auto_increment,
					`cat_id` mediumint(8) NOT NULL default '0',
					`title` varchar(255) NOT NULL default '',
					`content` text NOT NULL,
					`password` varchar(30) NOT NULL default '1',
					`diggurl` varchar(200) NOT NULL default '',
					`datetimestamp` varchar(20) NOT NULL default '',
					`user_id` mediumint(8) NOT NULL default '0',
					`type` tinyint(1) NOT NULL default '1',
					`visible` tinyint(1) NOT NULL default '1',
					`allow_comments` tinyint(1) NOT NULL default '1',
					`show_digg_button` tinyint(1) NOT NULL default '1',
					`show_reddit_button` tinyint(1) NOT NULL default '1',
					`show_delicious_button` tinyint(1) NOT NULL default '1',
					`show_slashdot_button` tinyint(1) NOT NULL default '1',
					PRIMARY KEY  (`id`)
					) TYPE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "pages");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "menus` (
				  `id` mediumint(8) NOT NULL auto_increment,
				  `title` varchar(200) NOT NULL default '',
				  `link` varchar(255) NOT NULL default '',
				  `type` tinyint(1) NOT NULL default '1',
				  `order` mediumint(8) NOT NULL default '0',
					PRIMARY KEY  (`id`)
					) TYPE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "menus");
	
			$sql = "CREATE TABLE `" . DBTABLEPREFIX . "modules` (
				  `id` mediumint(8) NOT NULL auto_increment,
				  `name` varchar(200) NOT NULL default '',
				  `active` tinyint(1) NOT NULL default '1',
					PRIMARY KEY  (`id`)
					) TYPE=MyISAM AUTO_INCREMENT=1 ;";
			$result = mysql_query($sql);
			checkresult($result, $sql, "modules");
	
			$sql = "CREATE TABLE `" . USERSDBTABLEPREFIX . "users` (
					  id mediumint(8) NOT NULL auto_increment,
					  username varchar(255) NOT NULL default '',
					  password varchar(255) NOT NULL default '',
					  first_name varchar(50) NOT NULL default '',
					  last_name varchar(50) NOT NULL default '',
					  email_address varchar(255) NOT NULL default '',
					  website varchar(255) NOT NULL default '',
					  signup_date int(11) default NULL,
					  notes text NOT NULL,
					  tech tinyint(1) NOT NULL default '0',
					  user_level tinyint(1) NOT NULL default '0',
					  active tinyint(1) NOT NULL default '1',
					  language varchar(5) NOT NULL default 'en',
					  PRIMARY KEY  (id)
					) TYPE=MyISAM AUTO_INCREMENT=2 ;";
		    $result = mysql_query($sql);
		    checkresult($result, $sql, "users"); 
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_site_title', 'Fast Track Sites BTS');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert1");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_active', '1');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert2");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_inactive_msg', 'Sorry but our system is currently down, please check back later.');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert3");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_theme', 'default');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert4");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_cookie_name', 'ftsbts');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert5");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_site_tagline', 'Making the internet affordable');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert6");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_registered_users_comment', '0');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert7");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_per_page', '8');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert8");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_default_editor', '2');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert9");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_default_allow_comments', '1');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert10");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_default_show_digg_button', '1');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert11");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_default_show_reddit_button', '1');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert12");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_default_show_delicious_button', '1');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert13");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_default_show_slashdot_button', '1');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert14");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_default_on_main_page', '1');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert15");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "config` VALUES ('ftsbts_time_zone', '-6.00');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "configinsert16");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "entries` VALUES (1, 1, 'Welcome!', 'Thank you for using the Fast Track Sites Blogging Technology System, if you have any questions, please feel free to contact us at sales@fasttracksites.com.', '', '', '$currentdate', 1, 1, 1, 1, 1, 1, 1, 1);";
			$result = mysql_query($sql);
			checkresult($result, $sql, "entriesinsert1");
	
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "categories` VALUES (1, 'Uncategorized');";
			$result = mysql_query($sql);
			checkresult($result, $sql, "categoriesinsert1");	
		
			// Print this page
			$page_content = "
					<h2>Insert Table Results</h2>";
			
			if ($totalfailure == 1) {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create database tables.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created database tables.</span></span>";
			}
			
			$page_content .= "
					<br /><br />
					<h2><span class=\"iconText38px\"><img src=\"themes/installer/icons/addUser_38px.png\" alt=\"Add User\" /></span> <span class=\"iconText38px\">Create Your Admin Account</span></h2>
					Please enter your admin user information below:
					<form id=\"adminAccountForm\" action=\"install.php?step=5\" method=\"post\">
						<label for=\"usrUsername\">Username</label> <input type=\"text\" name=\"usrUsername\" id=\"usrUsername\" class=\"required validate-alphanum\" />
						<label for=\"usrEmailAddress\">Email Address</label> <input type=\"text\" name=\"usrEmailAddress\" id=\"usrEmailAddress\" class=\"required validate-email\" />
						<label for=\"usrPassword\">Password</label> <input type=\"password\" name=\"usrPassword\" id=\"usrPassword\" class=\"required validate-password\" />
						<label for=\"usrConfirmPassword\">Confirm Password</label> <input type=\"password\" name=\"usrConfirmPassword\" id=\"usrConfirmPassword\" class=\"required validate-password-confirm\" />
						<input type=\"submit\" name=\"submit\" class=\"button\" value=\"Next\" />
					</form>
					<script type=\"text/javascript\">
						var valid = new Validation('adminAccountForm', {immediate : true, useTitles:true});
						Validation.addAllThese([
								['validate-password', 'Your password must be at least 7 characters and cannot be your username, the word password, 1234567, or 0123456.', {
								minLength : 7,
								notOneOf : ['password','PASSWORD','1234567','0123456'],
								notEqualToField : 'usrUsername'
							}],
							['validate-password-confirm', 'Your passwords do not match.', {
								equalToField : 'usrPassword'
							}]
						]);
					</script>";
			break;
		case 5:
			$page->setTemplateVar("PageTitle", INSTALLER_SCRIPT_NAME . " Step 5 - Finish");	
			
			include('_db.php');
				
	    	// Create our admin account
			$usrUsername = keepsafe($_POST['usrUsername']); 
			$usrPassword = md5(keepsafe($_POST['usrPassword']));
			$usrEmailAddress = keepsafe($_POST['usrEmailAddress']);
		
	    	$sql = "INSERT INTO `" . USERSDBTABLEPREFIX . "users` (`username`, `password`, `email_address`, `signup_date`, `notes`, `user_level`) VALUES ('" . $usrUsername . "', '" . $usrPassword . "', '" . $usrEmailAddress . "', '" . time() . "', '', '1');";
	    	$result = mysql_query($sql);
		    checkresult($result, $sql, "AdminUser");
		
			// Print this page
			$page_content = "
					<h2>Create Your Admin Account Results</h2>";
			
			if ($totalfailure == 1) {
				$page_content .= "
					<span class=\"actionFailed\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/delete_20px.png\" alt=\"Action Failed\" /></span> <span class=\"iconText20px\">Unable to create admin account.</span></span>";
			}
			else {
				$page_content .= "
					<span class=\"actionSucceeded\"><span class=\"iconText20px\"><img src=\"themes/installer/icons/check_20px.png\" alt=\"Action Succeeded\" /></span> <span class=\"iconText20px\">Successfully created admin account.</span></span>";
			}
			
			$page_content .= "
					<h2>Finishing Up</h2>
					Installation is now complete, before using the system please make sure and delete this file (install.php) so that it cannot be reused by someone else.
					<br /><br />
					<a href=\"index.php\" class=\"button\">Finish</a>";
			break;	
	}
	
	// Send out the content
	$page->setTemplateVar("PageContent", $page_content);	
	
	include "themes/installer/template.php";
?>