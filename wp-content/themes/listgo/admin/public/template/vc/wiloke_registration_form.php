<?php
function wiloke_shortcode_registration_form($atts){
	$atts = shortcode_atts(
		array(
			'heading'       => esc_html__('Register Now', 'listgo'),
			'username_label'=> esc_html__('User Name', 'listgo'),
			'password_label'=> esc_html__('Password', 'listgo'),
			'email_label'   => esc_html__('Email Address', 'listgo'),
			'btn_text'      => esc_html__('Register', 'listgo'),
			'css'           => '',
			'extract_class' => ''
		),
		$atts
	);

	$wrapperClass = 'listgo-register ' . $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');

	ob_start(); ?>

	<div id="wiloke-sc-signup-signin-wrapper" class="<?php echo esc_attr($wrapperClass); ?>">
		<div class="listgo-register-header">
			<span class="listgo-register-header-line1"></span>
			<span class="listgo-register-header-line2"></span>
			<h4><?php echo esc_html($atts['heading']) ?></h4>
		</div>
		<div class="listgo-register-form">
			<form id="wiloke-shortcode-signup-form" class="signup-form" action="#">
				<p class="print-msg-here error-msg"></p>
                <div class="form-item">
                    <label for="email" class="label"><?php echo esc_html($atts['email_label']) ?> <sup>*</sup></label>
                    <span class="input-text">
                        <input id="email" type="email" name="email" value="" required>
                    </span>
                </div>
				<div class="form-item">
					<label for="username" class="label"><?php echo esc_html($atts['username_label']) ?> <sup>*</sup></label>
					<span class="input-text">
                        <input id="username" type="text" name="username" value="" required>
                    </span>
				</div>
                <div class="form-item">
                    <label for="password" class="label"><?php echo esc_html($atts['password_label']) ?> <sup>*</sup></label>
                    <span class="input-text">
                        <input id="password" type="password" name="password" value="" required>
                    </span>
                </div>
				<?php do_action('wiloke/signup-form/before-submit-button'); ?>
				<div class="form-item">
					<button type="submit" id="signup-btn" class="listgo-btn btn-primary listgo-btn--full signup-btn"><?php echo esc_html($atts['btn_text']); ?></button>
				</div>
				<?php if ( !empty($atts['description']) ) : ?>
					<div class="form-item form-description text-center" style="font-style: italic">
						<?php \Wiloke::wiloke_kses_simple_html($atts['description']); ?>
					</div>
				<?php endif; ?>
			</form>
		</div>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}