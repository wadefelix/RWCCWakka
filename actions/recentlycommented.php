<?php
    if ($user = $this->GetUser())
      {
        $max = $user["changescount"];
      }
    else
      {
        $max = 30;
      }
if ($pages = $this->LoadRecentComments_all())
  {
    foreach ($pages as $page)
    {
      // day header
        
      if ($max--<1)break;
      list($day, $time) = explode(" ", $page["time"]);
      if ($day != $curday)
        {
          if ($curday)
            print("<br />\n");
          print("<strong>$day:</strong><br />\n");
          $curday = $day;
        }

      // print entry
      print("&nbsp;&nbsp;&nbsp;(".$page["time"].") <a href=\"".$this->href("", $page["comment_on"], "show_comments=1")."#".$page["tag"]."\">".$this->GetDisplayName($page["comment_on"])."</a> . . . .".(/*$page["latest"]=="Y"*/false?" latest":"")
._MI_COMMENTBY/*" comment by "*/.$this->Format($page["user"]).($page["isnew"]=='Y'?"":"&nbsp;<span class=\"editcomment\">"._MI_EDIT_COMMENT."</span>")."<br />\n");
    }
  }
else
  {
    print("<em>"./*No recently commented page.*/_MI_NO_RECENT_COMMENT."</em>");
  }

?>
