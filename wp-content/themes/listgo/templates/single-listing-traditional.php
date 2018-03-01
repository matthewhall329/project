<?php
/*
 * Template Name: Classic Layout
 * Template Post Type: listing
 */
get_header();
global $wiloke, $post;
$aSettings = Wiloke::getPostMetaCaching($post->ID, 'listing_settings');
$headerImg = get_the_post_thumbnail_url($post, 'large');

if ( empty($headerImg) ){
	if ( isset($wiloke->aThemeOptions['listing_header_image']['id']) ){
		$headerImg = wp_get_attachment_image_url($wiloke->aThemeOptions['listing_header_image']['id'], 'large');
		$overlayColor = !empty($wiloke->aThemeOptions['listing_header_overlay']) ? $wiloke->aThemeOptions['listing_header_overlay']['rgba'] : '';
	}
}

while (have_posts()) : the_post();
	?>
	<div class="listing-single-wrap2">
		<div class="container">
			<div class="row">
				<div class="col-md-8">
					<div class="listing-single">

						<div class="listing-single__header">
                            <div class="listing-single__title">
                                <h1><?php the_title(); ?></h1>
								<?php do_action('wiloke/listgo/single/after_title', $post); ?>
                            </div>
							<?php WilokePublic::postMeta($post); ?>
							<?php WilokePublic::listingAction($post); ?>
						</div>
						<div class="listing-single__media">
                            <?php
                            if ( class_exists('Featured_Video_Plus') ){
                                the_post_thumbnail();
                            }else {
	                            Wiloke::lazyLoad($headerImg);
                            }
                            ?>
						</div>

						<div class="tab tab--2 listing-single__tab">
							<?php get_template_part('templates/single-listing-elements/tabs-nav'); ?>
							<?php get_template_part('templates/single-listing-elements/tabs-content'); ?>
						</div>

						<?php if ( Wiloke::$mobile_detect->isMobile() ) : ?>
							<div class="listing-single__sidebar">
		                		<?php get_sidebar('listing'); ?>
		                	</div>
		            	<?php endif;?>


						<?php do_action('wiloke/listgo/single-listing/before_related_post', $post); ?>

                        <!-- Related Posts -->
						<?php WilokePublic::renderRelatedPosts(); ?>
                        <!-- End / Related Posts -->

						<?php
						/*
						 * hooked: renderPaymentEndEditButton
						 */
						do_action('wiloke/listgo/single-listing/after_related_post', $post);
						?>

						<div class="listing-single-bar">
							<div class="container">
								<?php get_template_part('templates/single-listing-elements/tabs-nav'); ?>
								<?php WilokePublic::listingAction($post); ?>
								<?php do_action('wiloke/listgo/templates/single-listing/render_custom_tab_content', $post); ?>
							</div>
						</div>

					</div>
				</div>

				<?php
                	if ( !Wiloke::$mobile_detect->isMobile() ) {
                		get_sidebar('listing');
                	}else{
                	    ?>
                        <div id="listgo-sidebar-placeholder"></div>
                        <?php
                    }
            	?>

			</div>
		</div>
	</div>
	<?php
endwhile; wp_reset_postdata();
get_footer();