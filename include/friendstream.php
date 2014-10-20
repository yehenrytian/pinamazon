<?php
require_once ('AmazonAPI/sampleSettings.php');
require_once ('AmazonAPI/AmazonECS.class.php');
require_once ('facebookapi/fbmain.php');

$func = '';
if (isset($_GET['func']))
   {
   $func = $_GET['func']; 
   }

if (function_exists($func)) 
   {
   if ($func == "short_streampost")
      {
	  echo (call_user_func_array($func, $_GET['parms']));
	  }
   else if (isset($_GET['parms']))
      {
      $parms = $_GET['parms'];
	  //echo $parms;
	  $args = explode(',', $parms);
	  //print_r($args);
	  echo (call_user_func_array($func, $args));
	  }
   else
      echo ($func());
   }

function echo_title($title)
   {
   echo ($title); 
   }
  
// function used to shorten URL in streamed post
function short_streampost($t) 
  {
  if (get_magic_quotes_gpc())
     $t = stripslashes($t);
  
   // link URLs
  // make sure to exclude already shorten url by sina t.cn service	  
  //$t = " ".preg_replace( "/(([[:alnum:]]+:\/\/)|www\.)(?!(t|sinaurl)\.cn)([^[:space:]]*)".
	 // "([[:alnum:]#?\/&=])/ie", "getShortUrlStreamIt('\\1\\3\\4')", $t);
  
  $t = " ".preg_replace( "/(([htps]+:\/\/)|www\.)([^[:space:]]*)".
	  "([[:alnum:]#?\/&=])/ie", "getShortUrlStreamIt('$1$3$4')", $t);
  
  //$t = " ".preg_replace('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', "getShortUrlStreamIt('$1')", $t);
  return trim($t);
  }
   
function isValidURL($url)
   {
   return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
   }   

   
