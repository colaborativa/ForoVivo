<?php 
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 05/10/2008
 * @license: GPLv2
 */

get_header();
?> 

	<div class="fbnarrowcolumn narrowcolumn">
		
<?php

	$fb_user = fb_get_loggedin_user();
	if(isset($fb_user) && $fb_user!=""){
		$users = WPfbConnect_Logic::get_connected_friends($fb_user);
		echo "<h2>".__('Community friends', 'fbconnect')."</h2>\n";
		echo "<div class=\"fbconnect_userpics2\">\n";
		foreach($users as $user){
			echo "<div><a style=\"border: 2px solid #d5d6d7;\" onclick=\"location.href='./?fbconnect_action=myhome&fbuserid=".$user["uid"]."';\" href=\"./?fbconnect_action=myhome&fbuserid=".$user["uid"]."\"><fb:profile-pic uid=\"".$user["uid"]."\" size=\"square\" linked=\"false\"></fb:profile-pic></a></div>\n";
		}
		echo "</div>\n";
	}else{
		
	}
?> 
		
<?php
		$users_count = WPfbConnect_Logic::get_count_users();
		echo "<h2>".__('Community', 'fbconnect')." (".$users_count." ".__('members', 'fbconnect').")</h2>";
		
		$pos = 0;
		if (isset ($_REQUEST["pos"])){
			$pos= (int)$_REQUEST["pos"];
		}
		$viewusers = 50;
		$users = WPfbConnect_Logic::get_lastusers_fbconnect($viewusers,$pos);

			echo "<div class=\"fbconnect_userpics2\">";
			foreach($users as $user){
				echo get_avatar( $user->ID,50 );
			}
			echo "</div>";

		if ($pos>=$viewusers){
			echo '<a href="'.get_option('siteurl').'/?fbconnect_action=community&pos='.($pos-$viewusers).'">&laquo; '.__('Previous page', 'fbconnect').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>';
		}
		if (($pos+$viewusers)<$users_count){
			echo '<a href="'.get_option('siteurl').'/?fbconnect_action=community&pos='.($pos+$viewusers).'"> '.__('Next page', 'fbconnect').' &raquo;</a>';
		}

?>

</div>


<?php get_footer(); ?>