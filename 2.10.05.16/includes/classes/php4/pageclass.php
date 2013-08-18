

	//===============================================================
	// This function prints our sidebar 
	//
	// $tagClass = name of a class that is added to the sidebar ul
	//===============================================================
	function printSidebar($tagId, $tagClass = "") {		
		$doneonce = 0;
		$sidebarHTML = "";
		$classTag = ($tagClass != "") ? " class=\"" . $tagClass . "\"" : "";
		$idTag = ($tagId != "") ? " id=\"" . $tagId . "\"" : "";
		
		// Print opening tag
		$sidebarHTML .= ($tag != "a") ? "
				<ul" . $idTag . $classTag . ">" : ""; 
		
		// Print userOptionsLeft menu if its active
		if ($this->templateVars['uOLm_active'] == ACTIVE) {
			$sidebarHTML .= "
					<li class=\"title\">User Menu</li>";
			
			foreach ($this->userOptionsLeftMenu as $text => $page) {
				$sidebarHTML .= "
					<li><a href=\"" . $this->userOptionsLeftMenu[$text]['value'] . "\"><span>" . $text . "</span></a></li>";
			}
		}
		
		// Print adminOptionsLeft menu if its active
		if ($this->templateVars['aOLm_active'] == ACTIVE && ($_SESSION['user_level'] == BOARD_ADMIN || $_SESSION['user_level'] == SYSTEM_ADMIN)) {
			$sidebarHTML .= "
					<li class=\"title\">Admin Menu</li>";
			
			foreach ($this->adminOptionsLeftMenu as $text => $page) {
				$sidebarHTML .= "
					<li><a href=\"" . $this->adminOptionsLeftMenu[$text]['value'] . "\"><span>" . $text . "</span></a></li>";
			}
		}
	
		$sidebarHTML .= "
					<li class=\"titleFooter\"></li>
				</ul>";
		
		// Print closing tag
		echo $sidebarHTML;
	}	