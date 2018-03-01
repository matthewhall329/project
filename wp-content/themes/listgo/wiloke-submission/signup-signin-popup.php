<!-- Modal -->
<?php global $wiloke; ?>
<div class="wil-modal wil-modal--fade" id="modal-login">
    <div class="wil-modal__wrap">
        <div class="wil-modal__content">
            <div class="login-register">
                <div class="tab tab--form">
                    <ul id="signup-signin-navtab" class="tab__nav">
                        <li class="active"><a href="#signin"><?php esc_html_e('Sign In', 'listgo'); ?></a></li>
                        <li><a href="#signup"><?php esc_html_e('Sign Up', 'listgo'); ?></a></li>
                    </ul>
                    <div id="signup-signin-wrapper" class="tab__content">
                        <div id="signin" class="tab__panel active">
                            <form id="wiloke-popup-signin-form" class="form signin-form" action="#">
                                <p class="print-msg-here error-msg"></p>
                                <div class="form-item">
                                    <label for="username" class="label"><?php esc_html_e('Username or Email Address', 'listgo'); ?> <sup>*</sup></label>
                                    <span class="input-text">
                                        <input id="username" name="userlogin" type="text" required>
                                    </span>
                                </div>
                                <div class="form-item">
                                    <label for="password" class="label"><?php esc_html_e('Password', 'listgo'); ?> <sup>*</sup></label>
                                    <span class="input-text">
                                        <input id="password" name="password" type="password" required>
                                    </span>
                                </div>
                                <div class="form-item">
                                    <label for="remember-login" class="input-checkbox">
                                        <input id="remember-login" name="remember" type="checkbox" value="yes"><span></span>
                                        <?php esc_html_e('Remember me?', 'listgo'); ?>
                                    </label>
                                    <a href="#lostpassword" class="fr color-primary switch-tab"><ins><?php esc_html_e('Lost your password', 'listgo'); ?></ins></a>
                                </div>
                                <div class="form-item">
                                    <button id="signin-btn" class="listgo-btn btn-primary listgo-btn--full signin-btn" type="submit"><?php esc_html_e('Sign In', 'listgo'); ?></button>
                                </div>
                                <div class="form-item">
                                    <?php esc_html_e('Don\'t have an account?', 'listgo'); ?> <a href="#signup" class="fr color-primary switch-tab"><ins><?php esc_html_e('Register', 'listgo'); ?></ins></a>
                                </div>
                            </form>
                        </div>
                        <div id="signup" class="tab__panel">
                            <form id="wiloke-popup-signup-form" class="form signup-form" action="#">
                                <p class="print-msg-here error-msg"></p>
	                            <?php if ( isset($wiloke->aThemeOptions['toggle_user_send_user_activation_key']) && ($wiloke->aThemeOptions['toggle_user_send_user_activation_key']=='enable') ) : ?>
                                    <div class="form-item">
                                        <i><?php esc_html_e('Your account details will be confirmed via email.', 'listgo'); ?></i>
                                    </div>
	                            <?php endif; ?>
                                <div class="form-item">
                                    <label for="popup-email" class="label"><?php esc_html_e('Email', 'listgo'); ?> <sup>*</sup></label>
                                    <span class="form__input">
                                        <input id="popup-email" name="email" type="email" required>
                                    </span>
                                </div>
                                <div class="form-item">
                                    <label for="popup-username" class="label"><?php esc_html_e('Username', 'listgo'); ?> <sup>*</sup></label>
                                    <span class="form__input">
                                        <input id="popup-username" name="username" type="text" required>
                                    </span>
                                </div>
                                <div class="form-item">
                                    <label for="popup-password" class="label"><?php esc_html_e('Password', 'listgo'); ?> <sup>*</sup></label>
                                    <span class="form__input">
                                        <input id="popup-password" name="password" type="password" required>
                                    </span>
                                </div>
                                <?php if ( !empty($wiloke->aThemeOptions['sign_in_desc']) ) : ?>
                                <div class="form-item desc">
                                    <?php Wiloke::wiloke_kses_simple_html($wiloke->aThemeOptions['sign_in_desc']); ?>
                                </div>
                                <?php endif; ?>
                                <?php do_action('wiloke/signup-form/before-submit-button'); ?>
                                <div class="form-item">
                                    <button type="submit" id="signup-btn" class="listgo-btn btn-primary listgo-btn--full signup-btn"><?php esc_html_e('Sign Up', 'listgo'); ?></button>
                                </div>

                                <div class="form-item">
                                    <?php esc_html_e('Already have an account?', 'listgo'); ?> <a href="#signin" class="fr color-primary switch-tab"><ins><?php esc_html_e('Sign In', 'listgo'); ?></ins></a>
                                </div>

                            </form>

                        </div>
                        <div id="lostpassword" class="tab__panel">
                            <form id="recoverpassword-form" action="#" class="form">
                                <p class="print-msg-here error-msg"></p>
                                <div class="form-item">
                                    <label for="user_login" class="label"><?php esc_html_e('Username or Email Address', 'listgo'); ?> <sup>*</sup></label>
                                    <span class="form__input">
                                        <input id="user_login" name="user_login" type="text">
                                    </span>
                                </div>
                                <div class="form-item">
                                    <button type="submit" id="recoverpassword" class="listgo-btn btn-primary listgo-btn--full"><?php esc_html_e('Reset Password', 'listgo'); ?></button>
                                </div>
                                <div class="form-item">
                                    <a href="#signin" class="fr color-primary switch-tab"><ins><?php esc_html_e('Cancel', 'listgo'); ?></ins></a>
                                </div>
                            </form>
                        </div>
                        <div class="account__switch signup-signin-with-social">
                            <p class="print-msg-here error-msg"></p>
                            <?php
                                if ( class_exists('WilokeLoginWithSocial') ) {
                                    echo do_shortcode('[wiloke_twitter_login]');
                                    echo do_shortcode('[wiloke_facebook_login]');
                                    echo do_shortcode('[wiloke_google_login]');
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wil-modal__close"><?php esc_html_e('Close', 'listgo'); ?></div>
        </div>
    </div>
    <div class="wil-modal__overlay"></div>
</div>
<!-- End / Modal -->