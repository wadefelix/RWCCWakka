<div class = "page">
<?php
    //Tweaked and Internationalized By:
    //CooCooWakka http://www.hsfz.net.cn/coo/wiki
    //CooYip cooyeah@gmail.com
    
    
     
    // purge referrers
    if ($days = $this->GetConfigValue("referrers_purge_time")) {
        $this->Query("delete from ".$this->config["table_prefix"]."referrers where time < date_sub(now(), interval '".mysql_escape_string($days)."' day)");
    }
     
    if ($global = $_REQUEST["global"]) {
        //$title = "Sites linking to this Wakka (<a href=\"".$this->href("referrers_sites", "", "global=1")."\">see list of domains</a>):";
        $title = _MI_SITESLINKINGHERE."(<a href=\"".$this->href("referrers_sites", "", "global=1")."\">"._MI_LISTDOMAIN."</a>):";
        $referrers = $this->LoadReferrers();
    } else {
        //$title = "External pages linking to ".$this->Link($this->GetPageTag()).
        $title = _MI_PAGELINK.$this->Link($this->GetPageTag()). ($this->GetConfigValue("referrers_purge_time") ? " ("._MI_LAST." ".($this->GetConfigValue("referrers_purge_time") == 1 ? "24 "._MI_HOURS : $this->GetConfigValue("referrers_purge_time")." "._MI_DAYS).")" : "")." (<a href=\"".$this->href("referrers_sites")."\">"._MI_LISTDOMAIN."</a>):";
        $referrers = $this->LoadReferrers($this->GetPageTag());
    }
     
    print("<strong>$title</strong><br /><br />\n");
    if ($referrers) {
    $today = date("Y-m-d");
        {
            print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
            foreach ($referrers as $referrer) {
                print("<tr>");
                print("<td width=\"30\" align=\"right\" valign=\"top\" style=\"padding-right: 10px\">".$referrer["num"]."</td>");
                if($today==date("Y-m-d",strtotime($referrer['time'])) && $this->GetConfigValue("referrers_purge_time")>1)
                print("<td valign=\"top\"><b><a rel=\"nofollow\" href=\"".$referrer["referrer"]."\">".$referrer["referrer"]."</a></b></td>");
                else
                print("<td valign=\"top\"><a rel=\"nofollow\" href=\"".$referrer["referrer"]."\">".$referrer["referrer"]."</a></td>");
                print("</tr>\n");
            }
            print("</table>\n");
        }
    } else {
        print("<em>"._MI_NONE."</em><br />\n");
    }
     
    if ($global) {
        $link1 = sprintf(_MI_VIEW_REFERRERS_PAGE, $this->href("referrers_sites"), $this->href("referrers"), $this->GetPageTag());
        //    print("<br />[<a href=\"".$this->href("referrers_sites")."\">View referring sites for ".$this->GetPageTag()." only</a> | <a href=\"".$this->href("referrers")."\">View referrers for ".$this->GetPageTag()." only</a>]");
        print("<br />".$link1);
    } else {
        $link1 = sprintf(_MI_VIEW_REFERRERS_GLOBAL, $this->href("referrers_sites", "", "global=1"), $this->href("referrers", "", "global=1"));
         
        //    print("<br />[<a href=\"".$this->href("referrers_sites", "", "global=1")."\">View global referring sites</a> | <a href=\"".$this->href("referrers", "", "global=1")."\">View global referrers</a>]");
        print("<br />".$link1);
    }
     
    
?>
</div>
