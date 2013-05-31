<?php 
//set temp files
$file_input=tempnam("","ccwakka.in"); //set the input file for vim
$file_output=tempnam("","ccwakka.out"); //set the output file from vim
$file_script=tempnam("","ccwakka.vim"); //set the vim script file

//set vim env
$vim_cmd="vim";
$vim_args="-s $file_script ".$file_input;

//set filetype
$type=$args;

//generate vim script
$vim_script=":set filetype=$type
:source \$VIMRUNTIME/syntax/2html.vim
:w! $file_output
:q!
:q!
";
$fp=fopen($file_script,"w");
fputs($fp,$vim_script);
fclose($fp);

//generate input file
$fp=fopen($file_input,"w");
fputs($fp,$text);
fclose($fp);

//execute vim
exec($vim_cmd." ".$vim_args);

//get output
$fp=fopen($file_output,"r");
$result=fread($fp,filesize($file_output));
fclose($fp);

//clear all temp files
unlink($file_output);
unlink($file_input);
unlink($file_script);

//modify result
//$result=preg_replace("/^.*?\n/","",$result,2);
$result=preg_replace("/<title>.*?<\/title>/","",$result);
$result=trim(strip_tags($result,"<font>"));
print($result);
?>
