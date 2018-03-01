<?php
class WilokeSearchSystem{
	public function __construct() {
		add_action('wp_ajax_wiloke_search_suggestion', array($this, 'searchSuggestion'));
		add_action('wp_ajax_nopriv_wiloke_search_suggestion', array($this, 'searchSuggestion'));
	}

	public function addFieldsToSearchForm(){
		if ( !is_page_template('templates/half-map.php') && !is_page_template('templates/listing.php') && !is_tax('listing_cat') && !is_tax('listing_location') && !is_tax('listing_tag') ){
			return false;
		}

		global $wiloke;
		if ( !isset($wiloke->aThemeOptions['listing_toggle_search_by_tag']) || ($wiloke->aThemeOptions['listing_toggle_search_by_tag'] == 'enable') ) :
			$aTags = get_terms(
				array(
					'taxonomy'   => 'listing_tag',
					'hide_empty' => true
				)
			);

			$aSearching = isset($_REQUEST['s_listing_tag']) ? $_REQUEST['s_listing_tag'] : '';

			if ( !empty($aTags) && !is_wp_error($aTags) ) : ?>
                <div class="form-item item--tags">
                    <label class="label"><?php esc_html_e('Filter by tags', 'listgo'); ?> <span class="toggle-tags"></span></label>
                    <div class="item--tags-toggle">
						<?php foreach ( $aTags as $oTag ) :
							$checked = !empty($aSearching) && in_array($oTag->term_id, $aSearching) ? 'checked' : ''; ?>
                            <label for="s_listing_tag<?php echo esc_attr($oTag->term_id); ?>" class="input-checkbox">
                                <input id="s_listing_tag<?php echo esc_attr($oTag->term_id); ?>" name="s_listing_tag[]" class="listgo-filter-by-tag" type="checkbox" value="<?php echo esc_attr($oTag->term_id); ?>" <?php echo esc_attr($checked); ?>>
                                <span></span>
								<?php echo esc_html($oTag->name); ?>
                            </label>
						<?php endforeach; ?>
                    </div>
                </div>
				<?php
			endif;
		endif;
	}

