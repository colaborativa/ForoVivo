<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */
if (!class_exists('WPfbConnect_Logic')):

/**
 * Basic logic for wp-fbConnect plugin.
 */
class WPfbConnect_Logic {

	function get_connected_friends($fb_uid) {
	  $results = array();
	  $query = 'SELECT uid, email_hashes, has_added_app FROM user WHERE has_added_app = 1 AND uid IN '.
	    '(SELECT uid2 FROM friend WHERE uid1 = '.$fb_uid.')';
	  $rows = fb_fql_query($query);
	    
	    // Do filtering in PHP because the FQL doesn't allow it (yet)
	    if ($rows!=null && !empty($rows)) {
	      foreach ($rows as $row) {
	        if ((is_array($row['email_hashes']) && count($row['email_hashes']) > 0) ||
	            ($row['has_added_app'] == 1)) {
	          unset($row['has_added_app']);
	          $results[] = $row;
	        }
	      }
	    }
	
	  return $results;
	}

	/**
	 * Soft verification of plugin activation
	 *
	 * @return boolean if the plugin is okay
	 */
	function uptodate() {
		global $fbconnect;

		if( get_option('fb_db_revision') != FBCONNECT_DB_REVISION ) {
			$fbconnect->enabled = false;
			update_option('fb_plugin_enabled', false);
			return false;
		}
		$fbconnect->enabled = (get_option('fb_plugin_enabled') == true );
		return $fbconnect->enabled;
	}


	/**
	 * Get the internal SQL Store.  If it is not already initialized, do so.
	 *
	 * @return WPfbConnect_Store internal SQL store
	 */
	function getStore() {
		global $fbconnect;

		if (!isset($fbconnect->store)) {
			set_include_path( dirname(__FILE__) . PATH_SEPARATOR . get_include_path() );
			require_once 'fbConnectStore.php';

			$fbconnect->store = new WPfbConnect_Store($fbconnect);
			if (null === $fbconnect->store) {
				$fbconnect->enabled = false;
			}
		}

		return $fbconnect->store;
	}




	/**
	 * Called on plugin activation.
	 *
	 * @see register_activation_hook
	 */
	function activate_plugin() {
		global $fbconnect;
		//fbconnect_init();

		$store =& WPfbConnect_Logic::getStore();
		$store->create_tables();

	}



	/**
	 * Called on plugin deactivation.  Cleanup tables.
	 *
	 * @see register_deactivation_hook
	 */
	function deactivate_plugin() {
	}


