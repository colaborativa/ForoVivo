<?php
/*
Plugin Name: Login-Logout
Version: 1.3.2
Author: Roger Howorth
Author URI: www.thehypervisor.com
Description: Adds a user friendly widget to make login/logout easy. Compatible WP 2.7+
License: http://www.gnu.org/licenses/gpl.html
*/
/*
Installation
1. Copy the file login-and-out.php to your WordPress plugins directory.
2. Login to WordPress as Administrator, go to Plugins and Activate it.
3. Add the Login-Logout widget to your Widget-enabled Sidebar
   instead of the default "Meta" Widget

Credit: Thanks to Patrick Khoo http://www.deepwave.net/ for model code. I worked with his Hide dashboard code, removed unwanted sections and updated for Wordpress 2.7+.

Copyright (c) 2009 Roger Howorth

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

function rhsidebar_meta($args) {
	extract ($args);
	global $user_identity , $user_email;
	$options = get_option('rh_hidedash_options');
	if ( !wp_specialchars($options['sidebar_width']) ) $options['sidebar_width'] = "200"; 
	echo $before_widget;
        if ( $options['center_widget'] ) echo '<div style="width:'. wp_specialchars($options['sidebar_width']) . 'px; margin: 0px auto;">';
	echo $before_title . $options['title'] . $after_title;

        $all_links = get_option ( 'rh_hidedash_links_options' );
        if ( !empty($all_links)) {
            foreach ( $all_links as $link ) {
            $extra_links = $extra_links . '<a href="'. current($link) .'">'. key($link).'</a> ';
            } 
        }
	if (is_user_logged_in()) {
		// User Already Logged In
		get_currentuserinfo();  // Usually someone already did this, right?
		if ( $options['display_email'] == '1' && !$options['hide_option_label'] ) printf('Welcome, <u><b>%s</b></u> (%s)<br />Options: &nbsp;',$user_identity,$user_email); else
		if ( $options['display_email'] == '1' && $options['hide_option_label'] ) printf('Welcome, <u><b>%s</b></u> (%s)<br />',$user_identity,$user_email); else
                if ( $options['hide_option_label'] ) printf('Welcome, <u><b>%s</b></u><br />',$user_identity);
                else printf('Welcome, <u><b>%s</b></u><br />Options: &nbsp;',$user_identity);
		// Default Strings
		$link_string_site = "<a href=\"".get_bloginfo('wpurl')."/wp-admin/index.php\" title=\"".__('Site Admin')."\">".__('Site Admin')."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
		$link_string_logout = '<a href="'. wp_logout_url($_SERVER['REQUEST_URI']) .'" title="Log out">Log out</a>';
		$link_string_edit = "<a href=\"".get_bloginfo('wpurl')."/wp-admin/edit.php\" title=\"".__('Edit Posts')."\">".__('Edit Posts')."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
		$link_string_profile = "<a href=\"".get_bloginfo('wpurl')."/wp-admin/profile.php\" title=\"".__('My Profile')."\">".__('My Profile')."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";

		// Administrator?
		if (current_user_can('level_10')) {
			echo $link_string_site;
			echo $link_string_logout;
                        if ( $extra_links ) echo '<br />Links: '.$extra_links;
                        if ( $options['center_widget'] ) echo '</div>';
			echo $after_widget;
			return;
		}

		// level_2?
		if (current_user_can('level_2')) {
			if ($options['allow_authed']) {
				// Allow level_2 user to see Dashboard - treat like Administrator
				echo $link_string_site;
				echo $link_string_logout;
                                if ( $options['center_widget'] ) echo '</div>';
				echo $after_widget;
				return;
			}
			// Hide Dashboard for level_2 user
			echo $link_string_edit;
			echo $link_string_logout;
                        if ( $options['center_widget'] ) echo '</div>';
			echo $after_widget;
			return;
		}

		// Less than level_2 user - Hide Dashboard from this User
		echo $link_string_profile;
		echo $link_string_logout;
                if ( $options['center_widget'] ) echo '</div>';
		echo $after_widget;
		return;
	}

	// User _NOT_ Logged In
	echo "<a href=\"".get_bloginfo('wpurl')."/wp-login.php?action=register&amp;redirect_to=".$_SERVER['REQUEST_URI']."\" title=\"".__('Register')."\">".__('Register')."</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	echo "<a href=\"".get_bloginfo('wpurl')."/wp-login.php?action=login&amp;redirect_to=".$_SERVER['REQUEST_URI']."\" title=\"".__('Login')."\">".__('Login')."</a>";
	echo $after_widget;
	return;
}

function rhsidebar_meta_control () {
	$options = get_option('rh_hidedash_options');
	if ( $_POST['rhhd_submit'] ) {
		$options['title'] = strip_tags(stripslashes($_POST['rhhd_title']));
		$options['sidebar_width'] = strip_tags(stripslashes($_POST['rhhd_sb_width']));
                $options["display_email"] = $_POST['edisplay_email'];
                $options["center_widget"] = $_POST['ecenter_widget'];
                $options["hide_option_label"] = $_POST['ehide_option_label'];
		update_option('rh_hidedash_options', $options);
	}
	$title = wp_specialchars($options['title']);
	if ( !wp_specialchars($options['sidebar_width']) ) $options['sidebar_width'] = "160"; 
	?>
	<p style="text-align: center">
		<input type="hidden" name="rhhd_submit" id="rhhd_submit" value="1" />
		<label for="rhhd_title"><?php _e('Title:'); ?> <input type="text" id="rhhd_title" name="rhhd_title" value="<?php echo $title; ?>" /></label></p>

	<p style="text-align: center">
                <label for="edisplay_email"><?php _e('Display email: '); ?><input type="checkbox" '; <?php if ( $options["display_email"] == '1' ) echo 'checked="yes" '; ?> name="edisplay_email" id="edisplay_email" value="1" /></label></p>
	<p style="text-align: center">
                <label for="ecenter_widget"><?php _e('Center widget: '); ?><input type="checkbox" '; <?php if ( $options["center_widget"] == '1' ) echo 'checked="yes" '; ?> name="ecenter_widget" id="ecenter_widget" value="1" /></label></p>
	<p style="text-align: center">
                <label for="ehide_option_label"><?php _e('Hide option label: '); ?><input type="checkbox" '; <?php if ( $options["hide_option_label"] == '1' ) echo 'checked="yes" '; ?> name="ehide_option_label" id="ehide_option_label" value="1" /></label></p>
	<p style="text-align: center">
		<label for="rhhd_sb_width"><?php _e('Sidebar width:'); ?> <input type="text" size="5" maxlength="5" id="rhhd_sb_width" name="rhhd_sb_width" value="<?php echo wp_specialchars($options['sidebar_width']) ; ?>" /></label></p>
	</p>
        <p>Please visit <a href="tools.php?page=login_out_menu">Login & Out widget settings</a> to adjust other settings.</p>
	<?php
	return;
}


function rh_plugin_init() {
	register_sidebar_widget('Hypervisor Login/Logout', 'rhsidebar_meta');
	register_widget_control('Hypervisor Login/Logout', 'rhsidebar_meta_control');
	return;
}

add_action("plugins_loaded", "rh_plugin_init");

// Hook for adding admin menus
add_action('admin_menu', 'login_and_out_menu');

// action function for above hook
function login_and_out_menu() {
	add_management_page('Login & Out', 'Login & Out', 8, 'login_out_menu', 'login_out_menu');
}

// login_out_menu() displays the page content for the Login & Out admin submenu
function login_out_menu() {
    if ( isset ($_POST['update_loginout']) )  { 
        if ( !wp_verify_nonce ( $_POST['loginout-verify-key'], 'loginout') ) die('Failed security check. Reload page and retry');

        $cur_links = array();
        $new_links = array();
        $cur_links = get_option ( 'rh_hidedash_links_options' );
        if ( !empty ($cur_links) ) { 
            $count=0;
            foreach ( $cur_links as $link ) {
               /* remove unwanted links... if a link is not ticked do not add to new_links array */
               if ( $_POST[$count] <> '1' ) { $count++; continue;}
               $new_links[] = $link;
               $count++;
               }
             }
         // if we posted a new link add it to new_link array
         if ( $_POST['nlink-text'] <> '' ) $new_links[] = array($_POST['nlink-text'] => $_POST['nlink-target']);
         if ( !empty ( $new_links) ) {
             array_unique ( $new_links) ;
             sort ( $new_links);
             }
         update_option ( 'rh_hidedash_links_options', $new_links );
         ?><div id="message" class="updated fade"><p><strong><?php _e('Login and Out options updated.'); ?></strong></div><?php
    }  // end if isset
    ?><form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    <div class="form-field">
    <?php
    echo "<h2>Login and Out widget options</h2>";
    echo '<input type="hidden" name="loginout-verify-key" id="loginout-verify-key" value="' . wp_create_nonce('loginout') . '" />';

    echo "<h3>Add a link to the widget</h3>";
    echo '<p>Add text for a new link :</p><p><input type="text" name="nlink-text" id="nlink-text" value="" /></p>';
    echo '<p>Add target for a new link :</p><p><input type="text" name="nlink-target" id="nlink-target" value="" /></p>';
    echo "<h3>Links on the widget (un-tick to delete)</h3>";
    $all_links = get_option ( 'rh_hidedash_links_options' );
    if ( !empty ($all_links) )
       {
        echo '<table border="2" cellpadding="4" width="50%"><tr>';
        $count = 0;
        $link = array();
        echo '<strong><td>Link text</td><td>Link target</td></tr></strong>';
        foreach ( $all_links as $link ) {
        echo '<td>'. key($link).'</td><td>'. current($link).'</td><td><input type="checkbox" checked="yes" '; echo ' name="'. $count.'" id="'. $count.'" value="1" /></td></tr>';
        $count++;
        } echo '</table>';
     } else echo "No links in database.";

    ?>
    <p class="submit">
    <form action="<?php echo __FILE__ ?>" method "post"><input type="submit" name="update_loginout" value="Submit!">
    </form>
    </p><br />
<h5>Like this plugin?</h5>
Please visit our website <a href="http://www.thehypervisor.com">The Hypervisor</a>
</div>
Or consider making a donation.<br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypal.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form>
<?php
}
