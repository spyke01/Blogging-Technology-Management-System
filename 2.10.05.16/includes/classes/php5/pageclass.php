<?php
/***************************************************************************
 *                               pageclass.php
 *                            -------------------
 *   begin                : Tuesday, August 15, 2006
 *   copyright            : (C) 2006 Paden Clayton
 *   website              : http://www.fasttracksites.com
 *   email                : sales@fasttracksites.com
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 * This program is licensed under the FTS program license that has been 
 * included with this program. It is located inside the license.txt file 
 * that is found in the base directory. This license is legally binding and
 * in the event of a breach of contract, legal action may be taken. 
 *
 ***************************************************************************/
 
class pageClass { 
	private $templateVars = array();
	private $menus = array();

	//===============================================================
	// This function will me used for setting our template variables 
	//===============================================================
	public function setTemplateVar($varname, $varvalue) {
		$this->templateVars[$varname] = $varvalue;
	}
	
	//===============================================================
	// This function will me used for retreiving our template variables 
	//===============================================================
	public function getTemplateVar($varname) {
		return $this->templateVars[$varname];
	}
	
	//===============================================================
	// This function will me used for printing our template variables 
	//===============================================================
	public function printTemplateVar($varname) {
		echo $this->templateVars[$varname];
	}
	
	//===============================================================
	// This function will allow us to add breadcrumbs to our trail
	//===============================================================
	public function addBreadCrumb($name, $link) {
		$this->breadCrumbTrail[] = array($this->makeNavURL($name), $this->makeNavURL($link));
	}
	
	//===============================================================
	// This function determines wether the link is internal or 
	// external. An internal link should be the id in the DB. 
	//===============================================================
	public function makeMenuItem($menu, $label, $page, $class = "") {
		if (!array_key_exists($menu, $this->menus)) $this->menus[$menu] = array();
	
		$this->menus[$menu][$label] = array();
		$this->menus[$menu][$label]['value'] = $this->makeNavURL($page);
		if ($class != "") { $this->menus[$menu][$label]['class'] = $class; }
	}
	
	//===============================================================
	// This function determines wether the link is internal or 
	// external. An internal link should be the id in the DB. 
	//===============================================================
	public function makeNavURL($id) {
		if (is_numeric($id)) {
			return "index.php?p=viewentry&amp;id=" . $id;
		}
		else {
			return $id;
		}
	}

	//===============================================================
	// This function prints our menus on the page, it also allows 
	// for customization of what type of what type of tag to use 
	//
	// $menu = top, left, bottom
	// $tag = a, ul, ol
	// $seperator = text that goes between links ie <br />
	// $tagClass = name of a class that is added to each tag
	// $tagBodyClass = name of class that is added to UL or OL
	// $headeritem = text or other item that will be at top of menu
	//===============================================================
	public function printMenu($menu, $tag, $seperator = "", $tagClass = "", $tagBodyID = "", $tagBodyClass = "", $headerItem = "") {
		$doneonce = 0;
		$menuHTML = "";
		$classTag = ($tagBodyClass != "") ? " class=\"" . $tagBodyClass . "\"" : "";
		$idTag = ($tagBodyID != "") ? " id=\"" . $tagBodyID . "\"" : "";
		
		// Print opening tag
		$menuHTML .= ($tag != "a") ? "<" . $tag . $idTag . $classTag . ">" : "";
		$menuHTML .= ($headerItem != "") ? "\n						" . $headerItem : "";
		
		foreach ($this->menus[$menu] as $label => $settingsArray) {
			if ($doneonce == "1" && $seperator != "" && $tag == "a") { echo $seperator; } // do seperators only for a's
			
			$classTag = ($tagClass != "") ? " class=\"" . $tagClass . "\"" : "";
			$classTag = (trim($settingsArray['class']) != "") ? " class=\"" . $settingsArray['class'] . "\"" : $classTag;
			
			// If we are using a list then wrap it with li tags
			$menuHTML .= ($tag == "ul" || $tag == "ol") ? "\n						<li" . $classTag . ">" : "";
			$menuHTML .= ($settingsArray['value'] != "") ? "<a href=\"" . $settingsArray['value'] . "\"><span>" . $label . "</span></a>" : $label;
			$menuHTML .= ($tag == "ul" || $tag == "ol") ? "</li>" : "";
			
			$doneonce = "1";
		}
		
		// Print closing tag
		$menuHTML .= ($tag != "a") ? "\n					</" . $tag . ">\n" : "";
		
		return $menuHTML;
	}	

