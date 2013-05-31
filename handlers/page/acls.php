<div class="page">
                 <?php
//Tweaked and Internationalized By:
//CooCooWakka http://www.hsfz.net.cn/coo/wiki
//CooYip cooyeah@hotmail.com
                 if ($this->UserIsOwner() || $this->IsAdmin())
               {
                 if ($_POST)
                   {
                     // store lists
                     $this->SaveAcl($this->GetPageTag(), "read", $_POST["read_acl"]);
                     $this->SaveAcl($this->GetPageTag(), "write", $_POST["write_acl"]);
                     $this->SaveAcl($this->GetPageTag(), "comment", $_POST["comment_acl"]);
                     $message = _MI_MODMESSAGE;//"Access control lists updated";

                     // change owner?
                     if ($newowner = $_POST["newowner"])
                       {
                         $this->SetPageOwner($this->GetPageTag(), $newowner);
                         $message .= _MI_ACL_GIVEOWNERSHIP.$newowner;//" and gave ownership to ".$newowner;
                       }

                     // redirect back to page
                     $this->SetMessage($message."!");
                     $this->Redirect($this->Href());
                   }
                 else
                   {
                     // load acls
                     $readACL = $this->LoadAcl($this->GetPageTag(), "read");
                     $writeACL = $this->LoadAcl($this->GetPageTag(), "write");
                     $commentACL = $this->LoadAcl($this->GetPageTag(), "comment");

                     // show form
                     ?>
                     <h3><?php echo _MI_ACLSLIST.$this->Link($this->GetPageTag())
                       ?></h3>
                       <br />

                       <?php echo $this->FormOpen("acls") ?>
                       <table border="0" cellspacing="0" cellpadding="0">
                                                     <tr>
                                                     <td valign="top" style="padding-right: 20px">
                                                                            <strong><?php  echo _MI_READACL/*Read ACL:*/?></strong><br />
                                                                            <textarea name="read_acl" rows="4" cols="20"><?php echo $readACL["list"] ?></textarea>
                                                                                                                    <td>
                                                                                                                    <td valign="top" style="padding-right: 20px">
                                                                                                                                           <strong><?php  echo _MI_WRITEACL/*Write ACL:*/?></strong><br />
                                                                                                                                           <textarea name="write_acl" rows="4" cols="20"><?php echo $writeACL["list"] ?></textarea>
                                                                                                                                                                           <td>
                                                                                                                                                                           <td valign="top" style="padding-right: 20px">
                                                                                                                                                                                                  <strong><?php  echo _MI_COMMENTSACL/*Comments ACL:*/?></strong><br />
                                                                                                                                                                                                  <textarea name="comment_acl" rows="4" cols="20"><?php echo $commentACL["list"] ?></textarea>
                                                                                                                                                                                                                                    <td>
                                                                                                                                                                                                                                    </tr>
                                                                                                                                                                                                                                    <tr>
                                                                                                                                                                                                                                    <td colspan="3">
                                                                                                                                                                                                                                                <strong><?php  echo _MI_SETOWNER /*Set Owner:*/?></strong><br />
                                                                                                                                                                                                                                                <select name="newowner">
                                                                                                                                                                                                                                                             <option value=""><?php  echo _MI_NOCHANGE ?></option>
                                                                                                                                                                                                                                                                           <option value=""></option>
                                                                                                                                                                                                                                                                           <?php
                                                                                                                                                                                                                                                                           if ($users = $this->LoadUsers())
                                                                                                                                                                                                                                                                           {
                                                                                                                                                                                                                                                                           foreach($users as $user)
                                                                                                                                                                                                                                                                           {
                                                                                                                                                                                                                                                                           print("<option value=\"".htmlentities($user["name"])."\">".$user["name"]."</option>\n");
                                                                                                                                                                                                                                                                         }
                                                                                                                                                                                                                                                                         }
                                                                                                                                                                                                                                                                           ?>
                                                                                                                                                                                                                                                                           </select>
                                                                                                                                                                                                                                                                           <td>
                                                                                                                                                                                                                                                                           </tr>
                                                                                                                                                                                                                                                                           <tr>
                                                                                                                                                                                                                                                                           <td colspan="3">
                                                                                                                                                                                                                                                                           <br />
                                                                                                                                                                                                                                                                           <input type="submit" value="<?php  echo _MI_STOREACLS/*Store ACLs*/?>" style="width: 120px" accesskey="s" />
                                                                                                                                                                                                                                                                           <input type="button" value="<?php  echo _MI_CANCEL?>" onClick="history.back();" style="width: 120px" />&nbsp;&nbsp;&nbsp;<?php echo "<a href=\"".$this->href('move')."\">"._MI_MOVETHIS."</a>" ?>
                                                                                                                                                                                                                                                                           </td>
                                                                                                                                                                                                                                                                           </tr>
                                                                                                                                                                                                                                                                           </table>
                                                                                                                                                                                                                                                                           <?php
                                                                                                                                                                                                                                                                           print($this->FormClose());
                                                                                                                                                                                                                                                                         }
                                                                                                                                                                                                                                                                         }
                                                                                                                                                                                                                                                                           else
                                                                                                                                                                                                                                                                           {
                                                                                                                                                                                                                                                                           print("<em>"._MI_YOUARE_NOTOWNER/*You're not the owner of this page.*/."</em>");
                                                                                                                                                                                                                                                                     }

                                                                                                                                                                                                                                                                           ?>
                                                                                                                                                                                                                                                                           </div>
