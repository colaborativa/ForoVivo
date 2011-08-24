<?php get_header(); ?>

<div class="fbnarrowcolumn narrowcolumn">

		<h2><?php _e('User profile', 'fbconnect') ?></h2>
		
		<?php
			if(file_exists (TEMPLATEPATH.'/userprofile.php')){
				include( TEMPLATEPATH.'/userprofile.php');
			}else{
				include( FBCONNECT_PLUGIN_PATH.'/userprofile.php');
			}

		 ?>

		<div style="width:100%;clear:both;">&nbsp;</div>
		<h2><?php _e('User comments', 'fbconnect') ?></h2>
		<?php
			if(file_exists (TEMPLATEPATH.'/usercomments.php')){
				include( TEMPLATEPATH.'/usercomments.php');
			}else{
				include( FBCONNECT_PLUGIN_PATH.'/usercomments.php');
			}
		?>

</div>


<?php get_footer(); ?>