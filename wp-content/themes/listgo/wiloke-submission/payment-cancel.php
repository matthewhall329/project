<?php
/*
 * Template name: Payment Cancel
 * This template displays the cancel message
 *
 * @since 1.0
 * @author Wiloke
 * @link https://wiloke.com
 * @package: Wiloke/Themes
 * @subpackage: Listgo
 */

get_header();
if ( have_posts() ){
	while (have_posts()){
		the_post();
		$aPageSettings = Wiloke::getPostMetaCaching($post->ID, 'page_settings');
		WilokePublic::singleHeaderBg($post, $aPageSettings);
		?>
		<div class="container">
			<?php the_content(); ?>
		</div>
		<?php
	}

	do_action( 'wiloke/payment/cancelled' );
}
get_footer();