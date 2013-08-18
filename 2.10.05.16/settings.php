<? 
/***************************************************************************
 *                               settings.php
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
	
	if (isset($_POST['submit'])) {
		foreach($_POST as $name => $value) {
			if ($name != "submit"){							
				$sql = "UPDATE `" . DBTABLEPREFIX . "config` SET value = '" . $value . "' WHERE name = '" . $name . "'";
				$result = mysql_query($sql);
				//echo $sql . "<br />";
			}
		}
		
		// Handle checkboxes, unchecked boxes are not posted so we check for this and mark them in the DB as such
		if (!isset($_POST['ftsbts_active'])) {
			$sql = "UPDATE `" . DBTABLEPREFIX . "config` SET value = '0' WHERE name = 'ftsbts_active'";
			$result = mysql_query($sql);
		}
	}
		
	
	$sql = "SELECT * FROM `" . DBTABLEPREFIX . "config`";
	$result = mysql_query($sql);
	
	// This is used to let us get the actual items and not just name and value
	while ($row = mysql_fetch_array($result)) {
		$current_config[$row['name']] = $row['value'];
	}
		
	// Give our template the values
	$content = "<form action=\"" . $menuvar['SETTINGS'] . "\" method=\"post\" target=\"_top\">
					<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
						<tr><td class=\"title1\" colspan=\"2\">Blog Settings</td></tr>
						<tr class=\"row1\">
							<td><strong>Blog Title: </strong></td>
							<td><input type=\"text\" name=\"ftsbts_site_title\" size=\"50\" value=\"" . $current_config['ftsbts_site_title'] . "\" /></td>
						</tr>
						<tr class=\"row2\">
							<td><strong>Blog Tag Line: </strong></td>
							<td><input type=\"text\" name=\"ftsbts_site_tagline\" size=\"50\" value=\"" . $current_config['ftsbts_site_tagline'] . "\" /></td>
						</tr>
						<tr class=\"row1\">
							<td><strong>Posts Shown on Each Page: </strong></td>
							<td><input type=\"text\" name=\"ftsbts_per_page\" size=\"50\" value=\"" . $current_config['ftsbts_per_page'] . "\" /></td>
						</tr>
						<tr class=\"row2\">
							<td><strong>Active: </strong></td>
							<td>
								<input name=\"ftsbts_active\" type=\"checkbox\" value=\"1\"". testChecked($current_config['ftsbts_active'], ACTIVE) . " />
							</td>
						</tr>
						<tr class=\"row1\">
							<td><strong>Only Registered Users Can Post Comments: </strong></td>
							<td>
								" . createDropdown("yesOrNo", "ftsbts_registered_users_comment", $current_config['ftsbts_registered_users_comment'], "") . "
							</td>
						</tr>
						<tr class=\"row2\">
							<td><strong>Inactive Message: </strong></td>
							<td>
								<textarea name=\"ftsbts_inactive_msg\" cols=\"40\" rows=\"10\">" . $current_config['ftsbts_inactive_msg'] . "</textarea>
							</td>
						</tr>
						<tr class=\"row1\">
							<td><strong>Time Zone: </strong></td>
							<td>
								" . createDropdown("timezone", "ftsbts_time_zone", $current_config['ftsbts_time_zone'], "") . "
							</td>
						</tr>
						<tr><td class=\"title1\" colspan=\"2\">Default Entry Settings</td></tr>
						<tr class=\"row1\">
							<td><strong>Default Entry Editor: </strong></td>
							<td>
								" . createDropdown("editorType", "ftsbts_default_editor", $current_config['ftsbts_default_editor'], "") . "
							</td>
						</tr>
						<tr class=\"row2\">
							<td><strong>Allow Comments: </strong></td>
							<td>
								" . createDropdown("allowComments", "ftsbts_default_allow_comments", $current_config['ftsbts_default_allow_comments'], "") . "
							</td>
						</tr>
						<tr class=\"row1\">
							<td><strong>Show Digg Button: </strong></td>
							<td>
								" . createDropdown("yesOrNo", "ftsbts_default_show_digg_button", $current_config['ftsbts_default_show_digg_button'], "") . "
							</td>
						</tr>
						<tr class=\"row2\">
							<td><strong>Show Reddit Button: </strong></td>
							<td>
								" . createDropdown("yesOrNo", "ftsbts_default_show_reddit_button", $current_config['ftsbts_default_show_reddit_button'], "") . "
							</td>
						</tr>
						<tr class=\"row1\">
							<td><strong>Show Del.icio.us Button: </strong></td>
							<td>
								" . createDropdown("yesOrNo", "ftsbts_default_show_delicious_button", $current_config['ftsbts_default_show_delicious_button'], "") . "
							</td>
						</tr>
						<tr class=\"row2\">
							<td><strong>Show Slashdot Button: </strong></td>
							<td>
								" . createDropdown("yesOrNo", "ftsbts_default_show_slashdot_button", $current_config['ftsbts_default_show_slashdot_button'], "") . "
							</td>
						</tr>
						<tr class=\"row1\">
							<td><strong>Entry is Available on Main Page: </strong></td>
							<td>
								" . createDropdown("visible", "ftsbts_default_on_main_page", $current_config['ftsbts_default_on_main_page'], "") . "
							</td>
						</tr>
					</table>
					<br />
					<center><input type=\"submit\" name=\"submit\" value=\"Update Settings\" /></center>
				</form>";

	$page->setTemplateVar('PageContent', $content);
}
else {
	$page->setTemplateVar('PageContent', "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>