	/**
	 * Facebook connect Login 
	 */
	function wp_login_fbconnect() {
		
		global $wp_version;
		$self = basename( $GLOBALS['pagenow'] );
		
		$fb_user = fb_get_loggedin_user();
		
		$user = wp_get_current_user();

		if ( $fb_user && (!is_user_logged_in() || $user->fbconnect_userid != $fb_user)) { //Intenta hacer login estando registrado en facebook
			require_once(ABSPATH . WPINC . '/registration.php');
			$usersinfo = fb_user_getInfo($fb_user);

			$wpid = "";
			$fbwpuser = WPfbConnect_Logic::get_userbyFBID($fb_user);
			$wpid = "";
			if (!is_user_logged_in() && !$fbwpuser){
				$userdata = get_userdatabylogin( "FB_".$fb_user );	
				if((!$userdata || $userdata=="") && isset($usersinfo) && $usersinfo!=""){
					$user_data = array();
					$user_data['user_login'] = "FB_".$fb_user;
					$user_data['user_pass'] = substr( md5( uniqid( microtime() ).$_SERVER["REMOTE_ADDR"] ), 0, 15);
					$user_data['user_nicename'] = $usersinfo["first_name"]." ".$usersinfo["last_name"];
					$user_data['display_name'] = $usersinfo["first_name"]." ".$usersinfo["last_name"];
					$user_data['user_url'] = $usersinfo["profile_url"];
					$wpid = wp_insert_user($user_data);
					update_usermeta( $wpid, "first_name", $usersinfo["first_name"] );
					update_usermeta( $wpid, "last_name", $usersinfo["last_name"] );
					//update_usermeta( $wpid, "description", $usersinfo["about_me"] );
					//update_usermeta( $wpid, "birthday", $usersinfo["birthday"] );
					//update_usermeta( $wpid, "current_location", $usersinfo["current_location"] );
					//update_usermeta( $wpid, "sex", $usersinfo["sex"] );
					WPfbConnect_Logic::set_userid_fbconnect($wpid,$fb_user);
				}else{
					WPfbConnect_Logic::set_userid_fbconnect($userdata->ID,$fb_user);
					$wpid = $userdata->ID;
				}
			}elseif(is_user_logged_in() && $fbwpuser && $user->ID != $fbwpuser->ID){ // El usuario FB está asociado a un usaurio WP distinto al logeado
				WPfbConnect_Logic::set_userid_fbconnect($user->ID,$fb_user);
				WPfbConnect_Logic::set_userid_fbconnect($fbwpuser->ID,"0"); //Eliminamos la asociación anterior
				$wpid = $user->ID;
			}elseif(is_user_logged_in() && $user->fbconnect_userid != $fb_user){ // El usuario WP no está asociado al de FB
				WPfbConnect_Logic::set_userid_fbconnect($user->ID,$fb_user);
				$wpid = $user->ID;
			}else{
				$wpid = $user->ID;
			}

			//$userdata = get_userdatabylogin( "FB_".$fb_user );
			$userdata = WPfbConnect_Logic::get_userbyFBID($fb_user);
			WPfbConnect_Logic::set_lastlogin_fbconnect($userdata->ID);
			global $current_user;
//			print_r($current_user);
//			print_r($userdata);
			$current_user = null;
			
			WPfbConnect_Logic::fb_set_current_user($userdata);
			global $userdata;
			if (isset($userdata) && $userdata!="")
				$userdata->fbconnect_userid = $fb_user;
			//wp_set_auth_cookie($userdata->ID, false);

		}
	}


	function fb_set_current_user($userdata, $remember = true) {

		$user = set_current_user($userdata->ID);
			
		if (function_exists('wp_set_auth_cookie')) {
			wp_set_auth_cookie($userdata->ID, $remember);
		} else {
			wp_setcookie($userdata->user_login, md5($userdata->user_pass), true, '', '', $remember);
		}

		//do_action('wp_login', $user->user_login);
	}


	//REYES
	function fb_logout(){
		fb_expire_session();
	}
	

	function comment_fbconnect($comment_ID) {
		global $fbconnect;
//REYES
		$comment = WPfbConnect_Logic::get_comment_byID($comment_ID);
		$fb_user = fb_get_loggedin_user();
			
			if (is_user_logged_in() && $fb_user){
		
				WPfbConnect_Logic::set_comment_fbconnect($comment_ID,$fb_user);

				if ($_REQUEST["sendToFacebook"]){
					$blogname = get_option('blogname');
					$blogdesc = get_option('blogdescription');
					$siteurl = get_option('siteurl');
				
					$url = get_permalink($comment->comment_post_ID);

			  		$template_data = array('post_title' => '<a href="'.$url.'">'.$comment->post_title.'</a>',
			                         'body' => apply_filters( 'comment_text', $comment->comment_content),
			                         'body_short' => substr(strip_tags(apply_filters( 'comment_text', $comment->comment_content)),0,255),
			                         'post_permalink' => $url,
						 'blogname' => '<a href="'.$siteurl.'">'.$blogname.'</a>',
						 'blogdesc' => $blogdesc,
						 'siteurl' => $siteurl);
					$_SESSION["template_data"]= $template_data;
					//fb_feed_publishUserAction($template_data);
				}
			}

	}


	/**
	 * Mark the provided comment as an Facebook Connect comment
	 *
	 */
	function set_comment_fbconnect($comment_ID,$fb_user = 0) {
		global $wpdb, $fbconnect;

		$comments_table = WPfbConnect::comments_table_name();
		$wpdb->query("UPDATE $comments_table SET fbconnect='".$fb_user."' WHERE comment_ID='$comment_ID' LIMIT 1");
	}

