<?php
     
    /*
    Yes, most of the formatting used in this file is HORRIBLY BAD STYLE. However,
    most of the action happens outside of this file, and I really wanted the code
    to look as small as what it does. Basically. Oh, I just suck. :)
    */
     
    //Tweaked and Internationalized By:
    //CooCooWakka http://www.hsfz.net.cn/coo/wiki
    //CooYip cooyeah@gmail.com
    // $Id: wakka.php,v 1.10 2005/12/23 04:33:53 cooyeah Exp $
     
    require("./libs/clswakka.php");
    define("WAKKA_VERSION", "0.1.2");
    define("COO_VERSION", "0.0.9rc3");
    define("COO_CVS_TAG", '$Id: wakka.php,v 1.10 2005/12/23 04:33:53 cooyeah Exp $');
     
    /*
    Moved Wakka Class to libs/clswakka.php
    Jan 2005
    */
     
    @error_reporting(E_ALL^E_NOTICE);
     
    // stupid version check
    if (!isset($_REQUEST))
        die('$_REQUEST[] not found. Wakka requires PHP 4.1.0 or higher!');
    // Store Callback
    if (!function_exists("CooSavecallback")) {
        function CooSavecallback($things) {
            $thing = $things[1];
            global $wakka;
            if ($thing == "/*sign*/") {
                $today = date($wakka->GetConfigValue('time_stamp_format'));
                // get current user
                $user = $wakka->GetUserName();
                return "By **$user** at $today.";
            }
            else if($thing == "    ") {
                return " ";
            }
            else
                return $thing;
        }
    }
     
    //Set include path
    $include_path = ini_get("include_path");
    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
    ini_set("include_path", ".;./local/libs/;./libs/;".$include_path);
    else
        ini_set("include_path", ".:./local/libs/:./libs/:".$include_path);
     
    // workaround for the amazingly annoying magic quotes.
    function magicQuotesSuck(&$a) {
        if (is_array($a)) {
            foreach ($a as $k => $v) {
                if (is_array($v))
                    magicQuotesSuck($a[$k]);
                else
                    $a[$k] = stripslashes($v);
            }
        }
    }
    // set_magic_quotes_runtime(0);
    if (get_magic_quotes_gpc()) {
        magicQuotesSuck($_POST);
        magicQuotesSuck($_GET);
        magicQuotesSuck($_COOKIE);
        magicQuotesSuck($_REQUEST);
    }
     
    $def_base_url = "http://".$_SERVER["SERVER_NAME"].($_SERVER["SERVER_PORT"] != 80 ? ":".$_SERVER["SERVER_PORT"] : "").$_SERVER["REQUEST_URI"].(preg_match("/".preg_quote("wakka.php")."$/", $_SERVER["REQUEST_URI"]) ? "?wakka=" : "");
    if (preg_match("/^(.*?)wakka.php(.*?)$/", $def_base_url, $matches))$def_base_path = $matches[1];
    else $def_base_path = $def_base_url;
    // default configuration values
    $wakkaDefaultConfig = array(
    "mysql_host" => "localhost",
        "mysql_database" => "wakka",
        "mysql_user" => "wakka",
        "table_prefix" => "wakka_",
         
    "root_page" => "HomePage",
        "wakka_name" => "MyCooCooWakkaSite",
        "base_url" => $def_base_url,
        "base_path" => $def_base_path,
        "rewrite_mode" => (preg_match("/".preg_quote("wakka.php")."$/", $_SERVER["REQUEST_URI"]) ? "0" : "1"),
         
    "action_path" => "actions",
        "handler_path" => "handlers",
         
    "header_action" => "header",
        "footer_action" => "footer",
        "navigation_links" => "PageIndex :: CategoryCategory :: RecentChanges :: RecentlyCommented :: [[UserSettings _MI_USERSETTINGS]]",
        "logged_in_navigation_links" => "PageIndex :: CategoryCategory :: RecentChanges :: RecentlyCommented :: [[UserSettings _MI_USERSETTINGS_ON]]",
        "referrers_purge_time" => 1,
        "pages_purge_time" => 0,
         
    "hide_comments" => 0,
        "admin_users" => "",
        "default_write_acl" => "*",
        "default_read_acl" => "*",
        "default_comment_acl" => "*",
        "upload_path" => "./upload",
        "mime_types" => "mime.types",
        "language" => "english",
        "charset" => "gb2312",
        "SpecialCharsetSupport" => "auto",
        "AllowHtmlTags" => "<a><i><b><br><p><table><tr><td><img><th><strong><font>",
        "default_css" => "wakka.css",
        "AutoAddAnchor" => "0",
        "AllowUploadExts" => "png|jpg|gif|bmp|exe|zip|rar|gz|tar|bz2|rtf|doc|txt|mp3|rm|wma|wav",
        "AllowUploadMaxFileSize" => "5242880",
        "ccuid" => md5(uniqid("")),
        "session_name" => uniqid("ccwakka"),
        "time_stamp_format" => "D F j, Y, g:i a",
        "max_trace" => 10,
        "no_referrer" => 0,
        "open_register" => 0,
        "login_method" => "basic", // basic OR ldap
        "ldap_server" => "ldap://localhost",
        "ldap_organization" => "",
        "ldap_root_dn" => "dc=example,dc=com",
        "ldap_uid_field" => "uid",
        "ldap_bind_dn" => "cn=admin,dc=example,dc=com",
        "ldap_bind_pswd" => "admin",
        "freeze" => 0 );
     
     
     
    // load config
    if (!$configfile = GetEnv("WAKKA_CONFIG"))
        $configfile = "wakka.config.php";
    if (file_exists($configfile))
        include($configfile);
    $wakkaConfigLocation = $configfile;
    if (is_array($wakkaConfig))$wakkaConfig = array_merge($wakkaDefaultConfig, $wakkaConfig);
    else $wakkaConfig = $wakkaDefaultConfig;
    $clang = $_COOKIE['language'];
    if ($clang)
        $wakkaConfig["language"] = $clang;
    if (!@include_once("./language/".$wakkaConfig["language"]."/main.php"))
        $lang_err = 1;
    if ($wakkaconfig["language"] != "english")
        include_once("./language/english/main.php");
    if ($lang_err) {
        //echo "I have to use English~~";
        @include_once("./language/english/main.php") or die("<h3>Language error! Stop.</h3>");
    }
    $specialwakkapage_def = array(
    "PageIndex" => _MI_PAGEINDEX,
        "RecentChanges" => _MI_RECENTCHANGES,
        "RecentlyCommented" => _MI_RECENTLYCOMMENTED,
        "HomePage" => _MI_HOMEPAGE,
        "MyChanges" => _MI_MYCHANGES,
        "MyPages" => _MI_MYPAGES,
        "OrphanedPages" => _MI_ORPHANEDPAGES,
        "TextSearch" => _MI_TEXTSEARCH,
        "WantedPages" => _MI_WANTEDPAGES,
        "UserSettings" => _MI_USERSETTINGS,
        "UploadFile" => _MI_UPLOADFILE,
        "CategoryCategory" => _MI_CATEGORYCATEGORY,
        );
    $trans_nav = array(
    "_MI_PAGEINDEX" => _MI_PAGEINDEX,
        "_MI_RECENTCHANGES" => _MI_RECENTCHANGES,
        "_MI_RECENTLYCOMMENTED" => _MI_RECENTLYCOMMENTED,
        "_MI_HOMEPAGE" => _MI_HOMEPAGE,
        "_MI_MYCHANGES" => _MI_MYCHANGES,
        "_MI_MYPAGES" => _MI_MYPAGES,
        "_MI_ORPHANEDPAGES" => _MI_ORPHANEDPAGES,
        "_MI_TEXTSEARCH" => _MI_TEXTSEARCH,
        "_MI_WANTEDPAGES" => _MI_WANTEDPAGES,
        "_MI_USERSETTINGS_ON" => _MI_USERSETTINGS_ON,
        "_MI_USERSETTINGS" => _MI_USERSETTINGS,
        "_MI_UPLOADFILE" => _MI_UPLOADFILE,
        );
     
    $wakkaConfig1 = $wakkaConfig;
    //reload config after get the language setting.
    if (file_exists($configfile))
        include($configfile);
    $wakkaConfigLocation = $configfile;
    if (is_array($wakkaConfig))$wakkaConfig = array_merge($wakkaConfig1, $wakkaConfig);
    else $wakkaConfig = $wakkaConfig1;
    if ($clang)
        $wakkaConfig["language"] = $clang;
     
    if (is_array($specialwakkapage))$specialwakkapage_tmp = array_merge($specialwakkapage_def, $specialwakkapage);
    else $specialwakkapage_tmp = $specialwakkapage_def;
    global $specialwakkapage;
    $specialwakkapage = $specialwakkapage_tmp;
     
    // check for locking
    if (file_exists("locked")) {
        // read password from lockfile
        $lines = file("locked");
        $lockpw = trim($lines[0]);
         
        // is authentification given?
        if (isset($_SERVER["PHP_AUTH_USER"])) {
            if (!(($_SERVER["PHP_AUTH_USER"] == "admin") && ($_SERVER["PHP_AUTH_PW"] == $lockpw))) {
                $ask = 1;
            }
        } else {
            $ask = 1;
        }
         
        if ($ask) {
            header("WWW-Authenticate: Basic realm=\"".$wakkaConfig["wakka_name"]." Install/Upgrade Interface\"");
            header("HTTP/1.0 401 Unauthorized");
            print("This site is currently being upgraded. Please try again later.");
            exit;
        }
    }
     
     
    // compare versions, start installer if necessary
    if ($wakkaConfig["wakka_version"] != WAKKA_VERSION || $wakkaConfig["coo_version"] != COO_VERSION) {
        // start installer
        if (!$installAction = trim($_REQUEST["installAction"]))
            $installAction = "default";
        include("setup/header.php");
        if (file_exists("setup/".$installAction.".php"))
            include("setup/".$installAction.".php");
        else
            print("<em>Invalid action</em>");
        include("setup/footer.php");
        exit;
    }
     
    $wakkaConfig["logged_in_navigation_links"] = strtr($wakkaConfig["logged_in_navigation_links"], $trans_nav);
    $wakkaConfig["navigation_links"] = strtr($wakkaConfig["navigation_links"], $trans_nav);
     
     
    // set session name
    session_name($wakkaConfig["session_name"]);
     
    // start session
    session_start();
     
    // fetch wakka location
    $wakka = $_REQUEST["wakka"];
     
    // remove leading slash
    $wakka = preg_replace("/^\//", "", $wakka);
     
    // split into page/method
    if (preg_match("#^(.+?)/(.*)$#", $wakka, $matches))
        list(, $page, $method) = $matches;
    else if (preg_match("#^(.*)$#", $wakka, $matches))
    list(, $page) = $matches;
     
    // create wakka object
    $wakka = new Wakka($wakkaConfig);
     
    // go!
    $wakka->Run($page, $method);
    // print_r($_SERVER);
    ini_set("include_path", $include_path); //Restore the settings;
?>
