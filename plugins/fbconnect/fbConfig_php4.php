<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */

function fb_get_loggedin_user() {
	$fbclient = & facebook_client();
	if ($fbclient)
		return $fbclient->get_loggedin_user();
	return null;
}

function fb_user_getInfo($fb_user) {
	$fbclient = & facebook_client();
	if ($fbclient){
		$fbapi_client = & $fbclient->api_client;
		$userinfo = $fbapi_client->users_getInfo($fb_user, "about_me,profile_url,first_name,last_name,birthday,current_location,sex,pic,pic_small,pic_big,pic_square");
		//print_r($userinfo);
		if (isset($userinfo["users_getInfo_response"]) && isset($userinfo["users_getInfo_response"]["user"]))
			return $userinfo["users_getInfo_response"]["user"];
		else
			return $userinfo;
	}
	return null;
}

function fb_feed_getRegisteredTemplateBundles() {
	$fbclient = & facebook_client();
	if ($fbclient){
		$fbapi_client = & $fbclient->api_client;
		$templates = $fbapi_client->feed_getRegisteredTemplateBundles();
	
		if (isset($templates) && isset($templates["feed_getRegisteredTemplateBundles_response"]) && isset($templates["feed_getRegisteredTemplateBundles_response"]["template_bundle"])){
		 $templates = $templates["feed_getRegisteredTemplateBundles_response"]["template_bundle"];
		 if (!isset($templates[0])){
		 	$newtemplates = array();
			$newtemplates[] = $templates;
			$templates = $newtemplates;
		 }
		 foreach ($templates as $key=>$template){ //PHP5 structure
		 	if (isset($template["one_line_story_templates"]) && isset($template["one_line_story_templates"]["one_line_story_template"])){
		 		$templates[$key]["one_line_story_templates"][]=$template["one_line_story_templates"]["one_line_story_template"];
		 	}
			if(isset($template["short_story_templates"]) && isset($template["short_story_templates"]["short_story_template"])){
		 		$templates[$key]["short_story_templates"][]=$template["short_story_templates"]["short_story_template"];
		 	}
		 }
		 return $templates;
		}else{
		 return array();
		}
	}
	return null;
}

function fb_feed_registerTemplateBundle($one_line_stories,$short_stories,$full_stories){
	$fbclient = & facebook_client();
	if ($fbclient){
		$fbapi_client = & $fbclient->api_client;
		$response = $fbapi_client->feed_registerTemplateBundle($one_line_stories,$short_stories,$full_stories);
		if (isset($response["feed_registerTemplateBundle_response"]))
			return $response["feed_registerTemplateBundle_response"];
		else
			return $response;
	}
	return null;
}

function fb_feed_deactivateTemplateBundleByID($templateID){
	$fbclient = & facebook_client();
	if ($fbclient){		
		$fbapi_client = & $fbclient->api_client;
		$fbapi_client->feed_deactivateTemplateBundleByID($templateID);
	}
	return null;	
}

function fb_feed_getRegisteredTemplateBundleByID($templateID){
	$fbclient = & facebook_client();
	if ($fbclient){		
		$fbapi_client = & $fbclient->api_client;
		return $fbapi_client->feed_getRegisteredTemplateBundleByID($templateID);
	}
	return null;
}

function fb_fql_query($query){
	$fbclient = & facebook_client();
	if ($fbclient){		
		$fbapi_client = & $fbclient->api_client;
		$response = $fbapi_client->fql_query($query);
		if (isset($response["fql_query_response"]) && isset($response["fql_query_response"]["user"])){
			if (isset($response["fql_query_response"]["user"][0])){
				return $response["fql_query_response"]["user"];
			}else{
				$newresp = array();
				$newresp[] = $response["fql_query_response"]["user"];
			}
		}
		return $response;
	}
	return null;
}
function fb_expire_session(){
	$fbclient = & facebook_client();
    if ($fbclient && $fbclient->get_loggedin_user()!="") {
		$fbclient->expire_session();
	}
}

function fb_feed_publishUserAction($template_data){
	$fbclient = & facebook_client();
	if ($fbclient){		
		$fbapi_client = & $fbclient->api_client;
		$feed_bundle_id = get_option('fb_templates_id');
		$fbapi_client->feed_publishUserAction( $feed_bundle_id, 
                                           $template_data , 
                                           null, 
                                          null,2);
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

//REYES
function json_encode($data){
	require_once 'facebook-client4/classes/JSON.php';
	$json = new Services_JSON();
	return $json->encode($data);
}													  