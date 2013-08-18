<? 
/***************************************************************************
 *                               viewentry.php
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
	
	$page_content = "";
	
	// Select our entry from the DB
	$sql = "SELECT * FROM `" . DBTABLEPREFIX . "entries` WHERE id = '" . $actual_id . "' LIMIT 1";
	$result = mysql_query($sql);
		
	if (mysql_num_rows($result) == "0") { include '404.php'; }
	else {
		//================================================
		// Print our entry
		//================================================		
		while ($row = mysql_fetch_array($result)) {
			if ($row['password'] != "" && keepsafe($_POST['entrypassword']) != "" && $row['password'] != keepsafe($_POST['entrypassword'])) {
				$page_content .= "\n				<div class=\"entry\">
														<h3 class=\"entrytitle\">Incorrect Password</h3>
														<div class=\"entrycontent\">
															The password you submitted does not match the one on file. Please try again.<br /><br />
															<form name=\"passwordForm\" action=\"" . $menuvar['VIEWENTRY'] . "&id=" . $actual_id . "\" method=\"post\">
																<label for=\"entrypassword\">Password </label> <input name=\"entrypassword\" type=\"password\" />
																<input name=\"submit\" type=\"submit\" class=\"button\" value=\"View Entry\" />
															</form>
														</div>
													</div>";				
			}
			elseif ($row['password'] != "" && $_POST['entrypassword'] == "") {
				$page_content .= "\n				<div class=\"entry\">
														<div class=\"entrycontent\">
															This entry is password protected, to view this entry please type in the password.<br /><br />
															<form name=\"passwordForm\" action=\"" . $menuvar['VIEWENTRY'] . "&id=" . $actual_id . "\" method=\"post\">
																<label for=\"entrypassword\">Password </label> <input name=\"entrypassword\" type=\"password\" />
																<input name=\"submit\" type=\"submit\" class=\"button\" value=\"View Entry\" />
															</form>
														</div>
													</div>";			
			} 
			elseif (($row['password'] != "" && $row['password'] == $_POST['entrypassword']) || $row['password'] == "") {
				$page_content .= "\n				<div class=\"entry\">
														<h3 class=\"entrytitle\">" . $row['title'] . "</h3>
														<p class=\"entrydate\">
														<span class=\"posted\">Posted On: </span>
														<span class=\"month\">" . makeMonth($row['datetimestamp']) . "</span>
														<span class=\"day\">" . makeDay($row['datetimestamp']) . "<span class=\"comma\">,</span></span>
														<span class=\"year\">" . makeYear($row['datetimestamp']) . "</span>
						  								</p>
														<div class=\"entrycontent\">
															" . (($row['type'] == PAGE_BBCODE) ? bbcode($row['content']) : $row['content']) . "
															<br /><br />
															" . (($row['show_digg_button'] == ACTIVE) ? diggButton("", "", "", "", true, $row['id']) : "") . "
															" . (($row['show_reddit_button'] == ACTIVE) ? redditButton("", "Submit to Reddit", "", $row['id']) : "") . "
															" . (($row['show_delicious_button'] == ACTIVE) ? deliciousButton("", "Submit to Del.icio.us", "", $row['id']) : "") . "
															" . (($row['show_slashdot_button'] == ACTIVE) ? slashdotButton("", "Submit to Slashdot", "", $row['id']) : "") . "
														</div>
													</div>
													<br /><br />";
				
				$page->setTemplateVar("PageTitle", $row['title']);
			
			
				if ((($bts_config['ftsbts_registered_users_comment'] == ACTIVE && isset($_SESSION['username'])) || ($bts_config['ftsbts_registered_users_comment'] == INACTIVE)) && $row['allow_comments'] == ALLOW_COMMENTS && $_SESSION['user_level'] != BANNED) {
					//================================================
					// Print the comments for our entry
					//================================================
					$page_content .= "\n					<a name=\"comments\"></a>
															<h3 class=\"entrytitle\">Comments</h3><br />
															<div id=\"updateMe\">";
					
					$sql2 = "SELECT * FROM `" . DBTABLEPREFIX . "comments` WHERE entry_id = '" . $actual_id . "' ORDER BY datetimestamp ASC";
					$result2 = mysql_query($sql2);
					
					if (mysql_num_rows($result2) == "0") { // No comments yet!
							$page_content .= "\n					<div class=\"comment\">
																		Be the first to post a comment!
																	</div><br />";	
					}
					else {	 // Print all our comments	
						while ($row2 = mysql_fetch_array($result2)) {
							if ($row2['id'] == $newCommentId) { $page_content .= "\n					<a name=\"newComment\"></a>"; }
							
							$page_content .= "\n					<div id=\"" . $row2['id'] . "\" class=\"comment\">";
							$poster = ($row2['website'] != "") ? "<a href=\"" . $row2['website'] . "\">" . $row2['name'] . "</a>" : $row2['name'];
							$page_content .= "\n						" . $poster . "<br />";
							if ($_SESSION['user_level'] == ADMIN || $_SESSION['user_level'] == MOD) { $page_content .= "\n						Email: " . $row2['email_address'] . "<br />"; }
							$page_content .= "\n						<small>Posted on " . makeDate($row2['datetimestamp']) . "</small>
																		<br /><br />
																		<div id=\"" . $row2['id'] . "_text\">
																			" . bbcode($row2['comment']) . "<br /><br />
																		</div>";
							
							// Allow editing of comments
							if ($_SESSION['user_level'] == ADMIN || $_SESSION['user_level'] == MOD || $_SESSION['userid'] == $row2['user_id']) {
								$page_content .= "\n						<script type=\"text/javascript\">
																				new Ajax.InPlaceEditor('" . $row2['id'] . "_text', 'ajax.php?action=updatecomment&id=" . $row2['id'] . "', {rows:8,cols:50,loadTextURL:'ajax.php?action=getcommenttext&id=" . $row2['id'] . "'});
																			</script>
																			<strong>[Click On Your Comment To Edit It]</strong><br />";
							}
							
							// Allow deletion of comments
							if ($_SESSION['user_level'] == ADMIN || $_SESSION['user_level'] == MOD || $_SESSION['userid'] == $row2['user_id']) { $page_content .= "\n						<a onclick=\"new Ajax.Request('ajax.php?action=deletecomment&id=" . $row2['id'] . "', {asynchronous:true, onSuccess:function(){ new Effect.Squish('" . $row2['id'] . "');}});\">[Delete This Comment]</a>"; }
							$page_content .= "\n					</div><br />";
						}
						mysql_free_result($result2);
					}
					$page_content .= "\n					</div>";
				
					//================================================
					// Print our new comment form
					//================================================
					$page_content .= "\n					<form name=\"newCommentForm\" id=\"newCommentForm\" action=\"" . $PHP_SELF . "\" method=\"post\" class=\"commentsform\" onsubmit=\"return false;\">
																<fieldset>
																	<legend>Add a Comment</legend>
																	<input type=\"hidden\" name=\"entrypassword\" class=\"hiddenField\" value=\"" . keepsafe($_POST['entrypassword']) . "\" />
																	<label for=\"name\">Name <em>*</em></label> <input type=\"text\" name=\"name\" class=\"required\" value=\"" . $_SESSION['first_name'] . " " . $_SESSION['last_name'] . "\" />
																	<label for=\"emailaddress\">Email Address <em>*</em></label> <input type=\"text\" name=\"emailaddress\" class=\"required validate-email\" value=\"" . $_SESSION['email_address'] . "\" />
																	<label for=\"website\">Website</label> <input type=\"text\" name=\"website\" value=\"" . $_SESSION['website'] . "\" />
																	<label for=\"comments\">Comments</label> <textarea name=\"comments\" class=\"required\"></textarea>
																	<br /><input name=\"submit\" type=\"submit\" class=\"button\" value=\"Add Comment!\" />
																</fieldset>
															</form>
															<script type=\"text/javascript\">
																var valid = new Validation('newCommentForm', {immediate : true, useTitles:true, onFormValidate : ValidateForm});
				
																function ValidateForm(result, form){
																	if (result) {
																		var name=document.newCommentForm.name
																		var emailID=document.newCommentForm.emailaddress
																		var comment=document.newCommentForm.comments
																		new Ajax.Updater('updateMe', 'ajax.php?action=postcomment&id=" . $actual_id .  "', {onComplete:function(){ new Effect.Highlight('newComment');},asynchronous:true, parameters:Form.serialize(form), evalScripts:true}); 
																		comment.value = '';
																		return false;
																	}
																 }
															</script>";
				}
				else {
					$page_content .= "\n					<a name=\"comments\"></a>
															<h3 class=\"entrytitle\">Comments</h3><br />
															<div class=\"comment\">";
					if ($row['allow_comments'] != ALLOW_COMMENTS) {
						$page_content .= "\n						Commenting has been disabled for this post!";
					}
					if ($_SESSION['user_level'] == BANNED) {
						$page_content .= "\n						Your account has been banned and you are no longer allowed to post comments!";
					}
					if ($bts_config['ftsbts_registered_users_comment'] == ACTIVE) {
						$page_content .= "\n						Only registered users may post comments.";
					}
					$page_content .= "\n					</div><br />";					
				}
			}
			else {
				$page_content .= "\n				<div class=\"entry\">
														<h3 class=\"entrytitle\">Problem Encountered</h3>
														<div class=\"entrycontent\">
															There was a problem encountered while attempting to load the entry. Please contact the webmaster.
														</div>
													</div>";				
			}
		}
	}
	mysql_free_result($result);
		
	$page->setTemplateVar("PageContent", $page_content);

?>