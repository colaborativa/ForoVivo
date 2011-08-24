<?php 
get_header(); 
?>
	<div id="content" class="narrowcolumn">
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

<?php 
get_footer(); 
?>
