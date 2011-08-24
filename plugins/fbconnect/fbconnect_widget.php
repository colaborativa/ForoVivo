<?php
/**
 * @author: Javier Reyes Gomez (http://www.sociable.es)
 * @date: 23/12/2008
 * @license: GPLv2
 */

	if ( $user->ID ) {
		echo "<div class=\"fbconnect_miniprofile\">";
		echo "<div class=\"fbconnect_userpicmain\"><a onclick=\"location.href='".$siteurl."/?fbconnect_action=myhome&amp;userid=".$user->ID."';\" href=\"".$siteurl."/?fbconnect_action=myhome&amp;userid=".$user->ID."\"><fb:profile-pic uid=\"".$user->fbconnect_userid."\" size=\"thumb\" linked=\"false\"></fb:profile-pic></a></div>";
		echo "<p>".$welcometext;
		echo "<br/><a href=\"".$siteurl."/?fbconnect_action=myhome&amp;userid=".$user->ID."\">".$user->display_name."</a>";
		echo "<br/><a href=\"".$siteurl."/wp-admin/profile.php"."\">Edit profile</a>";
		echo '<br/><a href="#" onclick="FB.Connect.logout(function() { window.location = \''.$siteurl.'/?fbconnect_action=logout'.'\'; })">Logout</a>';
		echo "</p>";
		echo "</div>";
	}else{
		echo $alreadytext."<br/>";
		echo '<a href="'.$siteurl.'/wp-login.php'.'"><b>Login</b></a><br/>';
	}
	echo "<div class=\"invitebutton\">";
	
	if ($fb_user){
		echo "<input type=\"button\" value=\"".$invitetext."\" style=\"width:100%;\" onclick=\"location.href='".$siteurl."/?fbconnect_action=invite'\"/>";
	}else{
		echo $logintext."<br/>";	
		echo "<fb:login-button length=\"".$loginbutton."\" onlogin=\"window.location = '".$uri."';\"></fb:login-button>\n";
	}
	echo "</div>";		
	

	echo "<div class=\"fbconnect_LastUsers\">";
	echo "<div class=\"fbconnect_title\">".$lastvisittext."</div>";
	echo "<div class=\"fbconnect_userpics\">";
	

	foreach($users as $user){
			echo get_avatar( $user->ID,50 );
	}

	echo "</div>";
	echo '<div style="text-align:right;"><a href="'.$siteurl.'/?fbconnect_action=community'.'">'.__('view more...', 'fbconnect').' </a></div>';
	echo "</div>";

?> 