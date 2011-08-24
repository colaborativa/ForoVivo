<?php
/**
 * @package WordPress
 * @subpackage Forovivo
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/javascript/prototype.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/javascript/scriptaculous.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/javascript/lightview.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/javascript/curvycorners.js"></script>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/lightview.css">
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>

<?php wp_head(); ?>
</head>
<body>
