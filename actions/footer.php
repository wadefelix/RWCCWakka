<?
/*
CooCooWakka's footer.
You'd better not modify this file. Please edit myfooter.php (read myfooter.php.example).
The things you put into that file will show at the bottom of the page.
*/
?>
<div class="footer">
<?php
 echo $this->FormOpen("", "TextSearch", "get");
 echo $this->HasAccess("write") ? "<a href=\"".$this->href("edit")."\" title=\""./*Click to edit this page.*/_MI_CLICK_TO_EDIT."\">"._MI_EDIT."</a> ::\n" : "";
echo $this->GetPageTime() ? "<a href=\"".$this->href("revisions")."\" title=\""./*Click to view recent page revisions.*/_MI_CLICK_TO_REVISION."\">".$this->GetPageTime()."</a>(<a href=\"".$this->getlastdiff($this->page["tag"])."\">"./*diff*/_MI_DIFF."</a>)&nbsp;<a href=\"".$this->href("revisions.xml")."\" title=\""./*Click to view recent page revisions in XML format.*/_MI_CLICK_TO_XML."\"><img src=\"".$this->tinyHref("xml/xml.gif")."\" width=\"36\" height=\"14\" align=\"middle\" style=\"border : 0px;\" alt=\"XML\" /></a> ::\n" : "";

// if this page exists
if ($this->page)
{
 /* MatthiasAppel: CategoryCategory */
 if ($category = $this->GetPageCategory())
 {
 print($this->Format($category));
 }
 else
 {
 print(/*"None"*/_MI_NO_CATEGORY);
 }
// print(($this->HasAccess("write") ? " :: <a href=\"".$this->href("categorize")."\" title=\""./*Click to edit page category*/_MI_CLICK_TO_EDIT_CAT."\">"./*Edit Category*/_MI_EDIT_CAT."</a>" : ""));

 print(" :: ");
 /**/

 // if owner is current user
 // if owner is current user
 if ($this->UserIsOwner())
 {
 print(_MI_ISOWNER."::<a href=\"".$this->href("manage")."\">"./*Edit ACLs*/_MI_MANAGE_PAGE."</a> ::");
$aclshown=true;
 }
 else
 {
 if ($owner = $this->GetPageOwner())
 {
 print(_MI_OWNER.':'.$this->Format($owner));
 }
 else
 {
 print(_MI_NOBODY.($this->GetUser() ? " (<a href=\"".$this->href("claim")."\">"._MI_OWNERSHIP."</a>)" : ""));
 }
 print(" :: ");
 }
if($this->IsManager() && !$aclshown){
print("<a href=\"".$this->href("manage")."\">"./*Edit ACLs*/_MI_MANAGE_PAGE."</a> ::");
}
 }
if ($this->page){
?>
<a href="<?php echo $this->href("referrers") ?>" title="<?php /*Click to view a list of URLs referring to this page.*/echo _MI_CLICK_TO_REFERRERS?>"><?php  echo _MI_REFERRERS."(".$this->GetCounter("refer").")"; ?></a><br />

<?php  }//close if.show ref or not ?> 
<?php 
if($this->method!="show" && $this->page){
?>
<?php echo $this->Link($this->tag,"show",_MI_RETURN)." :: "; ?>
<?php 
} //if method != show
?>
 <?php  echo _MI_SEARCH; ?>:<input name="phrase" size="15" style="border: none; border-bottom: 1px solid #CCCCAA; padding: 0px; margin: 0px;" value="<?php echo htmlspecialchars($this->GetPageTag())?>" />
<?php  if ($this->page){ ?>
<!--counter--> :: <?php  printf(_MI_SHOW_COUNT,$this->GetEditCount(),$this->GetCounter("tview"),$this->GetCounter("view"),($this->GetEditCount()?round($this->GetCounter("tview")/$this->GetEditCount(),2):0)) 
?>
<?php 
}//close if.show counter or not
?>
<?php echo $this->FormClose(); //search form
?>
</div>

<div class="copyright">
<a href="#ccwakka_page_top">To Top</a> :: <?php echo $this->Link("http://validator.w3.org/check/referer", "", /*"Valid XHTML 1.0 Transitional"*/_MI_VALID_XHTML) ?> ::<?php /*echo $this->Link("http://jigsaw.w3.org/css-validator/check/referer", "", "Valid CSS")." :: "*/ ?><?php /*Powered by*/echo _MI_POWEREDBY ?><?php echo $this->Link("CooCooWakka:HomePage", "", "CooCooWakka ".COO_VERSION/*" Based on "_MI_BASEDON."Wakka ".$this->GetWakkaVersion()*/) ?>
</div>

<div class="custom_foot">
<? @include "myfooter.php" //you see ?>
</div>

<div class="last_foot">
<?php
 if ($this->GetConfigValue("debug"))
 {
 print("<span style=\"font-size: 11px; color: #888888\"><strong>Query log:</strong><br />\n");
 foreach ($this->queryLog as $query)
 {
 print($query["query"]." (".$query["time"].")<br />\n");
 }
 print("<strong>Total:</strong>".($this->GetMicroTime()-$this->starttime)."<br />\n");
 print("</span>");
 }
 ?>
</div>
<!-- nothing more should be put here, plz put everything you want to say in <div last_foot>. or user will have difficulties in printing the page -->
</body>
</html>

