<?php
require_once ('../include/friendstream.php');
//require_once ('../include/AmazonAPI/sampleSettings.php');
//require_once ('../include/AmazonAPI/AmazonECS.class.php');
//ini_set('session.gc_maxlifetime', 3600);
//session_cache_limiter ('private, must-revalidate');
//session_cache_expire(60); // in minutes
//$lifeTime = 24 * 3600; 
//setcookie(session_name(), session_id(), time() + $lifeTime, "/"); 
session_start();

$category = NULL;
$keyword = NULL;
if (isset($_GET['cat']))
   {
   $category = $_GET['cat'];
   if (isset($_SESSION['lastCategory']) && $_SESSION['lastCategory'] != $category)
	  {
	  $_SESSION['lastCategory'] = $category;
	  }
   $_SESSION['loadCount'] = 0;	  
   }
else
   {
   $category = 'All';
   unset($_SESSION['lastCategory']);
   unset($_SESSION['loadCount']);   
   }
   
if (isset($_POST['category']))
   {
   $category = $_POST['category'];
   if (isset($_SESSION['lastCategory']) && $_SESSION['lastCategory'] != $category)
	  {
	  $_SESSION['lastCategory'] = $category;
	  }
   $_SESSION['loadCount'] = 0;
   if (isset($_POST['search']))
      {
	  $keyword = $_POST['search'];
	  }
   }
else
   {
   //unset($_SESSION['lastKw']);
   }


/* Create facebook connection */
    $config['baseurl']  =  'http://pinamazon.tk/';
	$config['baseurllogout']  =  '../include/twitterapi/clearsession.php?twitter=clear';
	
	// clear seesion variable for logout
	if (isset($_GET['facebook']) && $_GET['facebook'] == 'clear')
	   unset($_SESSION['facebook']);

	// Create our Application instance.
    if (!isset($_SESSION['facebook'])) {
	$facebook = new Facebook(array(
      'appId'  => $fbconfig['appid'],
      'secret' => $fbconfig['secret'],
      'cookie' => true,
    ));
	}
	else{
	   $facebook = $_SESSION['facebook'];
	}
	
	 // We may or may not have this data based on a $_GET or $_COOKIE based session.
    // If we get a session here, it means we found a correctly signed session using
    // the Application Secret only Facebook and the Application know. We dont know
    // if it is still valid until we make an API call using the session. A session
    // can become invalid if it has already expired (should not be getting the
    // session back in this case) or if the user logged out of Facebook.
	
	$user = $facebook->getUser();
	
	//print_r($facebook->getAccessToken());

    $fbme = null;
    // Session based graph API call.
    if ($user) {
      try {
        //$uid = $facebook->getUser();
        $fbme = $facebook->api('/me');
      } catch (FacebookApiException $e) {
          dump($e);
      }
    }
 
    //if user is logged in and session is valid.
    if ($fbme){
	    if (!isset($_SESSION['facebook']))	
           $_SESSION['facebook'] = $facebook;		
    }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
    <title>Pinamazon - Pinterest style Amazon</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="Pinterest style Amazon" />
    <meta name="keywords" content="amazon, online shopping, ebay, pinterest, facebook " />
    <meta name="author" content="Ye Henry Tian" />
    <meta name="distribution" content="global" />
    <meta name="robots" content="follow, all" />
    <meta name="language" content="en" />
    <meta name="revisit-after" content="2 days" />
    <meta content="jaolb+4U3+k7xWefD1IT+pPv3Nevk/TJsQW8ZV3uXBI=" name="verify-v1" />
    <meta property="wb:webmaster" content="309eb8104fb27ab2" />
    <link rel="shortcut icon" href="../images/pinterest-icon.jpg" />
    <link href="fscss2.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="../include/desandro-vanilla-masonry-1e41589/css/style.css" />
</head>

<body>

<script src="../include/desandro-vanilla-masonry-1e41589/masonry.min.js"></script>
<script type="text/javascript">
window.onload = function() {
  var wall = new Masonry( document.getElementById('container') );
  
};
//columnWidth: 100
</script>

<script type="text/javascript" language="javascript" src="../include/ajax2.js" charset="utf-8"></script>
<script type="text/javascript" src="../include/jQuery-Screw/examples/js/jquery.1.6.1.js"></script>
<script type="text/javascript" src="../include/jQuery-Screw/examples/js/jquery.screw.js"></script>
<script type="text/javascript">
// Initialize jQuery
jQuery(document).ready(function($){

// Call screw on the body selector  
$("body").screw({
    loadingHTML: '<div align="center"><img alt="Loading" src="../images/loadingBlack64.gif"></div>'
});

});
</script>

