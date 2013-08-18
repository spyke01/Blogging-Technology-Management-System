<? 
/***************************************************************************
 *                               entries.php
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
	// Handle editing, adding, and deleting of entries
	//==================================================	
	if ($actual_action == "newentry") {
		if (isset($_POST['submit'])) {
			$style = ($actual_style == "bbcode") ? PAGE_BBCODE : PAGE_WYSIWYG;
			$sql = "INSERT INTO `" . DBTABLEPREFIX . "entries` (`title`, `content`, `type`, `cat_id`, `password`, `datetimestamp`, `allow_comments`, `visible`, `show_digg_button`, `show_reddit_button`, `show_delicious_button`, `show_slashdot_button`) VALUES ('" . keeptasafe($_POST['entrytitle']) . "', '" . keeptasafe($_POST['entrycontent']) . "', '" . $style . "', '" . keepsafe($_POST['entrycatid']) . "', '" . keepsafe($_POST['entrypassword']) . "', '" . time() . "', '" . keepsafe($_POST['entryallowcomments']) . "', '" . keepsafe($_POST['entryvisible']) . "', '" . keepsafe($_POST['entryshowdiggbutton']) . "', '" . keepsafe($_POST['entryshowredditbutton']) . "', '" . keepsafe($_POST['entryshowdeliciousbutton']) . "', '" . keepsafe($_POST['entryshowslashdotbutton']) . "')";
			$result = mysql_query($sql);
			
			// Create the RSS Feed file
			include 'createrss.php';
			
			if ($result) {
				$content = "
							<center>Your entry has been added, and you are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['ENTRIES'] . "\">";
			}
			else {
				$content = "
							<center>There was an error while creating your entry. You are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['ENTRIES'] . "\">";						
			}
		}
		else {
			$content .= "
						<form name=\"newentryform\" action=\"" . $menuvar['ENTRIES'] . "&action=newentry&style=" . $actual_style . "\" method=\"post\">
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\" colspan=\"2\">New Entry</td>
								</tr>
								<tr class=\"row2\">
									<td><strong>Title:</strong></td><td><input name=\"entrytitle\" type=\"text\" size=\"60\" /></td>
								</tr>";
			if ($actual_style == "bbcode") { $content .= bbcode_box(); }	
			$content .= "		<tr class=\"row1\">
									<td colspan=\"2\">
										<textarea id=\"entrycontent\" name=\"entrycontent\" class=\"wysiwygbox\" rows=\"10\" cols=\"58\"></textarea>
									</td>
								</tr>
							</table>									
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Category</td>
								</tr>
								<tr class=\"row2\">
									<td class=\"center\">
										<select name=\"entrycatid\" class=\"settingsDropDown\">";
											$sql = "SELECT * FROM `" . DBTABLEPREFIX . "categories` ORDER BY name ASC";
											$result = mysql_query($sql);

											while ($row = mysql_fetch_array($result)) {
												$content .= "\n								<option value=\"" . $row['id'] . "\"" . testSelected("Uncategorized", $row['name']) . ">" . $row['name'] . "</option>";
											}
											
			$content .= "\n							</select>
									</td>
								</tr>
							</table>									
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Allow Comments</td>
								</tr>
								<tr class=\"row2\">
									<td class=\"center\">
										" . createDropdown("allowComments", "entryallowcomments", $current_config['ftsbts_default_allow_comments'], "") . "
									</td>
								</tr>
							</table>								
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Entry is Available on Main Page</td>
								</tr>
								<tr class=\"row2\">
									<td class=\"center\">
										" . createDropdown("visible", "entryvisible", $current_config['ftsbts_default_on_main_page'], "") . "
									</td>
								</tr>
							</table>
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Show Digg Button</td>
								</tr>
								<tr class=\"row2\">
									<td class=\"center\">
										" . createDropdown("yesOrNo", "entryshowdiggbutton", $current_config['ftsbts_default_show_digg_button'], "") . "
									</td>
								</tr>
							</table>
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Show Reddit Button</td>
								</tr>
								<tr class=\"row2\">
									<td class=\"center\">
										" . createDropdown("yesOrNo", "entryshowredditbutton", $current_config['ftsbts_default_show_reddit_button'], "") . "
									</td>
								</tr>
							</table>
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Show Del.icio.us Button</td>
								</tr>
								<tr class=\"row2\">
									<td class=\"center\">
										" . createDropdown("yesOrNo", "entryshowdeliciousbutton", $current_config['ftsbts_default_show_delicious_button'], "") . "
									</td>
								</tr>
							</table>
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Show Slashdot Button</td>
								</tr>
								<tr class=\"row2\">
									<td class=\"center\">
										" . createDropdown("yesOrNo", "entryshowslashdotbutton", $current_config['ftsbts_default_show_slashdot_button'], "") . "
									</td>
								</tr>
							</table>
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Password</td>
								</tr>
								<tr class=\"row2\">
									<td>Leave blank for none</td>
								</tr>
								<tr class=\"row1\">
									<td class=\"center\"><input type=\"text\" name=\"entrypassword\" size=\"60\" /></td>
								</tr>
							</table>									
							<br />
							<center><input type=\"submit\" name=\"submit\" value=\"Create Entry\" /></center>";
			if ($actual_style == "wysiwyg") {
				$content .= "
							<script type=\"text/javascript\">
								tinyMCE.init({
									// General options
									mode : \"textareas\",
									theme : \"advanced\",
									plugins : \"safari,pagebreak,style,layer,table,advhr,advimage,advlink,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template\",

									// Theme options
									theme_advanced_buttons1 : \"bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect\",
									theme_advanced_buttons2 : \"cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor\",
									theme_advanced_buttons3 : \"tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen\",
									theme_advanced_buttons4 : \"insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak\",
									theme_advanced_toolbar_location : \"top\",
									theme_advanced_toolbar_align : \"left\",
									theme_advanced_statusbar_location : \"bottom\",
									theme_advanced_resizing : true,

									// Drop lists for link/image/media/template dialogs
									template_external_list_url : \"lists/template_list.js\",
									external_link_list_url : \"lists/link_list.js\",
									external_image_list_url : \"lists/image_list.js\",
									media_external_list_url : \"lists/media_list.js\"
								});
							</script>";
			}
			else {
				$content .= "	<script type=\"text/javascript\" src=\"javascripts/bbcode.js\"></script>";			
			}
			$content .= "			</form>";
		}
	}	
	elseif ($actual_action == "editentry" && isset($actual_id)) {
		if (isset($_POST['submit'])) {
			$sql = "UPDATE `" . DBTABLEPREFIX . "entries` SET title = '" . keeptasafe($_POST['entrytitle']) . "', content = '" . keeptasafe($_POST['entrycontent']) . "', cat_id = '" . keepsafe($_POST['entrycatid']) . "', password = '" . keepsafe($_POST['entrypassword']) . "', allow_comments = '" . keepsafe($_POST['entryallowcomments']) . "', visible = '" . keepsafe($_POST['entryvisible']) . "', show_digg_button = '" . keepsafe($_POST['entryshowdiggbutton']) . "', show_reddit_button = '" . keepsafe($_POST['entryshowredditbutton']) . "', show_delicious_button = '" . keepsafe($_POST['entryshowdeliciousbutton']) . "', show_slashdot_button = '" . keepsafe($_POST['entryshowslashdotbutton']) . "' WHERE id = '" . $actual_id . "'";
			$result = mysql_query($sql);
			
			// Create the RSS Feed file
			include 'createrss.php';
			
			if ($result) {
				$content = "
							<center>Your entry has been updated, and you are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['ENTRIES'] . "\">";
			}
			else {
				$content = "
							<center>There was an error while updating your entry. You are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['ENTRIES'] . "\">";						
			}
		}
		else {
			$sql = "SELECT * FROM `" . DBTABLEPREFIX . "entries` WHERE id = '" . $actual_id . "' LIMIT 1";
			$result = mysql_query($sql);
			
			if (mysql_num_rows($result) == 0) {
				$content = "
							<center>There was an error while accessing the entry you are trying to update. You are being redirected to the main page.</center>
							<meta http-equiv=\"refresh\" content=\"5;url=" . $menuvar['ENTRIES'] . "\">";	
			}
			else {
				$row = mysql_fetch_array($result);

				$content .= "
							<form name=\"newentryform\" action=\"" . $menuvar['ENTRIES'] . "&action=editentry&id=" . $row['id'] . "\" method=\"post\">
								<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
									<tr>
										<td class=\"title1\" colspan=\"2\">Edit Entry</td>
									</tr>
									<tr class=\"row2\">
										<td><strong>Title:</strong></td><td><input name=\"entrytitle\" type=\"text\" size=\"60\" value=\"" . $row['title'] . "\" /></td>
									</tr>";
				if ($actual_style == "bbcode") { $content .= bbcode_box(); }	
				$content .= "		<tr class=\"row1\">
										<td colspan=\"2\">
											<textarea id=\"entrycontent\" name=\"entrycontent\" class=\"wysiwygbox\" rows=\"10\" cols=\"58\">" . $row['content'] . "</textarea>
										</td>
									</tr>
								</table>									
								<br />
								<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
									<tr>
										<td class=\"title1\">Category</td>
									</tr>
									<tr class=\"row2\">
										<td class=\"center\">
											<select name=\"entrycatid\" class=\"settingsDropDown\">";
												$sql2 = "SELECT * FROM `" . DBTABLEPREFIX . "categories` ORDER BY name ASC";
												$result2 = mysql_query($sql2);
	
												while ($row2 = mysql_fetch_array($result2)) {
													$content .= "\n								<option value=\"" . $row2['id'] . "\"" . testSelected($row['cat_id'], $row2['id']) . ">" . $row2['name'] . "</option>";
												}
												
				$content .= "\n							</select>
										</td>
									</tr>
								</table>										
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Allow Comments</td>
								</tr>
								<tr class=\"row2\">
									<td class=\"center\">
										" . createDropdown("allowComments", "entryallowcomments", $row['allow_comments'], "") . "
									</td>
								</tr>
							</table>								
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Entry is Available on Main Page</td>
								</tr>
								<tr class=\"row2\">
									<td class=\"center\">
										" . createDropdown("visible", "entryvisible", $row['visible'], "") . "
									</td>
								</tr>
							</table>
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Show Digg Button</td>
								</tr>
								<tr class=\"row2\">
									<td class=\"center\">
										" . createDropdown("yesOrNo", "entryshowdiggbutton", $row['show_digg_button'], "") . "
									</td>
								</tr>
							</table>
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Show Reddit Button</td>
								</tr>
								<tr class=\"row2\">
									<td class=\"center\">
										" . createDropdown("yesOrNo", "entryshowredditbutton", $row['show_reddit_button'], "") . "
									</td>
								</tr>
							</table>
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Show Del.icio.us Button</td>
								</tr>
								<tr class=\"row2\">
									<td class=\"center\">
										" . createDropdown("yesOrNo", "entryshowdeliciousbutton", $row['show_delicious_button'], "") . "
									</td>
								</tr>
							</table>
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Show Slashdot Button</td>
								</tr>
								<tr class=\"row2\">
									<td class=\"center\">
										" . createDropdown("yesOrNo", "entryshowslashdotbutton", $row['show_slashdot_button'], "") . "
									</td>
								</tr>
							</table>
							<br />
							<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
								<tr>
									<td class=\"title1\">Password</td>
								</tr>
								<tr class=\"row2\">
									<td>Leave blank for none</td>
								</tr>
								<tr class=\"row1\">
									<td class=\"center\"><input type=\"text\" name=\"entrypassword\" size=\"60\" value=\"" . $row['password'] . "\" /></td>
								</tr>
							</table>									
							<br />
							<center><input type=\"submit\" name=\"submit\" value=\"Update Entry\" /></center>";
				if ($actual_style == "wysiwyg") {
					$content .= "
							<script type=\"text/javascript\">
								tinyMCE.init({
									// General options
									mode : \"textareas\",
									theme : \"advanced\",
									plugins : \"safari,pagebreak,style,layer,table,advhr,advimage,advlink,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template\",

									// Theme options
									theme_advanced_buttons1 : \"bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect\",
									theme_advanced_buttons2 : \"cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor\",
									theme_advanced_buttons3 : \"tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen\",
									theme_advanced_buttons4 : \"insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak\",
									theme_advanced_toolbar_location : \"top\",
									theme_advanced_toolbar_align : \"left\",
									theme_advanced_statusbar_location : \"bottom\",
									theme_advanced_resizing : true,

									// Drop lists for link/image/media/template dialogs
									template_external_list_url : \"lists/template_list.js\",
									external_link_list_url : \"lists/link_list.js\",
									external_image_list_url : \"lists/image_list.js\",
									media_external_list_url : \"lists/media_list.js\"
								});
							</script>";
				}
				else {
					$content .= "	<script type=\"text/javascript\" src=\"javascripts/bbcode.js\"></script>";			
				}
				$content .= "</form>";							
			}			
		}
	}
	else {
		if ($actual_action == "deleteentry") {
			// Delete the entry
			$sql = "DELETE FROM `" . DBTABLEPREFIX . "entries` WHERE id='" . $actual_id . "' LIMIT 1";
			$result = mysql_query($sql);
			
			// Delete all comments associated with this entry
			$sql = "DELETE FROM `" . DBTABLEPREFIX . "comments` WHERE entry_id='" . $actual_id . "' LIMIT 1";
			$result = mysql_query($sql);
		}		
		
		//==================================================
		// Print out our entries table
		//==================================================
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "entries` ORDER BY datetimestamp DESC";
		$result = mysql_query($sql);
		
		$x = 1; //reset the variable we use for our row colors	
		
		$content = "\n<script src=\"javascripts/confirm.js\" defer type=\"text/javascript\"></script>";
		$content .= "<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"2\">
									<div style=\"float: right;\"><a href=\"" . $menuvar['ENTRIES'] . "&action=newentry&style=bbcode\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/add.png\" alt=\"Add an entry using the BBCode editor\" /></a> <a href=\"" . $menuvar['ENTRIES'] . "&action=newentry&style=wysiwyg\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/add2.png\" alt=\"Add an entry using the WYSIWYG editor\" /></a></div>
									Current Entries
								</td>
							</tr>							
							<tr class=\"title2\">
								<td><strong>Entry Title</strong></td><td></td>
							</tr>";
							
		// Print out or list of entries			
		while ($row = mysql_fetch_array($result)) {
			$entry_style = ($row['type'] == PAGE_BBCODE) ? "bbcode" : "wysiwyg"; // Is the entry in BBCode?
			$content .=			"<tr class=\"row" . $x . "\">
									<td>" . $row['title'] . "</td>
									<td>
										<center><a href=\"" . $menuvar['ENTRIES'] . "&action=editentry&id=" . $row['id'] . "&style=" . $entry_style . "\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/check.png\" alt=\"Edit\" /></a> <a href=\"" . $menuvar['ENTRIES'] . "&action=deleteentry&id=" . $row['id'] . "\" onclick=\"return confirmDelete('entry');\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/delete.png\" alt=\"Delete\" /></a></center>
									</td>
								</tr>";
			$x = ($x==2) ? 1 : 2;
		}
		mysql_free_result($result);
		
	
		$content .=		"</table>";
	}
	$page->setTemplateVar('PageContent', $content);
}
else {
	$page->setTemplateVar('PageContent', "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>