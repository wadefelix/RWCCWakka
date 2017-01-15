<?php
     
    /*
    Yes, most of the formatting used in this file is HORRIBLY BAD STYLE. However,
    most of the action happens outside of this file, and I really wanted the code
    to look as small as what it does. Basically. Oh, I just suck. :)
    */
   

    class Wakka {
        var $dblink;
        var $page;
        var $tag;
        var $queryLog = array();
        var $interWiki = array();
        var $VERSION;
        var $temp;
	    var $starttime;
        var $nofollow=false;
        // constructor
        function Wakka($config) {
            $this->starttime=$this->GetMicroTime();
            $this->config = $config;
            $this->dblink = new mysqli($this->config["mysql_host"], $this->config["mysql_user"], $this->config["mysql_password"],$this->config["mysql_database"]);
            #mysql_select_db($this->config["mysql_database"], $this->dblink);
            $this->VERSION = COO_VERSION;
        }
        // ERROR
        function SetError($err_text="Unknown Error!",$err_type=E_USER_WARNING){
        //err_type:E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR and E_COMPILE_WARNING
        //         E_USER_NOTICE, E_USER_WARNING, E_USER_ERROR
           trigger_error ($err_text, $err_type);
        }
        // DATABASE
        function Query($query) {
            $start = $this->GetMicroTime();
            if (!$result = $this->dblink->query($query)) {
                ob_end_clean();
                die("Query failed: ".$query." (".$this->dblink->error.")");
            }
            $time = $this->GetMicroTime() - $start;
            $this->queryLog[] = array(
            "query" => $query,
                "time" => $time);
            return $result;
        }
        function LoadSingle($query) {
            if ($data = $this->LoadAll($query))
                return $data[0];
        }
        function LoadAll($query) {
            if ($r = $this->Query($query)) {
                while ($row = $r->fetch_assoc())
                $data[] = $row;
                $r->free();
            }
            return $data;
        }
       // MISC
        function GetMicroTime() {
            list($usec, $sec) = explode(" ", microtime());
            return ((float)$usec + (float)$sec);
        }
        function IncludeBuffered($filename, $notfoundText = "", $vars = "", $path = "local:.") {
            if ($path)
                $dirs = explode(":", $path);
            else
                $dirs = array("");
             
            foreach($dirs as $dir) {
                if ($dir)
                    $dir .= "/";
                $fullfilename = $dir.$filename;
                if (file_exists($fullfilename)) {
                    if (is_array($vars))
                        extract($vars);
                     
                    ob_start();
                    include($fullfilename);
                    $output = ob_get_contents();
                    ob_end_clean();
                    return $output;
                }
            }
            if ($notfoundText)
                return $notfoundText;
            else
                return false;
        }
         
        // VARIABLES
        function GetPageTag() {
            return $this->tag;
        }
        function GetPageTime() {
            return $this->page["time"];
        }
        function GetMethod() {
            return $this->method;
        }
        function GetConfigValue($name) {
            return $this->config[$name];
        }
        function GetWakkaName() {
            return $this->GetConfigValue("wakka_name");
        }
        function GetWakkaVersion() {
            return $this->COO_VERSION;
        }
         
        // PAGES
        function LoadPage($tag, $time = "", $cache = 1) {
            // retrieve from cache
            if (!$time && $cache) {
                $page = $this->GetCachedPage($tag);
            }
            // load page
            if ($page === null || (!isset($page['body']) && isset($page['id'])))//if it is "really" null(not cached) or only is it cached id
            {
                $page = $this->LoadSingle("select * from ".$this->config["table_prefix"]."pages where tag = '".mysqli_real_escape_string($this->dblink,$tag)."' ".($time ? "and time = '".mysqli_real_escape_string($this->dblink,$time)."'" : "and latest = 'Y'")." limit 1");
            }
            // cache result
            if (!$time)
                $this->CachePage($page, $tag);
            return $page;
        }

        function LoadPage_tiny($tag, $time = "", $cache = 1) {
            // retrieve from cache
            if (!$time && $cache) {
                $page = $this->GetCachedPage($tag);
            }
            // load page
            if ($page === null)//if it is "really" null(not cached)
            {
                $page = $this->LoadSingle("select id,aliasname from ".$this->config["table_prefix"]."pages where tag = '".mysqli_real_escape_string($this->dblink,$tag)."' ".($time ? "and time = '".mysqli_real_escape_string($this->dblink,$time)."'" : "and latest = 'Y'")." limit 1");
            }
            // cache result
            if (!$time)
                $this->CachePage($page, $tag);
            
            return $page;
        }

        function PageExists($tag,$latest=true){
            $page=$this->LoadPage_tiny($tag);
            if($page['id'])return true;
            else return false;
        }

        function GetCachedPage($tag) {
            $ret = $this->pageCache[$tag];
            if ($tag && !$ret && is_array($this->pageCache))
            if (array_key_exists($tag, $this->pageCache)) {
                //if it doesn't exist.
                $ret = 0;
            }
            return $ret;
        }
        function CachePage($page, $tag = "") {
            if (!$tag)$tag = $page["tag"];
            $this->pageCache[$tag] = $page;
        }
        function SetPage($page) {
            $this->page = $page;
            if ($this->page["tag"])
                $this->tag = $this->page["tag"];
        }
        function LoadPageById($id) {
            return $this->LoadSingle("select * from ".$this->config["table_prefix"]."pages where id = '".mysqli_real_escape_string($this->dblink,$id)."' limit 1");
        }
        function LoadRevisions($tag) {
            return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where tag = '".mysqli_real_escape_string($this->dblink,$tag)."' order by time desc");
        }
        function LoadRevisionsID($tag) {
            return $this->LoadAll("select id from ".$this->config["table_prefix"]."pages where tag = '".mysqli_real_escape_string($this->dblink,$tag)."' order by time desc");
        }
        function LoadPagesLinkingTo($tag) {
            return $this->LoadAll("select from_tag as tag from ".$this->config["table_prefix"]."links where to_tag = '".mysqli_real_escape_string($this->dblink,$tag)."' order by tag");
        }
        function LoadPagesLinkingFrom($tag) {  //Added since 0.0.7.9
            return $this->LoadAll("select to_tag as tag from ".$this->config["table_prefix"]."links where from_tag = '".mysqli_real_escape_string($this->dblink,$tag)."' order by tag");
        }
        function LoadRecentlyChanged() {
            if ($pages = $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' and comment_on = '' order by time desc")) {
                foreach ($pages as $page) {
                    $this->CachePage($page);
                }
                return $pages;
            }
        }
        function GetLatestPageID($tag){
           if ($page = $this->LoadPage($tag)){
                return $page['id'];
           }
        }
        function LoadRecentlyChanged_all($max="0") {
	
            if ($pages = $this->LoadAll("select aliasname,id,tag,time,user,note,isnew,latest,tinychange from ".$this->config["table_prefix"]."pages where comment_on = '' order by time desc".($max?" limit $max":""))) {
                foreach ($pages as $page) {
                    if ($page['latest'] == 'Y' && $page['tag']!=$this->tag)
                    $this->CachePage($page);
                }
                return $pages;
            }
        }
        function LoadWantedPages() {
            return $this->LoadAll("select distinct ".$this->config["table_prefix"]."links.to_tag as tag,count(".$this->config["table_prefix"]."links.from_tag) as count from ".$this->config["table_prefix"]."links left join ".$this->config["table_prefix"]."pages on ".$this->config["table_prefix"]."links.to_tag = ".$this->config["table_prefix"]."pages.tag where ".$this->config["table_prefix"]."pages.tag is NULL group by ".$this->config["table_prefix"]."links.to_tag order by count desc");
        }
        function LoadOrphanedPages() {
            return $this->LoadAll("select distinct tag from ".$this->config["table_prefix"]."pages left join ".$this->config["table_prefix"]."links on ".$this->config["table_prefix"]."pages.tag = ".$this->config["table_prefix"]."links.to_tag where ".$this->config["table_prefix"]."links.to_tag is NULL and ".$this->config["table_prefix"]."pages.comment_on = '' order by tag");
        }
        function LoadPageTitles() {
            return $this->LoadAll("select distinct tag from ".$this->config["table_prefix"]."pages order by tag");
        }
        function LoadAllPages() {
            return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' order by tag");
        }
    	/**
    	*
    	* This is the short Description for the Function
    	*
    	* This is the long description for the Class
    	*
    	* @return	mixed	 Description
    	* @access	public
    	* @see		??
    	*/
    	function LoadAllPages_tiny(){
    		$pages=$this->LoadAll("select id,tag,aliasname,comment_on from ".$this->config["table_prefix"]."pages where latest = 'Y' order by tag");
    		foreach($pages as $page){
    			if($page['tag']!=$this->tag)
    			$this->CachePage($page);		
    		}
    		return $pages;
    	}
        function FullTextSearch($phrase) {
            return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where latest = 'Y' and match(tag, body) against('".mysqli_real_escape_string($this->dblink,$phrase)."')");
        }
         
        function FullTextSearch2($pharse) {
            $searchsplit = array();
            $searchsplit = explode(" ", $pharse);
            if (count($searchsplit) > 12) {
                SetMessage("Sorry, your keywords are too many!");
                return null;
            }
            foreach($searchsplit as $word) {
                $pharse = "%".mysqli_real_escape_string($this->dblink,$word)."%";
                $thisresult = array();
                $qresult = $this->LoadAll("select id,tag from ".$this->config["table_prefix"]."pages where latest='Y' and
                    (tag like '$pharse' or body like '$pharse');");
                if ($qresult)
                foreach($qresult as $q)if($this->HasAccess("read", $q['tag']))
                $thisresult[] = $q['id'];
                if ($i++)
                $thisresult = array_intersect($lastresult, $thisresult);
                $lastresult = $thisresult;
                //echo $i;
                if (!is_array($lastresult))
                return null;
                if (count($lastresult) < 1)
                return null;
            }
             
            $result = $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where id in (" . implode(",", $lastresult). ");");
            //array_unique($lastresult);
            return $result;
        }
        function PrePBody($body) {
            //PreProcess some special chars in body before save
            $body = preg_replace_callback("/(\%\%.*?\%\%|\"\".*?\"\"|\[\[.*?\]\]|\b[a-z]+:\/\/\S+|\'\'|\*\*|\#\#|__|<|>|\/\*idea\*\/|\/\/|======.*?======|=====.*?=====|====.*?====|===.*?===|==.*?==|----|---|\n\t+(-|[0-9,a-z,A-Z]+\))?|\{\{.*?\}\}|\/\*sign\*\/|". "[ ]{4})/msi", "CooSavecallback", $body);
            return $body;
        }
        function HasBadWords($text){
            if(!$this->badwordlist){
                $lines = file("badwords.conf");
                if(is_array($localbad=@file("local/badwords.conf"))) {
            		$lines =@array_merge($lines,$localbad);
            	}
    		    if($lines) {
                    foreach ($lines as $line) {
                        if ($line = trim($line)) {
                            $this->badwordlist[]=$line;
                        }
                    }
                    $this->badwordlist=array_unique($this->badwordlist);
                }
            }
            //die($this->badwordlist);
            foreach($this->badwordlist as $item){
                if(preg_match("/".preg_quote($item,"/")."/i",$text))return $item;
            }
            return false;
            
        }
        function SavePage($tag, $body, $comment_on = "", $tinychange = "N", $note = "", $aliasname = false) {
            if(preg_match("#^CCW_SYS_#i",$tag)) {
                $this->Redirect($this->href("", $this->config["root_page"]));
            }
            $today = date("D F j, Y, g:i a");
            // get current user
            $user = $this->GetUserName();
            //preprocess the body
            $body = $this->PrePBody($body);
            $page=$this->LoadPage($tag);
            if($aliasname===false)
                $aliasname=$page['aliasname'];
            }
            if($bad=$this->HasBadWords($body)){
	    	    $this->SetMessage(_MI_NOT_SAVE_BADWORDS.$bad);
                return;
            }
            // TODO: check write privilege
            if ($this->HasAccess("write", $tag) || ($comment_on?$this->HasAccess("comment",$comment_on):false) ) {
                // is page new?
                if (!$oldPage = $this->LoadPage($tag)) {
                    // create default write acl. store empty write ACL for comments.
                    $this->SaveAcl($tag, "write", ($comment_on ? "" : $this->GetConfigValue("default_write_acl")));
                     
                    // create default read acl
                    $this->SaveAcl($tag, "read", $this->GetConfigValue("default_read_acl"));
                     
                    // create default comment acl.
                    $this->SaveAcl($tag, "comment", $this->GetConfigValue("default_comment_acl"));
                     
                    // current user is owner; if user is logged in! otherwise, no owner.
                    if ($this->GetUser() || $comment_on)
                        $owner = $user;
                     
                } else {
                    // aha! page isn't new. keep owner!
                    $owner = $oldPage["owner"];
                    /* MatthiasAppel: CategoryCategory */
                    $category = $oldPage["category"]; /**/
                    $comment_on = $oldPage["comment_on"];
                     
                }
                $isnew = ($this->LoadPage($tag) ?"N":"Y");
                $page = $this->LoadPage($tag);
                // set all other revisions to old
                $this->Query("update ".$this->config["table_prefix"]."pages set latest = 'N' where tag = '".mysqli_real_escape_string($this->dblink,$tag)."'");
                 
                // add new revision
                $this->Query("insert into ".$this->config["table_prefix"]."pages set ". "tag = '".mysqli_real_escape_string($this->dblink,$tag)."', ". ($comment_on ? "comment_on = '".mysqli_real_escape_string($this->dblink,$comment_on)."', " : ""). "time = now(), ". "owner = '".mysqli_real_escape_string($this->dblink,$owner)."', ". /* MatthiasAppel: CategoryCategory */ "category = '".mysqli_real_escape_string($this->dblink,$category)."', ". /**/
                 
                "user = '".mysqli_real_escape_string($this->dblink,$user)."', ". "latest = 'Y', ". "body = '".mysqli_real_escape_string($this->dblink,trim($body))."', ". "tinychange= '".$tinychange."', ". "note= '".mysqli_real_escape_string($this->dblink,trim($note))."',". "isnew= '".$isnew."',". "aliasname= '".mysqli_real_escape_string($this->dblink,trim($aliasname))."',". "tview_count= '".(is_numeric($page['tview_count'])?$page["tview_count"]:0)."',". "refer_count= '".(is_numeric($page["refer_count"])?$page['refer_count']:0)."';" );
            }
             
            $this->WriteRecentChangesXML();
	        return true;
        }
        // Log
	/**
	*
	* This is the short Description for the Function
	*
	* This is the long description for the Class
	*
	* @return	mixed	 Description
	* @access	public
	* @see		??
	*/
	function LogOperation($method,$page="",$obj="",$user="",$notes=""){
		if(!$user)$user=$this->GetUserName();
		$text="";
		switch($method){
		case "_OP_PAGE_REMOVE":
		$text="**"._MI_PAGE_DELETED."**".": [del]//[[$page]]//[/del]....$user".($notes?"($notes)":"");
		break;
		case "_OP_FILE_UPLOAD":
		$text="**"._MI_FILE_UPLOADED."**".": $obj(//$page//)....$user".($notes?"($notes)":"");
		break;
		case "_OP_FILE_REMOVE":
		$text="**"._MI_FILE_REMOVED."**".": [del]$obj"."[/del](//[[$page]]//)....$user".($notes?"($notes)":"");
		break;
		case "_OP_PAGE_MOVE":
		$text="**"._MI_PAGE_MOVED."**".": //[[$page]]//"._MI_MOVE_TO."[[$obj]]....$user".($notes?"($notes)":"");
		break;
        case "_OP_PAGE_ROLLBACK":
        $text="**"._MI_PAGE_ROLLBACKED."**".": //[[$page]]//....$user".($notes?"($notes)":"");
        break;
		}
		if(!$text)$text="$user did sth unknown($method)~?!";
		$text.="\n";
		$today = date($this->GetConfigValue('time_stamp_format'));
		$text.= $today;
		$text.="\n\n";
		$log=$this->LoadPage("OperatingLog");
		$log=$text.$log['body'];
		$this->SavePage("OperatingLog",$log);
	}
        // COOKIES
        function SetSessionCookie($name, $value) {
	        $cpath=str_replace(basename($HTTP_SERVER_VARS['PHP_SELF']),"",$HTTP_SERVER_VARS['PHP_SELF']);
            SetCookie($name, $value, 0, $cpath);
            $_COOKIE[$name] = $value;
        }
        function SetPersistentCookie($name, $value) {
            $cpath=str_replace(basename($HTTP_SERVER_VARS['PHP_SELF']),"",$HTTP_SERVER_VARS['PHP_SELF']);
            SetCookie($name, $value, time() + 90 * 24 * 60 * 60, $cpath);
            $_COOKIE[$name] = $value;
        }
        function DeleteCookie($name) {
	        $cpath=str_replace(basename($HTTP_SERVER_VARS['PHP_SELF']),"",$HTTP_SERVER_VARS['PHP_SELF']);
            SetCookie($name, "", 1, $cpath);
            $_COOKIE[$name] = "";
        }
        function GetCookie($name) {
            return $_COOKIE[$name];
        }
         
        // HTTP/REQUEST/LINK RELATED
        function SetMessage($message) {
            $_SESSION["message"] = $message;
        }
        function GetMessage() {
            $message = $_SESSION["message"];
            $_SESSION["message"] = "";
            return $message;
        }	
        function Redirect($url) {
            header("Location: $url");
            exit;
        }
        // returns just PageName[/method].
        function MiniHref($method = "", $tag = "") {
            if (!$tag = trim($tag))
                $tag = $this->tag;
            $tag_link = urlencode($tag); //supporting mutil-language
            return $tag_link.($method ? "/".$method : "");
        }
        function MiniHref_orig($method = "", $tag = "") { //used by html forms
            if (!$tag = trim($tag))
                $tag = $this->tag;
            //$tag_link=urlencode($tag); 
            return $tag.($method ? "/".$method : "");
        }
         
        // returns the full url to a page/method.
        function Href($method = "", $tag = "", $params = "") {
             
            //Modify by cooyeah May 27th 2003
            if (substr($tag, -4) == ".gif")
            return $tag;
            if (substr($tag, 0, 4) == "xml/")
            return $tag;
            $href = $this->config["base_url"].$this->MiniHref($method, $tag);
            if ($params) {
                $href .= ($this->config["rewrite_mode"] == 1 ? "?" : "&amp;").$params;
            }
            return $href;
        }
        // returns the Absolute URL of a file in CooCooWakka Directory
        function tinyHref($source) {
            //Get Absolute URL by cooyeah Nov 2nd 2003
            $url = $this->GetConfigValue("base_path");
            if (!preg_match("/.*\/$/", $url) && !preg_match("/^\/.*/", $source))$url .= "/";
            return $url.$source;
        }
        function Link($tag, $method = "", $text = "", $track = 1, $icon = true) {
            if($this->env['no_icon']) {
        		$icon=false;
        	}
            if (!$text) {
                $text = $this->GetDisplayName($tag);
            }
            // is this an interwiki link?
            //if (preg_match("/^([A-Z][A-Z,a-z]+)[:]([A-Z][a-z]+[A-Z,0-9][A-Z,a-z,0-9]*)$/", $tag, $matches))
            if (preg_match("/^([A-Z][A-Z,a-z]+)[:](.*)$/", $tag, $matches )) {
                $tag = $this->GetInterWikiUrl($matches[1], $matches[2]);
                return ($icon?"<a href=\"$tag\" target=\"_blank\"><img src=\"".$this->tinyHref("/images/inter.gif")."\" width=\"11\" border=\"0\" alt=\"[InterWiki]\" title=\""._MI_NEWWINDOW/*Open [InterWiki] in new window*/."\" hspace=\"4\" height=\"11\" /></a>":"").
                "<a href=\"$tag\">$text</a>";
            }
            // is this a full link? ie, does it contain alpha-numeric characters?
            else if (preg_match("/^([a-z]+:\/\/\S+?)([^[:alnum:]^\/])?$/", $tag)) {
                // check for protocol-less URLs
                if (!preg_match("/:/", $tag)) {
                    $tag = "http://".$tag;
                }
                $tag = str_replace("&", "&amp;", $tag);
                $this->outlinks[]=$tag;
                if($this->nofollow) {
                    $nofollow=" rel=\"nofollow\"";
                }
		        return ($icon?"<a href=\"$tag\" target=\"_blank\" $nofollow><img src=\"".$this->tinyHref("/images/www.gif")."\" width=\"11\" border=\"0\" alt=\"[External Link]\" title=\""._MI_NEWWINDOW/*Open [ExternalLink] in new window*/."\" hspace=\"4\" height=\"11\" /></a>":"")."<a href=\"$tag\" $nofollow>$text</a>";
            }
            // check for email addresses
            else if (preg_match("/^.+\@.+\..+$/", $tag)) {
                $tag = "mailto:".$tag;
                return "<a href=\"$tag\">$text</a>";
            }
             
            /*
            //Link to "network neighbour"
            //uncomment it to enable
            else if (preg_match("/^\\\\.+/",$tag)) //Link to "network neighbour"
            {
            return "<a href=\"$tag\">$text</a>";
            }*/
             
            else {
                if (preg_match("/^(.*?)[#](.*?)$/", $tag, $matches)) {
                    //By cooyeah
                    //print_r($matches);
                    $tag = $matches[1];
                    $anchor = $matches[2];
                }
                // it's a Wakka link!
                if ($_SESSION["linktracking"] && $track)
                    $this->TrackLinkTo($tag);
                $result = ($this->PageExists($tag) ? "<a href=\"".$this->href($method, $tag).($anchor?"#$anchor\">":"\">").$text."</a>" : "<span class=\"missingpage\">".$text."</span><span class=\"missingpage_question\"><a href=\"".$this->href("edit", $tag)."\">?</a></span>");
                return $result;
            }
        }
        function GetAliasName($tag = "") {
            global $specialwakkapage;
            if (!$tag) {
                $tag = $this->GetPageTag();
                $ipage = $this->page;
            }
            else
                $ipage = $this->LoadPage_tiny($tag);
             
            $text = ($specialwakkapage[$tag] != $tag?$specialwakkapage[$tag]:null);
            $aliasname_db = $ipage["aliasname"];
            if ($aliasname_db)
            $text = $aliasname_db;
            // echo $text."-->".$tag;
            return $text;
        }
        function GetDisplayName($tag = "") {
            $alias = $this->GetAliasName($tag);
            $alias = ($alias?$alias:$tag);
            return $alias;
        }
        // function PregPageLink($matches) { return $this->Link($matches[1]); }
        function IsWikiName($text) {
            return preg_match("/^[A-Z][a-z]+[A-Z0-9][A-Za-z0-9]*$/", $text);
        }
        function TrackLinkTo($tag) {
	    $_SESSION["linktable"][] = $tag;
        }
        function GetLinkTable() {
            return $_SESSION["linktable"];
        }
        function ClearLinkTable() {
            $_SESSION["linktable"] = array();
        }
        function StartLinkTracking() {
            $_SESSION["linktracking"] = 1;
        }
        function StopLinkTracking() {
            $_SESSION["linktracking"] = 0;
        }
        function WriteLinkTable($tag="") {
	        if(!$tag) {
	            $tag=$this->GetPageTag();
	        }
            // delete old link table
            $this->Query("delete from ".$this->config["table_prefix"]."links where from_tag = '".mysqli_real_escape_string($this->dblink,$tag)."'");
            if ($linktable = $this->GetLinkTable()) {
                $from_tag = mysqli_real_escape_string($this->dblink,$tag);
                foreach ($linktable as $to_tag) {
                    $lower_to_tag = strtolower($to_tag);
                    if (!$written[$lower_to_tag]) {
                        $this->Query("insert into ".$this->config["table_prefix"]."links set from_tag = '".$from_tag."', to_tag = '".mysqli_real_escape_string($this->dblink,$to_tag)."'");
                        $written[$lower_to_tag] = 1;
                    }
                }
            }
        }
     
        function Header() {
            return $this->Action($this->GetConfigValue("header_action"), 1);
        }
        function Footer() {
            return $this->Action($this->GetConfigValue("footer_action"), 1);
        }
         
        // FORMS
        function FormOpen($method = "", $tag = "", $formMethod = "post",$args="") {
            //modify by cooyeah may 27 2003
            if($args)$args=" ".$args;
            $result = "<form action=\"".$this->href($method, $tag)."\" method=\"".$formMethod."\"".$args.">\n";
            if (!$this->config["rewrite_mode"])
                $result .= "<input type=\"hidden\" name=\"wakka\" value=\"".$this->MiniHref_orig($method, $tag)."\"/>\n";
            return $result;
        }
        function FormClose() {
            return "</form>\n";
        }
         
        // INTERWIKI STUFF
        function ReadInterWikiConfig() {
            $lines = file("interwiki.conf");
    	    $local_lines=@file("local/interwiki.conf");
            if(is_array($local_lines)) {
                $lines=array_merge($lines,$local_lines);
            }
	    
            if($lines) {
                foreach ($lines as $line) {
                    if ($line = trim($line)) {
                        list($wikiName, $wikiUrl) = explode(" ", trim($line));
                        $this->AddInterWiki($wikiName, $wikiUrl);
                    }
                }
            }
        }
        function AddInterWiki($name, $url) {
            $this->interWiki[$name] = $url;
        }
        function GetInterWikiUrl($name, $tag) {
            if ($url = $this->interWiki[$name]) {
                return $url.$tag;
            }
        }
        // Counter
        function CountView(&$page) {
            if (!$page)$page = &$this->page;
            else if($page["latest"] == "N")$old = true;
            $tag = $page['tag'];
            if (!$old)
            $query = "update ".$this->GetConfigValue("table_prefix")."pages set view_count=view_count+1,tview_count=tview_count+1 where tag='".mysqli_real_escape_string($this->dblink,$tag)."' and latest='Y';";
            else
                {
                $query = "update ".$this->GetConfigValue("table_prefix")."pages set tview_count=tview_count+1 where tag='".mysqli_real_escape_string($this->dblink,$tag)."' and latest='Y';";
                $this->Query($query);
                $query = "update ".$this->GetConfigValue("table_prefix")."pages set view_count=view_count+1 where id='".$page['id']."';";
            }
            $this->Query($query);
            $page["view_count"]++;//it is cached page;
            $page["tview_count"]++;
        }
        function GetCounter($item = "", $page = "") {
            if (!$page)$page = $this->page;
            $count["view"] = $page["view_count"];
            $count["tview"] = $page["tview_count"];
            $count["refer"] = $page["refer_count"];
            return ($item?$count[$item]:$count);
        }
        function GetEditCount($tag = "") {
            if (!$tag) {
                $tag = $this->GetPageTag();
            }
            if ($this->temp["revisions_count"] == null){
                $count = count($this->LoadAll("select id from ".$this->GetConfigValue("table_prefix")."pages where tag='".mysqli_real_escape_string($this->dblink,$tag)."';"));
                $this->temp["revisions_count"]=$count;
            } else {
                $count = $this->temp["revisions_count"];
            }
            return $count;
        }
        // FILES
        /**
        *
        * This is the short Description for the Function
        *
        * This is the long description for the Class
        *
        * @return	mixed	 Description
        * @access	public
        * @see		??
        */
        function GetPageFiles($tag=""){
            $files=array();
            if(!$tag) {
                $tag=$this->GetPageTag();
            }
            $dirpath=$this->GetPageUploadDir($tag);
            if(!is_dir($dirpath)) {
                return false;
            }
            if ($handle = opendir($dirpath)) 
            {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != ".." && !is_dir($file)){
                        $filesArr[] = array(
                            'name' => trim($file),
                            'url' => $this->href('files.xml', $tag, 'action=download&file='.urlencode(trim($file)))
                        );
                    }
                }
                closedir($handle);
            }
            return $filesArr;
        }
        /**
        *
        * This is the short Description for the Function
        *
        * This is the long description for the Class
        *
        * @return	mixed	 Description
        * @access	public
        * @see		??
        */
        function GetPageUploadDir($tag="",$exist=true){
            if(!$tag) {
                $tag=$this->GetPageTag();
            }
            $upload_path = $this->config['upload_path'].'/'.$tag;
            return (is_dir($upload_path) || !$exist) ? $upload_path : false;
        }
        // REFERRERS
        function LogReferrer($tag = "", $referrer = "") {
            // fill values
            if (!$tag = trim($tag))
                $tag = $this->GetPageTag();
            if (!$referrer = trim($referrer))
                $referrer = $_SERVER["HTTP_REFERER"];
             
            // check if it's coming from another site
            if ($referrer && !preg_match("/^".preg_quote($this->GetConfigValue("base_url"), "/")."/", $referrer)/* && !$this->HasBadWords($referrer)*/) {
                if (!$this->config['no_referrer'])$this->Query("insert into ".$this->config["table_prefix"]."referrers set ". "page_tag = '".mysqli_real_escape_string($this->dblink,$tag)."', ". "referrer = '".mysqli_real_escape_string($this->dblink,$referrer)."', ". "time = now()");
                $this->Query("update ".$this->GetConfigValue("table_prefix")."pages set refer_count=refer_count+1 where tag='".mysqli_real_escape_string($this->dblink,$tag)."' and latest='Y';");
                 
            }
        }
        function LoadReferrers($tag = "") {
            return $this->LoadAll("select referrer,time, count(referrer) as num from ".$this->config["table_prefix"]."referrers ".($tag = trim($tag) ? "where page_tag = '".mysqli_real_escape_string($this->dblink,$tag)."'" : "")." group by referrer order by num desc");
        }
         
        // PLUGINS
        /*
        function Action($action, $forceLinkTracking = 0) {
        $action = trim($action);
         
        // stupid attributes check
        if (stristr($action, "=\"")) {
        // extract $action and $vars_temp ("raw" attributes)
        preg_match("/^([A-Za-z0-9]*)(.*)$/", $action, $matches);
        list(, $action, $vars_temp) = $matches;
         
        // match all attributes (key and value)
        preg_match_all("/([A-Za-z0-9]*)=\"(.*)\"/U", $vars_temp, $matches);
         
        // prepare an array for extract() to work with (in $this->IncludeBuffered())
        if (is_array($matches)) {
        for ($a = 0; $a < count($matches); $a++) {
        $vars[$matches[1][$a]] = $matches[2][$a];
        }
        }
        }
        if (!$forceLinkTracking) $this->StopLinkTracking();
        $result = $this->IncludeBuffered(strtolower($action).".php", "<i>Unknown action \"$action\"</i>", $vars, $this->config["action_path"]);
        $this->StartLinkTracking();
        return $result;
        }
        */
        function Action($action, $forceLinkTracking = 0) {
            if (!$forceLinkTracking)
                $this->StopLinkTracking();
             
            $vars['all_action_params'] = '';
            $vars['action_params'] = array();
            $vars['tokens'] = array();
             
            if (preg_match('/^\s*(\S+)\s*[(<\[](.*)[)>\]]\s*$/Ds', $action, $matches)) {
                list(, $action, $params) = $matches;
                $vars['all_action_params'] = $params;
                foreach(array_map('trim', explode(',', $params)) as $param) {
                    $vars['action_params'][] = $param;
                    $vars['tokens'][strtolower($param)] = true;
                }
            }
            else
                if (preg_match('/^\s*(\S+)(\s*$|\s+.+$)/s', $action, $matches)) {
                list(, $action, $params) = $matches;
                $vars['all_action_params'] = $params;
                 
                if (strpos($params, '=') !== false) {
                    while (preg_match('/(?:\s|^)(\S+)\s*=\s*([^=]*)$/s', $params, $matches)) {
                        list(, $name, $value) = $matches;
                        $vars['action_params'][$name] = trim($value);
                        $params = preg_replace('/(?:\s|^)(\S+)\s*=\s*([^=]*)$/s', '', $params);
                    }
                    $vars['action_params'] = array_reverse($vars['action_params']);
                    foreach($vars['action_params'] as $param => $value)
                    $vars['tokens'][strtolower($param)] = true;
                }
                //             else
                //             {
                foreach (array_map('trim', explode(' ', $params)) as $param)
                if ($param !== '') {
                    $vars['action_params'][] = $param;
                    $vars['tokens'][strtolower($param)] = true;
                }
                //             }
            }
             
            $result = $this->IncludeBuffered($this->config["action_path"]."/".strtolower($action).".php", '<i>Unknown action "'. $action.'"</i>', $vars);
             
            $this->StartLinkTracking();
            return $result;
        }
        function Method($method) {
            if (!$handler = $this->page["handler"])
                $handler = "page";
            $methodLocation = $handler."/".$method.".php";
            return $this->IncludeBuffered($this->config["handler_path"]."/".$methodLocation, "<i>Unknown method \"$methodLocation\"</i>", "");
        }
        function Format($text, $formatter = "wakka", $args = "") {
            return $this->IncludeBuffered("formatters/".$formatter.".php", "<i>Formatter \"$formatter\" not found</i>", compact("text", "args"));
        }
         
        // USERS
        function LoadUser($name, $password = 0) {
            return $this->LoadSingle("select * from ".$this->config["table_prefix"]."users where name = '".mysqli_real_escape_string($this->dblink,$name)."' ".($password === 0 ? "" : "and password = '".mysqli_real_escape_string($this->dblink,$password)."'")." limit 1");
        }
        function LoadUsers() {
            return $this->LoadAll("select * from ".$this->config["table_prefix"]."users order by name");
        }
        function GetUserName() {
            if ($user = $this->GetUser())
                $name = $user["name"];
            else if ($_SERVER["HTTP_X_FORWARDED_FOR"])
            $name = $_SERVER["HTTP_X_FORWARDED_FOR"];
            else
	    //Comment it to make it run faster
	    //if (!$name = gethostbyaddr($_SERVER["REMOTE_ADDR"]))
            $name = $_SERVER["REMOTE_ADDR"];
            return $name;
        }
        function UserName() {
            /* deprecated! */ return $this->GetUserName();
        }
        function GetUser() {
            return $_SESSION["user"];
        }
        function SetUser($user,$remember=false) {
            $_SESSION["user"] = $user;
            if($remember){
                $this->SetPersistentCookie("name", $user["name"]);
                //$this->SetPersistentCookie("password", $user["password"]);
            }else{
                $this->SetSessionCookie("name", $user["name"]);
                //$this->SetSessionCookie("password", $user["password"]);
            }
        }
        function LogoutUser() {
            $_SESSION["user"] = "";
            $this->DeleteCookie("name");
            $this->DeleteCookie("password");
        }
        function IsAdmin($user = "") {
            if (!$user)$user = $this->GetUserName();
            $adminstring = $this->config["admin_users"];
            $adminarray = explode(',' , $adminstring);
             
            foreach ($adminarray as $admin) {
                if (trim($admin) == $user) {
                    return true;
                }
            }   //bug found and fix by Mic
            return false;
             
        }
        function IsManager($user = "", $tag="") {
            return ($this->IsAdmin($user) || $this->UserIsOwner($user,$tag));
        }
        function UserWantsComments() {
            if (!$user = $this->GetUser())
                return false;
            return ($user["show_comments"] == "Y");
        }
         
         
        // COMMENTS
        function LoadComments($tag) {
            function LoadComments_SortComment($a,$b){
                return strnatcmp($a['tag'],$b['tag']);
                
            }
            $comments = $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where comment_on = '".mysqli_real_escape_string($this->dblink,$tag)."' and latest = 'Y' order by time");
            if($comments)usort($comments,"LoadComments_SortComment");
            return $comments;
        }
        function LoadRecentComments_all() {
            return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where comment_on != '' order by time desc");
        }
        function LoadRecentComments() {
            return $this->LoadAll("select * from ".$this->config["table_prefix"]."pages where comment_on != '' and latest = 'Y' order by time desc");
        }
        function LoadRecentlyCommented($limit = 50) {
            // NOTE: this is really stupid. Maybe my SQL-Fu is too weak, but apparently there is no easier way to simply select
            //       all comment pages sorted by their first revision's (!) time. ugh!
             
            // load ids of the first revisions of latest comments. err, huh?
            if ($ids = $this->LoadAll("select min(id) as id from ".$this->config["table_prefix"]."pages where comment_on != '' group by tag order by id desc")) {
                // load complete comments
                foreach ($ids as $id) {
                    $comment = $this->LoadSingle("select * from ".$this->config["table_prefix"]."pages where id = '".$id["id"]."' limit 1");
                    if (!$comments[$comment["comment_on"]] && $num < $limit) {
                        $comments[$comment["comment_on"]] = $comment;
                        $num++;
                    }
                }
                 
                // now load pages
                if ($comments) {
                    // now using these ids, load the actual pages
                    foreach ($comments as $comment) {
                        $page = $this->LoadPage($comment["comment_on"]);
                        $page["comment_user"] = $comment["user"];
                        $page["comment_time"] = $comment["time"];
                        $page["comment_tag"] = $comment["tag"];
                        $pages[] = $page;
                    }
                }
            }
            // load tags of pages
            //return $this->LoadAll("select comment_on as tag, max(time) as time, tag as comment_tag, user from ".$this->config["table_prefix"]."pages where comment_on != '' group by comment_on order by time desc");
            return $pages;
        }
        /**
        *
        * MatthiasAppel
        *
        * Includes for category mod
        *
        */
        function LoadCategories() {
            return $this->LoadAll("select * from " . $this->config["table_prefix"] . "pages where tag like 'Category%' and latest = 'Y' order by tag");
        }
         
        function GetPageCategory($tag = "", $time = "") {
            if (!$tag = trim($tag)) 
                $tag = $this->GetPageTag();
                if ($page = $this->LoadPage($tag, $time)) {
                    return ($page["category"]?/*$this->GetDisplayName(*/$page["category"]:null);
                }
            
        }
         
        function SetPageCategory($tag, $category) {
            // updated latest revision with new category
            $this->Query("update " . $this->config["table_prefix"] . "pages set Category = '" . mysqli_real_escape_string($this->dblink,$category) . "' where tag = '" . mysqli_real_escape_string($this->dblink,$tag) . "' and latest = 'Y' limit 1");
        }
         
        function StripQuotes($param) {
            if (substr($param, 0, 1) == '"') {
                $param = substr($param, 1, strlen($param)-2);
            }
            return $param;
        }
         
        /* MatthiasAppel */
        // ACCESS CONTROL
        // returns true if logged in user is owner of current page, or page specified in $tag
        function UserIsOwner($tag = "") {
            // check if user is logged in
            if (!$this->GetUser())
                return false;
             
            // set default tag
            if (!$tag = trim($tag))
                $tag = $this->GetPageTag();
             
            // check if user is owner
            if ($this->GetPageOwner($tag) == $this->GetUserName())
                return true;
        }
        function GetPageOwner($tag = "", $time = "") {
            if (!$tag = trim($tag))
                $tag = $this->GetPageTag();
            if ($page = $this->LoadPage($tag, $time))
                return $page["owner"];
        }
        function SetPageOwner($tag, $user) {
            // check if user exists
            if (!$this->LoadUser($user))
                return;
             
            // updated latest revision with new owner
            $this->Query("update ".$this->config["table_prefix"]."pages set owner = '".mysqli_real_escape_string($this->dblink,$user)."' where tag = '".mysqli_real_escape_string($this->dblink,$tag)."' and latest = 'Y' limit 1");
        }
        function LoadAcl($tag, $privilege, $useDefaults = 1) {
            if ((!$acl = $this->LoadSingle("select * from ".$this->config["table_prefix"]."acls where page_tag = '".mysqli_real_escape_string($this->dblink,$tag)."' and privilege = '".mysqli_real_escape_string($this->dblink,$privilege)."' limit 1")) && $useDefaults) {
                $acl = array("page_tag" => $tag, "privilege" => $privilege, "list" => $this->GetConfigValue("default_".$privilege."_acl"));
            }
            return $acl;
        }
        function SaveAcl($tag, $privilege, $list) {
            if ($this->LoadAcl($tag, $privilege, 0))
                $this->Query("update ".$this->config["table_prefix"]."acls set list = '".mysqli_real_escape_string($this->dblink,trim(str_replace("\r", "", $list)))."' where page_tag = '".mysqli_real_escape_string($this->dblink,$tag)."' and privilege = '".mysqli_real_escape_string($this->dblink,$privilege)."' limit 1");
            else
                $this->Query("insert into ".$this->config["table_prefix"]."acls set list = '".mysqli_real_escape_string($this->dblink,trim(str_replace("\r", "", $list)))."', page_tag = '".mysqli_real_escape_string($this->dblink,$tag)."', privilege = '".mysqli_real_escape_string($this->dblink,$privilege)."'");
        }
        // returns true if $user (defaults to current user) has access to $privilege on $page_tag (defaults to current page)
        function HasAccess($privilege, $tag = "", $user = "") {
            // see whether user is registered and logged in
            if ($user = $this->GetUser())
                $registered = true;
            $isadmin = $this->IsAdmin($user);
            // set defaults
            if (!$tag = trim($tag))
                $tag = $this->GetPageTag();
            if (!$user = $this->GetUserName())
                ;
             
            // load acl
            $acl = $this->LoadAcl($tag, $privilege);
             
            //if current user is admin, return true. Of course he can do anything.
            if ($this->IsManager())
            return true;
            //if frozen
            if ($this->GetConfigValue("freeze") && $privilege!="show")return false;  
            // if current user is owner, return true. Owner can do anything too!
            if ($this->UserIsOwner($tag))
                return true;
             
            // fine fine... now go through acl
            foreach (explode("\n", $acl["list"]) as $line) {
                $line = trim($line);
                 
                // check for inversion character "!"
                if (preg_match("/^[!](.*)$/", $line, $matches)) {
                    $negate = true;
                    $line = $matches[1];
                } else {
                    $negate = false;
                }
                 
                // if there's still anything left... lines with just a "!" don't count!
                if ($line) {
                    switch ($line[0]) {
                        // comments
                        case "":
                        break;
                        // everyone
                        case "*":
                        return !$negate;
                        // only registered users
                        case "#":
                        return ($registered) ? !$negate :
                         false;
                        // aha! a user entry.
                        default:
                        if ($line == $user) {
                            return !$negate;
                        }
                    }
                }
            }
             
            // tough luck.
            return false;
        }
        function basic_password_verify($pswd, $hash) {
            if (strlen($hash)==32) {
                return md5($pswd) == $hash;
            } else {
                if (version_compare(phpversion(), '5.5.0', '>=')) {
                    return password_verify($pswd, $hash);
                } /* else {
                    # https://github.com/ircmaxell/password_compat
                } */
            }
            return false;
        }
        function ldap_authenticate_by_username( $p_username, $p_password ) {
                $c_username = $p_username;

                $t_ldap_organization = $this->GetConfigValue('ldap_organization');
                $t_ldap_root_dn = $this->GetConfigValue( 'ldap_root_dn' );
        
                $t_ldap_uid_field = $this->GetConfigValue('ldap_uid_field');
                $t_search_filter = "(&$t_ldap_organization($t_ldap_uid_field=$c_username))";
                $t_search_attrs = array('mail','dn');
        
                # Bind
                //log_event( LOG_LDAP, "Binding to LDAP server" );
                $ldapconn = ldap_connect($this->GetConfigValue('ldap_server'));
                if ( $ldapconn === false ) {
                    return false;
                }
                
                ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
                $ldaprdn = $this->GetConfigValue('ldap_bind_dn');
                $ldappass = $this->GetConfigValue('ldap_bind_pswd');
                $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
                // verify binding
                if ($ldapbind) {
                    //echo "LDAP bind successful...";
                } else {
                    //echo "LDAP bind failed...";
                    return false;
                }

                # Search for the user id
                $t_sr = ldap_search( $ldapconn, $t_ldap_root_dn, $t_search_filter, $t_search_attrs );
                if ( $t_sr === false ) {
                    ldap_unbind( $ldapconn );
                    return false;
                }
        
                $t_info = ldap_get_entries( $ldapconn, $t_sr );
                if ( $t_info === false ) {
                    ldap_free_result( $t_sr );
                    ldap_unbind( $ldapconn );
                    return false;
                }
        
                $t_authenticated = false;
        
                if ( $t_info['count'] > 0 ) {
                    # Try to authenticate to each until we get a match
                    for ( $i = 0; $i < $t_info['count']; $i++ ) {
                        $t_dn = $t_info[$i]['dn'];
        
                        # Attempt to bind with the DN and password
                        if ( ldap_bind( $ldapconn, $t_dn, $p_password ) ) {
                            $t_authenticated = true;
                            break;
                        }
                    }
                } else {
                    return false;
                }
                
                ldap_free_result( $t_sr );
                ldap_unbind( $ldapconn );
                return $t_authenticated;
        }
         
        // XML
        function WriteRecentChangesXML() {
            $xml = "<?xml version=\"1.0\" encoding=\"".$this->GetConfigValue("charset")."\"?>\n";
            $xml .= "<rss version=\"0.92\">\n";
            $xml .= "<channel>\n";
            $xml .= "<title>".$this->GetConfigValue("wakka_name")." - RecentChanges</title>\n";
            $xml .= "<link>".$this->GetConfigValue("base_url")."</link>\n";
            $xml .= "<description>Recent changes to the ".$this->GetConfigValue("wakka_name")." WakkaWiki</description>\n";
            $xml .= "<language>en-us</language>\n";
             
            if ($pages = $this->LoadRecentlyChanged_all()) {
                foreach ($pages as $i => $page) {
                    if ($i < 50) {
                        $xml .= "<item>\n";
                        $xml .= "<title>".$page["tag"]."</title>\n";
                        $xml .= "<link>".$this->href("show", $page["tag"], "time=".urlencode($page["time"]))."</link>\n";
                        $xml .= "<description>".$page["time"]." by ".$page["user"].($page['note']?"<br />Summary:".$page['note']:"")."</description>\n";
                        $xml .= "</item>\n";
                    }
                }
            }
             
            $xml .= "</channel>\n";
            $xml .= "</rss>\n";
             
            $filename = "xml/recentchanges_".preg_replace("/[^a-zA-Z0-9]/", "", strtolower($this->GetConfigValue("wakka_name"))).".xml";
             
            $fp = @fopen($filename, "w");
            if ($fp) {
                fwrite($fp, $xml);
                fclose($fp);
            }
        }
         
        // MAINTENANCE
        function Maintenance() {
            // purge referrers
            if ($days = $this->GetConfigValue("referrers_purge_time")) {
                $this->Query("delete from ".$this->config["table_prefix"]."referrers where time < date_sub(now(), interval '".mysqli_real_escape_string($this->dblink,$days)."' day)");
            }
             
            // purge old page revisions
            if ($days = $this->GetConfigValue("pages_purge_time")) {
                $this->Query("delete from ".$this->config["table_prefix"]."pages where time < date_sub(now(), interval '".mysqli_real_escape_string($this->dblink,$days)."' day) and latest = 'N'");
            }
        }
        function GetLastDiff($tag) {
            $query="type=last";
            return $this->Href("diff", $tag, $query);
        }
        function GetLastDiffID($tag,$id="") {
            if(!$id)$id=$this->GetLatestPageID($tag);
            $result['a']=$id;
            if($page=$this->LoadSingle("select id from ".$this->GetConfigValue("table_prefix")."pages where tag='".mysqli_real_escape_string($this->dblink,$tag)."' and id < '$id' ORDER BY 'id' DESC LIMIT 1;")){
                $result['b']=$page['id'];
            }
            return $result['b'];
        }
        /**
        *
        * Log the trace of user
        *
        */
        function LogTrace($tag="") {
            if(!$tag) {
                $tag=$this->tag;
            }
            if(!$_SESSION['trace_p']){
                $_SESSION['trace']=array();
                $_SESSION['trace'][0]=$tag;
                $_SESSION['trace_p']=1;  //i don't know why i can't use count(), so i use this var.
                return;
            }
            $m_remove=false;
            $m_count=$_SESSION['trace_p'];
            for($i=0;$i<$m_count;$i++){
                if($m_remove)$_SESSION['trace'][$i-1]=$_SESSION['trace'][$i];
                if($_SESSION['trace'][$i]==$tag)$m_remove=true;
            }
            if($m_remove) {
                $_SESSION['trace'][$m_count-1]=$tag;
            } else {
                if($m_count>=$this->GetConfigValue("max_trace")){
                  	for($i=1;$i<$m_count;$i++) {
                  	    $_SESSION['trace'][$i-1]=$_SESSION['trace'][$i];
                  	}
                  	$_SESSION['trace'][$m_count-1]=$tag;
                    return;
                }
                
                $_SESSION['trace'][$m_count]=$tag;
                $_SESSION['trace_p']++;
            }
            /*// used to reset when debug
            $_SESSION['trace']="";
            $_SESSION['trace_p']="";
            */
        }
        // THE BIG EVIL NASTY ONE!
        function Run($tag, $method = "") {
            if(preg_match("#^CCW_SYS_#i",$tag)) {
                $this->Redirect($this->href("", $this->config["root_page"]));
            }
            //$this->Maintenance(); // TODO: maybe only do this occasionally?
            $this->ReadInterWikiConfig();
            // do our stuff!
            if (!$this->method = trim($method)) {
                $this->method = "show";
            }
            if (!$this->tag = trim($tag)) {
                $this->Redirect($this->href("", $this->config["root_page"]));
            }
            // if ((!$this->GetUser() && $_COOKIE["name"]) && ($user = $this->LoadUser($_COOKIE["name"], $_COOKIE["password"]))) {
            //     $this->SetUser($user);
            // }
            $this->LogReferrer($tag);
            $this->SetPage($this->LoadPage($tag, $_REQUEST["time"]));
            if ($this->page) {
                $this->LogTrace($this->page['comment_on']);//if comment_on is not null then log the page which the comment belongs to.
            } 
            if (!preg_match("/\.[\S]+$/", $this->method)) {
                print($this->Header().$this->Method($this->method).$this->Footer());
            } else {
                print($this->Method($this->method));
            }
        }
    }
     
?>
