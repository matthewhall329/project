<?php
/*
 * Template name: Checkout
 */
global $wiloke;
use WilokeListgoFunctionality\Framework\Helpers\Render;
use WilokeListgoFunctionality\Framework\Payment\PaymentConfiguration;

get_header();
    while(have_posts()) : the_post();
        $aPageSettings = Wiloke::getPostMetaCaching($post->ID, 'page_settings');
        WilokePublic::singleHeaderBg($post, $aPageSettings); ?>
        <div class="page-checkout">
            <div class="container">
                <div class="page-checkout__content">
                    <h3><?php esc_html_e('Your package', 'listgo'); ?></h3>
                    <?php $aPackageInfo = \WilokeListgoFunctionality\Framework\Helpers\GetSettings::getPostMeta($_REQUEST['package_id'], get_post_type($_REQUEST['package_id'])); ?>
                    <input type="hidden" id="post_id" name="post_id" value="<?php echo isset($_REQUEST['post_id']) ? esc_attr($_REQUEST['post_id']) : ''; ?>">
                    <input type="hidden" id="wiloke-package-id" name="package_id" value="<?php echo isset($_REQUEST['package_id']) ? esc_attr($_REQUEST['package_id']) : ''; ?>">
                    <div class="table-responsive">
                        <table class="table page-checkout__table">
                            <tr>
                                <th><?php esc_html_e('Package', 'listgo'); ?></th>
                                <th><?php esc_html_e('Regular Price', 'listgo'); ?></th>
                                <th><?php esc_html_e('Listing availability', 'listgo'); ?></th>
                                <th><?php esc_html_e('Number of listings', 'listgo'); ?></th>
                                <th><?php esc_html_e('Publish on map', 'listgo'); ?></th>
                                <th><?php esc_html_e('Add ribbon to listing', 'listgo'); ?></th>
                                <th><?php esc_html_e('Add Gallery To Sidebar', 'listgo'); ?></th>
                                <th><?php esc_html_e('Embed Video', 'listgo'); ?></th>
                                <th><?php esc_html_e('Flexible Listing Style', 'listgo'); ?></th>
                                <th><?php esc_html_e('Embed Open Table', 'listgo'); ?></th>
                            </tr>
                            <tr>
                                <td><?php echo esc_html(get_the_title($_REQUEST['package_id'])); ?></td>
                                <td><?php Render::price($aPackageInfo['price']); ?></td>
                                <td><?php echo empty($aPackageInfo['duration']) ? esc_html__('Unlimited availability', 'listgo') : esc_html($aPackageInfo['duration']); ?></td>
                                <td><?php echo empty($aPackageInfo['number_of_posts']) ? esc_html__('Unlimited listings', 'listgo') : esc_html($aPackageInfo['number_of_posts']); ?></td>
                                <td><?php echo ($aPackageInfo['publish_on_map']==='enable') ? esc_html__('Enable', 'listgo') : esc_html__('Disable', 'listgo'); ?></td>
                                <td><?php echo ($aPackageInfo['toggle_add_feature_listing']==='enable') ? esc_html__('Enable', 'listgo') : esc_html__('Disable', 'listgo'); ?></td>
                                <td><?php echo ($aPackageInfo['toggle_allow_add_gallery']==='enable') ? esc_html__('Enable', 'listgo') : esc_html__('Disable', 'listgo'); ?></td>
                                <td><?php echo ($aPackageInfo['toggle_allow_embed_video']==='enable') ? esc_html__('Enable', 'listgo') : esc_html__('Disable', 'listgo'); ?></td>
                                <td><?php echo ($aPackageInfo['toggle_listing_template']==='enable') ? esc_html__('Enable', 'listgo') : esc_html__('Disable', 'listgo'); ?></td>
                                <td><?php echo ($aPackageInfo['toggle_open_table']==='enable') ? esc_html__('Enable', 'listgo') : esc_html__('Disable', 'listgo'); ?></td>
                            </tr>
                        </table>
                    </div>

                    <div class="fr">
                        <h3><?php esc_html_e('Proceed to checkout', 'listgo'); ?></h3>
                        <input type="hidden" name="planID" id="wiloke-planID" value="<?php echo esc_attr($_REQUEST['package_id']); ?>">
	                    <?php if ( $aGateWays = PaymentConfiguration::getGatewaySettings() ) :  foreach ($aGateWays as $gateway => $aSetting) : ?>
                            <a id="wiloke-proceed-with-<?php echo esc_attr($gateway); ?>" class="wiloke-proceed-checkout listgo-btn listgo-btn--sm listgo-btn--round" data-gateway="<?php echo esc_attr($gateway); ?>"><?php echo esc_html($aSetting['text']); ?></a>
	                    <?php endforeach;  endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    endwhile;
    wp_reset_postdata();
get_footer();