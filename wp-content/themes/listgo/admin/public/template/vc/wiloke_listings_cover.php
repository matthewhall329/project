<?php
function wiloke_shortcode_listings_cover($atts){
	$atts = shortcode_atts(
		array(
			'get_listings_by'       => 'listing_location',
			'icon'                  => '',
			'heading_background'    => '',
			'listing_cat'           => '',
			'listing_location'      => '',
			'posts_per_page'        => 5,
			'extract_class'         => '',
			'css'                   => ''
		),
		$atts
	);
	$wrapperClass = 'categories-box ' . ' ' . $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');
	$wrapperClass = trim($wrapperClass);
	$imgBg = !empty($atts['heading_background']) ? wp_get_attachment_image_url($atts['heading_background'], 'medium') : '';

	$aArgs = array(
		'post_type'         => 'listing',
		'posts_per_page'    => $atts['posts_per_page'],
		'post_status'       => 'publish'
	);

	if ( $atts['get_listings_by'] === 'listing_location' ){
		$aArgs['tax_query'][] = array(
			'taxonomy' => 'listing_location',
			'field'    => 'term_id',
			'terms'    => $atts['listing_location']
		);
		$oTerm = Wiloke::getTermCaching('listing_location', $atts['listing_location']);
	}else{
		$aArgs['tax_query'][] = array(
			'taxonomy' => 'listing_cat',
			'field'    => 'term_id',
			'terms'    => $atts['listing_cat']
		);
		$oTerm = Wiloke::getTermCaching('listing_location', $atts['listing_location']);
	}
	$query = new WP_Query($aArgs);
	ob_start();
	if ( $query->have_posts() ) :
	?>
	<div class="<?php echo esc_attr($wrapperClass); ?>">
		<div class="categories-box__header tb bg-scroll" style="background-image: url(<?php echo esc_url($imgBg); ?>)">
			<div class="tb__cell">
				<a href="<?php echo esc_url($oTerm->link); ?>"><span class="categories-box__icon"><i class="<?php echo esc_attr($atts['icon']); ?>"></i></span><span class="categories-box__name"><?php echo esc_attr($oTerm->name); ?></span></a>
			</div>
		</div>
		<ul class="categories-box__sub">
			<?php
			while ( $query->have_posts() ){
				$query->the_post();
				?>
				<li><a href="<?php echo esc_url(get_permalink($query->post->ID)); ?>"><?php echo esc_html($query->post->post_title); ?></a></li>
				<?php
			}
		?>
		</ul>
	</div>
	<?php
	endif; wp_reset_postdata();
	$content = ob_get_clean();
	return $content;
}