=== Get Custom Field Values ===
Contributors: coffee2code
Donate link: http://coffee2code.com/donate
Tags: custom fields, widget, widgets, meta, extra, data, post, posts, page, pages, coffee2code
Requires at least: 2.6
Tested up to: 2.8.2
Stable tag: 3.0.1
Version: 3.0.1

Use widgets or template tags to easily retrieve and control the display of any custom field values/meta data for posts or pages.

== Description ==

Use widgets or template tags to easily retrieve and control the display of any custom field values/meta data for posts or pages.

The power of custom fields gives this plugin the potential to be dozens of plugins all rolled into one.

This plugin allows you to harness the power of custom fields/meta data.  Use the "Get Custom Field" widget, the [custom_field] shortcode (which has a sidebar widget to help you build the shortcode), or one of six template tags to retrieve and display custom fields.  Find a custom field for the current post, a specified post, a recent post, or randomly.  And for the custom field values found by the plugin, you may optionally specify text or HTML to appear before and after the results.  If nothing is found, then nothing is display (unless you define text to appear when no results are found).  If multiple results are found, only the first will be displayed unless you specify a string to be used to join the results together (such as ","), in which case all will be returned.  Visit the Examples section to see how this plugin can be cast in dozens of different ways.

There are six template tags provided by this plugin.  Here they are, with an explanation of when they are appropriate for use:

