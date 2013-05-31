<?php echo $this->FormOpen("", "", "GET") ?>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
<td><?php  /*Search for*/echo _MI_SEARCHFOR ?>:&nbsp;
</td>
<td><input name="phrase" size="40" value="<?php echo htmlspecialchars($_REQUEST["phrase"]) ?>" /> <input type="submit" value="<?php  echo _MI_SEARCH ?>" /></td>
</tr>
</table>
<?php echo $this->FormClose();
?>

<?php
if ($phrase = $_REQUEST["phrase"])
  {
    print("<br />");
    if ($results = $this->FullTextSearch2($phrase))
      {
        printf("<strong>"._MI_SEARCHRESULT/*Search results for */."</strong><br /><br />\n",$phrase);
        foreach ($results as $i => $page)
        {
          print(($i+1).". ".$this->Link($page["tag"])."<br />\n");
        }
      }
    else
      {
        printf(/*No results for */_MI_NORESULT,$phrase);
      }
  }

?>
