<?php
    if ($_REQUEST["action"] == "logout") {
        $this->LogoutUser();
        $this->SetMessage(/*"You are now logged out."*/_MI_YOU_LOGOUT);
        $this->Redirect($this->href());
    }
    else if ($user = $this->GetUser()) {
         
        // is user trying to update?
        if ($_REQUEST["action"] == "update") {
            $this->Query("update ".$this->config["table_prefix"]."users set ". "email = '".mysql_escape_string($_POST["email"])."', ". "doubleclickedit = '".mysql_escape_string($_POST["doubleclickedit"])."', ". "show_comments = '".mysql_escape_string($_POST["show_comments"])."', ". "revisioncount = '".mysql_escape_string($_POST["revisioncount"])."', ". "changescount = '".mysql_escape_string($_POST["changescount"])."', ". "motto = '".mysql_escape_string($_POST["motto"])."' ". "where name = '".$user["name"]."' limit 1");
             
            $this->SetUser($this->LoadUser($user["name"]));
             
            // forward
            $this->SetMessage(/*"User settings stored!"*/_MI_USERSETTING_STORE);
            $this->Redirect($this->href());
        }
         
        // user is logged in; display config form
        print($this->FormOpen());
    ?>
 <input type="hidden" name="action" value="update" /> 
<table> 
  <tr> 
    <td align="right"></td> 
    <td><?php echo _MI_YOUARE.$this->Link($user["name"]) ?>!</td> 
  </tr> 
  <tr> 
    <td align="right"><?php  /*Your email address*/echo _MI_YOUR_EMAIL ?> 
      :</td> 
    <td><input name="email" value="<?php echo htmlentities($user["email"]) ?>" size="40" /></td> 
  </tr> 
  <tr> 
    <td align="right"><?php  /*Doubleclick Editing*/echo _MI_DCLICK_EDIT ?> 
      :</td> 
    <td><input type="hidden" name="doubleclickedit" value="N"> 
      <input type="checkbox" name="doubleclickedit" value="Y" <?php echo $user["doubleclickedit"] == "Y" ? "checked" : "" ?> /></td> 
  </tr> 
  <tr> 
    <td align="right"><?php  /*Show comments by default*/ echo _MI_SHOWCOMMENT_DEF ?> 
      :</td> 
    <td><input type="hidden" name="show_comments" value="N"> 
      <input type="checkbox" name="show_comments" value="Y" <?php echo $user["show_comments"] == "Y" ? "checked" : "" ?> /></td> 
  </tr> 
  <tr> 
    <td align="right"><?php  /*Recent changes limit*/ echo _MI_RECENTCHANGES_LIMIT?> 
      :</td> 
    <td><input name="changescount" value="<?php echo htmlentities($user["changescount"]) ?>" size="40" /></td> 
  </tr> 
  <tr> 
    <td align="right"><?php  /*Revision list limit*/echo _MI_REVISION_LIMIT ?> 
      :</td> 
    <td><input name="revisioncount" value="<?php echo htmlentities($user["revisioncount"]) ?>" size="40" /></td> 
  </tr> 
  <tr> 
    <td align="right"><?php  /*Your motto*/ echo _MI_YOURMOTTO ?> 
      :</td> 
    <td><input name="motto" value="<?php echo htmlspecialchars($user["motto"]) ?>" size="40" /></td> 
  </tr> 
  <tr> 
    <td></td> 
    <td><input type="submit" value="<?php  echo /*Update Settings*/_MI_UPDATE_SETTINGS?>" /> 
      <input type="button" value="<?php   echo /*Logout*/_MI_LOGOUT ?>" onClick="document.location='<?php echo $this->href("", "", "action=logout"); ?>'" /></td> 
  </tr> 
</table> 
<br /> 
    <?php
        /*See a list of pages you own (<a href="<?php echo $this->href("", "MyPages"); ?>">MyPages</a>) and pages you've edited (<a href="<?php echo $this->href("", "MyChanges"); ?>">MyChanges</a>).<br />
        */
        //printf((_MI_SEE_YOUROWN)."<br />",$this->href("", "MyPages"),$this->href("", "MyChanges"));
        print($this->format(_MI_SEE_YOUROWN));
        print($this->FormClose());
    } else {
        // user is not logged in
         
        // is user trying to log in or register?
        if ($_REQUEST["action"] == "login") {
            // if user name already exists, check password
            if ($existingUser = $this->LoadUser($_POST["name"])) {
                // check password
                if ($existingUser["password"] == md5($_POST["password"])) {
                    $this->SetUser($existingUser,$_POST['rememberme']);
                    $this->Redirect($this->href());
                } else {
                    $error = _MI_ERROR_PASSWD;//"Wrong password!";
                }
            }
            // otherwise, create new account
            else if($this->GetConfigValue("open_register"))
                {
                $name = trim($_POST["name"]);
                $email = trim($_POST["email"]);
                $password = $_POST["password"];
                $confpassword = $_POST["confpassword"];
                 
                // check if name is WikkiName style
                if (!$this->IsWikiName($name)) $error = _MI_ERROR_NAMEFMT;//"User name must be WikiName formatted!";
                else if (!$email) $error = _MI_ERROR_NOEMAIL;//"You must specify an email address.";
                else if (!preg_match("/^.+?\@.+?\..+$/", $email)) $error = _MI_ERROR_EMAILFMT;//"That doesn't quite look like an email address.";
                else if ($confpassword != $password) $error = _MI_ERROR_PWDMATCH;//;"Passwords didn't match.";
                else if (preg_match("/ /", $password)) $error = _MI_ERROR_PWDSPACE;//"Spaces aren't allowed in passwords.";
                else if (strlen($password) < 5) $error = _MI_ERROR_PWDTOOSHORT;//"Password too short.";
                else
                    {
                    $this->Query("insert into ".$this->config["table_prefix"]."users set ". "signuptime = now(), ". "name = '".mysql_escape_string($name)."', ". "email = '".mysql_escape_string($email)."', ". "password = md5('".mysql_escape_string($_POST["password"])."')");
                     
                    // log in
                    $this->SetUser($this->LoadUser($name));
                     
                    // forward
                    $this->Redirect($this->href());
                }
            }
        }
        //modify by cooyeah May 27 2003
         
        print($this->FormOpen());
    ?>
<input type="hidden" name="action" value="login" /> 
<table> 
  <tr> 
    <td align="right"></td> 
    <td><?php echo $this->Format(_MI_PLEASELOGIN/*"If you're already a registered user, log in here!"*/); ?></td> 
  </tr> 
    <?php
        if ($error) {
            print("<tr><td></td><td><div class=\"error\">".$this->Format($error)."</div></td></tr>\n");
        }
    ?>
  <tr> 
    <td align="right"><?php  /*Your WikiName:*/ echo _MI_YOURWIKINAME ?></td> 
    <td><input name="name" size="40" value="<?php echo $name ?>" /></td> 
  </tr> 
  <tr> 
    <td align="right"><?php  /*Password (5+ chars):*/ echo _MI_PASSWORDHERE?></td> 
    <td><input type="password" name="password" size="40" /></td> 
  </tr> 
  <tr> 
    <td align="right">&nbsp;</td> 
    <td><input type="checkbox" name="rememberme" value="1" /><?php  echo _MI_REMEMBER_ME ?></td> 
  </tr> 
  <tr> 
    <td></td> 
    <td><input type="submit" value="<?php  echo _MI_LOGIN_REG /*Login / Register*/ ?>" size="40" /></td> 
  </tr>
<? if(!$this->GetConfigValue("open_register"))echo _MI_CLOSEREGISTER ?>
<? if($this->GetConfigValue("open_register")){ ?> 
  <tr> 
    <td align="right"></td> 
    <td width="500"><?php echo $this->Format(_MI_REGTIPS/*"Stuff you only need to fill in when you're logging in for the first time (and thus signing up as a new user on this site)." */)?>
    </td> 
  </tr> 
  <tr> 
    <td align="right"><?php  /*Confirm password:*/ echo _MI_CONFIRMPWD ?></td> 
    <td><input type="password" name="confpassword" size="40" /></td> 
  </tr> 
  <tr> 
    <td align="right"><?php  echo _MI_EMAILADDR/*Email address:*/?></td> 
    <td><input name="email" size="40" value="<?php echo $email ?>" /></td> 
  </tr> 
  <tr> 
    <td></td> 
    <td><input type="submit" value="<?php  echo _MI_LOGIN_REG /*Login / Register*/?>" size="40" /></td> 
  </tr>
<? } ?> 
</table> 
    <?php
        print($this->FormClose());
    }
?>
