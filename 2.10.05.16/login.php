<?
/***************************************************************************
 *                               login.php
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
define('IN_LOGIN', 1); //let the header file know were here to stay Hey! Hey! Hey! 

$current_time = time();

//========================================
// Login Function for registering session
//========================================
if (isset($_POST['password'])) {
	// Convert to simple variables
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	if((!$username) || (!$password)){
		echo "Please enter ALL of the information! <br />";
		exit();
	}	
	
	// strip away any dangerous tags
	$username = keepsafe($username);
	$password = keepsafe($password);
	
	// Convert password to md5 hash
	$password = md5($password);

	// check if the user info validates the db
	$sql = mysql_query("SELECT * FROM `" . USERSDBTABLEPREFIX . "users` WHERE username='" . $username . "' AND password='" . $password . "' AND active='1' ");
	$login_check = mysql_num_rows($sql);
	
	if($login_check > 0){
		while($row = mysql_fetch_array($sql)){			
			if (isset($_POST['autologin'])) {
				$cookiename = $bts_config['ftsbts_cookie_name'];
				setcookie($cookiename, $row['id'] . "-" . $row['password'], time()+2592000 ); //set cookie for 1 month
			}
									
			// Register some session variables!
			$_SESSION['STATUS'] = "true";
			$_SESSION['userid'] = $row['id'];
			$_SESSION['username'] = $row['username'];
			$_SESSION['epassword'] = $row['password'];
			$_SESSION['email_address'] = $row['email_address'];
			$_SESSION['first_name'] = $row['first_name'];
			$_SESSION['last_name'] = $row['last_name'];
			$_SESSION['website'] = $row['website'];
			$_SESSION['user_level'] = $row['user_level'];
			$_SESSION['script_locale'] = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');			

			header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . $menuvar['LOGIN']);			
		 	$content = "You are now logged in as " . $_SESSION['username'] . ". <br /><div class=\"center\"><a href=\"" . $menuvar['LOGOUT'] . "\">Logout</a></div>"; 
		}

	} 
	else {
		$content = "You could not be logged in! Either the username and password do not match or you have not validated your membership!<br />
		Please try again!<br /><a href=\"" . $mnuvar['HOME'] . "\">Home</a>.";
	}
}

//========================================
// If we got here check and see if they 
// are logged in, if not print login page
//========================================
else{

	if (isset($_SESSION['username'])) {
		$content = "
			You are logged in as " . $_SESSION['username'] . ", and are being redirected to the main page. 
			<br /><div class=\"center\"><a href=\"" . $menuvar['LOGOUT'] . "\">Logout</a></div>
			<meta http-equiv=\"refresh\" content=\"1;url=" . $menuvar['ADMIN'] . "\">";
 
	}
	else { 
		$content = "<form action=\"" . $menuvar['LOGIN'] . "\" method=\"post\" id=\"loginForm\">
						<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" class=\"contentBox half\">
							<tr><td class=\"title1\" colspan=\"2\">User Login</td></tr>
							<tr>
								<td width=\"32%\" class=\"row1\">Username: </td>
								<td width=\"68%\" class=\"row2\"><input type=\"text\" name=\"username\" class=\"required\" title=\"Enter your username. This is a required field\" size=\"20\" maxlength=\"40\" value=\"\" /></td>
							</tr>
							<tr>
								<td width=\"32%\" class=\"row1\">Password: </td>
								<td width=\"68%\" class=\"row2\"><input type=\"password\" name=\"password\" class=\"required\" title=\"Enter your password. This is a required field\" size=\"20\" maxlength=\"25\" /></td>
							</tr>
							<tr>
								<td width=\"100%\" colspan=\"2\" class=\"row1\">
									<span class=\"center\"><input type=\"submit\" class=\"button\" name=\"login\" value=\"Login\" /><input type=\"checkbox\" class=\"check\" name=\"autologin\" border=\"0\" value=\"ON\" checked /> Stay logged in</span>
								</td>
							</tr>
						</table>
					</form>
					<script type=\"text/javascript\">
						var valid = new Validation('loginForm', {immediate : true, useTitles:true});
					</script>";


	}
}
unset($_POST['password']); //weve finished registering the session variables le them pass so they dont get reregistered

$page->setTemplateVar('PageContent', $content);	

?>