<div class = "page">
<?php
    //CooCooWakka  http://www.hsfz.net.cn/coo/wiki
    //CooYip cooyeah@hotmail.com
     
    if ($this->UserIsOwner() || $this->IsAdmin()) {
        if ($_POST) {
             
            $new_tag = $_POST["newtag"];
            $new_tag = trim($new_tag);
            if ($new_tag && $new_tag != $this->GetPageTag()) {
                //does page already exist
                $redirect_body = "{{redirect page=".$new_tag."}}";
                $redirect_body_back = "{{redirect page=".$this->GetPageTag()."}}";
                $revisions = $this->LoadRevisions($new_tag);
                if (count($revisions) == 0)$error = 0;
                elseif(count($revisions) > 1)$error = 1;
                elseif($revisions[0]['body'] == $redirect_body_back) {
                    $error = 0;//allow to move back.
                    $this->Query("update ".$this->GetConfigValue("table_prefix")."pages set latest='N' where tag='".mysqli_real_escape_string($this->dblink,$new_tag)."';");
                }
                else $error = 1;
                if ($error == 1) {
                    $message = _MI_ERROR_PAGEXIST;
                    $this->SetMessage($message);
                    $this->Redirect($this->Href("move", $this->GetPageTag()));
                } else {
                    //Move the latest edition first.
                    $this->Query("update ".$this->GetConfigValue("table_prefix")."pages set tag='".mysqli_real_escape_string($this->dblink,$new_tag)."' where latest='Y' and tag='".mysqli_real_escape_string($this->dblink,$this->GetPageTag())."';");
                    //Write redirection to old page.
                    $this->SavePage($this->GetPageTag(), $redirect_body, "", "Y", _MI_MOVE_TO.$new_tag);
                    //Move the old versions.
                    $this->Query("update ".$this->GetConfigValue("table_prefix")."pages set tag='".mysqli_real_escape_string($this->dblink,$new_tag)."' where latest!='Y'and tag='".mysqli_real_escape_string($this->dblink,$this->GetPageTag())."';");
                    //Move the comments
                    $this->Query("update ".$this->GetConfigValue("table_prefix")."pages set comment_on='".mysqli_real_escape_string($this->dblink,$new_tag)."' where comment_on='".mysqli_real_escape_string($this->dblink,$this->GetPageTag())."';");


                    //Modify the link tracking table
                    $this->Query("update ".$this->GetConfigValue("table_prefix")."links set from_tag='".mysqli_real_escape_string($this->dblink,$new_tag)."' where from_tag='".mysqli_real_escape_string($this->dblink,$this->GetPageTag())."';");
                    $this->Query("insert into ".$this->GetConfigValue("table_prefix")."links set from_tag='".mysqli_real_escape_string($this->dblink,$this->GetPageTag())."', to_tag='".mysqli_real_escape_string($this->dblink,$new_tag)."';");
                     
                    //Goto the new page
                    $this->Redirect($this->Href("", $new_tag));
                    
                }
            }
        }
    ?>
    <?php
        print($this->Format(_MI_MOVEPAGE_INTRO));
        //  print(_MI_MOVEPAGE_TO);
        print($this->FormOpen("move")."\n");
    ?>
		<?php  print(_MI_MOVEPAGE_TO) ?><input type="text" name="newtag"/>
		<input name="submit" type="submit" value="<?php echo _MI_MOVE ?>" />
	<?php 	print($this->FormClose());
}else{
 print("<em>"._MI_YOUARE_NOTOWNER/*You're not the owner of this page.*/."</em>");
}
?>
</div>
