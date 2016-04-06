<div class="page">
<?php
//Tweaked and Internationalized By:
//CooCooWakka http://www.hsfz.net.cn/coo/wiki
//CooYip cooyeah@hotmail.com

$this->nofollow=true;

// purge referrers
    if ($days = $this->GetConfigValue("referrers_purge_time")) {
        $this->Query("delete from ".$this->config["table_prefix"]."referrers where time < date_sub(now(), interval '".mysqli_real_escape_string($this->dblink,$days)."' day)");
    }

                 if ($global = $_REQUEST["global"])
               {
                 //$title = "Domains/sites linking to this Wakka (<a href=\"".$this->href("referrers", "", "global=1")."\">see list of different URLs</a>):";
$title=_MI_SITESLINKINGHERE."(<a href=\"".$this->href("referrers", "", "global=1")."\">"._MI_LISTURL."</a>):";
                 $referrers = $this->LoadReferrers();
               }
             else
               {
         //        $title = "Domains/sites pages linking to ".$this->Link($this->GetPageTag()).
           //         ($this->GetConfigValue("referrers_purge_time") ? " (last ".($this->GetConfigValue("referrers_purge_time") == 1 ? "24 hours" : $this->GetConfigValue("referrers_purge_time")." days").")" : "")." (<a href=\"".$this->href("referrers")."\">see list of different URLs</a>):";
					$title=_MI_SITESLINKINGHERE.$this->Link($this->GetPageTag()).
                    ($this->GetConfigValue("referrers_purge_time") ? " ("._MI_LAST." ".($this->GetConfigValue("referrers_purge_time") == 1 ? "24 "._MI_HOURS : $this->GetConfigValue("referrers_purge_time")." "._MI_DAYS).")" : "")." (<a href=\"".$this->href("referrers")."\">"._MI_LISTURL."</a>):";
                 $referrers = $this->LoadReferrers($this->GetPageTag());
               }

           print("<strong>$title</strong><br /><br />\n");
if ($referrers)
  {
    for ($a = 0; $a < count($referrers); $a++)
      {
        $temp_parse_url = parse_url($referrers[$a]["referrer"]);
        $temp_parse_url = ($temp_parse_url["host"] != "") ? strtolower(preg_replace("/^www\./Ui", "", $temp_parse_url["host"])) : "unknown";

        if (isset($referrer_sites["$temp_parse_url"]))
          {
            $referrer_sites["$temp_parse_url"] += $referrers[$a]["num"];
          }
        else
          {
            $referrer_sites["$temp_parse_url"] = $referrers[$a]["num"];
          }
      }

    array_multisort($referrer_sites, SORT_DESC, SORT_NUMERIC);
    reset($referrer_sites);

    print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
    foreach ($referrer_sites as $site => $site_count)
    {
      print("<tr>");
      print("<td width=\"30\" align=\"right\" valign=\"top\" style=\"padding-right: 10px\">$site_count</td>");
      print("<td valign=\"top\">" . (($site != "unknown") ? "<a href=\"http://$site\" rel=\"nofollow\" >$site</a>" : $site) . "</td>");
      print("</tr>\n");
    }
    print("</table>\n");
  }
else
  {
    print("<em>"._MI_NONE."</em><br />\n");
  }

if ($global)
  {
   //print("<br />[<a href=\"".$this->href("referrers_sites")."\">View referring sites for ".$this->GetPageTag()." only</a> | <a href=\"".$this->href("referrers")."\">View referrers for ".$this->GetPageTag()." only</a>]");
	  $link1=sprintf(_MI_VIEW_REFERRERS_PAGE,$this->href("referrers_sites"),$this->href("referrers"),$this->GetPageTag());
//    print("<br />[<a href=\"".$this->href("referrers_sites")."\">View referring sites for ".$this->GetPageTag()." only</a> | <a href=\"".$this->href("referrers")."\">View referrers for ".$this->GetPageTag()." only</a>]");
	print("<br />".$link1);
  }
else
  {
    //print("<br />[<a href=\"".$this->href("referrers_sites", "", "global=1")."\">View global referring sites</a> | <a href=\"".$this->href("referrers", "", "global=1")."\">View global referrers</a>]");
	  	$link1=sprintf(_MI_VIEW_REFERRERS_GLOBAL,$this->href("referrers_sites", "", "global=1"),$this->href("referrers", "", "global=1"));

//    print("<br />[<a href=\"".$this->href("referrers_sites", "", "global=1")."\">View global referring sites</a> | <a href=\"".$this->href("referrers", "", "global=1")."\">View global referrers</a>]");
	print("<br />".$link1);
  }

$this->nofollow=false;
?>
</div>
