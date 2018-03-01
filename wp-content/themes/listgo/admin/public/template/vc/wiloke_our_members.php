<?php
function wiloke_shortcode_our_members($atts){
	$atts = shortcode_atts(
		array(
			'roles'             => '',
			'members_per_row'   => 'col-sm-6 col-lg-4',
            'extract_class' => '',
            'css' => ''
		),
		$atts
	);

	$wrapperClass = 'our-member' . ' ' . $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');
	ob_start();
	?>
	<div class="<?php echo esc_attr(trim($wrapperClass)); ?>">
		<div class="row row-clear-lines">
            <?php
            if ( !Wiloke::$wilokePredis || Wiloke::$wilokePredis->exists(WilokeUser::$redisKey) ) {
                if ( !empty($atts['roles']) ){
                    $aUsers = get_users(
                        array(
                            'role__in' => explode(',', $atts['roles'])
                        )
                    );
                }else{
                    $aUsers = get_users();
                }

                foreach ($aUsers as $oUser){
                    $aUser['ID'] = $oUser->ID;
                    $aUser['display_name'] = $oUser->display_name;
                    $aUser['role'] = isset($oUser->roles[0]) ? $oUser->roles[0] : 'subscriber';
                    wiloke_shortcode_render_member($aUser, $atts);
                }
            }else{
                if ( !empty($atts['roles']) ){
                    $aRoles = explode(',', $atts['roles']);
                    foreach ( $aRoles as $role ){
                        $aUsers = Wiloke::$wilokePredis->hGetAll(WilokeUser::$redisRoles.'|'.$role);
                        foreach ( $aUsers as $aUser ){
                            if ( !empty($aUser) ){
                                $aUser = json_decode($aUser, true);
                                wiloke_shortcode_render_member($aUser, $atts, true);
                            }
                        }
                    }
                }else{
                    $aUsers = Wiloke::$wilokePredis->hGetAll(WilokeUser::$redisKey);
                    foreach ( $aUsers as $aUser ){
                        if ( !empty($aUser) ){
                            $aUser = json_decode($aUser, true);
                            wiloke_shortcode_render_member($aUser, $atts, true);
                        }
                    }
                }
            }
            ?>
		</div>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

function wiloke_shortcode_render_member($aUser, $atts, $isRedis=false){
    global $wiloke;
    $followers = WilokePublic::getNumberOfFollowers($aUser['ID']);
    $following = WilokePublic::getNumberOfFollowing($aUser['ID']);
    $avatar = $isRedis ? Wiloke::getUserAvatar($aUser['ID'], $aUser, 'thumbnail') : Wiloke::getUserAvatar($aUser['ID'], '', 'thumbnail');
    $totalArticle = count_user_posts($aUser['ID'], 'listing');
    ?>
    <div class="<?php echo esc_attr($atts['members_per_row']); ?>">
        <div class="member-item">
            <a href="<?php echo esc_url(get_author_posts_url($aUser['ID'])); ?>">
                <div class="member-item__avatar">
                    <?php
                    if ( strpos($avatar, 'profile-picture.jpg') === false ) {
	                    Wiloke::lazyLoad($avatar);
                    }else{
                        $firstCharacter = strtoupper(substr(trim($aUser['display_name']), 0, 1));
	                    echo '<span style="background-color: '.esc_attr(WilokePublic::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
                    }
                    ?>
                </div>
                <div class="overflow-hidden">
                    <h5 class="member-item__name"><?php echo esc_html($aUser['display_name']); ?></h5>
                    <?php WilokePublic::renderBadge($aUser['role']); ?>
                    <p class="member-item__total">
	                    <?php if ( !empty($totalArticle) ) : ?>
                            <span class="total-posts"><i class="icon_pencil-edit"></i> <?php echo esc_html($totalArticle) . ' ' . ($totalArticle > 1 ? esc_html__('Articles', 'listgo') : esc_html__('Article', 'listgo')); ?></span>
	                    <?php endif; ?>
                    </p>
                    <p class="member-item__follow">
                        <span class="followers"><span class="count"><?php echo esc_html($followers); ?></span> <?php echo absint($followers) < 2 ? esc_html__('Follower', 'listgo') : esc_html__('Followers', 'listgo'); ?></span>
                        <span class="following"><span class="count"><?php echo esc_html($following); ?></span> <?php esc_html_e('Following', 'listgo'); ?></span>
                    </p>
                </div>
            </a>
        </div>
    </div>
    <?php
}