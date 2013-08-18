<? 
/***************************************************************************
 *                               createrss.php
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
	// open a file handle for the RSS file
	$fp = fopen ("rss.xml", "w");
	$rssData = "";
	
	// Select our entry from the DB
	$feedsql = "SELECT e.*, c.name FROM `" . DBTABLEPREFIX . "entries` e LEFT JOIN `" . DBTABLEPREFIX . "categories` c ON c.id=e.cat_id WHERE e.visible = '" . VISIBLE . "' ORDER BY e.datetimestamp DESC";
	$feedresult = mysql_query($feedsql);
		
	if ($feedresult && mysql_num_rows($feedresult) != 0) {
		//================================================
		// Print our RSS
		//================================================
		$rssData = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">
				<channel>
					<title>" . $bts_config['ftsbts_site_title'] . "</title>
					<description></description>
					<link>http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/</link>
					<language>en</language>";
			
		while ($feedrow = mysql_fetch_array($feedresult)) {
			$content = ($feedrow['type'] == PAGE_BBCODE) ? bbcode($feedrow['content']) : $feedrow['content'];
			
			// translate special characters back into html
			$trans_tbl = get_html_translation_table(HTML_ENTITIES);
			$trans_tbl = array_flip($trans_tbl );
			$content = strtr($content, $trans_tbl);
			$content = strip_tags($content);
			
			// Abbridge content if needed
			$content = (strlen($content) > 250) ? substr($content, 0, 250) . "...." : $content;
			
			$rssData .= "
					<item>
						<title>" . $feedrow['title'] . "</title>
						<description>				
							" . $content . "
						</description>
						<category>" . $feedrow['name'] . "</category>
						<link>http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . $menuvar['VIEWENTRY'] . "&amp;id=" . $feedrow['id'] . "</link>
						<guid isPermaLink=\"false\"/>
					</item>";
			
			$page->setTemplateVar("PageTitle", $feedrow['title']);
		}
		$rssData .= "
				</channel>
			</rss>";
		
		mysql_free_result($feedresult);
	}
	
	// Write the feed
	fwrite ($fp, $rssData);
	
	// Close our file handle
	fclose ($fp);
?>