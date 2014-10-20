
/***********************************************
* Ajax functions
* Created by: Ye Henry Tian
* Date: March 6, 2010
***********************************************/

function GetXmlHttpObject()
   {
   if (window.XMLHttpRequest)
      {
      // code for IE7+, Firefox, Chrome, Opera, Safari
      return new XMLHttpRequest();
      }
   if (window.ActiveXObject)
      {
      // code for IE6, IE5
      return new ActiveXObject("Microsoft.XMLHTTP");
      }
   return null;
   }

function tweetBoxReset()
   {
   document.streamform.happening.value = "What is happening?";
   document.streamform.remain.value = 140;   
   }
function tweetPopUpBoxReset()
   {
   document.streamformpop.happeningpopup.value = "What is happening?";
   document.streamformpop.remain.value = 140;   
   }

function streamIt(status)
   {
    var msg = ""; 
	msg += "________________________________________________________\n\n"
    msg += "Do you want to stream the following happening?\n"
    msg += "________________________________________________________\n\n"
	msg += status;
	if (confirm(msg))
	   {
	   document.streamform.happening.value = status;
	   textcount(document.streamform.happening, 140, document.streamform.remain);
	   }
	else
	   {
	   document.streamform.happening.value = "What is happening?";
	   document.streamform.remain.value = 140;
	   }
   }

function stateChanged(xmlhttp, fieldId)
   {
   if (xmlhttp.readyState == 4 || xmlhttp.readyState == "complete")
      {
      hideimg(fieldId);	
	  document.getElementById(fieldId).innerHTML = xmlhttp.responseText;
      }
   else
      loadimg(fieldId);
   }
   
function stateChangedList(xmlhttp, fieldId)
   {
   if (xmlhttp.readyState == 4 || xmlhttp.readyState == "complete")
      {
	  document.getElementById(fieldId).innerHTML = xmlhttp.responseText;
      }
   }
   
function stateChangedPost(xmlhttp)
   {
   if (xmlhttp.readyState == 4 || xmlhttp.readyState == "complete")
      {
	  if (document.streamform.twitter.checked)
        getAjaxUpdates('get_tweets', '', 'twitterlist');
      if (document.streamform.weibo.checked)
         getAjaxUpdates('get_weibos', '', 'weibolist');
	  if (document.streamform.facebook.checked)
	     getAjaxUpdates('get_fbupdates', '', 'fblist');
	  if (document.streamform.buzz.checked)
         getAjaxUpdates('get_pluses', '', 'buzzlist');
	  //document.streamform.happening.value = "What is happening?";
	  document.getElementById('streambox').innerHTML = "<textarea name=\"happening\" oninput=\"textcount(this, 140, this.form.remain)\" onfocus=\"if (this.value == 'What is happening?') this.value='';textcount(this, 140, this.form.remain);\" onblur=\"if (this.value == '') this.value='What is happening?';textcount(this, 140, this.form.remain);\" onkeydown=\"textcount(this, 140, this.form.remain)\" onkeyup=\"textcount(this, 140, this.form.remain)\" class=\"tweetarea\";>What is happening?</textarea>";
	  document.streamform.remain.value = 140;
	  
	  //document.getElementById('streambox').innerHTML = xmlhttp.responseText;
      }
   else
      document.getElementById('streambox').innerHTML = "<span class='notice'>Posting...</span><img src='../images/loading.gif' style=\"width=:220px;height:19px\"/>";
   }
   
function stateChangedBuzzLogin(xmlhttp)
   {
   if (xmlhttp.readyState == 4 || xmlhttp.readyState == "complete")
      {
	  window.location = "../friendstream/";
      }
   }   
   
function buzzLogin()
   {
   var xmlhttp = GetXmlHttpObject();
   if (xmlhttp == null)
      {
      alert ("Browser does not support HTTP Request!");
      return;
      }
   
   var url = "../include/friendstream.php?func=buzzLogin";
   //alert ("I am here22! " + url);
   url = url + "&sid=" + Math.random();
   xmlhttp.onreadystatechange=function(){stateChangedBuzzLogin(xmlhttp)}
   xmlhttp.open("GET", url, true);
   xmlhttp.send(null);
   }
   
function stateChangedShortUrl(xmlhttp)
   {
   if (xmlhttp.readyState == 4 || xmlhttp.readyState == "complete")
      {
	  //alert(xmlhttp.responseText);	  
	  document.getElementById('streambox').innerHTML = "<textarea name=\"happening\" oninput=\"textcount(this, 140, this.form.remain)\" onfocus=\"if (this.value == 'What is happening?') this.value='';textcount(this, 140, this.form.remain);\" onblur=\"if (this.value == '') this.value='What is happening?';textcount(this, 140, this.form.remain);\" onkeydown=\"textcount(this, 140, this.form.remain)\" onkeyup=\"textcount(this, 140, this.form.remain)\" class=\"tweetarea\";>" + unescape(xmlhttp.responseText);
      textcount(document.streamform.happening, 140, document.streamform.remain);
	  }
   else
      document.getElementById('streambox').innerHTML = "<span class='notice'>Shorting URLs...</span><img src='../images/loading.gif' style=\"width=:220px;height:19px\"/>";
   }   
   