// function used to get recent tweets for the user in Pinterest style
function get_tweets_pin($category = 'home')
   {
   ini_set('session.gc_maxlifetime', 3600);
   session_cache_limiter ('private, must-revalidate'); 
   session_cache_expire(60); // in minutes 	   
   session_start();
   
   // do pagenavigation
   if (isset($_SESSION['lastCategory']))
      {
	  if ($_SESSION['lastCategory'] == $category)
	     {
		 $_SESSION['loadCount']++;
		 }
	  else
	     {
		 $_SESSION['lastCategory'] = $category;
		 $_SESSION['loadCount'] = 1;
		 }
	  }
   else
      {
      $_SESSION['lastCategory'] = $category;
	  $_SESSION['loadCount'] = 1;  
	  }

   
   $loadCount = $_SESSION['loadCount'];
   //echo ("loadCount is ".$loadCount);
   
   $max_id = 0;
   // fetch public timeline in json format
   if (isset($_SESSION['twitter']))
      {
	  $twitter = $_SESSION['twitter'];
      if (!$twitter)
         {
         $error .= '<h3>Session expired! Please refresh page to sign in again.</h3>';
	     return $error;
	     }
	  
	  switch($category)
	     {
		 case 'home':
		    if ($max_id > 0)
		       $options = array('include_entities' => 1, 'count' => 20, 'page' => $loadCount, 'max_id' => $max_id);
			else
			   $options = array('include_entities' => 1, 'count' => 20, 'page' => $loadCount);   
            $xml = $twitter->getHomeTimeline($options);
   			break;
		 case 'me':
		    if ($max_id > 0)
		       $options = array('include_entities' => 1, 'count' => 20, 'include_rts' => 1, 'page' => $loadCount, 'max_id' => $max_id);
			else
			   $options = array('include_entities' => 1, 'count' => 20, 'include_rts' => 1, 'page' => $loadCount);
		    $xml = $twitter->getUserTimeline($options);			
			break;
		 case 'public':
            $options = array('include_entities' => 1, 'count' => 20);
            $xml = $twitter->getPublicTimeline($options);
			break;
		case 'mentions':
		    if ($max_id > 0)
		       $options = array('include_entities' => 1, 'count' => 20, 'include_rts' => 1, 'page' => $loadCount, 'max_id' => $max_id);
			else
			   $options = array('include_entities' => 1, 'count' => 20, 'include_rts' => 1, 'page' => $loadCount);
		    $xml = $twitter->getMentions($options);			
			break;
		case 'replies':
		    if ($max_id > 0)
		       $options = array('include_entities' => 1, 'count' => 20, 'include_rts' => 1, 'page' => $loadCount, 'max_id' => $max_id);
			else
			   $options = array('include_entities' => 1, 'count' => 20, 'include_rts' => 1, 'page' => $loadCount);
		    $xml = $twitter->getReplies($options);			
			break;
		case (strncmp('search', $category, 6) == 0) :
		    //echo (substr($category, 6));
		    //$options = array('include_entities' => 1, 'result_type' => 'mixed', 'q' => 'china', 'rpp' => 20);
		    //$xml = $twitter->http('http://search.twitter.com/search.json?result_type=mixed&include_entities=1&q=china', 'GET');
			//$xml = $twitter->searchTwitter($options);
			//$xml  = json_decode($xml, true);
			return echo_tweets_json(substr($category, 6));		
	     }
	  
	  //print_r($wb);
	  
	  }
   else if ($category == 'public')
      {
	  $twitter = $_SESSION['twitterOAuth'];
      if (!$twitter)
         {
         $error .= '<h3>Session expired! Please refresh page to sign in again.</h3>';
	     return $error;
	     }
	  $xml = $twitter->http('http://api.twitter.com/1/statuses/public_timeline.xml?&include_entities=t&count=20', 'GET');
	  }
   
   $twitter_status = new SimpleXMLElement($xml);
   
   //print_r($twitter_status->status[0]);
   
   $counts = count($twitter_status->status);
   $tweets = '<div id="twitterlist">';
   $tweets .= '<h4>' .$counts. ' tweet(s) found. </h4>';
   $tweets .= '<div style="overflow:auto; overflow-x: hidden; height:100%; width:100%; -moz-border-radius: 15px;">';
   if ($loadCount > 1)
      $tweets .= '<div id="container'.$loadCount.'" class="clearfix">';
   else
      $tweets .= '<div id="container" class="clearfix">';
	  
   foreach($twitter_status->status as $status){
      foreach($status->user as $user){
	     $tweets .= '<div class="box col2"><div class="grid-item twitter"><div class="grid-item-content">';
         $tweets .= (parse_twitter(htmlspecialchars($status->text)));
		 $max_id = $status->id;
		 //$streamit = 'FS:@'.$user->screen_name.': '.$status->text;
		 
		 // handle entities
		 if (count($status->entities->media) > 0)
		    {
		    $media = $status->entities->media[0];
			//print_r($media->creative);
			$tweets .= '<br/><a href="javascript:void(0);"><img onmouseover="imgpreloader(\''.$media->creative->media_url[0].'\')" class="weiboimg" onclick="zC(\''.$media->creative->media_url[0].'\')" src="'.$media->creative->media_url[0].'" width='.($media->creative->sizes->small->w / 2).' height='.($media->creative->sizes->small->h / 2).'/></a>';
		    }		 
		 
	     $tweets .= '<br/><div><img src="../images/twitter.ico" alt="twitter" /><a target="_blank" href="http://twitter.com/'.$user->name.'/statuses/'.$status->id.'">Tweet Link</a></div>';
		 if (isset($_SESSION['twitter']))
		    {
		 $tweets .= '<br/><div align="right" style="font-size:90%">';
		 /*if ($status->retweeted == 'true')
		    $tweets .= '<img src="../images/retweet_on.gif" alt="retweet_on" style="width:13px; height:13px;"/>';
		 else*/
		    $tweets .= '<img src="../images/icon-twitter-retweet.png" alt="retweet" style="width:13px; height:13px;"/>';
		 $tweets .= '<a href="javascript:void(0);" onclick="retweetPopUp('.'\''.htmlspecialchars(addcslashes($user->profile_image_url, "\n\r\'\""), ENT_QUOTES).'\',\''.htmlspecialchars(addcslashes($status->text, "\n\r\'\""), ENT_QUOTES).'\',\''.$status->id.'\')">Retweet</a> | <img src="../images/icon-twitter-reply.png" alt="reply" style="width:13px; height:13px;"/><a href="javascript:void(0);" onclick="replyPopUp('.'\''.htmlspecialchars(addcslashes($user->profile_image_url, "\n\r\'\""), ENT_QUOTES).'\',\''.htmlspecialchars(addcslashes($status->text, "\n\r\'\""), ENT_QUOTES).'\',\''.$status->id.'\','.'\''.$user->screen_name.'\')">Reply</a></div>';
			}
		 $tweets .= '</div><div class="grid-item-meta">';
	     $tweets .= '<a target="_blank" href="http://twitter.com/'.$user->name.'"><img class="grid-item-avatar" style="width:25px; height:25px;" src="'.$user->profile_image_url.'"></a>';
         $tweets .= '<a target="_blank" href="http://twitter.com"><img src="../images/twitter.gif" class="grid-service-icon" alt="Twitter" style="width: 16px; height: 16px;"></a>';
	     $tweets .= 'Tweeted by <a target="_blank" href="http://twitter.com/#!/'.$user->screen_name.'">'.$user->name.'</a>';
		 if ($user->verified == 'true')
			$tweets .= '<img src="../images/verified.png" alt="verified" width="13px" height="13px" />';
		 
		 $tweets .= ' via '.$status->source;
		 if ($user->location != NULL)
		    $tweets .= ' from '.$user->location.'<br /><span class="grid-item-date">'.$status->created_at.'</span>';
	     else
		    $tweets .= '<br /><span class="grid-item-date">'.$status->created_at.'</span>';
       }
     $tweets .= '</div></div></div>';
     }  
   $tweets .= '</div></div></div>';
   
   // auto load control
   if ($category != 'public' && $loadCount <= 10)
      {
	  $tweets .= '<span class="screw" rel="../include/friendstream.php?func=get_tweets_pin&parms='.$category.'"></span>';
	  }
   else if ($category == 'public' && $loadCount <= 10)
      {
	  $tweets .= '<span class="screw" rel="../include/friendstream.php?func=get_tweets_pin&parms=public"></span>';
	  } 
   
   return $tweets;	   
   }
   