<!-- UJian Button BEGIN -->
<script type="text/javascript" src="http://v1.ujian.cc/code/ujian.js?type=slide"></script>
<!-- UJian Button END -->

<div id="fb-root"></div>
<script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({appId: '<?=$fbconfig['appid']?>', status: true, cookie: true, xfbml: true});

                /* All the events registered */
                FB.Event.subscribe('auth.login', function(response) {
                    // do something with response
                    login();
                });
                FB.Event.subscribe('auth.logout', function(response) {
                    // do something with response
                    logout();
                });
				
            };
            (function() {
                var e = document.createElement('script');
                e.type = 'text/javascript';
                e.src = document.location.protocol +
                    '//connect.facebook.net/en_US/all.js&xfbml=1';
                e.async = true;
                document.getElementById('fb-root').appendChild(e);
            }());

            function login(){
                document.location.href = "<?=$config['baseurl']?>";
            }
            function logout(){
                document.location.href = "<?=$config['baseurllogout']?>";
            }
</script>



<div id="friendstreamwrapper">
<div id="wrapper">

<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_floating_style addthis_32x32_style" style="right:0px;top:150px;">
<a class="addthis_button_preferred_1"></a>
<a class="addthis_button_preferred_2"></a>
<a class="addthis_button_preferred_3"></a>
<a class="addthis_button_preferred_4"></a>
<a class="addthis_button_compact"></a>
</div>
<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
<script type="text/javascript" src="http://s7.addthis.com/js/300/addthis_widget.js#pubid=ra-50610b553e7cadd5"></script>
<!-- AddThis Button END -->

<div id="head">
    <h1><a href="http://pinamazon.tk"></a></h1>

<div id="search" align="right">
<form action="http://www.google.ca/cse" id="cse-search-box" target="_blank">
  <div>
    <input type="hidden" name="cx" value="partner-pub-6635598879568444:c8xv0514j98" />
    <input type="hidden" name="ie" value="UTF-8" />
    <input type="text" name="q" size="30" />
    <input type="image" style="width:25px; height:25px;" name="sa" value="Search" src="../images/icon_search.png"/>
  </div>
</form>
<script type="text/javascript" src="http://www.google.ca/cse/brand?form=cse-search-box&amp;lang=en"></script>
</div>
</div>

<div id="navigationhome">
<nav role="navigation">
	<ul>
		<li><img src="../images/new-twitter-home.png" style="width:16px; height:16px; float:left;"/><a href="./">home</a></li>
		<!-- <li><a href="./about.html">about</a></li> -->
		<li><a href="http://about.me/yehenrytian" target="_blank">about me</a></li>
	</ul>
</nav>
</div>

<div id="columns">

<?php
/*
<ul id="column2" class="column">
  <li class="widget color-fbblue">  
   <div class="widget-head">
     <h3>Facebook Updates</h3>
   </div>
   <div class="widget-content">
    <?php if (!$fbme) { ?>
        <span class="notice" style="padding-bottom:5px">Login via Facebook to see your recent wall posts from your friends.</span>
    <?php } ?>
    <div class="snaoauth">
    <p style="padding-top:5px">
    <fb:login-button autologoutlink="true" perms="read_stream,user_status,friends_status,user_location,friends_location,status_update,publish_stream"></fb:login-button>
    </p></div>
    
    <?php 
	// emit updates list
	if ($fbme){ 
	   ?>
	   <!-- Data retrived from user profile are shown here --> 
       
      <!-- <div class="snaoauth"><p><img src="../images/adept_update.png" alt="refresh" width="25px" height="25px"/><a id="fbrefresh" href="javascript:void(0);" onclick="getAjaxUpdates('get_fbupdates', '', 'fblist')" title="refresh updates">refresh posts</a></p></div>
       <div id="refreshUpdates" class="loading"></div>
       <?php
	     //echo (get_fbupdates());
	   } ?>
    </div>
    </li>
</ul>*/