function getShortUrl(parms)
   {
   var msg = ""; 
	msg += "________________________________________________________\n\n"
    msg += "Do you want to stream the following happening?\n"
    msg += "________________________________________________________\n\n"
	msg += parms;
	//if (confirm(msg))
	   parms += " </textarea>";
	//else 
	   //return;
	  
	
   var xmlhttp = GetXmlHttpObject();
   if (xmlhttp == null)
      {
      alert ("Browser does not support HTTP Request!");
      return;
      }
   
   var url = "../include/friendstream.php?func=short_streampost";
 
   if (parms != '')
      url = url + "&parms=" + encodeURIComponent(parms);
   //alert ("I am here22! " + url);
   url = url + "&sid=" + Math.random();
   xmlhttp.onreadystatechange=function(){stateChangedShortUrl(xmlhttp)}
   xmlhttp.open("GET", url, true);
   xmlhttp.send(null);
   }
   
var twittertimerId = 0;
var fbtimerId = 0;
var buzztimerId = 0;
var weibotimerId = 0;
function autoRefresh(name, value)
   {
   var timeoutPeriod = value * 60000;
   var snaId = '';
   var timerId = 0;
   switch(name)
	   {
		case 'weiboautorefresh':
		   snaId = 'weiborefresh';
		   if (timeoutPeriod == 0)
              clearInterval(weibotimerId);
           else
              weibotimerId = setInterval(function() {ajaxRefresh(snaId);}, timeoutPeriod);
		   break;
		case 'buzzautorefresh':
		   snaId = 'buzzrefresh';
		   if (timeoutPeriod == 0)
              clearInterval(buzztimerId);
           else
              buzztimerId = setInterval(function() {ajaxRefresh(snaId);}, timeoutPeriod);
		   break;
		case 'fbautorefresh':
		    snaId = 'fbrefresh';
			if (timeoutPeriod == 0)
               clearInterval(fbtimerId);
            else
               fbtimerId = setInterval(function() {ajaxRefresh(snaId);}, timeoutPeriod);
			break;
		case 'twitterautorefresh':
		    snaId = 'twitterrefresh';
			if (timeoutPeriod == 0)
               clearInterval(twittertimerId);
            else
               twittertimerId = setInterval(function() {ajaxRefresh(snaId);}, timeoutPeriod);
			break;	
	    default:
		   alert("Error: Wrong field name: " + name);
		   break;
	   }
   //alert('Auto Refresh ' + value + ' ' + snaId);
 
   //document.getElementById('weiborefresh').click();
   //$('#weiborefresh').click();
   }
   
function ajaxRefresh(snaId)
   {
   document.getElementById(snaId).onclick();   
   }
 
function loadimg(fieldId){
	var id ='';
	switch(fieldId)
	   {
		case 'twitterlist':
		   id = 'refreshTweets';
		   break;
		case 'fblist':
		   id = 'refreshUpdates';
		   break;
		case 'buzzlist':
		    id = 'refreshBuzzs';
			break;
		case 'weibolist':
		    id = 'refreshWeibos';
			break;	
	    default:
		   alert("Error Wrong fieldId!");
		   break;
	   }
   	
   document.getElementById(id).innerHTML = "<span style='font-size:bold;'>Loading...</span><img src='../images/loading.gif' width='70%' height='70%'/>";
}

function hideimg(fieldId){
	var id ='';
	switch(fieldId)
	   {
		case 'twitterlist':
		   id = 'refreshTweets';
		   break;
		case 'fblist':
		   id = 'refreshUpdates';
		   break;
		case 'buzzlist':
		    id = 'refreshBuzzs';
			break;
		case 'weibolist':
		    id = 'refreshWeibos';
			break;
	    default:
		   alert("Error Wrong fieldId!");
		   break;
	   }
   document.getElementById(id).innerHTML = "";
}

function streamAjaxPost()
   {
   var xmlhttp = GetXmlHttpObject();
   if (xmlhttp == null)
      {
      alert ("Browser does not support HTTP Request!");
      return;
      }
   
   if (!verify(document.streamform))
      return;
   
   var post = unescape(document.streamform.happening.value);
   
   var parms = "";
   
   if (document.streamform.twitter.checked)
      parms += "&twitter=true";
   if (document.streamform.facebook.checked)
      parms += "&facebook=true";
   if (document.streamform.buzz.checked)
      parms += "&buzz=true";
   if (document.streamform.weibo.checked)
      parms += "&weibo=true";

   var url = "../include/streamit.php?post=" + encodeURIComponent(post);
   if (parms != '')
      url = url + parms;
   
   //alert("I am here22! " + url);
   url = url + "&sid=" + Math.random();
   xmlhttp.onreadystatechange=function(){stateChangedPost(xmlhttp)}
   xmlhttp.open("GET", url, true);
   xmlhttp.send(null);
   }


