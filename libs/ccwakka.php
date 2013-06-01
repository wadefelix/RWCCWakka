<?php
//By cooyeah
//Chinese Support
//GB PY
global $wakka;
//#####################################################################
//PageIndex
//cc_pageindex($pages);
//From CooCooWakka
//#####################################################################
global $py_gb;
$py_gb=array(
           array("a",-20319),
           array("b",-20283),
           array("c",-19775),
           array("d",-19218),
           array("e",-18710),
           array("f",-18526),
           array("g",-18239),
           array("h",-17922),
           array("j",-17417),
           array("k",-16474),
           array("l",-16212),
           array("m",-15640),
           array("n",-15165),
           array("o",-14922),
           array("p",-14914),
           array("q",-14630),
           array("r",-14149),
           array("s",-14090),
           array("t",-13318),
           array("w",-12838),
           array("x",-12556),
           array("y",-11847),
           array("z",-11055),
       );
//PY LIST
function pi_g($num){
	global $py_gb;
	$d=$py_gb;
	if($num>0&&$num<160){
		return chr($num);
	}
	elseif($num<-20319||$num>-10247){
		return "";
	}else{
		for($i=count($d)-1;$i>=0;$i--){if($d[$i][1]<=$num)break;}
		return $d[$i][0];
	}
}

function pi_c($str){
    global $wakka;
    global $useiconv;
    if($useiconv)
    $str=iconv($wakka->GetConfigValue("charset"),"gb2312",$str);
	$ret="";
	//for($i=0;$i<strlen($str);$i++){
	$i=0;
	$p=ord(substr($str,$i,1));
	if($p>160){
		$q=ord(substr($str,++$i,1));
		$p=$p*256+$q-65536;
	}
	$ret.=pi_g($p);
	//}
	return $ret;
}
//End PY List
//End GB PY
function cc_pageindex($pages,$style=1){
//$style:
//1: Default, like PageIndex
//2: time,revision like MyChanges
global $wakka;
global $useiconv;
$isiconv=false;
if (function_exists('iconv')) $isiconv=true;

$useiconv=false;
	if(count($pages)>0){
	
		if($wakka->GetConfigValue("SpecialCharsetSupport")=="auto"){
//		echo $wakka->GetConfigValue["charset"];
			if(preg_match("/^gb/i",$wakka->GetConfigValue("charset"))){
				$prp=1;
				//echo "HelloCoo";
			}elseif($isiconv && preg_match("/^big5$/i",$wakka->GetConfigValue("charset"))){
                $prp=1;
                $useiconv=true;
            }elseif($isiconv && preg_match("/^utf-8$/i",$wakka->GetConfigValue("charset"))){
                $prp=1;
                $useiconv=true;
            }
			else $prp=0;
		
		}else{
		
			$prp=0;
		
		}
	if($prp==1){
		usort($pages, "gb_coo_cmp");

		foreach ($pages as $page)
		{
			if (!$page["comment_on"])
			{
				$firstChar = strtoupper($page["tag"][0]);
				if (!preg_match("/[A-Za-z]/", $firstChar))
				{
					$firstChar = strtoupper(pi_c($page["tag"]));
					if (!preg_match("/[A-Za-z]/", $firstChar))
						$firstChar="#";
				}

				if ($firstChar != $curChar)
				{
					if ($curChar)
						print("<br />\n");
					print("<strong>$firstChar</strong><br />\n");
					$curChar = $firstChar;
				}
				$alias=($wakka->GetAliasName($page["tag"]) ?"(".$wakka->GetAliasName($page["tag"]).")":null);
				if($style==1)print($wakka->Link($page["tag"],"",$page["tag"].$alias)."<br />\n");
				if($style==2 && $lasttag!=$page["tag"])print("&nbsp;&nbsp;&nbsp;(".$page["time"].") (".$wakka->Link($page["tag"], "revisions", "history", 0).") ".$wakka->Link($page["tag"],"",$page["tag"].$alias,0)."<br />\n");
			}
			$lasttag=$page["tag"];
		}
		}
		else {
		usort($pages, "coo_cmp");

        foreach ($pages as $page)
        {
                if (/*!preg_match("/^Comment/", $page["tag"])*/!$page["comment_on"]) {
                        $firstChar = strtoupper($page["tag"][0]);
                        if (!preg_match("/[A-Z,a-z]/", $firstChar)) {
                                $firstChar = "#";
                        }

                        if ($firstChar != $curChar) {
                                if ($curChar) print("<br />\n");
                                print("<strong>$firstChar</strong><br />\n");
                                $curChar = $firstChar;
                        }

                        $alias=($wakka->GetAliasName($page["tag"]) ?"(".$wakka->GetAliasName($page["tag"]).")":null);
				if($style==1)print($wakka->Link($page["tag"],"",$page["tag"].$alias)."<br />\n");
				if($style==2 && $lasttag!=$page["tag"])print("&nbsp;&nbsp;&nbsp;(".$page["time"].") (".$wakka->Link($page["tag"], "revisions", "history", 0).") ".$wakka->Link($page["tag"],"",$page["tag"].$alias,0)."<br />\n");
                }
        }
		}
	}
	else
		print("<em>"/*No pages found.*/._MI_NOPAGE."</em>");
}
function gb_coo_cmp ($a, $b)
{
	$uu = strtoupper($a["tag"][0]);
	if (!preg_match("/[A-Za-z]/", $uu))
	{
		$vv = strtoupper(pi_c($a["tag"])).$a["tag"];

		$a["tag"]=$vv;
	}else
		$a["tag"][0]=strtoupper($a["tag"][0]);
	$uu = strtoupper($b["tag"][0]);
	if (!preg_match("/[A-Za-z]/", $uu))
	{
		$vv = strtoupper(pi_c($b["tag"])).$b["tag"];

		$b["tag"]=$vv;
	}else
		$b["tag"][0]=strtoupper($b["tag"][0]);

	return strcmp($a["tag"], $b["tag"]);
}

function coo_cmp ($a, $b) {
    $a["tag"][0]=strtoupper($a["tag"][0]);
//        $uu = strtoupper($b["tag"][0]);
$b["tag"][0]=strtoupper($b["tag"][0]);
    return strcmp($a["tag"], $b["tag"]);
}
//############################################################
?>
