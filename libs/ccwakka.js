/**
 * Some of these scripts were taken from wikipedia.org and DokuWiki, and were edited
 * for CooCooWakka
 */
/**
 * Some browser detection
 */
var clientPC  = navigator.userAgent.toLowerCase(); // Get client info
var is_gecko  = ((clientPC.indexOf('gecko')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('khtml') == -1) && (clientPC.indexOf('netscape/7.0')==-1));var is_safari = ((clientPC.indexOf('AppleWebKit')!=-1) && (clientPC.indexOf('spoofer')==-1));
var is_khtml  = (navigator.vendor == 'KDE' || ( document.childNodes && !document.all && !navigator.taintEnabled ));
if (clientPC.indexOf('opera')!=-1) {
    var is_opera = true;
    var is_opera_preseven = (window.opera && !document.childNodes);
    var is_opera_seven = (window.opera && document.childNodes);
}
var is_ie = document.selection  && !is_gecko;

/*
 * This sets a cookie by JavaScript
 *
 * @see http://www.webreference.com/js/column8/functions.html
 */
function setCookie(name, value, expires, path, domain, secure) {
  var curCookie = name + "=" + escape(value) +
      ((expires) ? "; expires=" + expires.toGMTString() : "") +
      ((path) ? "; path=" + path : "") +
      ((domain) ? "; domain=" + domain : "") +       ((secure) ? "; secure" : "");
  document.cookie = curCookie;
}

/*
 * This reads a cookie by JavaScript
 *
 * @see http://www.webreference.com/js/column8/functions.html
 */
function getCookie(name) {
  var dc = document.cookie;
  var prefix = name + "=";
  var begin = dc.indexOf("; " + prefix);
  if (begin == -1) {
    begin = dc.indexOf(prefix);
    if (begin != 0) return null;
  } else
    begin += 2;
  var end = document.cookie.indexOf(";", begin);
  if (end == -1)
    end = dc.length;
  return unescape(dc.substring(begin + prefix.length, end));
}

/*
 * This is needed for the cookie functions
 *
 * @see http://www.webreference.com/js/column8/functions.html
 */
function fixDate(date) {
  var base = new Date(0);
  var skew = base.getTime();
  if (skew > 0)
    date.setTime(date.getTime() - skew);
}

/**
 * Sizecontrol inspired by TikiWiki. This displays the buttons.
 */
function showSizeCtl(){
  if(document.getElementById) {
    var textarea = document.getElementById('body');
    var hgt = getCookie('CCWakkaSizeCtl');
    if(hgt == null){
      textarea.style.height = '300px';
    }else{
      textarea.style.height = hgt;
    }
    document.writeln('<span class="trans"><a onkeypress="javascript:sizeCtl(100)" onclick="javascript:sizeCtl(100)"><img src="'+baseURL+'images/larger.png" width="20" height="20" border="0"></a>');

    document.writeln('<a onkeypress="javascript:sizeCtl(-100)" onclick="javascript:sizeCtl(-100)"><img src="'+baseURL+'images/smaller.png" width="20" height="20" border="0"></a></span>');
  }
}

/**
 * This sets the vertical size of the editbox
 */
function sizeCtl(val){
  var textarea = document.getElementById('body');
  var height = parseInt(textarea.style.height.substr(0,textarea.style.height.length-2));
  height += val;
  textarea.style.height = height+'px';
                                                                                       
  var now = new Date();
  fixDate(now);
  now.setTime(now.getTime() + 365 * 24 * 60 * 60 * 1000); //expire in a year
  setCookie('CCWakkaSizeCtl',textarea.style.height,now);
}

/**
 * global var used for not saved yet warning
 */
var textChanged = false;
                                                                                       
function ssvchk(event_){
  if (!event_ && window.event) {
          event_ = window.event;
  }
  if(textChanged){
     event_.returnValue = notSavedYet;
    return notSavedYet;
  }
}

/**
 * This function escapes some special chars
 */
function escapeQuotes(text) {
  var re=new RegExp("'","g");
  text=text.replace(re,"\\'");
  re=new RegExp('"',"g");
  text=text.replace(re,'&quot;');
  re=new RegExp("\\n","g");
  text=text.replace(re,"\\n");
  return text;
}

/**
 * This function generates the actual toolbar buttons with localized text
 * we use it to avoid creating the toolbar where javascript is not enabled
 */
function formatButton(imageFile, speedTip, tagOpen, tagClose, sampleText, accessKey) {
  speedTip=escapeQuotes(speedTip);
  tagOpen=escapeQuotes(tagOpen);
  tagClose=escapeQuotes(tagClose);
  sampleText=escapeQuotes(sampleText);

  document.write("<a ");
  if(accessKey){
    document.write("accesskey=\""+accessKey+"\" ");
    speedTip = speedTip+' [ALT+'+accessKey.toUpperCase()+']';
  }
  document.write("href=\"###\" ");
  document.write("onclick=\"javascript:insertTags");
  document.write("('"+tagOpen+"','"+tagClose+"','"+sampleText+"');\" ");
  document.write("onkeypress=\"javascript:insertTags");
  document.write("('"+tagOpen+"','"+tagClose+"','"+sampleText+"');\">");
  document.write("<img width=\"24\" height=\"24\" src=\""+
                baseURL+imageFile+"\" border=\"0\" alt=\""+
                speedTip+"\" title=\""+speedTip+"\">");
  document.write("</a>");
  return;
}