function getAjaxUpdates(myFunction, parms, fieldId)
   {
   var xmlhttp = GetXmlHttpObject();
   if (xmlhttp == null)
      {
      alert ("Browser does not support HTTP Request!");
      return;
      }
   
   var url = "../include/friendstream.php?func=" + myFunction;
   if (parms != '')
      url = url + "&parms=" + parms;
   //alert ("I am here22! " + url);
   url = url + "&sid=" + Math.random();
   xmlhttp.onreadystatechange=function(){stateChanged(xmlhttp, fieldId)}
   xmlhttp.open("GET", url, true);
   xmlhttp.send(null);
   }

function getAjaxList(myFunction, parms, fieldId)
   {
   var xmlhttp = GetXmlHttpObject();
   if (xmlhttp == null)
      {
      alert ("Browser does not support HTTP Request!");
      return;
      }
   
   var url = "../include/common.funcs.php?func=" + myFunction;
   if (parms != '')
      url = url + "&parms=" + parms;
   //alert ("I am here22! " + url);
   url = url + "&sid=" + Math.random();
   xmlhttp.onreadystatechange=function(){stateChangedList(xmlhttp, fieldId)}
   xmlhttp.open("GET", url, true);
   xmlhttp.send(null);
   }

function getAjaxTitle(title, fieldId)
   {
   var xmlhttp = GetXmlHttpObject();
   if (xmlhttp == null)
      {
      alert ("Browser does not support HTTP Request!");
      return;
      }

   var url = "../include/common.funcs.php?func=echo_title";
   url = url + "&parms=" + title;
   url = url + "&sid=" + Math.random();
   xmlhttp.onreadystatechange=function(){stateChanged(xmlhttp, fieldId)}
   xmlhttp.open("GET", url, true);
   xmlhttp.send(null);
   }
   
function getAjaxVideo(myFunc, video, fieldId)
   {
   var xmlhttp = GetXmlHttpObject();
   if (xmlhttp == null)
      {
      alert ("Browser does not support HTTP Request!");
      return;
      }

   var url = "../include/common.funcs.php?func=" + myFunc;
   url = url + "&parms=" + video;
   url = url + "&sid=" + Math.random();
   xmlhttp.onreadystatechange=function(){stateChanged(xmlhttp, fieldId)}
   xmlhttp.open("GET", url, true);
   xmlhttp.send(null);
   }

function zC(imgSrc, width, hight){
 var cWidth,cHeight;
 var popUpDiv = document.getElementById('cdtest');
 
 cWidth = screen.width;
 cHeight = screen.height;
 var cObj = document.createElement("div");
 cObj.setAttribute("id","cdiv");
 cObj.style.position = "fixed";
 cObj.style.top = 0;
 cObj.style.left = 0;
 cObj.style.backgroundColor = "#0C0C0C";
// cObj.style.filter="progid:DXImageTransform.Microsoft.Alpha(style=3,opacity=25,finishOpacity=75";
 cObj.style.opacity = 0.65;
 cObj.style.filter = "alpha(opacity=65)";
 cObj.style.width = cWidth +"px";
 cObj.style.height = cHeight + "px";
 cObj.style.zIndex = "1000";
 document.body.appendChild(cObj);
 var iObj = document.createElement("img");
 //var iObj = new Image();
 iObj.src = imgSrc;
 iObj.setAttribute("id","cimg");
 iObj.style.position = "fixed";
 iObj.style.textAlign = "center";
 iObj.style.zIndex = "1001";
 iObj.style.cursor = "pointer";
 iObj.style.top = "50%";
 iObj.style.left = "50%"; 
 
 var imgWidth = typeof(width) != 'undefined' ? width : 0;
 var imgHight = typeof(hight) != 'undefined' ? hight : 0;
 
 if (imgWidth != 0)
    {
    iObj.style.width = imgWidth + "px";;
	iObj.style.marginLeft = 0 - (imgWidth / 2) + "px";
	}
 else
    {
    iObj.style.marginLeft = 0 - (iObj.width / 2) + "px";
	iObj.style.width = iObj.width;
	}
 
 if (imgHight != 0)
    {
    iObj.style.height = imgHight + "px";
	iObj.style.marginTop = 0 - (imgHight / 2) + "px";
	}
 else
    {
    iObj.style.marginTop = 0 - (iObj.height / 2) + "px";
	iObj.style.height = iObj.height;
	}
 
 iObj.onclick = function(){
  document.body.removeChild(iObj);
  document.body.removeChild(cObj);
  document.body.removeChild(closeObj);
 }
 
 iObj.onLoad=function(){}
 
 document.body.appendChild(iObj);
 
 var closeObj = new Image();
 closeObj.setAttribute("id","close");
 closeObj.style.position = "fixed";
 closeObj.style.left = "50%"
 closeObj.style.top = "50%"
 closeObj.style.marginLeft = (iObj.width / 2) + "px";
 closeObj.style.marginTop = iObj.style.marginTop;
 closeObj.style.width = "32px";
 closeObj.style.height = "32px";
 //closeObj.style.marginLeft = "-220px" ;
 //closeObj.style.marginTop = -200+"px";
 closeObj.style.textAlign = "center";
 closeObj.src = "../images/close-button.jpg";
 closeObj.style.zIndex = "1001";
 closeObj.style.cursor = "pointer";
 closeObj.onclick = function(){
  document.body.removeChild(iObj);
  document.body.removeChild(cObj);
  document.body.removeChild(closeObj);
 }
 
 document.body.appendChild(closeObj); 
}

