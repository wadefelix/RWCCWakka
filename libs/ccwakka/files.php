<?php 
function deldir($dir){
  $current_dir = opendir($dir);
  while($entryname = readdir($current_dir)){
     if(is_dir("$dir/$entryname") and ($entryname != "." and $entryname!="..")){
       deldir("${dir}/${entryname}");
     }elseif($entryname != "." and $entryname!=".."){
       unlink("${dir}/${entryname}");
     }
  }
  closedir($current_dir);
  rmdir(${dir});
}
?>

