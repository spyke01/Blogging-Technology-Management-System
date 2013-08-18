<?php 
/***************************************************************************
 *                               menus.php
 *                            -------------------
 *   begin                : Saturday, Sept 24, 2005
 *   copyright            : (C) 2005 Paden Clayton - Fast Track Sites
 *   email                : sales@fasttacksites.com
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

	//=========================================================
	// Prints our menus table for a specific menu
	//=========================================================
	function printMenuTable() {
		global $menuvar, $bts_config;
	
		$sql = "SELECT * FROM `" . DBTABLEPREFIX . "menus` ORDER BY order ASC";
		$result = mysql_query($sql);
		
		$x = 1; //reset the variable we use for our row colors	
		
		$content = "
						<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"3\">
									<div style=\"float: right;\"><a href=\"" . $menuvar['MENUS'] . "&action=newitem\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/add.png\" alt=\"Add an internal link to the menu\" /></a> <a href=\"" . $menuvar['MENUS'] . "&action=newremoteitem\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/add2.png\" alt=\"Add an external link to the menu\" /></a></div>
									Menu Items
								</td>
							</tr>
							<tr class=\"title2\">
								<td><strong>Menu Text</strong></td><td><strong>Links To This Page</strong></td><td></td>
							</tr>";
		if (!$result || mysql_num_rows($result) == 0) {
			$content .=			"<tr class=\"row" . $x . "\">
									<td colspan=\"3\">No items are set for this menu.</td>
								</tr>";
		}
		else {
			while ($row = mysql_fetch_array($result)) {
				$content .=			"<tr class=\"row" . $x . "\">
									<td><input type=\"text\" size=\"40\" name=\"menu_text[" . $row['id'] . "]\" value=\"" . $row['title'] . "\" /></td>
									<td>";
				
				if ($row['type'] == MENU_EXTERNAL) {
					$content .=	"
										<input type=\"text\" size=\"30\" name=\"menu_entry_id[" . $row['id'] . "]\" value=\"" . $row['link'] . "\" />";
				}
				else {
					$content .=	"				
										<select name=\"menu_entry_id[" . $row['id'] . "]\">";
										
					// Print out our pages so that we can choose from them
					$sql2 = "SELECT id, title FROM `" . DBTABLEPREFIX . "entries`";
					$result2 = mysql_query($sql2);									
				
					if (mysql_num_rows($result2) > 0) {
						while ($row2 = mysql_fetch_array($result2)) {
							$content .=						"<option value=\"" . $row2['id'] . "\"" . testSelected($row['link'], $row2['id']) . ">" . $row2['title'] . "</option>";
						}
						mysql_free_result($result2);
					}
					$content .=	"				
										</select>";
				}
				
				$content .=	"
									</td>
									<td>
										<a href=\"" . $menuvar['MENUS'] . "&action=move&id=" . $row['id'] . "&dir=up\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/arrowUp.png\" alt=\"Move Up\" /></a><a href=\"" . $menuvar['MENUS'] . "&action=move&id=" . $row['id'] . "&dir=down\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/arrowDown.png\" alt=\"Move Down\" /></a><a href=\"" . $menuvar['MENUS'] . "&action=delete&id=" . $row['id'] . "\" onclick=\"return confirmDelete('menu item');\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/icons/delete.png\" alt=\"Delete Menu\" /></a>
									</td>
								</tr>";
				$x = ($x==2) ? 1 : 2;
			}
			mysql_free_result($result);
		}
		$content .= "</table>";
		
		return $content;
	}

?>