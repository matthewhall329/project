<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $wiloke, $woocommerce_loop;
if ( isset($woocommerce_loop['name']) && !empty($woocommerce_loop['name']) ){
	$aProductClasses[] = 'col-sm-6';
	switch (abs($woocommerce_loop['columns'])) {
		case 3:
			$aProductClasses[] = 'col-md-4';
			$aProductClasses[] = 'col-lg-4';
			break;
		case 6:
			$aProductClasses[] = 'col-md-2';
			$aProductClasses[] = 'col-lg-2';
			break;
		case 2:
			$aProductClasses[] = 'col-md-6';
			$aProductClasses[] = 'col-lg-6';
			break;
		default:
			$aProductClasses[] = 'col-lg-3';
			$aProductClasses[] = 'col-md-3';
			break;
	}
}else{
	if ( empty($wiloke->aThemeOptions) ){
		$aProductClasses = array(
			'col-sm-6',
			'col-md-4',
			'col-lg-3'
		);
	}else{
		$aProductClasses = array(
			$wiloke->aThemeOptions['woo_products_per_row_on_desktops'],
			$wiloke->aThemeOptions['woo_products_per_row_on_tablets']
		);
	}
}

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<div class="<?php echo esc_attr(implode(' ', $aProductClasses)) ?>">
	<div class="product-item">
	<?php
	/**
	 * woocommerce_before_shop_loop_item hook.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10 x
	 * @hooked woocommerceProductMediaWrapper 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	/**
	 * woocommerce_before_shop_loop_item_title hook.
	 * @hooked woocommerceProductLinkToProductAndMedia 5 // Media Inside
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10 x
     * @hooked woocommerceAddToCart 15
	 * @hooked woocommerceProductMediaWrapperEnd 20
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' );

	/**
	 * woocommerce_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10 x
     * @hooked woocommerceProductTitle 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' );

	/**
	 * woocommerce_after_shop_loop_item_title hook.
	 *
	 * @hooked woocommerce_template_loop_rating - 5 // temporary remove rating
	 * @hooked woocommerce_template_loop_price - 10
	 */
	do_action( 'woocommerce_after_shop_loop_item_title' );

	/**
	 * woocommerce_after_shop_loop_item hook.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5 x
	 * @hooked woocommerce_template_loop_add_to_cart - 10 x
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
	</div>
</div>