/**
 * This function generates the actual toolbar buttons with localized text
 * we use it to avoid creating the toolbar where javascript is not enabled
 */
function insertButton(imageFile, speedTip, value, accessKey) {
  speedTip=escapeQuotes(speedTip);
  value=escapeQuotes(value);

  document.write("<a ");
  if(accessKey){
    document.write("accesskey=\""+accessKey+"\" ");
    speedTip = speedTip+' [ALT+'+accessKey.toUpperCase()+']';
  }
  document.write("href=\"###\" ");
  document.write("onclick=\"javascript:insertAtCarret");
  document.write("(document.getElementById(\'body\'),'"+value+"');\" ");
  document.write("onkeypress=\"javascript:insertAtCarret");
  document.write("(document.getElementById(\'body\'),'"+value+"');\">");

  document.write("<img width=\"24\" height=\"24\" src=\""+
                baseURL+imageFile+"\" border=\"0\" alt=\""+
                speedTip+"\" title=\""+speedTip+"\">");
  document.write("</a>");
  return;
}

/**
 * apply tagOpen/tagClose to selection in textarea, use sampleText instead
 * of selection if there is none copied and adapted from phpBB
 */
function insertTags(tagOpen, tagClose, sampleText) {
  var txtarea = document.getElementById('body');
  // IE
  if(document.selection  && !is_gecko) {
    var theSelection = document.selection.createRange().text;
    if(!theSelection) { theSelection=sampleText;}
    txtarea.focus();
    if(theSelection.charAt(theSelection.length - 1) == " "){// exclude ending space char, if any
      theSelection = theSelection.substring(0, theSelection.length - 1);
      document.selection.createRange().text = tagOpen + theSelection + tagClose + " ";
    } else {
      document.selection.createRange().text = tagOpen + theSelection + tagClose;
    }
  // Mozilla
  } else if(txtarea.selectionStart || txtarea.selectionStart == '0') {
     var startPos = txtarea.selectionStart;
    var endPos = txtarea.selectionEnd;
    var scrollTop=txtarea.scrollTop;
    var myText = (txtarea.value).substring(startPos, endPos);
    if(!myText) { myText=sampleText;}
    if(myText.charAt(myText.length - 1) == " "){ // exclude ending space char, if any
      subst = tagOpen + myText.substring(0, (myText.length - 1)) + tagClose + " ";
    } else {
      subst = tagOpen + myText + tagClose;
    }
    txtarea.value = txtarea.value.substring(0, startPos) + subst +
    txtarea.value.substring(endPos, txtarea.value.length);
    txtarea.focus();

    var cPos=startPos+(tagOpen.length+myText.length+tagClose.length);
    txtarea.selectionStart=cPos;
    txtarea.selectionEnd=cPos;
    txtarea.scrollTop=scrollTop;

  // All others
  } else {
    var copy_alertText=alertText;
    var re1=new RegExp("\\$1","g");
    var re2=new RegExp("\\$2","g");
    copy_alertText=copy_alertText.replace(re1,sampleText);
    copy_alertText=copy_alertText.replace(re2,tagOpen+sampleText+tagClose);
    var text;
    if (sampleText) {
      text=prompt(copy_alertText);
    } else {
      text="";
    }
    if(!text) { text=sampleText;}
    text=tagOpen+text+tagClose;
    //append to the end
    txtarea.value += "\n"+text;

    // in Safari this causes scrolling
    if(!is_safari) {
      txtarea.focus();
    }

  }
  textChanged=true;
  // reposition cursor if possible
  if (txtarea.createTextRange) txtarea.caretPos = document.selection.createRange().duplicate();
}

/*
 * Insert the given value at the current cursor position
 *
 * @see http://www.alexking.org/index.php?content=software/javascript/content.php
 */
function insertAtCarret(field,value){
  //IE support
  if (document.selection) {
    field.focus();
    if(opener == null){
      sel = document.selection.createRange();
    }else{
      sel = opener.document.selection.createRange();
    }
    sel.text = value;
  //MOZILLA/NETSCAPE support
  }else if (field.selectionStart || field.selectionStart == '0') {
    var startPos  = field.selectionStart;
    var endPos    = field.selectionEnd;
    var scrollTop = field.scrollTop;
    field.value = field.value.substring(0, startPos)
                  + value
                  + field.value.substring(endPos, field.value.length);

    field.focus();
    var cPos=startPos+(value.length);
    field.selectionStart=cPos;
    field.selectionEnd=cPos;
    field.scrollTop=scrollTop;
  } else {
    field.value += "\n"+value;
  }
  textChanged=true;
  // reposition cursor if possible
  if (field.createTextRange) field.caretPos = document.selection.createRange().duplicate();
}

