<?php
use WilokeListgoFunctionality\Submit\User as WilokeUser;
use WilokeListgoFunctionality\Frontend\FrontendListingManagement as WilokeFrontEndManagement;


global $WilokeListGoFunctionalityApp, $wpdb;
?>
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
		<h2 class="author-page__title">
			<i class="icon_documents_alt"></i> <?php esc_html_e('Billing', 'listgo'); ?>
		</h2>

        <!-- Recurring Area -->
		<?php
		$postsPerPage = 100;
		if ( !\WilokeListgoFunctionality\Framework\Payment\PaymentConfiguration::isNonRecurringPayment() ) :
			$aUserPaymentInfo = \WilokeListgoFunctionality\Model\UserModel::getLatestPlanByPlanType('pricing');

			$currentGateway = '';
			$remainingItems = '';
			$planID = '';
		    if ( !empty($aUserPaymentInfo) ){
			    $aPackageInfo = \WilokeListgoFunctionality\Framework\Helpers\GetSettings::getPostMeta($aUserPaymentInfo['planID'], get_post_type($aUserPaymentInfo['planID']));

			    $currentGateway = !empty($aPackageInfo['price']) ? $aUserPaymentInfo['gateway'] : 'free';
			    $remainingItems = $aUserPaymentInfo['remainingItems'];
			    $planID         = $aUserPaymentInfo['planID'];
            }

            $aArgs = array(
                'post_type'     => 'pricing',
                'post_status'   => 'publish'
            );

		    $aCustomerPlans = \WilokeListgoFunctionality\Framework\Payment\PaymentConfiguration::getCustomerPlans();
            if ( $aCustomerPlans ){
                $aArgs['post__in'] = $aCustomerPlans;
                $aArgs['orderby'] = 'post__in';
            }else{
                $aArgs['posts_per_page'] = -1;
            }
			$aGateways = \WilokeListgoFunctionality\Framework\Payment\PaymentConfiguration::getGatewaySettings(array('banktransfer'));

            $query = new WP_Query($aArgs);
            if ( $query->have_posts() ) :
		?>
            <div id="wiloke-my-subscription-plan-wrapper" class="account-page">
                <h4><?php esc_html_e('My Subscription', 'listgo'); ?><span class="wiloke-my-plan-name"><?php echo !empty($planID) ? get_the_title($planID) : esc_html__('No Plan', 'listgo'); ?></span> - <?php esc_html_e('Remaining Listing(s)', 'listgo') ?> <span class="wiloke-my-plan-name"><?php echo $remainingItems > 1000 ? esc_html__('Unlimited', 'listgo') : esc_html($remainingItems); ?></span></h4>
                <?php if ( isset($_REQUEST['status']) && ($_REQUEST['status'] === 'changed_plan') && isset($_REQUEST['payment_method']) && ($_REQUEST['payment_method'] === 'paypal') && ( isset($_REQUEST['token']) && ( Wiloke::getSession(WilokeFrontEndManagement::$latestTokenKey) !== $_REQUEST['token']) ) ) : ?>
                <div id="wiloke-transaction-message" class="wil-alert wil-alert-has-icon" data-token="<?php echo esc_attr($_REQUEST['token']); ?>">
                    <span class="wil-alert-icon"><i class="icon_error-triangle_alt"></i></span>
                    <p class="wil-alert-message"><?php esc_html_e('Updating Your Business Plan ... ', 'listgo'); ?></p>
                </div>
                <?php endif; ?>

                <div id="wiloke-failed-change-plan" class="wil-alert wil-alert-has-icon alert-danger hidden">
                    <span class="wil-alert-icon"><i class="icon_error-triangle_alt"></i></span>
                    <p class="wil-alert-message"></p>
                </div>
                <div id="wiloke-success-change-plan" class="wil-alert wil-alert-has-icon alert-success hidden">
                    <span class="wil-alert-icon"><i class="icon_box-checked"></i></span>
                    <p class="wil-alert-message"></p>
                </div>

                <div class="billing-row">

                    <div class="billing-left">
                        <?php
                        if ( $currentGateway == 'banktransfer' ) :
                            WilokeFrontEndManagement::message(
                                array(
                                    'message' => esc_html__('Auto-change plan is not available for Direct Bank Transfer. To downgrade/upgrade your plan, please contact the site manager.', 'wiloke')
                                ),
                                'info'
                            );
                        else:
                        ?>
                        <form id="wiloke-modify-subscription-plan-form" action="" method="POST">
                            <p>
                                <label for="wiloke-submission-customer-plan"><?php esc_html_e('Want to Modify Your Plan?', 'listgo'); ?></label>
                                <select name="wiloke_submission_customer_plan" id="wiloke-submission-customer-plan">
                                    <?php if ( empty($planID) ) : ?>
                                    <option value="">---</option>
                                    <?php endif; ?>
                                    <?php
                                        while ($query->have_posts()) :
                                            if ( empty($planID) ){
                                                $planID = $query->post->ID;
                                            }
                                            $query->the_post();
                                    ?>
                                            <option value="<?php echo esc_attr($query->post->ID); ?>" <?php selected($planID, $query->post->ID); ?>><?php echo esc_html($query->post->post_title); ?></option>
                                    <?php
                                        endwhile;
                                    ?>
                                </select>
                            </p>
                            <p>
                                <label for="wiloke-submission-proceed-with"><?php esc_html_e('Proceed with', 'listgo'); ?></label>
                                <select name="wiloke_submission_proceed_with" id="wiloke-submission-proceed-with">
                                    <?php if ( !empty($aGateways) ) : ?>
                                        <?php foreach ($aGateways as $gateway => $aInfo)  : ?>
                                            <option value="<?php echo esc_attr($gateway); ?>" <?php selected($gateway, $currentGateway); ?>><?php echo esc_html($aInfo['text']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </p>
                            <button type="submit" class="listgo-btn btn-primary listgo-btn--sm"><?php esc_html_e('Update Plan', 'listgo'); ?></button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <div class="billing-right">
                        <div id="wiloke-show-package-detail">
                            <?php echo do_shortcode('[wiloke_pricing specify_ids="'.$planID.'" is_check_billing_type="no"]'); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; wp_reset_postdata(); endif; ?>
        <!-- End / Recurring Area -->

        <!-- Billing History -->
        <?php
        $aInvoicesInfo = \WilokeListgoFunctionality\Frontend\FrontendBilling::getInvoices(array(
	        'posts_per_page' => $postsPerPage
        ));
        ?>
        <div id="wiloke-listgo-my-billing" class="account-page" data-total="<?php echo esc_attr($aInvoicesInfo['total']); ?>" data-postsperpage="<?php echo esc_attr($postsPerPage); ?>" style="padding: 40px;">
            <h4><?php esc_html_e('Billing History', 'listgo'); ?></h4>
            <?php if ( !empty($aInvoicesInfo['total']) ) : ?>
                <table class="table">
                    <thead>
	                    <?php \WilokeListgoFunctionality\Frontend\FrontendBilling::renderInvoiceHead(); ?>
                    </thead>
                    <tbody id="wiloke-listgo-show-billings" class="f-listings">
                        <?php
                        foreach ($aInvoicesInfo['oInvoices'] as $oResult){
                            \WilokeListgoFunctionality\Frontend\FrontendBilling::renderBillingItem($oResult);
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8" id="wiloke-billing-pagination" class="nav-links text-center"></td>
                        </tr>
                    </tfoot>
                </table>
            <?php else: ?>
            <?php  WilokeAlert::render_alert( __('There is no billing yet.', 'listgo'), 'info', false,  false); ?>
            <?php endif; ?>
        </div>
        <!-- End / Billing History -->

        <!-- Card Settings -->
        <?php
        $aGatewayKeys = is_array($aGateways) ? array_keys($aGateways) : '';
        if ( !empty($aGatewayKeys) && (in_array('2checkout', $aGatewayKeys) || in_array('stripe', $aGatewayKeys)) ) :
            $aCardInfo = WilokeUser::getCard();
            if ( empty($aCardInfo['card_name']) ){
                $aCardInfo = apply_filters('wiloke/wiloke-listgo-functionality/app/frontend/FrontendTwoCheckout/cardInfo', $aCardInfo);
            }
        ?>
            <div id="wiloke-listgo-my-credit-debit-card-wrapper" class="account-page" style="margin-top: 30px;">
                <h4><?php esc_html_e('Credit / Debit Card', 'listgo'); ?></h4>
                <form id="wiloke-my-credi-debit-card-form" action="<?php echo esc_url(admin_url('admin-ajax.php?action=wiloke_save_card')); ?>" method="POST">
                    <div class="form form--profile">

                        <div class="row">
                            <div id="wiloke-save-card-msg-wrapper" class="col-md-12 hidden">
                                <div class="wil-alert wil-alert-has-icon alert-success">
                                    <span class="wil-alert-icon"><i class="icon_box-checked"></i></span>
                                    <p class="wil-alert-message"></p>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-item">
                                    <label for="cardNumber" class="label"><?php esc_html_e('Card Number', 'listgo'); ?></label>
                                    <span class="input-text">
                                        <input id="cardNumber" name="card_number" type="text" value="<?php echo esc_attr($aCardInfo['card_number']); ?>" required>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">

                                <label class="label"><?php esc_html_e('Expiration Date (MM/YYYY)', 'listgo'); ?></label>

                                <div class="row">

                                    <div class="col-xs-6">
                                        <div class="form-item">
                                            <span class="input-text">
                                                <input id="expMonth" type="text" size="2" name="expMonth" value="<?php echo esc_attr($aCardInfo['expMonth']); ?>" placeholder="<?php esc_html_e('MM', 'listgo') ?>" required>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-xs-6">
                                        <div class="form-item">
                                            <span class="input-text">
                                                <input id="expYear" type="text" size="4" name="expYear" value="<?php echo esc_attr($aCardInfo['expYear']); ?>" placeholder="<?php esc_html_e('YYYY', 'listgo') ?>" required>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-item">
                                    <label for="cvv" class="label"><?php esc_html_e('CVC', 'listgo'); ?></label>
                                    <span class="input-text">
                                        <input id="cvv" type="text" name="cvv" value="" required>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-item">
                                    <label for="cardName" class="label"><?php esc_html_e('Name On Card', 'listgo'); ?></label>
                                    <span class="input-text">
                                        <input id="cardName" type="text" name="card_name" value="<?php echo esc_attr($aCardInfo['card_name']); ?>" required>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-item">
                                    <label for="cardAddress" class="label"><?php esc_html_e('Address', 'listgo'); ?></label>
                                    <span class="input-text">
                                        <input id="cardAddress" name="card_address1" type="text" value="<?php echo esc_attr($aCardInfo['card_address1']); ?>" required>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-item">
                                    <label for="cardCity" class="label"><?php esc_html_e('Card holder\'s City', 'listgo'); ?></label>
                                    <span class="input-text">
                                        <input id="cardCity" name="card_city" type="text" value="<?php echo esc_attr($aCardInfo['card_city']); ?>" required>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-item">
                                    <label for="cardCountry" class="label"><?php esc_html_e('Card holder\'s Country', 'listgo'); ?></label>

                                    <select name="card_country" id="cardCountry">
                                        <?php
                                            foreach (wilokeRepository('app:countryCode') as $symbol => $country) :
                                        ?>
                                            <option <?php selected($symbol, $aCardInfo['card_country']); ?> value="<?php echo esc_attr($symbol); ?>"><?php echo esc_html($country); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-item">
                                    <label for="cardEmail" class="label"><?php esc_html_e('Card holder\'s Email', 'listgo'); ?></label>
                                    <span class="input-text">
                                        <input id="cardEmail" name="card_email" type="text" value="<?php echo esc_attr($aCardInfo['card_email']); ?>" required>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-item">
                                    <label for="cardPhone" class="label"><?php esc_html_e('Card holder\'s Phone', 'listgo'); ?></label>
                                    <span class="input-text">
                                        <input id="cardPhone" name="card_phone" type="text" value="<?php echo esc_attr($aCardInfo['card_phone']); ?>" required>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="profile-actions">
                                    <input type="submit" class="listgo-btn btn-primary listgo-btn--sm" value="<?php esc_html_e('Save Card', 'listgo'); ?>">
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        <?php endif; ?>
        <!-- End / Card Settings -->

	</div>
</div>