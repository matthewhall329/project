<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */

if ( is_singular('listing') ){
    get_template_part('review-form');
	return false;
}

?>
<div id="comments" class="comments">
	<?php if (post_password_required()) : ?>
    <p><?php esc_html_e( 'Post is password protected. Enter the password to view any comments.', 'listgo' ); ?></p>
</div>
<?php return; endif; ?>
<?php if ( have_comments() ) : ?>
    <div class="comments-inner-wrap">
        <div class="comments__header">
            <h4 class="comment__title">
                <?php WilokePublic::renderComment(); ?>
                <a href="#respond" class="comments__header-create"><?php  if ( comments_open() || post_type_supports( get_post_type(), 'comments' ) ) : ?><?php esc_html_e('Leave a comment', 'listgo'); ?><?php endif; ?></a>
            </h4>
        </div>

        <ol class="commentlist">
			<?php
			wp_list_comments(
				array(
					'callback' => array('WilokeComment', 'comment_template'),
					'max_depth'=>3
				)
			);
			?>
        </ol>

        <div class="comment-navigation">
            <div class="alignleft"><?php previous_comments_link() ?></div>
            <div class="alignright"><?php next_comments_link() ?></div>
        </div>
    </div>
<?php endif; ?>
<?php if ( !comments_open() || !post_type_supports( get_post_type(), 'comments' ) ) : ?>
</div>
<?php else : ?>
</div>
<!-- LEAVER YOUR COMMENT -->
<?php
    $commenter = wp_get_current_commenter();
    $commenter['comment_author'] = $commenter['comment_author'] == '' ? '': $commenter['comment_author'];
    $commenter['comment_author_email'] = $commenter['comment_author_email'] == '' ? '': $commenter['comment_author_email'];
    $commenter['comment_author_url'] = $commenter['comment_author_url'] == '' ? '': $commenter['comment_author_url'];

    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );
    $sao      = ( $req ? " <sup>*</sup>" : '' );

    $fields = array(
        'author' => '<p class="col-sm-6"><label for="author">'.esc_html__('Your name', 'listgo').$sao.'</label><input type="text" id="author" class="required-field" name="author" tabindex="1" value="'.esc_attr($commenter['comment_author']).'" ' . $aria_req . ' /></p>',
        'email'  => '<p class="col-sm-6"><label for="email">'.esc_html__('Your Email', 'listgo').$sao.'</label><input type="text" id="email" class="required-field" name="email" value="'.esc_attr($commenter['comment_author_email']).'" ' . $aria_req . ' /></p>'
    );

    $comment_field = '<p class="col-sm-12"><label for="message">'.esc_html__('Comment', 'listgo').$sao.'</label><textarea id="comment" class="required-field" name="comment" rows="5" cols="10" required></textarea></p>';

    $comment_args = array(
        'fields'                => $fields,
        'title_reply'           => esc_html__( 'Leave your comment', 'listgo' ),
        'comment_field'         => $comment_field,
        'comment_notes_after'   => '',
        'comment_notes_before'  => '',
        'submit_field'          => '<p class="col-sm-12">%1$s %2$s</p>',
        'logged_in_as'          => '<p class="col-sm-12 form-login-logout">' . Wiloke::wiloke_kses_simple_html(sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'listgo' ), get_edit_user_link(), $user_identity, wp_logout_url( apply_filters( 'the_permalink', esc_url(get_permalink( $post->ID ) ) ) ) ), true) . '</p>'
    );

    comment_form($comment_args);
?>
<?php endif; ?>