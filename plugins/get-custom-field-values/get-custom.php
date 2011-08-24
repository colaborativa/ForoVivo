<?php
/*
Plugin Name: Get Custom Field Values
Version: 3.0.1
Plugin URI: http://coffee2code.com/wp-plugins/get-custom-field-values
Author: Scott Reilly
Author URI: http://coffee2code.com
Description: Use widgets or template tags to easily retrieve and control the display of any custom field values/meta data for posts or pages.

The power of custom fields gives this plugin the potential to be dozens of plugins all rolled into one.

This plugin allows you to harness the power of custom fields/meta data.  Use the "Get Custom Field" widget,
the [custom_field] shortcode (which has a sidebar widget to help you build the shortcode), or one of six 
template tags to retrieve and display custom fields.  Find a custom field for the current post, a specified
post, a recent post, or randomly.  And for the custom field values found by the plugin, you may optionally
specify text or HTML to appear before and after the results.  If nothing is found, then nothing is display
(unless you define text to appear when no results are found).  If multiple results are found, only the first
will be displayed unless you specify a string to be used to join the results together (such as ","), in which
case all will be returned.  Visit the Examples section to see how this plugin can be cast in dozens of
different ways.

There are six template tags provided by this plugin.  Here they are, with an explanation of when they are appropriate
for use:
* c2c_get_custom() : Use this inside "the loop" to retrieve a custom field value for a post
* c2c_get_current_custom() : This is only available on the permalink post template (single.php) and page template
(page.php).  Can be used inside or outside "the loop".  Useful for using custom field to define text you want to
include on a post or page's header, footer, or sidebar.
* c2c_get_post_custom() : Useful when you know the ID of the post whose custom field value you want.
* c2c_get_random_custom() : Retrieve the value of a random instance of the specified custom field key, as long as the
field is associated with a published posted, non-passworded post (you can modify a setting in the plugin file to
search passworded posts as well)
* c2c_get_random_post_custom() : Retrieve the value of a random instance of the specified custom field key for a given
post
* c2c_get_recent_custom() : Retrieves the most recent (according to the associated post's publish date) value of
the specified custom field.

You can filter the custom field values that the plugin would display.  Add filters for 'the_meta' to filter custom 
field data (see the end of the code file for commented out samples you may wish to include).  You can also add 
per-meta filters by hooking 'the_meta_$sanitized_field'.  `$sanitized_field is a clean version of the value of
$field`where everything but alphanumeric and underscore characters have been removed.  So to filter the value of
the "Related Posts" custom field, you would need to add a filter for 'the_meta_RelatedPosts`.

Compatible with WordPress 2.6+, 2.7+, 2.8+.

=>> Read the accompanying readme.txt file for more information.  Also, visit the plugin's homepage
=>> for more information and the latest updates

Installation:

1. Download the file http://coffee2code.com/wp-plugins/get-custom.zip and unzip it into your 
wp-content/plugins/ directory.
2. (optional) Add filters for 'the_meta' to filter custom field data (see the end of the file for 
commented out samples you may wish to include).  And/or add per-meta filters by hooking 'the_meta_$field'
3. Activate the plugin through the 'Plugins' admin menu in WordPress
4. Give post(s) a custom field with a value.
5. (optional; only for WP2.8+) Go to the Appearance -> Widgets admin page to create one or more 'Get Custom Field' sidebar
widgets for your widget-enabled theme.
6. (optional) Use one of the six template functions provided by this plugin to retrieve the contents of custom
fields.  You must 'echo' the result if you wish to display the value on your site.

Function arguments:
    $field	: This is the name of the custom field you wish to display
    $before	: The text to display before all field value(s)
    $after	: The text to display after all field value(s)
    $none	: The text to display in place of the field value should no field value exists; if defined as ''
    		and no field value exists, then nothing (including no $before and $after) gets displayed
    $between 	: The text to display between multiple occurrences of the custom field; if defined as '', then
    		only the first instance will be used
    $before_last: The text to display between the next-to-last and last items listed when multiple occurrences of
    		the custom field; $between MUST be set to something other than '' for this to take effect
    
Additional arguments used by c2c_get_recent_custom():
   $limit	: The limit to the number of custom fields to retrieve (also used by c2c_get_random_post_custom())
   $unique	: Boolean ('true' or 'false') to indicate if each custom field value in the results should be unique
   $order	: Indicates if the results should be sorted in chronological order ('ASC') (the earliest custom field value
   		listed first), or reverse chronological order ('DESC') (the most recent custom field value listed first)
   $include_pages : Boolean ('true' or 'false') to indicate if pages should be included when
   		retrieving recent custom values; default is 'true'
   $show_pass_post : Boolean ('true' or 'false') to indicate if password protected posts should be included when 
   		retrieving recent custom values; default is 'false'
		
Examples: (visit the plugin's homepage for more examples)

	<?php echo c2c_get_custom('mymood'); ?>  // with this simple invocation, you can echo the value of any metadata field
	
	<?php echo c2c_get_custom('mymood', 'Today's moods: ', '', ', '); ?>
	
	<?php echo c2c_get_recent_custom('mymood', 'Most recent mood: '); ?>
	
	<?php echo c2c_get_custom('mymood', '(Current mood: ', ')', ''); ?>
	
	<?php echo c2c_get_custom('mylisten', 'Listening to : ', '', 'No one at the moment.'); ?>
	
	<?php echo c2c_get_custom('myread', 'I\'ve been reading ', ', if you must know.', 'nothing'); ?>
	
	<?php echo c2c_get_custom('todays_link', '<a class="tlink" href="', '" >Today\'s Link</a>'); ?>

	<?php echo c2c_get_current_custom('meta_description', '<meta name="description" content="', '" />' ); ?>

	<?php echo c2c_get_post_custom($post->ID, 'Price: ', ' (non-refundable)'); ?>

	<?php echo c2c_get_random_custom('featured_image', '<img src="/wp-content/images/', '" />'); ?>

	<?php echo c2c_get_random_post_custom($post->ID, 'quote', 1, 'Quote: <em>', '</em>'); ?>

	<?php echo c2c_get_custom('related_offsite_links', 
		   'Here\'s a list of offsite links related to this post:<ol><li><a href="',
		   '">Related</a></li></ol>',
		   '',
		   '">Related</a></li><li><a href="'); ?>
	
	<?php echo c2c_get_custom('more_pictures',
		   'Pictures I\'ve taken today:<br /><div class="more_pictures"><img alt="[photo]" src="',
		   '" /></div>',
		   '',
		   '" /> : <img alt="[photo]" src="'); ?>

	Custom 'more...' link text, by replacing <?php the_content(); ?> in index.php with this:
	<?php the_content(c2c_get_custom('more', '<span class="morelink">', '</span>', '(more...)')); ?>
	
*/

