<?php
/**
 * WilokeHead Class
 *
 * @category Front end
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
    exit;
}

use WilokeListGoFunctionality\Frontend\FrontendListingManagement;
use WilokeListGoFunctionality\Submit\AddListing as WilokeAddListing;

class WilokeHead
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_head', array($this, 'wpHead'), 10);
        add_action('wp_head', array($this, 'schemaMarkup'), 10);
	    add_filter('wp_title', array($this, 'wpTitle', 10));
    }

    public function schemaMarkup(){
        global $post;
        if ( !is_singular('listing') ){
            return '';
        }
	    $planID = Wiloke::getPostMetaCaching($post->ID, wilokeRepository('app:belongsToPlanID'));
	    $aPlanSettings = Wiloke::getPostMetaCaching($planID, 'pricing_settings');

	    if ( isset($aPlanSettings['toggle_schema_markup']) && ($aPlanSettings['toggle_schema_markup']=='disable') ){
	        return '';
        }

	    $aSettings = Wiloke::getPostMetaCaching($post->ID, 'listing_settings');
        $postalAddress = '';
        if ( isset($aSettings['map']) ){
            $postalAddress = $aSettings['map']['location'];
        }

        $aLocation = wp_get_post_terms($post->ID, 'listing_location');
	    $location = '';
        if ( !is_wp_error($aLocation) && !empty($aLocation) ){
            $location = $aLocation[0]->name;
        }
        $averageRating = WilokePublic::calculateAverageRating($post);
        $averageRating = $averageRating == '0.0' ? 0 : $averageRating;
        ?>
        <script type="application/ld+json">
        {
            "@context" : "http://schema.org",
            "@type" : "LocalBusiness",
            "name" : "<?php echo esc_html($post->post_title); ?>",
            "description" : "<?php Wiloke::wiloke_content_limit(100, $post, false, $post->post_content, false); ?>",
            "telephone" : "<?php echo esc_attr($aSettings['phone_number']); ?>",
            "url" : "<?php echo esc_url($aSettings['website']); ?>",
            "image" : "<?php echo esc_url(get_the_post_thumbnail_url($post->ID, 'full')); ?>",
            <?php if (!empty($averageRating)) : ?>
            "aggregateRating" : {
                "@type" : "AggregateRating",
                "ratingValue" : "<?php echo esc_attr($averageRating); ?>",
                "reviewCount" : "<?php echo esc_attr(FrontendListingManagement::getListingView($post->post_author, $post->ID)); ?>",
                "bestRating": "5",
                "worstRating":"1"
            },
            <?php endif; ?>
            "address" : {
                "@type" : "PostalAddress",
                "streetAddress" : "<?php echo esc_attr($postalAddress); ?>",
                "addressLocality" : "<?php echo esc_attr($location); ?>"
            },
            "openingHours" : "<?php $this->generateSchemaOpeningHours($post); ?>",
            "priceRange": "<?php $this->getPriceRange($aSettings); ?>"
        }
        </script>
        <?php
    }

    public function getPriceRange($aSettings){
	    if ( empty($aSettings) || empty($aSettings['price_segment']) ){
		    echo '';
	    }

	    if ( empty($aSettings['price_from']) && empty($aSettings['price_to']) ){
		    echo '';
	    }
        global $WilokeListGoFunctionalityApp;
	    $currency = WilokePublic::getPaymentField('currency_code');
	    $symbol = $WilokeListGoFunctionalityApp['currencySymbol'][$currency];

	    echo $symbol . $aSettings['price_from'] . ' - ' . $symbol . $aSettings['price_to'];
    }

    public function convertToTwentyFourFormat($hour, $format){
        if ( $format == 'PM' ){
            $hour = 12 + $hour;
        }

        return $hour;
    }

    public function generateSchemaOpeningHours($post){
	    $toggleBusinessHour = get_post_meta($post->ID, 'wiloke_toggle_business_hours', true);
	    if ( isset($toggleBusinessHour) && $toggleBusinessHour === 'disable' ){
		    return '';
	    }

        $aDays = array(
            'Mo', 'Tu', 'We', 'Th', 'Fri', 'Sa', 'Su'
        );

	    $aBusinessHours  = Wiloke::getPostMetaCaching($post->ID, 'wiloke_listgo_business_hours');

	    $beforeSH = $beforeSM = $beforeCH = $beforeCM = 0;
	    $beforeKey = 'Mo';
	    $aMarkupBNH = array();

	    foreach ($aBusinessHours as $key => $aBusinessHour){
	        if ( !isset($aBusinessHour['closed']) ){
		        if ( $key == 0 ){
			        $beforeSH = $this->convertToTwentyFourFormat($aBusinessHour['start_hour'], $aBusinessHour['start_format']);
			        $beforeSM = $aBusinessHour['start_minutes'];
			        $beforeCH = $this->convertToTwentyFourFormat($aBusinessHour['close_hour'], $aBusinessHour['close_format']);
			        $beforeCM = $aBusinessHour['close_minutes'];
			        $aMarkupBNH[$aDays[$key]] = $beforeSH . ':' . $beforeSM . '-' . $beforeCH . ':' . $beforeCM;
                    $beforeKey = $aDays[$key];
		        }else{
			        $currentSH = $this->convertToTwentyFourFormat($aBusinessHour['start_hour'], $aBusinessHour['start_format']);
			        $currentSM = $aBusinessHour['start_minutes'];
			        $currentCH = $this->convertToTwentyFourFormat($aBusinessHour['close_hour'], $aBusinessHour['close_format']);
			        $currentCM = $aBusinessHour['close_minutes'];
			        if ( ($currentSH == $beforeSH) && ($beforeSM == $currentSM) && ($beforeCH == $currentCH) && ( $beforeCM == $currentCM ) ){

				        $aMarkupBNH[$beforeKey.'-'.$aDays[$key]] = $aMarkupBNH[$beforeKey];
				        unset($aMarkupBNH[$beforeKey]);
				        $beforeKey = $beforeKey.'-'.$aDays[$key];
			        }else{
				        $aMarkupBNH[$aDays[$key]] = $beforeSH . ':' . $beforeSM . '-' . $beforeCH . ':' . $beforeCM;
				        $beforeSH = $currentSH;
				        $beforeSM = $currentSM;
				        $beforeCH = $currentCH;
				        $beforeCM = $currentCM;
				        $beforeKey= $aDays[$key];
			        }
		        }
            }
        }

        if ( empty($aMarkupBNH) ){
	        return '';
        }

        $aProcessMarkup = array();
        foreach ( $aMarkupBNH as $range => $bsH ){
	        $aProcessMarkup[] = $range . ' ' . $bsH;
        }

        echo '[' . implode(',', $aProcessMarkup) . ']';
    }

    public function wpHead(){
	    global $wiloke, $post;
	    if ( empty($wiloke) ) {
		    return;
	    }

	    if ( !function_exists('wp_site_icon') || ( function_exists('has_site_icon') && !has_site_icon() ) ) {
		    if ( isset($wiloke->aThemeOptions['general_favicon']['thumbnail']) && !empty($wiloke->aThemeOptions['general_favicon']['thumbnail']) ){
			    ?>
                <link rel="shortcut icon" href="<?php echo esc_url($wiloke->aThemeOptions['general_favicon']['thumbnail']); ?>" />
			    <?php
		    }
	    }

	    if( is_home() || is_front_page() ) :
		    ?>
            <meta name="description" content="<?php echo esc_attr( $wiloke->aThemeOptions['seo_home_meta_description'] ); ?>" />
            <meta name="keywords" content="<?php echo esc_attr( $wiloke->aThemeOptions['seo_home_meta_keywords'] ); ?>" />
	    <?php else: ?>
            <meta name="description" content="<?php echo esc_attr( $wiloke->aThemeOptions['seo_other_meta_description'] ); ?>" />
            <meta name="keywords" content="<?php echo esc_attr( $wiloke->aThemeOptions['seo_other_meta_keywords'] ); ?>" />
		    <?php
	    endif;
	    if( $wiloke->aThemeOptions['seo_open_graph_meta'] === 'enable' && !is_404() && !empty($post) ) :
		    if( is_singular() ){
			    $aPageSettings = Wiloke::getPostMetaCaching($post->ID, 'single_page_settings');
			    if( isset($aPageSettings['page_description']) ){
				    $desc = esc_attr( $aPageSettings['page_description'] );
			    }else if( !empty( $post->post_excerpt ) ){
				    $desc = esc_attr( wp_trim_words( $post->post_excerpt, 50 ) );
			    }else if( strpos($post->post_content, '[vc_row') === false ){
				    $desc = esc_attr( wp_trim_words( $post->post_content, 50 ) );
			    }else{
				    $desc = esc_attr( $post->post_title );
			    }
		    }else if ( is_author() ){
			    $aAuthor = Wiloke::getUserMeta(get_queried_object_id());
			    $desc = $aAuthor['description'];
		    }else if ( is_tax() ){
			    $desc = term_description(get_queried_object_id(), get_query_var( 'taxonomy' ));
		    }

		    $desc = !isset($desc) || empty($desc) ? get_option('blogdescription') : $desc;
		    ?>
		    <?php
		    $aFbSettings = Wiloke::getOption('_wiloke_facebook_settings');
		    if ( isset($aFbSettings['app_id']) && !empty($aFbSettings['app_id']) ) :
			    ?>
                <meta property="fb:app_id" content="<?php echo esc_attr($aFbSettings['app_id']) ?>">
		    <?php endif; ?>

            <meta property="og:type" content="website" />
            <meta property="og:url" content="<?php echo get_permalink( $post->ID ); ?>" />
            <meta property="og:title" content="<?php echo esc_attr( $post->post_title ); ?>" />
            <meta property="og:description" content="<?php echo esc_attr(strip_shortcodes($desc)); ?>">
		    <?php
		    if ( is_singular() ) {
			    $metaImage = get_the_post_thumbnail_url($post->ID, 'large');
		    }else if(is_tax()){
			    $aOptions = Wiloke::getTermOption(get_queried_object_id());
			    $metaImage = isset($aOptions['featured_image']) ? wp_get_attachment_image_url($aOptions['featured_image'], 'large') : '';
		    }

		    $metaImage = (!isset($metaImage) || empty($metaImage)) && !empty($wiloke->aThemeOptions['seo_og_image']['id']) ? wp_get_attachment_image_url($wiloke->aThemeOptions['seo_og_image']['id'], 'large') : '';

		    if ( !empty($metaImage) ) {
			    echo '<meta property="og:image" content="'.esc_url($metaImage).'" />';
		    }
	    endif;
    }

	public function wpTitle($title){
		global $wiloke, $paged, $page;
        $toggleCustomMetaTags = ($wiloke->aThemeOptions['seo_toggle_custom_meta_tags'] == 'disable') ? true : false;

		if ( $toggleCustomMetaTags ) {
			$title = trim( str_replace( array( '&raquo;', get_bloginfo( 'name' ), '|' ),array( '', '', ''), $title ) );
			$seprated = '&raquo;';
            ob_start();
			if( is_home() || is_front_page() )
			{
				if( !empty( $wiloke->aThemeOptions['seo_home_title_format'] ) )
				{
					echo esc_html( str_replace( array('%Site Title%', '%Tagline%' ), array( get_bloginfo( 'name' ), get_bloginfo( 'description', 'display' ) ),$wiloke->aThemeOptions['seo_home_title_format'] ) );
				}else{
					$site_description = get_bloginfo( 'description', 'display' );
					if( $wiloke->aThemeOptions['seo_home_title_format'] == 'blogname_blogdescription' )
					{
						bloginfo( 'name' );
						if ( $site_description ){
							echo ' '.$seprated." $site_description";
						}

					}else if( $wiloke->aThemeOptions['seo_home_title_format'] == 'blogdescription_blogname' ){
						if ( $site_description ){
							echo esc_html( $seprated ). Wiloke::wiloke_kses_simple_html($site_description, true);
						}
						bloginfo( 'name' );
					}else{
						bloginfo( 'name' );
					}
				}

			}else if( is_page() || is_single() )
			{

				if( $wiloke->aThemeOptions['seo_single_post_page_title_format'] == 'posttitle_blogname' )
				{

					echo esc_html( $title.' '.$seprated.' ' );
					bloginfo( 'name' );

				}else if( $wiloke->aThemeOptions['seo_single_post_page_title_format'] == 'blogname_posttitle' ){
					bloginfo( 'name' );
					echo esc_html( ' '.$seprated.' '.$title );
				}else{
					echo esc_html( $title );
				}

			}else{
				if( $wiloke->aThemeOptions['seo_archive_title_format'] == 1 )
				{
					echo esc_html( $title.' '.$seprated.' ' );
					bloginfo( 'name' );

				}else if( $wiloke->aThemeOptions['seo_archive_title_format'] == 2 ){
					bloginfo( 'name' );
					echo esc_html( ' '.$seprated.' '.$title );
				}else{
					echo esc_html( $title );
				}
			}
			if ( $paged >= 2 || $page >= 2 ){
				echo esc_html( ' '.$seprated.' ' . 'Page '. max( $paged, $page ) );
			}

            $out = ob_get_contents();
            ob_end_clean();

            return $out;
		}
	}

    public function enqueue_scripts()
    {
        wp_enqueue_style('wiloke-alert', Wiloke::$public_url . 'source/css/alert.css', array(), WILOKE_THEMEVERSION);

        global $post, $current_user, $wiloke;

        if ( $wiloke->aThemeOptions['toggle_google_recaptcha'] == 'enable' && !is_user_logged_in() ){
            wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, false);
        }

        $aUserInfo = array();
        if ( !empty($current_user) ){
            $aUserInfo = array(
                'user_id'               => $current_user->ID,
                'author_name'           => $current_user->user_nicename,
                'user_url'              => $current_user->user_url,
                'author_avatar_urls'    => array(
                    96 => get_avatar_url($current_user->ID, array('size'=>96))
                )
            );
        }
        $mapPageUrl = isset($wiloke->aThemeOptions['header_search_map_page']) ? get_permalink($wiloke->aThemeOptions['header_search_map_page']) : '';
        $mapToken = isset($wiloke->aThemeOptions['general_mapbox_api']) ? $wiloke->aThemeOptions['general_mapbox_api'] : '';
        $mapTheme = isset($wiloke->aThemeOptions['general_map_theme']) ? $wiloke->aThemeOptions['general_map_theme'] : '';
        $mapMaxZoom = isset($wiloke->aThemeOptions['general_map_max_zoom']) && !empty($wiloke->aThemeOptions['general_map_max_zoom']) ? $wiloke->aThemeOptions['general_map_max_zoom'] : 4;
        $mapMinZoom = isset($wiloke->aThemeOptions['general_map_min_zoom']) && !empty($wiloke->aThemeOptions['general_map_min_zoom']) ? $wiloke->aThemeOptions['general_map_min_zoom'] : -1;
        $mapClusterRadius = isset($wiloke->aThemeOptions['general_map_cluster_radius']) && !empty($wiloke->aThemeOptions['general_map_cluster_radius']) ? $wiloke->aThemeOptions['general_map_cluster_radius'] : 60;
	    $mapCenterZoom = isset($wiloke->aThemeOptions['general_map_center_zoom']) && !empty($wiloke->aThemeOptions['general_map_center_zoom']) ? $wiloke->aThemeOptions['general_map_center_zoom'] : 10;
        $toggleAskForPosition = isset($wiloke->aThemeOptions['listing_toggle_ask_for_geocode']) && !empty($wiloke->aThemeOptions['listing_toggle_ask_for_geocode']) ? $wiloke->aThemeOptions['listing_toggle_ask_for_geocode'] : 'enable';
        $googleAPIKey = isset($wiloke->aThemeOptions['general_map_api']) && !empty($wiloke->aThemeOptions['general_map_api']) ? $wiloke->aThemeOptions['general_map_api'] : '';
        $paymentMode = WilokePublic::getPaymentField('mode');

	    $mapSingleMaxZoom = isset($wiloke->aThemeOptions['general_map_single_max_zoom']) && !empty($wiloke->aThemeOptions['general_map_single_max_zoom']) ? $wiloke->aThemeOptions['general_map_single_max_zoom'] : 4;
	    $mapSingleMinZoom = isset($wiloke->aThemeOptions['general_map_min_zoom']) && !empty($wiloke->aThemeOptions['general_map_single_min_zoom']) ? $wiloke->aThemeOptions['general_map_single_min_zoom'] : -1;
	    $mapSingleCenterZoom = isset($wiloke->aThemeOptions['general_map_single_center_zoom']) && !empty($wiloke->aThemeOptions['general_map_single_center_zoom']) ? $wiloke->aThemeOptions['general_map_single_center_zoom'] : 60;

        if ( $paymentMode != 'sandbox' ){
            $twoCheckoutMode = 'production';
	        $twoCheckOutSellerID = WilokePublic::getPaymentField('2co_live_seller_id');
	        $twoCheckOutPublishableKey = WilokePublic::getPaymentField('2co_live_publishable_key');
        }else{
	        $twoCheckOutSellerID = WilokePublic::getPaymentField('2co_sandbox_seller_id');
	        $twoCheckOutPublishableKey = WilokePublic::getPaymentField('2co_sandbox_publishable_key');
	        $twoCheckoutMode = 'sandbox';
        }

	    $toggleListingShortcodes = '';
        if ( isset($_REQUEST['package_id']) ){
	        $aPackageSettings = WilokeAddListing::packageAllow();
	        $toggleListingShortcodes = isset($aPackageSettings['toggle_listing_shortcode']) ? $aPackageSettings['toggle_listing_shortcode'] : 'enable';
        }

        $disableFetchNotification = 'no';
        if (
            (isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'preview')
            || (
                    isset($post->ID) && ( ($post->ID == \WilokeListgoFunctionality\Framework\Payment\PaymentConfiguration::getField('addlisting')) || ($post->ID == \WilokeListgoFunctionality\Framework\Payment\PaymentConfiguration::getField('checkout')) || ($post->ID == \WilokeListgoFunctionality\Framework\Payment\PaymentConfiguration::getField('package'))
                    )
            ) ){
	        $disableFetchNotification = 'yes';
        }

        $countryRestriction = isset($wiloke->aThemeOptions['listing_country_restriction']) ? $wiloke->aThemeOptions['listing_country_restriction'] : '';

        wp_localize_script('jquery-migrate', 'WILOKE_LISTGO_TRANSLATION', apply_filters(
            'wiloke/listgo/general_transaltion', array(
		        'deniedaccess'      => esc_html__('You don\'t have permission to access this page.', 'listgo'),
		        'selectoptions'     => esc_html__('Select an option', 'listgo'),
		        'requirelocation'   => esc_html__('Please select your location', 'listgo'),
		        'report'            => esc_html__('Could you please tell us why this article should not display on our website?', 'listgo'),
		        'opennow'           => esc_html__('Open now', 'listgo'),
		        'closednow'         => esc_html__('Closed now', 'listgo'),
		        'location'          => esc_html__('Location', 'listgo'),
		        'address'           => esc_html__('Address', 'listgo'),
		        'phone_number'      => esc_html__('Phone', 'listgo'),
		        'website'           => esc_html__('Website', 'listgo'),
		        'email'             => esc_html__('Contact Us', 'listgo'),
		        'addtofavorite'     => esc_html__('Favorite', 'listgo'),
		        'save'              => esc_html__('Save', 'listgo'),
		        'following'         => esc_html__('Follow this author', 'listgo'),
		        'ajaxerror'         => esc_html__('Something went wrong', 'listgo'),
		        'needsingup'        => esc_html__('Please sign up to follow this author', 'listgo'),
		        'notallowfollow'    => esc_html__('OOps! You can not follow yourself', 'listgo'),
		        'usernotexists'     => esc_html__('This user does not exist.', 'listgo'),
		        'followsuccess'     => esc_html__('Thanks for your following. You will receive each new post by email.', 'listgo'),
		        'signuptofollow'    => esc_html__('Sign up to follow this author\'s article', 'listgo'),
		        'isfollowing'       => esc_html__('You are following the author\'s article', 'listgo'),
		        'signupbutnotemail' => esc_html__('Please supply your email address', 'listgo'),
		        'finddirections'    => esc_html__('Find directions', 'listgo'),
		        'gotomap'           => esc_html__('Go To Map', 'listgo'),
		        'viewdetail'        => esc_html__('View Detail', 'listgo'),
		        'next'              => esc_html__('Next', 'listgo'),
		        'prev'              => esc_html__('Prev', 'listgo'),
		        'notfound'          => esc_html__('Sorry, we couldn\'t find what you\'re looking for. Please try another search.', 'listgo'),
		        'somethingwrong'    => esc_html__('Oops! Something went wrong. Please contact the administrator to report this issue', 'listgo'),
		        'packagenotexist'   => esc_html__('The package does not exists.', 'listgo'),
		        'emailexisted'      => esc_html__('The email address you entered is already in use on another account', 'listgo'),
		        'passwdrequired'    => esc_html__('Please enter your password.', 'listgo'),
		        'signupfail'        => esc_html__('Wrong username or password.', 'listgo'),
		        'wrongemail'        => esc_html__('You entered a wrong email, please try another one.', 'listgo'),
		        'titlerequired'     => esc_html__('The title is required', 'listgo'),
		        'contentrequired'   => esc_html__('The content is required', 'listgo'),
		        'packagerequired'   => esc_html__('Please select a package before submitting', 'listgo'),
		        'deninedsubmission' => esc_html__('Your submission has been rejected. Reason: The post id does not exist or you are not author of this article', 'listgo'),
		        'isreviewing'       => esc_html__('We are reviewing this article so You can not edit it at the time. Please be patient and waiting an email from us', 'listgo'),
		        'checkoutwrong'     => esc_html__('Checkout page is not found. Please contact the site manager about this issue.', 'listgo'),
		        'securitycodewrong' => esc_html__('Security code is wrong', 'listgo'),
		        'readmore'          => esc_html__('Read more', 'listgo'),
		        'getdirections'     => esc_html__('Get directions', 'listgo'),
		        'followingtext'     => esc_html__('Following', 'listgo'),
		        'unfollowingtext'   => esc_html__('Follow', 'listgo'),
		        'pinned'            => esc_html__('Pinned Listing', 'listgo'),
		        'unpinned'          => esc_html__('Pin To Top', 'listgo'),
		        'geocodefailed'     => esc_html__('Geocoder failed due to:', 'listgo'),
		        'noresults'         => esc_html__('No results found', 'listgo'),
		        'publish'           => esc_html__('Published', 'listgo'),
		        'pending'           => esc_html__('Pending', 'listgo'),
		        'expired'           => esc_html__('Expired', 'listgo'),
	        )
        ));

        ob_start();
        ?>
        var wilokeVisited = 0;
        if ( typeof WILOKE_GLOBAL === 'undefined' )
        {
            window.WILOKE_GLOBAL                = {};
            WILOKE_GLOBAL.isLoggedIn            = '<?php echo is_user_logged_in() ? "yes" : "no"; ?>';
            WILOKE_GLOBAL.isDisableFetchNotification = '<?php echo esc_js($disableFetchNotification); ?>';
            WILOKE_GLOBAL.userInfo              = '<?php echo json_encode($aUserInfo); ?>';
            WILOKE_GLOBAL.homeURI               = '<?php echo esc_js(urlencode(get_template_directory_uri() . '/')); ?>';
            WILOKE_GLOBAL.blogname              = '<?php echo esc_attr(get_option('blogname')); ?>';
            WILOKE_GLOBAL.wiloke_nonce          = '<?php echo esc_js(wp_create_nonce('wiloke-nonce')); ?>';
            WILOKE_GLOBAL.ajaxurl               = '<?php echo esc_url(admin_url("admin-ajax.php")); ?>';
            WILOKE_GLOBAL.postID                = <?php echo isset($post->ID) ? esc_js($post->ID) : -1; ?>;
            WILOKE_GLOBAL.authorID              = <?php echo isset($post->post_author) ? esc_js($post->post_author) : -1; ?>;
            WILOKE_GLOBAL.post_type             = '<?php echo isset($post->post_type) ? esc_js($post->post_type) : -1; ?>';
            WILOKE_GLOBAL.portfolio_data        = {};
            WILOKE_GLOBAL.portfolio_loaded_cats = {};
            WILOKE_GLOBAL.mappage = '<?php echo esc_url($mapPageUrl); ?>';
            WILOKE_GLOBAL.twoCheckoutSellerID = '<?php echo esc_attr($twoCheckOutSellerID); ?>';
            WILOKE_GLOBAL.twoCheckoutPublishableKey = '<?php echo esc_attr($twoCheckOutPublishableKey); ?>';
            WILOKE_GLOBAL.googleapikey  = '<?php echo esc_js($googleAPIKey); ?>';
            WILOKE_GLOBAL.maptoken  = '<?php echo esc_js($mapToken); ?>';
            WILOKE_GLOBAL.maptheme  = '<?php echo esc_js($mapTheme); ?>';
            WILOKE_GLOBAL.mapmaxzoom  = <?php echo esc_js($mapMaxZoom); ?>;
            WILOKE_GLOBAL.mapminzoom  = <?php echo esc_js($mapMinZoom); ?>;
            WILOKE_GLOBAL.centerZoom  = <?php echo esc_js($mapCenterZoom); ?>;
            WILOKE_GLOBAL.mapSingleMaxZoom  = <?php echo esc_js($mapSingleMaxZoom); ?>;
            WILOKE_GLOBAL.mapSingleMinZoom  = <?php echo esc_js($mapSingleMinZoom); ?>;
            WILOKE_GLOBAL.mapSingleCenterZoom  = <?php echo esc_js($mapSingleCenterZoom); ?>;
            WILOKE_GLOBAL.mapcluster  = <?php echo esc_js($mapClusterRadius); ?>;
            WILOKE_GLOBAL.toggleAskForPosition  = '<?php echo esc_js($toggleAskForPosition); ?>';
            WILOKE_GLOBAL.posts__not_in         = {};
            WILOKE_GLOBAL.woocommerce           = {};
            WILOKE_GLOBAL.is_debug = '<?php echo defined('WP_DEBUG') && WP_DEBUG ? 'true' : 'false'; ?>';
            WILOKE_GLOBAL.siteCache = {};
            WILOKE_GLOBAL.countryRestriction = '<?php echo esc_js($countryRestriction); ?>';
            WILOKE_GLOBAL.toggleListingShortcodes = '<?php echo esc_attr($toggleListingShortcodes); ?>';
            WILOKE_GLOBAL.twocheckoutMode = '<?php echo esc_attr($twoCheckoutMode); ?>';
        }
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        wp_add_inline_script('jquery-migrate', $content);
    }
}