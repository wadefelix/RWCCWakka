<?php

// fetch configuration
$config = $_POST["config"];

// test configuration
print("<strong>Testing Configuration</strong><br />\n");
test("Testing MySQL connection settings...", $dblink = @mysql_connect($config["mysql_host"], $config["mysql_user"], $config["mysql_password"]));
test("Looking for database...", @mysql_select_db($config["mysql_database"], $dblink), "The database you configured was not found. Remember, it needs to exist before you can install/upgrade Wakka!");
print("<br />\n");

// do installation stuff
if (!$version = trim($wakkaConfig["wakka_version"])) $version = "0";
if (!$coov = trim($wakkaConfig["coo_version"])) $coov = "0";
switch ($version)
{
// new installation
case "0":
	print("<strong>Installing Stuff</strong><br />\n");
	$pageok=test("Creating page table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."pages (".
  			"id int(10) unsigned NOT NULL auto_increment,".
  			"tag varchar(50) NOT NULL default '',".
  			"time datetime NOT NULL default '0000-00-00 00:00:00',".
  			"body text NOT NULL,".
  			"body_r text NOT NULL,".
  			"owner varchar(50) NOT NULL default '',".
  			"category varchar(50) NOT NULL default '',".
			"user varchar(50) NOT NULL default '',".
  			"latest enum('Y','N') NOT NULL default 'N',".
  			"handler varchar(30) NOT NULL default 'page',".
  			"comment_on varchar(50) NOT NULL default '',".
  			"isnew enum('N','Y') NOT NULL default 'N',".
			"tinychange enum('N','Y') NOT NULL default 'N',".
  			"note varchar(255) default NULL,".
			"aliasname varchar(50) NOT NULL default '',".
			"keywords varchar(100) NOT NULL default '',".
			"description varchar(100) NOT NULL default '',".
			"refer_count int(11) NOT NULL default '0',".
		        "view_count int(11) NOT NULL default '0',".
			"tview_count int(11) NOT NULL default '0',".
			"PRIMARY KEY  (id),".
  			"FULLTEXT KEY tag (tag,body),".
  			"KEY idx_tag (tag),".
  			"KEY idx_time (time),".
  			"KEY idx_latest (latest),".
  			"KEY idx_comment_on (comment_on)".
			") ENGINE=MyISAM;", $dblink), "Already exists?", 0);
	test("Creating ACL table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."acls (".
  			"page_tag varchar(50) NOT NULL default '',".
			"privilege varchar(20) NOT NULL default '',".
  			"list text NOT NULL,".
 			"PRIMARY KEY  (page_tag,privilege)".
			") ENGINE=MyISAM", $dblink), "Already exists?", 0);
	test("Creating link tracking table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."links (".
			"from_tag char(50) NOT NULL default '',".
  			"to_tag char(50) NOT NULL default '',".
  			"UNIQUE KEY from_tag (from_tag,to_tag),".
  			"KEY idx_from (from_tag),".
  			"KEY idx_to (to_tag)".
			") ENGINE=MyISAM", $dblink), "Already exists?", 0);
	test("Creating referrer table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."referrers (".
  			"page_tag char(50) NOT NULL default '',".
  			"referrer char(150) NOT NULL default '',".
  			"time datetime NOT NULL default '0000-00-00 00:00:00',".
  			"KEY idx_page_tag (page_tag),".
  			"KEY idx_time (time)".
			") ENGINE=MyISAM", $dblink), "Already exists?", 0);
	test("Creating user table...",
		@mysql_query(
			"CREATE TABLE ".$config["table_prefix"]."users (".
  			"name varchar(80) NOT NULL default '',".
  			"password varchar(32) NOT NULL default '',".
  			"email varchar(50) NOT NULL default '',".
  			"motto text NOT NULL,".
  			"revisioncount int(10) unsigned NOT NULL default '20',".
  			"changescount int(10) unsigned NOT NULL default '50',".
  			"doubleclickedit enum('Y','N') NOT NULL default 'Y',".
  			"signuptime datetime NOT NULL default '0000-00-00 00:00:00',".
  			"show_comments enum('Y','N') NOT NULL default 'N',".
  			"PRIMARY KEY  (name),".
  			"KEY idx_name (name),".
  			"KEY idx_signuptime (signuptime)".
			") ENGINE=MyISAM", $dblink), "Already exists?", 0);
	if($pageok){
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = '".$config["root_page"]."', body = '".mysql_real_escape_string("Welcome to your [[CooCooWakka:CooCooWakka CooCooWakka]] site! Click on the \"Edit this page\" link at the bottom to get started.\n\nAlso don't forget to visit [[CooCooWakka:HomePage]]!\n\nUseful pages: OrphanedPages, WantedPages, TextSearch, UploadFile, CategoryCategory.")."', user = 'WakkaInstaller', time = now(), latest = 'Y' , isnew='Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'RecentChanges', body = '{{RecentChanges}}', user = 'WakkaInstaller', time = now(), latest = 'Y', isnew='Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'RecentlyCommented', body = '{{RecentlyCommented}}', user = 'WakkaInstaller', time = now(), latest = 'Y', isnew='Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'UserSettings', body = '{{UserSettings}}', user = 'WakkaInstaller', time = now(), latest = 'Y', isnew='Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'PageIndex', body = '{{PageIndex}}', user = 'WakkaInstaller', time = now(), latest = 'Y', isnew='Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'WantedPages', body = '{{WantedPages}}', user = 'WakkaInstaller', time = now(), latest = 'Y', isnew='Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'OrphanedPages', body = '{{OrphanedPages}}', user = 'WakkaInstaller', time = now(), latest = 'Y', isnew='Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'TextSearch', body = '{{TextSearch}}', user = 'WakkaInstaller', time = now(), latest = 'Y', isnew='Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'MyPages', body = '{{MyPages}}', user = 'WakkaInstaller', time = now(), latest = 'Y',isnew='Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'MyChanges', body = '{{MyChanges}}', user = 'WakkaInstaller', time = now(), latest = 'Y',isnew='Y'", $dblink);
//	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'PageIndex', body = '{{PageIndex}}', user = 'WakkaInstaller', time = now(), latest = 'Y',isnew='Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'CategoryCategory',body ='".mysql_real_escape_string("{{category  format=\"owner time history\"}}")."',user = 'WakkaInstaller', time=now(), latest='Y',isnew='Y'",$dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'UploadFile', body = '{{files}}', user = 'WakkaInstaller', time = now(), latest = 'Y',isnew='Y'", $dblink);
	mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'GoodStyle', body = '".mysql_real_escape_string("[[CooCooWakka:GoodStyle CooCooWakka's Style Guide]]")."', user = 'WakkaInstaller', time = now(), latest = 'Y', isnew='Y'", $dblink);
        mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'CooCooWakka', body = '".mysql_real_escape_string("[[CooCooWakka:HomePage Visit CooCooWakka's Home]]")."', user = 'WakkaInstaller', time = now(), latest = 'Y', isnew='Y'", $dblink);

	test("Adding some pages...", 1);
	}
	test("Reticulating splines...", 1);
	test("Writing TPS report...", 1);
	break;

