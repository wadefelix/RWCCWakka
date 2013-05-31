<div class = "page">
<?php
    //Tweaked and Internationalized By:
    //CooCooWakka http://www.hsfz.net.cn/coo/wiki
    //CooYip cooyeah@hotmail.com
    require_once("ccwakka/ccwakka_diff2.php");
    
    //Compare Special Items
    $special_compare[1]['name']="aliasname";
    $special_compare[2]['name']="category";
    $special_compare[1]['label']=_MI_DISPLAYALIASNAME;
    $special_compare[2]['label']=_MI_SET_CATEGORY.":";
    $added=array();
    $deleted=array();
    if ($this->HasAccess("read")) {
         
        /* NOTE: This is a really cheap way to do it. I think it may be more intelligent to write the two
        pages to temporary files and run /usr/bin/diff over them. Then again, maybe not.           */
        if ("last" == $_REQUEST['type']) {
            $ids = $this->GetLastDiffID($this->tag);
            $id_a = $this->GetLatestPageID($this->tag);
            $id_b = $ids;
             
        } else {
            $id_a = $_REQUEST['a'];
            $id_b = $_REQUEST['b'];
        }
         
        // load pages
        $pageA = $this->LoadPageById($id_a);
        $pageB = $this->LoadPageById($id_b);
        
        // compare special items
        foreach($special_compare as $item){
            if($pageA[$item['name']] != $pageB[$item['name']]) {
                $added[]="//".$item['label']."// ".htmlspecialchars($pageA[$item['name']]);
                $deleted[]="//".$item['label']."// ".htmlspecialchars($pageB[$item['name']]);
            }
        }
        

 
        // compare body
        $bodyA=$pageA['body'];
        $bodyB=$pageB['body'];
        /*
        $result = TextDiff($bodyA, $bodyB);
         
        $added = array_merge($added,$result['added']);
        $deleted = array_merge($deleted,$result['deleted']);
        */ 
        $output .= "<strong>"._MI_COMPARING." <a href=\"".$this->href("", "", "time=".urlencode($pageA["time"]))."\">".$pageA["time"]."</a> "._MI_TO." <a href=\"".$this->href("", "", "time=".urlencode($pageB["time"]))."\">".$pageB["time"]."</a></strong><br />\n";
        $right="<a href=\"".$this->href("", "", "time=".urlencode($pageA["time"]))."\">".$pageA["time"]."</a> ".$pageA['user'];
        $left="<a href=\"".$this->href("", "", "time=".urlencode($pageB["time"]))."\">".$pageB["time"]."</a> ".$pageB['user'];
        $df  = new Diff(split("\n",htmlspecialchars($bodyB)),split("\n",htmlspecialchars($bodyA)));
        $dformat = new TableDiffFormatter();
        $diff    = $dformat->format($df);
        print($output);
        $output="";
        ?>
        <table class="diff" width="100%">
        <tr>
        <td colspan="2" width="50%" class="diff-header">
          <?php echo $left?>
        </td>
        <td colspan="2" width="50%" class="diff-header">
          <?php echo $right?>
        </td>
      </tr>
      <?php echo $diff?>
    </table>
        <?
        if ($added) {
            // remove blank lines
            $output .= "<br />\n<strong>"./*Additions:*/_MI_ADDITIONS."</strong><br />\n";
            $output .= "<div class=\"additions\">".$this->Format(implode("\n", $added))."</div>";
        }
         
        if ($deleted) {
            $output .= "<br />\n<strong>"./*Deletions:*/_MI_DELETIONS."</strong><br />\n";
            $output .= "<div class=\"deletions\">".$this->Format(implode("\n", $deleted))."</div>";
        }
        
        if (!$added && !$deleted && !$diff) {
            $output .= "<br />\n"._MI_NODIFFERENCES/*No differences*/.".";
        }
        print($output);
    } else {
        print("<em>"._MI_NO_READ_ACCESS."</em>");
    }
?>
</div>

 
