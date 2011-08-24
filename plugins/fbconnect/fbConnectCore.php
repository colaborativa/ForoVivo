<?php
/*
 Plugin Name: Facebook Connector
 Plugin URI: http://www.sociable.es/facebook-connect
 Description: Allows the use of Facebook Connect for account registration, authentication, and commenting. 
 Author: Javier Reyes
 Author URI: http://www.sociable.es/
 Version: 1.2.1
 License: GPL (http://www.fsf.org/licensing/licenses/info/GPLv2.html) 
 */

define ( 'FBCONNECT_PLUGIN_REVISION', preg_replace( '/\$Rev: (.+) \$/', 'svn-\\1',
	'$Rev: 62 $') ); 

define ( 'FBCONNECT_DB_REVISION', 5);


define ( 'FBCONNECT_LOG_LEVEL', 'warning');     

set_include_path( dirname(__FILE__) . PATH_SEPARATOR . get_include_path() );   

require_once('fbConnectLogic.php');
require_once('fbConnectInterface.php');


restore_include_path();

@session_start();

if (! defined('WP_CONTENT_DIR'))
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');

if (! defined('WP_CONTENT_URL'))
    define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');

if (! defined('WP_PLUGIN_DIR'))
    define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');

if (! defined('WP_PLUGIN_URL'))
    define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');

if (isset($_REQUEST["fb_facebookapp_mode"])){
	$_SESSION["fb_facebookapp_mode"]= $_REQUEST["fb_facebookapp_mode"];
}


if ((isset($_SESSION["fb_facebookapp_mode"]) && $_SESSION["fb_facebookapp_mode"]=="on") || isset($_REQUEST["fb_sig_user"]) || isset($_REQUEST["fb_sig_in_profile_tab"])){
	define ('FBCONNECT_CANVAS', true);
}else{
	define ('FBCONNECT_CANVAS', false);
}

define ('FBCONNECT_PLUGIN_BASENAME', plugin_basename(dirname(__FILE__)));
define ('FBCONNECT_PLUGIN_PATH', WP_PLUGIN_DIR."/".FBCONNECT_PLUGIN_BASENAME);
define ('FBCONNECT_PLUGIN_URL', WP_PLUGIN_URL."/".FBCONNECT_PLUGIN_BASENAME);
define ('FBCONNECT_PLUGIN_LANG', FBCONNECT_PLUGIN_BASENAME."/lang");

//wp_enqueue_script('jquery'); 

if  (!class_exists('WPfbConnect')):
class WPfbConnect {
	var $store;
	var $consumer;

	var $log;
	var $status = array();

	var $message;	  // Message to be displayed to the user.
	var $action;	  // Internal action tag. 'success', 'warning', 'error', 'redirect'.

	var $response;

	var $enabled = true;

	var $bind_done = false;

	
	function WPfbConnect() {
		$this->log = &Log::singleton('error_log', PEAR_LOG_TYPE_SYSTEM, 'FBCONNECT');

		// Set the log level
		$fbconnect_log_level = constant('PEAR_LOG_' . strtoupper(FBCONNECT_LOG_LEVEL));
		$this->log->setMask(Log::UPTO($fbconnect_log_level));
	}


	/**
	 * Set Status.
	 **/
	function setStatus($slug, $state, $message) {
		$this->status[$slug] = array('state'=>$state,'message'=>$message);
	}


	function textdomain() {
		load_plugin_textdomain('fbconnect', PLUGINDIR ."/".FBCONNECT_PLUGIN_LANG);
	}

	function table_prefix() {
		global $wpdb;
		return isset($wpdb->base_prefix) ? $wpdb->base_prefix : $wpdb->prefix;
	}

	function comments_table_name() { return WPfbConnect::table_prefix() . 'comments'; }
	function usermeta_table_name() { return WPfbConnect::table_prefix() . 'usermeta'; }
	function users_table_name() { return WPfbConnect::table_prefix() . 'users'; }
}
endif;

if (!function_exists('fbconnect_init')):
function fbconnect_init() {
	if ($GLOBALS['fbconnect'] && is_a($GLOBALS['fbconnect'], 'WPfbConnect')) {
		return;
	}
	
	$GLOBALS['fbconnect'] = new WPfbConnect();
}
endif;

