<?php
use WilokeListgoFunctionality\Frontend\FrontendListingManagement as WilokeFrontendListingManagement;

$pageUrl = get_permalink($post->ID);
?>

<h2 class="author-page__title"><i class="icon_house"></i> <?php esc_html_e('Dashboard', 'listgo'); ?></h2>

<div class="account-dashbroad">
    <?php do_action('wiloke/listgo/wiloke-submission/account/user-dashboard/alert', $oUserInfo); ?>

    <div class="wil-card-group">
        <div class="wil-card wil-card--all-listing">
            <a href="<?php echo esc_url(WilokePublic::addQueryToLink($pageUrl, 'mode=my-listings')); ?>" class="wil-card-blue">
                <div class="wil-card__inner">
                    <span class="wil-card-nubmer"><?php echo WilokeFrontendListingManagement::getTotalPostByStatus('any'); ?></span>
                    <span class="wil-card-title"><?php esc_html_e('All Listings', 'listgo'); ?></span>
                </div>
            </a>
        </div>
        <div class="wil-card wil-card--published">
            <a href="<?php echo esc_url(WilokePublic::addQueryToLink($pageUrl, 'mode=my-listings&status=publish')); ?>" class="wil-card-green">
                <div class="wil-card__inner">
                    <span class="wil-card-nubmer"><?php echo WilokeFrontendListingManagement::getTotalPostByStatus('publish'); ?></span>
                    <span class="wil-card-title"><?php esc_html_e('Published', 'listgo'); ?></span>
                </div>
            </a>
        </div>
        <div class="wil-card wil-card--review">
            <a href="<?php echo esc_url(WilokePublic::addQueryToLink($pageUrl, 'mode=my-listings&status=pending')); ?>" class="wil-in-review">
                <div class="wil-card__inner">
                    <span class="wil-card-nubmer"><?php echo WilokeFrontendListingManagement::getTotalPostByStatus('pending'); ?></span>
                    <span class="wil-card-title"><?php esc_html_e('In Review', 'listgo'); ?></span>
                </div>
            </a>
        </div>
        <div class="wil-card wil-card--unpaid">
            <a href="<?php echo esc_url(WilokePublic::addQueryToLink($pageUrl, 'mode=my-listings&status=processing')); ?>" class="wil-card-orange">
                <div class="wil-card__inner">
                    <span class="wil-card-nubmer"><?php echo WilokeFrontendListingManagement::getTotalPostByStatus('processing'); ?></span>
                    <span class="wil-card-title"><?php esc_html_e('Unpaid', 'listgo'); ?></span>
                </div>
            </a>
        </div>
        <div class="wil-card wil-card--expired ">
            <a href="<?php echo esc_url(WilokePublic::addQueryToLink($pageUrl, 'mode=my-listings&status=expired')); ?>" class="wil-card-pink">
                <div class="wil-card__inner">
                    <span class="wil-card-nubmer"><?php echo WilokeFrontendListingManagement::getTotalPostByStatus('expired'); ?></span>
                    <span class="wil-card-title"><?php esc_html_e('Expired', 'listgo'); ?></span>
                </div>
            </a>
        </div>
    </div>

    <div class="wil-card-total-group">

        <div class="wil-card-total wil-card-total-view">
            <div class="wil-card-total-inner">
                <div class="wil-card-total-content">
                    <span class="wil-card-total-icon"><i class="icon_search"></i></span>
                    <p class="wil-card-total-number">
                        <span><?php echo esc_html(WilokeFrontendListingManagement::getTotalViewedOfAllListings()) ?></span><?php esc_html_e('Total Views', 'listgo'); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="wil-card-total wil-card-total-review">
            <div class="wil-card-total-inner">
                <div class="wil-card-total-content">
                    <span class="wil-card-total-icon"><i class="icon_star_alt"></i></span>
                    <p class="wil-card-total-number">
                        <span><?php echo esc_html(WilokeFrontendListingManagement::getTotalReviewedOfAllListings()); ?></span><?php esc_html_e('Total Reviews', 'listgo'); ?>
                    </p>
                </div>
            </div>
        </div>

    </div>

</div>
