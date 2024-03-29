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
	var $email;  
	var $templateVars = array();
	var $topmenu = array();
	var $optionsmenu = array();
	var $categoriesmenu = array();
	var $stayupdatedmenu = array();

	//===============================================================
	// This function will me used for setting our template variables 
	//===============================================================
	function setTemplateVar($varname, $varvalue) {
		$this->templateVars[$varname] = $varvalue;
	}
	
	//===============================================================
	// This function will me used for printing our template variables 
	//===============================================================
	function printTemplateVar($varname) {
		echo $this->templateVars[$varname];
	}
	
	//===============================================================
	// This function determines wether the link is internal or 
	// external. An internal link should be the id in the DB. 
	//===============================================================
	function makeMenuItem($menu, $label, $page, $class = "") {
		if ($menu == "top") {
			$this->topmenu[$label] = array();
			$this->topmenu[$label]['value'] = $this->makeNavURL($page);
			if ($class != "") { $this->topmenu[$label]['class'] = $class; }
		}
		
		if ($menu == "options") {
			$this->optionsmenu[$label] = array();
			$this->optionsmenu[$label] = $this->makeNavURL($page);
			if ($class != "") { $this->optionsmenu[$label]['class'] = $class; }
		}
		
		if ($menu == "categories") {
			$this->categoriesmenu[$label] = array();
			$this->categoriesmenu[$label] = $this->makeNavURL($page);
			if ($class != "") { $this->categoriesmenu[$label]['class'] = $class; }
		}
		
		if ($menu == "stayupdated") {
			$this->stayupdatedmenu[$label] = array();
			$this->stayupdatedmenu[$label] = $this->makeNavURL($page);
			if ($class != "") { $this->stayupdatedmenu[$label]['class'] = $class; }
		}
	}
	
	//===============================================================
	// This function determines wether the link is internal or 
	// external. An internal link should be the id in the DB. 
	//===============================================================
	function makeNavURL($id) {
		if (is_numeric($id)) {
			return "index.php?p=viewentry&amp;id=$id";
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
	function printMenu($menu, $tag, $seperator = "", $tagClass = "", $tagBodyID = "", $tagBodyClass = "", $headerItem = "") {
		global $bts_config;
		
		if ($menu == "top") { $actualMenu = &$this->topmenu; }
		
		$doneonce = 0;
	
		if ($tag != "a") { 
			$classTag = ($tagBodyClass != "") ? " class=\"$tagBodyClass\"" : "";
			$idTag = ($tagBodyID != "") ? " id=\"$tagBodyID\"" : "";
			echo "<$tag" . $idTag . $classTag . ">"; 
		}
		
		if ($headerItem != "") { echo "\n						$headerItem"; }
		
		foreach ($actualMenu as $text => $page) {
			if ($doneonce == "1" && $seperator != "" && $tag == "a") { echo $seperator; } // do seperators only for a's
			
			$classTag = ($tagClass != "") ? " class=\"$tagClass\"" : "";
			$classTag = (trim($actualMenu[$text]['class']) != "") ? " class=\"" . $actualMenu[$text]['class'] . "\"" : $classTag;
			
			if ($tag != "a") {
				if ($actualMenu[$text]['value'] != "") { echo "\n						<li" . $classTag . "><a href=\"" . $actualMenu[$text]['value'] . "\"><span>" . $text . "</span></a></li>"; }
				else { echo "\n						<li" . $classTag . ">$text</li>"; }
				
			}
			else {
				if ($actualMenu[$text]['value'] != "") { echo "\n						<a href=\"" . $actualMenu[$text]['value'] . "\"" . $classTag . "><span>" . $text . "</span></a>"; }
				else { echo "\n						$text"; }
			}
			
			$doneonce = "1";
		}
		
		if ($tag != "a") { echo "\n					</$tag>\n"; }
	}	

	//===============================================================
	// This function prints our sidebar 
	//
	// $tagClass = name of a class that is added to the sidebar ul
	//===============================================================
	function printSidebar($tagId, $tagClass = "") {
		global $bts_config;
		
		$doneonce = 0;
	
		$classTag = ($tagClass != "") ? " class=\"$tagClass\"" : "";
		$idTag = ($tagId != "") ? " id=\"$tagId\"" : "";
		echo "\n<ul" . $classTag . $idTag . ">"; 
		
		// Print Options menu if its active
		if ($this->templateVars['options_active'] == ACTIVE) {
			echo "\n	<li>";
			echo "\n		<h2>Options</h2>";
			echo "\n		<ul>";
			
			foreach ($this->optionsmenu as $text => $page) {
				echo "\n			<li><a href=\"" . $page . "\"><span>" . $text . "</span></a></li>";
			}
			
			echo "\n		</ul>";
			echo "\n	</li>";
		}
		
		// Print Categories menu if its active
		if ($this->templateVars['categories_active'] == ACTIVE) {
			echo "\n	<li>";
			echo "\n		<h2>Categories</h2>";
			echo "\n		<ul>";
			
			foreach ($this->categoriesmenu as $text => $page) {
				echo "\n			<li><a href=\"" . $page . "\"><span>" . $text . "</span></a></li>";
			}
			
			echo "\n		</ul>";
			echo "\n	</li>";
		}
		
		echo "\n	<li>";
		echo "\n		<h2>Stay Updated</h2>";
		echo "\n		<ul>";
		echo "\n			<li id=\"feed\"><a href=\"rss.xml\">RSS Feed</a></li>";
		echo "\n		</ul>";
		echo "\n	</li>";
		echo "\n</ul>\n";
	}	
} 

?>