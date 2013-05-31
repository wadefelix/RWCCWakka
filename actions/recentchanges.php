<?php
    $IsAdmin=$this->IsAdmin();
    $hardmax=300; 
    if (/*$_GET["mode"]=="all"*/true){
    $pages = $this->LoadRecentlyChanged_all($hardmax);
    $all = 1;
} else {
    $pages = $this->LoadRecentlyChanged();
    $all = 0;
}
if ($pages) {
    print("<a href=\"".$this->href("", "xml/recentchanges_".preg_replace("/[^a-zA-Z0-9]/", "", strtolower($this->GetConfigValue("wakka_name"))).".xml")."\"><img src=\"".$this->href("", "xml/xml.gif")."\" width=\"36\" height=\"14\" style=\"border : 0px;\" alt=\"XML\" /></a><br /><br />");
     
    if ($user = $this->GetUser()) {
        $max = $user["changescount"];
    } else {
        $max = 50;
    }
    if ($all)$max = $max;
    for($i = 0; $i < count($pages); $i++) {
        $page = $pages[$i];
        if ((($i <= $max) || !$max) && $i <=$hardmax) {
            // day header
            list($day, $time) = explode(" ", $page["time"]);
            $k = 1;
            if (!$curday) {
                print("<b>$day:</b><br />\n");
                $curday = $day;
            }
            while ($i+$k < count($pages) && $pages[$i+$k]['user'] == $page['user'] && $pages[$i+$k]['tag'] == $page['tag'] && $i+$k<=$hardmax && !($page['note'] && $pages[$i+$k]['note']) ) {
                list($nextday, $nexttime) = explode(" ", $pages[$i+$k]['time']);
                if ($nextday != $day)break;
                if (!$page['note'])$page['note'] = $pages[$i+$k]['note'];
		if($pages[$i+$k]["tinychange"]=='N')$page["tinychange"]='N';
		if($pages[$i+$k]["isnew"]=='Y')$page["isnew"]='Y';
		$k++;
            }
            if ($day != $curday) {
                //modify by cooyeah
                if ($curday)
                    print("<br />\n");
                print("<b>$day:</b><br />\n");
                $curday = $day;
            }
             
            // print entry
            $extra = ($page["tinychange"] == "Y"?"&nbsp;<span class=\"minor\">"._MI_MINOR."</span>":"");
            $extra .= ($page["isnew"] == "Y"?"&nbsp;<span class=\"new\">"./*new*/_MI_NEW."</span>":"");
            $extra .= ($page["note"] == ""?"":"&nbsp;<span class=\"note\">(".htmlspecialchars($page["note"]).")</span>");
            print("&nbsp;&nbsp;&nbsp;(".$page["time"].") (".$this->Link($page["tag"], "revisions", _MI_HISTORY, 0). "|<a href=\"".$this->Href("diff", $page["tag"], "a=".$page['id']."&amp;b=".$this->GetLastDiffID($page['tag'], $pages[$i+$k-1]['id']))."\">"._MI_DIFF."</a>".($IsAdmin?"|<a href=\"".$this->Href("manage", $page["tag"])."\">*</a>":"").") ".$this->Link($page["tag"], "", "", 0).
	    ($k > 1?" ($k ".$this->Link($page['tag'],"revisions",_MI_CHANGES,0).")":"")." . . . . ".$this->Format($page["user"]).$extra."<br />\n");
             
        }else break;
	$i=$i-1+$k;
	$max+=$k-1;
    }
}
 
?>
