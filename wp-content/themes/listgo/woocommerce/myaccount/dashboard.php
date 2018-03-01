<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

	<p><?php
		/* translators: 1: user display name 2: logout url */
		Wiloke::wiloke_kses_simple_html(
			sprintf(__( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'listgo' ),
			'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
				esc_url( wc_logout_url( wc_get_page_permalink( 'myaccount' ) ) )
			)
		);
		?></p>

	<p><?php
		Wiloke::wiloke_kses_simple_html(sprintf(__( 'You are in Shop Dashboard area. From your account dashboard you can view your <a href="%1$s">recent orders</a>.', 'listgo' ),esc_url( wc_get_endpoint_url( 'orders' ) )));
		if ( is_plugin_active('wiloke-listgo-functionality/wiloke-listgo-functionality.php') ){
			Wiloke::wiloke_kses_simple_html(sprintf(__(' If you want to go to the main Dashboard, please click on <a href="%s">Main Dashboard</a> link.', 'listgo'), esc_url(get_permalink(WilokePublic::getPaymentField('myaccount')))));
		}
		?></p>

<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_account_dashboard' );

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_before_my_account' );

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
