<? 
/***************************************************************************
 *                               users.php
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

if ($_SESSION['user_level'] == ADMIN) {
	//==================================================
	// Handle editing, adding, and deleting of users
	//==================================================	
	if ($actual_action == "newuser") {
		if (isset($_POST['submit'])) {
			if ($_POST['password'] == $_POST['password2']) {
				$password = md5(keepsafe($_POST['password']));
								
				$sql = "INSERT INTO `" . USERSDBTABLEPREFIX . "users` (`username`, `password`, `email_address`, `user_level`, `first_name`, `last_name`, `website`) VALUES ('" . keepsafe($_POST['username']) . "', '" . $password . "', '" . keepsafe($_POST['emailaddress']) . "', '" . keepsafe($_POST['userlevel']) . "', '" . keeptasafe($_POST['first_name']) . "', '" . keeptasafe($_POST['last_name']) . "', '" . keepsafe($_POST['website']) . "')";
				$result = mysql_query($sql);
				
				if ($result) {
					$content = "<center>Your new user has been added, and you are being redirected to the main page.</center>
								<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['USERS'] . "\">";
				}
				else {
					$content = "<center>There was an error while creating your new user. You are being redirected to the main page.</center>
								<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['USERS'] . "\">";						
				}
			}
			else {
				$content = "<center>The passwords you supplied do not match. You are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['USERS'] . "\">";			
			}
		}
		else {
			$content .= "
						<form name=\"newuserform\" action=\"" . $menuvar['USERS'] . "&amp;action=newuser\" method=\"post\">
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\" colspan=\"2\">Add A New User</td>
								</tr>
								<tr class=\"row1\">
									<td><strong>First Name:</strong></td><td><input name=\"first_name\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Last Name:</strong></td><td><input name=\"last_name\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row1\">
									<td><strong>Username:</strong></td><td><input name=\"username\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Password:</strong></td><td><input name=\"password\" type=\"password\" size=\"60\" /></td>
								</tr>
								<tr class=\"row1\">
									<td><strong>Confirm Password:</strong></td><td><input name=\"password2\" type=\"password\" size=\"60\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Email Address:</strong></td><td><input name=\"emailaddress\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Website:</strong></td><td><input name=\"website\" type=\"text\" size=\"60\" /></td>
								</tr>
								<tr class=\"row1\">
									<td><strong>User Level:</strong></td><td>
										<select name=\"userlevel\" class=\"settingsDropDown\">
											<option value=\"" . BANNED . "\">Banned</option>
											<option value=\"" . USER . "\">User</option>
											<option value=\"" . MOD . "\">Moderator</option>
											<option value=\"" . ADMIN . "\">Administrator</option>
										</select>
									</td>
								</tr>
							</table>									
							<br />
							<center><input type=\"submit\" name=\"submit\" value=\"Add User\" /></center>
						</form>";
		}
	}	
	elseif ($actual_action == "edituser" && isset($actual_id)) {
		if (isset($_POST['submit'])) {
			if ($_POST['password'] != "") {
				if ($_POST['password'] == $_POST['password2']) {
					$password = md5(keepsafe($_POST['password']));								

					$sql = "UPDATE `" . USERSDBTABLEPREFIX . "users` SET username = '" . keepsafe($_POST['username']) . "', password = '" . $password . "', email_address = '" . keepsafe($_POST['emailaddress']) . "', user_level = '" . keepsafe($_POST['userlevel']) . "', first_name = '" . keeptasafe($_POST['first_name']) . "', last_name = '" . keeptasafe($_POST['last_name']) . "', website = '" . keepsafe($_POST['website']) . "' WHERE id = '" . $actual_id . "'";
				}
				else {
					$content = "<center>The passwords you supplied do not match. You are being redirected to the main page.</center>
								<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['USERS'] . "\">";			
				}
			}
			else {
					$sql = "UPDATE `" . USERSDBTABLEPREFIX . "users` SET username = '" . keepsafe($_POST['username']) . "', email_address = '" . keepsafe($_POST['emailaddress']) . "', user_level = '" . keepsafe($_POST['userlevel']) . "', first_name = '" . keeptasafe($_POST['first_name']) . "', last_name = '" . keeptasafe($_POST['last_name']) . "', website = '" . keepsafe($_POST['website']) . "' WHERE id = '" . $actual_id . "'";
			}
			$result = mysql_query($sql);
			
			if ($result) {
				$content = "<center>Your user's details have been updated, and you are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['USERS'] . "\">";
			}
			else {
				$content = "<center>There was an error while updating your user's details. You are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['USERS'] . "\">";						
			}
		}
		else {
			$sql = "SELECT * FROM `" . USERSDBTABLEPREFIX . "users` WHERE id = '$actual_id' LIMIT 1";
			$result = mysql_query($sql);
			
			if (mysql_num_rows($result) == 0) {
				$content = "<center>There was an error while accessing the user's details you are trying to update. You are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['USERS'] . "\">";	
			}
			else {
				$row = mysql_fetch_array($result);
				
				function testlevel($currentlevel, $testinglevel) {
					$selected = ($currentlevel == $testinglevel) ? " selected=\"selected\"" : "";
					
					return $selected;
				}
				
				$content .= "
							<form name=\"newpageform\" action=\"" . $menuvar['USERS'] . "&amp;action=edituser&amp;id=" . $row['id'] . "\" method=\"post\">
								<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
									<tr>
										<td class=\"title1\" colspan=\"2\">Edit User's Details</td>
									</tr>
									<tr class=\"row1\">
										<td><strong>First Name:</strong></td><td><input name=\"first_name\" type=\"text\" size=\"60\" value=\"" . $row['first_name'] . "\" /></td>
									</tr>
									<tr class=\"row2\">
										<td><strong>Last Name:</strong></td><td><input name=\"last_name\" type=\"text\" size=\"60\" value=\"" . $row['last_name'] . "\" /></td>
									</tr>
									<tr class=\"row1\">
										<td><strong>Username:</strong></td><td><input name=\"username\" type=\"text\" size=\"60\" value=\"" . $row['username'] . "\" /></td>
									</tr>
									<tr class=\"row2\">
										<td><strong>New Password:</strong></td><td><input name=\"password\" type=\"password\" size=\"60\" /></td>
									</tr>
									<tr class=\"row1\">
										<td><strong>Confirm Password:</strong></td><td><input name=\"password2\" type=\"password\" size=\"60\" /></td>
									</tr>
									<tr class=\"row2\">
										<td><strong>Email Address:</strong></td><td><input name=\"emailaddress\" type=\"text\" size=\"60\" value=\"" . $row['email_address'] . "\" /></td>
									</tr>
									<tr class=\"row2\">
										<td><strong>Website:</strong></td><td><input name=\"website\" type=\"text\" size=\"60\" value=\"" . $row['website'] . "\" /></td>
									</tr>
									<tr class=\"row1\">
										<td><strong>User Level:</strong></td><td>
											<select name=\"userlevel\" class=\"settingsDropDown\">
												<option value=\"" . BANNED . "\"" . testlevel($row['user_level'], BANNED) . ">Banned</option>
												<option value=\"" . USER . "\"" . testlevel($row['user_level'], USER) . ">User</option>
												<option value=\"" . MOD . "\"" . testlevel($row['user_level'], MOD) . ">Moderator</option>
												<option value=\"" . ADMIN . "\"" . testlevel($row['user_level'], ADMIN) . ">Administrator</option>
											</select>
										</td>
									</tr>
								</table>									
								<br />
								<center><input type=\"submit\" name=\"submit\" value=\"Update User's Details\" /></center>
							</form>";							
			}			
		}
	}
	else {
		if ($actual_action == "deleteuser") {
			$sql = "DELETE FROM `" . USERSDBTABLEPREFIX . "users` WHERE id='" . $actual_id . "' LIMIT 1";
			$result = mysql_query($sql);
		}		
		
		//==================================================
		// Print out our users table
		//==================================================
		$sql = "SELECT * FROM `" . USERSDBTABLEPREFIX . "users` ORDER BY username ASC";
		$result = mysql_query($sql);
		
		$x = 1; //reset the variable we use for our row colors	
		
		$content = "<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"4\">
									<div style='float: right;'><a href=\"" . $menuvar['USERS'] . "&amp;action=newuser\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/add.png\" alt=\"Add a new user\" /></a></div>
									Current Users
								</td>
							</tr>							
							<tr class=\"title2\">
								<td><strong>Username</strong></td><td><strong>Full Name</strong></td><td><strong>User Level</strong></td><td></td>
							</tr>";
							
		while ($row = mysql_fetch_array($result)) {
			$level = ($row['user_level'] == ADMIN) ? "Administrator" : "Moderator";
			$level = ($row['user_level'] == USER) ? "User" : $level;
			$level = ($row['user_level'] == BANNED) ? "Banned" : $level;
			
			$content .=			"<tr class=\"row" . $x . "\">
									<td>" . $row['username'] . "</td>
									<td>" . $row['last_name'] . ", " . $row['first_name'] . "</td>
									<td>" . $level . "</td>
									<td>
										<center><a href=\"" . $menuvar['USERS'] . "&amp;action=edituser&amp;id=" . $row['id'] . "\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/check.png\" alt=\"Edit User Details\" /></a> <a href=\"" . $menuvar['USERS'] . "&amp;action=deleteuser&amp;id=" . $row['id'] . "\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/delete.png\" alt=\"Delete User\" /></a></center>
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