<div class = "page">
<?php
    //Tweaked and Internationalized By:
    //CooCooWakka http://www.hsfz.net.cn/coo/wiki
    //CooYip cooyeah@hotmail.com
    $this->nofollow=true;
    if ($this->HasAccess("read")) {
        if (!$this->page) {
            //                     print("This page doesn't exist yet. Maybe you want to <a href=\"".$this->href("edit")."\">create</a> it?");
            printf(_MI_NOTEXIST, $this->href("edit"));
        } else {
            // Log to the previous page
            if($_SEESION['now_page']!=$this->GetPageTag()){
                $_SESSION['prev_page']=$_SESSION['now_page'];
                $_SESSION['now_page']=$this->GetPageTag();
            }
            // comment header?
            if ($this->page["comment_on"]) {
                //  print("<div class=\"commentinfo\">This is a comment on ".$this->Link($this->page["comment_on"], "", "", 0).", posted by ".$this->Format($this->page["user"])." at ".$this->page["time"]."</div>");
                printf("<div class=\"commentinfo\">"._MI_THIS_COMMENT."</div>", $this->Link($this->page["comment_on"], "", "", 0), $this->Format($this->page["user"]), $this->page["time"]);
            }
             
            if ($this->page["latest"] == "N") {
                print("<div class=\"revisioninfo\">"./*This is an old revision of */_MI_THIS_OLDREVISION."<a href=\"".$this->href()."\">".$this->GetPageTag()."</a>"._MI_FROM.$this->page["time"].".</div>");
            }
             
             
            // display page
            $this->env['para_edit']=false;
            $pbody = $this->Format($this->page["body"], "wakka");
            $this->env['para_edit']=false;
            $pedit_html="<a href=\"".$this->Href("edit","","para=")."\\1\">edit</a>";
            $pbody = preg_replace("#-\rspara([0-9]+)\r\|#",$pedit_html,$pbody);
            print($pbody);
            $this->CountView($this->page);
            // if this is an old revision, display some buttons
            if ($this->HasAccess("write") && ($this->page["latest"] == "N")) {
                $latest = $this->LoadPage($this->tag);
            ?>
<br />
<?php echo $this->FormOpen("edit") ?>
<input type="hidden" name="previous" value="<?php echo $latest["id"] ?>" />
<input type="hidden" name="body" value="<?php echo htmlspecialchars($this->page["body"]) ?>" />
<input type="hidden" name="aliasname" value="<?php echo $this->page["aliasname"];?>" />
<input type="submit" value="<?php  /*Re-edit this old revision*/echo _MI_REEDIT_OLD;?>" />
<?php echo $this->FormClose();?>
            <?php
            }
        }
    } else {
        print("<em>"./*You aren't allowed to read this page.*/_MI_NO_READ_ACCESS."</em>");
    }
?>
</div>


<?php
    if ($this->HasAccess("read") && $this->GetConfigValue("hide_comments") != 1) {
        
        // load comments for this page
        $comments = $this->LoadComments($this->tag);
        
        // store comments display in session
        $tag = $this->GetPageTag();
        if (!isset($_SESSION["show_comments"][$tag]))
            $_SESSION["show_comments"][$tag] = ($this->UserWantsComments() ? "1" : "0");
         
        switch($_REQUEST["show_comments"]) {
            case "0":
            $_SESSION["show_comments"][$tag] = 0;
            break;
            case "1":
            $_SESSION["show_comments"][$tag] = 1;
            break;
        }
         
        // display comments!
        if ($this->page && $_SESSION["show_comments"][$tag]) {
            // display comments header
        ?>
<a name="comments"></a>
<div class="commentsheader">
<?php  echo _MI_COMMENT ?> <span class="commentctl">[<a href="<?php echo $this->href("", "", "show_comments=0") ?>"><?php  echo _MI_HIDECOMMENT/*Hide comments/form*/ ?></a>]</span>
</div>
        <?php
            // display comments themselves
            if ($comments) {
                foreach ($comments as $comment) {
                    print("<a name=\"".$comment["tag"]."\"></a>\n");
                    print("<div class=\"comment\">\n");
                    print($this->Format($comment["body"])."\n");
                    print("<div class=\"commentinfo\">\n-- ".$this->Format($comment["owner"])." (".$comment["time"].")".($this->IsManager("",$comment['tag'])?"&nbsp;<span class=\"commentctl\">[<a href=\"".$this->Href("manage",$comment['tag'])."\">*</a>]</span>":"")."\n</div>\n");
                    print("</div>\n");
                }
            }
             
            // display comment form
            print("<div class=\"commentform\">\n");
            if ($this->HasAccess("comment")) {
            ?>
<?php  /*Attach a comment to this page*/echo _MI_ADDCOMMENT?>:<br />
<?php echo $this->FormOpen("addcomment");?>
<textarea name="body" rows="6" style="width: 100%"></textarea><br />
<input type="submit" value="<?php echo _MI_SUBMITCOMMENT ?>" accesskey="s" />
<?php echo $this->FormClose();?>
            <?php
            }
            print("</div>\n");
        } else {
        ?>
<div class="commentsheader"><span class="commentctl">
        <?php
            switch (count($comments)) {
                case 0:
                print(/*"There is no comment on this page."*/_MI_NOCOMMENT);
                break;
                case 1:
                print(/*"There is one comment on this page."*/_MI_ONECOMMENT);
                break;
                default:
                printf(/*"There are ".count($comments)." comments on this page."*/_MI_MORECOMMENT,count($comments));
            }
        ?>
[<a href="<?php echo $this->href("", "", "show_comments=1#comments") ?>"><?php  /*Display comments/form*/echo _MI_DISPLAYCOMMENT ?></a>]</span>             </div>
        <?php
        }
    }
$this->nofollow=false;
?>
