<?php
function wiloke_shortcode_boxes_icon($atts){
	$atts = shortcode_atts(
		array(
			'style'							=> 'style1',
			'boxes'                         => '',
			'items_per_row'                 => 'col-md-6',
			'toggle_numerical_order_list'   => 'enable',
			'alignment'                     => 'text-left',
			'icon'                          => '',
			'extract_class'                 => '',
			'css'                           => ''
		),
		$atts
	);

	$class = 'iconbox iconbox-' . $atts['style'] . ' ' . $atts['alignment'];

	if ( empty( $atts['boxes'] ) ){
        return WilokeAlert::message(__('Box Content is empty now. You should create at least one or Remove it', 'listgo'), true);
	}

	if ( $atts['style'] == 'style1' ) {
		$class .= $atts['alignment'] === 'text-right' ? ' iconbox--iconright' : ' iconbox--iconleft';
	}

	$aBoxes = vc_param_group_parse_atts($atts['boxes']);

	$wrapperClass = $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');

	ob_start(); ?>

	<div class="<?php echo esc_attr($wrapperClass); ?>">
	
		<div class="row">

			<?php foreach ( $aBoxes as $order => $aBox ) : ?>

				<div class="<?php echo esc_attr($atts['items_per_row']); ?>">

					<div class="<?php echo esc_attr($class); ?>">

						<span class="iconbox__icon <?php echo esc_attr($aBox['icon']); ?>"></span>

						<div class="<?php echo esc_attr($atts['alignment']); ?> overflow-hidden">

							<h4 class="iconbox__title"><?php if ($atts['toggle_numerical_order_list'] === 'enable') : ?><span><?php $setOrder = absint($order)+1; $setOrder = $setOrder < 10 ? '0'.$setOrder : $setOrder; echo esc_html($setOrder); ?></span><?php endif; ?><?php echo esc_html($aBox['heading']) ?></h4>

							<div class="iconbox__content">
								<p><?php Wiloke::wiloke_kses_simple_html($aBox['description']); ?></p>
							</div>

						</div>

					</div>

				</div>

			<?php endforeach; ?>

		</div>
		
	</div>

	<?php

	$content = ob_get_clean();

	return $content;
}