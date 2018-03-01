<?php
use WilokeListgoFunctionality\Frontend\FrontendListingManagement as WilokeFrontendListingManagement;
?>
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <h2 class="author-page__title">
            <i class="icon_documents_alt"></i> <?php esc_html_e('My listings', 'listgo'); ?>
        </h2>
        <?php
            $postsPerPage = 10;
            $selected = 'all';
            if ( isset($_REQUEST['status']) && !empty($_REQUEST['status']) && ($_REQUEST['status'] !== 'any') ){
                $postStatus = trim($_REQUEST['status']);
	            $selected = $_REQUEST['status'];
            }
            $currentPageUrl = get_permalink($post->ID);
            $query = new WP_Query(
                array(
                    'post_type'         => 'listing',
                    'posts_per_page'    => $postsPerPage,
                    'post_status'       => isset($postStatus) ? $postStatus : array('publish', 'processing', 'pending', 'expired', 'renew', 'temporary_closed'),
                    'author__in'        => array(WilokePublic::$oUserInfo->ID)
                )
            );

            $totalPending = WilokeFrontendListingManagement::getTotalPostByStatus('pending');
            $totalProcessing = WilokeFrontendListingManagement::getTotalPostByStatus('processing');
            $totalExpired = WilokeFrontendListingManagement::getTotalPostByStatus('expired');
            $totalPending = WilokeFrontendListingManagement::getTotalPostByStatus('pending');
            $totalPublished = WilokeFrontendListingManagement::getTotalPostByStatus('publish');
            $totalAny = WilokeFrontendListingManagement::getTotalPostByStatus('any');
	    ?>
        <div id="wiloke-listgo-listing-management" class="account-page" data-postsperpage="<?php echo esc_attr($postsPerPage); ?>">
            <div id="nav-filters" class="nav-filter-dashbroad">
                <a class="<?php echo ($selected=='all') ? 'active' : ''; ?>" href="<?php echo esc_url(WilokePublic::addQueryToLink($currentPageUrl, 'mode=my-listings&status=all')); ?>" data-status="all" data-total="<?php echo esc_attr($totalAny); ?>"><i class="icon_creditcard"></i> <?php esc_html_e('All', 'listgo'); ?> <span>(<?php echo esc_attr($totalAny); ?>)</span></a>
                <a class="<?php echo ($selected=='publish') ? 'active' : ''; ?>" data-total="<?php echo esc_attr($totalPublished); ?>" href="<?php echo esc_url(WilokePublic::addQueryToLink($currentPageUrl, 'mode=my-listings&status=publish')); ?>" data-status="publish"><i class="icon_like"></i> <?php esc_html_e('Published', 'listgo'); ?> <span>(<?php echo esc_attr($totalPublished); ?>)</span></a>
                <a class="<?php echo ($selected=='pending') ? 'active' : ''; ?>" href="<?php echo esc_url(WilokePublic::addQueryToLink($currentPageUrl, 'mode=my-listings&status=pending')); ?>" data-total="<?php echo esc_attr($totalPending); ?>" data-status="pending"><i class="icon_cloud-upload_alt"></i> <?php esc_html_e('In Review', 'listgo'); ?> <span>(<?php echo esc_attr($totalPending); ?>)</span></a>
                <a class="<?php echo ($selected=='processing') ? 'active' : ''; ?>" data-total="<?php echo esc_attr($totalProcessing); ?>" href="<?php echo esc_url(WilokePublic::addQueryToLink($currentPageUrl, 'mode=my-listings&status=processing')); ?>" data-status="processing"><i class="icon_loading"></i> <?php esc_html_e('Unpaid', 'listgo'); ?> <span>(<?php echo esc_attr($totalProcessing); ?>)</span></a>
                <a class="<?php echo ($selected=='expired') ? 'active' : ''; ?>" data-total="<?php echo esc_attr($totalExpired); ?>" href="<?php echo esc_url(WilokePublic::addQueryToLink($currentPageUrl, 'mode=my-listings&status=expired')); ?>" data-status="expired"><i class="icon_lock_alt"></i> <?php esc_html_e('Expired', 'listgo'); ?> <span>(<?php echo esc_attr($totalExpired); ?>)</span></a>
            </div>

            <div id="wiloke-listgo-show-listings" class="f-listings">
	            <?php
                if ( $query->have_posts() ) :
                    $checkoutPage = '#';
                    $pricingPage = '#';
                    if ( empty($aPaymentSettings) || !isset($aPaymentSettings['checkout']) || empty($aPaymentSettings['checkout']) || !isset($aPaymentSettings['package']) || empty($aPaymentSettings['package']) ){
                        WilokeAlert::render_alert( __('You have not configured Check out page. Please go to Pricings -> Settings to complete it. If you do not see Pricing item on the admin menu, it means Wiloke Functionality is disabled, please go to Appearance -> Install Plugins to activate this plugin. Note that the plugin is required by Submission system.', 'listgo') );
                    }else{
                        $checkoutPage = get_permalink($aPaymentSettings['checkout']);
                        $pricingPage = get_permalink($aPaymentSettings['package']);
                    }

                    global $post, $wpdb;
                    $userID = get_current_user_id();
                    $pinnedTop = WilokeFrontendListingManagement::getPinnedToTop($userID);
                    while ( $query->have_posts() ) :
                        $query->the_post();
                        WilokeFrontendListingManagement::renderListingManagementItem($post, $checkoutPage, $pricingPage, $pinnedTop);
                    endwhile;
	            else:
		            WilokeAlert::render_alert( __('You do not have any listing yet. Please click on Add Listing button to create one.', 'listgo'), 'info', false, false );
	            endif; wp_reset_postdata();
	            ?>
            </div>
            <div style="margin-top: 40px;" id="wiloke-listgo-pagination" class="nav-links text-center"></div>
        </div>
    </div>
</div>