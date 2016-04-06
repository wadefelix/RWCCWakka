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
$result=mysqli_query($dblink,"select tag from ".$prefix."pages where tag='".mysqli_escape_string($dblink,$tag)."' and latest='Y'");
$r = mysqli_fetch_array($result);
if($r){
	return 3;
}
else
{
	$result=mysqli_query($dblink,"insert into ".$prefix."pages set tag = '".mysqli_escape_string($dblink,$tag)."', body = '".mysqli_escape_string($dblink,$body)."', user = 'WakkaInstaller', time = now(), latest = 'Y', isnew='Y'");
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
