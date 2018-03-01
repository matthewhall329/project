<?php
/**
 * Shop breadcrumb
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 * @see         woocommerce_breadcrumb()
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $breadcrumb ) ) {
	Wiloke::wiloke_kses_simple_html($wrap_before);
	foreach ( $breadcrumb as $key => $crumb ) {
		Wiloke::wiloke_kses_simple_html($before);
		if ( ! empty( $crumb[1] ) && sizeof( $breadcrumb ) !== $key + 1 ) {
			echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
		} else {
			echo '<span>' . esc_html( $crumb[0] ) . '</span>';
		}
		Wiloke::wiloke_kses_simple_html($after);
	}
	Wiloke::wiloke_kses_simple_html($wrap_after);
}