	//===============================================================
	// This function prints our sidebar 
	//
	// $tagClass = name of a class that is added to the sidebar ul
	//===============================================================
	public function printSidebar($tagId, $tagClass = "") {		
		$doneonce = 0;
		$sidebarHTML = "";
		$classTag = ($tagClass != "") ? " class=\"" . $tagClass . "\"" : "";
		$idTag = ($tagId != "") ? " id=\"" . $tagId . "\"" : "";
		
		// Print sidebar menu if its active
		if ($this->templateVars['sidebar_active'] == ACTIVE) {
			// Print opening tag
			$sidebarHTML .= ($tag != "a") ? "
				<ul" . $idTag . $classTag . ">" : ""; 
		
			foreach ($this->menus as $key => $menu) {
				if ($key != "top") {
					foreach ($menu as $label => $settingsArray) {
					
						$sidebarHTML .= "
							<li" . (($settingsArray['class'] != "") ? " class=\"" . $settingsArray['class'] . "\"" : "") . ">" . (($settingsArray['value'] != "") ? "<a href=\"" . $settingsArray['value'] . "\"><span>" . $label . "</span></a>" : $label) . "</li>";
					}
				}
			}
	
			$sidebarHTML .= "
					<li class=\"titleFooter\"></li>
				</ul>";
		}
		
		// Print closing tag
		return $sidebarHTML;
	}	

	//===============================================================
	// This function prints our breadcrumbs on the page, it also allows 
	// for customization of what type of what type of tag to use 
	//
	// $menu = top, left, bottom
	// $tag = a, ul, ol
	// $seperator = text that goes between links ie <br />
	// $tagClass = name of a class that is added to each tag
	// $tagBodyClass = name of class that is added to UL or OL
	// $headeritem = text or other item that will be at top of menu
	//===============================================================
	public function printBreadCrumbs($tag, $seperator = "", $tagClass = "", $tagBodyID = "", $tagBodyClass = "") {
		$breadCrumbHTML = "";
		$classTag = ($tagBodyClass != "") ? " class=\"" . $tagBodyClass . "\"" : "";
		$idTag = ($tagBodyID != "") ? " id=\"" . $tagBodyID . "\"" : "";
		
		// Print opening tag
		$breadCrumbHTML .= ($tag != "a") ? "<" . $tag . $idTag . $classTag . ">" : ""; 
		
		// Print our bread crumbs
		if (is_array($this->breadCrumbTrail)) {
			foreach ($this->breadCrumbTrail as $arrayCount => $dataArray) {			
				// Don't print the html if the variable is empty
				$classTag = ($tagClass != "") ? " class=\"" . $tagClass . "\"" : "";
				
				// If we are using a list then wrap it with li tags
				$breadCrumbHTML .= ($tag == "ul" || $tag == "ol") ? "\n						<li" . $classTag . ">" : "";
				$breadCrumbHTML .= ($dataArray[1] != "" && $arrayCount < (count($this->breadCrumbTrail) - 1)) ? "<a href=\"" . $dataArray[1] . "\"><span>" . $dataArray[0] . "</span></a>" : $dataArray[0];
				$breadCrumbHTML .= ($tag == "ul" || $tag == "ol") ? "</li>" : "";
				$breadCrumbHTML .= ($arrayCount < (count($this->breadCrumbTrail) - 1) && $seperator != "") ? $seperator : "";
			}
		}
		
		// Print closing tag
		$breadCrumbHTML .= ($tag != "a") ? "\n						</" . $tag . ">" : "";
		
		return $breadCrumbHTML;
	}
} 

?>