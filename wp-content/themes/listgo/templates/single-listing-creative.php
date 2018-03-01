<?php
/*
 * Template Name: Creative Layout
 * Template Post Type: listing
 */
get_header();
global $wiloke, $post;
$aSettings = Wiloke::getPostMetaCaching($post->ID, 'listing_settings');
$aOtherSettings = Wiloke::getPostMetaCaching($post->ID, 'listing_other_settings');
$overlayColor = WilokePublic::getSetting('header_overlay', 'listing_header_overlay', $aOtherSettings);
$headerImg = get_the_post_thumbnail_url($post->ID, 'large');
if ( empty($headerImg) ){
	if ( isset($wiloke->aThemeOptions['listing_header_image']['id']) ){
		$headerImg = wp_get_attachment_image_url($wiloke->aThemeOptions['listing_header_image']['id'], 'large');
		$overlayColor = !empty($wiloke->aThemeOptions['listing_header_overlay']) ? $wiloke->aThemeOptions['listing_header_overlay']['rgba'] : '';
	}
}

while (have_posts()) : the_post();

	?>
	<div class="listing-single-hero bg-scroll lazy" data-src="<?php echo esc_url($headerImg); ?>">
		<div class="listing-single-hero__inner">
			<div class="container">
				<div class="row">
					<div class="col-lg-10 col-lg-offset-1">
						<div class="listing-single__header">
							<div class="listing-single__title">
                                <h1><?php the_title(); ?></h1>
                                <?php do_action('wiloke/listgo/single/after_title', $post); ?>
                            </div>
							<?php WilokePublic::postMeta($post); ?>
							<?php WilokePublic::listingAction($post); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
        <div class="overlay" style="background-color:<?php echo esc_attr($overlayColor); ?>"></div>
	</div>

	<div class="listing-single-wrap1">
		<div class="container">
			<div class="row">
				<div class="col-md-8">
					<div class="listing-single">
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