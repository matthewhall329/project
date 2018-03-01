<?php
function wiloke_shortcode_awesome_map($atts){
	$atts = shortcode_atts(
		array(
			'source_map'        => 'all',
			'listing_location'  => '',
			'listing_cat'       => '',
			's'                 => '',
			'disable_draggable_when_init'=> 'disable',
			'popup_showing_tax' => 'listing_location',
			'center'            => null,
			'centerZoom'        => null,
			'css'               => '',
			'extract_class'     => '',
            'order_by'          => 'post_date',
            'order'             => 'DESC',
			'map_theme'         => ''
		),
		$atts
	);

	$aLocations  = array();
	$aCategories = array();

	if ( $atts['source_map'] === 'all' ){
		$aLocations = Wiloke::getTermCaching('listing_location');
		$aCategories = Wiloke::getTaxonomyHierarchy('listing_cat');
    }elseif ( $atts['source_map'] === 'listing_cat' ){
	    $catIDs = !empty($atts['listing_cat']) ? explode(',', $atts['listing_cat']) : '';
		$aCategories = Wiloke::getTaxonomyHierarchy('listing_cat', $catIDs);
		$atts['listing_cat_ids'] = $catIDs;
    }else{
		$catIDs = !empty($atts['listing_location']) ? explode(',', $atts['listing_location']) : '';
		$aLocations = Wiloke::getTermCaching('listing_location', $catIDs, array(
            'number' => 10
        ));
		$atts['listing_location_ids'] = $catIDs;
    }

    $atts['s_current_cat'] = $currentCat = isset($_REQUEST['s_listing_cat']) ? $_REQUEST['s_listing_cat'] : '';
	$isFocusSearch = false;
	if ( isset($_REQUEST['s_search']) && !empty($_REQUEST['s_search']) ){
		$search = $atts['s'] = $_REQUEST['s_search'];
		if ( empty($currentCat) ){
			$isFocusSearch = true;
        }
	}else{
		$search = $atts['s'] = '';
	}

	$atts['s_current_location'] = $currentLocation = isset($_REQUEST['s_listing_location']) && $_REQUEST['s_listing_location'] !== 'all' ? explode(',', $_REQUEST['s_listing_location']) : '';
	$atts['s_current_tag'] = $currentLocation = isset($_REQUEST['s_listing_tag']) ? $_REQUEST['s_listing_tag'] : '';

	$atts['mapTheme'] = empty($atts['map_theme']) ? '' : $atts['map_theme'];

    $aListings = WilokeListgoMap::getMap($atts, $isFocusSearch);
    if ( empty($atts['center']) ){
        foreach ( $aListings as $key => $aListing ){
            $atts['center'] = $aListing['listing_settings']['map']['latlong'];
            break;
        }
    }
    unset($atts['s_current_cat']);
    unset($atts['s']);
    unset($atts['s_current_location']);
    unset($atts['s_current_tag']);

    global $wiloke;

	$wrapperClass = $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');
    $uid = uniqid();
	ob_start(); ?>

    <div class="listgo-map-container <?php echo esc_attr($wrapperClass); ?>" id="<?php echo esc_attr($uid) ?>">
        <?php
            if ( empty($wiloke->aThemeOptions['general_mapbox_api']) || empty($wiloke->aThemeOptions['general_map_theme']) ){
                if ( current_user_can('edit_theme_options') ){
                    WilokeAlert::render_alert( __('The <strong>MapBox Token</strong> and <strong>MapBox theme</strong> are required. You can find these settings by clicking on <strong>Theme Options</strong> at the top bar -> <strong>General</strong> or <strong>Appearance</strong> -> <strong>Theme Options</strong> -> <strong>General</strong>. ', 'listgo'), 'warning' );
                }
            }
        ?>
        <div class="listgo-map-wrap" data-id="<?php echo esc_attr($uid) ?>">
            <div id="listgo-map" data-disabledraggableoninit="<?php echo esc_attr($atts['disable_draggable_when_init']); ?>" class="listgo-map full-height" data-configs="<?php echo esc_attr(json_encode($atts)); ?>" data-listings="<?php echo esc_attr(json_encode($aListings)); ?>" data-orderby="<?php echo esc_attr($atts['order_by']); ?>" data-order="<?php echo esc_attr($atts['order']); ?>"></div>

            <form method="GET" id="listgo-searchform" class="listgo-search-on-map">
                <div class="listgo-map__singlebox">
                    <button class="searchbox-hamburger" type="button"></button>
                    <input id="s_search" type="search" value="<?php echo esc_attr(stripslashes($search)); ?>" placeholder="<?php esc_html_e('Search...', 'listgo'); ?>">
                    <?php
                    if ( !empty($aCategories) && !is_wp_error($aCategories) ) :
	                    ?>
                        <input type="hidden" id="wiloke-original-search-suggestion" value="<?php echo esc_attr(json_encode($aCategories)); ?>">
                        <input type="hidden" id="s_listing_cat" name="s_listing_cat" value="<?php echo !empty($currentCat) ? esc_attr($currentCat[0]) : ''; ?>">
                    <?php endif; ?>
                    <input type="hidden" id="cache_previous_search" name="cache_previous_search" value="">
                    <button id="listgo-submit-searchkeyword" class="searchbox-icon"></button>
                </div>

                <div id="listgo-map__sidebar" class="listgo-map__settings">
                    <div class="listgo-map__field">
	                    <?php
	                    if ( !empty($aLocations) && !is_wp_error($aLocations) ){
		                    $aLocations = json_encode($aLocations);
	                    }else{
		                    $aLocations = '';
	                    }
	                    WilokeSearchSystem::renderLocationField(esc_html__('Location', 'listgo'), $aLocations);
	                    ?>
                        <div class="field-item">
                            <div class="row">
                                <div class="col-xs-6">
                                    <label for="s_opennow" class="checkbox-btn">
                                        <input id="s_opennow" type="checkbox" name="s_opennow" value="1">
                                        <span class="checkbox-btn-span"><i class="fa fa-clock-o"></i><?php esc_html_e('Open Now', 'listgo'); ?></span>
                                    </label>
                                </div>
                                <div class="col-xs-6">
                                     <label for="s_highestrated" class="checkbox-btn">
                                        <input id="s_highestrated" type="checkbox" name="s_highestrated" value="1">
                                        <span class="checkbox-btn-span"><i class="fa fa-star-o"></i><?php esc_html_e('Highest Rated', 'listgo'); ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="listgo-map__apply"><?php esc_html_e('Apply', 'listgo') ?></div>
                    </div>
                    <div class="listgo-map__result">
                        <ul id="listgo-map__show-listings"></ul>
                        <p id="wiloke-map-no-results" class="hidden" style="padding: 20px; text-align: center; font-weight: bold"><?php esc_html_e('Sorry, We found no listings matching your search.', 'listgo'); ?></p>
                    </div>
                </div>
            </form>

            <div class="listgo-map-wrap-expand"><i class="fa fa-expand"></i></div>
        </div>
    </div>
	<?php
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}
