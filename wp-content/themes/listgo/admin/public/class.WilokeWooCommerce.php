<?php

/**
 * WilokeWooCommerce Class
 *
 * @category Wiloke WooCommerce
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') ) {
    exit;
}

if( !class_exists('WilokeWooCommerce') ) {
	/**
	* 
	*/
	class WilokeWooCommerce  {
		/**
		 * Start WooCommerce
		 * @since 1.0
		 */
		public function woocommerceTopWrap(){
			?>
            <div class="shop"><div class="container">
			<?php
		}

		public function woocommerceFilterMenuItems($aItems){
			if ( is_plugin_active('wiloke-listgo-functionality/wiloke-listgo-functionality.php') ){
				unset($aItems['edit-address']);
				unset($aItems['edit-account']);
			}
			return $aItems;
		}

		public function woocommerceBreadcrumbConfiguration($args){
			return array(
				'delimiter'   => '&nbsp;&#47;&nbsp;',
				'wrap_before' => '<div class="header-page__breadcrumb"><div class="container"><ol class="wo_breadcrumb">',
				'wrap_after'  => '</ol></div></div>',
				'before'      => '<li>',
				'after'       => '</li>',
				'home'        => _x( 'Home', 'breadcrumb', 'listgo' ),
			);
		}

        public function woocommerceSearchResultWrapper(){
            ?><div class="shop-top"><?php
        }

        public function woocommerceSearchResultWrapperEnd(){
            ?></div><!-- End / Shop top --><?php
        }

		public function woocommerceMiniCart(){
			global $woocommerce;
			$currentItems = $woocommerce->cart->cart_contents_count;
			$total = $currentItems > 1 ? $currentItems . ' ' . esc_html__('items', 'listgo') : $currentItems . ' ' . esc_html__('item', 'listgo');
			?>
        <a href="<?php echo esc_url(wc_get_cart_url()); ?>" id="cart-mini-content" class="woocommerce-cart-mini" data-total="<?php echo esc_attr($currentItems); ?>"><i class="icon_bag_alt"></i><span>(<?php echo esc_html($total); ?>)</span></a><?php
		}

        public function woocommerceProductMediaWrapper(){
            ?><div class="product__media"><?php
        }

        public function woocommerceProductMediaWrapperEnd(){
            ?></div><!-- End / Product Media --><?php
        }

		public function woocommerceRenderHeaderPage(){
			global $wiloke, $wp_query;
			if ( is_product() ){
				return false;
			}

			if( is_tax('product_cat') ){
				$oTerm        = $wp_query->get_queried_object();
				$thumbnailID  = get_woocommerce_term_meta($oTerm->term_id, 'thumbnail_id', true);
				$bgImg        = wp_get_attachment_image_url($thumbnailID, 'large');
				$aOptions     = Wiloke::getTermOption($oTerm->term_id);
				$headerOverlay = isset($aOptions['header_overlay']) ? $aOptions['header_overlay'] : '';
			}else if ( is_tax('product_tag') ){
				$oTerm         = $wp_query->get_queried_object();
				$aOptions      = Wiloke::getTermOption($oTerm->term_id);
				$bgImg         = isset($aOptions['featured_image']) ? wp_get_attachment_image_url($aOptions['featured_image'], 'large') : '';
				$headerOverlay = isset($aOptions['header_overlay']) ? $aOptions['header_overlay'] : '';
			}

			if ( !isset($bgImg) || empty($bgImg) ){
				$bgImg = isset($wiloke->aThemeOptions['woocommerce_header_image']) && isset($wiloke->aThemeOptions['woocommerce_header_image']['id']) ? wp_get_attachment_image_url($wiloke->aThemeOptions['woocommerce_header_image']['id'], 'large') : '';
			}

			if ( !isset($headerOverlay) || empty($headerOverlay) ){
				$headerOverlay = isset($wiloke->aThemeOptions['woocommerce_header_overlay']) && isset($wiloke->aThemeOptions['woocommerce_header_overlay']['rgba']) ? $wiloke->aThemeOptions['woocommerce_header_overlay']['rgba'] : '';
			}

			?>
            <div class="lazy header-page bg-scroll p-top-0" data-src="<?php echo esc_url($bgImg); ?>">
                <div class="container">
                    <div class="header-page__inner">
                        <h2 class="header-page__title"><?php woocommerce_page_title(); ?></h2>
                    </div>
                </div>
				<?php woocommerce_breadcrumb(); ?>
                <div class="overlay" style="background-color: rgba(<?php echo esc_attr($headerOverlay); ?>)"></div>
            </div>
			<?php
		}

		public function woocomemrceTopWrapEnd(){
			?>
            </div></div><!-- End row,shop,container,wo_shop -->
			<?php
		}

		public function woocommerceProductLinkToProductAndMedia(){
			global $wiloke, $post;
			$size = 'wiloke_listgo_455x340';
			if ( Wiloke::$mobile_detect->isTablet() ){
				$size = !empty($wiloke->aThemeOptions['woo_products_per_row_on_tablets'])  && $wiloke->aThemeOptions['woo_products_per_row_on_tablets'] === 'col-md-4' ? 'wiloke_listgo_370x370' : 'wiloke_listgo_455x340';
			}else if(!Wiloke::$mobile_detect->isMobile()){
				$size = !empty($wiloke->aThemeOptions['woo_products_per_row_on_desktops'])  && $wiloke->aThemeOptions['woo_products_per_row_on_desktops'] === 'col-md-4' ? 'wiloke_listgo_370x370' : 'wiloke_listgo_455x340';
			}
			$size = apply_filters( 'single_product_archive_thumbnail_size', $size );

			if ( has_post_thumbnail() ){
				$thumbnailUrl = get_the_post_thumbnail_url($post->ID, $size);
			}else{
				$thumbnailUrl = wc_placeholder_img_src();
			}
			?>
            <a href="<?php the_permalink(); ?>"><?php Wiloke::lazyLoad($thumbnailUrl); ?></a>
			<?php
		}

		public function woocommerceAddToCart(){
			woocommerce_template_loop_add_to_cart();
		}

		public function woocommerceProductTitle(){
			?><h2 class="product__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php
		}
	}
}