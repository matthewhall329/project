<?php
use WilokeListgoFunctionality\Frontend\FrontendFollow as WilokeFrontendFollow;

if ( isset($_REQUEST['user']) ){
	$userID = abs($_REQUEST['user']);
}else{
	$userID = WilokePublic::$oUserInfo->ID;
}
global $wiloke;
$aListingFollowers = WilokeFrontendFollow::getFollowers($userID);
?>
<div class="our-member wil-multiple">
    <div class="our-member">
        <div class="row row-clear-lines">
            <?php
            if (!empty($aListingFollowers)) :
                foreach ( $aListingFollowers as $aFollower ) :
                    $avatar = Wiloke::getUserAvatar($aFollower['ID'], $aFollower, 'thumbnail');
                    $followers = WilokePublic::getNumberOfFollowers($aFollower['ID']);
                    $following = WilokePublic::getNumberOfFollowing($aFollower['ID']);
                    ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="member-item">
                            <a href="<?php echo esc_url(get_author_posts_url($aFollower['ID'])); ?>">
                                <div class="member-item__avatar">
                                    <?php
                                    if ( strpos($avatar, 'profile-picture.jpg') === false ) {
                                        Wiloke::lazyLoad($avatar);
                                    }else{
                                        $colorBgKey = array_rand($wiloke->aConfigs['frontend']['color_picker'], 1);
                                        echo '<span style="background-color: '.esc_attr($wiloke->aConfigs['frontend']['color_picker'][$colorBgKey]).'" class="widget_author__avatar-placeholder">'. esc_html(strtoupper(substr($aFollower['display_name'], 0, 1))) .'</span>';
                                    }
                                    ?>
                                </div>
                                <div class="overflow-hidden">
                                    <h5 class="member-item__name"><?php echo esc_html($aFollower['display_name']); ?></h5>
                                    <?php WilokePublic::renderBadge($aFollower['role']); ?>
                                    <p class="member-item__follow">
                                        <span class="followers"><span class="count"><?php echo esc_html($followers); ?></span> <?php echo absint($followers) < 2 ? esc_html__('Follower', 'listgo') : esc_html__('Followers', 'listgo'); ?></span>
                                        <span class="following"><span class="count"><?php echo esc_html($following); ?></span> <?php esc_html_e('Following', 'listgo'); ?></span>
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-md-12">
                    <?php WilokeAlert::render_alert(esc_html__('There are no followers yet.', 'listgo'), 'info', false, false); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>