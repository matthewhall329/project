<div class="<?php echo esc_attr($atts['before_item_class'] . ' ' . $termClasses); ?>">
	<div class="wiloke-listgo-listing-item grid-item" data-postid="<?php echo esc_attr($postID); ?>" data-info="<?php echo esc_attr(json_encode($aInfo)); ?>">
		<div class="<?php echo esc_attr($atts['item_class']); ?>">
			<div class="listing__media">
				<?php WilokePublic::renderCouponBadge($post); ?>
				<a class="lazy" href="<?php echo esc_url(get_permalink($postID)); ?>" data-src="<?php echo esc_url($aFeaturedImage['main']['src']); ?>">
					<img src="<?php echo esc_url($aFeaturedImage['main']['src']); ?>" srcset="<?php echo isset($aFeaturedImage['srcset']) ? esc_attr($aFeaturedImage['srcset']) : ''; ?>" alt="<?php echo esc_attr(get_the_title($postID)); ?>"  width="<?php echo esc_attr($aFeaturedImage['main']['width']); ?>" height="<?php echo esc_attr($aFeaturedImage['main']['height']); ?>" />
				</a>

				<?php if ( ($atts['layout'] !== 'listing--list1') ) : ?>
					<div class="listing__cat">
						<?php
						if ( $atts['show_terms'] === 'listing_location' ){
							WilokePublic::renderTaxonomy($postID, 'listing_location');
						}elseif( $atts['show_terms'] === 'listing_cat' ){
							WilokePublic::renderTaxonomy($postID, 'listing_cat');
						}else{
							WilokePublic::renderTaxonomy($postID, array('listing_cat','listing_location'));
						}
						?>
					</div>
				<?php endif; ?>

				<?php
				WilokePublic::renderAuthor($post, $atts);
				WilokePublic::renderFeaturedIcon($post);
				WilokePublic::renderListingStatus($post);
				do_action('wiloke/listgo/wiloke_listing_layout/before_close_listing_media', $post);
				?>
			</div>

			<div class="listing__body">
				<h3 class="listing__title">
					<a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo esc_html($post->post_title); ?></a>
					<?php do_action('wiloke/listgo/admin/public/template/vc/listing-layout/after_title', $post); ?>
				</h3>
				<?php
				WilokePublic::renderAverageRating($post, $atts);
				WilokePublic::renderContent($post, $atts);
				?>
				<?php if ( $atts['layout'] === 'listing--list' || $atts['layout'] === 'listing--grid' ) : ?>
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
				<?php elseif ( $atts['layout'] === 'listing--list1' ) : ?>
					<div class="item__actions">
						<div class="tb">
							<?php
							WilokePublic::renderViewDetail($post, $atts, 'cell-large');
							WilokePublic::renderFavorite($post, $atts);
							?>
						</div>
					</div>
				<?php else: ?>
					<div class="item__actions">
						<div class="tb">
							<?php
							WilokePublic::renderMapPage('s_search='.$post->post_title, $mapPage, $atts, false, 'cell-large');
							WilokePublic::renderViewDetail($post, $atts, 'cell-large');
							WilokePublic::renderFavorite($post, $atts);
							?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
