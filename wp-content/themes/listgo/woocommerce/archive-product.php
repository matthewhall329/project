<?php
get_header();
?>

<?php
/**
 * woocommerce_before_main_content hook.
 * @hooked woocommerceOpenTopWrap 5
 * @hooked woocommerceRenderHeaderPage 5
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20 x
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );
?>
<?php if ( have_posts() ) : ?>

	<?php
	/**
	 * woocommerce_before_shop_loop hook.
	 *
	 * @hooked woocommerceSearchResultWrapper 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerceMiniCart - 25
	 * @hooked woocommerce_catalog_ordering - 30
	 * @hooked woocommerceSearchResultWrapperEnd 40
	 */
	do_action( 'woocommerce_before_shop_loop' );
	?>

	<?php woocommerce_product_loop_start(); ?>

	<?php woocommerce_product_subcategories(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php
		/**
		 * woocommerce_shop_loop hook.
		 *
		 * @hooked WC_Structured_Data::generate_product_data() - 10
		 */
		do_action( 'woocommerce_shop_loop' );
		?>

		<?php wc_get_template_part( 'content', 'product' ); ?>

	<?php endwhile; // end of the loop. ?>

	<?php woocommerce_product_loop_end(); ?>

	<?php
	/**
	 * woocommerce_after_shop_loop hook.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );
	?>
<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

	<?php
	/**
	 * woocommerce_no_products_found hook.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
	?>

<?php endif; ?>
<?php
/**
 * woocommerce_after_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );
?>
<?php  get_sidebar('shop'); ?>
<?php
/**
 * @hooked woocomemrceTopWrapEnd 10
 */
do_action('wiloke_woocommerce_close_top_wrap');
?>
<?php get_footer( 'shop' ); ?>