* `c2c_get_custom()` : Use this inside "the loop" to retrieve a custom field value for a post
* `c2c_get_current_custom()` : This is only available on the permalink post template (single.php) and page template (page.php).  Can be used inside or outside "the loop".  Useful for using custom field to define text you want to include on a post or page's header, footer, or sidebar.
* `c2c_get_post_custom()` : Useful when you know the ID of the post whose custom field value you want.
* `c2c_get_random_custom()` : Retrieve the value of a random instance of the specified custom field key, as long as the field is associated with a published posted, non-passworded post (you can modify a setting in the plugin file to search passworded posts as well).
* `c2c_get_random_post_custom()` : Retrieves the value of random custom field(s) from a post when you know the ID of the post you're interested in.
* `c2c_get_recent_custom()` : Retrieves the most recent (according to the associated post's publish date) value of the specified custom field.

You can filter the custom field values that the plugin would display.  Add filters for '`the_meta`' to filter custom field data (see the end of the code file for commented out samples you may wish to include).  You can also add per-meta filters by hooking '`the_meta_$sanitized_field`'.  `$sanitized_field` is a clean version of the value of `$field` where everything but alphanumeric and underscore characters have been removed.  So to filter the value of the "Related Posts" custom field, you would need to add a filter for '`the_meta_RelatedPosts`'.

== Installation ==

1. Unzip `get-custom.zip` inside the `/wp-content/plugins/` directory, or upload `get-custom.php` to `/wp-content/plugins/`
1. (optional) Add filters for 'the_meta' to filter custom field data (see the end of the plugin file for commented out samples you may wish to include).  And/or add per-meta filters by hooking 'the_meta_$field'
1. Activate the plugin through the 'Plugins' admin menu in WordPress
1. Give post(s) a custom field with a value.
1. (optional) Go to the Appearance -> Widgets admin page to create one or more 'Get Custom Field' sidebar widgets for your widget-enabled theme.
1. (optional) Use one of the six template functions provided by this plugin to retrieve the contents of custom fields.  You must 'echo' the result if you wish to display the value on your site.

== Template Tags ==

The plugin provides six optional template tags for use in your theme templates.

= Functions =

* `<?php function c2c_get_custom( $field, $before='', $after='', $none='', $between='', $before_last='' ) ?>`
Template tag for use inside "the loop" and applies to the currently listed post.

* `<?php function c2c_get_current_custom( $field, $before='', $after='', $none='', $between='', $before_last='' ) ?>`                                                           
Template tag for use on permalink (aka single) page templates for posts and pages.

* `<?php function c2c_get_post_custom( $post_id, $field, $before='', $after='', $none='', $between='', $before_last='' ) ?>`                                                           
Template tag for use when you know the ID of the post you're interested in.

* `<?php function c2c_get_random_custom( $field, $before='', $after='', $none='' ) ?>`
Template tag for use to retrieve a random custom field value.

* `<?php function c2c_get_random_post_custom( $post_id, $field, $limit=1, $before='', $after='', $none='', $between='', $before_last='' ) ?>`
Template tag for use to retrieve random custom field value(s) from a post when you know the ID of the post you're interested in.

* `<?php function c2c_get_recent_custom( $field, $before='', $after='', $none='', $between=', ', $before_last='', $limit=1, $unique=false, $order='DESC', $include_pages=true, $show_pass_post=false )  ?>`
Template tag for use outside "the loop" and applies for custom fields regardless of post.

= Arguments =

* `$post_id`
Required argument (only used in `c2c_get_post_custom()`).  The ID of the post from which the custom field should be obtained.

* `$field`
Required argument.  The custom field key of interest.

* `$before`
Optional argument.  The text to display before all field value(s).

* `$after`
Optional argument.  The text to display after all field value(s).

* `$none`
Optional argument.  The text to display in place of the field value should no field value exists; if defined as '' and no field value exists, then nothing (including no `$before` and `$after`) gets displayed.

* `$between`
Optional argument.  The text to display between multiple occurrences of the custom field; if defined as '', then only the first instance will be used.

* `$before_last`
Optional argument.  The text to display between the next-to-last and last items listed when multiple occurrences of the custom field; `$between` MUST be set to something other than '' for this to take effect.

Arguments that only apply to `c2c_get_recent_custom()`:

* `$limit`
Optional argument.  The limit to the number of custom fields to retrieve. (also used by `c2c_get_random_post_custom()`)

* `$unique`
Optional argument.  Boolean ('true' or 'false') to indicate if each custom field value in the results should be unique.

* `$order`
Optional argument.  Indicates if the results should be sorted in chronological order ('ASC') (the earliest custom field value listed first), or reverse chronological order ('DESC') (the most recent custom field value listed first).

* `$include_pages`
Optional argument.  Boolean ('true' or 'false') to indicate if pages should be included when retrieving recent custom values; default is 'true'.

* `$show_pass_post`
Optional argument.  Boolean ('true' or 'false') to indicate if password protected posts should be included when retrieving recent custom values; default is 'false'.

= Examples =

* `<?php echo c2c_get_custom('mymood'); ?>  // with this simple invocation, you can echo the value of any metadata field`

* `<?php echo c2c_get_custom('mymood', 'Today's moods: ', '', ', '); ?>`

* `<?php echo c2c_get_recent_custom('mymood', 'Most recent mood: '); ?>`

* `<?php echo c2c_get_custom('mymood', '(Current mood: ', ')', ''); ?>`

* `<?php echo c2c_get_custom('mylisten', 'Listening to : ', '', 'No one at the moment.'); ?>`

* `<?php echo c2c_get_custom('myread', 'I\'ve been reading ', ', if you must know.', 'nothing'); ?>`

* `<?php echo c2c_get_custom('todays_link', '<a class="tlink" href="', '" >Today\'s Link</a>'); ?>`

* `<?php echo c2c_get_current_custom('meta_description', '<meta name="description" content="', '" />' ); ?>`

* `<?php echo c2c_get_post_custom($post->ID, 'Price: ', ' (non-refundable)'); ?>`

* `<?php echo c2c_get_random_custom('featured_image', '<img src="/wp-content/images/', '" />'); ?>`

* `<?php echo c2c_get_random_post_custom($post->ID, 'quote', 1, 'Quote: <em>', '</em>'); ?>`

* `<?php echo c2c_get_custom('related_offsite_links', 
	   'Here\'s a list of offsite links related to this post:<ol><li><a href="',
	   '">Related</a></li></ol>',
	   '',
	   '">Related</a></li><li><a href="'); ?>`

* `<?php echo c2c_get_custom('more_pictures',
	   'Pictures I\'ve taken today:<br /><div class="more_pictures"><img alt="[photo]" src="',
	   '" /></div>',
	   '',
	   '" /> : <img alt="[photo]" src="'); ?>`

* Custom 'more...' link text, by replacing `<?php the_content(); ?>` in index.php with this: `<?php the_content(c2c_get_custom('more', '<span class="morelink">', '</span>', '(more...)')); ?>`

== Frequently Asked Questions ==

= I added the template tag to my template and the post has the custom field I'm asking for but I don't see anything about it on the page; what gives? =

Did you `echo` the return value of the function, e.g. `<?php echo c2c_get_custom('mood', 'My mood: '); ?>`

= Can I achieve all the functionality allowed by the six template functions using the widget? =

Except for `c2c_get_custom()` (which is only available inside "the loop"), yes, by carefully setting the appropriate settings for the widget.

= How do I configure the widget to match up with the template functions? =

* `c2c_get_custom()` : not achievable via the widget
* `c2c_get_current_custom()` : set the "Post ID" field to `0`, leave "Pick random value?" unchecked, and set other values as desired.
* `c2c_get_post_custom()` : set the "Post ID" field to the ID of the post you want to reference and set other values as desired.
* `c2c_get_random_custom()` : leave "Post ID" blank, check "Pick random value?", and set other values as desired.
* `c2c_get_random_post_custom()` : set the "Post ID" field to the ID of the post you want to reference, check "Pick random value?", and set other values as desired.
* `c2c_get_recent_custom()` : leave "Post ID" blank and set other values as desired.

= Why can't I see the widget or shortcode builder as mentioned in the features listing? =

Those features are only available if you are running WordPress 2.8 or later.

== Screenshots ==

1. Screenshot of the plugin's widget configuration.
1. Screenshot of the plugin's shortcode builder.

== Changelog ==

= 3.0.1 =
* Added additional check to prevent error when running under WP older than 2.8

= 3.0 =
* Added widget support (widgetized the plugin)
* Added shortcode support ([custom_field])
* Added c2c_get_post_custom() : Useful when you know the ID of the post whose custom field value you want.
* Added c2c_get_random_custom() : Retrieve the value of a random instance of the specified custom field key, as long as the
field is associated with a published posted, non-passworded post
* Added c2c_get_random_post_custom() : Retrieve the value of a random instance of the specified custom field key for a given
post
* Added c2c_get_recent_custom() : Retrieves the most recent (according to the associated post's publish date) value of
the specified custom field.
* Used $wpdb->prepare() to safeguard queries
* Updated copyright
* Noted compatibility through 2.8+
* Dropped compatibility with versions of WP older than 2.6
* Tweaked description and docs

= 2.5 =
* Modified SQL query code for c2c_get_recent_custom() to explicitly look for post_type of 'post' and then optionally of 'page'
* Per-custom field filter name is now made using a sanitized version of the field key
* Minor code reformatting
* Removed pre-WP2.0 compatibility and compatibility checks
* Changed description
* Updated copyright date and version to 2.5
* Added readme.txt
* Tested compatibility with WP 2.3.3 and 2.5

= 2.1 =
* Removed the $filter argument from c2c_get_custom() and c2c_get_recent_custom()
* Replaced $filter argument with more robust filtering approach: filter every custom field via the action 'the_meta', filter specific custom fields via 'the_meta_$field'
* Add argument $include_static (defaulted to true) to c2c_get_recent_custom(); static posts (i.e. "pages") can be optionally excluded from consideration
* Verified to work for WP 1.5 (and should still work for WP 1.2)

= 2.02 =
* Minor bugfix

= 2.01 =
* Minor bugfix

= 2.0 =
* Added the new function c2c_get_recent_custom() that allows retrieving custom/meta data from outside "the loop"
* Better filtering (on meta field itself instead of final output string)
* Per-call filtering of meta fields
* Prepended all functions with "c2c_" to avoid potential function name collision with other plugins or future core functions. NOTE: If you are upgrading from an earlier version of the plugin, you'll need to change your calls from get_custom() to c2c_get_custom()
* Changes to make the plugin WordPress v1.3 ready (as-yet unverified)
* Switched to MIT license

= 1.0 =
* Added argument of $before_last (which, when $between is also defined, will be used to join the next-to-last and last items in a list)
* Added invocation of an action called 'the_meta' so that you can do add_filter('the_meta', 'some_function') and get custom field values filtered as they are retrieved
* To faciliate use of this plugin as the argument to another function, this plugin no longer echoes the value(s) it retrieves (user must prepend 'echo' to the call to get_custom())

= 0.91 =
* Minor bugfix

= 0.9 =
* Initial release