<div class="page">
                 <?php
				 //Tweaked and Internationalized By:
//CooCooWakka http://www.hsfz.net.cn/coo/wiki
//CooYip cooyeah@hotmail.com
                 if ($this->HasAccess("read"))
               {
                 // load revisions for this page
                 if ($pages = $this->LoadRevisions($this->tag))
                   {
                     $output .= $this->FormOpen("diff", "", "GET");
                     $output .= "<input type=\"submit\" value=\""._MI_SHOWDIFFERENCES."\" />\n";
                     $output .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"1\">\n";
                     if ($user = $this->GetUser())
                       {
                         $max = $user["revisioncount"];
                       }
                     else
                       {
                         $max = 20;
                       }

                     $c = 0;
                     foreach ($pages as $page)
                     {
                       $c++;
                       if (($c <= $max) || !$max)
                         {
		$extra=($page["tinychange"]=="Y"?"&nbsp;<span class=\"minor\">"._MI_MINOR."</span>":"");
                $extra.=($page["isnew"]=="Y"?"&nbsp;<span class=\"new\">"._MI_NEW."</span>":"");
                $extra.=($page["note"]==""?"":"&nbsp;<span class=\"note\">(".$page["note"].")<span>");
                           $output .= "<tr>";
                           $output .= "<td><input type=\"radio\" name=\"a\" value=\"".$page["id"]."\" ".($c == 1 ? "checked" : "")." /></td>";
                           $output .= "<td><input type=\"radio\" name=\"b\" value=\"".$page["id"]."\" ".($c == 2 ? "checked" : "")." /></td>";
                        $output .= "<td>&nbsp;<a href=\"".$this->href("show",$this->tag,"time=".urlencode($page["time"]))."\">".$page["time"]."</a></td>";   
			//$output .= "<td>&nbsp;<a href=\"".$this->href("show")."&time=".urlencode($page["time"])."\">".$page["time"]."</a></td>";//change ?time to &time
                           $output .= "<td>&nbsp;"._MI_BY." ".$this->Format($page["user"]).$extra."</td>";
                           $output .= "</tr>\n";
                         }
                     }
                     $output .= "</table><br />\n";
                     $output .= "<input type=\"button\" value=\""./*Return To Node / Cancel\*/_MI_RETURN."\" onClick=\"document.location='".$this->href("")."';\" />\n";
                     $output .= $this->FormClose()."\n";
                   }
                 print($output);
               }
             else
               {
                 print("<em>You aren't allowed to read this page.</em>");
               }
           ?>
           </div>
