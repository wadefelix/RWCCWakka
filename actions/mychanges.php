<?php
     
    // actions/mychanges.php
    // written by Carlo Zottmann
    // http://wakkawikki.com/CarloZottmann
    // modified by cooyeah
    // http://www.hsfz.net.cn/coo/wiki
    // CooCooWakka
    //Tweaked and Internationalized By:
    //CooCooWakka http://www.hsfz.net.cn/coo/wiki
    //CooYip cooyeah@hotmail.com
    include_once "ccwakka.php";
    if ($user = $this->GetUser()) {
        $my_edits_count = 0;
         
        if ($_REQUEST["bydate"] == 1) {
            //print("<strong>This is the list of pages you've edited, ordered by the time of your last change (<a href=\"".$this->href("", $tag)."\">order alphabetically</a>).</strong><br /><br />\n");
            print("<strong>"._MI_YOUREDIT."(<a href=\"".$this->href("", $tag)."\">"._MI_ORDERINDEX."</a>).</strong><br /><br />\n");
             
            if ($pages = $this->LoadAll("SELECT tag, time FROM ".$this->config["table_prefix"]."pages WHERE user = '".mysql_escape_string($this->UserName())."' AND comment_on='' ORDER BY time ASC, tag ASC")) {
                 
                foreach ($pages as $page) {
                    $edited_pages[$page["tag"]] = $page["time"];
                }
                arsort($edited_pages);
                //$edited_pages = array_reverse($edited_pages);
                 
                foreach ($edited_pages as $page["tag"] => $page["time"]) {
                    // day header
                    list($day, $time) = explode(" ", $page["time"]);
                    if ($day != $curday) {
                        if ($curday)
                            print("<br />\n");
                        print("<strong>$day:</strong><br />\n");
                        $curday = $day;
                    }
                     
                    // print entry
                    print("&nbsp;&nbsp;&nbsp;($time) (".$this->Link($page["tag"], "revisions", "history", 0).") ".$this->Link($page["tag"], "", "", 0)."<br />\n");
                     
                    $my_edits_count++;
                }
                 
                if ($my_edits_count == 0) {
                    print("<em>You didn't edit any pages yet.</em>");
                }
            } else {
                print("<em>No pages found.</em>");
            }
        } else {
            //        print("<strong>This is the list of pages you've edited, along with the time of your last change (<a href=\"".$this->href("", $tag,"bydate=1")."\">order by date</a>).</strong><br /><br />\n");
            print("<strong>"._MI_YOUREDIT."(<a href=\"".$this->href("", $tag, "bydate=1")."\">"._MI_ORDERDATE."</a>).</strong><br /><br />\n");
            if ($pages = $this->LoadAll("SELECT tag, time FROM ".$this->config["table_prefix"]."pages WHERE user = '".mysql_escape_string($this->UserName())."' AND tag NOT LIKE 'Comment%' ORDER BY tag ASC, time DESC")) {
                foreach ($pages as $page) {
                    if ($last_tag != $page["tag"]) {
                        $my_edits_count++;
                    }
                }
                cc_pageindex($pages, 2);
                if ($my_edits_count == 0) {
                    print("<em>"./*You didn't edit any pages yet.*/_MI_YOUNOEDIT."</em>");
                }
            } else {
                print("<em>"._MI_NOPAGE."</em>");
            }
        }
    } else {
        print("<em>"./*You're not logged in, thus the list of pages you've edited couldn't be retrieved.*/MI_CANNOT_RETRIEVE_EDIT."</em>");
    }
     
?>
