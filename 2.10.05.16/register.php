<? 
/***************************************************************************
 *                               register.php
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

	$done = 0;

	//==================================================
	// Handle editing, adding, and deleting of users
	//==================================================	
	if (isset($_POST['submit'])) {
		$username = keepsafe($_POST['username']);
		$password = md5(keepsafe($_POST['password']));
		$email = keepsafe($_POST['emailaddress']);
		$first_name = keeptasafe($_POST['first_name']);
		$last_name = keeptasafe($_POST['last_name']);
		$website = keepsafe($_POST['website']);
		$website = (substr($website, 0, 4) == "http") ? $website : "http://" . $website;
	
		// Check and make sure username is available
		$sql = "SELECT * FROM `" . USERSDBTABLEPREFIX . "users` WHERE username = '" . $username . "'";
		$result = mysql_query($sql);
		
		$usernameCheck = mysql_num_rows($result);
		
		if ($usernameCheck == 0 && $username != "") {
			if ($_POST['password'] == $_POST['password2']) {								
				$sql = "INSERT INTO `" . USERSDBTABLEPREFIX . "users` (`username`, `password`, `email_address`, `user_level`, `first_name`, `last_name`, `website`) VALUES ('" . $username . "', '" . $password . "', '" . $email . "', '" . USER . "', '" . $first_name . "', '" . $last_name . "', '" . $website . "')";
				$result = mysql_query($sql);
				
				if ($result) {
					$content = "<center>Your account has been created, and you are being redirected to the main page.</center>
								<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['HOME'] . "\">";
					$done = 1;
				}
				else {
					$content = "<center>There was an error while creating your new user. Please try again later.</center>";						
				}
			}
			else {
				$content = "<center>The passwords you supplied do not match. Please check them and try again.</center>";			
			}
		}
		else {
			$content = "<center>The username you specified has already been taken, please try again.</center>";			
		}		
	}
	if ($done != 1) {
		$content .= "
					<form id=\"newUserForm\" name=\"newUserForm\" action=\"" . $menuvar['REGISTER'] . "\" method=\"post\">
						<table class=\"contentBox\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\">
							<tr>
								<td class=\"title1\" colspan=\"2\">Create An Account</td>
							</tr>
							<tr class=\"row1\">
								<td><strong>First Name:</strong> <em>*</em></td><td><input name=\"first_name\" id=\"first_name\" type=\"text\" size=\"55\" class=\"required\" value=\"" . keeptasafe($_POST['first_name']) . "\" /></td>
							</tr>
							<tr class=\"row2\">
								<td><strong>Last Name:</strong> <em>*</em></td><td><input name=\"last_name\" id=\"last_name\" type=\"text\" size=\"55\" class=\"required\" value=\"" . keeptasafe($_POST['last_name']) . "\" /></td>
							</tr>
							<tr class=\"row1\">
								<td><strong>Username:</strong> <em>*</em></td><td><input name=\"username\" id=\"username\" type=\"text\" size=\"55\" class=\"required validate-alphanum\" value=\"" . keepsafe($_POST['username']) . "\" /></td>
							</tr>
							<tr class=\"row2\">
								<td><strong>Password:</strong> <em>*</em></td><td><input name=\"password\" id=\"password\" type=\"password\" size=\"55\" class=\"required validate-password\" /></td>
							</tr>
							<tr class=\"row1\">
								<td><strong>Confirm Password:</strong> <em>*</em></td><td><input name=\"password2\" id=\"password2\" type=\"password\" size=\"55\" class=\"required validate-password-confirm\" /></td>
							</tr>
							<tr class=\"row2\">
								<td><strong>Email Address:</strong> <em>*</em></td><td><input name=\"emailaddress\" id=\"emailaddress\" type=\"text\" size=\"55\" class=\"required validate-email\" value=\"" . keepsafe($_POST['emailaddress']) . "\" /></td>
							</tr>
							<tr class=\"row2\">
								<td><strong>Website:</strong></td><td><input name=\"website\" id=\"website\" type=\"text\" size=\"55\" value=\"" . keepsafe($_POST['website']) . "\" /></td>
							</tr>
						</table>									
						<br />
						<center><input type=\"submit\" name=\"submit\" value=\"Add User\" /></center>
					</form>
					<script type=\"text/javascript\">
						var valid = new Validation('newUserForm', {immediate : true, useTitles:true});
						Validation.addAllThese([
								['validate-password', 'Your password must be at least 7 characters and cannot be your username, the word password, 1234567, or 0123456.', {
								minLength : 7,
								notOneOf : ['password','PASSWORD','1234567','0123456'],
								notEqualToField : 'username'
							}],
							['validate-password-confirm', 'Your passwords do not match.', {
								equalToField : 'password'
							}]
						]);
					</script>";
	}

	
	$page->setTemplateVar("PageContent", $content);
?>