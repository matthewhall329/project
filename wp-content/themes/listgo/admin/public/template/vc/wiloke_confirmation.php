<?php
function wiloke_shortcode_confirmation($atts) {
	$atts = shortcode_atts(
		array(
			'valid_code'    => '',
			'in_valid_code' => '',
			'extract_class' => '',
			'css'           => ''
		),
		$atts
	);
	$wrapperClass = $atts['extract_class'] . vc_shortcode_custom_css_class($atts['css'], ' ');
	ob_start(); ?>

	<div class="wiloke-listgo-confirmation-wrapper <?php echo esc_attr(trim($wrapperClass)) ?>">
		<?php
			$status = \WilokeListgoFunctionality\Submit\User::checkConfirmationCode();
			if ( $status ){
				\WilokeListgoFunctionality\Frontend\FrontendListingManagement::message(
					array(
						'message' => $atts['valid_code']
					),
					'success'
				);
			}else{
				\WilokeListgoFunctionality\Frontend\FrontendListingManagement::message(
					array(
						'message' => $atts['in_valid_code']
					),
					'danger'
				);
			}
		?>
	</div>
	<?php
	return ob_get_clean();
}