// popup comment
function commentPopUp(userPic, oriPost, rePost, postId, oriPostId){
 var cObj = document.createElement("div");
 cObj.setAttribute("id","cdiv");
 cObj.setAttribute("name","cdiv");
 cObj.setAttribute("class","popupwrapper");
 document.body.appendChild(cObj);
 
 var textAreaDiv = document.createElement("div");
 textAreaDiv.setAttribute("id","popupbox");
 textAreaDiv.setAttribute("class","popupretweetbox");
 
 textAreaDiv.innerHTML = '<div id="columns"><ul id="column2" class="column" style="width:100%;">' + 
                           '<li class="widget color-red" style="margin: 5px 0px 0px 0px; padding:0px">' + 
						   '<div class="widget-head"><h3>Add comment to this weibo</h3></div>' + 
						   '<div class="widget-content" style="padding-bottom:10px;" align="center">' +
						   '<img class="grid-item-avatar" style="width:48px;height:48px;margin-top:10px;" src="' + userPic +'">' +
						   '<div class="notice" style="margin-top:5px;" align="left">' + oriPost + (isblank(rePost) ? '' : rePost) + '</div><br/>' +
						   '<br/><form method="post" enctype="multipart/form-data" name="streamformpop" id="streamformpop">' +
						   '<div id="streamboxpop" name="streamboxpop" class="tweetbox">' +
						   '<textarea id="happeningpopup" class="tweetarea" oninput="textcount(this, 140, this.form.remain);" onfocus="textcount(this, 140, this.form.remain);" onblur="textcount(this, 140, this.form.remain);" onkeydown="textcount(this, 140, this.form.remain);" onkeyup="textcount(this, 140, this.form.remain);"></textarea></div><br/>' +
						   '<img class="grid-item-avatar" src="../images/sinaweibo.jpg" alt="weibo" style="width:90px;height:34px;"/>' +
						   '<div align="right"><span class="notice">Remaining:</span>' +
						   '<input class="remainpopup" disabled name="remain" value=140></div>' +
						   '<br/><div class="notice" align="left"><input type="checkbox" name="alsorepost" value="1"/>Also repost this weibo</div>' +
						   ((oriPostId != 0) ? '<div class="notice" align="left"><input type="checkbox" name="commentori" value="1"/>Also comment to weibo\'s original author</div>' : '') +
						   '<br/><div align="center"><input class="button streamit" type="button" value="Comment" onclick="' +
						   ((oriPostId != 0) ? 'rePostSNA(document.getElementById(\'happeningpopup\').value,4,\'' + postId + '\',document.streamformpop.alsorepost.checked,1,\''+oriPostId+'\')"/>' : 'rePostSNA(document.getElementById(\'happeningpopup\').value,4,\'' + postId + '\',document.streamformpop.alsorepost.checked,1,0)"/>') +
						   '<input class="button reset" type="button" value="Cancel" onclick="document.getElementById(\'close\').onclick();"/></div></form></div></li></ul></div>';

 document.body.appendChild(textAreaDiv);
 
 var closeObj = new Image();
 closeObj.setAttribute("id","close");
 closeObj.style.position = "fixed";
 closeObj.style.left = "50%"
 closeObj.style.top = "50%"
 closeObj.style.marginLeft = (170) + "px";
 closeObj.style.marginTop = -240 + "px";
 closeObj.style.width = "22px";
 closeObj.style.height = "22px";
 closeObj.src = "../images/close-button.jpg";
 closeObj.style.zIndex = "1001";
 closeObj.style.cursor = "pointer";
 closeObj.onclick = function(){
 document.body.removeChild(textAreaDiv);
 document.body.removeChild(cObj);
 document.body.removeChild(closeObj);
 }
 
 document.body.appendChild(closeObj);
}

