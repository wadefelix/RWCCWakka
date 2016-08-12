<?php
     
?>
<?php
    // Load the main class
    require_once 'HTML/QuickForm.php';
    include_once 'ccwakka/files.php';
    if (!$this->page)$this->Redirect($this->Href("show"));
?>
<div class="page">
<?php
    //Preparing the data
     
    //Values list
    $pageVal = array(
    "aliasname",
        "category",
        "owner" );
    $aclVal = array(
    "read",
        "write",
        "comment" );
     
    //Load Categories
    $list_category[""] = _MI_NONE;
    if ($categories = $this->LoadCategories())
        foreach($categories as $category) {
        $category = $category['tag'];
        if ($category)$list_category[$category] = $category."(".$this->GetDisplayName($category).")";
    }
     
    //Load Alias
    //$alias=$this->page['aliasname'];
     
    //Load ACLs
    foreach($aclVal as $aclkey) {
        $ACLs[$aclkey] = $this->LoadAcl($this->GetPageTag(), $aclkey);
        $default[$aclkey] = $ACLs[$aclkey]['list'];
    }
     
    //LoadUsers
    $list_owner[""] = _MI_NONE;
    if ($users = $this->LoadUsers())
        foreach($users as $user) {
        $user = $user['name'];
        if ($user)$list_owner[$user] = $user/*."(".$this->GetDisplayName($user).")"*/; //improved performance
         
    }
     
    //Load PageVal
    foreach($pageVal as $pagekey) {
        $default[$pagekey] = $this->page[$pagekey];
    }
     
    // TextArea Attr
    $taAttrs = array(
    'rows' => 3);
     
    // Instantiate the HTML_QuickForm object
    $form = new HTML_QuickForm('manageForm', 'post', $this->Href('manage'), '', array('width' => '100%'), true);
    $formRenderer = &$form->defaultRenderer();
    $formRenderer->setFormTemplate("\n<form{attributes}>\n<table border=\"0\" width=\"100%\">\n{content}\n</table>\n</form>");
    if ($form->validate()) {
        //    $form->freeze();
    }
     
    // Add some elements to the form
    $form->addElement('header', null, _MI_BASIC_PORPERTY);
    $form->addElement('text', 'aliasname', _MI_DISPLAYALIASNAME, array('size' => 50, 'maxlength' => 100));
    //$form->addElement('header',null,'Set Page\'s Category');
    $form->addElement('select', 'category', _MI_SET_CATEGORY.":", $list_category);
    $form->addElement('header', null, _MI_ACLSLIST);
    $form->addElement('textarea', 'read', _MI_READACL.':', $taAttrs);
    $form->addElement('textarea', 'write', _MI_WRITEACL.':', $taAttrs);
    $form->addElement('textarea', 'comment', _MI_COMMENTSACL.':', $taAttrs);
    $form->addElement('select', 'owner', _MI_SETOWNER.":", $list_owner);
    $buttons[] = &HTML_QuickForm::createElement('reset', 'btnClear', _MI_RESET);
    $buttons[] = &HTML_QuickForm::createElement('submit', 'btnSubmit', _MI_SUBMIT);
     
    if (!$form->IsFrozen()) {
        $form->addGroup($buttons, null, null, '&nbsp;');
    }
     
    $form->setDefaults($default);
     
    if ($form->validate()) {
         
        //update page's value
        $values = $form->exportValues($pageVal);
        $newpage = $this->page;
        foreach($values as $key => $val) {
            if ($val != $this->page[$key]) {
                $newpage[$key] = $val;
            }
        }
        if ($this->HasAccess('write')) {
            if ($newpage['aliasname'] != $this->page['aliasname'] || $newpage['category'] != $this->page['category'])
            /*$changed=false;
            foreach($pageVal as $item)if($newpage[$item]!=$this->page[$item])$changed=true;
            if ($changed)*/
            $this->SavePage($this->GetPageTag(), $newpage['body'], "", 'Y', $note, $newpage['aliasname']);
            //    $this->SavePage();
            $this->SetPageCategory($this->GetPageTag(), $newpage['category']);
        }
         
        //update page's acls
        if ($this->IsManager()) {
            $values = $form->exportValues($aclVal);
            foreach($values as $key => $val) {
                if ($val != $ACLs[$key]['list']) {
                     
                    $this->SaveAcl($this->GetPageTag(), $key, $val);
                     
                }
            }
            if ($newpage['owner'] != $this->page['owner'])
            $this->SetPageOwner($this->GetPageTag(), $newpage['owner']);
        }
        $this->Redirect($this->Href('manage'));
         
    }
     
    //////////////////////////////////////////////////
    // Move Page
    //////////////////////////////////////////////////
     
    $mform = new HTML_QuickForm('moveForm', '', $this->Href('manage'), null, null, true);
    $mform->addElement('header', null, _MI_MOVETHIS);
    $mform->addElement('static', null, '', $this->Format(_MI_MOVEPAGE_INTRO));
    $mform->addElement('text', 'newtag', _MI_MOVEPAGE_TO, array('size' => 50, 'maxlength' => 100));
    $mform->addGroup($buttons, null, null, '&nbsp;');
    $mform->addRule('newtag', '', 'required');
    $mform->applyFilter('newtag', 'trim');
     
     
    if ($mform->validate()) {
        $new_tag = $mform->exportValue("newtag");
        if ($this->IsManager()) {
            if ($new_tag && $new_tag != $this->GetPageTag()) {
                //does page already exist
                $redirect_body = "{{redirect page=".$new_tag."}}";
                $redirect_body_back = "{{redirect page=".$this->GetPageTag()."}}";
                $revisions = $this->LoadRevisions($new_tag);
                if (count($revisions) == 0)$error = 0;
                elseif(count($revisions) > 1)$error = 1;
                elseif($revisions[0]['body'] == $redirect_body_back) {
                    $error = 0;//allow to move back.
                    $this->Query("update ".$this->GetConfigValue("table_prefix")."pages set latest='N' where ta
                        g='".mysql_escape_string($new_tag)."';");
                }
                else $error = 1;
                if ($error == 1) {
                    $message = _MI_ERROR_PAGEXIST;
                    $this->SetMessage($message);
                    $this->Redirect($this->Href("move", $this->GetPageTag()));
                } else {
                    //Move the latest edition first.
                    $this->Query("update ".$this->GetConfigValue("table_prefix")."pages set tag='".mysql_escape_string($new_tag)."' where latest='Y' and tag='".mysql_escape_string($this->GetPageTag())."';");
                    //Write redirection to old page.
                    $this->SavePage($this->GetPageTag(), $redirect_body, "", "Y", _MI_MOVE_TO.$new_tag);
                    //Move the old versions.
                    $this->Query("update ".$this->GetConfigValue("table_prefix")."pages set tag='".mysql_escape_string($new_tag)."' where latest!='Y'and tag='".mysql_escape_string($this->GetPageTag())."';");
                    //Move the comments
                    $this->Query("update ".$this->GetConfigValue("table_prefix")."pages set comment_on='".mysql_escape_string($new_tag)."' where comment_on='".mysql_escape_string($this->GetPageTag())."';");
                     
                    //Modify the link tracking table
                    $this->Query("update ".$this->GetConfigValue("table_prefix")."links set from_tag='".mysql_escape_string($new_tag)."' where from_tag='".mysql_escape_string($this->GetPageTag())."';");
                    $this->Query("insert into ".$this->GetConfigValue("table_prefix")."links set from_tag='".mysql_escape_string($this->GetPageTag())."', to_tag='".mysql_escape_string($new_tag)."';");
                     
                    if ($this->GetPageUploadDir($this->GetPageTag())) {
                        rename($this->GetPageUploadDir($this->GetPageTag()), $this->GetPageUploadDir($new_tag, false));
                    }
                     
                     
                     
                    $this->LogOperation("_OP_PAGE_MOVE", $this->GetPageTag(), $new_tag);
                    //Goto the new page
                    $this->Redirect($this->Href("", $new_tag));
                     
                }
            }
        }
    }
     
    /////////////////////
    // Remove Page
    /////////////////////
    $delright = $this->IsAdmin();
    $rmform = new HTML_QuickForm('removeForm', '', $this->Href('manage'), null, null, true);
    $rmform->addElement('header', null, _MI_REMOVE_THIS);
    $rmform->addElement('static', null, '', $this->Format(_MI_REMOVE_PAGE_INTRO));
    $rmform->addElement('text', 'confirm_name', _MI_TYPE_THIS_PAGE_TAG, array('size' => 18));
    $rmform->addElement('submit', 'btnSubmit', _MI_REMOVE_THIS);
    $rmform->addRule('confirm_name', _MI_TYPE_RIGHT_NAME, 'required');
    $rmform->addRule('confirm_name', _MI_TYPE_RIGHT_NAME, 'regex', "/^".preg_quote($this->GetPageTag(), "$/")."/");
    if ($this->page['comment_on']) {
        $rmform->setDefaults(array("confirm_name" => $this->page['tag']));
    }
    if ($rmform->validate() && $delright) {
        $rmtag = $this->GetPageTag();
        $this->Query("delete from ".$this->GetConfigValue("table_prefix")."pages where tag='".mysql_escape_string($rmtag)."' or comment_on='".mysql_escape_string($rmtag)."';");
        $this->Query("delete from ".$this->GetConfigValue("table_prefix")."links where from_tag='".mysql_escape_string($rmtag)."';");
        if ($this->GetPageUploadDir($rmtag))
        deldir($this->GetPageUploadDir($rmtag));
        $this->LogOperation("_OP_PAGE_REMOVE", $rmtag);
        if ($this->page['comment_on'])$this->Redirect($this->Href("", $this->page['comment_on'], "#comments"));
        $this->Redirect($this->Href());
    }
    /////////////////
    // roll back
    /////////////////
    $rollbackright = $this->Isadmin();
    $delright = $this->IsAdmin();
    $rbform = new HTML_QuickForm('rollbackForm', '', $this->Href('manage'), null, null, true);
    $rbform->addElement('header', null, _MI_ROLL_BACK);
    $rbform->addElement('static', null, '', $this->Format(_MI_ROLL_BACK_INTRO));
    //$rmform->addElement('text', 'confirm_name', _MI_TYPE_THIS_PAGE_TAG, array('size' => 10));
    $rbform->addElement('submit', 'rbbtnSubmit', _MI_ROLL_BACK);
    if ($rbform->validate() && $rollbackright) {
        $rbtag = $this->GetPageTag();
        $rbid = $this->GetLastDiffId($rbtag);
        $this->Query("delete from ".$this->GetConfigValue("table_prefix")."pages where tag='".mysql_escape_string($rbtag)."' and latest='Y';");
        $this->Query("update ".$this->GetConfigValue("table_prefix")."pages set latest='Y' where id='$rbid';");
        $newpage = $this->LoadSingle("select * from ".$this->GetConfigValue("table_prefix")."pages where tag='".mysql_escape_string($rbtag)."' and latest='Y';");
        if ($newpage) {
            $this->ClearLinkTable();
            $this->StartLinkTracking();
            //$dummy = $this->Header();
            $dummy .= $this->Format($newpage['body']);
            //$dummy .= $this->Footer();
            $this->StopLinkTracking();
            $this->WriteLinkTable($rbtag);
            $this->ClearLinkTable();
        }
        $this->LogOperation("_OP_PAGE_ROLLBACK", $rbtag);
        //$this->Redirect($this->Href());
    }
     
     
    $form->setRequiredNote(_MI_REQUIRED_NOTE);
    $mform->setRequiredNote(_MI_REQUIRED_NOTE);
    if ($delright)$rmform->setRequiredNote(_MI_REQUIRED_NOTE);
    if ($rollbackright)$rbform->setRequiredNote(_MI_REQUIRED_NOTE);
    $form->display();
    $mform->display();
    if ($delright)$rmform->display();
    if ($rollbackright)$rbform->display();
?>
</div>
