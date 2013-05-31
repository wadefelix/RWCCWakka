<?php

if ($pages = $this->LoadOrphanedPages())
  {
    foreach ($pages as $page)
    {
      print($this->Link($page["tag"], "", "", 0)."<br />\n");
    }
  }
else
  {
    print("<em>"._MI_NOORPHANED/*No orphaned pages. Good!*/."</em>");
  }

?>
