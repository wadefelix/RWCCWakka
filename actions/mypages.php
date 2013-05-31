<?php

// actions/mypages.php
// written by Carlo Zottmann
// http://wakkawikki.com/CarloZottmann
//Tweaked and Internationalized By:
//CooCooWakka http://www.hsfz.net.cn/coo/wiki
//CooYip cooyeah@hotmail.com

include_once "ccwakka.php";
if ($user = $this->GetUser())
  {
    print("<strong>"./*This is the list of pages you own.*/_MI_MYPAGETITLE."</strong><br /><br />\n");

    $my_pages_count = 0;

    if ($pages = $this->LoadAllPages())
      {
        foreach ($pages as $page)
        {
          if ($this->UserName() == $page["owner"] && !preg_match("/^Comment/", $page["tag"]))
            {
 		$n_pages[]=$page;
              $my_pages_count++;
            }
        }
	cc_pageindex($n_pages);
        if ($my_pages_count == 0)
          {
            print("<em>"._MI_NOOWNPAGE/*You don't own any pages.*/."</em>");
          }
      }
    else
      {
        print("<em>"._MI_NOPAGE."</em>");
      }
  }
else
  {
    print("<em>"./*You're not logged in, thus the list of your pages couldn't be retrieved.*/_MI_CANNOT_RETRIEVE_OWN."</em>");
  }

?>
