<? 
/***************************************************************************
 *                               index.php
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
// If the db connection file is missing we should redirect the user to install page
if (!file_exists('_db.php')) {
	header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/install.php");	
	exit();
}

include 'includes/header.php';

// Set which modules we are adding in
//$modulesArray = explode(",", str_replace(" ", "", $bts_config['ftsbts_modules']));
$modulesArray = array();
$sql = "SELECT * FROM `" . DBTABLEPREFIX . "modules` WHERE active='" . ACTIVE . "'";
$result = mysql_query($sql);
		
if ($result && mysql_num_rows($result) != 0) {
	while ($row = mysql_fetch_array($result)) {
		$modulesArray[] = $row['name'];
	}
	mysql_free_result($result);
}

$requested_page_id = $_GET['p'];
$requested_section = $_GET['s'];
$requested_module = $_GET['module'];
$requested_file = $_GET['file'];
$requested_id = $_GET['id'];
$requested_page = $_GET['page'];
$requested_action = $_GET['action'];
$requested_style = $_GET['style'];

$actual_page_id = ($requested_page_id == "" || !isset($requested_page_id)) ? 1 : $requested_page_id;
$actual_page_id = parseurl($actual_page_id);
$actual_section = parseurl($requested_section);
$actual_module = parseurl($requested_module);
$actual_file = parseurl($requested_file);
$actual_id = parseurl($requested_id);
$actual_page = parseurl($requested_page);
$actual_page = (trim($actual_page) == "") ? "1" : $actual_page;
$actual_action = parseurl($requested_action);
$actual_style = parseurl($requested_style);
$page_content = "";

// Warn the user if the install.php script is present
if (file_exists('install.php')) {
	$page_content = "<div class=\"errorMessage\">Warning: install.php is present, please remove this file for security reasons.</div>";
}
if (file_exists('update.php')) {
	$page_content = "<div class=\"errorMessage\">Warning: update.php is present, please remove this file for security reasons.</div>";
}

// We want to show all of our menus by default
$page->setTemplateVar("sidebar_active", ACTIVE);

//========================================
// Logout Function
//========================================
// Prevent spanning between apps to avoid a user getting more acces that they are allowed
if ($_SESSION['script_locale'] != rtrim(dirname($_SERVER['PHP_SELF']), '/\\') && session_is_registered('userid')) {
	session_destroy();
}

if ($actual_page_id == "logout") {
	define('IN_FTSBTS', true);
	include '_db.php';
	include_once ('includes/menu.php');
	include_once ('config.php');
	global $bts_config;
	
	//Destroy Session Cookie
	$cookiename = $bts_config['ftsbts_cookie_name'];
	setcookie($cookiename, false, time()-2592000); //set cookie to delete back for 1 month
	
	//Destroy Session
	session_destroy();
	if(!session_is_registered('first_name')){
		header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/index.php");	
		exit();
	}
}

//Check to see if advanced options are allowed or not
if (version_functions("advancedOptions") == true) {
	// If the system is locked, then only a moderator or admin should be able to view it
	if ($_SESSION['user_level'] != ADMIN && $_SESSION['user_level'] != MOD && $bts_config['ftsbts_active'] != ACTIVE) {
		if ($actual_page_id == "login") {
			include 'login.php';
		}
		else {	
			$page->setTemplateVar("PageTitle", "Currently Disabled");
			$page->setTemplateVar("PageContent", bbcode($bts_config['ftsbts_inactive_msg']));
			$page->makeMenuItem("options", "Login", "index.php?p=login");
		}
	}
	else {
		//========================================
		// Admin panel options
		//========================================
		if ($actual_page_id == "admin") {
			if (!$_SESSION['username']) { include 'login.php'; }
			else {
				if ($_SESSION['user_level'] == MOD || $_SESSION['user_level'] == ADMIN) {
					if ($actual_section == "" || !isset($actual_section)) {
						include 'admin.php'; 
						$page->setTemplateVar("PageTitle", "Admin Panel");
					}
					elseif ($actual_section == "settings") {
						include 'settings.php';				
						$page->setTemplateVar("PageTitle", "Settings");
					}
					elseif ($actual_section == "menus") {
						include 'menus.php';		
						$page->setTemplateVar("PageTitle", "Menus");		
					}
					elseif ($actual_section == "categories") {
						include 'categories.php';		
						$page->setTemplateVar("PageTitle", "Categories");		
					}
					elseif ($actual_section == "entries") {
						include 'entries.php';				
						$page->setTemplateVar("PageTitle", "Entries");
					}
					elseif ($actual_section == "themes") {
						include 'themes.php';			
						$page->setTemplateVar("PageTitle", "Themes");	
					}
					elseif ($actual_section == "users") {
						include 'users.php';			
						$page->setTemplateVar("PageTitle", "Users");	
					}
					elseif ($actual_section == "modules") {
						include 'modules.php';			
						$page->setTemplateVar("PageTitle", "Modules");	
					}
					elseif ($actual_section == "module") {
						// Only allow us to view active modules
						if (in_array($actual_module, $modulesArray)) {
							if (file_exists('modules/' . $actual_module . '/' . $actual_file . '.php')) { include ('modules/' . $actual_module . '/' . $actual_file . '.php'); }
							else { include '404.php'; }
						}
						else { include '404.php'; }
					}
				}
				else { setTemplateVar("PageContent", "You are not authorized to access the admin panel."); }
			}
		}
		elseif ($actual_page_id == "createrss") {
			include 'createrss.php';
		}
		elseif ($actual_page_id == "login") {
			include 'login.php';
		}
		elseif ($actual_page_id == "register") {
			include 'register.php';
		}
		elseif ($actual_page_id == "version") {
			$page->setTemplateVar("PageTitle", "Version Information");	
			
			include('_license.php');
		
			$page_content .= "
				<div class=\"roundedBox\">
					<h1>Version Information</h1>
					<strong>Application:</strong> " . A_NAME . "<br />
					<strong>Version:</strong> " . A_VERSION . "<br />
					<strong>Registered to:</strong> " . $A_Licensed_To . "<br />
					<strong>Serial:</strong> " . $A_License . "
				</div>";
			
			$page->setTemplateVar("PageContent", $page_content);	
		}
		elseif ($actual_page_id == "viewentry" && $actual_id != "") {
			include 'viewentry.php';
			
			// We only want to see the options menu while in the admin panel
			$page->setTemplateVar("options_active", ACTIVE);
			$page->setTemplateVar("categories_active", ACTIVE);
			$page->setTemplateVar("stayupdated_active", ACTIVE);		
		}
		else {
			//=================================================
			// If not in admin section, then print out our 
			// menus from the database
			//=================================================
			$limit = " LIMIT " . (($actual_page * $bts_config['ftsbts_per_page'])  - $bts_config['ftsbts_per_page']) . " , " . $bts_config['ftsbts_per_page'];
			// echo $actual_page . "<br />" . $bts_config['ftsbts_per_page'] . "<br />" . $limit;
			
			$sql = ($actual_page_id == "viewcategory" && $actual_id != "") ? "SELECT * FROM `" . DBTABLEPREFIX . "entries` WHERE cat_id = '" . $actual_id . "' ORDER BY datetimestamp DESC" . $limit : "SELECT * FROM `" . DBTABLEPREFIX . "entries` WHERE  visible = '" . VISIBLE . "' ORDER BY datetimestamp DESC" . $limit;
			$result = mysql_query($sql);
			
			if (!$result || mysql_num_rows($result) == 0) { include '404.php'; }
			else {
				//================================================
				// Get Page Info
				//================================================			
				while ($row = mysql_fetch_array($result)) {
					$sql2 = "SELECT entry_id FROM `" . DBTABLEPREFIX . "comments` WHERE entry_id = '" . $row['id'] . "'";
					$result2 = mysql_query($sql2);
					$numOfComments = mysql_num_rows($result2);
					mysql_free_result($result2);
				
					$page_content .= "\n				<div class=\"entry\">
															<h3 class=\"entrytitle\">$row[title]</h3>";
					if ($row['password'] != "") {
						$page_content .= "\n					<div class=\"entrycontent\">
																	This entry is password protected, to view this entry please type in the password.<br /><br />
																	<form name=\"passwordForm\" action=\"" . $menuvar['VIEWENTRY'] . "&id=" . $row['id'] . "\" method=\"post\">
																		<label for=\"entrypassword\">Password </label> <input name=\"entrypassword\" type=\"password\" />
																		<input name=\"submit\" type=\"submit\" class=\"button\" value=\"View Entry\" />
																	</form>
																</div>";
					}
					else {
						$page_content .= "
										<p class=\"entrydate\">
											<span class=\"posted\">Posted On: </span>
											<span class=\"month\">" . makeMonth($row['datetimestamp']) . "</span>
											<span class=\"day\">" . makeDay($row['datetimestamp']) . "<span class=\"comma\">,</span></span>
											<span class=\"year\">" . makeYear($row['datetimestamp']) . "</span>
		  								</p>
										<div class=\"entrycontent\">
											" . (($row['type'] == PAGE_BBCODE) ? bbcode($row['content']) : $row['content']) . "
											<br /><br />
											" . (($row['show_digg_button'] == ACTIVE) ? diggButton("", "", "", "", true, $row['id']) : "") . "
											" . (($row['show_reddit_button'] == ACTIVE) ? redditButton("", "Submit to Reddit", "", $row['id']) : "") . "
											" . (($row['show_delicious_button'] == ACTIVE) ? deliciousButton("", "Submit to Del.icio.us", "", $row['id']) : "") . "
											" . (($row['show_slashdot_button'] == ACTIVE) ? slashdotButton("", "Submit to Slashdot", "", $row['id']) : "") . "
										</div>
										<div class=\"entrydata\">
											<div class=\"entrydata-right\"><a href=\"" . $menuvar['VIEWENTRY'] . "&amp;id=" . $row['id'] . "#comments\" title=\"View Comments\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/images/comments.gif\" alt=\"\" /> Comments(" . $numOfComments . ")</a></div>
											" . (($_SESSION['user_level'] == MOD || $_SESSION['user_level'] == ADMIN) ? "<br/><a href=\"" . $menuvar['ENTRIES'] . "&action=editentry&id=" . $row['id'] . "&style=" . (($row['type'] == PAGE_BBCODE) ? "bbcode" : "wysiwyg") . "\" class=\"editLink\">Edit This Entry</a>" : "") . "
										</div>";
					}
					$page_content .= "\n				</div>
														<br /><br />";
				}
				mysql_free_result($result);
	
				$totalposts = 0;
				$totalpages = 0;
				
				$sql = ($actual_page_id == "viewcategory" && $actual_id != "") ? "SELECT COUNT(*) AS numRows FROM `" . DBTABLEPREFIX . "entries` WHERE cat_id = '" . $actual_id . "' AND visible = '" . VISIBLE . "' ORDER BY datetimestamp DESC" : "SELECT COUNT(*) AS numRows FROM `" . DBTABLEPREFIX . "entries` WHERE  visible = '" . VISIBLE . "' ORDER BY datetimestamp DESC";
				$result = mysql_query($sql);
				
				if ($result && mysql_num_rows($result) > 0) {
					while ($row = mysql_fetch_array($result)) {
						$totalposts = $row['numRows'];
					}
					mysql_free_result($result);
				}
				
				while ($totalposts >= 0) {
					$totalposts = $totalposts - $bts_config['ftsbts_per_page'];
					$totalpages++;
				}
				
				$urlextras = ($actual_page_id == "viewcategory" && $actual_id != "") ? "p=viewcategory&id=" . $actual_id . "&page=" : "page=";
				if ($actual_page != "1") { $page_content .= "\n				<a href=\"index.php?" . $urlextras . ($actual_page - 1) . "\">&lt;&lt; Prev Page</a>"; }
				if ($actual_page != $totalpages) { $page_content .= "\n				<a href=\"index.php?" . $urlextras . ($actual_page + 1) . "\">Next Page &gt;&gt;</a>"; }
				
				$page->setTemplateVar("PageTitle", $bts_config['ftsbts_site_title']);
				$page->setTemplateVar("PageContent", $page_content);		
			}
	
		}
	
		//================================================
		// Get Menus
		//================================================
		
		$page->makeMenuItem("top", "Home", "index.php");	
		
		// Make our Options seperator
		$page->makeMenuItem("sidebar", "Options", "", "title");
		
		// Handle Admin items
		if ($_SESSION['user_level'] == MOD || $_SESSION['user_level'] == ADMIN) {
			if ($actual_page_id == "admin") {
				$page->makeMenuItem("sidebar", "Settings", $menuvar['SETTINGS']);
				$page->makeMenuItem("sidebar", "Entries", $menuvar['ENTRIES']);
				$page->makeMenuItem("sidebar", "Menus", $menuvar['MENUS']);
				$page->makeMenuItem("sidebar", "Categories", $menuvar['CATEGORIES']);
				$page->makeMenuItem("sidebar", "Modules", $menuvar['MODULES']);
				$page->makeMenuItem("sidebar", "Themes", $menuvar['THEMES']);
				$page->makeMenuItem("sidebar", "Users", $menuvar['USERS']);
				
				// Include our module's menus.php file to add in our menu items
				if (count($modulesArray) > 0) {
					foreach($modulesArray as $key => $value) {
						if (file_exists('modules/' . $value . '/menus.php')) { include ('modules/' . $value . '/menus.php'); }
					}
				}
			}
			else {
				$page->makeMenuItem("top", "Admin Panel", $menuvar['ADMIN']);
			
				$page->makeMenuItem("sidebar", "Quick Entry", $menuvar['ENTRIES'] . "&action=newentry&style=" . (($bts_config['ftsbts_default_editor'] == PAGE_BBCODE) ? "bbcode" : "wysiwyg"));
			}
		}
		
		// Handle login/logout and register menu items
		if (!isset($_SESSION['username'])) {
			$page->makeMenuItem("sidebar", "Login", $menuvar['LOGIN']);
			$page->makeMenuItem("sidebar", "Create An Account", $menuvar['REGISTER']);
		}
		else {
			$page->makeMenuItem("sidebar", "Logout", $menuvar['LOGOUT']);
		}
		
		$sql = "SELECT title, link FROM `" . DBTABLEPREFIX . "menus` ORDER BY order";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {	
				$page->makeMenuItem("top", $row['title'], $row['link']);
			}			
			mysql_free_result($result);
		}		
		
		// Make our Categories menu
		$page->makeMenuItem("sidebar", "Categories", "", "title");
		
		// Make category menu items
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "categories` ORDER BY name";
		$result = mysql_query($sql);
				
		if ($result && mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_array($result)) {	
				$sql2 = "SELECT id FROM `" . DBTABLEPREFIX . "entries` WHERE cat_id = '" . $row['id'] . "'";
				$result2 = mysql_query($sql2);
				$numOfEntries = mysql_num_rows($result2);
				mysql_free_result($result2);	
				
				if ($numOfEntries == "0") { $page->makeMenuItem("sidebar", $row['name'] . " (" . $numOfEntries . ")", "index.php"); }
				else { $page->makeMenuItem("sidebar", $row['name'] . " (" . $numOfEntries . ")", "index.php?p=viewcategory&amp;id=" . $row['id']); }
			}
			mysql_free_result($result);
		}
		
		// Make our Categories menu
		$page->makeMenuItem("sidebar", "Stay Updated", "", "title");
		$page->makeMenuItem("sidebar", "RSS Feed", "rss.xml", "feed");
	}
}
else { $page->setTemplateVar("PageContent", version_functions("advancedOptionsText")); }

version_functions("no");
include "themes/" . $bts_config['ftsbts_theme'] . "/template.php";
?>