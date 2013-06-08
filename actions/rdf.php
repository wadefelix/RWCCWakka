<?php 
require_once "XML/RSS.php";
$surl=$action_params['url'];
$surl=$this->StripQuotes($surl);
$rss = new XML_RSS($surl);
$rss->parse();
echo "<ul>\n";
foreach ($rss->getItems() as $item) {
    echo "<li><a href=\"" . $item['link'] . "\">" . $item['title'] . "</a></li>\n";
    echo $item['description'];
}
echo "</ul>\n";

?>
