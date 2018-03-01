<?php
/*
 * Template Name: User Dashboard
 */
use WilokeListGoFunctionality\Frontend\Notification as WilokeNotification;
use WilokeListGoFunctionality\CustomerPlan\CustomerPlan as WilokeCustomerPlan;
get_header();
	WilokePublic::accountHeaderBg();
    $aPaymentSettings = WilokePublic::getPaymentField();
    $myAccount = get_permalink($aPaymentSettings['myaccount']);
    $mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
	?>
	<div class="section-account-nav">
		<div class="container">

			<div class="account-nav">
                <span class="account-nav__toggle">
                    <i class="fa fa-bars"></i>
                    <?php esc_html_e('Dashboards', 'listgo'); ?>
                </span>

				<ul class="account-nav__menu">
                    <?php if ( is_user_logged_in() && ( !isset($_REQUEST['user']) || ( absint($_REQUEST['user']) === WilokePublic::$oUserInfo->ID )) ) :
	                    $totalListings = WilokePublic::totalMyListings();
	                    $totalFavorites = WilokeFavorite::totalMyFavorites();
	                    $totalNotifications = WilokeNotification::countTotalNotifications();
                    ?>
                        <li class="<?php echo empty($mode) ? 'active' : ''; ?>  wiloke-view-dashboard"><a href="<?php echo esc_url($myAccount); ?>"><i class="icon_house_alt"></i> <?php esc_html_e('Dashboard', 'listgo'); ?></a></li>
                        <li class="<?php echo ($mode === 'profile') ? 'active' : ''; ?> wiloke-view-profile"><a href="<?php echo esc_url(WilokePublic::addQueryToLink($myAccount, 'mode=profile')); ?>"><i class="icon_id"></i> <?php esc_html_e('Profile', 'listgo'); ?></a></li>
                        <li class="<?php echo ($mode === 'my-listings') ? 'active' : ''; ?> wiloke-view-mylistings"><a href="<?php echo esc_url(WilokePublic::addQueryToLink($myAccount, 'mode=my-listings')); ?>"><i class="icon_document_alt"></i> <?php esc_html_e('My Listings', 'listgo'); ?> (<?php echo esc_html($totalListings); ?>)</a></li>
                        <li class="<?php echo ($mode === 'notifications') ? 'active' : ''; ?> wiloke-view-notifications"><a href="<?php echo esc_url(WilokePublic::addQueryToLink($myAccount, 'mode=notifications')); ?>"><i class="icon_lightbulb_alt"></i> <?php esc_html_e('Notifications', 'listgo'); ?> <span class="wiloke-notifications-count">(<?php echo esc_html($totalNotifications); ?>)</span></a></li>
                        <li class="<?php echo ($mode === 'my-favorites') ? 'active' : ''; ?> wiloke-view-favorites"><a href="<?php echo esc_url(WilokePublic::addQueryToLink($myAccount, 'mode=my-favorites')); ?>"><i class="icon_heart_alt"></i> <?php esc_html_e('Favorites', 'listgo'); ?> (<?php echo esc_html($totalFavorites); ?>)</a></li>
                        <li class="<?php echo ($mode === 'my-billing') ? 'active' : ''; ?> wiloke-view-billinghistory"><a href="<?php echo esc_url(WilokePublic::addQueryToLink($myAccount, 'mode=my-billing')); ?>"><i class="icon_creditcard"></i> <?php esc_html_e('Billing', 'listgo'); ?></a></li>
                        <?php if ( function_exists('is_woocommerce') ) : ?>
                        <li class="menu-item-has-children wiloke-view-woocommerce">
                            <a href="#">
                                <i class="icon_creditcard"></i>
                                <?php  esc_html_e('Shop', 'listgo'); ?>
                            </a>
                            <ul class="submenu">
                                <li><a href="<?php echo esc_url(wc_get_cart_url()); ?>"><?php echo esc_html__( 'My Cart', 'listgo' ); ?></a></li>
                                <li><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php echo esc_html__( 'Orders', 'listgo' ); ?></a></li>
                                <li><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'downloads' ) ); ?>"><?php echo esc_html__( 'Downloads', 'listgo' ); ?></a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="active"><a href="#"><i class="icon_id"></i> <?php esc_html_e('Profile', 'listgo'); ?></a></li>
                        <?php
	                    $totalListings = count_user_posts($_REQUEST['user'], 'listing');
                        if ($totalListings) :
                        ?>
                        <li><a href="<?php echo esc_url(get_author_posts_url($_REQUEST['user'])); ?>"><i class="icon_documents_alt"></i> <?php echo esc_html($totalListings); ?> <?php echo absint($totalListings) > 1 ? esc_html__('Listings', 'listgo') : esc_html__('Listing', 'listgo'); ?></a></li>
                        <?php endif; ?>
                    <?php endif; ?>
				</ul>

                <?php if ( isset($aPaymentSettings['toggle']) && ($aPaymentSettings['toggle'] == 'enable') ) : ?>
				<div class="account-nav__addlisting">
					<a href="<?php echo esc_url(apply_filters('wiloke-submission/add-listing-btn-url', '')); ?>" class="listgo-btn btn-primary listgo-btn--sm listgo-btn--round"><?php esc_html_e('+ Add Listing', 'listgo'); ?></a>
				</div>
                <?php endif; ?>

			</div>

		</div>
	</div>

	<div class="section-account-page">
		<div class="container">
            <?php
                $dontHavePermissionMsg = esc_html__('You don\'t have permission to access this page', 'listgo');
                switch ($mode){
                    case 'my-listings':
                        if ( is_user_logged_in() ){
	                        include get_template_directory() . '/wiloke-submission/account/listings.php';
                        }else{
                            WilokeAlert::render_alert($dontHavePermissionMsg);
                        }
                        break;
	                case 'following':
		                include get_template_directory() . '/wiloke-submission/account/following.php';
		                break;
	                case 'followers':
		                include get_template_directory() . '/wiloke-submission/account/followers.php';
		                break;
                    case 'edit-profile':
	                    if ( is_user_logged_in() ){
		                    include get_template_directory() . '/wiloke-submission/account/edit-profile.php';
	                    }else{
		                    WilokeAlert::render_alert($dontHavePermissionMsg);
	                    }
                        break;
	                case 'my-favorites':
		                if ( is_user_logged_in() ){
			                include get_template_directory() . '/wiloke-submission/account/favorites.php';
		                }else{
			                WilokeAlert::render_alert($dontHavePermissionMsg);
		                }
		                break;
	                case 'my-billing':
		                if ( is_user_logged_in() ){
			                include get_template_directory() . '/wiloke-submission/account/billing.php';
		                }else{
			                WilokeAlert::render_alert($dontHavePermissionMsg);
		                }
		                break;
	                case 'notifications':
		                if ( is_user_logged_in() ){
			                include get_template_directory() . '/wiloke-submission/account/notifications.php';
		                }else{
			                WilokeAlert::render_alert($dontHavePermissionMsg);
		                }
		                break;
	                case 'profile':
		                if ( isset($_REQUEST['user']) ){
			                $oUserInfo = (object)Wiloke::getUserMeta($_REQUEST['user']);
			                $isViewByMySelf = false;
		                }else{
			                $oUserInfo = WilokePublic::$oUserInfo;
			                $isViewByMySelf = true;
		                }
		                include get_template_directory() . '/wiloke-submission/account/guest-dashboard.php';
		                break;
                    default:
	                    if ( isset($_REQUEST['user']) ){
		                    $oUserInfo = (object)Wiloke::getUserMeta($_REQUEST['user']);
		                    $isViewByMySelf = false;
	                    }else{
		                    $oUserInfo = WilokePublic::$oUserInfo;
		                    $isViewByMySelf = true;
	                    }
	                    global $wiloke;

	                    if ( is_user_logged_in() && ( !isset($_REQUEST['user']) || ($_REQUEST['user'] == get_current_user_id()) ) ){
		                    include get_template_directory() . '/wiloke-submission/account/user-dashboard.php';
	                    }else{
		                    include get_template_directory() . '/wiloke-submission/account/guest-dashboard.php';
	                    }
                        break;
                }
            ?>
		</div>
	</div>
	<?php
get_footer();