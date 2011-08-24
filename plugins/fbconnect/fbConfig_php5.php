<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */

function fb_get_loggedin_user() {
	try{
		$fbclient = & facebook_client();
		if ($fbclient)
			return $fbclient->get_loggedin_user();
	}catch (FacebookRestClientException $e) {
		//echo "Facebook connect error:".$e->getCode();
	}
	return null;
}

function fb_user_getInfo($fb_user) {
	try{
		$fbclient = & facebook_client();
		if ($fbclient){
			$fbapi_client = & $fbclient->api_client;
			$userinfo = $fbapi_client->users_getInfo($fb_user, "about_me,profile_url,first_name,last_name,birthday,current_location,locale,sex,pic,pic_with_logo,pic_small,pic_small_with_logo,pic_big_with_logo,pic_big,pic_square,pic_square_with_logo,affiliations,email_hashes,hometown_location,hs_info,education_history,interests,meeting_for,meeting_sex,movies,music,political,profile_update_time,proxied_email,quotes,relationship_status,religion,significant_other_id,timezone,tv,work_history");
			if (isset($userinfo[0])){
				return $userinfo[0];
			}else{
				return $userinfo;
			}
		}
	}catch (FacebookRestClientException $e) {
		//echo "Facebook connect error:".$e->getCode();
	}
	return null;
}

function fb_feed_getRegisteredTemplateBundles() {
	try{
		$fbclient = & facebook_client();
		if ($fbclient){
			$fbapi_client = & $fbclient->api_client;
			return $fbapi_client->feed_getRegisteredTemplateBundles();
		}
	}catch (FacebookRestClientException $e) {
		//echo "Facebook connect error:".$e->getCode();
	}
	return null;
}

function fb_feed_registerTemplateBundle($one_line_stories,$short_stories,$full_stories){
	try{
		$fbclient = & facebook_client();
		if ($fbclient){
			$fbapi_client = & $fbclient->api_client;
			return $fbapi_client->feed_registerTemplateBundle($one_line_stories,$short_stories,$full_stories);
		}
	}catch (FacebookRestClientException $e) {
		//echo "Facebook connect error:".$e->getCode();
	}
	return null;
}

function fb_feed_deactivateTemplateBundleByID($templateID){
	 try{
		$fbclient = & facebook_client();
		if ($fbclient){		
			$fbapi_client = & $fbclient->api_client;
			$fbapi_client->feed_deactivateTemplateBundleByID($templateID);
		}
	}catch (FacebookRestClientException $e) {
		//echo "Facebook connect error:".$e->getCode();
	}
	return null;	
}

function fb_feed_getRegisteredTemplateBundleByID($templateID){
	 try{
		$fbclient = & facebook_client();
		if ($fbclient){		
			$fbapi_client = & $fbclient->api_client;
			return $fbapi_client->feed_getRegisteredTemplateBundleByID($templateID);
		}
	}catch (FacebookRestClientException $e) {
		//echo "Facebook connect error:".$e->getCode();
	}
	return null;
}

function fb_fql_query($query){
  	try{
		$fbclient = & facebook_client();
		if ($fbclient){		
			$fbapi_client = & $fbclient->api_client;
			return $fbapi_client->fql_query($query);
		}
	}catch (FacebookRestClientException $e) {
		//echo "Facebook connect error:".$e->getCode();
	}
	return null;
}
function fb_expire_session(){
	try {
	
		$fbclient = & facebook_client();
	    if ($fbclient && $fbclient->get_loggedin_user()!="") {
			$fbclient->expire_session();
		}
	}catch (Exception $e) {
	// nothing, probably an expired session
	}
}

function fb_feed_publishUserAction($template_data){
	try {
		$fbclient = & facebook_client();
		if ($fbclient){		
			$fbapi_client = & $fbclient->api_client;
			$feed_bundle_id = get_option('fb_templates_id');
			$fbapi_client->feed_publishUserAction( $feed_bundle_id, 
	                                           json_encode($template_data) , 
	                                           null, 
	                                          null,2);
		}
	}catch (Exception $e) {
	// nothing, probably an expired session
	}
}

function fb_events_get($uid=null, $eids=null, $start_time=null, $end_time=null, $rsvp_status=null){
	try {
		$fbclient = & facebook_client();
		if ($fbclient){		
			$fbapi_client = & $fbclient->api_client;
			return $fbapi_client->events_get( $uid, $eids, $start_time, $end_time, $rsvp_status);
		}
	}catch (Exception $e) {
	// nothing, probably an expired session
	}
}

function fb_photos_getAlbums($uid=null, $aids=null) {
	try {
		$fbclient = & facebook_client();
		if ($fbclient){		
			$fbapi_client = & $fbclient->api_client;
			return $fbapi_client->photos_getAlbums($uid, $aids);
		}
	}catch (Exception $e) {
	// nothing, probably an expired session
	}

}

function fb_photos_get($subj_id=null, $aid=null, $pids=null){
	try {
		$fbclient = & facebook_client();
		if ($fbclient){		
			$fbapi_client = & $fbclient->api_client;
			return $fbapi_client->photos_get($subj_id, $aid, $pids);
		}
	}catch (Exception $e) {
		print_r($e);
		echo "ERROR";
	// nothing, probably an expired session
	}
}		

function fb_users_getStandardInfo($uids=null, $fields=null) {
	try {
		$fbclient = & facebook_client();
		if ($fbclient){		
			$fbapi_client = & $fbclient->api_client;
			return $fbapi_client->users_getStandardInfo($uids, $fields);
		}
	}catch (Exception $e) {
		print_r($e);
		echo "ERROR";
	// nothing, probably an expired session
	}

}

function fb_showFeedDialog(){
		$template_data = $_SESSION["template_data"];
		if (isset($template_data) && $template_data!=""){
				echo "<script type='text/javascript'>\n";
				//echo "jQuery(window).ready(function() {\n";  NO FUNCIONA COMO EL ONLOAD
				echo "window.onload = function() {\n";
					echo "FB.ensureInit(function(){\n";
					echo "	  FB.Connect.showFeedDialog(".get_option('fb_templates_id').", ".json_encode($template_data).", null, null, FB.FeedStorySize.full , FB.RequireConnect.promptConnect);";
					echo "});\n";
				echo "   };\n";
				//echo "   });\n";
				echo "	</script>";
				$_SESSION["template_data"] = "";
		}
		

}

function fb_hash($email) {
      $normalizedAddress = trim(strtolower($email));
      //crc32 outputs signed int
      $crc = crc32($normalizedAddress);
      //output in unsigned int format
      $unsignedCrc = sprintf('%u', $crc);
      $md5 = md5($normalizedAddress);
      return "{$unsignedCrc}_{$md5}";
}
	
function fb_connect_registerUsers($accounts=null){
	try {
		$fbclient = & facebook_client();
		if ($fbclient){		
			$fbapi_client = & $fbclient->api_client;
			return $fbapi_client->connect_registerUsers(json_encode($accounts));
		}
	}catch (Exception $e) {
		print_r($e);
		echo "ERROR";
	// nothing, probably an expired session
	}	
}													  