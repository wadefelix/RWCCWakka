<?php 
//By cooyeah
//CooCooWakka
//http://www.hsfz.net.cn/coo/wiki
require_once('./libs/phpsyndication.lib.php');
$url=$action_params['url'];

$s = @new RSStoHTML($url,"./libs/cache/","",3600/2);
echo(@$s->getHtml()); 
?>
