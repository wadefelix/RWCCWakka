<?php
/* CooCooWakka
 * http://www.hsfz.net.cn/coo/wiki/
 * by CooYip<cooyeah@hotmail.com>
 * Last Modified:$Id: wakka.php,v 1.5 2005/07/05 03:38:50 cooyeah Exp $
*/
?>

<?php

// This may look a bit strange, but all possible formatting tags have to be in a single regular expression for this to work correctly. Yup!

global $coo_pat,$coo_pat2,$url_pattern;
$url_pattern="[a-zA-Z]+\:\/\/[a-zA-Z0-9\-\._\?\,\'\/\\\+&%\$#\=~\:@]+";
$coo_pat2="/(".
          "\%\%.*?\%\%|\"\".*?\"\"|\[\[.*?\]\]".
//          "|\b[a-z]+:\/\/\S+|[chr(0xa1)-chr(0xff)]+[a-z]+:\/\/\S+".
	  "|\b(".$url_pattern.")|[".chr(0xa1)."-".chr(0xff)."]+?(".$url_pattern.")".
          "|\n>".
          "|\'\'.*?\'\'(\[\[.*?\]\]){0,1}|\*\*|\#\#|__|<|>|@@.*?@@".
	  "|\[QUOTE\].*?\[\/QUOTE\]".
          "|\/\*idea\*\/".
          "|\/\/".
          "|======.*?======|=====.*?=====|====.*?====|===.*?===|==.*?==".
          "|\[HTML\].*?\[\/HTML\]".
          "|\[IMG\].*?\[\/IMG\]".
	  "|\[DEL\].*?\[\/DEL\]".
          "|`".
	"|\[LEFT].*?\[\/LEFT\]|\[RIGHT].*?\[\/RIGHT\]|\[CENTER].*?\[\/CENTER\]".
          "|\[TABLE\].*?\[\/TABLE\]".
          "|----[-]*|---".
          "|\n[\t:]+(-|#[#]{0,1}|[0-9a-zA-Z]+\))?".
          "|\{\{.*?\}\}|".
          "\b[A-Z][A-Za-z]+[:]([A-Za-z0-9]+)\b|".
          "\b([A-Z][a-z]+[A-Z0-9][A-Za-z0-9]*)\b|".
         "[".chr(0xa1)."-".chr(0xff)."]+?([A-Z][a-z]+[A-Z0-9][A-Za-z0-9]*)[".chr(0xa1)."-".chr(0xff)."]*?|".
          "\b([A-Z][a-z]+[A-Z0-9][A-Za-z0-9]*)[".chr(0xa1)."-".chr(0xff)."]+?|".
          "[".chr(0xa1)."-".chr(0xff)."]+?([A-Z][a-z]+[A-Z0-9][A-Za-z0-9]*)\b|".
          "\n|[ ]{2})/msi";

//Pattern in head
$coo_pat="/(\[\[.*?\]\]".
	 "|\b(".$url_pattern.")|[".chr(0xa1)."-".chr(0xff)."]+?(".$url_pattern.")".
	 "|<|>|\*\*|\/\/|".
	 "|\[DEL\].*?\[\/DEL\]".
	 "\b[A-Z][A-Za-z]+[:]([A-Za-z0-9]+)\b|".
         "\b([A-Z][a-z]+[A-Z0-9][A-Za-z0-9]*)\b|".
	 "[".chr(0xa1)."-".chr(0xff)."]+?([A-Z][a-z]+[A-Z0-9]{1}[A-Za-z0-9]*)[".chr(0xa1)."-".chr(0xff)."]*?|".
          "\b([A-Z][a-z]+[A-Z0-9]{1}[A-Za-z0-9]*)[".chr(0xa1)."-".chr(0xff)."]+?|".
          "[".chr(0xa1)."-".chr(0xff)."]+?([A-Z][a-z]+[A-Z0-9]{1}[A-Za-z0-9]*)\b".          ")/msi";
         