	public static function searchForm($mapPage=null, $isShowAdvancedFilter=false, $atts=array(), $currentlySearchOn=null){
		global $wiloke, $post;
		
		$isShowAdvancedFilter = apply_filters('wiloke/listgo/admin/public/show_advanced_filter', $isShowAdvancedFilter);

		$inputName = 's_search';
		$pageID = '';
		if ( empty($mapPage) ){
			if ( !empty($wiloke->aThemeOptions) ){
				if ( $wiloke->aThemeOptions['listing_search_page'] === 'self' ){
					$mapPage = get_permalink($post->ID);
					$pageID = $post->ID;
				}else{
					if ( isset($wiloke->aThemeOptions['header_search_map_page']) && !empty($wiloke->aThemeOptions['header_search_map_page']) ){
						$mapPage = get_permalink($wiloke->aThemeOptions['header_search_map_page']);
						$pageID = $wiloke->aThemeOptions['header_search_map_page'];
					}else{
						$mapPage =  home_url('/');
						$inputName = 's';
					}
				}
			}else{
				$mapPage =  home_url('/');
				$inputName = 's';
			}
		}
		$aLocations = get_terms(
			array(
				'taxonomy'    => 'listing_location',
				'orderby'     => isset($wiloke->aThemeOptions['listing_search_location_order_by']) ? $wiloke->aThemeOptions['listing_search_location_order_by'] : 'id',
				'hide_empty'  => 1,
				'number'      => isset($wiloke->aThemeOptions['listing_search_number_of_locations']) ? $wiloke->aThemeOptions['listing_search_number_of_locations'] : 10
			)
		);

		$aCategories = Wiloke::getTaxonomyHierarchy('listing_cat');

		$searchFieldTitle = isset($atts['search_field_title']) && !empty($atts['search_field_title']) ? $atts['search_field_title'] : esc_attr($wiloke->aThemeOptions['header_search_keyword_label']);
		$locationFieldTitle = isset($atts['location_field_title']) && !empty($atts['location_field_title']) ? $atts['location_field_title'] : esc_attr($wiloke->aThemeOptions['header_search_location_label']);

		$s = isset($_REQUEST['s_search']) ? $_REQUEST['s_search'] : get_search_query();
		$s = isset($_REQUEST['cache_previous_search']) && !empty($_REQUEST['cache_previous_search']) ? $_REQUEST['cache_previous_search'] : $s;

		$catID = '';
		if ( isset($_REQUEST['s_listing_cat']) ){
			$catID = $_REQUEST['s_listing_cat'][0];
		}elseif ( is_tax('listing_cat') ){
			$catID = get_queried_object()->term_id;
			$s = single_term_title('', false);
		}

		$sWithinRadius = $wiloke->aThemeOptions['listgo_search_default_radius'];
		if ( isset($_REQUEST['s_within_radius']) && !empty($_REQUEST['s_within_radius']) ){
			$sWithinRadius =  trim($_REQUEST['s_within_radius']);
		}

		$sUnit = isset($wiloke->aThemeOptions['listgo_search_default_unit']) ? $wiloke->aThemeOptions['listgo_search_default_unit'] : 'km';
		if ( isset($_REQUEST['s_unit']) && !empty($_REQUEST['s_unit']) ){
			$sUnit = trim($_REQUEST['s_unit']);
		}
		$maxRadius = !isset($wiloke->aThemeOptions['listgo_search_max_radius']) ? 20 : $wiloke->aThemeOptions['listgo_search_max_radius'];
		$minRadius = !isset($wiloke->aThemeOptions['listgo_search_min_radius']) ? 1 : $wiloke->aThemeOptions['listgo_search_min_radius'];

		?>
		<form action="<?php echo esc_url($mapPage); ?>" method="GET" id="listgo-searchform" class="form form--listing <?php echo esc_attr($currentlySearchOn); ?>">
			<?php if ( isset($atts['alignment']) && ( $atts['alignment'] === 'not_center' || $atts['alignment'] === 'not_center_2' || $atts['alignment'] === 'not_center_3') && !empty($atts['search_form_title']) ) : ?>
				<h3 class="wiloke-searchform-title"><?php echo esc_html($atts['search_form_title']); ?></h3>
			<?php endif; ?>
			<input type="hidden" name="page_id" value="<?php echo esc_attr($pageID); ?>">
			<div class="form-item item--search">
				<label for="<?php echo esc_attr($inputName); ?>" class="label"><?php echo esc_html($searchFieldTitle); ?></label>
				<span class="input-text input-icon-inside">
                    <input id="<?php echo esc_attr($inputName); ?>" type="text" name="<?php echo esc_attr($inputName); ?>" value="<?php echo esc_attr(stripslashes($s)); ?>">
					<?php
					if ( !empty($aCategories) && !is_wp_error($aCategories) ) :
						?>
						<input type="hidden" id="wiloke-original-search-suggestion" value="<?php echo esc_attr(json_encode($aCategories)); ?>">
						<input type="hidden" id="s_listing_cat" name="s_listing_cat[]" value="<?php echo esc_attr($catID); ?>">
					<?php endif; ?>
					<i class="input-icon icon_search"></i>
                    <input type="hidden" id="cache_previous_search" name="cache_previous_search" value="">
                </span>
			</div>
			<?php
			if ( !empty($aLocations) && !is_wp_error($aLocations) ){
				$aLocations = json_encode($aLocations);
			}else{
				$aLocations = '';
			}
			$toggleFilterByPrice = !isset($wiloke->aThemeOptions['listing_toggle_filter_price']) || ($wiloke->aThemeOptions['listing_toggle_filter_price']=='enable');
			self::renderLocationField($locationFieldTitle, $aLocations);
			?>
			<?php if ( $isShowAdvancedFilter ) : ?>
				<div class="form-item item--toggle-opennow" <?php if(!$toggleFilterByPrice) : ?>style="width: 50%;" <?php endif; ?>>
					<label for="s_opennow" class="checkbox-btn">
						<input id="s_opennow" type="checkbox" name="s_opennow" value="1">
						<span class="checkbox-btn-span"><i class="fa fa-clock-o"></i><?php esc_html_e('Open Now', 'listgo'); ?></span>
					</label>
				</div>
				<div class="form-item item--toggle-highestrated" <?php if(!$toggleFilterByPrice) : ?>style="width: 50%;" <?php endif; ?>>
					<label for="s_highestrated" class="checkbox-btn">
						<input id="s_highestrated" type="checkbox" name="s_highestrated" value="1">
						<span class="checkbox-btn-span"><i class="fa fa-star-o"></i><?php esc_html_e('Highest Rated', 'listgo'); ?></span>
					</label>
				</div>

				<?php if ( $toggleFilterByPrice ) : ?>
					<div class="form-item item--price">
						<?php
						$aPriceSegments = array_merge(
							array('all' => esc_html__('Cost - It doesn\'t matter', 'listgo')),
							$wiloke->aConfigs['frontend']['price_segmentation']
						);
						?>
						<span class="input-select2 input-icon-inside">
		                    <select id="s_price_segment" name="s_price_segment" class="js_select2" data-placeholder="<?php echo esc_html__('Cost', 'listgo'); ?>">
		                        <?php
		                        foreach ( $aPriceSegments as $price => $name ) :
			                        $name = isset($wiloke->aThemeOptions['header_search_'.$price.'_cost_label']) ? $wiloke->aThemeOptions['header_search_'.$price.'_cost_label'] : $name;
			                        ?>
			                        <option value="<?php echo esc_attr($price); ?>"><?php echo esc_html($name); ?></option>
		                        <?php endforeach; ?>
		                    </select>
		                </span>
					</div>
				<?php endif; ?>

				<div class="form-item item--radius">
					<div class="listgo-unit-wrapper">
						<label class="label" for="s_unit"><?php esc_html_e('Radius', 'listgo'); ?></label>
						<div class="listgo-unit">
							<i class="arrow_carrot-down"></i>
							<select id="s_unit" name="s_unit">
								<option value="km" <?php selected($sUnit, 'km'); ?>><?php esc_html_e('Kilometer', 'listgo'); ?></option>
								<option value="mi" <?php selected($sUnit, 'mi'); ?>><?php esc_html_e('Mile', 'listgo'); ?></option>
							</select>
						</div>
					</div>
					<div class="input-slider" data-max-radius="<?php echo esc_attr($maxRadius); ?>" data-min-radius="<?php echo esc_attr($minRadius); ?>" data-current-radius="<?php echo esc_attr($sWithinRadius); ?>">
						<input id="s_radius" name="s_radius" type="hidden" value="<?php echo esc_attr($sWithinRadius); ?>">
					</div>
				</div>
			<?php endif; ?>

			<?php do_action('wiloke/listgo/admin/public/search-form'); ?>

			<div class="form-item item--submit">
				<input type="submit" value="<?php esc_html_e('Search Now', 'listgo'); ?>" />
			</div>
		</form>
		<?php
	}

