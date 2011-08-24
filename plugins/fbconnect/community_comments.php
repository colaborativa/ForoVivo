<?php 
		$comments = WPfbConnect_Logic::get_community_comments(5);
?>
<?php if ($comments) : ?>

	<ol class="commentlist">
	<?php global $comment;?>
	<?php foreach ($comments as $comment) : ?>

		<li <?php echo $oddcomment; ?>id="comment-<?php comment_ID() ?>">
			<?php echo get_avatar( $comment, 32 ); ?>
			<b style="font-size: 175%;"><a href="<?php echo get_permalink($comment->comment_post_ID) ?>" rel="bookmark" title="Permanent Link to <?php echo $comment->post_title;?>"><?php echo $comment->post_title;?></a></b>
			<br/>
			<cite><?php _e('Sent by', 'fbconnect'); ?> <?php comment_author_link() ?></cite>
			<small class="commentmetadata"><a href="<?php echo get_permalink($comment->comment_post_ID) ?>#comment-<?php comment_ID() ?>" title=""><?php comment_date('F jS, Y') ?> at <?php comment_time() ?></a> <?php edit_comment_link('edit','&nbsp;&nbsp;',''); ?></small>
			
			<?php comment_text() ?>

		</li>

	<?php
		/* Changes every other comment to a different class */
		$oddcomment = ( empty( $oddcomment ) ) ? 'class="alt" ' : '';
	?>

	<?php endforeach; /* end for each comment */ ?>

	</ol>
<?php endif; ?>