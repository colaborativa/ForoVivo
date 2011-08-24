=== Facebook Connect Wordpress plugin ===
Author: Javier Reyes (www.sociable.es) (www.sixjumps.com)
Contributors: jreyesg
Donate link: http://www.sociable.es/facebook-connect/
Tags: Facebook,Facebook Connect,community,login,auth,profile,last visitors
Requires at least: 2.6
Tested up to: 2.7
Stable tag: trunk

== Description ==
Complete integration of your Wordpress blog and Facebook, using Facebook Connect. Build a tribe/community around your blog: Facebook login, automatic user registration, send comments to Facebook feed, invite your Facebook friends.

1. Access the blog (login) with your Facebook credentials where facebook validates the username without asking for the password necessary to access the blog/website.

2. The user does not need to register in the blog because thanks to the plugin the user can utilize his complete profile information he already edited in Facebook. 
This is tremendously useful as the user does not have to register and create profiles over and over again! 
The blog can now utilize the shared user profile information for customization and statistics in our Wordpress Blog. 
The plugin creates a Wordpress Blog user with the exact same facebook profile information which then could be edited by the user if he likes so.

3. It is possible to obtain your friends/contacts from facebook and to generate invitations to join our Blog/Site. 
One way of promoting the site using the so powerful and marvellous word-of-mouth.

4. You additionally can access to a list of existing members of the blog/site.

5. The users activity in the blog/site such as posts can be sent to Facebooks mini-feed of the users profile and is then visible in facebook in his lifestream.

6. You can show your last visits to the blog including the users photo that have accessed the page.



== Installation ==

This plugin follows the [standard WordPress installation method][]:

1. Log in to the Facebook Developer application: http://www.facebook.com/developers/
2. Create a new application, more info: http://developers.facebook.com/get_started.php
3. Upload the `fbconnect` folder to the `/wp-content/plugins/` directory
4. Activate the plugin through the 'Plugins' menu in WordPress
5. Configure the plugin through the 'Facebook Connector' section of the 'Settings' menu
6. Use the Facebook App info (step 2) to configure the plugin
7. Create a new template for comments.
8. Activate the Facebook Connector widget from the 'Design / Widtgets' menu.
9. If you dont see the Facebook Connect login button, and the user images, you need to modify
your header.php theme file:
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">

 
[standard WordPress installation method]: http://codex.wordpress.org/Managing_Plugins#Installing_Plugins

== Frequently Asked Questions ==
1.My Wordpress Theme dont support widgets, What can i do?

If your wordpress don't support widgets, you can add the FB Connect plugin manually:

< ?php
widget_FacebookConnector(array());
?>

2. IExplorer don't show the login button and user photos

On some wordpress themes you need to modify your "header.php" file:

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">

and check your "footer.php" theme file: if don't have a < ?php wp_footer(); ?> line
Add it before the end body tag:

< ?php wp_footer(); ?>
< /body>
< /html> 

== Screenshots ==
1. Screenshot Facebook login and last visitors widget 
2. Screenshot Community members page
3. Screenshot User profile page
4. Screenshot Send comment to Facebook
5. Screenshot Facebook feed comment
6. Screenshot Invite your Facebook friends

== Help and Updates ==
http://www.sociable.es/facebook-connect

== Changelog ==
= version 1.2 =
*Show page title on community,invite friends and profile
*Facebook Share button
*Solved Facebook comment popup scroll problem
*Show Facebook profile link, on the plugin profile page
*Facebook libs updated
*Square user avatar
*Català translation: By Miquel Labòria (Blog: http://apple.bloks.cat/)
*German translation: By Sebastian Schwittay (Blog: http://www.pixelreality.net/) and Manuel Gruber (Blog: http://www.manuelgruber.com)
*Simplified Chinese translation: By Eric X. (Blog: http://blog.xilibi.com/)
*French translation: By Gilles Barbier (Blog: http://www.gillesblog.com) and By Mathieu DHORDAIN (Blog: http://www.dhordain.com/)
*Italian translation: By Valentino Aluigi (http://www.maverick.it , http://www.valentinoaluigi.com)





= version 1.0 =
*Multilanguage support, fbconnect/lang. (spanish and english for the moment)
*Widget and CSS personalization (copy fbconnect_widget.php and fbconnect.css to your wordpress theme directory)
*Facebook libraries already loaded by other plugin. 

= version 0.9.9 =
*Fixed some w3.org validation errors (Thanks to Axel from IEEE)
*More changes to solve the infinite reload problem
*Removed sidebar import to avoid problems with some themes

= version 0.9.8 =
*User profile page translation, spanish to english

= version 0.9.7 =
*Some changes to solve the infinite reload problem
*CSS changes for Google Chrome, and other themes

= version 0.9.6 =
*Changes changes on fbconnect.css to avoid problems with themes
*Solved problem with Wordpress 2.7 logout
*Changes on the inclusion of community.php and myhome.php
*Login button size configuration

= version 0.9.5 =
*Removed /css/fb_connect.css (404 not found, Thanks to Claude Vedovini)
*Some changes on fbconnect.css to avoid problems with themes(Thanks to Claude Vedovini)
*Solved problem with auth_expiresession() on PHP4
*Show blog name on invite friends button
*Changes on the users count, to show on the community page

= version 0.9.1 =
*Removed SSL support (problems with https://static.ak.connect.facebook.com/pics/t_silhouette.jpg)

= version 0.9 =
*SSL Support
*Include pages from user theme (MyHome and Community)

= version 0.8 =
*Removed site_url usage
*Changed order on comments list and community users
*Community users pagination

= version 0.7 =
*Facebook Connect comment dialog
*Solved problems with Community page (sidebar.php  not found)

= version 0.0.4 =
*PHP4 support
*New page for friends invite
*CSS Styles
*Some bugs

= version 0.0.1 =
-