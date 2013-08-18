<? 
/***************************************************************************
 *                               modules.php
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

if ($_SESSION['user_level'] == ADMIN || $_SESSION['user_level'] == MOD) {
	//==================================================
	// Handle editing, adding, and deleting of pages
	//==================================================	
	if (isset($_POST['submit'])) {
		$errorCount = 0;
		
		// Since we cannot handle turning off modules we must reset all modules and then activate them one at a time
		$sql = "UPDATE `" . DBTABLEPREFIX . "modules` SET active='" . INACTIVE . "'";
		$result = mysql_query($sql);
		
		if (isset($_POST['modulesActive'])) {
			foreach ($_POST['modulesActive'] as $moduleName => $moduleActive) {			
				$sql = "SELECT * FROM `" . DBTABLEPREFIX . "modules` WHERE name='" . keepsafe($moduleName) . "'";
				$result = mysql_query($sql);
		
				if ($result && mysql_num_rows($result) != 0) {
					$sql2 = "UPDATE `" . DBTABLEPREFIX . "modules` SET active='" . keepsafe($moduleActive) . "' WHERE name='" . keepsafe($moduleName) . "'";
					$result2 = mysql_query($sql2);
					if (!$result2) { $errorCount++; }
				
					mysql_free_result($result);
				}
				else {
					$sql2 = "INSERT INTO `" . DBTABLEPREFIX . "modules` (active, name) VALUES ('" . keepsafe($moduleActive) . "', '" . keepsafe($moduleName) . "')";
					$result2 = mysql_query($sql2);
					if (!$result2) { $errorCount++; }
				}			
			}
		}
		
		if ($errorCount == 0) {
			$content = "
						<center>Your modules have been successfully updated.</center>
						<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['MODULES'] . "\">";
		}
		else {
			$content = "
						<center>There was an error while attempting to update your modules.</center>
						<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['MODULES'] . "\">";
		}		
	}		
	else {	
		//==================================================
		// Get the current theme
		//==================================================
		$currentlyActivatedModules = array();
		
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "modules` WHERE active='" . ACTIVE . "'";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) != 0) {
			while ($row = mysql_fetch_array($result)) {
				$currentlyActivatedModules[] = $row['name'];
			}
			mysql_free_result($result);
		}
		
		$x = 1; //reset the variable we use for our row colors	
		
		//==================================================
		// Get and store our available themes
		//==================================================		
		$modulespath = "modules";
		if($dir = opendir($modulespath)){					
			$sub_dir_names = array();
			while (false !== ($file = readdir($dir))) {				
				if ($file != "." && $file != ".." && is_dir($modulespath . '/' . $file)) {
					$sub_dir_names[$file] .= '';
				}
			}			
		}		
		ksort($sub_dir_names); //sort by name
			
		//==================================================
		// Print our table
		//==================================================
		$content = "<form name=\"themechanger\" action=\"" . $menuvar['MODULES'] . "\" method=\"post\">
						<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"4\">Available Modules</td>
							</tr>							
							<tr class=\"title2\">
								<td><strong>Name</strong></td><td><strong>Version</strong></td><td><strong>Author</strong></td><td><strong>Active</strong></td>
							</tr>";			
			
		foreach($sub_dir_names as $name => $nothing) { 			
			$MODULE_NAME = "N/A"; // Reset variable		
			$MODULE_VERSION = "N/A"; // Reset variable
			$MODULE_AUTHOR = "N/A"; // Reset variable
			
			if (file_exists($modulespath . '/' . $name . '/moduledetails.php')) { include ($modulespath . '/' . $name . '/moduledetails.php'); }
			
			$content .=			"<tr class=\"row" . $x . "\">
									<td width=\"40%\">" . $MODULE_NAME . "</td>
									<td width=\"30%\">" . $MODULE_VERSION . "</td>
									<td width=\"30%\">" . $MODULE_AUTHOR . "</td>
									<td width=\"10%\"><center><input name=\"modulesActive[" . $name . "]\" type=\"checkbox\" value=\"" . ACTIVE . "\"" . testChecked(1, in_array($name, $currentlyActivatedModules)) . " /></center></td>
								</tr>";
								
			$x = ($x==2) ? 1 : 2;					
		}		
		$content .=	"	</table>
					<br />
					<center><input name=\"submit\" type=\"submit\" value=\"Update\" /></center>
				</form>";
	}
	$page->setTemplateVar('PageContent', $content);
}
else {
	$page->setTemplateVar('PageContent', "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>