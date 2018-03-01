<?php
if ( !defined('WILOKE_TURN_OFF_REDIS') ){
	define('WILOKE_TURN_OFF_REDIS', true);
}

function listgoIsWooCommerce(){
	return function_exists('is_woocommerce');
}

function listgoIsUserNotLoggedIn(){
	return !is_user_logged_in();
}

if ( !function_exists('listgoMyformatTinyMCE') ){
    add_filter( 'tiny_mce_before_init', 'listgoMyformatTinyMCE' );
    function listgoMyformatTinyMCE( $in ) {
        $in['wordpress_adv_hidden'] = FALSE;
        return $in;
    }
}

function listgoRenderDate($date){
	$dateToTimeStamp = strtotime($date);
	return date(get_option('date_format'), $dateToTimeStamp);
}

function listgoIsListingSupportsTimeKit(){
    if ( !is_singular('listing') ){
        return false;
    }

    global $post;
	$planID = get_post_meta($post->ID, 'wiloke_submission_belongs_to_session_ID', true);
	if ( !empty($planID) ){
		$aPlanSettings = get_post_meta($planID, 'pricing_settings', true);
		if ( isset($aPlanSettings['toggle_timekit']) && $aPlanSettings['toggle_timekit'] == 'disable' ){
			return false;
		}
	}

	return true;
}

add_filter('wpseo_sitemap_entry', 'listgo_wpseo_sitemap_entry', 10, 3);
function listgo_wpseo_sitemap_entry($url, $type, $oTerm){
	global $wiloke;

	if ( $type == 'term' ){
		if ( is_array($url) ){
			$realUrl = $url['loc'];
		}else{
			$realUrl = $url;
		}

		if ( strpos($realUrl, 'listing-location') !== false ){
			if ( !empty($wiloke->aThemeOptions['custom_listing_location_slug']) ){
				$replace = trim($wiloke->aThemeOptions['custom_listing_location_slug']);
				$target = '/listing-location/';
			}
		}elseif (strpos($realUrl, 'listing-cat') !== false) {
			if ( !empty($wiloke->aThemeOptions['custom_listing_cat_slug']) ){
				$replace = trim($wiloke->aThemeOptions['custom_listing_cat_slug']);
				$target = '/listing-cat/';
			}
		}elseif ( strpos($realUrl, 'listing-tag') !== false ) {
			if ( !empty($wiloke->aThemeOptions['custom_listing_tag_slug']) ){
				$replace = trim($wiloke->aThemeOptions['custom_listing_tag_slug']);
				$target = '/listing-tag/';
			}
		}
	}elseif($type == 'post'){
		if ( is_array($url) ){
			$realUrl = $url['loc'];
		}else{
			$realUrl = $url;
		}
		
		if ( strpos($realUrl, '/listing/') !== false ){
			if ( !empty($wiloke->aThemeOptions['custom_listing_single_slug']) ){
				$replace = trim($wiloke->aThemeOptions['custom_listing_single_slug']);
				$target = '/listing/';
			}
		}
	}

	if ( isset($replace) ){
		$realUrl = str_replace($target, '/'.$replace.'/', $realUrl);
		if ( is_array($url) ){
			$url['loc'] = $realUrl;
		}else{
			$url = $realUrl;
		}
	}

	return $url;
}

require_once  ( get_template_directory() . '/admin/run.php' );

/*
 |--------------------------------------------------------------------------
 | After theme setup
 |--------------------------------------------------------------------------
 |
 | Run needed functions after the theme is setup
 |
 */
