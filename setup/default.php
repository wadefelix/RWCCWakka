<?php
//check mod_rewrite
if(function_exists(apache_get_modules)){
$mods=@apache_get_modules();
if(is_array($mods)){
if(in_array("mod_rewrite",$mods))$rewritable=1;
}
}else
$rewritable=1;
?>
<form action="<?php echo myLocation() ?>?installAction=install" method="POST">
  <table>
    <tr> 
      <td></td>
      <td><strong>CooCooWakka Installation</strong></td>
    </tr>
    <?php
    $wakkaConfig['mysql_password']="";
	if ($wakkaConfig["wakka_version"])
	{
		print("<tr><td></td><td>Your installed Wakka is reporting itself as ".$wakkaConfig["coo_version"]."(".$wakkaConfig["wakka_version"]."). You are about to <strong>upgrade</strong> to CooCooWakka ".COO_VERSION.". Please review your configuration settings below.<strong>Please Retype the Database Password and Pay Special Attention to the Language and Charset Settings</strong></td></tr>\n");
	}
	else
	{
		print("<tr><td></td><td>Since there is no existing CooCooWakka configuration, this probably is a fresh CooCooWakka install. You are about to install CooCooWakka ".COO_VERSION.". Please configure your CooCooWakka site using the form below.</td></tr>\n");
	}
	?>
    <tr> 
      <td></td>
      <td><br />
        NOTE: This installer will try to write the configuration data to the file 
        <tt>wakka.config.php</tt>, located in your CooCooWakka directory. In order for 
        this to work, you must make sure the web server has write access to that 
        file! If you can't do this, you will have to edit the file manually (the 
        installer will tell you how).<br /> <br />
        See <a href="http://www.wakkawiki.com/WakkaInstallation" target="_blank">WakkaWiki:WakkaInstallation</a> 
        for details.</td>
    </tr>
    <tr> 
      <td></td>
      <td><br /> <strong>Database Configuration</strong></td>
    </tr>
    <tr> 
      <td></td>
      <td>The host your MySQL server is running on. Usually "localhost" (ie, the 
        same machine your CooCooWakka site is on).</td>
    </tr>
    <tr> 
      <td align="right" nowrap>MySQL host:</td>
      <td><input type="text" size="50" name="config[mysql_host]" value="<?php echo $wakkaConfig["mysql_host"] ?>" /></td>
    </tr>
    <tr> 
      <td></td>
      <td>The MySQL database CooCooWakka should use. This database needs to exist already 
        once you continue!</td>
    </tr>
    <tr> 
      <td align="right" nowrap>MySQL database:</td>
      <td><input type="text" size="50" name="config[mysql_database]" value="<?php echo $wakkaConfig["mysql_database"] ?>" /></td>
    </tr>
    <tr> 
      <td></td>
      <td>Name and password of the MySQL user used to connect to your database.</td>
    </tr>
    <tr> 
      <td align="right" nowrap>MySQL user name:</td>
      <td><input type="text" size="50" name="config[mysql_user]" value="<?php echo $wakkaConfig["mysql_user"] ?>" /></td>
    </tr>
    <tr> 
      <td align="right" nowrap>MySQL password:</td>
      <td><input type="password" size="50" name="config[mysql_password]" value="<?php echo $wakkaConfig["mysql_password"] ?>" /></td>
    </tr>
    <tr> 
      <td></td>
      <td>Prefix of all tables used by CooCooWakka. This allows you to run multiple 
        CooCooWakka installations using the same MySQL database by configuring them 
        to use different table prefixes.</td>
    </tr>
    <tr> 
      <td align="right" nowrap>Table prefix:</td>
      <td><input type="text" size="50" name="config[table_prefix]" value="<?php echo $wakkaConfig["table_prefix"] ?>" /></td>
    </tr>
    <tr> 
      <td></td>
      <td><br /> <strong>Wakka Site Configuration</strong></td>
    </tr>
    <tr> 
      <td></td>
      <td>The name of your CooCooWakka site. It usually is a WikiName and looks SomethingLikeThis.</td>
    </tr>
    <tr> 
      <td align="right" nowrap>Your CooCooWakka's name:</td>
      <td><input type="text" size="50" name="config[wakka_name]" value="<?php echo $wakkaConfig["wakka_name"] ?>" /></td>
    </tr>
    <tr> 
      <td></td>
      <td>Your CooCooWakka site's home page. Should be a WikiName.</td>
    </tr>
    <tr> 
      <td align="right" nowrap>Home page:</td>
      <td><input type="text" size="50" name="config[root_page]" value="<?php echo $wakkaConfig["root_page"] ?>" /></td>
    </tr>
    <tr> 
      <td></td>
      <td>META Keywords/Description that get inserted into the HTML headers.</td>
    </tr>
    <tr> 
      <td align="right" nowrap>Meta Keywords:</td>
      <td><input type="text" size="50" name="config[meta_keywords]" value="<?php echo $wakkaConfig["meta_keywords"] ?>" /></td>
    </tr>
    <tr> 
      <td align="right" nowrap>Meta Description:</td>
      <td><input type="text" size="50" name="config[meta_description]" value="<?php echo $wakkaConfig["meta_description"] ?>" /></td>
    </tr>
    <tr> 
      <td></td>
      <td><br /> <strong>Wakka URL Configuration</strong><?php echo $wakkaConfig["wakka_version"] ? "" : "<br />Since this is a new installation, the installer tried to guess the proper values. Change them only if you know what you're doing!" ?></td>
    </tr>
    <tr> 
      <td></td>
      <td>Your CooCooWakka site's base URL. Page names get appended to it, so it should 
        include the "?wakka=" parameter stuff if the funky URL rewriting stuff 
        doesn't work on your server.</td>
    </tr>
    <tr> 
      <td align="right" nowrap>Base URL:</td>
      <td><input type="text" size="50" name="config[base_url]" value="<?php echo $wakkaConfig["base_url"] ?>" /></td>
    </tr>
    <tr>
    <td></td>
    <td>Your CooCooWakka site's base url path.(For example: http://yourhost/wakka/)</td>
    </tr>
    <tr>
      <td align="right" nowrap>Base Path:</td>
            <td><input type="text" size="50" name="config[base_path]" value="<?php echo $wakkaConfig["base_path"] ?>" /></td>
	   <br /><br /> 
    </tr>
    <tr> 
      <td></td>
      <td>Rewrite mode should be enabled if you are using CooCooWakka with URL rewriting. Do not enable it unless you know what is <a href="http://coo.hsfz.net/wiki/UrlRewriting">URL Rewritng</a>.
      <?if(!$rewritable){?><br /><b>It is detected that your server is NOT supporting URL Rewriting.</b><?}?>
      </td>
    </tr>
    <tr> 
     <td align="right" nowrap>Rewrite Mode:</td> 
      <td><input type="hidden" name="config[rewrite_mode]" value="0" /><input type="checkbox" name="config[rewrite_mode]" value="1" <?php echo $wakkaConfig["rewrite_mode"] ? "checked" : "" ?> />
        Enabled</td>
    </tr>
    <tr> 
      <td><div align="right">Language:</div></td>
      <td><select name="config[language]" id="config[language]">
          <option value="english" selected>English</option>
          <option value="schinese">Chinese (Simplified,GB) ºÚÃÂ÷–Œƒ</option>
          <option value="tchinese">Chinese (Traditional,BIG5) ¡c≈È§§§Â</option>
	  <option value="schinese-utf">Chinese (Simplified,unicode-UTF8)</option>
	  <option value="tchinese-utf">Chinese (Traditional,unicode-UTF8)</option>
        </select></td>
    </tr>
    <tr>
      <td><div align="right">Charset:</div></td>
      <td><select name="config[charset]" id="config[charset]">
<!--          <option value="gb18030">gb18030</option> -->
          <option value="big5">big5</option>
          <option value="utf-8">utf-8</option>
          <option value="gb2312" selected>gb2312</option>
	  <option value="gbk">gbk</option>
        </select></td>
    </tr>
    <tr> 
      <td></td>
      <td><input type="submit" value="Continue" />
      </td>
    </tr>
  </table>
</form>
