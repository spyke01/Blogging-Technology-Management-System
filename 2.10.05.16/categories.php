<? 
/***************************************************************************
 *                               categories.php
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
	if ($actual_action == "newcategory") {
		if (isset($_POST['submit'])) {
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "categories` (`name`) VALUES ('" . keeptasafe($_POST['catname']) . "')";
			$result = mysql_query($sql);
				
			if ($result) {
				$content = "
							<center>Your new category has been added, and you are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['CATEGORIES'] . "\">";
			}
			else {
				$content = "
							<center>There was an error while creating your new category. You are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['CATEGORIES'] . "\">";						
			}
		}
		else {
			$content .= "
						<form name=\"newcategoryform\" action=\"" . $menuvar['CATEGORIES'] . "&amp;action=newcategory\" method=\"post\">
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\" colspan=\"2\">New Category Information</td>
								</tr>
								<tr class=\"row1\">
									<td><strong>Category Name:</strong></td><td><input name=\"catname\" type=\"text\" size=\"60\" /></td>
								</tr>
							</table>									
							<br />
							<center><input type=\"submit\" name=\"submit\" value=\"Create New Category!\" /></center>
						</form>";	
		}
	}	
	elseif ($actual_action == "editcategory" && isset($actual_id)) {
		if (isset($_POST[submit])) {
			$sql = "UPDATE `" . DBTABLEPREFIX . "categories` SET name = '" . keeptasafe($_POST['catname']) . "' WHERE id = '" . $actual_id . "'";
			$result = mysql_query($sql);
			
			if ($result) {
				$content = "
							<center>Your category's details have been updated, and you are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['CATEGORIES'] . "\">";
			}
			else {
				$content = "
							<center>There was an error while updating your category's details. You are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['CATEGORIES'] . "\">";						
			}
		}
		else {
			$sql = "SELECT * FROM `" . DBTABLEPREFIX . "categories` WHERE id = '" . $actual_id . "' LIMIT 1";
			$result = mysql_query($sql);
			
			if (mysql_num_rows($result) == 0) {
				$content = "
							<center>There was an error while accessing the category's details you are trying to update. You are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['CATEGORIES'] . "\">";	
			}
			else {
				$row = mysql_fetch_array($result);
				
				$content .= "
							<form name=\"editcategoryform\" action=\"" . $menuvar['CATEGORIES'] . "&amp;action=editcategory&amp;id=" . $row['id'] . "\" method=\"post\">
								<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
									<tr>
										<td class=\"title1\" colspan=\"2\">Edit Category Information</td>
									</tr>
									<tr class=\"row1\">
										<td><strong>Category Name:</strong></td><td><input name=\"catname\" type=\"text\" size=\"60\" value=\"" . $row['name'] . "\" /></td>
									</tr>
								</table>									
								<br />
								<center><input type=\"submit\" name=\"submit\" value=\"Update Category Information\" /></center>
							</form>";							
			}			
		}
	}
	else {
		if ($actual_action == "deletecategory") {
			$sql = "DELETE FROM `" . DBTABLEPREFIX . "categories` WHERE id='" . $actual_id . "' LIMIT 1";
			$result = mysql_query($sql);
		}		
		
		//==================================================
		// Print out our users table
		//==================================================
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "categories` ORDER BY name ASC";
		$result = mysql_query($sql);
		
		$x = 1; //reset the variable we use for our row colors	
		
		$content = "<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"2\">
									<div style=\"float: right;\"><a href=\"" . $menuvar['CATEGORIES'] . "&amp;action=newcategory\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/add.png\" alt=\"Add a new category\" /></a></div>
									Current Categories
								</td>
							</tr>							
							<tr class=\"title2\">
								<td><strong>Name</strong></td><td></td>
							</tr>";
							
		while ($row = mysql_fetch_array($result)) {
			
			$content .=			"<tr class=\"row" . $x . "\">
									<td>" . $row['name'] . "</td>
									<td>
										<center><a href=\"" . $menuvar['CATEGORIES'] . "&amp;action=editcategory&amp;id=" . $row['id'] . "\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/check.png\" alt=\"Edit User Details\" /></a> <a href=\"" . $menuvar['CATEGORIES'] . "&amp;action=deletecategory&amp;id=" . $row['id'] . "\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/delete.png\" alt=\"Delete User\" /></a></center>
									</td>
								</tr>";
			$x = ($x==2) ? 1 : 2;
		}
		mysql_free_result($result);
		
	
		$content .=		"</table>";
	}
	$page->setTemplateVar("PageContent", $content);
}
else {
	$page->setTemplateVar("PageContent", "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>