<?php
	$userprofile = WPfbConnect_Logic::get_user();
?>	
	
<div class="fbconnect_userprofile">
	<div class="fbconnect_userpicmain">
		<fb:profile-pic uid="<?php echo $userprofile->fbconnect_userid;?>" size="small" linked="true"></fb:profile-pic>
	</div>

	<div class="fbconnect_profiletexts">
	    <b><?php _e('Status:', 'fbconnect') ?></b> <fb:user-status uid="<?php echo $userprofile->fbconnect_userid;?>" linked="true"></fb:user-status>
		<br/><b><?php _e('Name:', 'fbconnect') ?> </b><?php echo $userprofile->display_name; ?>
		<br/><b><?php _e('Nickname:', 'fbconnect') ?> </b><?php echo $userprofile->nickname; ?>
		<br/><b><?php _e('Member since:', 'fbconnect') ?> </b><?php echo $userprofile->user_registered; ?>
		<br/><b><?php _e('Website URL:', 'fbconnect') ?> </b><a href="<?php echo $userprofile->user_url; ?>" rel="external nofollow"><?php echo $userprofile->user_url; ?></a>
		<br/><b><?php _e('About me:', 'fbconnect') ?> </b><?php echo $userprofile->description; ?><br/>
		<?php if (isset($userprofile->fbconnect_userid) && $userprofile->fbconnect_userid!="" && $userprofile->fbconnect_userid!="0") : ?>
			<br/><b><a href="http://www.facebook.com/profile.php?id=<?php echo $userprofile->fbconnect_userid; ?>" rel="external nofollow"><img class="icon-text-middle" src="<?php echo FBCONNECT_PLUGIN_URL; ?>/images/facebook_24.png"/><?php _e('Facebook profile', 'fbconnect') ?></a></b>
		<?php endif; ?>
	</div>
</div>
