<?php
header("Content-type: application/x-javascript");
if ($this->HasAccess("read")) {
if ($this->page) {
$body=$this->Format($this->page['body']);
$lines=split("\n",$body);
foreach($lines as $line){
if($line)
print("document.writeln(\"".addslashes($line)."\");\n");
}
}
}
?>