// The funny upgrading stuff. Make sure these are in order! //
// And yes, there are no break;s here. This is on purpose.  //

// from 0.1 to 0.1.1
case "0.1":
	print("<strong>0.1 to 0.1.1</strong><br />\n");
	test("Just very slightly altering the pages table...", 
		@mysql_query("alter table ".$config["table_prefix"]."pages add body_r text not null default '' after body", $dblink), "Already done? Hmm!", 0);
	test("Claiming all your base...", 1);

// from 0.1.1 to 0.1.2
case "0.1.1":
	print("<strong>0.1.1 to 0.1.2</strong><br />\n");
	test("Sending hatemail to the Wakka developers...", 1);
	test("Writing a negative C&C Generals review...", 1);
	test("Generating world peace...", 1);
case "0.1.2":
	switch($coov){
	case "0":
//	if(!$wakkaConfig["logged_in_navigation_links"]){ //beta1
//	print("<strong>Upgrade to CooCooWakka 0.0.1</strong><br />\n");
//	test("Add Category...",mysql_query("alter table ".$config["table_prefix"]."pages add category varchar(50) not null default '' after owner;?,$dblink),"OK,You don't have to do that~",0);
	print("<br /><strong>Preparing...</strong><br />");
@mysql_query("alter table ".$config["table_prefix"]."pages add category varchar(50) not null default '' after owner;",$dblink);
@mysql_query("alter table ".$config["table_prefix"]."pages ADD tinychange ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL , ADD note VARCHAR( 50 ), ADD isnew ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL AFTER comment_on ;",$dblink);
//	}
//	if(!$wakkaConfig["AllowHtmlTags"]){ //0.0.2
//	print("<strong>Upgrade to CooCooWakka 0.0.2</strong><br />\n");
/*	test("Add two small marks...",mysql_query("alter table ".$config["table_prefix"]."pages 
ADD tinychange ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL , ADD note VARCHAR( 50 ),
ADD isnew ENUM( 'N', 'Y' ) DEFAULT 'N' NOT NULL AFTER comment_on ;
",$dblink),"Nothing Wrong,Go on~",0);
//	}*/
	print("<strong>Upgrading To CooCooWakka 0.0.7.0!</strong><br />\n");
	/*test("Add Category...",*/
	$v7=mysql_query("alter table ".$config["table_prefix"]."pages add `aliasname` VARCHAR( 50 ) NOT NULL ,ADD `keywords` VARCHAR( 100 ) NOT NULL ,ADD `description` VARCHAR( 100 ) NOT NULL ,ADD `edit_count` INT NOT NULL ,ADD `view_count` INT NOT NULL ,ADD `tview_count` INT NOT NULL ;",$dblink);
	test("Upgrading DataBase...",$v7,"What's Up?",0);
	//mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'GoodStyle', body = '".mysql_escape_string("[[CooCooWakka:GoodStyle CooCooWakka's Style Guide]]")."', user = 'WakkaInstaller', time = now(), latest = 'Y', isnew='Y'", $dblink);
        //mysql_query("insert into ".$config["table_prefix"]."pages set tag = 'CooCooWakka', body = '".mysql_escape_string("[[CooCooWakka:HomePage Visit CooCooWakka's Home]]")."', user = 'WakkaInstaller', time = now(), latest = 'Y', isnew='Y'", $dblink);
	installpage("GoodStyle","[[CooCooWakka:GoodStyle CooCooWakka's Style Guide]]",$config["table_prefix"],$dblink);
	installpage("CooCooWakka","[[CooCooWakka:HomePage Visit CooCooWakka's Home]]",$config["table_prefix"],$dblink);
       test("Adding some new pages...",1);
       case "0.0.7":
       print("<strong>Upgrading To CooCooWakka 0.0.7.1!</strong><br />\n");
       test("Fixing .htaccess...",1);
       case "0.0.7.1":
       print("<strong>Upgrading To CooCooWakka 0.0.7.2!</strong><br />\n");
       $v72=mysql_query("ALTER TABLE ".$config["table_prefix"]."pages CHANGE `edit_count` `refer_count` INT(11) DEFAULT '0' NOT NULL;",$dblink);
       test("Creating counter...",$v72,"~Er???",0);
       case "0.0.7.2":
	print("<strong>Upgrading To CooCooWakka 0.0.7.3!</strong><br />\n");
	test("Adding Table Parser...",1);
	test("Adding PageMoving handler...",1);
       case "0.0.7.3":
	print("<strong>Upgrading To CooCooWakka 0.0.7.4!</strong><br />\n");
	test("Fixing...",1);
        case "0.0.7.4":
    print("<strong>Upgrading To CooCooWakka 0.0.7.5!</strong><br />\n");
    test("Getting used to Mozilla...",1);
    test("Fixing some tiny bugs...",1);
        case "0.0.7.5":
    print("<strong>Upgrading To CooCooWakka 0.0.7.6!</strong><br />\n");
    test("Fixing formatters...",1);
        case "0.0.7.6":
    print("<strong>Upgrading To CooCooWakka 0.0.7.7!</strong><br />\n");
    test("Fixing files action...",1);
        case "0.0.7.7":
    print("<strong>Upgrading To CooCooWakka 0.0.7.8!</strong><br />\n");
    test("Fixing Big5 supporting Issues...",1);
    test("Improving email address recongizer...",1);
    test("Adding vim formatter...",1);
    test("Fixing monospace bug...",1);
	case "0.0.7.8":
    print("<strong>Upgrading To CooCooWakka 0.0.7.9!</strong><br />\n");
    test("Fixing wakka formatter...",1);
    test("Add some beta code which is uesless now :-)...",1);
    case "0.0.7.9":
    print("<strong>Upgrading To CooCooWakka 0.0.8.0!</strong><br />\n");
    test("Adding new management interface...",1);
    test("Using new Edit/Diff actions...",1);
    test("Adding PEAR_HTML_QUICKFORM...",1);
    case "0.0.8.0":
    print("<strong>Upgrading To CooCooWakka 0.0.8.1!</strong><br />\n");
    test("Adding BadWords list...",1);
    test("Improving security...",1);
    test("Resetting session name...",1);
    $config['session_name']=uniqid("ccwakka");
    case "0.0.8.1":
    print("<strong>Upgrading To CooCooWakka 0.0.8.2!</strong><br />\n");
    $v82=mysql_query("ALTER TABLE ".$config["table_prefix"]."pages CHANGE `note` `note` VARCHAR(255) DEFAULT NULL",$dblink);
    test("Making a tiny change of the DataBase...",$v82,"Failed?",0);
    test("Fixing a bug on Windows Platform...",1);
    test("Updating RecentChanges/FilesAction/LocalImage/PageIndex...",1);
    case "0.0.8.2":
    print("<strong>Upgrading To CooCooWakka 0.0.8.3!</strong><br />\n");
    test("Fixing some bugs...",1);
    case "0.0.8.3":
    print("<strong>Upgrading To CooCooWakka 0.0.8.4!</strong><br />\n");
    test("Fixing some bugs~~...",1);
    case "0.0.8.4":
    print("<strong>Upgrading To CooCooWakka 0.0.8.5!</strong><br />\n");
    test("Fixing some bugs~~~...",1);
    case "0.0.8.5":
    print("<strong>Upgrading To CooCooWakka 0.0.8.6!</strong><br />\n");
    test("Fixing some bugs~~~~...",1);
    case "0.0.8.6":
    print("<strong>Upgrading To CooCooWakka 0.0.8.6.1!</strong><br />\n");
    test("Fixing some bugs~~~~...",1);
    print("<strong>Upgrading To CooCooWakka 0.0.9rc1!</strong><br />\n");
    test("Adding many new features...",1);
    case "0.0.9rc1":
    print("<strong>Upgrading To CooCooWakka 0.0.9rc2!</strong><br />\n");
    test("Upgrading...",1);
    case "0.0.9rc2":
    print("<strong>Upgrading To CooCooWakka 0.0.9rc3!</strong><br />\n");
    test("Upgrading...",1);
}	
}

?>

<p>
In the next step, the installer will try to write the updated configuration file, <tt><?php echo $wakkaConfigLocation ?></tt>.
Please make sure the web server has write access to the file, or you will have to edit it manually.
Once again, see <a href="http://www.wakkawiki.com/WakkaInstallation" target="_blank">WakkaWiki:WakkaInstallation</a> for details.
</p>

<form action="<?php echo myLocation(); ?>?installAction=writeconfig" method="POST">
<input type="hidden" name="config" value="<?php echo htmlentities(serialize($config)) ?>" />
<input type="submit" value="Continue" />
</form>