	/**
	 * Update last user login date
	 *
	 */
	function set_lastlogin_fbconnect($userID) {
		global $wpdb, $fbconnect;
		$users_table = WPfbConnect::users_table_name();
		$wpdb->query("UPDATE $users_table SET fbconnect_lastlogin=".date("U")." WHERE ID='$userID' LIMIT 1");
	}
	
	/**
	 * Get last users
	 *
	 */
	function get_lastusers_fbconnect($num = 10,$start=0) {
		global $wpdb, $fbconnect;
		$users_table = WPfbConnect::users_table_name();
		$users = $wpdb->get_results("SELECT * FROM $users_table ORDER BY fbconnect_lastlogin DESC LIMIT ".$start.",".$num);
		return $users;
	}

	/**
	 * User count
	 *
	 */
	function get_count_users() {
		global $wpdb, $fbconnect;
		$users_table = WPfbConnect::users_table_name();
		$users = $wpdb->get_results("SELECT count(ID) as userscount FROM $users_table");
		if (count($users)>0){
			return $users[0]->userscount;
		}else{
			return null;
		}
	}
		
	/**
	 * Get user by fbid
	 *
	 */
	function get_userbyFBID($fbid) {
		global $wpdb, $fbconnect;
		$users_table = WPfbConnect::users_table_name();
		$users = $wpdb->get_results("SELECT * FROM $users_table WHERE fbconnect_userid = ".$fbid);
		if (count($users)>0){
			return $users[0];
		}else{
			return null;
		}
	}
	
	/**
	 * Update Facebook userID
	 *
	 */
	function set_userid_fbconnect($userID,$fbuserid) {
		global $wpdb, $fbconnect;
		$users_table = WPfbConnect::users_table_name();
		$wpdb->query("UPDATE $users_table SET fbconnect_userid=".$fbuserid." WHERE ID='$userID' LIMIT 1");
	}

	/**
	 * Get community comments
	 *
	 */
	function get_community_comments($limit=10) {
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments wpcomments, $wpdb->posts posts WHERE wpcomments.comment_post_ID=posts.ID AND wpcomments.comment_approved = '1' ORDER BY comment_date DESC LIMIT ".$limit));
	}
		
	/**
	 * Get user comments
	 *
	 */
	function get_user_comments($user_id,$limit=25) {
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments wpcomments, $wpdb->posts posts WHERE wpcomments.comment_post_ID=posts.ID AND wpcomments.fbconnect = %s AND wpcomments.comment_approved = '1' ORDER BY comment_date DESC LIMIT ".$limit, $user_id));
	}
	
	/**
	 * Get user comments
	 *
	 */
	function get_user_comments_byID($user_id,$limit=25) {
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments wpcomments, $wpdb->posts posts WHERE wpcomments.comment_post_ID=posts.ID AND wpcomments.user_id = %s AND wpcomments.comment_approved = '1' ORDER BY comment_date DESC LIMIT ".$limit, $user_id));
	}
	
	/**
	 * Get a comment by ID
	 *
	 */
	function get_comment_byID($comment_id) {
		global $wpdb;
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments wpcomments, $wpdb->posts posts WHERE wpcomments.comment_post_ID=posts.ID AND wpcomments.comment_ID = %s", $comment_id));
		if ($comments != ""){
			return $comments[0];			
		}
	}

	/**
	 * This filter callback simply approves all Facebook Connect comments
	 *
	 * @param string $approved comment approval status
	 * @return string new comment approval status
	 */
	function comment_approval($approved) {
		$fb_user = fb_get_loggedin_user();
		if ($fb_user) {
			return 1;
		}else{
			return $approved;
		}
	}

	function html_namespace($html_lang){
		return "xmlns:fb=\"http://www.facebook.com/2008/fbml\" ".$html_lang;
	}

