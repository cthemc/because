<?php
/**
 * The template for displaying Comments
 *
 * The area of the page that contains comments and the comment form.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<?php do_action('output_social_poll_action'); ?>

<?php global $post; ?>
<div id="comments" class="comments-area sp_comments-area">

	<?php if ( have_comments() ) : ?>

	<h2 class="comments-title">
       <?php
			printf( _n( 'One thought on &ldquo;%2$s&rdquo;', '<span class="comment_count">%1$s</span> thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'twentyfourteen' ), number_format_i18n( get_comments_number() ), get_the_title() );
		?>
	</h2>

	<div id="sp_comments_order" data-post_id="<?php echo $post->ID; ?>">Orderby 
    	<a href="javascript:void(0)" class="sp_orderby_param" data-sortby="newest" data-nonce="<?php echo wp_create_nonce("comment_sorting_nonce") ?>">Newest</a> | 
        <a href="javascript:void(0)" class="sp_orderby_param" data-sortby="oldest" data-nonce="<?php echo wp_create_nonce("comment_sorting_nonce") ?>">Oldest</a> |
        <a href="javascript:void(0)" class="sp_orderby_param" data-sortby="top" data-nonce="<?php echo wp_create_nonce("comment_sorting_nonce") ?>">Top</a> 
    </div>	<?php //.sp_comments_order ?>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'twentyfourteen' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'twentyfourteen' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'twentyfourteen' ) ); ?></div>
	</nav><!-- #comment-nav-above -->
	<?php endif; // Check for comment navigation. ?>

<?php /*?>	<ol class="comment-list">
		<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
				'avatar_size'=> 34,
				'callback' => spComments::sp_comment_callback($comment, $args, $depth)
			) );
			
			
			$args = array(
				'style'      => 'ol',
				'short_ping' => true,
				'avatar_size'=> 34,
			);
		  //wp_list_comments('type=comment&callback='.spComments::sp_comment_callback($comment, $args, $depth));
			
		?>
	</ol><!-- .comment-list -->
<?php */?>
	
		<ol class="sp_comment-list">
			<?php wp_list_comments('type=comment&callback=mytheme_comment&style=ol'); ?>
		</ol>
	

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Comment navigation', 'twentyfourteen' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'twentyfourteen' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'twentyfourteen' ) ); ?></div>
	</nav><!-- #comment-nav-below -->
	<?php endif; // Check for comment navigation. ?>

	<?php if ( ! comments_open() ) : ?>
	<p class="no-comments"><?php _e( 'Comments are closed.', 'twentyfourteen' ); ?></p>
	<?php endif; ?>

	<?php endif; // have_comments() ?>

	<?php comment_form(); ?>

</div><!-- #comments -->
