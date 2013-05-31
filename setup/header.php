<?php

// stuff
function test($text, $condition, $errorText = "", $stopOnError = 1) {
	print("$text ");
	if ($condition)
	{
		print("<span class=\"ok\">OK</span><br />\n");
		return true;
	}
	else
	{
		print("<span class=\"failed\">FAILED</span>");
		if ($errorText) print(": ".$errorText);
		print("<br />\n");
		if ($stopOnError) exit;
		return false;
	}
}

function myLocation()
{
	list($url, ) = explode("?", $_SERVER["REQUEST_URI"]);
	return $url;
}
function installpage($tag,$body,$prefix,$dblink){
//0:error
//1:ok
//3:already exist
$result=mysql_query("select tag from ".$prefix."pages where tag='".mysql_escape_string($tag)."' and latest='Y'",$dblink);
$r = mysql_fetch_array($result);
if($r){
	return 3;
}
else
{
	$result=mysql_query("insert into ".$prefix."pages set tag = '".mysql_escape_string($tag)."', body = '".mysql_escape_string($body)."', user = 'WakkaInstaller', time = now(), latest = 'Y', isnew='Y'", $dblink);
	return $result;
//}
}
}
?>
<html>
<head>
  <title>CooCooWakka Installation</title>
  <style>
    P, BODY, TD, LI, INPUT, SELECT, TEXTAREA { font-family: Verdana; font-size: 13px; }
    INPUT { color: #880000; }
    .ok { color: #008800; font-weight: bold; }
    .failed { color: #880000; font-weight: bold; }
    A { color: #0000FF; }
  </style>
</head>

<body>
