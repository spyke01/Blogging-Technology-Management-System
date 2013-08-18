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
								<select name=\"ftsbts_active\" class=\"settingsDropDown\">
									<option value=\"". ACTIVE . "\"" . testSelected($current_config['ftsbts_active'], ACTIVE) . ">Active</option>
									<option value=\"". INACTIVE . "\"" . testSelected($current_config['ftsbts_active'], INACTIVE) . ">Inactive</option>
								</select>
							</td>
						</tr>
						<tr class=\"row1\">
							<td><strong>Only Registered Users Can Post Comments: </strong></td>
							<td>
								<select name=\"ftsbts_registered_users_comment\" class=\"settingsDropDown\">
									<option value=\"". ACTIVE . "\"" . testSelected($current_config['ftsbts_registered_users_comment'], ACTIVE) . ">Active</option>
									<option value=\"". INACTIVE . "\"" . testSelected($current_config['ftsbts_registered_users_comment'], INACTIVE) . ">Inactive</option>
								</select>
							</td>
						</tr>
						<tr class=\"row2\">
							<td><strong>Inactive Message: </strong></td>
							<td>
								<textarea name=\"ftsbts_inactive_msg\" cols=\"40\" rows=\"10\">" . $current_config['ftsbts_inactive_msg'] . "</textarea>
							</td>
						</tr>
						<tr><td class=\"title1\" colspan=\"2\">Default Entry Settings</td></tr>
						<tr class=\"row1\">
							<td><strong>Default Entry Editor: </strong></td>
							<td>
								<select name=\"ftsbts_default_editor\" class=\"settingsDropDown\">
									<option value=\"". PAGE_BBCODE . "\"" . testSelected($current_config['ftsbts_default_editor'], PAGE_BBCODE) . ">BBCode</option>
									<option value=\"". PAGE_WYSIWYG . "\"" . testSelected($current_config['ftsbts_default_editor'], PAGE_WYSIWYG) . ">WYSIWYG</option>
								</select>
							</td>
						</tr>
						<tr class=\"row2\">
							<td><strong>Allow Comments: </strong></td>
							<td>
								<select name=\"ftsbts_default_allow_comments\" class=\"settingsDropDown\">
									<option value=\"". ALLOW_COMMENTS . "\"" . testSelected($current_config['ftsbts_default_allow_comments'], ALLOW_COMMENTS) . ">Yes</option>
									<option value=\"". DO_NOT_ALLOW_COMMENTS . "\"" . testSelected($current_config['ftsbts_default_allow_comments'], DO_NOT_ALLOW_COMMENTS) . ">No</option>
								</select>
							</td>
						</tr>
						<tr class=\"row1\">
							<td><strong>Show Digg Button: </strong></td>
							<td>
								<select name=\"ftsbts_default_show_digg_button\" class=\"settingsDropDown\">
									<option value=\"". ACTIVE . "\"" . testSelected($current_config['ftsbts_default_show_digg_button'], ACTIVE) . ">Yes</option>
									<option value=\"". INACTIVE . "\"" . testSelected($current_config['ftsbts_default_show_digg_button'], INACTIVE) . ">No</option>
								</select>
							</td>
						</tr>
						<tr class=\"row2\">
							<td><strong>Show Reddit Button: </strong></td>
							<td>
								<select name=\"ftsbts_default_show_reddit_button\" class=\"settingsDropDown\">
									<option value=\"". ACTIVE . "\"" . testSelected($current_config['ftsbts_default_show_reddit_button'], ACTIVE) . ">Yes</option>
									<option value=\"". INACTIVE . "\"" . testSelected($current_config['ftsbts_default_show_reddit_button'], INACTIVE) . ">No</option>
								</select>
							</td>
						</tr>
						<tr class=\"row1\">
							<td><strong>Show Del.icio.us Button: </strong></td>
							<td>
								<select name=\"ftsbts_default_show_delicious_button\" class=\"settingsDropDown\">
									<option value=\"". ACTIVE . "\"" . testSelected($current_config['ftsbts_default_show_delicious_button'], ACTIVE) . ">Yes</option>
									<option value=\"". INACTIVE . "\"" . testSelected($current_config['ftsbts_default_show_delicious_button'], INACTIVE) . ">No</option>
								</select>
							</td>
						</tr>
						<tr class=\"row2\">
							<td><strong>Show Slashdot Button: </strong></td>
							<td>
								<select name=\"ftsbts_default_show_slashdot_button\" class=\"settingsDropDown\">
									<option value=\"". ACTIVE . "\"" . testSelected($current_config['ftsbts_default_show_slashdot_button'], ACTIVE) . ">Yes</option>
									<option value=\"". INACTIVE . "\"" . testSelected($current_config['ftsbts_default_show_slashdot_button'], INACTIVE) . ">No</option>
								</select>
							</td>
						</tr>
						<tr class=\"row1\">
							<td><strong>Entry is Available on Main Page: </strong></td>
							<td>
								<select name=\"ftsbts_default_on_main_page\" class=\"settingsDropDown\">
									<option value=\"". VISIBLE . "\"" . testSelected($current_config['ftsbts_default_on_main_page'], VISIBLE) . ">Yes</option>
									<option value=\"". HIDDEN . "\"" . testSelected($current_config['ftsbts_default_on_main_page'], HIDDEN) . ">No</option>
								</select>
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