// popup retweet/repost/like
function repostPopUp(userPic, oriPost, rePost, postId, oriPostId){
 var cObj = document.createElement("div");
 cObj.setAttribute("id","cdiv");
 cObj.setAttribute("name","cdiv");
 cObj.setAttribute("class","popupwrapper");
 document.body.appendChild(cObj);
 
 var textAreaDiv = document.createElement("div");
 textAreaDiv.setAttribute("id","popupbox");
 textAreaDiv.setAttribute("class","popupretweetbox");
 
 textAreaDiv.innerHTML = '<div id="columns"><ul id="column2" class="column" style="width:100%;">' + 
                           '<li class="widget color-red" style="margin: 5px 0px 0px 0px; padding:0px">' + 
						   '<div class="widget-head"><h3>Repost this weibo</h3></div>' + 
						   '<div class="widget-content" style="padding-bottom:10px;" align="center">' +
                           '<img class="grid-item-avatar" style="width:48px;height:48px;margin-top:10px;" src="' + userPic +'">' +
						   '<div class="notice" style="margin-top:5px;" align="left">' + (isblank(rePost) ? oriPost : rePost) + '</div><br/>' +
						   '<br/><form method="post" enctype="multipart/form-data" name="streamformpop" id="streamformpop">' +
						   '<div id="streamboxpop" name="streamboxpop" class="tweetbox">' +
						   '<textarea id="happeningpopup" class="tweetarea" oninput="textcount(this, 140, this.form.remain);" onfocus="textcount(this, 140, this.form.remain);" onblur="textcount(this, 140, this.form.remain);" onkeydown="textcount(this, 140, this.form.remain);" onkeyup="textcount(this, 140, this.form.remain);">' + (isblank(rePost) ? "": oriPost) + '</textarea></div><br/>' +
						   '<img class="grid-item-avatar" src="../images/sinaweibo.jpg" alt="weibo" style="width:90px;height:34px;"/>' +
						   '<div align="right"><span class="notice">Remaining:</span>' +
						   '<input class="remainpopup" disabled name="remain" value=140></div>' +
						   '<br/><div class="notice" align="left"><input type="checkbox" name="alsocomment" value="1"/>Add comment to author</div>' +
						   ((oriPostId != 0) ? '<div class="notice" align="left"><input type="checkbox" name="commentori" value="1"/>Also comment to weibo\'s original author</div>' : '') +
						   '<br/><div align="center"><input class="button streamit" type="button" value="Repost" onclick="' +
						   ((oriPostId != 0) ? 'rePostSNA(document.getElementById(\'happeningpopup\').value,4,\'' + postId +'\',1,document.streamformpop.alsocomment.checked,(document.streamformpop.commentori.checked ? '+oriPostId+':0))"/>' : 'rePostSNA(document.getElementById(\'happeningpopup\').value,4,\'' + postId +'\',1,document.streamformpop.alsocomment.checked,0)"/>') +
						   '<input class="button reset" type="button" value="Cancel" onclick="document.getElementById(\'close\').onclick();"/></div></form></div></li></ul></div>';

 document.body.appendChild(textAreaDiv);
 
 var closeObj = new Image();
 closeObj.setAttribute("id","close");
 closeObj.style.position = "fixed";
 closeObj.style.left = "50%"
 closeObj.style.top = "50%"
 closeObj.style.marginLeft = (170) + "px";
 closeObj.style.marginTop = -240 + "px";
 closeObj.style.width = "22px";
 closeObj.style.height = "22px";
 closeObj.src = "../images/close-button.jpg";
 closeObj.style.zIndex = "1001";
 closeObj.style.cursor = "pointer";
 closeObj.onclick = function(){
 document.body.removeChild(textAreaDiv);
 document.body.removeChild(cObj);
 document.body.removeChild(closeObj);
 }
 
 document.body.appendChild(closeObj);
}

