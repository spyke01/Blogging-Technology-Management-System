<? 
/***************************************************************************
 *                               menus.php
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
	
	if (isset($_POST['submit'])) {
		foreach ($_POST['menu_text'] as $menu_id => $menu_value) {
			$sql = "UPDATE `" . DBTABLEPREFIX . "menus` SET title='" . keepsafe($menu_value) . "' WHERE id='" . keepsafe($menu_id) . "'";
			$result = mysql_query($sql);
		}
		
		foreach ($_POST['menu_entry_id'] as $menu_id => $menu_value) {
			$sql = "UPDATE `" . DBTABLEPREFIX . "menus` SET link='" . keepsafe($menu_value) . "' WHERE id='" . keepsafe($menu_id) . "'";
			$result = mysql_query($sql);
		}
		
	}
	
	if ($actual_action == "move") {
		$sql = "SELECT order FROM `" . DBTABLEPREFIX . "menus` WHERE id='" . $actual_id . "' LIMIT 1";
		$result = mysql_query($sql);
		
		if ($row = mysql_fetch_array($result)) { $current_order_num = $row['order']; }
		else { echo "Error getting menu order<br />$sql"; }
		mysql_free_result($result);
		
		if ($_GET[dir] == "up") { $target_order_num = ($current_order_num > 1) ? $current_order_num - 1 : 1; }
		else { $target_order_num = $current_order_num + 1; }
		
		$sql = "UPDATE `" . DBTABLEPREFIX . "menus` SET order='999' WHERE id='" . $actual_id . "'";
		$result = mysql_query($sql);
			
		$sql = "UPDATE `" . DBTABLEPREFIX . "menus` SET order='" . $current_order_num . "' WHERE order='" . $target_order_num . "' AND menus_menu='" . keepsafe($_GET['menuid']) . "'";
		$result = mysql_query($sql);
			
		$sql = "UPDATE `" . DBTABLEPREFIX . "menus` SET order='" . $target_order_num . "' WHERE id='" . $actual_id . "'";
		$result = mysql_query($sql);
	}
	
	if ($actual_action == "delete") {
		$sql = "DELETE FROM `" . DBTABLEPREFIX . "menus` WHERE id='" . $actual_id . "' LIMIT 1";
		$result = mysql_query($sql);
	}
	
	if ($actual_action == "newitem") {
		$sql = "SELECT order FROM `" . DBTABLEPREFIX . "menus` ORDER BY order DESC LIMIT 1";
		$result = mysql_query($sql);
		if ($row=mysql_fetch_array($result)) { $order = $row['order'] + 1; }
		
		$sql = "INSERT INTO `" . DBTABLEPREFIX . "menus` (`order`, `title`, `type` ) VALUES ('" . $order . "', 'New Menu', '" . MENU_INTERNAL . "');";
		$result = mysql_query($sql);
	}
	
	if ($actual_action == "newremoteitem") {
		$sql = "SELECT order FROM `" . DBTABLEPREFIX . "menus` ORDER BY order DESC LIMIT 1";
		$result = mysql_query($sql);
		if ($row=mysql_fetch_array($result)) { $order = $row['order'] + 1; }
		
		$sql = "INSERT INTO `" . DBTABLEPREFIX . "menus` (`order`, `title`, `type` ) VALUES ('" . $order . "', 'New Menu', '" . MENU_EXTERNAL . "');";
		$result = mysql_query($sql);
	}

	//==================================================
	// Print out our top menu items
	//==================================================
	
	$content = "<form action=\"" . $menuvar['MENUS'] . "\" method=\"post\">
					" . printMenuTable() . "
					<br /><br />
					<center><input type=\"submit\" name=\"submit\" value=\"Update Settings\" /></center>
				</form>";

	$page->setTemplateVar('PageContent', $content);
}
else {
	$page->setTemplateVar('PageContent', "\nYou Are Not Authorized To Access This Area. Please Refrain From Trying To Do So Again.");
}
?>