?>

       
<ul id="column2" class="column">
  <li class="widget color-red" id="intro">  
    <div class="widget-head">
      <h3>Pinamazon(beta) - Pinterest style Amazon online shopping</h3>
    </div>
    <div class="widget-content">
    
    <div align="center">
    <form onSubmit="return(verify(this));" action="/" class="search" method="post">
	<fieldset>
        <a href="../include/twitterapi/clearsession.php?twitter=clear" title="Search"><img src="http://creativehoopla.com/bookmarketing/images/stories/search-icon.png" width="36px" height="36px" alt="Search"/></a>
        <select name="category">
        <?php 
		  if (isset($_POST['category'])) 
		     echo ('<option selected="selected">'.$category.'</option>'); 
		?>
			<option value="All">All</option>
            <option value="Apparel">Apparel</option>
            <option value="Automotive">Automotive</option>
            <option value="Baby">Baby</option>
            <option value="Beauty">Beauty</option>
			<option value="Books">Books</option>            
            <option value="DVD">DVD</option>
			<option value="Electronics">Electronics</option>
            <option value="Jewelry">Jewelry</option>
            <option value="Kitchen">Kitchen</option>
			<option value="Music">Music</option>
            <option value="Shoes">Shoes</option>
			<option value="Software">Software</option>
			<option value="Toys">Toys</option>
            <option value="Video">Video</option>
            <option value="VideoGames">VideoGames</option>
            <option value="Watches">Watches</option>
		</select>
        <?php 
		  if ($keyword == NULL) 
		     echo ('<input name="search" type="text" placeholder="Search" />'); 
		  else
		     echo ('<input name="search" type="text" placeholder="Search"  value="'.$keyword.'" />');   
		?>
		<input type="submit" value="Go" />
	</fieldset>
    </form>
    </div>		
    
    <?php
     echo '<div class="snaoauth"><p>';
	
	 echo '<img src="../images/shopbydep.png" alt="refresh" />';
	 echo '<a id="All" href="./?cat=All" title="All" '. (($category == NULL || $category == 'All') ? 'class="button red"' : '').'>All</a>';
	 echo ' | <a id="Apparel" href="./?cat=Apparel" title="Apparel" '.(($category == 'Apparel') ? 'class="button red"' : '').'>Apparel</a>';
	 echo ' | <a id="Automotive" href="./?cat=Automotive" title="Automotive" '.(($category == 'Automotive') ? 'class="button red"' : '').'>Automotive</a>';
	 echo ' | <a id="Baby" href="./?cat=Baby" title="Baby" '.(($category == 'Baby') ? 'class="button red"' : '').'>Baby</a>';
	 echo ' | <a id="Beauty" href="./?cat=Beauty" title="Beauty" '.(($category == 'Beauty') ? 'class="button red"' : '').'>Beauty</a>';
	 
	 echo ' | <a id="Books" href="./?cat=Books" title="Books" '.(($category == 'Books') ? 'class="button red"' : '').'>Books</a>';
	 echo ' | <a id="DVD" href="./?cat=DVD" title="DVD" '.(($category == 'DVD') ? 'class="button red"' : '').'>DVD</a>';
	 echo ' | <a id="Electronics" href="./?cat=Electronics" title="Electronics" '.(($category == 'Electronics') ? 'class="button red"' : '').'>Electronics</a>';
	 echo ' | <a id="Jewelry" href="./?cat=Jewelry" title="Jewelry" '.(($category == 'Jewelry') ? 'class="button red"' : '').'>Jewelry</a>';
	 echo ' | <a id="Kitchen" href="./?cat=Kitchen" title="Kitchen" '.(($category == 'Kitchen') ? 'class="button red"' : '').'>Kitchen</a>';
	 echo ' | <a id="Music" href="./?cat=Music" title="Music" '.(($category == 'Music') ? 'class="button red"' : '').'>Music</a>';
	 echo ' | <a id="Shoes" href="./?cat=Shoes" title="Shoes" '.(($category == 'Shoes') ? 'class="button red"' : '').'>Shoes</a>';
	 echo ' | <a id="Software" href="./?cat=Software" title="Software" '.(($category == 'Software') ? 'class="button red"' : '').'>Software</a>';
	 echo ' | <a id="Toys" href="./?cat=Toys" title="Toys" '.(($category == 'Toys') ? 'class="button red"' : '').'>Toys</a>';
	 echo ' | <a id="Video" href="./?cat=Video" title="Video" '.(($category == 'Video') ? 'class="button red"' : '').'>Video</a>';
	 echo ' | <a id="VideoGames" href="./?cat=VideoGames" title="VideoGames" '.(($category == 'VideoGames') ? 'class="button red"' : '').'>VideoGames</a>';
	 echo ' | <a id="Watches" href="./?cat=Watches" title="Watches" '.(($category == 'Watches') ? 'class="button red"' : '').'>Watches</a>';
	 
	 echo '</p></div>';
	
	if (1)
	   {
	   //echo ("here44!\n");	   
	   if ($keyword != NULL)
	      {
	      echo (search_amazon_items($category, $keyword));

		  }
	   else
	      {
	      echo (search_amazon_items($category));
		  }
	   }
	?> 
   </div>
   </li>