if (!function_exists('fbconnect_title')):
function fbconnect_title($title) {
	if($_REQUEST['fbconnect_action']=="community"){
		return __('Community', 'fbconnect')." - ".$title;
	}else if($_REQUEST['fbconnect_action']=="myhome"){
		$userprofile = WPfbConnect_Logic::get_user();
		return $userprofile->display_name." - ".$title;
	}else if($_REQUEST['fbconnect_action']=="invite"){
		return _e('Invite your friends', 'fbconnect')." - ".$title;
	}
		
	return $title;
}
endif;

/*
Ver rewrite.php
if (!function_exists('fbconnect_add_custom_urls')):
function fbconnect_add_custom_urls() {
  add_rewrite_rule('(userprofile)/[/]?([0-9]*)[/]?([0-9]*)$', 
  'index.php?fbconnect_action=myhome&fbuserid=$matches[2]&var2=$matches[3]');
  add_rewrite_tag('%fbuserid%', '[0-9]+');
  add_rewrite_tag('%var2%', '[0-9]+');
}
endif;
*/
//wp_enqueue_script( 'prototype' );
// -- Register actions and filters -- //

add_filter('wp_title', 'fbconnect_title');

// runs the function in the init hook
//add_action('init', 'fbconnect_add_custom_urls');

add_filter('get_comment_author_url', array('WPfbConnect_Logic', 'get_comment_author_url'));


if( get_option('fb_add_post_share') ) {
	add_action('the_content', array( 'WPfbConnect_Interface', 'add_fbshare' ) );
}

add_action( 'init', array( 'WPfbConnect','textdomain') ,1 ); // load textdomain

register_activation_hook(FBCONNECT_PLUGIN_BASENAME.'/fbConnectCore.php', array('WPfbConnect_Logic', 'activate_plugin'));
register_deactivation_hook(FBCONNECT_PLUGIN_BASENAME.'/fbConnectCore.php', array('WPfbConnect_Logic', 'deactivate_plugin'));

add_action( 'admin_menu', array( 'WPfbConnect_Interface', 'add_admin_panels' ) );

add_filter('language_attributes', array('WPfbConnect_Logic', 'html_namespace'));
add_filter('get_avatar', array('WPfbConnect_Logic', 'fb_get_avatar'),10,4);
// Add hooks to handle actions in WordPress

//add_action( 'wp_authenticate', array( 'WPfbConnect_Logic', 'wp_authenticate' ) ); // fbconnect loop start
add_action( 'wp_logout', array( 'WPfbConnect_Logic', 'fb_logout'),1);

add_action( 'init', array( 'WPfbConnect_Logic', 'wp_login_fbconnect' ),100 ); // fbconnect loop done


// Comment filtering
add_action( 'comment_post', array( 'WPfbConnect_Logic', 'comment_fbconnect' ), 5 );

//add_filter( 'comment_post_redirect', array( 'WPfbConnect_Logic', 'comment_post_redirect'), 0, 2);
if( get_option('fb_enable_approval') ) {
	add_filter( 'pre_comment_approved', array('WPfbConnect_Logic', 'comment_approval'));
}


// include internal stylesheet
add_action( 'wp_head', array( 'WPfbConnect_Interface', 'style'));
add_action( 'login_head', array( 'WPfbConnect_Interface', 'style'));

if( get_option('fb_enable_commentform') ) {
	add_action( 'comment_form', array( 'WPfbConnect_Interface', 'comment_form'), 10);
}

add_action( 'wp_footer', array( 'WPfbConnect_Logic', 'fbconnect_init_scripts'), 1);

if(!function_exists('carga_template')):
function carga_template() {
	if (isset($_REQUEST['fbconnect_action'])){
		set_include_path( TEMPLATEPATH . PATH_SEPARATOR . dirname(__FILE__) .PATH_SEPARATOR. WP_PLUGIN_DIR.'/'.FBCONNECT_PLUGIN_BASENAME. PATH_SEPARATOR . get_include_path() );   
		if($_REQUEST['fbconnect_action']=="community"){
			include( 'community.php');
		}else if($_REQUEST['fbconnect_action']=="myhome"){
			include( 'myhome.php');
		}else if($_REQUEST['fbconnect_action']=="tab"){
			include('fbconnect_tab.php');
		}else if($_REQUEST['fbconnect_action']=="invite"){
			include('invitefriends.php');
		}else if($_REQUEST['fbconnect_action']=="logout"){
			if(function_exists('wp_logout')):
				wp_logout();
			endif;
			if(function_exists('wp_redirect')):
				wp_redirect( get_option('siteurl') );
			endif;
		}else if($_REQUEST['fbconnect_action']=="fbfeed"){
			include( 'fbfeed.php');
		}
		restore_include_path();
		exit;
	}
}
endif;
add_action('template_redirect', 'carga_template');

