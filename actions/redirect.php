<?php
$delay=2000;
$page=$action_params['page'];

if ($page == "") { $page = $this->config['root_page']; }
//echo "Redirecting to ".$this->Link($page).", please wait...\n";
//type="text/javascript"
$linktracking=$_SESSION['linktracking'];
$this->StartLinkTracking();

printf(_MI_REDIRECT_TO,$this->Link($page));
echo "<script type=\"text/javascript\">\n";
//echo "    this.document.location = \"" . $this->config['base_url'].$page . "\";\n";
echo "setTimeout(\"location.href='".addslashes($this->Href("",$page))."'\",$delay);";
echo "</script>";

$_SESSION['linktracking']=$linktracking;

?>
