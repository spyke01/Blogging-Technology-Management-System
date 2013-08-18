<?php 
/***************************************************************************
 *                               buttons.php
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
	// Plugin Name: Digg This
	// Version: 1.0.1
	// Plugin URI: http://www.aviransplace.com/index.php/digg-this-wordpress-plugin/
	// Author: Aviran Mordo
	// Author URI: http://www.aviransplace.com
	// Description: Adds Digg story link on detection on digg referer
	//=========================================================
	
	function quickDiggButton($id) {
	   diggButton("", "", "", "", true, $id);
	}
	
	function diggButton($before="", $link_text="Digg this story", $after="", $ping_str="", $use_digg_button=false, $id) {
		global $bts_config;
		$orig_ref = $_SERVER['HTTP_REFERER'];
		$ref = strtolower($orig_ref);
		
		$sql = "SELECT diggurl, title, content FROM `" . DBTABLEPREFIX . "entries` WHERE id = '" . $id . "'";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$digg_link = $row['diggurl'];
		$title = $row['title'];
		$content = substr(htmlspecialchars($row['content']), 0, 350);
		mysql_free_result($result);	
	   
		if ( $digg_link == '' && substr_count($ref, "_") > 0 && strpos($ref, "digg.com") !== false && strpos($ref, "?") == false && strpos($ref, "upcoming") == false && (strpos($ref, "digg.com")==0 || strpos($ref, "http://digg.com")==0 || strpos($ref, "www.digg.com")==0 || strpos($ref, "http://www.digg.com")==0)) { 
			$digg_link = $orig_ref; 
			
			$sql = "UPDATE `" . DBTABLEPREFIX . "entries` SET diggurl = '" . $digg_link . "' WHERE id = '" . $id . "'";
			$result = mysql_query($sql);	
		}
	
		if (substr_count(strtolower($digg_link),"digg.com")) {
			$returnit = "".$before;
			if ($use_digg_button) {
				$returnit .= "<link rel=\"plugin\" title=\"Digg This - Wordpress Plugin\" href=\"http://www.aviransplace.com/digg-this-wordpress-plugin/\" />";
				$returnit .= "<iframe src=\"http://digg.com/api/diggthis.php?u=" . htmlspecialchars($digg_link) . "\" height=\"82\" width=\"55\" frameborder=\"0\" scrolling=\"no\"></iframe>";
			}
			else {
				$returnit .= "<a href=\"".$digg_link."\" ping=\"" . get_permalink($post->ID) . $ping_str . "\" title=\"Digg this story\">".$link_text."</a>";
			}
			$returnit .= $after;
		}
		else {
			$returnit = "<a href=\"http://www.digg.com/submit?url=" . urlencode("http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') ."/index.php?p=viewentry&id=" . $id ) . "&phase=2&title=" . urlencode($title) . "&bodytext=" . urlencode($content) . "\" title=\"digg this post\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/images/diggbutton.png\" alt=\"Digg This!\" /></a>";
		}
		return $returnit;
	}
	
	//=========================================================
	// Add a del.icio.us button
	//=========================================================
	function deliciousButton($before = "", $link_text = "Submit to Del.icio.us", $after = "", $id) {
		global $bts_config;
		$link = urlencode("http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') ."/index.php?p=viewentry&id=" . $id );
		$button = "";
		
		$sql = "SELECT title FROM `" . DBTABLEPREFIX . "entries` WHERE id = '" . $id . "'";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			$row = mysql_fetch_array($result);
			$title = $row['title'];
	   
			$button = $before . "<a href=\"http://del.icio.us/post?url=" . $link . "&title=" . $title . "\" title=\"" . $link_text . "\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/images/delicious.gif\" alt=\"" . $link_text . "\" />" . $link_text . "</a>" . $after;
			
			mysql_free_result($result);	
		}
		
		return $button;
	}
	
	//=========================================================
	// Add a slashdot button
	//=========================================================
	function slashdotButton($before = "", $link_text = "Submit to Slashdot", $after = "", $id) {
		global $bts_config;
		$link = urlencode("http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') ."/index.php?p=viewentry&id=" . $id );
		$button = "";
		
		$sql = "SELECT title FROM `" . DBTABLEPREFIX . "entries` WHERE id = '" . $id . "'";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			$row = mysql_fetch_array($result);
			$title = $row['title'];
	   
			$button = $before . "<a href=\"http://slashdot.org/submit.pl?url=" . $link . "&title=" . $title . "\" title=\"" . $link_text . "\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/images/slashdot.gif\" alt=\"" . $link_text . "\" />" . $link_text . "</a>" . $after;
			
			mysql_free_result($result);	
		}
		
		return $button;
	}
	
	//=========================================================
	// Add a reddit button
	//=========================================================
	function redditButton($before = "", $link_text = "Submit to Reddit", $after = "", $id) {
		global $bts_config;
		$link = urlencode("http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') ."/index.php?p=viewentry&id=" . $id );
		$button = "";
		
		$sql = "SELECT title FROM `" . DBTABLEPREFIX . "entries` WHERE id = '" . $id . "'";
		$result = mysql_query($sql);
		
		if ($result && mysql_num_rows($result) > 0) {
			$row = mysql_fetch_array($result);
			$title = $row['title'];
	   
			$button = $before . "<a href=\"http://www.reddit.com/submit?url=" . $link . "\" title=\"" . $link_text . "\"><img src=\"themes/" . $bts_config['ftsbts_theme'] . "/images/reddit.gif\" alt=\"" . $link_text . "\" />" . $link_text . "</a>" . $after;
			
			mysql_free_result($result);	
		}
		
		return $button;
	}

?>