<?php
/* mime stuff take from Paul Southworth */
$mt_f = $this->config['mime_types'];
if ($mt_f == '')
  $mt_f='mime.types';

/* build an array keyed on the file ext */
if (is_readable($mt_f))
  {
    $mime_types=array();
    /* open our mime.types file for reading */
	    
$mt_fd=fopen($mt_f,"r");
    while (!feof($mt_fd))
      {
        /* pull a line off the file */
        $mt_buf=trim(fgets($mt_fd,1024));
        /* discard if the line was blank or started with a comment */
        if (strlen($mt_buf) > 0)
          if (substr($mt_buf,0,1) != "#")
            {
              /* make temp array of the mime.types line we just read */
              $mt_tmp=preg_split("/[\s]+/", $mt_buf, -1, PREG_SPLIT_NO_EMPTY);
              $mt_num=count($mt_tmp);
              /* if $mt_num = 1 then we got no file extensions for the type */
              if ($mt_num > 1)
                {
                  for ($i=1;$i<$mt_num;$i++)
                    {
                      /* if we find a comment mid-line, stop processing */
                      if (strstr($mt_tmp[$i],"#"))
                        {
                          break;
                          /* otherwise stick the type in an array keyed by extension */
                        }
                      else
                        {
                          $mime_types[$mt_tmp[$i]]=$mt_tmp[0];
                        }
                    }
                  /* zero the temporary array */
                  unset($mt_tmp);
                }
            }
      }
    /* close the mime.types file we were reading */
    fclose($mt_fd);
  }
else
  {
    echo "ERROR: unreadable file " . $mt_f . "\n";
  }

// upload path
if ($this->config['upload_path'] == '')
  $this->config['upload_path'] = 'files';
$upload_path = $this->config['upload_path'].'/'.$this->GetPageTag();
if (! is_dir(iconv($this->config["charset"], "GBK",$upload_path)))
  mkdir_r(iconv($this->config["charset"], "GBK",$upload_path));

// do the action
switch ($_REQUEST['action'])
  {
  case 'download':
      $_REQUEST['file'] = urldecode($_REQUEST['file']);
    if ($this->HasAccess('read'))
      {
        $path = "{$upload_path}/{$_REQUEST['file']}";
        $filename = basename($path);
        $filesize_lc = filesize(iconv($this->config["charset"], "GBK",$path));
       	if($filesize_lc <= $this->config["AllowUploadMaxFileSize"]){
            header('MIME-Version: 1.0');
    	    $afn = preg_split("/\./",$filename);
            $ext =  strtolower($afn[count($afn)-1]);
            $mime_type = $mime_types[$ext];
            if ($mime_type == '')
              $mime_type = 'application/octet-stream';
            //$filename_utf8 = iconv("GBK", $this->config["charset"], $filename);
            $filename_utf8 = rawurlencode($filename);
            header("Content-Type: {$mime_type}; name=\"{$filename_utf8}\"");
            header('Content-Length: '. $filesize_lc);
/* http://blog.robotshell.org/2012/deal-with-http-header-encoding-for-file-download/
正确处理下载文件时HTTP头的编码问题（Content-Disposition）

Content-Disposition: attachment;
                     filename="encoded_text";
                     filename*=utf-8''encoded_text

若有attachment则肯定是给出下载提示框。若没有的，对于图片等文件浏览器直接打开
*/
            header("Content-Disposition: filename=\"{$filename_utf8}\"; filename*=utf-8''{$filename_utf8}");
            $fp=fopen(iconv($this->config["charset"], "GBK",$path),'r');
            print fread($fp,filesize(iconv($this->config["charset"], "GBK",$path)));
            fclose($fp);
            exit();
    	}
    	else
    	{
        	header("location:".$this->tinyHref($this->config['upload_path']."/".$this->GetPageTag()."/".$filename));
        	exit();
    	}
      }
  case 'delete':
    if ($this->HasAccess('write'))
      {
        @unlink(iconv($this->config["charset"], "GBK","{$upload_path}/{$_REQUEST['file']}"));
	$this->LogOperation("_OP_FILE_REMOVE",$this->GetPageTag(),$_REQUEST['file']);
        print $this->redirect($this->href());
      }
  }
?>
