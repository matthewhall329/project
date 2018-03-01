<?php
$this->_loader->add_action('wiloke/signup-form/before-submit-button', $this->_public, 'addGooglereCAPTCHA');
$this->_loader->add_action('wiloke/review-form/before-submit-button', $this->_public, 'addGooglereCAPTCHAToReviewForm');
$this->_loader->add_action('wiloke/wiloke-submission/signup-signin-in-addlisting/after-signup-account', $this->_public, 'addGooglereCAPTCHA');
$this->_loader->add_action('wiloke/listgo/admin/public/search-form', $this->instSearchSystem, 'addFieldsToSearchForm');
$this->_loader->add_filter('wiloke_sharing_posts/filter/title', $this->_public, 'filterWilokeSharingPostsTitle', 10, 1);
$this->_loader->add_filter('wiloke_sharing_posts/filter/css_wrapper', $this->_public, 'filterWilokeSharingPostsCssClass', 10, 1);
$this->_loader->add_filter('wiloke_sharing_posts/filter/social_item', $this->_public, 'filterWilokeSharingPostsRenderTitle', 10, 1);
$this->_loader->add_filter('wpcf7_form_hidden_fields', $this->_public, 'addHiddenFilterForContactForm7OnListingPage', 10, 1);
$this->_loader->add_filter('wpcf7_mail_components', $this->_public, 'filterRecipientOfContactFormSeven', 10, 3);
$this->_loader->add_filter('wp_footer', $this->_public, 'putLoginRegisterToFooter');
$this->_loader->add_filter('wiloke-login-with-social/twitter/button_class', $this->_public, 'addNewClassToTwitterButton');
$this->_loader->add_filter('wiloke-login-with-social/facebook/button_class', $this->_public, 'addNewClassToFacebookButton');
$this->_loader->add_filter('wiloke-login-with-social/google/button_class', $this->_public, 'addNewClassToGoogleButton');
$this->_loader->add_filter('wiloke-login-with-social/twitter/user_data_login', $this->_public, 'beforeInsertUserWithSocialMediaLogin');
$this->_loader->add_filter('wiloke-login-with-social/after_login_redirect_to', $this->_public, 'afterLoggedWithSocialMediaRedirectTo', 10, 2);
$this->_loader->add_action('wiloke-login-with-social/after_insert_user', $this->_public, 'updateUserMeta');
/**
 * WooCommerce Hooks and Filters
 */
$this->_loader->add_action('woocommerce_before_main_content', $this->woocommerce, 'woocommerceRenderHeaderPage', 5);
$this->_loader->add_action('woocommerce_before_main_content', $this->woocommerce, 'woocommerceTopWrap', 5);
$this->_loader->add_filter('woocommerce_account_menu_items', $this->woocommerce, 'woocommerceFilterMenuItems', 10);
// Breadcrumb and search result
$this->_loader->add_filter('woocommerce_breadcrumb_defaults', $this->woocommerce, 'woocommerceBreadcrumbConfiguration');
$this->_loader->add_action('woocommerce_before_shop_loop', $this->woocommerce, 'woocommerceSearchResultWrapper', 10);
$this->_loader->add_action('woocommerce_before_shop_loop', $this->woocommerce, 'woocommerceMiniCart', 25);
$this->_loader->add_action('woocommerce_before_shop_loop', $this->woocommerce, 'woocommerceSearchResultWrapperEnd', 40);

// Product content structure
$this->_loader->add_action('woocommerce_before_shop_loop_item', $this->woocommerce, 'woocommerceProductMediaWrapper', 10);
$this->_loader->add_action('woocommerce_before_shop_loop_item_title', $this->woocommerce, 'woocommerceProductLinkToProductAndMedia', 5);
$this->_loader->add_action('woocommerce_before_shop_loop_item_title', $this->woocommerce, 'woocommerceAddToCart', 15);
$this->_loader->add_action('woocommerce_before_shop_loop_item_title', $this->woocommerce, 'woocommerceProductMediaWrapperEnd', 20);
$this->_loader->add_action('woocommerce_shop_loop_item_title', $this->woocommerce, 'woocommerceProductTitle', 10);

$this->_loader->add_action('wiloke_woocommerce_close_top_wrap', $this->woocommerce, 'woocomemrceTopWrapEnd', 10);

remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