function retweetPopUp(userPic, oriTweet, tweetId){
 var cObj = document.createElement("div");
 cObj.setAttribute("id","cdiv");
 cObj.setAttribute("name","cdiv");
 cObj.setAttribute("class","popupwrapper");
 document.body.appendChild(cObj);
 
 var textAreaDiv = document.createElement("div");
 textAreaDiv.setAttribute("id","popupbox");
 textAreaDiv.setAttribute("class","popupretweetbox");
 
 textAreaDiv.innerHTML = '<div id="columns"><ul id="column2" class="column" style="width:100%;">' + 
                           '<li class="widget color-black" style="margin: 5px 0px 0px 0px; padding:0px">' + 
						   '<div class="widget-head"><h3>Retweet this to your followers?</h3></div>' + 
						   '<div class="widget-content" style="padding-bottom:10px;" align="center">' +
						   '<img class="grid-item-avatar" style="width:48px;height:48px;margin-top:10px;" src="' + userPic +'">' +
						   '<div class="notice" style="margin-top:10px;" align="left">' + oriTweet + '</div>' +
						   '<div id="streamboxpop" name="streamboxpop"></div>' +
						   '<br/><div align="center"><input class="button streamit" type="button" value="Retweet" onclick="rePostSNA(\'none\',1,\'' + tweetId + '\',1,0,0)"/>' +
						   '<input class="button reset" type="button" value="Cancel" onclick="document.getElementById(\'close\').onclick();"/></div></div></li></ul></div>';

 document.body.appendChild(textAreaDiv);
 
 var closeObj = new Image();
 closeObj.setAttribute("id","close");
 closeObj.style.position = "fixed";
 closeObj.style.left = "50%"
 closeObj.style.top = "50%"
 closeObj.style.marginLeft = (185) + "px";
 closeObj.style.marginTop = -225 + "px";
 closeObj.style.width = "22px";
 closeObj.style.height = "22px";
 closeObj.src = "../images/close-button.jpg";
 closeObj.style.zIndex = "1001";
 closeObj.style.cursor = "pointer";
 closeObj.onclick = function(){
 document.body.removeChild(textAreaDiv);
 document.body.removeChild(cObj);
 document.body.removeChild(closeObj);
 }
 
 document.body.appendChild(closeObj);
}

function replyPopUp(userPic, oriTweet, tweetId, oriUserName){
 var cObj = document.createElement("div");
 cObj.setAttribute("id","cdiv");
 cObj.setAttribute("name","cdiv");
 cObj.setAttribute("class","popupwrapper");
 document.body.appendChild(cObj);
 
 var textAreaDiv = document.createElement("div");
 textAreaDiv.setAttribute("id","popupbox");
 textAreaDiv.setAttribute("class","popupretweetbox");
 
 textAreaDiv.innerHTML = '<div id="columns"><ul id="column2" class="column" style="width:100%;">' + 
                           '<li class="widget color-black" style="margin: 5px 0px 0px 0px; padding:0px">' + 
						   '<div class="widget-head"><h3>Reply to ' + '@'+ oriUserName + '</h3></div>' + 
						   '<div class="widget-content" style="padding-bottom:10px;" align="center">' +
						   '<img class="grid-item-avatar" style="width:48px;height:48px;margin-top:10px;" src="' + userPic +'">' +
						   '<div class="notice" style="margin-top:10px;" align="left">' + oriTweet + '</div><br/>' +
						   '<br/><form method="post" enctype="multipart/form-data" name="streamformpop" id="streamformpop">' +
						   '<div id="streamboxpop" name="streamboxpop" class="tweetbox">' +
						   '<textarea id="happeningpopup" class="tweetarea" oninput="textcount(this, 140, this.form.remain);" onfocus="textcount(this, 140, this.form.remain);" onblur="textcount(this, 140, this.form.remain);" onkeydown="textcount(this, 140, this.form.remain);" onkeyup="textcount(this, 140, this.form.remain);">' + '@'+ oriUserName + '</textarea></div><br/>' +
						   '<img class="grid-item-avatar" src="../images/full_logo_white_blue.png" alt="twitter" style="width:100px;height:18px;"/>' + 
						   '<div align="right"><span class="notice">Remaining:</span>' +
						   '<input class="remainpopup" disabled name="remain" value=140></div>' +
						   '<br/><div align="center"><input class="button streamit" type="button" value="Tweet" onclick="rePostSNA(document.getElementById(\'happeningpopup\').value,1,\'' + tweetId + '\',0,1,0)"/>' +
						   '<input class="button reset" type="button" value="Cancel" onclick="document.getElementById(\'close\').onclick();"/></div></form></div></li></ul></div>';

 document.body.appendChild(textAreaDiv);
 
 var closeObj = new Image();
 closeObj.setAttribute("id","close");
 closeObj.style.position = "fixed";
 closeObj.style.left = "50%"
 closeObj.style.top = "50%"
 closeObj.style.marginLeft = (185) + "px";
 closeObj.style.marginTop = -225 + "px";
 closeObj.style.width = "22px";
 closeObj.style.height = "22px";
 closeObj.src = "../images/close-button.jpg";
 closeObj.style.zIndex = "1001";
 closeObj.style.cursor = "pointer";
 closeObj.onclick = function(){
 document.body.removeChild(textAreaDiv);
 document.body.removeChild(cObj);
 document.body.removeChild(closeObj);
 }
 
 document.body.appendChild(closeObj);
}

