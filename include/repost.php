<?php
require_once ('twitterapi/config.php');
require_once ('twitterapi/twitter.lib.php');


ini_set('session.gc_maxlifetime', 3600);
session_cache_limiter ('private, must-revalidate');
session_cache_expire(60); // in minutes 
session_start();

$repostTwitter = isset($_GET['twitter']) ? 1 : 0;
//$repostFB = isset($_GET['facebook']) ? 1 : 0;
//$repostBuzz = isset($_GET['buzz']) ? 1 : 0;
//$repostWeibo = isset($_GET['weibo']) ? 1 : 0;
$repostComment = isset($_GET['comment']) ? 1 : 0;
$repostCommentOri = isset($_GET['oricomment']) ? 1 : 0;
$repostZhuanfa = isset($_GET['zhuanfa']) ? 1 : 0;
//$repostLike = isset($_GET['like']) ? 1 : 0;
//$repostFavor = isset($_GET['favorite']) ? 1 : 0;
$repostRetweet = isset($_GET['retweet']) ? 1 : 0;
$repostReply = isset($_GET['reply']) ? 1 : 0;

$sid = isset($_GET['sid']) ? $_GET['sid'] : 0;
$sidori = isset($_GET['oricomment']) ? $_GET['oricomment'] : 0;

$repostText = trim($_GET['reposttext']);

// strip "/"
if (get_magic_quotes_gpc())
   $repostText = stripslashes($repostText);

// check for Twitter
if ($repostTwitter)
   {
   $twitter = $_SESSION['twitter'];
   
   if (!$twitter)
      {
      echo 'Session expired! Please refresh page to sign in again!';
	  return;
	  }
	  
  if ($sid == 0)
      {
      echo 'Error: missing Twitter tweet ID!';
	  return;
	  }
	  
   // handle twitter actions
   try {
	   // action retweet
	   if ($repostRetweet)
	      {
		  $rtstring = $twitter->retweetStatus($sid);
		  //echo $rtstring.$sid;
		  }
       // also reply
       else if ($repostReply)
          {
		  $parameters = array('status' => $repostText, 'in_reply_to_status_id' => $sid);
	      //$rtstring = $twitter->post('statuses/update', $parameters);
		  $rtstring = $twitter->updateStatus($repostText, $sid);
		  //echo $rtstring;
	      }
	   
       } catch (Exception $e) {
         print_r($e);
       }
   }
   

?>
