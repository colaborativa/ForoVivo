<?php
/*
 * Get Custom Field Values plugin widget code
 *
 * Copyright (c) 2004-2009 by Scott Reilly (aka coffee2code)
 *
 */

if ( class_exists('WP_Widget') && !class_exists('GetCustomWidget') ) :
class GetCustomWidget extends WP_Widget {
	var $widget_id = 'get_custom';
	var $title = '';
	var $config = array(
		// input can be 'checkbox', 'multiselect', 'select', 'short_text', 'text', 'textarea', 'hidden', or 'none'
		// datatype can be 'array' or 'hash'
		// can also specify input_attributes
		'title' => array('input' => 'text', 'default' => 'Custom Field',
				'label' => 'Title'),
		'field' => 	array('input' => 'text', 'default' => '',
				'label' => 'Custom field key',
				'help' => '<strong>*Required.</strong>  The name of the custom field key whose value you wish to have displayed.'),
		'this_post' =>	array('input' => 'checkbox', 'default' => false,
				'label' => 'This post?',
				'help' => 'The post containing this shortcode. Takes precedence over \'Post ID\''),
		'post_id' => array('input' => 'short_text', 'default' => '',
				'label' => 'Post ID',
				'help' => 'ID of post whose custom field\'s value you want to display. Leave blank to search for the custom field in any post. Use <code>0</code> to indicate it should only work on the permalink page for a page/post.'),
		'random' =>	array('input' => 'checkbox', 'default' => false,
				'label' => 'Pick random value?'),
		'limit' => array('input' => 'short_text', 'default' => 1,
				'label' => 'Limit',
				'help' => 'The number of custom field items to list. Only applies if Post ID is empty and "Pick random value?" is unchecked.'),
		'before' => array('input' => 'text', 'default' => '',
				'label' => 'Before text',
				'help' => 'Text to display before the custom field.'),
		'after' => 	array('input' => 'text', 'default' => '',
				'label' => 'After text',
				'help' => 'Text to display after the custom field.'),
		'none' => 	array('input' => 'text', 'default' => '',
				'label' => 'None text',
				'help' => 'Text to display if no matching custom field is found (or it has no value).  Leave this blank if you don\'t want anything to display when no match is found.'),
		'between' => array('input' => 'text', 'default' => ', ',
				'label' => 'Between text',
				'help' => 'Text to display between custom field items if more than one are being shown.'),
		'before_last' => array('input' => 'text', 'default' => '',
				'label' => 'Before last text',
				'help' => 'Text to display between the second to last and last custom field items if more than one are being shown.')
	);
	var $defaults = array();