// function used to get recent tweets for the user in Pinterest style
function search_amazon_items($category = 'All', $keyword = 'new')
{
   //ini_set('session.gc_maxlifetime', 3600);
   //session_cache_limiter ('private, must-revalidate');
   //session_cache_expire(60); // in minutes
   //$lifeTime = 24 * 3600;
   //setcookie(session_name(), session_id(), time() + $lifeTime, "/"); 
   session_start();
   
   $keyword = urldecode($keyword);
    // fetch public timeline in json format
   if (isset($_SESSION['amazon']))
   {
	  //echo ("here11!\n");
	  $amazonEcs = $_SESSION['amazon'];
      if (!$amazonEcs)
      {
	  $error .= '<h3>Session expired! Please click <a href="../include/twitterapi/clearsession.php?twitter=clear" title="Refresh">here</a> to refresh page.</h3>';
      return $error;
	  }
   }
   else
   { 
   try 
	 {
     //echo ("I am here 7788\n");
	 $amazonEcs = new AmazonECS(AWS_API_KEY, AWS_API_SECRET_KEY, 'com', AWS_ASSOCIATE_TAG);
	 $amazonEcs->associateTag(AWS_ASSOCIATE_TAG);
	 $_SESSION['amazon'] = $amazonEcs;
	 //$response = $amazonEcs->category('Bikinis')->responseGroup('Large')->search("sexy");
	 }
     catch(Exception $e)
     {
     echo $e->getMessage();
     }   
   //$error .= '<h3>Session expired! Please click <a href="../include/twitterapi/clearsession.php?twitter=clear" title="Refresh">here</a> to refresh page.</h3>';
   //echo $error;
   }
   
   // do pagenavigation
   if (isset($_SESSION['lastCategory']))
      {
	  //echo ($_SESSION['lastCategory']);	  
	  if ($_SESSION['lastCategory'] == $category)
	     {
		 //echo ("here11\n");	 
		 $_SESSION['loadCount']++;
		 }
	  else
	     {
		 $_SESSION['lastCategory'] = $category;
		 //echo ("here22\n");	 
		 $_SESSION['loadCount'] = 1;
		 }
	  }
   else
      {
      $_SESSION['lastCategory'] = $category;
	  //echo ("here33\n");	 
	  $_SESSION['loadCount'] = 1;
	  }
   
   $loadCount = $_SESSION['loadCount'];	
  
	  
   try 
   {
   $response = $amazonEcs->category($category)->responseGroup('Images,ItemAttributes,OfferSummary,Reviews')->page($loadCount)->search($keyword);
   //var_dump($response);
   }
   catch(Exception $e)
   {
   echo $e->getMessage();
   }
   	  
   $counts = $response->Items->TotalResults;
   $items = '<div id="amazonlist">';
   if (func_num_args() == 2)
      $items .= '<h4>Total ' . $counts. ' item(s) found for: "'.$keyword.'" in "'.$category.'" (Page '.$loadCount.'). <a target="_blank" href="'.$response->Items->MoreSearchResultsUrl.'">See full search results</a></h4>';
   else
      $items .= '<h4>Total ' . $counts. ' item(s) found (Page '.$loadCount.'). <a target="_blank" href="'.$response->Items->MoreSearchResultsUrl.'">See full list</a></h4>';
   $items .= '<div style="overflow:auto; overflow-x: hidden; height:100%; width:100%; -moz-border-radius: 15px;">';
   if ($loadCount > 1)
      $items .= '<div id="container'.$loadCount.'" class="clearfix">';
   else
      $items .= '<div id="container" class="clearfix">';
	  
   foreach($response->Items->Item as $item){
	     if (!isset($item->SmallImage->URL)) continue;
	     $items .= '<div class="box col3"><div class="grid-item google_reader"><div class="grid-item-content">';
         $items .= '<a target="_blank" href="'.$item->DetailPageURL.'"><span class="itemtitle">'.$item->ItemAttributes->Title.'</span></a>';
		 // handle item image
		 $small_image_url = $item->SmallImage->URL;
		 $medium_image_url = $item->MediumImage->URL;
		 $large_image_url = $item->LargeImage->URL;
		 $items .= '<br/><a href="javascript:void(0);"><img onmouseover="imgpreloader(\''.$large_image_url.'\')" class="weiboimg" onclick="zC(\''.$large_image_url.'\')" src="'.$medium_image_url.'" width='.($item->MediumImage->Width->_).' height='.($item->MediumImage->Height->_).'/></a>';
		 
		 $items .= '<br/>List Price: <span class="pricetag listprice">'. $item->ItemAttributes->ListPrice->FormattedPrice .'</span>';
		 $items .= '<br/>Lowest Price: <span class="pricetag">'. $item->OfferSummary->LowestNewPrice->FormattedPrice .'</span>';
		 $items .= '<br/>Category: <a target="_blank" href="'.$item->DetailPageURL.'">'. $item->ItemAttributes->ProductGroup .'</a>';
		 $items .= '<br/><a target="_blank" href="'.$item->DetailPageURL.'"><img src="../images/buy_from_amazon_button.jpg" width="89px" height="26px"/></a>';
		 
		 $items .= '</div><div class="grid-item-meta">';
	     //$items .= '<a target="_blank" href=""><img class="grid-item-avatar" style="width:25px; height:25px;" src="'.$user->profile_image_url.'"></a>';
         $items .= '<a target="_blank" href="http://amazon.com"><img src="../images/amazon.png" class="grid-service-icon" alt="Amazon" style="width: 16px; height: 16px;"></a>';
	     $items .= 'Brand: <a target="_blank" href="">'.$item->ItemAttributes->Brand.'</a>';

		 $items .= '<br />Release Date: '.$item->ItemAttributes->ReleaseDate;
		 $items .= '<br /><iframe frameborder="0" seamless="seamless" scrolling="no" src="'.$item->CustomerReviews->IFrameURL.'" width="100%" height="230px"></iframe>';
         $items .= '</div></div></div>';
	   }     
     $items .= '</div></div></div>';  
     
	 // auto load control
	 if (($category == 'All' && $loadCount < 5) || ($category != 'All' && $loadCount < 10))
     {
     if (func_num_args() == 2)
	    {
	    //echo (urlencode($keyword));
	    $items .= '<span class="screw" rel="../include/friendstream.php?func=search_amazon_items&parms='.$category.','.urlencode($keyword).'"></span>';
		}
	 else
	    $items .= '<span class="screw" rel="../include/friendstream.php?func=search_amazon_items&parms='.$category.'"></span>';
	 }
	 else if (($category == 'All' && $loadCount == 5) || ($category != 'All' && $loadCount == 10))
	 {
     if (func_num_args() == 2)
	    $items .= '<div align="center"><h4>Total ' . $counts. ' item(s) found for: "'.$keyword.'" in "'.$category.'". <a target="_blank" href="'.$response->Items->MoreSearchResultsUrl.'">See full search results</a></h4></div>';
	 else
	    $items .= '<div align="center"><h4>Total ' . $counts. ' item(s) found. <a target="_blank" href="'.$response->Items->MoreSearchResultsUrl.'">See full list</a></h4></div>';
     }
	 
	 return $items;
}
   