function stateChangedPopUpRepost(xmlhttp, type)
   {
   if (xmlhttp.readyState == 4 || xmlhttp.readyState == "complete")
      {
	  document.getElementById('close').onclick();
	  if (type == 4)
	     {
	     getAjaxUpdates('get_weibos', '', 'weibolist');
		 //document.getElementById('streamboxpop').innerHTML = xmlhttp.responseText;
		 }
	  else if (type == 1)
	     {
		 getAjaxUpdates('get_tweets', '', 'twitterlist');
		 //document.getElementById('streamboxpop').innerHTML = xmlhttp.responseText;
	     }
	  }
   else
      {
      document.getElementById('streamboxpop').className = "tweetbox";
      document.getElementById('streamboxpop').innerHTML = "<span class='notice'>Processing...</span><img src='../images/loading.gif' style=\"width=:220px;height:19px\"/>";
	  }
   }   

function rePostSNA(text, type, id, action1, action2, oriId)
   {
   var xmlhttp = GetXmlHttpObject();
   if (xmlhttp == null)
      {
      alert ("Browser does not support HTTP Request!");
      return;
      }
	  
   var post = unescape(text);
   var parms = "";
   
   if (type == 1)
      {
	  parms += "&twitter=true";
      parms += "&sid=" + id;
	  if (action1)
	     parms += "&retweet=true";
	  else if (action2)
	     parms += "&reply=true";
	  }
   else if (type == 4)
      {
	  parms += "&weibo=true";
      parms += "&sid=" + id;
	  if (action1)
	     parms += "&zhuanfa=true";
	  if (action2)
	     parms += "&comment=true";
	  if (oriId != 0)
	     parms += "&oricomment=" + oriId;
	  }
   
   var url = "../include/repost.php?reposttext=" + encodeURIComponent(post);
   if (parms != '')
      url = url + parms;
 
   //alert ("I am here22! " + url);
   xmlhttp.onreadystatechange=function(){stateChangedPopUpRepost(xmlhttp, type)}
   xmlhttp.open("GET", url, true);
   xmlhttp.send(null);
   }


function imgpreloader(imgSrc) {
  heavyImage = new Image(); 
  heavyImage.src = imgSrc;
}
/*
page_request.onreadystatechange=function(){
   loadpage(page_request, containerid)
}*/

function verify(f)
{
  var msg = "";
  var empty_fields = "";
  var errors = "";
  var uncheckedSNA = 0;

  // Loop through the elements of the form, looking for all
  // text and textarea elements that don't have an
  //  "optional" property defined.  Then, check for fields
  // that are empty and make a list of them.
  // Also, if any of these elements have a "min" or a "max"
  // property defined, then verify that they are numbers 
  // and that they are in the right range.
  // Put together error messages for fields that are wrong.
  for(var i = 0; i < f.length; i++)
  {
     var e = f.elements[i];	 	
	 
	 if (e.type == "checkbox" && (e.name == "twitter" || e.name == "facebook" || e.name == "buzz" || e.name == "weibo"))
        {
		   if (!e.checked)
		   {
			 //alert("I am here!\n" + e.name);
			 ++uncheckedSNA;
             continue;
		   }
	    }
		
	if (e.type == "textarea" && e.name == "happening")
	   {
		   if (e.value == "What is happening?" || isblank(e.value))
		   {
			 empty_fields += "\n        " +
                           e.name;
             continue;
		   }
	    }
		
	if (e.type == "text" && e.name == "search")
	   {
		   if (e.value == "Search" || isblank(e.value))
		   {
			 empty_fields += "\n        " +
                           e.name;
             continue;
		   }
	    }	
	 
  } // for each character in field
  
  // For friendstream
  if (uncheckedSNA == 4)
     {
	 errors += "____________________________________________________  _  _\n\n"
     errors += "Error: You have NOT selected any SNA network yet!\n"
     errors += "Please select at least one SNA network!\n";
     errors += "____________________________________________________  _  _\n\n"
     }

  // Now, if there were any errors, then display the
  // messages, and return true to prevent the form from
  // being submitted.  Otherwise return false
  if (!empty_fields && !errors)
  {
	 return true;
  }
  
  msg  = "____________________________________________________  _  _\n\n"
  msg += "The form was not submitted because of the " +
         "following error(s).\n";
  msg += "Please correct these error(s) and re-submit.\n";
  msg += "____________________________________________________  _  _\n\n"

  if (empty_fields)
  {
     msg += "- The following required field(s) are empty:"
           + empty_fields + "\n";
     if (errors)
        msg += "\n";
  }
  msg += errors;
  alert(msg);
  return false;
}

// A utility function that returns true if a string 
// contains only whitespace characters.
function isblank(s)
{
  for(var i = 0; i < s.length; i++)
  {
     var c = s.charAt(i);
     if ((c != ' ') &&
         (c != '\n') &&
         (c != '\t'))
        return false;
  }
  return true;
}

