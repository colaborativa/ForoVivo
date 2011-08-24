<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */
if (version_compare("5", phpversion(),"<")){
	include_once 'facebook-client/facebook.php';
	include_once 'fbConfig_php5.php';
}else{	
	include_once 'facebook-client4/facebook.php';
	include_once 'fbConfig_php4.php';
}

function is_config_setup() {
  return (get_api_key() && get_api_secret() &&
          get_api_key() != 'YOUR_API_KEY' &&
          get_api_secret() != 'YOUR_API_SECRET');
}

// Whether the site is "connected" or not
function is_fbconnect_enabled() {
  if (!is_config_setup()) {
    return false;
  }

  // Change this if you want to turn off Facebook connect
  return true;
}
function get_api_key() {
	if (isset($_POST['fb_api_key']) && $_POST['fb_api_key']!=""){
		return $_POST['fb_api_key'];
	}else{
		return get_option('fb_api_key');
	}
}
function get_api_secret() {
	if (isset($_POST['fb_api_secret']) && $_POST['fb_api_secret']!=""){
		return $_POST['fb_api_secret'];
	}else{
		return get_option('fb_api_secret');
	}
  
}

function get_base_fb_url() {
  return "connect.facebook.com";
}

function get_ssl_root() {
  return 'https://www.'.get_base_fb_url();
}


function get_static_root() {
  return 'http://static.ak.'.get_base_fb_url();
}


function get_feed_bundle_id() {
  return get_option('fb_templates_id');
}

/*
 * Get the facebook client object for easy access.
 */
function facebook_client() {
  static $facebook = null;
  $api_key = get_api_key();
  $api_secret = get_api_secret();
  if ($facebook === null && $api_key!="" && $api_secret!="") {
	$facebook = new Facebook($api_key, $api_secret, false, get_base_fb_url());
  }
  return $facebook;
}

