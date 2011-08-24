<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */

include_once 'fbConfig.php';

if (!class_exists('WPfbConnect_Interface')):
class WPfbConnect_Interface {

	/**
	 * Enqueue required javascript libraries.
	 *
	 * @action: init
	 **/
	function js_setup() {
	}

	/**
	 * Add Facebook Share
	 *
	 * @action: the_content
	 **/
	function add_fbshare($content) {
		global $post;
		$content .= '<p class="fbconnect_share"><fb:share-button class="url" href="'.get_permalink($post->ID).'" /></p>';
		return $content;
	}

	/**
	 * Include internal stylesheet.
	 *
	 * @action: wp_head, login_head
	 **/
	function style() {
		if ( file_exists( TEMPLATEPATH . '/fbconnect.css') ){
			$css_path =  get_template_directory_uri() . '/fbconnect.css?ver='.FBCONNECT_PLUGIN_REVISION;
		}else{
			//$css_path = get_option('siteurl') . '/' . PLUGINDIR . '/fbconnect/fbconnect.css?ver='.FBCONNECT_PLUGIN_REVISION;
			$css_path = FBCONNECT_PLUGIN_URL . '/fbconnect.css?ver='.FBCONNECT_PLUGIN_REVISION;
		}
		echo '<link rel="stylesheet" type="text/css" href="'.$css_path.'" />';
		
	}


	/**
	 *  Modify comment form.
	 *
	 * @action: comment_form
	 **/
	function comment_form() {
		$fb_user = fb_get_loggedin_user();
		if (is_user_logged_in() && $fb_user) {
			echo '<img class="icon-text-middle" src="'.FBCONNECT_PLUGIN_URL .'/images/facebook_24.png"/>';
			echo '<input style="width:20px;" type="checkbox" name="sendToFacebook" id="sendToFacebook" checked="checked" />'.__('Publish this comment to Facebook', 'fbconnect');
		}
	}


	/**
	 * Spam up the admin interface with warnings.
	 **/
	function admin_notices_plugin_problem_warning() {
		echo'<div class="error"><p><strong>'.__('The WordPress fbconnect plugin is not active.', 'fbconnect').'</strong>';
		printf(_('Check %sfbconnect Options%s for a full diagnositic report.', 'fbconnect'), '<a href="options-general.php?page=global-fbconnect-options">', '</a>');
		echo '</p></div>';
	}
	

	/**
	 * Setup admin menus for fbconnect options and ID management.
	 *
	 * @action: admin_menu
	 **/
	function add_admin_panels() {
		$hookname = add_options_page(__('fbconnect options', 'fbconnect'), __('Facebook Connector', 'fbconnect'), 8, 'global-fbconnect-options', 
			array( 'WPfbConnect_Interface', 'options_page')  );
		add_action("admin_head-$hookname", array( 'WPfbConnect_Interface', 'js_setup' ));
		add_action("admin_head-$hookname", array( 'WPfbConnect_Interface', 'style' ));

	}


	function register_feed_forms($fb_online_stories,$fb_short_stories_title,$fb_short_stories_body,$fb_full_stories_title,$fb_full_stories_body) {
	  $one_line_stories = $short_stories = $full_stories = array();
	
	  $one_line_stories[] = $fb_online_stories;
	  $short_stories[] = array('template_title' => $fb_short_stories_title,
	                         'template_body' => $fb_short_stories_body);
	  $full_stories = array('template_title' => $fb_full_stories_title,
	                         'template_body' => $fb_full_stories_body);
	  $form_id = fb_feed_registerTemplateBundle($one_line_stories,$short_stories,$full_stories);
		
	  return $form_id;
	}

