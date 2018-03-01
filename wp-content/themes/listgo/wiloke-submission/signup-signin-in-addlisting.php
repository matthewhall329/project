<?php
if ( is_user_logged_in() ){
    return '';
}
global $wiloke;
?>
<div id="wiloke-signup-signin-wrapper">
	<div class="add-listing-group">
		<div class="row">
			<div class="col-sm-12">
				<div class="form-item">
					<label class="input-toggle">
						<input type="checkbox" id="createaccount" name="createaccount">
						<span></span>
						<?php esc_html_e('Create an account?', 'listgo'); ?>
					</label>
				</div>
		        <div class="create-account-fields hidden">
			        <?php if ( isset($wiloke->aThemeOptions['toggle_user_send_user_activation_key']) && ($wiloke->aThemeOptions['toggle_user_send_user_activation_key']=='enable') ) : ?>
                    <div class="form-item">
                        <i><?php esc_html_e('Your account details will be confirmed via email.', 'listgo'); ?></i>
                    </div>
			        <?php endif; ?>
		            <div class="form-item">
		                <label for="wiloke-reg-email" class="label"><?php esc_html_e('Email', 'listgo'); ?> <sup>*</sup></label>
		                <span class="input-text">
			                <input id="wiloke-reg-email" type="email" name="wiloke_reg_email">
			            </span>
		                <span id="wiloke-reg-invalid-email" class="validate-message hidden"><?php esc_html_e('Invalid Email', 'listgo'); ?></span>
		            </div>
                    <div class="form-item">
                        <label for="wiloke-reg-password" class="label"><?php esc_html_e('Password', 'listgo'); ?> <sup>*</sup></label>
                        <span class="input-text">
			                <input id="wiloke-reg-password" type="password" name="wiloke_reg_password">
			            </span>
                        <span id="wiloke-reg-invalid-email" class="validate-message hidden"><?php esc_html_e('We need your password', 'listgo'); ?></span>
                    </div>

			        <?php do_action('wiloke/wiloke-submisison/signup-signin-in-addlisting/before-create-account-form'); ?>
		        </div>
				<div class="signup-account-fields">
		            <div class="form-item validate-required">
		                <span id="wiloke-signup-failured" class="validate-message hidden"></span>
		            </div>

					<div class="form-item">
						<label for="wiloke-user-login" class="label"><?php esc_html_e('Username Or Email Address', 'listgo'); ?> <sup>*</sup></label>
						<span class="input-text">
			                <input id="wiloke-user-login" type="text" name="wiloke_user_login">
			            </span>
		                <span id="wiloke-invalid-user" class="validate-message hidden"><?php esc_html_e('Please enter in your username or your email.', 'listgo'); ?></span>
					</div>
					<div class="form-item password-field">
						<label for="wiloke-my-password" class="label"><?php esc_html_e('Password', 'listgo'); ?> <sup>*</sup></label>
						<span class="input-text">
			                <input id="wiloke-my-password" type="password" name="wiloke_my_password">
			            </span>
					</div>
					<div class="form-item">
						<button id="wiloke-signin-account" type="button" class="listgo-btn btn-primary listgo-btn--sm"><?php esc_html_e('Sign In', 'listgo'); ?></button>
					</div>
				</div>

				<?php do_action('wiloke/wiloke-submission/signup-signin-in-addlisting/after-signup-account'); ?>
			</div>
		</div>
	</div>
</div>