<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/****************************************
* CooCooWakka
*
* This file is a part of CooCooWakka
* CooCooWakka
* http://www.hsfz.net.cn/coo/wiki
* cooyeah@hotmail.com
* $Id: include.php,v 1.2 2005/04/29 10:28:20 cooyeah Exp $
****************************************/
$tag=$action_params['page'];
$tag=$this->StripQuotes($tag);
if($this->HasAccess("read",$tag)){
    if($page=$this->LoadPage($tag)){
        print($this->format($page['body']));
    }
}
?>