// function used to get user recent Facebook Profile feed (Wall)
function get_fbupdates()
   {
   //ini_set('session.gc_maxlifetime', 3600);
   //session_cache_limiter ('private, must-revalidate'); 
   //session_cache_expire(60); // in minutes 
   session_start();
   $facebook = $_SESSION['facebook']; 
   
   //echo ("I am here22!\n");
   if (!$facebook)
      {
      $error .= '<h3>Session expired! Please refresh page to sign in again.</h3>';
	  return $error;
	  }
	   
   // call facebook graph API
   try {
	     //$movies = $facebook->api('/me/movies');
        $fbupdates = $facebook->api('/me/home');
        }
        catch(Exception $o){
            dump($o);
        }
	   
	   
   $updates = '<div id="fblist">';
   $updates .= '<h4>' .count($fbupdates["data"]). ' post(s) found. </h4>';
   $updates .= '<div style="overflow:auto; overflow-x: hidden; height:650px; width:100%; -moz-border-radius: 15px;">';
   $updates .= '<ul>';	
   
   // Create an array for caching user id and link/location mapping
   //$userLinkTable = array();
   //$userLocTable = array();
   
   //print_r($fbupdates["data"][0]);
   
   foreach ($fbupdates["data"] as $fbupdate){
	     /*   
	     if (!isset($userLinkTable[$fbupdate->{"from"}->{"id"}]))
	        {
			$user = $facebook->api('/'.$fbupdate->{"from"}->{"id"}.'?fields=location,link');
			$userLink = $user->{"link"};
			$userLocation = $user->{'location'}->{'name'};
			$userLinkTable[$fbupdate->{"from"}->{"id"}] = $userLink;
			$userLocTable[$fbupdate->{"from"}->{"id"}] = $userLocation;
			}
	     else
		    {
		    $userLink = $userLinkTable[$fbupdate->{"from"}->{"id"}];
			$userLocation = $userLocTable[$fbupdate->{"from"}->{"id"}];
			}
		 */
		 // use facebook profile link to get the profile 
		 $userLink = 'http://www.facebook.com/profile.php?id='.$fbupdate["from"]["id"];
			
	     // get the post url
		 list($userid, $postid) = split('_', $fbupdate["id"]);
		 
		 //$userLink = 'https://graph.facebook.com/'.$fbupdate->{"from"}->{"id"}.'?fields=link&access_token='.$facebook->getAccessToken();
	     $updates .= '<li><div class="grid-item flickr"><div class="grid-item-content">';
		 $streamit = 'FS:@'.$fbupdate["from"]["name"].': ';
		 
		 if ($fbupdate["icon"] != NULL)
		    $updates .= '<img class="grid-item-avatar" style="width:16px; height:16px;" src="'.$fbupdate["icon"].'">';
		 
		 // parse facebook wall post
		 if ($fbupdate["type"] == "photo" || $fbupdate["type"] == "checkin")
		    {
		    if ($fbupdate["picture"] != NULL)
			   $updates .= '<a target="_blank" href="'.$fbupdate["link"].'"><img class="grid-item-avatar" style="width:45px; height:45px;" src="'.$fbupdate["picture"].'"></a>';
			$updates .= '<span style="font-weight:bold">'.$fbupdate["name"].'</span>';
			if (isset($fbupdate["caption"]))
			   $updates .= '<br/>'.parse_twitter(htmlspecialchars($fbupdate["caption"]));
			else
			   $updates .= '<br/>';   
			if (isset($fbupdate["description"]))   
			   $updates .= '<br/>'.parse_twitter(htmlspecialchars($fbupdate["description"]));
			else
			   $updates .= '<br/>';
			if (isset($fbupdate["properties"]))
			   {
			   foreach ($fbupdate["properties"] as $property){
				  if (isset($property["href"]))
				     $updates .= '<br/><a target="_blank" href="'.$property["href"].'">'.$property["name"].': '.$property["text"].'</a>';
				  else
			         $updates .= '<br/>'.$property["name"].': '.$property["text"];
				  }
			   }
			$updates .= '<br/>';
			$streamit .= $fbupdate["name"].' '.$fbupdate["description"].' '.$fbupdate["link"];
			}
		 else if ($fbupdate["picture"] != NULL)
		    {
		    $updates .= '<a target="_blank" href="'.$fbupdate["link"].'"><img class="grid-item-avatar" style="width:45px; height:45px;" src="'.$fbupdate["picture"].'"></a>';
			if ($fbupdate["type"] != "link")
			   {
			   $updates .= '<span style="font-weight:bold">'.$fbupdate["name"].'</span><br/>'.parse_twitter($fbupdate["description"]).'<br/>';
			   $streamit .= $fbupdate["name"].' '.$fbupdate["description"].' '.$fbupdate["link"];
			   }
			}
			
	     if ($fbupdate["type"] == "question")
		    {
			$updates .= parse_twitter(htmlspecialchars($fbupdate["story"])).'<br/>';
			$streamit .= ' '.$fbupdate["story"];	
		    }
			
	     if (isset($fbupdate["message"]))
		    {
            $updates .= parse_twitter(htmlspecialchars($fbupdate["message"])).'<br/>';
			$streamit .= ' '.$fbupdate["message"];
			}
		 if (isset($fbupdate["place"]))
		    {
            $updates .= '<a target="_blank" href="'.$userLink.'">'.$fbupdate["from"]["name"].'</a> is at <a target="_blank" href="https://www.facebook.com/pages/'.$fbupdate["place"]["name"].'/'.$fbupdate["place"]["id"].'">'.$fbupdate["place"]["name"].'</a>.<br/>';
			$streamit .= ' '.$fbupdate["from"]["name"].' is at '.$fbupdate["place"]["name"];
			}	
		 if ($fbupdate["type"] == "link")
		    {
			$updates .= '<a target="_blank" href="'.$fbupdate["link"].'"><span style="font-weight:bold">'.$fbupdate["name"].'</span></a>';
			if (isset($fbupdate["caption"]))
			   $updates .= '<br/>'.parse_twitter(htmlspecialchars($fbupdate["caption"]));
			if (isset($fbupdate["description"]))   
			   $updates .= '<br/>'.parse_twitter(htmlspecialchars($fbupdate["description"]));
			if (isset($fbupdate["properties"]))
			   {
			   foreach ($fbupdate["properties"] as $property){
				  if (isset($property["href"]))
				     $updates .= '<br/><a target="_blank" href="'.$property["href"].'">'.$property["name"].': '.$property["text"].'</a>';
				  else
			         $updates .= '<br/>'.$property["name"].': '.$property["text"];
				  }
			   }
			$updates .= '<br/>'; 
			$streamit .= $fbupdate["name"].' '.$fbupdate["description"].' '.$fbupdate["link"];
			}
			
		 $updates .= '<br/><div style="display:inline"><img src="../images/commentfb.PNG" alt="comment" style="width: 16px; height: 16px;"/><a target="_blank" href="http://www.facebook.com/'.$userid.'/posts/'.$postid.'">Comment('.($fbupdate["comments"]["count"]?$fbupdate["comments"]["count"]:0).')</a> | ';
		 $updates .= '<img src="../images/likefb.PNG" alt="like" style="width: 16px; height: 16px;"/><a target="_blank" href="http://www.facebook.com/'.$userid.'/posts/'.$postid.'">Like('.($fbupdate["likes"]["count"]?$fbupdate["likes"]["count"]:0).')</a></div>';
		 
		 // streamIt	 
	     $updates .= '<div align="right" style="font-size:90%"><img src="../images/fsicon2.png" alt="friendstream" style="width:13px; height:13px;"/><a href="javascript:void(0);" onclick="getShortUrl(\''.htmlspecialchars(addcslashes($streamit, "\n\r\'\""), ENT_QUOTES).'\')">StreamIt</a></div>';
	 
	     $updates .= '</div><div class="grid-item-meta">';
	     $updates .= '<a target="_blank" href="'.$userLink.'"><img class="grid-item-avatar" style="width:25px; height:25px;" src="http://graph.facebook.com/'.$fbupdate["from"]["id"].'/picture"></a>';
         $updates .= '<a target="_blank" href="http://www.facebook.com"><img src="../images/facebook.gif" class="grid-service-icon" alt="Facebook"  style="width: 16px; height: 16px;"></a>';
	     $updates .= 'Posted by <a target="_blank" href="'.$userLink.'">'.$fbupdate["from"]["name"].'</a> via '.($fbupdate['attribution'] ? $fbupdate['attribution'] : 'facebook');
		 //if ($userLocation != NULL)
		   // $updates .= ' from '.$userLocation;
		 
		 $updates .='<br /><span class="grid-item-date">'.$fbupdate["created_time"].'</span>';
		 $updates .= '</div></div></li>';
					
   }
   $updates .= '</ul></div></div>';
      
   return $updates;	   
   }   
   
   
