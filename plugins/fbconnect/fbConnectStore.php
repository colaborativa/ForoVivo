<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */

if (!class_exists('WPfbConnect_Store')):
class WPfbConnect_Store {
	var $comments_table_name;
	var $usermeta_table_name;
	var $users_table_name;

	function WPfbConnect_Store()
	{
		global $wpdb;

		$table_prefix = isset($wpdb->base_prefix) ? $wpdb->base_prefix : $wpdb->prefix;

		$this->comments_table_name =  $table_prefix . 'comments';
		$this->usermeta_table_name =  $wpdb->prefix . 'usermeta';
		$this->users_table_name =  $wpdb->prefix . 'users';
	
	}

	function isError($value)
	{
		return $value === false;
	}

	/**
	 * Create Facebook Connect columns 
	 */
	function create_tables()
	{
		global $wp_version, $wpdb, $fbconnect;

		if ($wp_version >= '2.3') {
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		} else {
			require_once(ABSPATH . 'wp-admin/admin-db.php');
			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		}

		// add column to comments table
		$result = maybe_add_column($this->comments_table_name, 'fbconnect',
				"ALTER TABLE $this->comments_table_name ADD `fbconnect` varchar(50) NOT NULL DEFAULT '0'");

		// add column to users table
		$result = maybe_add_column($this->users_table_name, 'fbconnect_lastlogin',
				"ALTER TABLE $this->users_table_name ADD `fbconnect_lastlogin` int(14) NOT NULL DEFAULT '0'");

		// add column to users table
		$result = maybe_add_column($this->users_table_name, 'fbconnect_userid',
				"ALTER TABLE $this->users_table_name ADD `fbconnect_userid` varchar(250) NOT NULL DEFAULT '0'");

	}

}
endif;


?>
