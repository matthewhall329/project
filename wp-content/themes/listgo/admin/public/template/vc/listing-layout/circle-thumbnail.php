<div class="<?php echo esc_attr($atts['before_item_class'] . ' ' . $termClasses); ?>">
	<div class="wiloke-listgo-listing-item listing listing--list2" data-postid="<?php echo esc_attr($postID); ?>" data-info="<?php echo esc_attr(json_encode($aInfo)); ?>">
		<div class="listing__media">
			<?php WilokePublic::renderCouponBadge($post); ?>
			<div class="listing__media-cat">
				<a class="lazy bg-scroll" href="<?php echo esc_url(get_permalink($postID)); ?>" data-src="<?php echo esc_url($aFeaturedImage['main']['src']); ?>">
					<img src="<?php echo esc_url($aFeaturedImage['main']['src']); ?>" srcset="<?php echo isset($aFeaturedImage['srcset']) ? esc_attr($aFeaturedImage['srcset']) : ''; ?>" alt="<?php echo esc_attr(get_the_title($postID)); ?>"  width="<?php echo esc_attr($aFeaturedImage['main']['width']); ?>" height="<?php echo esc_attr($aFeaturedImage['main']['height']); ?>" />
				</a>

				<?php
					if ( $atts['show_terms'] === 'listing_location' ){
						WilokePublic::renderTaxonomy($postID, 'listing_location', true);
					}else{
						WilokePublic::renderTaxonomy($postID, 'listing_cat', true);
					}
				?>
			</div>

			<?php WilokePublic::renderAuthor($post, $atts); ?>
			<?php WilokePublic::renderAverageRating($post, array('toggle_render_rating'=>'enable')); ?>
		</div>

		<div class="listing__body">
			<h3 class="listing__title">
                <a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo esc_html($post->post_title); ?></a>
                <?php do_action('wiloke/listgo/admin/public/template/vc/listing-layout/after_title', $post); ?>
            </h3>
			<?php WilokePublic::renderPriceSegment($post); ?>
			<?php WilokePublic::renderListingStatus($post); ?>
			<?php WilokePublic::renderContent($post, array('toggle_render_post_excerpt'=>false, 'toggle_render_address'=>'enable')); ?>

			<div class="item__actions">
				<div class="tb">
					<?php
					WilokePublic::renderViewDetail($post, $atts, 'cell-large');
					WilokePublic::renderMapPage('s_search='.$post->post_title, $mapPage, $atts, true);
					WilokePublic::renderFindDirection($aPageSettings, $atts);
					WilokePublic::renderFavorite($post, $atts);
					?>
				</div>
			</div>
		</div>
	</div>
</div>