/**
 * If the current comment was submitted with FacebookConnect, return true
 * useful for  <?php echo ( is_comment_fbconnect() ? 'Submitted with FacebookConnect' : '' ); ?>
 */
if(!function_exists('is_comment_fbconnect')):
function is_comment_fbconnect() {
	global $comment;
	return ( $comment->fbconnect == 1 );
}
endif;

/**
 * If the current user registered with FacebookConnect, return true
 */
if(!function_exists('is_user_fbconnect')):
function is_user_fbconnect($id = null) {
	global $current_user;
    $user = $current_user;
	if ($id != null) {
		$user = get_userdata($id);
	}
	if($user!=null && $user->fbconnect_userid){
		return true;
	}else{
		return false;
	}
}
endif;


//MAIN WIDGET
if(!function_exists('widget_FacebookConnector_init')):
function widget_FacebookConnector_init() {
if (!function_exists('register_sidebar_widget')) return;
function widget_FacebookConnector($args) {
		
		extract($args);

		$options = get_option('widget_FacebookConnector');

		if (!isset($options) || $options==""){
			$before_title ="<h2>";
			$after_title ="</h2>";
			$options = widget_FacebookConnector_init_options($options);
		}
		$title = $options['title'];
		$welcometext = $options['welcometext'];
		$footertext = $options['footertext'];
		$invitetext = $options['invitetext'];
		$lastvisittext = $options['lastvisittext'];
		$logintext = $options['logintext'];
		$loginbutton = $options['loginbutton'];
		$alreadytext = $options['alreadytext'];
		$maxlastusers = $options['maxlastusers'];

		echo $before_widget . $before_title . $title . $after_title;

		$fb_user = fb_get_loggedin_user();

		$user = wp_get_current_user();
		
		$users = WPfbConnect_Logic::get_lastusers_fbconnect($maxlastusers);
		$siteurl = get_option('siteurl');

		$uri = "";
		if (isset($_SERVER["REQUEST_URI"])){
			$uri = $_SERVER["REQUEST_URI"];			
		}
		
		set_include_path( TEMPLATEPATH . PATH_SEPARATOR . dirname(__FILE__) .PATH_SEPARATOR. WP_PLUGIN_DIR.'/'.FBCONNECT_PLUGIN_BASENAME. PATH_SEPARATOR . get_include_path() );   
		
		include( 'fbconnect_widget.php');
		
		restore_include_path();
		
		echo $footertext . $after_widget;
	}

	function widget_FacebookConnector_init_options($options){
		if (!isset($options['title'])){
			$options['title'] = "Community";
		}
		if (!isset($options['welcometext'])){
			$options['welcometext'] = "Welcome to ".get_option('blogname')."!";
		}
		if (!isset($options['lastvisittext'])){
			$options['lastvisittext'] = "Last visitors";
		}
		if (!isset($options['invitetext'])){
			$options['invitetext'] = "Invite your friends!";
		}
		if (!isset($options['logintext'])){
			$options['logintext'] = "Login using Facebook:";
		}
		if (!isset($options['loginbutton'])){
			$options['loginbutton'] = "long";
		}
		if (!isset($options['alreadytext'])){
			$options['alreadytext'] = "Already a member?";
		}
		if (!isset($options['footertext'])){
			$options['footertext'] = 'Powered by <a href="http://www.sociable.es">Sociable!</a>';
		}
		if (!isset($options['maxlastusers'])){
			$options['maxlastusers'] = "9";
		}
		return $options;
	}
	
	function widget_FacebookConnector_control() {
		$options = get_option('widget_FacebookConnector');
		if ( $_POST['FacebookConnector-submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['FacebookConnector-title']));
			$options['welcometext'] = stripslashes($_POST['FacebookConnector-welcometext']);
			$options['footertext'] = stripslashes($_POST['FacebookConnector-footertext']);
			$options['invitetext'] = stripslashes($_POST['FacebookConnector-invitetext']);
			$options['lastvisittext'] = stripslashes($_POST['FacebookConnector-lastvisittext']);
			$options['logintext'] = stripslashes($_POST['FacebookConnector-logintext']);
			$options['loginbutton'] = stripslashes($_POST['FacebookConnector-loginbutton']);
			$options['alreadytext'] = stripslashes($_POST['FacebookConnector-alreadytext']);
			$options['maxlastusers'] = (int)$_POST['FacebookConnector-maxlastusers'];
			update_option('widget_FacebookConnector', $options);
		}

		$options = widget_FacebookConnector_init_options($options);
		
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$welcometext = htmlspecialchars($options['welcometext'], ENT_QUOTES);
		$footertext = htmlspecialchars($options['footertext'], ENT_QUOTES);
		$invitetext = htmlspecialchars($options['invitetext'], ENT_QUOTES);
		$lastvisittext = htmlspecialchars($options['lastvisittext'], ENT_QUOTES);
		$logintext = htmlspecialchars($options['logintext'], ENT_QUOTES);
		$loginbutton = htmlspecialchars($options['loginbutton'], ENT_QUOTES);
		$alreadytext = htmlspecialchars($options['alreadytext'], ENT_QUOTES);
		$maxlastusers = htmlspecialchars($options['maxlastusers'], ENT_QUOTES);
		//get_option('blogname')

		echo '<p style="text-align:right;"><label for="FacebookConnector-title">'.__('Title:', 'fbconnect').' <input style="width: 180px;" id="FacebookConnector-title" name="FacebookConnector-title" type="text" value="'.$title.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="FacebookConnector-welcometext">'.__('Welcome msg:', 'fbconnect').' <input style="width: 180px;" id="FacebookConnector-welcometext" name="FacebookConnector-welcometext" type="text" value="'.$welcometext.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="FacebookConnector-footertext">'.__('Footer msg:', 'fbconnect').' <input style="width: 180px;" id="FacebookConnector-footertext" name="FacebookConnector-footertext" type="text" value="'.$footertext.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="FacebookConnector-invitetext">'.__('Invite msg:', 'fbconnect').' <input style="width: 180px;" id="FacebookConnector-invitetext" name="FacebookConnector-invitetext" type="text" value="'.$invitetext.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="FacebookConnector-lastvisittext">'.__('Visitors title:', 'fbconnect').' <input style="width: 180px;" id="FacebookConnector-lastvisittext" name="FacebookConnector-lastvisittext" type="text" value="'.$lastvisittext.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="FacebookConnector-logintext">'.__('Login msg:', 'fbconnect').' <input style="width: 180px;" id="FacebookConnector-logintext" name="FacebookConnector-logintext" type="text" value="'.$logintext.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="FacebookConnector-alreadytext">'.__('Member msg:', 'fbconnect').' <input style="width: 180px;" id="FacebookConnector-alreadytext" name="FacebookConnector-alreadytext" type="text" value="'.$alreadytext.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="FacebookConnector-maxlastusers">'.__('Max users:', 'fbconnect').' <input style="width: 180px;" id="FacebookConnector-maxlastusers" name="FacebookConnector-maxlastusers" type="text" value="'.$maxlastusers.'" /></label></p>';
		echo '<p style="text-align:right;"><label for="FacebookConnector-loginbutton">'.__('Login button:', 'fbconnect').' <SELECT style="width: 180px;" id="FacebookConnector-loginbutton" name="FacebookConnector-loginbutton">';
		echo '<OPTION ';
		if ($loginbutton=="long") echo "SELECTED";
		echo ' VALUE="long">long</OPTION>';
		echo ' <OPTION ';
		if ($loginbutton=="short") echo "SELECTED";
		echo ' VALUE="short">short</OPTION>';
		echo '</SELECT></label></p>';
		echo '<input type="hidden" id="FacebookConnector-submit" name="FacebookConnector-submit" value="1" />';
	}		

	register_sidebar_widget('FacebookConnector', 'widget_FacebookConnector');
	register_widget_control('FacebookConnector', 'widget_FacebookConnector_control', 300, 100);
}
endif;

add_action('plugins_loaded', 'widget_FacebookConnector_init');

?>
