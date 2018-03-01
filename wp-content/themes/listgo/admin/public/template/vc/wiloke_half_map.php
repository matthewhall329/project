<?php
function wiloke_shortcode_half_map($aAtts){
	$aAtts = shortcode_atts(
		array(
			'layout'                        => 'listing--list',
			'show_terms'                    => 'both',
			'link_to_map_page_additional_class' => 'listgo_panto_marker',
			'link_to_map_page_text'         => esc_html__('Pan to marker', 'listgo'),
			'listing_location'              => '',
			'order_by'                      => 'post_date',
			'posts_per_page'                => 4,
			'listing_cat'                   => '',
			's'                             => '',
			'disable_draggable_when_init'   => 'disable',
			'toggle_render_favorite'   		=> 'enable',
			'toggle_render_view_detail'   	=> 'enable',
			'toggle_render_address'         => 'enable',
			'toggle_render_find_direction'  => 'enable',
			'toggle_render_link_to_map_page'=> 'enable',
			'popup_showing_tax'             => 'listing_location',
			'center'                        => null,
			'before_item_class'             => 'col-sm-6',
			'item_class' 		            => 'listing listing--grid',
			'max_cluster_radius'            => '',
			'map_theme'                     => '',
			'image_size'                    => 'medium',
			'max_zoom'                      => '',
			'min_zoom'                      => '',
			'center_zoom'                   => '',
			'extract_class'                 => '',
			'css'                           => ''
		),
		$aAtts
	);

	wp_enqueue_script('listgo-halfmap');
	global $wiloke;

	$aAtts['maxClusterRadius']  = $aAtts['max_cluster_radius'];
	$aAtts['mapTheme']          = $aAtts['map_theme'];
	$aAtts['maxZoom']           = $aAtts['max_zoom'];
	$aAtts['minZoom']           = $aAtts['min_zoom'];
	$aAtts['centerZoom']        = $aAtts['center_zoom'];

	unset($aAtts['max_cluster_radius']);
	unset($aAtts['map_theme']);
	unset($aAtts['min_zoom']);
	unset($aAtts['center_zoom']);

	if ( empty($aAtts['center']) ){
	    unset($aAtts['center']);
    }

	$aArgs = array(
		'posts_per_page' => !empty($aAtts['posts_per_page']) ? abs($aAtts['posts_per_page']) : 10,
		'post_type'      => 'listing',
		'post_status'    => 'publish'
	);

	$aArgs['meta_query'] = array(
		'relation' => 'AND',
		array(
			'meta_key' => 'listing_settings',
			'value'    => 'latlong";s:0:',
			'compare'  => 'NOT LIKE'
		),
		array(
			'meta_key' => 'wiloke_submission_do_not_show_on_map',
			'value'    => 'disable',
			'compare'  => '!='
		)
	);

	$aAtts['s_current_cat'] = $currentCat = isset($_REQUEST['s_listing_cat']) ? $_REQUEST['s_listing_cat'] : '';

	if ( empty($currentCat) ){
		$aAtts['s'] = isset($_REQUEST['s_search']) ? $_REQUEST['s_search'] : '';
	}

	$aAtts['s_current_location'] = $currentLocation = isset($_REQUEST['s_listing_location']) && $_REQUEST['s_listing_location'] !== 'all' ? explode(',', $_REQUEST['s_listing_location']) : '';
	$aAtts['s_current_tag'] = $currentLocation = isset($_REQUEST['s_listing_tag']) ? $_REQUEST['s_listing_tag'] : '';

	if ( isset($_REQUEST['s_listing_cat']) && !empty($_REQUEST['s_listing_cat']) && !empty($_REQUEST['s_listing_cat'][0]) ){
		$aArgs['tax_query'][] = array(
			'taxonomy' => 'listing_cat',
			'field'    => 'term_id',
			'terms'    => $_REQUEST['s_listing_cat']
		);
	}else if ( isset($_REQUEST['s_search']) ){
	    $aArgs['s'] = $_REQUEST['s_search'];
    }

	$isNotFound = false;

    if ( isset($_REQUEST['location_term_id']) && !empty($_REQUEST['location_term_id']) ){
	    $aArgs['tax_query'][] = array(
		    'taxonomy' => 'listing_location',
		    'field'    => 'term_id',
		    'terms'    => $_REQUEST['location_term_id']
	    );
    }else if ( isset($_REQUEST['location_latitude_longitude']) && !empty($_REQUEST['location_latitude_longitude']) ){
	    $distance = !empty($wiloke->aThemeOptions['listgo_search_default_radius']) ? $wiloke->aThemeOptions['listgo_search_default_radius'] :  10;
	    $unit = !empty($wiloke->aThemeOptions['listgo_search_default_unit']) ? $wiloke->aThemeOptions['listgo_search_default_unit'] :  'km';
	    $aLatLng = explode(',', $_REQUEST['location_latitude_longitude']);
	    $aListingInRadius = \WilokeListgoFunctionality\Model\GeoPosition::searchLocationWithin($aLatLng[0], $aLatLng[1], $distance, $unit);
	    if ( empty($aListingInRadius) ){
            $isNotFound = true;
        }else{
		    $aArgs['post__in'] = $aListingInRadius['IDs'];
        }
    }

	$wrapperClass = $aAtts['extract_class'] . ' ' . vc_shortcode_custom_css_class($aAtts['css'], ' ');
	$uid = uniqid();
	ob_start(); ?>

	<div id="<?php echo esc_attr($uid) ?>" class="listgo-map-container listgo-listlayout-on-page-template <?php echo esc_attr($wrapperClass); ?>">
		<?php
		if ( empty($wiloke->aThemeOptions['general_mapbox_api']) || empty($wiloke->aThemeOptions['general_map_theme']) ){
			if ( current_user_can('edit_theme_options') ){
				WilokeAlert::render_alert( __('The <strong>MapBox Token</strong> and <strong>MapBox theme</strong> are required. You can find these settings by clicking on <strong>Theme Options</strong> at the top bar -> <strong>General</strong> or <strong>Appearance</strong> -> <strong>Theme Options</strong> -> <strong>General</strong>. ', 'listgo'), 'warning' );
			}
		}
		?>
		<div id="listgo-half-map-wrap" class="listgo-half-map-wrap" data-id="<?php echo esc_attr($uid) ?>" data-configs="<?php echo esc_attr(json_encode($aAtts)); ?>">
			<span class="header-page__breadcrumb-filter"><i class="fa fa-filter"></i> <?php esc_html_e('Filter', 'listgo'); ?></span>
            <div id="wiloke-half-results" class="wiloke-half-results">
                <div class="wiloke-listing-layout" data-atts="<?php echo esc_attr(json_encode($aAtts)); ?>">
                    <div class="from-wide-listing">
                        <div class="from-wide-listing__header">
                            <span class="from-wide-listing__header-title"><?php echo esc_html__('Filter', 'listgo') ?></span>
                            <span class="from-wide-listing__header-close"><span>×</span> <?php echo esc_html__('Close', 'listgo') ?></span>
                        </div>
                        <?php WilokeSearchSystem::searchForm(null, true, array(), 'listing-template'); ?>
                        <div id="listgo-mobile-search-only" class="from-wide-listing__footer">
                            <span><?php echo esc_html__('Apply', 'listgo') ?></span>
                        </div>
                    </div>

                    <div class="listgo-wrapper-grid-items row row-clear-lines" data-col-lg="3">
                        <?php
                        if ( !$isNotFound ){
	                        if ( isset($aArgs['tax_query']) && count($aArgs['tax_query']) > 1 ){
		                        $aArgs['tax_query']['relation'] = 'AND';
	                        }
	                        $query = new WP_Query($aArgs);

	                        if ( $query->have_posts() ){
		                        while ($query->have_posts()){
			                        $query->the_post();
			                        WilokeListingLayout::listingQuery($aAtts, null, true);
		                        }
		                        wp_reset_postdata();
	                        }else{
		                        \WilokeListgoFunctionality\Frontend\FrontendListingManagement::message(
			                        array(
				                        'message' => esc_html__('Sorry, We can\'t found what are you looking for.', 'listgo')
			                        ),
			                        'warning',
			                        false
		                        );
                            }
                        }else{
                            \WilokeListgoFunctionality\Frontend\FrontendListingManagement::message(
                               array(
                                   'message' => esc_html__('Sorry, We can\'t found what are you looking for.', 'listgo')
                               ),
                                'warning',
                                false
                            );
                        }
                        ?>
                    </div>

                    <div id="wiloke-listgo-listlayout-pagination" class="nav-links text-center" data-total="<?php echo !$isNotFound ? esc_attr($query->found_posts) : 0; ?>" data-postsperpage="<?php echo esc_attr($aAtts['posts_per_page']); ?>"></div>

                </div>
            </div>
			<div id="listgo-half-map" data-disabledraggableoninit="" class="listgo-half-map listgo-map">
				<span class="listgo-half-map__close"></span>
			</div>
		</div>
	</div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