	/*
	 * Display and handle updates from the Admin screen options page.
	 *
	 * @options_page
	 */
	function options_page() {
		global $wp_version, $fbconnect;

			// if we're posted back an update, let's set the values here
			if ( isset($_POST['info_update']) ) {
				check_admin_referer('wp-fbconnect-info_update');

				$error = '';
				update_option( 'fb_api_key', $_POST['fb_api_key'] );
				update_option( 'fb_api_secret', $_POST['fb_api_secret'] );
				update_option( 'fb_enable_commentform', isset($_POST['enable_commentform']) ? true : false );
				update_option( 'fb_enable_approval', isset($_POST['enable_approval']) ? true : false );
				update_option( 'fb_add_post_share', isset($_POST['fb_add_post_share']) ? true : false );
				update_option( 'fb_use_ssl', isset($_POST['fb_use_ssl']) ? true : false );
				update_option( 'fb_use_fbml', isset($_POST['fb_use_fbml']) ? true : false );
				
				if ($error !== '') {
					echo '<div class="error"><p><strong>'.__('At least one of Facebook Connector options was NOT updated', 'fbconnect').'</strong>'.$error.'</p></div>';
				} else {
					echo '<div class="updated"><p><strong>'.__('Facebook Connector options updated', 'fbconnect').'</strong></p></div>';
				}

			
			}elseif (isset($_POST['template_update'])){
				check_admin_referer('wp-fbconnect-info_update');

				$error = '';

				update_option( 'fb_online_stories', $_POST['fb_online_stories'] );
				update_option( 'fb_short_stories_title', $_POST['fb_short_stories_title'] );
				update_option( 'fb_short_stories_body', $_POST['fb_short_stories_body'] );
				update_option( 'fb_full_stories_title', $_POST['fb_full_stories_title'] );
				update_option( 'fb_full_stories_body', $_POST['fb_full_stories_body'] );
				$templatesId = WPfbConnect_Interface::register_feed_forms($_POST['fb_online_stories'],$_POST['fb_short_stories_title'],$_POST['fb_short_stories_body'], $_POST['fb_full_stories_title'], $_POST['fb_full_stories_body']);
				if ($templatesId!=""){
					update_option( 'fb_templates_id', $templatesId );
				}else{
					$error = " ";
				}	
				if ($error !== '') {
					echo '<div class="error"><p><strong>'.__('Error updating Facebook templates!', 'fbconnect').'</strong>'.$error.'</p></div>';
				} else {
					echo '<div class="updated"><p><strong>'.__('Facebook templates updated! Templates ID:'.$templatesId, 'fbconnect').'</strong></p></div>';
				}

			}elseif ( isset($_REQUEST['action']) ) {
				switch($_REQUEST['action']) {
					case 'delete_template' :
						check_admin_referer('wp-fbconnect-info_delete_template');
						fb_feed_deactivateTemplateBundleByID($_REQUEST['template_id']);
						
						$fb_templates_id = get_option('fb_templates_id');
						if ($_REQUEST['template_id'] == $fb_templates_id){
							update_option( 'fb_templates_id', "" );
						}
						echo '<div class="updated"><p><strong>Facebook template removed:'.$_REQUEST['template_id'].'</strong></p></div>';
						break;
					case 'activate_template' :
						check_admin_referer('wp-fbconnect-info_activate_template');
						$template = fb_feed_getRegisteredTemplateBundleByID($_REQUEST['template_id']);

						if (isset($template) && $template!="" ){
								update_option( 'fb_templates_id', $_REQUEST['template_id'] );
								update_option( 'fb_online_stories', $template["one_line_story_templates"][0] );
								update_option( 'fb_short_stories_title', $template["short_story_templates"][0]["template_title"] );
								update_option( 'fb_short_stories_body', $template["short_story_templates"][0]["template_body"] );
								update_option( 'fb_full_stories_title', $template["full_story_templates"][0]["template_title"] );
								update_option( 'fb_full_stories_body', $template["full_story_templates"][0]["template_body"] );
						}
						
						break;
				}
			}
			
			// Display the options page form
			$siteurl = get_option('home');
			if( substr( $siteurl, -1, 1 ) !== '/' ) $siteurl .= '/';
			?>
			<div class="wrap">
				<h2><?php _e('Facebook Connector Options', 'fbconnect') ?></h2>

				<form method="post">


					<h3><?php _e('Facebook Application Configuration', 'fbconnect') ?></h3>
     				<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Facebook App. Config.', 'fbconnect') ?></th>
							<td>
							<a href="http://www.facebook.com/developers/" target="_blank"><?php _e('Go to Facebook Developer App', 'fbconnect') ?></a>
							</td>
						</tr>

						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="fb_api_key"><?php _e('Facebook API Key:', 'fbconnect') ?></label></th>
							<td>
							<input type="text" name="fb_api_key" id="fb_api_key" size="50" value="<?php echo get_option('fb_api_key');?>"/>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="fb_api_secret"><?php _e('Facebook API Secret:', 'fbconnect') ?></label></th>
							<td>
							<input type="text" name="fb_api_secret" size="50" id="fb_api_secret" value="<?php echo get_option('fb_api_secret');?>"/>
							</td>
						</tr>							
						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="enable_approval"><?php _e('Automatic Approval:', 'fbconnect') ?></label></th>
							<td>
								<p><input type="checkbox" name="enable_approval" id="enable_approval" <?php 
									echo get_option('fb_enable_approval') ? 'checked="checked"' : ''; ?> />
									<label for="enable_approval"><?php _e('Enable comment auto-approval', 'fbconnect') ?></label>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><label for="fb_add_post_share"><?php _e('Add share button:', 'fbconnect') ?></label></th>
							<td>
								<p><input type="checkbox" name="fb_add_post_share" id="fb_add_post_share" <?php 
									echo get_option('fb_add_post_share') ? 'checked="checked"' : ''; ?> />
									<label for="fb_add_post_share"><?php _e('Add Facebook share button', 'fbconnect') ?></label>
							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Comment Form:', 'fbconnect') ?></th>
							<td>
								<p><input type="checkbox" name="enable_commentform" id="enable_commentform" <?php
								if( get_option('fb_enable_commentform') ) echo 'checked="checked"'
								?> />
									<label for="enable_commentform"><?php _e('Allow send user comments to Facebook.', 'fbconnect') ?></label></p>

							</td>
						</tr>
						<tr valign="top">
							<th style="width: 33%" scope="row"><?php _e('Secure login:', 'fbconnect') ?></th>
							<td>
								<p><input type="checkbox" name="fb_use_ssl" id="fb_use_ssl" <?php
								if( get_option('fb_use_ssl') ) echo 'checked="checked"'
								?> />
									<label for="fb_use_ssl"><?php _e('Facebook Connect Via SSL.', 'fbconnect') ?></label></p>

							</td>
						</tr>
     				</table>

					
					<?php wp_nonce_field('wp-fbconnect-info_update'); ?>
					
     				<p class="submit"><input class="button-primary" type="submit" name="info_update" value="<?php _e('Update Configuration', 'fbconnect') ?> &raquo;" /></p>
     			</form>
				
	
				<form method="post">

					<h3><?php _e('Comment templates', 'fbconnect') ?></h3>
					<?php
							$fb_templates_id = get_option('fb_templates_id');
							$templates = fb_feed_getRegisteredTemplateBundles();

							//print_r($templates);
							if ($fb_templates_id == '' && isset($templates) && $templates!="" ){
								echo '<div class="error"><p><strong>'.__('No active Facebook template, please activate a template or create a new one!', 'fbconnect').'</strong>'.$error.'</p></div>';
							}else if ($fb_templates_id == ''){
								echo '<div class="error"><p><strong>'.__('Templates not synchronized with Facebook, write the templates text and press create templates!', 'fbconnect').'</strong>'.$error.'</p></div>';
							} else {
								echo '<div class="updated"><p><strong>'.__('Active Facebook template ID:', 'fbconnect').$fb_templates_id.'</strong></p></div>';
							}
	
							
	
							if (isset($templates) && $templates!="" ){
								foreach ($templates as $template){
									echo "<br/><b>".__('Template ID:', 'fbconnect').$template["template_bundle_id"].'</b> <a href="'.wp_nonce_url(sprintf('?page=%s&action=activate_template&template_id=%s', $_REQUEST['page'],$template["template_bundle_id"]), 'wp-fbconnect-info_activate_template').'">'.__('Activate template', 'fbconnect').'</a> ';
									echo '<a href="'.wp_nonce_url(sprintf('?page=%s&action=delete_template&template_id=%s', $_REQUEST['page'],$template["template_bundle_id"]), 'wp-fbconnect-info_delete_template').'">'.__('Delete template', 'fbconnect').'</a>';
									if ($fb_templates_id == $template["template_bundle_id"]){
										echo '<b style="color:red;"> <-'.__('Active Facebook template', 'fbconnect').'</b>';
									}

									echo "<br/>".__('One line story:', 'fbconnect').$template["one_line_story_templates"][0];
									echo "<br/>".__('Short story title:', 'fbconnect').$template["short_story_templates"][0]["template_title"];
									echo "<br/>".__('Short story body:', 'fbconnect').$template["short_story_templates"][0]["template_body"];
									echo "<br/>".__('Full story title:', 'fbconnect').$template["full_story_template"]["template_title"];
									echo "<br/>".__('Full story body:', 'fbconnect').$template["full_story_template"]["template_body"];
									echo "<br/>";
								}
							}
								//print_r($templates);
							$fb_online_stories = get_option('fb_online_stories');
							if (!$fb_online_stories){
								$fb_online_stories = '{*actor*} commented on {*blogname*}, post title: {*post_title*}, {*body_short*} ';
							}
		
							$fb_short_stories_title = get_option('fb_short_stories_title');
							if (!$fb_short_stories_title){
								$fb_short_stories_title = '{*actor*} commented on {*blogname*}, post title: {*post_title*} ';
							}
							$fb_short_stories_body = get_option('fb_short_stories_body');
							if (!$fb_short_stories_body){
								$fb_short_stories_body = '{*body_short*}';
							}
							$fb_full_stories_title = get_option('fb_full_stories_title');
							if (!$fb_full_stories_title){
								$fb_full_stories_title = '{*actor*} commented on {*blogname*}, post title: {*post_title*} ';
							}
							$fb_full_stories_body = get_option('fb_full_stories_body');
							if (!$fb_full_stories_body){
								$fb_full_stories_body = '{*body*}';
							}
							?>

	     				<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
							<tr valign="top">
								<th style="width: 33%" scope="row"><label for="fb_online_stories"><?php _e('One line stories template:', 'fbconnect') ?></label></th>
								<td>
								<textarea rows="3" cols="50" name="fb_online_stories" id="fb_online_stories"><?php echo htmlspecialchars(stripslashes($fb_online_stories)); ?></textarea> 
								</td>
							</tr>
							<tr valign="top">
								<th style="width: 33%" scope="row"><?php _e('Short stories template:', 'fbconnect') ?></th>
								<td>
								<label for="fb_short_stories_title"><?php _e('Title', 'fbconnect') ?></label><br/>
								<textarea rows="3" cols="50" name="fb_short_stories_title" id="fb_short_stories_title"><?php echo htmlspecialchars(stripslashes($fb_short_stories_title));?></textarea> <br/>
								<label for="fb_short_stories_body"><?php _e('Body', 'fbconnect') ?></label><br/>
								<textarea rows="3" cols="50" name="fb_short_stories_body" id="fb_short_stories_body"><?php echo htmlspecialchars(stripslashes($fb_short_stories_body));?></textarea> 
								</td>
							</tr>
							<tr valign="top">
								<th style="width: 33%" scope="row"><?php _e('Full stories template:', 'fbconnect') ?></th>
								<td>
								<label for="fb_full_stories_title"><?php _e('Title', 'fbconnect') ?></label><br/>
								<textarea rows="3" cols="50" name="fb_full_stories_title" id="fb_full_stories_title"><?php echo htmlspecialchars(stripslashes($fb_full_stories_title));?></textarea> <br/>
								<label for="fb_full_stories_body"><?php _e('Body', 'fbconnect') ?></label><br/>
								<textarea rows="3" cols="50" name="fb_full_stories_body" id="fb_full_stories_body"><?php echo htmlspecialchars(stripslashes($fb_full_stories_body));?></textarea> 
								</td>
							</tr>
	
	     				</table>

						<?php wp_nonce_field('wp-fbconnect-info_update'); ?>
	     				<p class="submit"><input class="button-primary" type="submit" name="template_update" value="<?php _e('Create Template', 'fbconnect') ?> &raquo;" /></p>
	     			</form>
			</div>
    			<?php
	} // end function options_page


}
endif;

?>
