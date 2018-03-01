<?php
function wiloke_shortcode_promotion($atts){
	$atts = shortcode_atts(
		array(
			'heading'       => '',
			'desc'          => '',
			'btn_name'      => '',
			'btn_link'      => '',
			'type'          => '',
			'btn_color'     => '',
			'btn_bg_color'  => '',
			'desc_color'    => '',
			'heading_color' => '',
			'bg_color'      => '',
			'css'           => '',
			'extract_class' => ''
		),
		$atts
	);

	$wrapperClass = 'section-promo text-center' . ' ' . $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');

	ob_start(); ?>

	<div class="<?php echo esc_attr($wrapperClass); ?>" style="background-color: <?php echo esc_attr($atts['bg_color']); ?>">

		<?php if ( !empty($atts['heading']) ) :?>
			<h3 style="color: <?php echo esc_attr($atts['heading_color']); ?>"><?php echo esc_html($atts['heading']); ?></h3>
		<?php endif; ?>

		<?php if ( !empty($atts['desc']) ) :?>
			<p style="color: <?php echo esc_attr($atts['desc_color']); ?>"><?php echo esc_html($atts['desc']); ?></p>
		<?php endif; ?>

		<?php if ( !empty($atts['btn_link']) ) : ?>
		<a style="background-color: <?php echo esc_attr($atts['btn_bg_color']); ?>; color: <?php echo esc_attr($atts['btn_color']); ?>" href="<?php echo esc_url($atts['btn_link']); ?>" target="<?php echo esc_attr($atts['type']); ?>" class="listgo-btn btn-black"><?php echo esc_html($atts['btn_name']); ?></a>
		<?php endif; ?>

	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}