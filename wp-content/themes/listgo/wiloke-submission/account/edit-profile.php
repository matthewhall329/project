<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <h4 class="author-page__title"><i class="icon_pencil-edit"></i> <?php esc_html_e('Edit Profile', 'listgo'); ?></h4>
        <div class="account-page">
            <div class="form form--profile">
                <form method="POST" id="wiloke-listgo-update-profile" action="<?php the_permalink(); ?>">
                    <div class="clearfix"></div>
        
                    <?php
                        $profilePictureID = '';
                        $profilePicture = get_template_directory_uri() . '/img/profile-picture.jpg';
                        if ( isset(WilokePublic::$oUserInfo->meta['wiloke_profile_picture']) && !empty(WilokePublic::$oUserInfo->meta['wiloke_profile_picture']) ){
                            $profilePicture =  wp_get_attachment_image_url(WilokePublic::$oUserInfo->meta['wiloke_profile_picture']);
                            $profilePictureID = WilokePublic::$oUserInfo->meta['wiloke_profile_picture'];
                        }
        
                        $coverImgID = '';
                        $coverImg = '';
                        if ( isset(WilokePublic::$oUserInfo->meta['wiloke_cover_image']) && !empty(WilokePublic::$oUserInfo->meta['wiloke_cover_image']) ){
                            $coverImg =  wp_get_attachment_image_url(WilokePublic::$oUserInfo->meta['wiloke_cover_image'], 'large');
                            $coverImgID = WilokePublic::$oUserInfo->meta['wiloke_cover_image'];
                        }
                    ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="profile-avatar wiloke-js-upload">
                                <?php
                                $avatar = Wiloke::getUserAvatar(WilokePublic::$oUserInfo->ID, null, array(65, 65));
                                $firstCharacter = strtoupper(substr(WilokePublic::$oUserInfo->display_name, 0, 1));
                                $hiddenAvatar = '';
                                $hiddenText = '';
                                if ( strpos($avatar, 'profile-picture.jpg') !== false ) {
                                   $hiddenAvatar = 'hidden';
                                }else{
                                    $hiddenText = 'hidden';
                                }
                                ?>
                                <img src="<?php echo esc_url($avatar); ?>" class="wiloke-preview <?php echo esc_attr($hiddenAvatar); ?>" alt="<?php echo esc_attr(WilokePublic::$oUserInfo->display_name); ?>" height="65" width="65" class="avatar">
                                <span id="wiloke-avatar-by-text" style="background-color: <?php echo esc_attr(WilokePublic::getColorByAnphabet($firstCharacter)); ?>" class="<?php echo esc_attr($hiddenText); ?> widget_author__avatar-placeholder"><?php echo esc_html($firstCharacter); ?></span>
                                <input type="hidden" id="wiloke_profile_picture" class="wiloke-insert-id" name="wiloke_profile_picture" value="<?php echo esc_attr($profilePictureID); ?>">
                                <div class="profile-avatar__change">
                                    <i class="fa fa-camera"></i>
                                    <a href="#"><?php esc_html_e('Change Avatar', 'listgo'); ?></a>
                                </div>
                            </div>
                            <div class="profile-background wiloke-js-upload" data-imgsize="large">
                                <input type="hidden" id="wiloke_cover_image" class="wiloke-insert-id" name="wiloke_cover_image"  value="<?php echo esc_attr($coverImgID); ?>">
                                <img class="wiloke-preview profile-background__placeholder" src="<?php echo esc_url($coverImg); ?>" alt="<?php esc_html_e('Cover Image', 'listgo'); ?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-item">
                                <label for="overlay_color" class="label"><?php esc_html_e('Header Overlay Color', 'listgo'); ?></label>
                                <span class="input-text">
                                    <input id="overlay_color" class="colorpicker" type="text" name="wiloke_color_overlay" value="<?php echo isset(WilokePublic::$oUserInfo->meta['wiloke_color_overlay']) ? esc_attr(WilokePublic::$oUserInfo->meta['wiloke_color_overlay']) : ''; ?>">
                                </span>
                            </div>
                        </div>
        
                        <div class="col-sm-6">
                            <div class="form-item">
                                <label for="first_name" class="label"><?php esc_html_e('First name', 'listgo'); ?></label>
                                <span class="input-text">
                                    <input id="first_name" type="text" name="first_name" value="<?php echo esc_attr(get_user_meta(WilokePublic::$oUserInfo->ID, 'first_name', true)); ?>">
                                </span>
                            </div>
                        </div>
        
                        <div class="col-sm-6">
                            <div class="form-item">
                                <label for="last_name" class="label"><?php esc_html_e('Last name', 'listgo'); ?></label>
                                <span class="input-text">
                                    <input id="last_name" name="last_name" type="text" value="<?php echo esc_attr(get_user_meta(WilokePublic::$oUserInfo->ID, 'last_name', true)); ?>">
                                </span>
                            </div>
                        </div>
        
                        <div class="col-sm-6">
                            <div class="form-item">
                                <label for="nickname" class="label"><?php esc_html_e('Nickname', 'listgo'); ?> <sup>*</sup></label>
                                <span class="input-text">
                                    <input id="nickname" name="nickname" type="text" value="<?php echo esc_attr(WilokePublic::$oUserInfo->user_nicename); ?>" required>
                                </span>
                            </div>
                        </div>
        
                        <div class="col-sm-6">
                            <div class="form-item">
                                <label for="display_name" class="label"><?php esc_html_e('Display Name', 'listgo'); ?> <sup>*</sup></label>
                                <span class="input-text">
                                    <input id="display_name" name="display_name" type="text" value="<?php echo esc_attr(WilokePublic::$oUserInfo->display_name); ?>" required>
                                </span>
                            </div>
                        </div>
        
                        <div class="col-sm-6">
                            <div class="form-item">
                                <label for="address" class="label"><?php esc_html_e('Address', 'listgo'); ?></label>
                                <span class="input-text">
                                    <input id="address" name="address" type="text" value="<?php echo esc_attr(WilokePublic::$oUserInfo->meta['wiloke_address']); ?>">
                                </span>
                            </div>
                        </div>
        
                        <div class="col-sm-6">
                            <div class="form-item">
                                <label for="user_url" class="label"><?php esc_html_e('Website', 'listgo'); ?></label>
                                <span class="input-text">
                                    <input id="user_url" name="user_url" type="text" value="<?php echo isset(WilokePublic::$oUserInfo->user_url) ? esc_url(WilokePublic::$oUserInfo->user_url) : ''; ?>">
                                </span>
                            </div>
                        </div>
        
                        <div class="col-sm-6">
                            <div class="form-item">
                                <label for="wiloke_phone" class="label"><?php esc_html_e('Phone', 'listgo'); ?></label>
                                <span class="input-text">
                                    <input id="wiloke_phone" name="wiloke_phone" type="text" value="<?php echo esc_attr(WilokePublic::$oUserInfo->meta['wiloke_phone']); ?>">
                                </span>
                            </div>
                        </div>
        
                        <div class="col-sm-6">
                            <div class="form-item">
                                <label for="user_email" class="label"><?php esc_html_e('Email', 'listgo'); ?> <sup>*</sup></label>
                                <span class="input-text">
                                    <input id="user_email" name="user_email" type="email" required value="<?php echo esc_attr(WilokePublic::$oUserInfo->user_email); ?>">
                                </span>
                            </div>
                        </div>
        
                        <div class="col-sm-12">
                            <div class="form-item">
                                <label for="description" class="label"><?php esc_html_e('Intro your self', 'listgo'); ?></label>
                                <span class="input-text">
                                    <textarea id="description" name="description" rows="4" cols="10"><?php Wiloke::wiloke_kses_simple_html(WilokePublic::$oUserInfo->description); ?></textarea>
                                </span>
                            </div>
                        </div>
        
                    </div>

                    <?php
                    $aFacebookChat   = get_user_meta(WilokePublic::$oUserInfo->ID, 'wiloke_fb_chat', true);
                    $aFacebookChat = wp_parse_args(
	                    $aFacebookChat,
	                    array(
		                    'fanpageID' => '',
		                    'apiID' => ''
	                    )
                    );
                    ?>
                    <h4 class="profile-title"><?php esc_html_e('Facebook Chat Settings', 'listgo'); ?></h4>
                    <p><a href="https://blog.wiloke.com/setup-facebook-chat-listgo/" target="_blank"><?php esc_html_e('How to setup Facebook Chat?', 'listgo'); ?></a></p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-item">
                                <label for="facebook_api_id" class="label"><?php esc_html_e('Facebook API ID', 'listgo'); ?></label>
                                <span class="input-text">
                                    <input id="facebook_api_id" type="text" name="facebookchat[apiID]" value="<?php echo esc_attr($aFacebookChat['apiID']); ?>">
                                </span>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-item">
                                <label for="facebook_fanpage_id" class="label"><?php esc_html_e('Facebook Fanpage ID', 'listgo'); ?></label>
                                <span class="input-text">
                                    <input id="facebook_fanpage_id" type="text" name="facebookchat[fanpageID]" value="<?php echo esc_attr($aFacebookChat['fanpageID']); ?>">
                                </span>
                            </div>
                        </div>
                    </div>
        
                    <h4 class="profile-title"><?php esc_html_e('Change Password', 'listgo'); ?></h4>
        
                    <div class="row">
        
                        <div class="col-sm-12">
                            <div class="form-item">
                                <label for="current_password" class="label"><?php esc_html_e('Current Password', 'listgo'); ?></label>
                                <span class="input-text">
                                    <input type="password" id="current_password" name="current_password">
                                </span>
                            </div>
                        </div>
        
                        <div class="col-sm-12">
                            <div class="form-item">
                                <label for="new_password" class="label"><?php esc_html_e('New Password', 'listgo'); ?></label>
                                <span class="input-text">
                                    <input id="new_password" type="password" name="new_password">
                                </span>
                            </div>
                        </div>
        
                        <div class="col-sm-12">
                            <div class="form-item">
                                <label for="confirm_new_password" class="label"><?php esc_html_e('Confirm New Password', 'listgo'); ?></label>
                                <span class="input-text">
                                    <input id="confirm_new_password" type="password" name="confirm_new_password">
                                </span>
                            </div>
                        </div>
        
                    </div>

                    <h4 class="profile-title"><?php esc_html_e('Social Networks', 'listgo'); ?></h4>
                    <div class="row">
                        <?php
                        foreach ( WilokeSocialNetworks::$aSocialNetworks as $social ) :
                            if ( $social === 'bloglovin' ){
                                $icon = 'heart';
                            }elseif($social === 'rutube'){
                                $icon = 'video-camera';
                            }elseif($social === 'livejournal'){
                                $icon = 'plane';
                            }else{
                                $icon = $social;
                            }
                            $value = isset(WilokePublic::$oUserInfo->meta['wiloke_user_socials'][$social]) ? WilokePublic::$oUserInfo->meta['wiloke_user_socials'][$social] : '';
                        ?>
                        <div class="col-sm-6">
                            <div class="form-item">
                                <label for="<?php echo esc_attr($social); ?>" class="label"><?php echo esc_html(ucfirst(str_replace('_', ' ', $social))); ?></label>
                                <span class="input-text input-icon-left">
                                    <input id="<?php echo esc_attr($social); ?>" type="text" name="wiloke_user_socials[<?php echo esc_attr($social); ?>]" value="<?php echo esc_url($value); ?>">
                                    <i class="input-icon fa fa-<?php echo esc_attr($icon); ?>"></i>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
        
                    <div class="row">
                    <div class="col-sm-12">
                        <div class="profile-actions">
                            <span class="update-status success-msg"></span>
                            <div class="profile-actions__right">
                                <button id="wiloke-listgo-submit-update-profile" type="submit" class="listgo-btn btn-primary btn-small"><?php esc_html_e('Update', 'listgo'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        
        </div>
    </div>
</div>