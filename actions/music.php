<?php
//CooCooWakka http://www.hsfz.net.cn/coo/wiki
//CooYip cooyeah@hotmail.com

$listname=$action_params['ListName'];
$listpage=$this->LoadPage($listname);
$listbody=$listpage['body'];
$list=explode("\n",$listbody);
//echo $listbody;
//echo "<br>---------<br>";
$first="true";
$head="
      <script language=\"JavaScript\">
      <!--
      function ctrlsnd(sndAction,sndObj){
      if(eval(sndObj)!= null)
      {
      if(navigator.appName==\"Netscape\")
      eval(sndObj+((sndAction==\"stop\")?\".stop()\":\".run()\"));
      else if(eval(sndObj+\".FileName\"))
      eval(sndObj+((sndAction==\"stop\")?\".stop()\":\".play()\"));
      }
      }
      function oo(t,m){
      neww=window.open(\"\",t,\"height=50,width=250,top=2,left=2,toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no,status=no\");
      neww.document.write(\"<html><title>\"+t+\"</title><embed src=\\\"\"+m+\"\\\" hidden='true' loop='1' autostart='true' >\");
      neww.document.write(\"<b>Now Playing:<br/ >\"+t+\"</b>\");
      neww.document.write(\"</html>\");
      neww.document.close();

      }

      //--></script>
      ";
foreach($list as $musicd)
{
  list($title,$music)=explode("  ",$musicd);
  $i++;
  //$result.="<b>$title</b> [<a style=\"COLOR: #000000; TEXT-DECORATION: none\" href=\"#\" onClick=\"ctrlsnd('play','document.Sound$i')\" target=_self>Play</a>] [<a style=\"COLOR: #000000; TEXT-DECORATION: none\" href=\"javascript:ctrlsnd('stop','document.Sound$i')\" target=_self>Stop</a>]";
  $result.="<b>$title</b> [<a style=\"COLOR: #000000; TEXT-DECORATION: none\" href=\"javascript:document.musicbox$i.run()\" target=_self>Play</a>] [<a style=\"COLOR: #000000; TEXT-DECORATION: none\" href=\"javascript:document.musicbox$i.stop()\" target=_self>Stop</a>] [<a style=\"COLOR: #000000; TEXT-DECORATION: none\" href=\"javascript:oo('".addslashes($title)."','".addslashes($music)."')\" target=\"_self\">NotUsingIE/PlayInNewWindow</a>]" ;
  $result.="<object id=musicbox$i height=0 width=0 classid=CLSID:05589FA1-C356-11CE-BF01-00AA0055595A><param NAME=\"FileName\" VALUE=\"$music\"><param NAME=\"loop\" VALUE=\"true\"><param NAME=\"autostart\" VALUE=\"$first\"><param NAME=\"volume\" VALUE=\"0\"></object>";
  //$result.="<embed name=\"Sound$i\" src=\"$music\" mastersound loop=\"true\" autostart=\"$first\" hidden=true width=0 height=0></embed>";
  $first="false";
  $result.="<br /><br />";

}
if(!$action_params['NoEdit'])
  $result.="<div align=right>[<a href=\"".$this->href("edit",$listname)."\">Edit Music List</a>]</div>";
echo $head;
echo $result;
?>
