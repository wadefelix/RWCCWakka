<?php

//print("<xmp>"); print_r($_REQUEST); exit;

if ($this->HasAccess("comment"))
  {
    // find number

    // $tag_prefix="Comment_".$this->tag."_";
    if ($latestComments = $this->LoadAll("select tag, id from ".$this->config["table_prefix"]."pages where comment_on !='' order by id desc"))
      {
        function LoadComments_SortComment_add($a,$b){
                return -1*strnatcmp($a['tag'],$b['tag']);

        }

        usort($latestComments,LoadComments_SortComment_add);
        $latestComment=$latestComments[0];
        preg_match("/^Comment([0-9]+)$/", $latestComment["tag"], $matches);
        $num = $matches[1] + 1;
      }
    else
      {
        $num = "1";
      }

    $body = trim($_POST["body"]);
    if (!$body)
      {
        $this->SetMessage(/*"Comment body was empty -- not saved!"*/_MI_COMMENTEMPTY);
      }
    else
      {
        // store new comment
        $this->SavePage("Comment".$num, $body, $this->tag);
	
      }


    // redirect to page
    $this->redirect($this->href());
  }
else
  {
    print("<div class=\"page\"><em>"./*Sorry, you're not allowed to post comments to this page.*/_MI_NO_COMMENT_ACCESS."</em></div>\n");
  }

?>
