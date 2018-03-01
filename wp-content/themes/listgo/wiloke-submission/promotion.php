<?php
/*
 * Template name: Promotion
 */

use WilokeListGoFunctionality\Frontend\FrontendListingManagement;
use WilokeListgoFunctionality\Framework\Helpers\GetSettings;
use WilokeListgoPromotion\Helpers\Repository;
use WilokeListgoFunctionality\Framework\Payment\PaymentConfiguration;
use WilokeListgoPromotion\Register\RegisterPromotionPricing;

global $wiloke;

get_header();
if ( have_posts() ) :
	while (have_posts()) :
		the_post();
		$aPageSettings = Wiloke::getPostMetaCaching($post->ID, 'page_settings');
		WilokePublic::singleHeaderBg($post, $aPageSettings);
		?>
		<div class="page-checkout">
			<div class="container">
				<div class="page-promotion__content">
					<div class="col-lg-10 col-lg-offset-1">
						<div class="account-page account-page-add-listing">
							<div class="clearfix"></div>

							<form id="wiloke-form-preview-listing" class="form-add-listing" method="POST" action="<?php echo esc_url(get_permalink($post->ID)); ?>" data-currency="<?php echo esc_attr(PaymentConfiguration::getCurrencyCode()); ?>" data-currencyposition="<?php echo esc_attr(PaymentConfiguration::getField('currency_position')); ?>">
                                <div id="wiloke-promotion-message"></div>
								<div class="row">
									<div class="col-sm-12">
										<div class="form-item">
											<label for="user-listing" class="label utility__label"><?php esc_html_e('Select your listing', 'listgo'); ?> <sup>*</sup></label>
											<select id="user-listing" name="wiloke_listgo_listing_id" class="wiloke-js-select-listing-ajax utility__select">
                                                <?php
                                                if ( isset($_REQUEST['postID']) && !empty($_REQUEST['postID']) ) :
	                                                $aAvailablePromotion['sidebar-promotion'] = GetSettings::getPostMeta($_REQUEST['postID'], Repository::getConfig('ads-position:sidebar'));
	                                                $aAvailablePromotion['sidebar-searchandtax'] = GetSettings::getPostMeta($_REQUEST['postID'], Repository::getConfig('ads-position:topOfSearchAndTaxPage'));
	                                                $aAvailablePromotion['sidebar-underlistingcontent'] = GetSettings::getPostMeta($_REQUEST['postID'], Repository::getConfig('ads-position:belowListingContent'));
                                                ?>
                                                    <option data-availablepromotion="<?php echo esc_attr(json_encode($aAvailablePromotion)); ?>" value="<?php echo esc_attr($_REQUEST['postID']); ?>" selected="selected"><?php echo get_the_title($_REQUEST['postID']); ?></option>
                                                <?php endif; ?>
                                            </select>
										</div>
									</div>
								</div>

								
								<div class="form-item">
									<label for="user-listing" class="label utility__label"><?php esc_html_e('Promote your listing', 'listgo'); ?> <sup>*</sup></label>
									<?php
										$query = new WP_Query(
											array(
												'post_type'         => RegisterPromotionPricing::$postType,
												'posts_per_page'    => 1,
												'post_status'       => 'publish',
												'order'             => 'DESC'
											)
										);

										if ( $query->have_posts() ){
											while ($query->have_posts()){
												$query->the_post();
												$aSettings = GetSettings::getPostMeta($query->post->ID, 'wiloke_submission_promotion');
                                                do_action('wiloke/listgo/wiloke-submission/promotion', $aSettings, $query->post);
											}
											wp_reset_postdata();
										}
									?>
								</div>

								
								<div class="utility__footer">

									<div class="row">
										<div class="col-sm-12">
											<div class="utility__total">
												<span id="wiloke-listgo-promotion-total"><?php esc_html_e('Total: ', 'listgo'); ?> <span class="print-value">0</span></span>
											</div>
											<div class="utility__action">
												<?php if ( $aGateWays = PaymentConfiguration::getGatewaySettings() ) :  foreach ($aGateWays as $gateway => $aSetting) : ?>
													<a id="wiloke-submission-promote-with-<?php echo esc_attr($gateway); ?>" class="wiloke-submission-promotion listgo-btn listgo-btn--sm btn-primary listgo-btn--round" data-gateway="<?php echo esc_attr($gateway); ?>"><?php echo esc_html($aSetting['text']); ?></a>
												<?php endforeach;  endif; ?>
											</div>
										</div>
										
									</div>
								</div>

							</form>

						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	endwhile;
endif;
wp_reset_postdata();
get_footer();