/*
Copyright (c) 2004-2009 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation 
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, 
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

include(dirname(__FILE__) . '/get-custom.widget.php');
include(dirname(__FILE__) . '/get-custom.shortcode.php');

// Template tag for use inside "the loop" and applies to the currently listed post
function c2c_get_custom( $field, $before='', $after='', $none='', $between='', $before_last='' ) {
	return c2c__format_custom($field, (array)get_post_custom_values($field), $before, $after, $none, $between, $before_last);
} //end c2c_get_custom()

// Template tag for use on permalink (aka single) page templates for posts and pages
function c2c_get_current_custom( $field, $before='', $after='', $none='', $between='', $before_last='' ) {
	if ( !(is_single() || is_page()) )
		return;
	
	global $wp_query;
	$post_obj = $wp_query->get_queried_object();
	$post_id = $post_obj->ID;
	return c2c__format_custom($field, (array)get_post_custom_values($field, $post_id), $before, $after, $none, $between, $before_last);	
} //end c2c_get_current_custom()

// Template tag for use when you know the ID of the post you're interested in
function c2c_get_post_custom( $post_id, $field, $before='', $after='', $none='', $between='', $before_last='' ) {
	return c2c__format_custom($field, (array)get_post_custom_values($field, $post_id), $before, $after, $none, $between, $before_last);	
} //end c2c_get_post_custom()

// Template tag for use to retrieve a random custom field value
function c2c_get_random_custom( $field, $before='', $after='', $none='' ) {
	global $wpdb;
	$search_passworded_posts = false;  // Change this if you want
	
	$sql = "SELECT postmeta.meta_value FROM $wpdb->postmeta as postmeta 
				LEFT JOIN $wpdb->posts AS posts ON (posts.ID = postmeta.post_id)
				WHERE postmeta.meta_key = %s AND postmeta.meta_value != ''
				AND posts.post_status = 'publish' ";
	if ($search_passworded_posts)
		$sql .= "AND posts.post_password = '' ";
	$sql .= "ORDER BY rand() LIMIT 1";
	$value = $wpdb->get_var( $wpdb->prepare($sql, $field) );
	return c2c__format_custom($field, array($value), $before, $after, $none);	
} //end c2c_get_random_custom()

// Template tag for use to retrieve random custom field value(s) from a post when you know the ID of the post you're interested in
function c2c_get_random_post_custom( $post_id, $field, $limit=1, $before='', $after='', $none='', $between='', $before_last='' ) {
	$cfields = (array)get_post_custom_values($field, $post_id);
	shuffle($cfields);
	if (count($cfields) > $limit)
		$cfields = array_slice($cfields, 0, $limit);
	return c2c__format_custom($field, $cfields, $before, $after, $none, $between, $before_last);
} //end c2c_get_random_post_custom()

// Template tag for use outside "the loop" and applies for custom fields regardless of post
function c2c_get_recent_custom( $field, $before='', $after='', $none='', $between=', ', $before_last='', $limit=1, $unique=false, $order='DESC', $include_pages=true, $show_pass_post=false ) {
	global $wpdb;
	if ( empty($between) ) $limit = 1;
	if ( $order != 'ASC' ) $order = 'DESC';

	$sql = "SELECT ";
	if ( $unique ) $sql .= "DISTINCT ";
	$sql .= "meta_value FROM $wpdb->posts AS posts, $wpdb->postmeta AS postmeta ";
	$sql .= "WHERE posts.ID = postmeta.post_id AND postmeta.meta_key = %s ";
	$sql .= "AND posts.post_status = 'publish' AND ( posts.post_type = 'post' ";
	if ( $include_pages )
		$sql .= "OR posts.post_type = 'page' ";
	$sql .= ') ';
	if ( !$show_pass_post ) $sql .= "AND posts.post_password = '' ";
	$sql .= "AND postmeta.meta_value != '' ";
	$sql .= "ORDER BY posts.post_date $order LIMIT %d";
	$results = array(); $values = array();
	$results = $wpdb->get_results( $wpdb->prepare($sql, $field, $limit) );
	if ( !empty($results) )
		foreach ($results as $result) { $values[] = $result->meta_value; };
	return c2c__format_custom($field, $values, $before, $after, $none, $between, $before_last);
} //end c2c_get_recent_custom()

/* Helper function */
function c2c__format_custom( $field, $meta_values, $before='', $after='', $none='', $between='', $before_last='' ) {
	$values = array();
	if ( empty($between) ) $meta_values = array_slice($meta_values,0,1);
	if ( !empty($meta_values) )
		foreach ($meta_values as $meta) {
			$sanitized_field = preg_replace('/[^a-z0-9_]/i', '', $field);
			$meta = apply_filters("the_meta_$sanitized_field", $meta);
			$values[] = apply_filters('the_meta', $meta);
		}

	if ( empty($values) ) $value = '';
	else {
		$values = array_map('trim', $values);
		if ( empty($before_last) ) $value = implode($values, $between);
		else {
			switch ($size = sizeof($values)) {
				case 1:
					$value = $values[0];
					break;
				case 2:
					$value = $values[0] . $before_last . $values[1];
					break;
				default:
					$value = implode(array_slice($values,0,$size-1), $between) . $before_last . $values[$size-1];
			}
		}
	}
	if ( empty($value) ) {
		if ( empty($none) ) return;
		$value = $none;
	}
	return $before . $value . $after;
} //end c2c__format_custom()

// Some filters you may wish to perform: (these are filters typically done to 'the_content' (post content))
//add_filter('the_meta', 'convert_chars');
//add_filter('the_meta', 'wptexturize');

// Other optional filters (you would need to obtain and activate these plugins before trying to use these)
//add_filter('the_meta', 'c2c_hyperlink_urls', 9);
//add_filter('the_meta', 'text_replace', 2);
//add_filter('the_meta', 'textile', 6);

?>