add_action('after_setup_theme', 'wiloke_listgo_after_setup_theme');
function wiloke_listgo_after_setup_theme(){
	add_theme_support('html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ));
	add_theme_support('title-tag');
	add_theme_support('widgets');
	add_theme_support('woocommerce');
	add_theme_support('automatic-feed-links');
	add_theme_support('menus');
	add_theme_support('post-thumbnails');
	add_theme_support('title-tag');
	add_theme_support('post-formats', array( 'gallery', 'quote', 'video', 'audio' ));
	add_theme_support( 'editor-style' );
	// Woocommerce
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	add_image_size('wiloke_listgo_370x370', 370, 370, true);
	add_image_size('wiloke_listgo_740x370', 740, 370, true);
	add_image_size('wiloke_listgo_740x740', 740, 740, true);
	add_image_size('wiloke_listgo_455x340', 455, 340, true);
	$GLOBALS['content_width'] = apply_filters('wiloke_filter_content_width', 1200);
	load_theme_textdomain( 'listgo', get_template_directory() . '/languages' );
}

add_filter('wp_list_categories', 'wiloke_listgo_count_span');
function wiloke_listgo_count_span($links) {
	$links = str_replace('</a> (', ' (', $links);
	$links = str_replace(')', ')</a>', $links);
	return $links;
}

add_filter('get_archives_link', 'wiloke_listgo_archive_count_span');
function wiloke_listgo_archive_count_span($links) {
	$links = str_replace('</a>&nbsp;(', ' (', $links);
	$links = str_replace(')', ')</a>', $links);
	return $links;
}

add_filter('wiloke_menu_style_filter', 'wiloke_listgo_mega_menu_style');
function wiloke_listgo_mega_menu_style($args) {
	return array(
		'wiloke-menu-horizontal' => esc_html__('Menu Horizontal', 'listgo'),
	);
}

add_filter('wiloke_menu_theme_filter', 'wiloke_listgo_mega_menu_themes');
function wiloke_listgo_mega_menu_themes($args) {
	return array(
		'' => esc_html__('Default', 'listgo'),
	);
}

// Footer Style
add_filter('body_class', 'wiloke_listgo_body_class');
function wiloke_listgo_body_class($classes) {
	global $wiloke;

	$style = 'footer-style1';

	if ( isset($wiloke->aThemeOptions['footer_style']) ) {
		$style = $wiloke->aThemeOptions['footer_style'];
	}

	if ( is_singular('listing') && is_page_template('default') ){
	    $layout = !empty($wiloke->aThemeOptions['listing_layout']) ? $wiloke->aThemeOptions['listing_layout'] : 'templates/single-listing-traditional.php';
		$aParseListingTemplate = explode('/', $layout);
	    $fClass = str_replace('/', '', $layout);
	    $classes[] = 'listing-template-' . $fClass . ' listing-template-'.$aParseListingTemplate[1];
    }

	$classes[] = $style;

	return $classes;
}

add_filter( 'user_can_richedit', 'patrick_user_can_richedit');

function patrick_user_can_richedit($c) {
	return true;
}

add_action('wiloke/listgo/wiloke-submission/addlisting/before_listing_information', 'listgoAddCustomFieldsToListingPage', 10, 2);
add_action('wiloke/wiloke-submission/addlisting/print_advanced_custom_fields', 'listgoAddCustomFieldsToListingPage', 10, 2);

if ( !function_exists('listgoAddCustomFieldsToListingPage') ){
	function listgoAddCustomFieldsToListingPage($postID, $packageID){
		if ( !function_exists('acf_form') ){
			return '';
		}

		$aPackageSettings = Wiloke::getPostMetaCaching($packageID, 'pricing_settings');
        ?>
        <div class="add-listing-group">
            <?php
            if ( !isset($aPackageSettings['afc_custom_field']) || empty($aPackageSettings['afc_custom_field']) ){
                return '';
            }

            if ( isset($aPackageSettings['toggle_custom_field']) && ($aPackageSettings['toggle_custom_field'] == 'disable') ){
                return '';
            }

            $aSettings = array(
                'group_title' => get_the_title($aPackageSettings['afc_custom_field']),
                'group_desc'  => '',
                /* (string) Unique identifier for the form. Defaults to 'acf-form' */
                'id' => 'acf-form',
                /* (int|string) The post ID to load data from and save data to. Defaults to the current post ID.
                Can also be set to 'new_post' to create a new post on submit */
                'post_id' => isset($postID) ? trim($postID) : 'new_post',
                /* (array) An array of post data used to create a post. See wp_insert_post for available parameters.
                The above 'post_id' setting must contain a value of 'new_post' */
                'new_post' => false,

                /* (array) An array of field group IDs/keys to override the fields displayed in this form */
                'field_groups' => array($aPackageSettings['afc_custom_field']),

                /* (array) An array of field IDs/keys to override the fields displayed in this form */
                'fields' => false,
                /* (boolean) Whether or not to create a form element. Useful when a adding to an existing form. Defaults to true */
                'form' => false,
                'return' => '',
                /* (string) Determines element used to wrap a field. Defaults to 'div'
                Choices of 'div', 'tr', 'td', 'ul', 'ol', 'dl' */
                'field_el' => 'div',

                /* (string) Whether to use the WP uploader or a basic input for image and file fields. Defaults to 'wp'
                Choices of 'wp' or 'basic'. Added in v5.2.4 */
                'uploader' => is_user_logged_in() ? 'wp' : 'basic',
                /* (boolean) Whether to include a hidden input field to capture non human form submission. Defaults to true. Added in v5.3.4 */
                'honeypot' => false
            );
		?>
			<?php if ( !empty($aSettings['group_title']) ) : ?>
				<h4 class="add-listing-title profile-title"><?php echo esc_html($aSettings['group_title']); ?></h4>
			<?php endif; ?>
			<?php if ( !empty($aSettings['group_title']) ) : ?>
				<p class="add-listing-description"><?php echo esc_html($aSettings['group_desc']); ?></p>
			<?php endif; ?>
            <div class="row">
                <?php acf_form( $aSettings ); ?>
            </div>
		</div>
		<?php
	}
}