	function GetCustomWidget() {
		$this->title = __('Get Custom Field');
		foreach ( $this->config as $key => $value )
			$this->defaults[$key] = $value['default'];
		$widget_ops = array( 'classname' => 'widget_' . $this->widget_id, 'description' => __('A list of custom field value(s) from posts or pages.') );
		$control_ops = array( 'width' => 300 ); //array( 'width' => 400, 'height' => 350, 'id_base' => $this->widget_id );
		$this->WP_Widget( $this->widget_id, $this->title, $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract($args);

		/* Settings */
		foreach ( array_keys($this->config) as $key )
			$$key = apply_filters('c2c_get_custom_widget_'.$key, $instance[$key]);

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		// Determine, based on inputs given, which template tag to use.
		if ( '0' === $post_id )
			$post_id = 'current';

		if ( $post_id ) {
			if ( 'current' == $post_id )
				echo c2c_get_current_custom($field, $before, $after, $none, $between, $before_last);
			elseif ( $random )
				echo c2c_get_random_post_custom($post_id, $field, $limit, $before, $after, $none, $between, $before_last);
			else
				echo c2c_get_post_custom($post_id, $field, $before, $after, $none, $between, $before_last);				
		} else {
			if ( $random )
				echo c2c_get_random_custom($field, $before, $after, $none);
			else
				echo c2c_get_recent_custom($field, $before, $after, $none, $between, $before_last, $limit);
		}

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		foreach ( $new_instance as $key => $value )
			$instance[$key] = $value;
		if ( !trim($instance['field']) )
			return false;
		return $instance;
	}

	function form( $instance, $exclude_options = array('this_post') ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );	
		$i = $j = 0;
		foreach ( $instance as $opt => $value ) {
			if ( $opt == 'submit' ) continue;
			if ( in_array($opt, $exclude_options) ) continue;
			$input = $this->config[$opt]['input'];
			$label = $this->config[$opt]['label'];
			if ( $input == 'none' ) {
				if ( $opt == 'more' ) {
					$i++; $j++;
					echo "<p>$label</p>";
					echo "<div class='widget-group widget-group-$i'>";
				} elseif ( $opt == 'endmore' ) {
					$j--;
					echo '</div>';
				}
				continue;
			}
			if ( $input == 'checkbox' ) {
				$checked = ($value == 1) ? 'checked=checked ' : '';
				$value = 1;
			} else {
				$checked = '';
			};
			if ( $input == 'multiselect' ) {
				// Do nothing since it needs the values as an array
			} elseif ( $this->config[$opt]['datatype'] == 'array' ) {
				if ( !is_array($value) )
					$value = '';
				else 
					$value = implode(('textarea' == $input ? "\n" : ', '), $value);
			} elseif ( $this->config[$opt]['datatype'] == 'hash' ) {
				if ( !is_array($value) )
					$value = '';
				else {
					$new_value = '';
					foreach ( $value AS $shortcut => $replacement )
						$new_value .= "$shortcut => $replacement\n";
					$value = $new_value;
				}
			}
			echo "<p>";
			$input_id = $this->get_field_id($opt);
			$input_name = $this->get_field_name($opt);
			$value = esc_attr($value);
			if ( $label && ($input != 'multiselect') ) echo "<label for='$input_id'>$label:</label> ";
			if ( $input == 'textarea' ) {
				echo "<textarea name='$input_name' id='$input_id' class='widefat' {$this->config[$opt]['input_attributes']}>" . $value . '</textarea>';
			} elseif ( $input == 'select' ) {
				echo "<select name='$input_name' id='$input_id'>";
				foreach ( $this->config[$opt]['options'] as $sopt ) {
					$selected = $value == $sopt ? " selected='selected'" : '';
					echo "<option value='$sopt'$selected>$sopt</option>";
				}
				echo "</select>";
			} elseif ( $input == 'multiselect' ) {
				echo '<fieldset style="border:1px solid #ccc; padding:2px 8px;">';
				if ( $label ) echo "<legend>$label: </legend>";
				foreach ( $this->config[$opt]['options'] as $sopt ) {
					$selected = in_array($sopt, $value) ? " checked='checked'" : '';
					echo "<input type='checkbox' name='$input_name' id='$input_id' value='$sopt'$selected>$sopt</input><br />";
				}
				echo '</fieldset>';
			} else {
				if ( $input == 'short_text' ) {
					$tclass = '';
					$tstyle = 'width:25px;';
					$input = 'text';
				} else {
					$tclass = 'widefat';
					$tstyle = '';
				}
				echo "<input name='$input_name' type='$input' id='$input_id' value='$value' class='$tclass' style='$tstyle' $checked {$this->config[$opt]['input_attributes']} />";
			}
			if ( $this->config[$opt]['help'] )
				echo "<br /><span style='color:#888; font-size:x-small;'>({$this->config[$opt]['help']})</span>";
			echo "</p>\n";
		}
		// Close any open divs
		for ( ; $j > 0; $j-- ) { echo '</div>'; }
	}

} // end class GetCustomWidget

add_action( 'widgets_init', create_function('', 'register_widget(\'GetCustomWidget\');') );

endif; // end if !class_exists()
?>