function strChinese(str){  
  var   pattern   =   /[\u4E00-\u9FA5]/;    
  return   pattern.test(str);  
  }  

var thumbExtArray = new Array(".gif", ".jpg", ".jpeg");
var videoExtArray = new Array(".flv");

// This function used to check the textarea's characters count
function textcount(message,total,remain)
{
   var max = total;
   if (message.value.length > max) {
       message.value = message.value.substring(0,max);
       remain.value = 0;
       alert("Your text can not excess " + total + " characters! Extra characters will be truncated!");
	   return false;
      }
   else {
        remain.value = max - message.value.length;
		return true;
      }
}

function utf8_encode ( argString ) {
    // Encodes an ISO-8859-1 string to UTF-8  
    // 
    // version: 1009.2513
    // discuss at: http://phpjs.org/functions/utf8_encode    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: sowberry
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman    // +   improved by: Yves Sucaet
    // +   bugfixed by: Onno Marsman
    // +   bugfixed by: Ulrich
    // *     example 1: utf8_encode('Kevin van Zonneveld');
    // *     returns 1: 'Kevin van Zonneveld'    var string = (argString+''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
 
    var utftext = "";
    var start, end;
    var stringl = 0; 
    start = end = 0;
    stringl = string.length;
    for (var n = 0; n < stringl; n++) {
        var c1 = string.charCodeAt(n);        var enc = null;
 
        if (c1 < 128) {
            end++;
        } else if (c1 > 127 && c1 < 2048) {            enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
        } else {
            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
        }
        if (enc !== null) {            if (end > start) {
                utftext += string.substring(start, end);
            }
            utftext += enc;
            start = end = n+1;        }
    }
 
    if (end > start) {
        utftext += string.substring(start, string.length);    }
 
    return utftext;
}

function utf8_decode ( str_data ) {
    // Converts a UTF-8 encoded string to ISO-8859-1  
    // 
    // version: 1009.2513
    // discuss at: http://phpjs.org/functions/utf8_decode    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // +      input by: Aman Gupta
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Norman "zEh" Fuchs
    // +   bugfixed by: hitwork    // +   bugfixed by: Onno Marsman
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: utf8_decode('Kevin van Zonneveld');
    // *     returns 1: 'Kevin van Zonneveld'    var tmp_arr = [], i = 0, ac = 0, c1 = 0, c2 = 0, c3 = 0;
    
    str_data += '';
    
    while ( i < str_data.length ) {        c1 = str_data.charCodeAt(i);
        if (c1 < 128) {
            tmp_arr[ac++] = String.fromCharCode(c1);
            i++;
        } else if ((c1 > 191) && (c1 < 224)) {            c2 = str_data.charCodeAt(i+1);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
            i += 2;
        } else {
            c2 = str_data.charCodeAt(i+1);            c3 = str_data.charCodeAt(i+2);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
            i += 3;
        }
    } 
    return tmp_arr.join('');
}

/* BAROMETER feedback */
var BAROMETER;
if(BAROMETER == undefined) {
  BAROMETER = {};
}

BAROMETER.load = function(barometer_id) {
  this.barometer_id = barometer_id;
  this.empty_url = "http://getbarometer.s3.amazonaws.com/install/images/transparent.gif";
  this.feedback_url = 'http://install.getbarometer.com/system/feedback_form/' + this.barometer_id;

  this.tab_html = '<a id="barometer_tab" onclick="BAROMETER.show();" href="javascript:void(0);">Feedback</a>';
  this.overlay_html = '<div id="barometer_overlay" style="display: none;">' +
			'<div id="barometer_main" style="top: 130.95px;">' +
			'<a id="barometer_close" onclick="document.getElementById(\'barometer_overlay\').style.display = \'none\';return false" href="javascript:void(0);"/></a>' +
			'<div id="overlay_header">' +
				'<a href="http://getbarometer.com">Powered by Barometer</a>' +
				'</div>' +
				'<iframe src="' + this.empty_url + '" id="barometer_iframe" allowTransparency="true" scrolling="no" frameborder="0" class="loading"></iframe>' +
				'</div>' +
				'<div id="barometer_screen" onclick="document.getElementById(\'barometer_overlay\').style.display = \'none\';return false" style="height: 100%;"/>' +
				'</div>' +
	'</div>';
       
    document.write(this.tab_html);
    document.write(this.overlay_html);    
};

BAROMETER.show = function() {
  document.getElementById('barometer_iframe').setAttribute("src", this.feedback_url);
  document.getElementById('barometer_overlay').style.display = "block";
  return false;
};

function masonryContainer(id){  
  var wall = new Masonry( document.getElementById('container' + id) );
  }  
