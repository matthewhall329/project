<?php
$this->_loader->add_action('save_post', $this->_adminGeneral, 'saveSettings');
$this->_loader->add_action('save_post_listing', $this->_adminGeneral, 'saveListing');
//$this->_loader->add_action('post_updated', $this->_adminGeneral, 'afterListingUpdated');
$this->_loader->add_action('admin_enqueue_scripts', $this->_adminGeneral, 'enqueue_scripts');


/*
 |--------------------------------------------------------------------------
 | Theme Options
 |--------------------------------------------------------------------------
 | Configurations and handles
 |
 */
$this->_loader->add_action('init', $this->_themeOptions, 'render');
$this->_loader->add_action('init', $this->_themeOptions, 'update_theme_options');


/*
 |--------------------------------------------------------------------------
 | Add filters get term caching
 |--------------------------------------------------------------------------
 | @param $aTerm /admin/class.wiloke.com
 |
 */
$this->_loader->add_filter('wiloke/admin/get_term_caching', $this->_adminGeneral, 'get_more_params_of_term', 10, 1);


/**
 * Register Highlight box for listing
 * @since 1.0
 */
$this->_loader->add_action('add_meta_boxes', $this->_adminGeneral, 'highlight_box_in_listing');

/**
 * Additional Settings For Listing Type
 * @since 1.0
 */
$this->_loader->add_action('add_meta_boxes', $this->_adminGeneral, 'addAdditionalSettingsForListing');
$this->_loader->add_action('wiloke_mega_menu/filter_shortcodes', $this->_adminGeneral, 'filterMegaMenuShortcodes', 10, 1);

/**
 * Add Additional Minify Scripts
 * @since 1.0
 */
$this->_loader->add_filter('wiloke-service/minify-scripts/settings', $this->_adminGeneral, 'addAdditionalScripts', 10, 1);