	public static function renderLocationField($locationFieldTitle, $aLocations){
		$termID = '';
		$termName = '';
		if ( isset($_REQUEST['location_term_id']) ){
			$termID = $_REQUEST['location_term_id'];
			$termName = $_GET['s_listing_location'];
		}elseif ( is_tax('listing_location') ){
			$termID = get_queried_object()->term_id;
			$termName = single_term_title('', false);
		}

		?>
		<div class="form-item item--localtion">
			<label for="s_listing_location" class="label"><?php echo esc_html($locationFieldTitle); ?></label>
			<span class="input-text input-icon-inside">
                <input type="text" id="s_listing_location" class="auto-location-by-google" name="s_listing_location" value="<?php echo esc_attr($termName); ?>">
                <input type="hidden" id="s-listing-location-suggestion" value="<?php echo esc_attr($aLocations); ?>">
                <input type="hidden" id="s-location-place-id" name="location_place_id" value="<?php echo isset($_REQUEST['location_place_id']) ? esc_attr($_REQUEST['location_place_id']) : ''; ?>">
                <input type="hidden" id="s-location-latitude-longitude-id" name="location_latitude_longitude" value="<?php echo isset($_REQUEST['location_latitude_longitude']) ? esc_attr($_REQUEST['location_latitude_longitude']) : ''; ?>">
                <input type="hidden" id="s-location-term-id" name="location_term_id" value="<?php echo esc_attr($termID); ?>">
                <i id="listgo-current-location" class="input-icon icon_pin_alt"></i>
            </span>
		</div>
		<?php
	}

