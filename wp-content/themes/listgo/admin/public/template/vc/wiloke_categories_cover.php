<?php
function wiloke_shortcode_categories_cover($atts){
	$atts = shortcode_atts(
		array(
			'heading'               => '',
			'icon'                  => '',
			'heading_background'    => '',
			'listing_cat'           => '',
			'listing_location'      => '',
			'extract_class'         => '',
			'css'                   => ''
		),
		$atts
	);
	$wrapperClass = 'categories-box ' . ' ' . $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');
	$wrapperClass = trim($wrapperClass);
	$imgBg = !empty($atts['heading_background']) ? wp_get_attachment_image_url($atts['heading_background'], 'medium') : '';

	$atts['listing_location'] = !empty($atts['listing_location']) ? explode(',', $atts['listing_location']) : '';
	$atts['listing_cat'] = !empty($atts['listing_cat']) ? explode(',', $atts['listing_cat']) : '';

	ob_start();
	?>
	<div class="<?php echo esc_attr($wrapperClass); ?>">
		<div class="categories-box__header tb bg-scroll" style="background-image: url(<?php echo esc_url($imgBg); ?>)">
			<div class="tb__cell">
				<span class="categories-box__icon"><i class="<?php echo esc_attr($atts['icon']); ?>"></i></span><span class="categories-box__name"><?php echo esc_attr($atts['heading']); ?></span>
			</div>
		</div>
		<ul class="categories-box__sub">
			<?php
			if ( !empty($atts['listing_cat']) ){
				foreach ( $atts['listing_cat'] as $termID ){
					$oCatInfo = Wiloke::getTermCaching('listing_cat', $termID);

					if ( !empty($oCatInfo) && !is_wp_error($oCatInfo) ){
						?>
						<li><a href="<?php echo esc_url($oCatInfo->link); ?>"><?php echo esc_html($oCatInfo->name); ?> <span class="count">(<?php echo esc_attr($oCatInfo->count); ?>)</span></a></li>
						<?php
					}
				}
			}

			if ( !empty($atts['listing_location']) ){
				foreach ( $atts['listing_location'] as $termID ){
					$oCatInfo = Wiloke::getTermCaching('listing_location', $termID);
					if ( !empty($oCatInfo) && !is_wp_error($oCatInfo) ){
						?>
						<li><a href="<?php echo esc_url($oCatInfo->link); ?>"><?php echo esc_html($oCatInfo->name); ?> <span class="count">(<?php echo esc_attr($oCatInfo->count); ?>)</span></a></li>
						<?php
					}
				}
			}
			?>
		</ul>

	</div>
	<?php
	$content = ob_get_clean();
	return $content;
}