if (!function_exists("wakka2callback"))
{
	function wakka2callback($things)
	{
		$thing = $things[1];
		global $coo_pat,$coo_pat2,$url_pattern;

		static $oldIndentLevel = 0;
		static $indentClosers = array();
		static $br = 1;
	//	if(preg_match("/./",$thing))print_r($things);
		global $wakka;
		//		echo $thing;
		//print_r($things);
		// convert HTML thingies
		if (preg_match("/^\[HTML\](.*)\[\/HTML\]$/is", $thing, $matches))
		{
			$matches[1] = str_replace("&quot;", "\"", $matches[1]);
			$result=strip_tags($matches[1],$wakka->GetConfigValue("AllowHtmlTags"));
			return $result;
		}else if (preg_match("/^\[DEL\](.*?)\[\/DEL\]$/is",$thing,$matches))
		{
			$dualp=preg_replace_callback($coo_pat,"wakka2callback",$matches[1]);
			return "<strike>".$dualp."</strike>";
		}
		else if (preg_match("/^\[TABLE\](.*)\[\/TABLE\]$/is", $thing, $matches))
		{//table parser by Cooyeah and Frizen
			$s=$matches[1];
			$TableStyle="border=\"1\"";
			$intable_pat=str_replace("|\[TABLE\].*?\[\/TABLE\]","",$coo_pat2);
			while(preg_match("/\|\|([\t ]*?)\|\|/",$s))
			$s=preg_replace("/\|\|([\t ]*?)\|\|/","||&nbsp;||",$s); //deal with empty cells
			$s=preg_replace("/\|\|([^\f\n\r\t\v]*?)\n/","||\\1||\n",$s);
			$s=preg_replace("/\|\|$/","||\n",$s);

			while(preg_match("/\|\|(.*?)\|\|\n/",$s,$ths)){
	
				$p="||".$ths[1]."||";
				while(preg_match("/\|\|(\s{0,})(.*?)(\s{0,})\|\|/",$p,$thss)){
					
					$len_a=strlen($thss[1]);
					$len_b=strlen($thss[3]);
					if($len_b==0)$ali="right";
					if($len_a==0)$ali="left";
					if($len_b!=0 && $len_a!=0)$ali="center";
					$cont=preg_replace_callback($intable_pat,"wakka2callback",$thss[2]);
					preg_match("/\|\|(\t{0,})(.*?)(\t{0,})\|\|/",$p,$thss);
					
					$cols=strlen($thss[1])+strlen($thss[3]);
					if($cols<=0)
					{
						$cols=1;
					}
					$p=preg_replace("/\|\|(\t{0,})(.*?)(\t{0,})\|\|/","<td align=\"$ali\" colspan=\"$cols\">$cont</td>||",$p,1);
				

				}
				$p=preg_replace("/\|\|/","",$p);
				$p="<table $TableStyle><tr>\n".$p."</tr>\n</table>\n";
				$s=preg_replace("/\|\|.*?\|\|\n/",$p,$s,1);
								
				
			}
			$s=preg_replace("/<td align=\"left\" colspan=\"[0-9]+\"><\/td>/","",$s);
			$s=preg_replace("/<td><\/tr>/","</tr>",$s);
	
			$s=preg_replace("/<\/table>\n<table ".preg_quote($TableStyle).">/","\n",$s);
			return $s;
		}
		else if ($thing == "<")
			return "&lt;";
		else if ($thing == ">")
			return "&gt;";
		// code text
		else if (preg_match("/^\%\%(.*)\%\%$/s", $thing, $matches))
		{
			// check if a language has been specified
			$code = $matches[1];
			$code=str_replace("\\%\\%","%%",$code);
			$code=str_replace("\\\\%\\\\%","\\%\\%",$code);
			if (preg_match("/^\((.+?)\)(.*)$/s", $code, $matches))
			{
				list(, $language, $code) = $matches;
			}
            if (preg_match("/^(.+?):(.+?)$/i",$language,$matches2))
            {
                list(,$language, $args) = $matches2;
            }
			switch ($language)
			{
            case "vim":
                    $formatter = "vim";
                break;
			}
            if(!$language){
                $output = "<pre class=\"code\">";
                $output .= $wakka->Format(trim($code), "code", $args);
                $output .= "</pre>";
            }else if($language=="math"){
//                $output = "<pre class=\"code\">";
                $output .= $wakka->Format(trim($code), "math", $args);
//                $output .= "</pre>";
            }
            else if($language=="vim"){
                $output = "<pre class=\"code\">";
			    $output .= $wakka->Format(trim($code), $formatter, $args);
                $output .= "</pre>";
            }else{
            include_once "./libs/geshi/geshi.php";
            $geshi = new GeSHi(trim($code), $language,"./libs/geshi/geshi/");
            $geshi->set_encoding($wakka->GetConfigValue('charset'));
            $geshi->enable_classes();
            $geshi->set_header_type(GESHI_HEADER_PRE);
            $geshi->set_overall_class('code');
            $output.= $geshi->parse_code();
            }
			return $output;
		}

		//IMG
		else if($thing=="  ")
			return "&nbsp;&nbsp;";
		else if($thing=="\n>"){
			$ret=($br?"<br />":null)."&nbsp;&nbsp;&nbsp;&nbsp;";
			$br=1;
			return $ret;
		}
		else if (preg_match("/^\[IMG\](.*)\[\/IMG\]$/is", $thing, $matches))
		{
			$matches[1] = str_replace("&quot;", "\"", $matches[1]);
			$localimages=$wakka->GetPageFiles();
			if(is_array($localimages))
			foreach($localimages as $item){
			if($item['name']==$matches[1]){
			$matches[1]=$item['url'];
			break;
			}
			}
			return "<img class=\"image\" src=\"".$matches[1]."\">";
		}
		else if (preg_match("/^\[LEFT\](.*)\[\/LEFT\]$/is", $thing, $matches))
		{
		//	$matches[1] = str_replace("&quot;", "\"", $matches[1]);
			$out=$matches[1];
			$out=preg_replace_callback($coo_pat2,"wakka2callback",$out);
			return "<table align=\"left\"><tr><td>$out</td></tr></table>";
		}
		else if (preg_match("/^\[RIGHT\](.*)\[\/RIGHT\]$/is", $thing, $matches))
		{
//			$matches[1] = str_replace("&quot;", "\"", $matches[1]);
//			return "<table align=\"right\"><tr><td><img class=\"image\" src=\"".$matches[1]."\"></td></tr></table>";
			$out=$matches[1];
                        $out=preg_replace_callback($coo_pat2,"wakka2callback",$out);
                        return "<table align=\"right\"><tr><td>$out</td></tr></table>";
		}
		else if (preg_match("/^\[CENTER\](.*)\[\/CENTER\]$/is", $thing, $matches))
                {
                        $out=$matches[1];
                        $out=preg_replace_callback($coo_pat2,"wakka2callback",$out);
                        return "<table align=\"center\"><tr><td>$out</td></tr></table>";
                }

		//Idea Icons
		else if ($thing == "/*idea*/")
			return "<img src=\"".$wakka->tinyHref("/images/idea.gif")."\" width=\"7\" border=\"0\" alt=\"Hot!\" title=\"Hot!\" hspace=\"4\" height=\"11\" />";
		// bold
		else if ($thing == "**")
		{
			static $bold = 0;
			return (++$bold % 2 ? "<strong>" : "</strong>");
		}
		// italic
		else if ($thing=="//")
		{
			static $italic = 0;
			return (++$italic % 2 ? "<em>" : "</em>");
			//			return "<em>".$matches[1]."</em>";
		}
		// underlinue
		else if ($thing == "__")
		{
			static $underline = 0;
			return (++$underline % 2 ? "<u>" : "</u>");
		}
		// monospace
		else if ($thing == "##")
		{
			static $monospace = 0;
			return (++$monospace % 2 ? "<tt>" : "</tt>");
		}
                else if ($thing == "`")
                {
                    static $inlinecode = 0;
                    return (++$inlinecode%2 ? "<code>" : "</code>");
                }
		// notes
        else if (preg_match("/^\'\'(.*?)\'\'(\[\[(.*?)\]\]){0,1}$/",$thing,$matches)) {
            static $refnum=0;
            static $reflists=array();
            $ref_source=trim($matches[3]);
            if($ref_source){
                if(false===$refi=array_search($ref_source,$reflists)){
                    //echo $refi;
                    $refi=$refnum++;
                    $reflists[]=$ref_source;
                    //print_r($reflists);
                }
                $refi++;
                
                if(preg_match("/^(\S+)\s+(\S+)$/",$ref_source,$refis)){
                    $refi=$refis[2];
                    $ref_source=$refis[1];
                }
                $ref_link=$wakka->Link($ref_source,"","[".$refi."]",1,false);
        
                
                return "<span class=\"referring\">".preg_replace_callback($coo_pat,"wakka2callback",$matches[1])."</span>".$ref_link;
            }
            return "<span class=\"notes\">".preg_replace_callback($coo_pat,"wakka2callback",$matches[1])."</span>";
                
        }
		/*else if ($thing == "''")
		{
			static $notes = 0;
			return (++$notes % 2 ? "<span class=\"notes\">" : "</span>");
		}*/
		// urls
		else if (preg_match("/^(".$url_pattern.")$/", $things[2], $matches) || preg_match("/^(".$url_pattern.")$/", $things[3], $matches)) {
			$url = $matches[1];

			//return "<a href=\"$url\" target=\"_blank\"><img src=\"".$wakka->tinyHref("/images/www.gif")."\" width=\"11\" border=\"0\" alt=\"External Link\" title=\"Open [ExternalLink] in new window\" hspace=\"4\" height=\"11\" /></a><a href=\"$url\">$url</a>".$matches[2];
			//$out= "<a href=\"$url\" target=\"_blank\"><img src=\"".$wakka->tinyHref("/images/www.gif")."\" width=\"11\" border=\"0\" alt=\"External Link\" title=\"Open [ExternalLink] in new window\" hspace=\"4\" height=\"11\" /></a><a href=\"$url\" target=\"_blank\">$url</a>";
            $out = $wakka->Link($url);
			return str_replace($url,$out,$thing);
		}
		// forced anchor
		else if(preg_match("/^@@(.*?)@@$/s",$thing,$matches))
		{
			return "<a name=\"".htmlspecialchars($matches[1])."\"></a>";
		}
		// headers
		else if (preg_match("/^(={2,6})(.*?)(={2,6})$/s",$thing,$matches))
		{
			//By cooyeah
			$br=0;
			if(strlen($matches[1])!=strlen($matches[3]))
				return $thing;
			$lvl=7-strlen($matches[1]);
			$dualp=preg_replace_callback($coo_pat,"wakka2callback",$matches[2]);
			if($wakka->GetConfigValue("AutoAddAnchor"))$ret.="<a name=\"".htmlspecialchars($matches[2])."\"></a>";//add anchor
			$ret.="<h$lvl>".$dualp."</h$lvl>";
			return $ret;

		}
		else if(preg_match("/^\[QUOTE\](.*?)\[\/QUOTE\]$/is",$thing,$matches))
		{
			$br=0;
            $out=preg_replace_callback($coo_pat2,"wakka2callback",$matches[1]);
			return "<blockquote><p>".$out."</p></blockquote>";
		}
		// separators
		else if (preg_match("/^----[-]*$/",$thing))
		{
			// TODO: This could probably be improved for situations where someone puts text on the same line as a separator.
			//       Which is a stupid thing to do anyway! HAW HAW! Ahem.
			$br = 0;
			return "<hr noshade=\"noshade\" size=\"1\" />";
		}
		// forced line breaks
		else if ($thing == "---")
		{
			return "<br />";
		}
		// escaped text
		else if (preg_match("/^\"\"(.*)\"\"$/s", $thing, $matches))
		{
			$text=str_replace("\\\"\\\"","\"\"",$matches[1]);
			$text=str_replace("\\\\\"\\\\\"","\\\"\\\"",$text);
             $text=htmlspecialchars($text);
            $text=str_replace("\t","&#09;",$text);
            $text=str_replace(" ","&nbsp;",$text); 
            $text=nl2br($text);

           
			return $text;
		}
		// forced links
        else if (preg_match("/^\[\[(\S*#.+?)([|](.+))+?\]\]$/",$thing,$matches)){  //deal with anchor link with space
            return $wakka->Link($matches[1],"",$matches[3]);
        }
		else if (preg_match("/^\[\[(\S*)(\s+(.+))?\]\]$/", $thing, $matches))
		{
			list (, $url, , $text) = $matches;
			if ($url)
			{
				//if (!$text) $text = $url;
				return $wakka->Link($url, "", $text);
			}
			else
			{
				return "";
			}
		}
		// indented text
		//else if (preg_match("/\n(\t+)(-|([0-9,a-z,A-Z]+)\))?(\n|$)/s", $thing, $matches))
		else if (preg_match("/\n([\t:]+)(-|#[#]{0,1}|([0-9a-zA-Z]+)\))?(\n|$)/s", $thing, $matches))
		{
            static $oldIndentType="";
			// new line
			$result .= ($br ? "<br />\n" : "\n");

			// we definitely want no line break in this one.
			$br = 0;

			// find out which indent type we want
			$newIndentType = $matches[2];
            
            $monotag=false;
            if($newIndentType=="##"){  //it is a monospace tag~~
                $newIndentType="";
                $monotag=true;
                static $monospace = 0;
                $mono=(++$monospace % 2 ? "<tt>" : "</tt>");
            }     
   
     		if($newIndentType=="#")$newIndentType="1";
			if (!$newIndentType) { $opener = "<dl><dd>"; $closer = "</dd></dl>"; $br = 0; }
			else if ($newIndentType == "-") { $opener = "<ul><li>"; $closer = "</li></ul>"; $li = 1; }
			else { $opener = "<ol type=\"".$newIndentType."\"><li>"; $closer = "</li></ol>"; $li = 1; }
            
            $ddopener="<dl><dd>";
            $ddcloser="</dd></dl>";
			// get new indent level
			$newIndentLevel = strlen($matches[1]);
            //print($newIndentLevel);
			if ($newIndentLevel > $oldIndentLevel)
			{
				for ($i = 0; $i < $newIndentLevel - $oldIndentLevel-1; $i++)
				{
					$result .= $ddopener;
					array_push($indentClosers, $ddcloser);
				}
                $result .= $opener;
                array_push($indentClosers,$closer);
			}
			else if ($newIndentLevel < $oldIndentLevel)
			{
                //$result .= array_pop($indentClosers);
				for ($i = 0; $i < $oldIndentLevel - $newIndentLevel; $i++)
				{
					$result .= array_pop($indentClosers);
				}
			}
            else if ($newIndentLevel == $oldIndentLevel && $newIndentType != $oldIndentType){
                $result .= array_pop($indentClosers);
                for ($i = 0; $i < $oldIndentLevel -2; $i++)
                {
                    $result .= $array_pop($indentClosers);
                }
                for ($i = 0; $i < $newIndentLevel -2; $i++)
                {
                    $result .= $ddopener;
                    array_push($indentClosers, $ddcloser);
                }
                $result .= $opener;
                array_push($indentClosers,$closer);
            }
            //print_r($indentClosers);
            //print_r(htmlspecialchars($result));
            $oldIndentType = $newIndentType;
			$oldIndentLevel = $newIndentLevel;

			if ($li && !preg_match("/".preg_quote($opener)."$/", $result))
			{
				$result .= "</li><li>";
			}
            if($monotag)$result.=$mono;
			return $result;
		}
		// new lines
		else if ($thing == "\n")
		{
			// if we got here, there was no tab in the next line; this means that we can close all open indents.
			$c = count($indentClosers);
			for ($i = 0; $i < $c; $i++)
			{
				$result .= array_pop($indentClosers);
				$br = 0;
			}
			$oldIndentLevel = 0;

			$result .= ($br ? "<br />\n" : "\n");
			$br = 1;
			return $result;
		}
		// events
		else if (preg_match("/^\{\{(.*?)\}\}$/s", $thing, $matches))
		{
			if ($matches[1])
				return $wakka->Action($matches[1]);
			else
				return "{{}}";
		}
		// interwiki links!
		else if (preg_match("/^[A-Z][A-Za-z]+[:]([A-Za-z0-9]+)$/s", $thing))
		{
			return $wakka->Link($thing);
		}
		// wakka links!
		else if (preg_match("/^[A-Z][a-z]+[A-Z0-9][A-Za-z0-9]*$/s", $thing))
		{

			return $wakka->Link($thing);
		}
		//For Chinese-English mixed condition
		else if($things[7])
		{
			if (preg_match("/^[A-Z][a-z]+[A-Z0-9][A-Za-z0-9]*$/s", $things[7]))
			{
                
				return str_replace($things[7],$wakka->Link($things[7]),$thing);
			}
		}
        else if($things[9]){
            if (preg_match("/^[A-Z][a-z]+[A-Z0-9][A-Za-z0-9]*$/s", $things[9]))
            {
               return str_replace($things[9],$wakka->Link($things[9]),$thing);
            }
        }
        else if($things[8]){
            if (preg_match("/^[A-Z][a-z]+[A-Z0-9][A-Za-z0-9]*$/s", $things[8]))
            {
               return str_replace($things[8],$wakka->Link($things[8]),$thing);
            }
        }
		// if we reach this point, it must have been an accident.
        //print_r($things);
		return $thing;
	}
}