// function used to parse twitter tweets
function parse_twitter($t) 
  {
	    // link URLs
		$t = " ".preg_replace( "/(([htps]+:\/\/)|www\.)([^[:space:]]*)".
	        "([[:alnum:]#?\/&=])/i", "<a href=\"\\1\\3\\4\" target=\"_blank\">".
	        "\\1\\3\\4</a>", $t);
		
	    /*$t = " ".preg_replace( "/(([[:alnum:]]+:\/\/)|www\.)([^[:space:]]*)".
	        "([[:alnum:]#?\/&=])/i", "<a href=\"\\1\\3\\4\" target=\"_blank\">".
	        "\\1\\3\\4</a>", $t);*/
	 
	    // link mailtos
	    $t = preg_replace( "/(([a-z0-9_]|\\-|\\.)+@([^[:space:]]*)".
	        "([[:alnum:]-]))/i", "<a href=\"mailto:\\1\">\\1</a>", $t);
	 
	    //link twitter users
	    $t = preg_replace( "/ *@([a-z0-9_]*) ?/i", " <a href=\"http://twitter.com/\\1\" target=\"_blank\">@\\1</a> ", $t);
	 
	    //link twitter arguments
	    $t = preg_replace( "/ *#([a-z0-9_]*) ?/i", " <a href=\"http://twitter.com/search?q=%23\\1\" target=\"_blank\">#\\1</a> ", $t);
	 
	    // truncates long urls that can cause display problems (optional)
	    $t = preg_replace("/>(([[:alnum:]]+:\/\/)|www\.)([^[:space:]]".
	        "{30,40})([^[:space:]]*)([^[:space:]]{10,20})([[:alnum:]#?\/&=])".
	        "</", ">\\3...\\5\\6<", $t);
	    return trim($t);
   }   
      
   
?>