	public function searchSuggestion(){
		$aData = $_GET;

		if ( !isset($aData['security']) || !check_ajax_referer('wiloke-nonce', 'security', true) ){
			wp_send_json_error(array('message'=>esc_html__('Something went wrong', 'listgo')));
		}

		if ( !isset($aData['s']) || empty($aData['s']) ){
			wp_send_json_error(array('message'=>esc_html__('Please enter in your destination', 'listgo')));
		}

		$aArgs = array(
			's'                 => $aData['s'],
			'posts_per_page'    => 5,
			'post_type'         => 'listing',
			'post_status'       => 'publish'
		);

		$aTaxQuery = array();
		$nothingFoundText = esc_html__('Nothing found. Please try another keyword.', 'listgo');
		if ( isset($aData['latLng']) && !empty($aData['latLng']) ){
			$aLatLng = explode(',', $aData['latLng']);
			$distance = isset($aData['sWithin']) ? abs($aData['sWithin']) :  10;
			$unit = isset($aData['sUnit']) ? trim($aData['sUnit']) :  'km';
			$aListingInRadius = \WilokeListgoFunctionality\Model\GeoPosition::searchLocationWithin(trim($aLatLng[0]), trim($aLatLng[1]), $distance, $unit);
			if ( empty($aListingInRadius) ){
				wp_send_json_error(array('message'=>$nothingFoundText));
			}

			$aArgs['post__in'] = $aListingInRadius['IDs'];
		}else{
			if ( isset($aData['listing_locations']) && !empty($aData['listing_locations']) ){
				$aData['listing_locations'] = isset($aData['location_term_id']) && !empty($aData['location_term_id']) ? $aData['location_term_id'] : $aData['listing_locations'];

				$aLocationArgs = WilokePublic::parseLocationQuery($aData);
				if ( empty($aLocationArgs) ){
					wp_send_json_error(array('message'=>$nothingFoundText));
				}else{
					$aTaxQuery[] = $aLocationArgs;
				}
			}
		}

		if ( isset($aData['listing_cats']) && !empty($aData['listing_cats']) && ($aData['listing_cats'] !== 'all') ){
			$aTaxQuery[] = array(
				'taxonomy' => 'listing_cat',
				'field'    => 'term_id',
				'terms'    => $aData['listing_cats']
			);
		}

		if ( isset($aData['listing_tags']) && !empty($aData['listing_tags']) && ($aData['listing_tags'] !== 'all') ){
			$aTaxQuery[] = array(
				'taxonomy' => 'listing_tag',
				'field'    => 'term_id',
				'terms'    => $aData['listing_tags']
			);
		}

		if ( !empty($aTaxQuery) ){
			if ( count($aTaxQuery) > 1 ){
				$aArgs['tax_query']['relation'] = 'AND';
			}
			$aArgs['tax_query'][] = $aTaxQuery;
		}

		$aArgs['orderby'] = 'menu_order post_date';
		$query = new WP_Query($aArgs);

		$aInfo = array();

		if ( $query->have_posts() ){
			while($query->have_posts()){
				$query->the_post();
				$aInfo[$query->post->ID] = WilokeListingLayout::listingInfo($query->post);
			}
			wp_send_json_success($aInfo);
		}else{
			wp_send_json_error(array('message'=>$nothingFoundText));
		}
	}
}