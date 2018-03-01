<?php
class WilokeComment{
	public static function comment_template($comment, $args, $depth){
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback':
				// Display trackbacks differently than normal comments.
				?>
				<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
				<p><?php esc_html_e( 'Pingback:', 'listgo' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', 'listgo' ), '<span class="edit-link">', '</span>' ); ?></p>
				<?php
				break;
			default :
				?>
			<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
				<div id="comment-<?php comment_ID(); ?>" class="comment__inner">
					<div class="comment__avatar">
						<?php
						$commentID   = get_comment_ID();
						$oAuthorInfo = get_comment($commentID);
						$avatar = Wiloke::getUserAvatar($oAuthorInfo->user_id);
						?>
						<a href="<?php comment_author_url($commentID); ?>">
							<?php
							if ( strpos($avatar, 'profile-picture.jpg') === false ) {
								Wiloke::lazyLoad($avatar);
							} else {
								$firstCharacter = strtoupper(substr($oAuthorInfo->comment_author, 0, 1));
								echo '<span style="background-color: '.esc_attr(WilokePublic::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
							}
							?>
						</a>
					</div>
					<div class="comment__body">

						<?php echo sprintf('<cite class="comment__name">%1$s</cite>',get_comment_author_link()); ?>

						<?php
						printf( '<span class="comment__date">%1$s</span>',
							/* translators: 1: date, 2: time */
							Wiloke::wiloke_kses_simple_html(sprintf( '%1$s', get_comment_date()), true)
						);
						WilokePublic::renderBadge($oAuthorInfo->user_id);
						?>

						<?php if ( '0' == $comment->comment_approved ) : ?>
							<p><?php esc_html_e( 'Your comment is awaiting moderation.', 'listgo' ); ?></p>
						<?php endif; ?>

						<div class="comment__content">
							<?php comment_text(); ?>
						</div>

						<div class="comment__edit-reply">
							<?php
							comment_reply_link( array_merge( $args, array( 'reply_text' => esc_html__( 'Reply', 'listgo' ), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) );
							if ( current_user_can( 'edit_comment', $comment->comment_ID ) )
							{
								edit_comment_link( esc_html__( 'Edit', 'listgo' ), '', '' );
							}
							?>
						</div><!-- .reply -->

					</div>
				</div>

				<?php
				break;
		endswitch; // end comment_type check
	}
}