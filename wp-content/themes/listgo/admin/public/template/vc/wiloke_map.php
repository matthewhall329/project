<?php
function wiloke_shortcode_map($atts){
	$atts = shortcode_atts(
		array(
			'latlng' => '',
			'marker' => '',
			'info'   => '',
			'height' => '80vh',
			'extract_class' => '',
			'css' => ''
		),
		$atts
	);

	if ( empty($atts['latlng']) ){
		return WilokeAlert::message(__('You are using the Map Shortcode but Latitude and Longitude - It is required by the shortcode - are empty.', 'listgo'), true);
	}

	ob_start();
	?>

	<div class="listgo-map-wrap">
		<div id="listgo-map-shortcode" class="listgo-map wiloke-listgo-map-shortcode" data-map="<?php echo esc_attr($atts['latlng']); ?>" data-info="<?php echo esc_attr($atts['info']); ?>" style="height: <?php echo esc_attr($atts['height']); ?>" data-marker="<?php echo esc_url(wp_get_attachment_image_url($atts['marker'], 'large')); ?>"></div>
	</div>

	<?php
	$content = ob_get_clean();
	return $content;
}