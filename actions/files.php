<?php
    if (! function_exists('mkdir_r')) {
        function mkdir_r ($dir) {
            if (strlen($dir) == 0)
                return 0;
            if (is_dir($dir))
                return 1;
            elseif (dirname($dir) == $dir)
            return 1;
            return (mkdir_r(dirname($dir)) and mkdir($dir, 0755));
        }
    }
    if (!function_exists('check_ext')) {
        function check_ext($file_name) {
            global $wakka;
            $exts = $wakka->GetConfigValue("AllowUploadExts");
            $pos = strrpos($file_name, '.');
            $ext = substr($file_name, $pos+1, (strlen($file_name)-$pos-1));
            $rs = preg_match("/^".$exts."$/i", $ext);
            return $rs;
        }
    }
    // upload path
    if ($this->config['upload_path'] == '')
        $this->config['upload_path'] = 'files';
    $upload_path = $this->config['upload_path'].'/'.$this->GetPageTag();
     
     
    if ($tokens['download'] ) {
         
        // link to download a file
        $text = $this->stripquotes($action_params['download']);
        if ($tokens['text']) {
            $text = $this->stripquotes($action_params['text']);
        }
        echo "<a href=\"".$this->href('files.xml', $this->GetPageTag(), 'action=download&file='.urlencode($this->stripquotes($action_params['download'])))."\">".$text."</a>";
    } elseif ($this->page AND $this->page['latest']=="Y" && $this->HasAccess('write') AND ($this->method <> 'print.xml') AND ($this->method <> 'edit')) {
         
        if (! is_dir(iconv($this->config["charset"], "GBK",$upload_path)))
            mkdir_r(iconv($this->config["charset"], "GBK",$upload_path));
         
        // upload action
        $uploaded = $_FILES['file'];
        if ($_REQUEST['action'] == 'upload' AND $uploaded['size'] > 0) {
            if($uploaded['size'] > $this->config["AllowUploadMaxFileSize"])
            {
                $this->SetMessage("Sorry, filesize not allowed.");
		        $this->Redirect($this->Href());
            }
	        if (file_exists($upload_path.'/'.$uploaded['name'])){
	    	    $this->SetMessage(_MI_FILE_EXIST);
		        $this->Redirect($this->Href());
		    }
            if (check_ext($uploaded['name'])){
                $dest = $upload_path.'/'.$uploaded['name'];
                move_uploaded_file ($uploaded['tmp_name'],iconv($this->config["charset"], "GBK", $dest ));
                //copy ($uploaded['tmp_name'], $upload_path.'/'.$uploaded['name']);
	            $this->LogOperation("_OP_FILE_UPLOAD",$this->GetPageTag(),$uploaded['name']);
	        }
            else
                //echo "Sorry, the site doesn't allow you to upload this type of files!(".$uploaded['name'].")";
            echo _MI_ERROR_FILETYPE."(".$uploaded['name'].")";
        }
         
        // form
        $result = "<form action=\"".$this->href()."\" method=\"post\" enctype=\"multipart/form-data\">\n";
        if (!$this->config["rewrite_mode"])
            $result .= "<input type=\"hidden\" name=\"wakka\" value=\"".$this->MiniHref_orig()."\">\n";
        echo $result;
    ?>
	<input type="hidden" name="action" value="upload">
    <input type="file" name="file">
    <input type="submit" value="+">
    <?php
        echo $this->FormClose();
    }
    if ($this->HasAccess('read')) {
        // uploaded files
        //print($this->Format("----"));
        $dir = opendir(iconv($this->config["charset"], "GBK",$upload_path));
        while ($file = readdir($dir)) {
            $file_utf8 = iconv("GBK",$this->config["charset"], $file);
            if ($file != '.' && $file != '..') {
                $delete_link = "<a href=\"".$this->href('files.xml', $this->GetPageTag(), 'action=delete&file='.urlencode($file_utf8))."\">x</a>";
                $download_link = "<a href=\"".$this->href('files.xml', $this->GetPageTag(), 'action=download&file='.urlencode($file_utf8))."\">".$file_utf8."</a>";
                if ($this->IsManager())print "[ {$delete_link} ] ";
                if ($file_utf8 == $uploaded['name'])
                    print "<em>{$download_link}</em>\n";
                else
                    print $download_link;
                print '<br>';
            }
        }
        closedir($dir);
        //print($this->Format("----"));
    }
?>