	function fb_get_avatar($avatar=null, $id_or_email = null, $size = null, $default=null){
		$fbuser = "";
		$username = "";
		if ( is_numeric($id_or_email) ) {
			$id = (int) $id_or_email;
			$user = get_userdata($id);
			$username = $user->display_name;
			if ( $user && isset($user->fbconnect_userid) && $user->fbconnect_userid!=""){
					$fbuser = $user->fbconnect_userid;
			}
		} elseif ( is_object($id_or_email) ) {
			if ( !empty($id_or_email->fbconnect) && $id_or_email->fbconnect!="0" ) {
				$id = (int) $id_or_email->user_id;
				$fbuser = $id_or_email->fbconnect;
			}else if ( !empty($id_or_email->user_id) ) {
				$id = (int) $id_or_email->user_id;
				$user = get_userdata($id);	
				$username = $user->display_name;
				if ( $user && isset($user->fbconnect_userid) && $user->fbconnect_userid!=""){
					$fbuser = $user->fbconnect_userid;
				}
			}
		}else{
			global $comment;
			if (isset($comment) && $comment!="" && !empty($comment->fbconnect) && $comment->fbconnect!="0"){
				$fbuser = $comment->fbconnect;
				$id=$comment->user_id;
			}
		}
		if ($fbuser != "" && $fbuser != 0){
			return "<span class=\"avatar\"><a onclick=\"location.href='".get_option('siteurl')."/?fbconnect_action=myhome&amp;userid=".$id."';\" href=\"".get_option('siteurl')."/?fbconnect_action=myhome&amp;userid=".$id."\"><fb:profile-pic uid=\"".$fbuser."\" size=\"square\" linked=\"false\"></fb:profile-pic></a></span>";
		}elseif($id!=""){
			return "<a href=\"".get_option('siteurl')."/?fbconnect_action=myhome&amp;userid=".$id."\">".$avatar."</a>";
		}

		return $avatar;
	}



	function set_error($error) {
		$_SESSION['fb_error'] = $error;
		return;
	}
	
	function get_user(){
		$fb_user ="";
		$wpuser ="";
		if (isset($_REQUEST['userid']) && $_REQUEST['userid']!=""){
			$wpuser = $_REQUEST['userid'];
		}elseif (isset($_REQUEST['fbuserid']) && $_REQUEST['fbuserid']!=""){
			$fb_user = $_REQUEST['fbuserid'];
		}else{
			$fb_user = fb_get_loggedin_user();
		}

		if ($fb_user!=""){
			$userprofile = WPfbConnect_Logic::get_userbyFBID($fb_user);
		}elseif($wpuser){
			$userprofile= get_userdata($wpuser);
		}
		return $userprofile;
	}
	
	function fbconnect_init_scripts(){
		if (get_option('fb_use_ssl')){
			echo "<script src='".get_ssl_root()."/js/api_lib/v0.4/FeatureLoader.js.php' type='text/javascript'></script>\n";
		}else{
			echo "<script src='".get_static_root()."/js/api_lib/v0.4/FeatureLoader.js.php' type='text/javascript'></script>\n";
		}
		echo "<script type='text/javascript'>\n";
		echo "FB_RequireFeatures([\"XFBML\"], function(){\n";
		if (get_option('fb_use_ssl')){
	    		echo "FB.Facebook.init(\"".get_api_key()."\", \"".FBCONNECT_PLUGIN_URL. "/xd_receiver_ssl.htm\");\n";
	    	}else{
	    		echo "FB.Facebook.init(\"".get_api_key()."\", \"".FBCONNECT_PLUGIN_URL. "/xd_receiver.htm\");\n";
	    	}
		$fb_user = fb_get_loggedin_user();
		$uri = "";
		if (isset($_SERVER["REQUEST_URI"])){
			$uri = $_SERVER["REQUEST_URI"];			
		}
			
/*		if (!$fb_user){
			echo "	    FB.Facebook.get_sessionState().waitUntilReady(function(session){\n";
			echo "      var is_now_logged_into_facebook = session ? true : false;\n";
			echo "      alert(\">\"+ is_now_logged_into_facebook);\n" ;
			echo "		if (is_now_logged_into_facebook){\n";
			echo "  		window.location = \"".$uri."\";}\n";
		  	echo "});\n";
		}*/
	    echo "});\n";
	   	echo "</script>\n";
		fb_showFeedDialog();
	}

//Replace user comments url with profile url
function get_comment_author_url($url){
	global $comment;
	if ($comment->user_id!="" && $comment->user_id!="0"){
		return get_option('siteurl')."/?fbconnect_action=myhome&amp;userid=".$comment->user_id;
	}
	return $url;
}

} 
endif; // end if-class-exists test

?>
