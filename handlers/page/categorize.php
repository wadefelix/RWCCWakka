<div class="page">
<?php

if ($this->HasAccess("write"))
{
    if ($_POST)
    {
        // change category?
        if ($newcategory = $_POST["newcategory"])
        {
            $this->SetPageCategory($this->GetPageTag(), $newcategory);
            $message = _MI_UPDATE_CAT;//"Page category updated";
        }

        // redirect back to page
        $this->SetMessage($message."!");
        $this->Redirect($this->Href());
    }
    else
    {
        // show form
        ?>
        <?php  
		printf("<h3>"._MI_SET_CATEGORY_FOR./*Select page category for <?php echo  $this->Link($this->GetPageTag()) ?>*/"</h3>",$this->Link($this->GetPageTag()));?>
        <br>

        <?php echo  $this->FormOpen("categorize") ?>
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <b><?php  echo _MI_SET_CATEGORY /*Set Category:*/?></b><br>
                    <select name="newcategory">
                        <option value=""><?php  /*Don't change*/ echo _MI_NOCHANGE?></option>
                        <!--<option value=""></option>-->
                        <?php
                        if ($categories = $this->LoadCategories())
                        {
                            foreach($categories as $category)
                            {
                                print("<option value=\"".htmlentities($category["tag"])."\">".$category["tag"]."</option>\n");
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <br>
                    <input type="submit" value="Store" accesskey="s">
                    <input type="button" value="Cancel" onClick="history.back();">
                </td>
            </tr>
        </table>
        <?php
        print($this->FormClose());
    }
}
else
{
    print("<i>"._MI_NO_WRITE_ACCESS."</i>");
}

?>
</div> 
