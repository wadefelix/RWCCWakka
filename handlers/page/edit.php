<div class = "page">
<script type="text/javascript"><!--
/*
 * Tasks to perform if a user attempts to leave the page.
 */
  window.onbeforeunload =function(e){return ssvchk(e);}
-->
</script>
<?php
$toolbar="<script type=\"text/javascript\"><!--
insertButton('"."images/tb/date.png"."','"._MI_DATE."','====".date("F j, Y")."====\\n','');
insertButton('"."images/tb/tab.png"."','"._MI_TAB."','\t','');
formatButton('"."images/tb/fonth1.png"."','H1','====== ',' ======\\n','','1');
formatButton('"."images/tb/fonth2.png"."','H2','===== ',' =====\\n','','2');
formatButton('"."images/tb/fonth3.png"."','H3','==== ',' ====\\n','','3');
formatButton('"."images/tb/fonth4.png"."','H4','=== ',' ===\\n','','4');
formatButton('"."images/tb/fonth5.png"."','H5','== ',' ==\\n','','5');
formatButton('"."images/tb/cite.png"."','"._MI_CITE."','[quote]\\n','\\n[/quote]\\n','','q');
formatButton('images/tb/bold.png','"._MI_BOLD_TEXT."','**','**','Bold Text','b');
formatButton('images/tb/italic.png','"._MI_ITALIC_TEXT."',\"\/\/\",\"\/\/\",'Italic Text','i');
formatButton('images/tb/underline.png','"._MI_UNDERLINED_TEXT."','__','__','Underlined Text','u');
formatButton('images/tb/code.png','"._MI_CODE_TEXT."','%%','%%','Code Text','c');
formatButton('images/tb/strike.png','"._MI_STRIKE_THROUGH_TEXT."','[del]','[\/del]','Strike-through Text','d');
formatButton('images/tb/link.png','"._MI_LINK."','[[',']]','SandBox');
insertButton('images/tb/rule.png','"._MI_HORIZONTAL_RULE."','----\\n');
--></script>
";
    //Tweaked and Internationalized By:
    //CooCooWakka http://www.hsfz.net.cn/coo/wiki
    //CooYip cooyeah@hotmail.com
    if ($this->HasAccess("write") && $this->HasAccess("read")) {
        if ($_POST) {
            // only if saving:
            if ($_POST["submit"] == _MI_STORE) {
                // check for overwriting
                if ($this->page) {
                    if ($this->page["id"] != $_POST["previous"]) {
                        $error = /*"OVERWRITE ALERT: This page was modified by someone else while you were editing it.<br />\nPlease copy your changes and re-edit this page."*/_MI_OVERWRITE_ALERT;
                        $hr = $this->Format("----\n","wakka");
                        $orig = "$hr<textarea name=\"OriginalBody\" style=\"width: 100%; height: 400px\">".htmlspecialchars($_POST["body"])."</textarea>$hr";
                        $_POST["previous"] = $this->page["id"];
                    }
                }
                // store
                if (!$error) {
                    $body = str_replace("\r", "", $_POST["body"]);
                    $note = $_POST["note"];
                    if ($_POST["tiny"] == "on")
                    $tinychange = "Y";
                    else
                        $tinychange = "N";
                     
                    //$aliasname = $_POST["aliasname"];
                    //I think it is too ugly to use the following way to log the changes.
                    /*  if($aliasname!=$this->page["aliasname"]){
                    //        if(!$oldalias=$this->page["aliasname"])$oldalias=_MI_NONE;
                    $oldalias=$this->page["aliasname"]);
                    $note.="|".sprintf(_MI_ALIAS_TO,"\"$oldalias\"","\"$aliasname\"")."|";
                    }*/
                    /*       if ($newcategory = $_POST["newcategory"]) {
                    //                    if(!$oldcategory=$this->$this->page['category'])$oldcategory=_MI_NONE;
                    $oldcategory=$this->$this->page['category'];
                    $note.="|".sprintf(_MI_CATEGORY_TO,"\"$oldcategory\"","\"$newcategory\"")."|";
                    }
                    */
                     
                    // add page (revisions)
                    if ($body <> $this->page['body']) {
                        //check if there's difference
                        if(!$this->SavePage($this->GetPageTag(), $body, "", $tinychange, $note)){
				$this->Redirect($this->href());
			}
                    }
		    /*
                    if ($newcategory = $_POST["newcategory"]) {
                        $this->SetPageCategory($this->GetPageTag(), $newcategory);
                        $message = _MI_CATEGORY_UPDATED;
                        $this->SetMessage($message."!");
                    }
		    */
                    // now we render it internally so we can write the updated link table.
                    $this->ClearLinkTable();
                    $this->StartLinkTracking();
                    //$dummy = $this->Header();
                    $dummy .= $this->Format($body);
                    //$dummy .= $this->Footer();
                    $this->StopLinkTracking();
                    $this->WriteLinkTable();
                    $this->ClearLinkTable();
                    $this->StartLinkTracking();
                    //$dummy = $this->Header();
                    $dummy .= $this->Format($this->config['logged_in_navigation_links']);
                    //$dummy .= $this->Footer();
                    $this->StopLinkTracking();
                    $this->WriteLinkTable("CCW_SYS_VIRTUAL_PAGE1");
                    $this->ClearLinkTable();
                    // purge old page revisions
                    if ($days = $this->GetConfigValue("pages_purge_time")) {
                    $this->Query("delete from ".$this->config["table_prefix"]."pages where time < date_sub(now(),interval '".mysqli_real_escape_string($this->dblink,$days)."' day) and latest = 'N'");
                    }
                    // forward
                    $this->Redirect($this->href());
                }
            }
        }
         
        // fetch fields
        if (!$previous = $_POST["previous"])
            $previous = $this->page["id"];
        if (!$body = $_POST["body"])
            $body = $this->page["body"];
        $note = $_POST["note"];
        if (!$aliasname = $_POST["aliasname"])
        $aliasname = $this->page["aliasname"];
         
        //if(!$_POST["tiny"]);
        // $tinychange=1;
        //  if($tinychange=="Y")
        //   $tinychange=1;
        //  else
        //   $tinychange=0;
        //echo $tinychange;
        // preview?
        if ($_POST["submit"] == _MI_PREVIEW) {
            $output .= "<script type=\"text/javascript\"><!--\ntextChanged=true;\n--></script>";
            $previewButtons = "<input name=\"submit\" type=\"submit\" value=\""._MI_STORE."\" onclick=\"textChanged=false\" onkeypress=\"textChanged=false\" accesskey=\"s\"  title=\"[ALT+S]\" />\n". "<input name=\"submit\" type=\"submit\" value=\""._MI_REEDIT."\" onclick=\"textChanged=false\" onkeypress=\"textChanged=false\" accesskey=\"p\"  title=\"[ALT+P]\" />\n". "<input type=\"hidden\" name=\"note\" value=\"".htmlspecialchars($_POST["note"])."\" />\n". "<input type=\"hidden\" name=\"tiny\" value=\"".$_POST["tiny"]."\" />\n". "<input type=\"button\" value=\""._MI_CANCEL."\" onClick=\"document.location='".$this->href("")."';\" />\n";
            $output .= "<div class=\"commentinfo\">"._MI_PREVIEW."<br />".(htmlspecialchars($_POST["note"])?_MI_SUMMARY.": ".htmlspecialchars($_POST["note"]):"")."</div>\n"; 
            $output .= $this->FormOpen("edit")."\n". "<input type=\"hidden\" name=\"previous\" value=\"".$previous."\" />\n". "<input type=\"hidden\" name=\"body\" value=\"".htmlspecialchars($body)."\" />\n";
             
            $output .= $this->Format($this->PrePBody($body));
             
            $output .= "<br />\n". $previewButtons. $this->FormClose()."\n";
        } else {
            // display form
            if ($error) {
                $output .= "<div class=\"error\">$error</div>\n";
            }
            if ($orig) {
                $output .= $orig;
                $body = $this->page["body"];
            }
             
            // append a comment?
            if ($_REQUEST["appendcomment"]) {
                $body = trim($body)."\n\n----\n\n--".$this->UserName()." (".strftime("%c").")";
            }
            $output .= $this->format(_MI_EDIT_ADVICE);
            $output .= $this->FormOpen("edit"). "<input type=\"hidden\" name=\"previous\" value=\"".$previous."\" />\n".
		$toolbar."<textarea onKeyDown=\"fKeyDown(event)\" id=\"body\" name=\"body\" style=\"width: 100%; height: 400px\" onchange=\"textChanged = true;\" class=\"editbox\">".htmlspecialchars($body)."</textarea>\n".
		"<div style=\"float:right\"><script type=\"text/javascript\"><!--\nshowSizeCtl();\n--></script></div>".
		"<p>"._MI_SUMMARY.": <input tabindex=2 type=text value=\"".$note."\" name=\"note\" maxlength=80 size=60 /></p>".

		"<p><input tabindex=3 type=checkbox name=\"tiny\" ".($_POST["tiny"] == "on"?"checked":"")."/>"./*This is a minor edit*/_MI_MINOR_EDIT."</p>".
            "<p><input name=\"submit\" type=\"submit\" value=\""._MI_STORE."\" accesskey=\"s\"  title=\"[ALT+S]\" onclick=\"textChanged=false\" onkeypress=\"textChanged=false\" /> <input name=\"submit\" onclick=\"textChanged=false\" onkeypress=\"textChanged=false\" type=\"submit\" value=\""._MI_PREVIEW."\" title=\"[ALT+P]\" accesskey=\"p\" /> <input type=\"button\" value=\""._MI_CANCEL."\" onClick=\"document.location='".$this->href("")."';\" /></p>\n".  
             
             
            $this->FormClose();
        }
         
         
        print($output);
    } else {
        print("<em>"/*You don't have write access to this page.*/._MI_NO_WRITE_ACCESS."</em>");
    }
?>
</div>
