<?php
$Charset=$this->GetConfigValue("charset");
if(trim($Charset) != '')header("Content-Type: text/html; charset=$Charset");
$message = $this->GetMessage();
$user = $this->GetUser();

//echo '<?xml version="1.0"';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--

-->
<html>
<head>
	<title><?php echo $this->GetWakkaName()." : ".$this->GetDisplayName($this->GetPageTag()); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta http-equiv="Content-Type" content="text/html; charset=<?php  echo $this->GetConfigValue("charset") ?>" />
	<meta name="keywords" content="<?php echo htmlspecialchars($this->GetConfigValue("meta_keywords")." ".$this->GetPageTag()); ?>" />
	<meta name="description" content="<?php echo htmlspecialchars($this->GetConfigValue("meta_description")) ?>" />
	<meta name="generator" content="CooCooWakka <?php echo $this->GetConfigValue("coo_version");?>" /><?php 
/* ask the ROBOTS not to visit some pages */
if(preg_match("/^(referrers|referrers_sites|edit|diff|manage)$/",$this->method) ||
($this->method=="show" && isset($_REQUEST['time'])) /* do not allow to visit the old version page */
){
print("\t<meta name=\"robots\" content=\"noindex, nofollow\">\n"); 
}
?><?php /* for live bookmarks */ ?>
	<link rel="alternate" type="application/rss+xml" title="Recently changes of <?php echo $this->GetWakkaName(); ?>" href="<?php echo $this->href("", "xml/recentchanges_".preg_replace("/[^a-zA-Z0-9]/", "", strtolower($this->GetConfigValue("wakka_name"))).".xml"); ?>" />
	<link rel="alternate" type="application/rss+xml" title="Revisions of <?php echo $this->GetWakkaName()." : ".$this->GetDisplayName($this->GetPageTag()); ?>" href="<?php echo $this->Href("revisions.xml"); ?>" />
<?php /* style sheet */ ?> 
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $this->tinyHref("/css/ccwakka.css")?>" />
	<link rel="stylesheet" media="print" type="text/css" href="<?php echo $this->tinyHref("/css/print.css")?>" />
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $this->tinyHref("/css/".$this->GetConfigValue("default_css"))?>" />
	<link rel="alternate stylesheet" media="screen" type="text/css" title="Print" href="<?php echo $this->tinyHref("/css/print.css")?>" />
<?php 
/*  alternate stylesheet. activate it if you like
if($handle=opendir("./css/")){
    while (false !== ($file = readdir($handle))) {
        if(preg_match("/\.css$/",$file) && $file<>"ccwakka.css"){
	echo "<link rel=\"alternate stylesheet\" media=\"screen\" title=\"".str_replace(".css","",$file)."\" href=\"".$this->tinyHref("/css/".$file)."\" />";
}}}
*/
?>
    <style>
    .trans img{
    behavior: url("<?php echo $this->tinyhref("/css/pngbehavior.htc") ?>");  //used for the IE png bug
    }
    </style>
	<script language="JavaScript" type="text/javascript" src="<?php echo $this->tinyHref("/libs/ccwakka.js")?>">
	</script>
	<script language="JavaScript" type="text/javascript"><!--
	var baseURL="<?php echo $this->tinyHref("/")?>";
	var notSavedYet = "<?php print(_MI_DISCARD_CONFIRM); ?>";
	function fKeyDown(event){
	if (event.keyCode == 9){
	event.returnValue = false;
	//insertAtCarret(event.target, String.fromCharCode(9));
	//return false;
	if(!is_gecko && document.selection) document.selection.createRange().text = String.fromCharCode(9);
	}
	}
    -->
	</script>
</head>

<body <?php echo (( isset($user["doubleclickedit"]) && $user["doubleclickedit"] == Y) && ($this->GetMethod() == "show") ? "ondblclick=\"document.location='".$this->href("edit")."';\" " : "") ?>
	<?php echo $message ? "onLoad=\"alert('".$message."');\" " : "" ?>
>

<!--
By CooCooWakka <?php echo $this->GetConfigValue("coo_version");?>
-->

<div class="header">
<!--for google adsense, put your adsense script to actions/googlead.php -->
<span id="googlead_head" style="float:right;">
<?php @include "googlead.php" ?>
</span>
<!-- title -->
<a name="ccwakka_page_top"></a>
<h2><?php echo $this->config["wakka_name"] ?> : <a href="<?php echo $this->config["base_url"] ?>TextSearch&amp;phrase=<?php echo urlencode($this->GetPageTag()); ?>"><?php echo $this->GetDisplayName($this->GetPageTag()) ?></a><span class="origname"><?php  if($this->GetAliasName($this->GetPageTag()))echo "[".$this->GetPageTag()."]" ?></span></h2>
<!-- navigation bar -->
	<?php echo $this->Format("[[".$this->config["root_page"]." ".$this->GetDisplayName($this->config["root_page"])."]]"); ?> ::
	<?php
	if ($this->GetUser()) {
	echo $this->config["logged_in_navigation_links"] ? $this->Format($this->config["logged_in_navigation_links"])." :: " : "";
        } else {
        echo $this->config["navigation_links"] ? $this->Format($this->config["navigation_links"])." :: " : "";
        }
	?> 
<!-- you are... -->	
	<?php echo _MI_YOUARE ?><?php echo $this->Format($this->UserName()) ?>
</div>
<!-- assistance bar -->
<div class="assist_bar">
<?php
if(is_array($_SESSION['trace']))
foreach ($_SESSION['trace'] as $tag){
$trace_line.=" &raquo; ".$this->Link($tag);
}
$category="";
while($category=$this->GetPageCategory($category)){
  $category_line = " &raquo; ".$this->Link($category).$category_line;
}
?>
<div class="trace_bar">
<?php echo _MI_TRACE.":".$trace_line; ?>
</div>
<?php if($category_line){?>
<div class="category_bar">
<?php echo _MI_CATEGORY.":".$category_line;?>
</div>
<?php
}
?>
</div>


