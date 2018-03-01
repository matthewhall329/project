<?php
function wiloke_shortcode_heading($atts){
	$atts = shortcode_atts(
		array(
			'blockname'     => 'Landmarks',
			'title'         => 'New Listings',
			'description'   => 'Enter in a description for this block',
			'alignment'     => 'text-left',
			'blogname_color'=> '',
			'title_color'   => '',
			'description_color' => '',
			'css'           => '',
			'extract_class' => ''
		),
		$atts
	);

	$wrapperClass = 'heading-title header-title--1 ' . $atts['alignment'] . ' ' . $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');
	ob_start();
	?>
	<div class="<?php echo esc_attr($wrapperClass); ?>">
		<div class="heading-title__h-group">
			<?php if(!empty($atts['blockname'])) : ?>
			<h4 class="heading-title__subtitle" style="color: <?php echo esc_attr($atts['blogname_color']); ?>"><?php echo esc_html($atts['blockname']); ?></h4>
			<?php endif; ?>
			<?php if(!empty($atts['title'])) : ?>
			<h2 class="heading-title__title" style="color: <?php echo esc_attr($atts['title_color']); ?>"><?php echo esc_html($atts['title']); ?></h2>
			<?php endif; ?>
		</div>
		<?php if (!empty($atts['description'])) : ?>
		<p class="heading-title__description" style="color: <?php echo esc_attr($atts['description_color']); ?>"><?php  Wiloke::wiloke_kses_simple_html($atts['description']); ?></p>
		<?php endif; ?>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}