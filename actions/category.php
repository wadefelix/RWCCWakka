<?php
// Category and some other tweaks help you to categorize your WikiPages
// by MatthiasAppel inspired from ErusUmbrae and his PageIndex action :)
//
// tokens: owner time history
// call: {{PageIndex token1 token2 ... }}
include_once "ccwakka.php";
if(!isset($tokens)) // Mod not installed => no tokens...
  $tokens = array();

$sql = "select tag, time, owner from " .
       $this->config["table_prefix"] .
       "pages where latest = 'Y' and tag <> '" .
       mysql_escape_string($this->GetPageTag()) .
       "' and Category = '" .
       mysql_escape_string($this->GetPageTag()) .
       "' order by tag";

if($categories = $this->LoadAll($sql))
  {

    $root = ($this->GetPageTag() == $this->GetPageCategory());

    echo "<h3>".$this->GetPageTag()."</h3>" . ($root ? "<br>\n" : "");
    cc_pageindex($categories);
  }else
{
echo "<em>"._MI_NOCATEGORY/*There's no pages in this category yet.*/."</em>";
}
?>