</ul>

</div> <!--columns-->



<div id="columns"> <!--footer-->
<ul id="column-footer" class="column">
  <li class="widget color-black" id="intro">
   <div class="widget-head">
    <h3></h3>
   </div>
   <div class="widget-content">
   <div class="columnleft" style="width:25%;">
   <h3>Site partners</h3>
	<ul>
    <li><a target="_blank" href="http://pinweibo.tk"><strong>Pinweibo.tk</strong></a> - Pinterest style weibo wall</li>
    <li><a target="_blank" href="http://friendstream.ca"><strong>Friendstream.ca</strong></a> - Your realtime social network updates</li>
    <li><a target="_blank" href="http://pintweet.tk"><strong>Pintweet.tk</strong></a> - Pinterest style twitter wall</li>
    <li><a target="_blank" href="http://firsttimer.ca"><strong>Firsttimer.ca</strong></a> - Get things done yourself</li>
	<!-- <li><a target="_blank" href="http://www.000webhost.com/489783.html"><strong>000webhost hosting</strong></a> - Free PHP Webhosting</li> -->
    <li><a target="_blank" href="http://desandro.com/"><strong>David DeSandro</strong></a> - Revels in the creative process.</li>
    <!-- <li><a target="_blank" href="http://net.tutsplus.com/"><strong>Nettuts+</strong></a> - Web development tutorials</li>-->
    </ul>
</div>

<div class="columnleft" style="width:10%;">
   <h3>Links</h3>
	<ul>
    <li><a target="_blank" href="http://pinweibo.tk"><strong>Pinweibo</strong></a></li>
    <li><a target="_blank" href="http://pintweet.tk"><strong>Pintweet</strong></a></li>	
    <li><a target="_blank" href="http://www.pinterest.com/"><strong>Pinterest</strong></a></li>	
    <li><a target="_blank" href="http://amazon.com"><strong>Amazon</strong></a></li>				
	<li><a target="_blank" href="http://www.taobao.com/"><strong>淘宝</strong></a></li>
    <!-- <li><a target="_blank" href="http://www.meilishuo.com/+"><strong>美丽说</strong></a></li> -->
    <!--<li><a target="_blank" href="http://www.duitang.com/"><strong>堆糖</strong></a></li>			-->
	</ul>
</div>

<div class="columnleft" style="width:25%;">
     <a href="https://twitter.com/yehenrytian" class="twitter-follow-button" data-show-count="false" data-dnt="true">Follow @yehenrytian</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
     <p>Developed by: <a target="_blank" href="http://about.me/yehenrytian">Ye Henry Tian</a></p>
     <h4>&copy; copyright 2012 - 2013 <a href="http://pinamazon.tk">Pinamazon.tk</a></h4>
</div>

<div class="columnleft" style="float:right;width:25%;">      
  <a href="http://s03.flagcounter.com/more/EzF"><img src="http://s03.flagcounter.com/count/EzF/bg=FFFFFF/txt=075FF7/border=0C9FCC/columns=2/maxflags=12/viewers=0/labels=1/pageviews=1/" alt="free counters" border="0"></a>
</div>

   </div>
   </li>
</ul>
</div> <!--footer-->

</div> <!--wrapper-->
</div> <!--friendstreamwrapper-->

 <!--<script type='text/javascript' src='../include/jquery-1.4.4.min.js?ver=1.4.4'></script>-->
 <!--<script type="text/javascript" src="jquery-ui-personalized-1.6rc2.min.js"></script>-->
 <script type="text/javascript" src="inettuts.js"></script>
    
<style type='text/css'>@import url('http://getbarometer.s3.amazonaws.com/install/assets/css/barometer.css');</style>
<script type="text/javascript" charset="utf-8">
  BAROMETER.load('f17xCQkSM27COTrmjXKrE');
</script>
 
</body>
</html>