$text = str_replace("\r", "", $text);
$text = trim($text)."\n";


if(!preg_match("/^\n/",$text)){
	$add_lb=true;
	$text="\n".$text;
}else $add_lb=false;
$text = preg_replace_callback($coo_pat2,"wakka2callback",$text);
if($add_lb){
$text = preg_replace("/^<br \/>/","",trim($text));
}






// we're cutting the last <br>
$text = preg_replace("/<br \/>$/", "", trim($text));
if(!function_exists("highlightString")){
	function highlightString($buffer,$highlight) {
		//$words = preg_split('/[ \t\n\(\)]+/', $highlight);
		$words = explode(' ', $highlight);
		foreach ($words as $word) {
			$pregs[]="/(".preg_quote($word,"/").")/i";
		}
		//$result= preg_replace('/(^|>)([^<]*)/e', '"\\1".preg_replace($pregs, \'<span class="searchhighlight">\'.chr(36).\'1</span>\', \'\\2\')', $buffer);
        $result= preg_replace_callback('/(^|>)([^<]*)/', 
            function ($m) use($pregs) {
                return $m[1].preg_replace($pregs, '<span class="searchhighlight">'.chr(36).'1</span>', $m[2]);
             
            }, $buffer);
		return stripslashes($result);
	}
}
if (isset($_SERVER['HTTP_REFERER'])) {
	$url = parse_url($_SERVER['HTTP_REFERER']);
	parse_str($url['query'], $query);
	if (isset($query['q'])) {
		$highlight = $query['q'];
		//ob_start("highlightString");
		$text=highlightString($text,$highlight);
	} else if (isset($query['phrase'])) {
		$highlight = $query['phrase'];
		//ob_start("highlightString");
		$text=highlightString($text,$highlight);

	}
}
print($text);
?>
