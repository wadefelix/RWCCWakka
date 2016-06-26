<form action="" >
<input id="wakkaname_tobecreated" type="text" name="pagetitle" />
<input onclick="var base_url=<?php echo "'".$this->GetConfigValue("base_url")."'";?>;var title=document.getElementById('wakkaname_tobecreated').value; self.location=base_url+title+'/edit';" type="button" value=<?php echo _MI_CREATE_PAGE; ?> />
</form>
