<?php
use WilokeListGoFunctionality\Register\RegisterBadges as WilokeBadges;

function wiloke_shortcode_our_team($atts, $content=null){
	$atts = shortcode_atts(
		array(
			'content'       => '',
			'cover_image'   => '',
			'roles'         => '',
			'get_members_by'=> 'roles',
			'member_ids'    => '',
			'extract_class' => '',
			'css' => ''
		),
		$atts
	);

	$wrapperClass = 'wil-team ' . ' ' . $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');
    $aUsers = array();
    $isRedis = false;

	if ( $atts['get_members_by'] === 'roles' ){

		if ( !Wiloke::$wilokePredis || !Wiloke::$wilokePredis->exists(WilokeUser::$redisKey) ){
			if ( !empty($atts['roles']) ){
				$oGetUsers = get_users(
					array(
						'role__in' => explode(',', $atts['roles'])
					)
				);
			}else{
				$oGetUsers = get_users();
			}

			foreach ($oGetUsers as $oUser){
				$aUser['ID'] = $oUser->ID;
				$aUser['display_name'] = $oUser->display_name;
				$aUser['role'] = isset($oUser->roles[0]) ? $oUser->roles[0] : 'subscrible';
				$aUsers[$oUser->ID] = $aUser;
			}
		}else{
			if ( !empty($atts['roles']) ){
				$aRoles = explode(',', $atts['roles']);
				foreach ( $aRoles as $role ){
					$oGetUsers = Wiloke::$wilokePredis->hGetAll(WilokeUser::$redisRoles.'|'.$role);
					foreach ( $oGetUsers as $aUser ){
						if ( !empty($aUser) ){
							$aUser = json_decode($aUser, true);
							$aUsers[$aUser['ID']] = $aUser;
						}
					}
				}
			}else{
				$aUsers = Wiloke::$wilokePredis->hGetAll(WilokeUser::$redisKey);
				foreach ( $aUsers as $aUser ){
					if ( !empty($aUser) ){
						$aUser = json_decode($aUser, true);
						$aUsers[$aUser['ID']] = $aUser;
					}
				}
			}
			$isRedis = true;
		}
    }else{
		if ( !Wiloke::$wilokePredis || !Wiloke::$wilokePredis->exists(WilokeUser::$redisKey) ){
			if ( !empty($atts['member_ids']) ){
				$oGetUsers = get_users(
					array(
						'include' => explode(',', $atts['member_ids'])
					)
				);
			}else{
				$oGetUsers = get_users();
			}

			foreach ($oGetUsers as $oUser){
				$aUser['ID'] = $oUser->ID;
				$aUser['display_name'] = $oUser->display_name;
				$aUser['role'] = isset($oUser->roles[0]) ? $oUser->roles[0] : 'subscrible';
				$aUsers[$oUser->ID] = $aUser;
			}
		}else{
			if ( !empty($atts['member_ids']) ){
				$aMembersIDs = explode(',', $atts['member_ids']);
				foreach ( $aMembersIDs as $memberID ){
					$aUser = Wiloke::$wilokePredis->hGet(WilokeUser::$redisKey, $memberID);
                    $aUser = json_decode($aUser, true);
                    $aUsers[$aUser['ID']] = $aUser;
				}
			}else{
				$oGetUsers = Wiloke::$wilokePredis->hGetAll(WilokeUser::$redisKey);
				foreach ( $oGetUsers as $aUser ){
					if ( !empty($aUser) ){
						$aUser = json_decode($aUser, true);
						$aUsers[$aUser['ID']] = $aUser;
					}
				}
			}

			$isRedis = true;
		}
    }

	ob_start();
    if ( !empty($content) || !empty($atts['cover_image']) ) :
	?>
    <div class="row equal-height">
        <div class="col-md-6">
            <div class="about__textblock">
                <div class="textblock">
                    <?php Wiloke::wiloke_kses_simple_html($content); ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">

        	<?php 

        		$image  = wp_get_attachment_image_url( $atts['cover_image'], 'large');

				if ($image): ?>
		            <div class="wil-image bg-scroll" style="background-image: url(<?php echo esc_url($image) ?>)"></div>
            		<?php 
            	endif 
            ?>

        </div>
    </div>
    <?php endif; ?>

	<div class="<?php echo esc_attr(trim($wrapperClass)); ?>">
		<div class="wil-team-preview">
            <div class="wil-team__carousel owl-carousel">
                <?php
                    foreach ( $aUsers as $aUser ){
	                    wiloke_shortcode_our_team_render_left_item($atts, $aUser, $isRedis);
                    }
                ?>
            </div>
        </div>

        <div class="wil-team-list">
            <div class="wil-team__list">
	            <?php
	            foreach ( $aUsers as $aUser ){
		            wiloke_shortcode_our_team_render_right_item($atts, $aUser, $isRedis);
	            }
	            ?>
            </div>
        </div>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

function wiloke_shortcode_our_team_render_left_item($atts, $aUser, $isRedis=false){
	$avatar = $isRedis ? Wiloke::getUserAvatar(null, $aUser, array(300, 300)) : Wiloke::getUserAvatar($aUser['ID'], '', array(300, 300));
	$aRoleInfo = WilokeBadges::getBadgeInfo($aUser['role']);
    ?>
    <div class="wil-team__preview" data-hash="wil-team-<?php echo esc_attr($aUser['ID']); ?>">

        <div class="wil-team__preview-avatar bg-scroll lazy" data-src='<?php echo esc_url($avatar); ?>'>
            <img class="owl-lazy" data-src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($aUser['display_name']); ?>">
        </div>

        <h6 class="wil-team__preview-name"><a href="<?php echo esc_url(get_author_posts_url($aUser['ID'])); ?>"><?php echo esc_html($aUser['display_name']); ?></a></h6>
        <span class="wil-team__preview-work"><a href="<?php echo esc_url(get_author_posts_url($aUser['ID'])); ?>"><?php echo esc_attr($aRoleInfo['label']); ?></a></span>
        <?php if ( !empty($aUser['meta']['wiloke_user_socials']) ) : ?>
        <hr>
        <div class="wil-team__preview-social">
            <?php foreach ( $aUser['meta']['wiloke_user_socials'] as $icon => $link ) :
                if ( empty($link) ){
                    continue;
                }
            ?>
            <a href="<?php echo esc_url($link); ?>"><i class="fa fa-<?php echo esc_attr($icon); ?>"></i></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

function wiloke_shortcode_our_team_render_right_item($atts, $aUser, $isRedis=false){
	$avatar = $isRedis ? Wiloke::getUserAvatar(null, $aUser, array(300, 300)) : Wiloke::getUserAvatar($aUser['ID'], '', array(300, 300));
    ?>
    <div class="wil-team__item">
        <a href="#wil-team-<?php echo esc_attr($aUser['ID']); ?>">
            <div class="wil-team__item-avatar bg-scroll lazy" data-src="<?php echo esc_url($avatar); ?>">
                <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($aUser['display_name']); ?>">
            </div>
            <span class="wil-team__item-view"><i class="icon_search"></i></span>
        </a>
    </div>
    <?php
}