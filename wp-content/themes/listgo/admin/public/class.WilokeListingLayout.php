<?php
use WilokeListGoFunctionality\AlterTable\AlterTablePriceSegment as WilokePriceSegmentTbl;
use WilokeListGoFunctionality\AlterTable\AlterTableBusinessHours as WilokeBusinessHoursTbl;
use WilokeListgoFunctionality\Model\GeoPosition;
use WilokeListgoFunctionality\Frontend\FrontendRating;

class WilokeListingLayout{
	public static $oListingCollections;

	public function __construct() {
		add_action('wp_ajax_nopriv_wiloke_loadmore_listing_layout', array($this, 'loadmoreListings'));
		add_action('wp_ajax_wiloke_loadmore_listing_layout', array($this, 'loadmoreListings'));

		add_action('wp_ajax_wiloke_fetch_listing', array($this, 'fetch_listing'));
		add_action('wp_ajax_nopriv_wiloke_fetch_listing', array($this, 'fetch_listing'));

		add_action('wp_ajax_nopriv_wiloke_get_listing_near_by_widget', array($this, 'fetchListingNearBy'));
		add_action('wp_ajax_wiloke_get_listing_near_by_widget', array($this, 'fetchListingNearBy'));
	}

	public function renderListingNearByItem($post){   
		global $wiloke;         
        if ( has_post_thumbnail($post->ID) ){
            $thumbnail = get_the_post_thumbnail_url($post->ID, array(460, 345));
        }else{
	        $thumbnail = wp_get_attachment_image_url($wiloke->aThemeOptions['listing_header_image']['id'], array(460, 345));
        }
        ?>
        <div class="col-sm-6 <?php echo esc_attr($wiloke->aThemeOptions['single_post_related_number_of_articles']); ?>">
           <div class="listing_related-item">
               <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                    <div class="listing_related-item__media" style="background-image: url(<?php echo esc_url($thumbnail); ?>)">
                       <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($post->post_title); ?>">
                   </div>
                   
                   <div class="listing_related-item__body">
                       <h2 class="listing_related-item__title"><?php echo esc_html($post->post_title); ?></h2>
                   </div>
               </a>
           </div>
       </div>
       <?php
	}

	public function fetchListingNearBy(){
		global $wiloke;

		$aLatLng = $_POST['latLng'];
		$distance = isset($wiloke->aThemeOptions['listgo_search_default_radius']) ? $wiloke->aThemeOptions['listgo_search_default_radius'] :  10;
		$unit = isset($wiloke->aThemeOptions['listgo_search_default_unit']) ? $wiloke->aThemeOptions['listgo_search_default_unit'] : 'km';

		$aListingInRadius = GeoPosition::searchLocationWithin(trim($aLatLng['lat']), trim($aLatLng['lng']), $distance, $unit, 3);
		
	 	$aArgs['post_type']		= 'listing';
        $aArgs['post_status'] 	= 'publish';
        $aArgs['post__not_in'] 	= array($_POST['postID']);

		if ( empty($aListingInRadius) ){
			$aCategories = wp_get_post_terms($_POST['postID'], 'listing_location');

            if ( empty($aCategories) || is_wp_error($aCategories) ){
                wp_send_json_error();
            }
			$aTermIDs = array();
            foreach ( $aCategories as $oCat ){
                $aTermIDs[] = $oCat->term_id;
            }
            $aArgs['tax_query'][] = array(
                'taxonomy' => 'listing_location',
                'field' => 'term_id',
                'terms' => $aTermIDs,
            );

        	$aArgs['posts_per_page'] 	= 2;

			$oQuery = new WP_Query($aArgs);

			if ( !$oQuery->have_posts() ){
				wp_send_json_error();
			}

			ob_start();
			while ($oQuery->have_posts()) {
				$oQuery->the_post();
				$this->renderListingNearByItem($oQuery->post);
			}
			$content = ob_get_contents();
			ob_end_clean();
			wp_send_json_success($content);
		}else{
			$aArgs['post__in'] = $aListingInRadius['IDs'];
			$oQuery = new WP_Query($aArgs);
			
			ob_start();
			if ( $oQuery->have_posts() ){
				while ($oQuery->have_posts()) {
					$oQuery->the_post();
					$this->renderListingNearByItem($oQuery->post);
				}
			}
			$content = ob_get_contents();
			ob_end_clean();
			wp_send_json_success($content);
		}
	}

	public static function listingQuery($atts, $mapPage='', $isGetInfo=true, $postID=null){
		global $wiloke;
		$aInfo = $terms = array();
		if ( empty($postID) ){
			global $post;
			$postID = $post->ID;
		}else{
			$post = get_post($postID);
		}

		if ( !empty(self::$oListingCollections) && isset(self::$oListingCollections[$postID]) ){
			$aListing       = json_decode(self::$oListingCollections[$postID], true);
			$aFeaturedImage = $aListing['featured_image'];
			$aPageSettings  = $aListing['listing_settings'];
			$thumbnail      = get_the_post_thumbnail_url($postID);
			$terms          = $aListing['terms_id'];
			$aInfo          = $aListing;
		}else{
			if( !has_post_thumbnail($postID) || (!$aFeaturedImage = Wiloke::generateSrcsetImg(get_post_thumbnail_id($postID), $atts['image_size'])) ){
				if ( !empty($wiloke->aThemeOptions['listing_header_image']['id']) ){
					$aFeaturedImage = Wiloke::generateSrcsetImg($wiloke->aThemeOptions['listing_header_image']['id'], $atts['image_size']);
					if ( !$aFeaturedImage ){
						$aFeaturedImage['main']['src'] = $wiloke->aThemeOptions['listing_header_image']['url'];
						$aFeaturedImage['main']['width'] = $wiloke->aThemeOptions['listing_header_image']['width'];
						$aFeaturedImage['main']['height'] = $wiloke->aThemeOptions['listing_header_image']['height'];
						$aFeaturedImage['srcset'] = '';
						$aFeaturedImage['sizes']  = '';
					}
				}else{
					$aFeaturedImage['main']['src']      = get_template_directory_uri() . '/img/featured-image.jpg';
					$aFeaturedImage['main']['width']    = 1000;
					$aFeaturedImage['main']['height']   = 500;
					$aFeaturedImage['srcset']           = '';
					$aFeaturedImage['sizes']            = '';
				}
			}
			$thumbnail     = get_the_post_thumbnail_url($postID);
			$aPageSettings = Wiloke::getPostMetaCaching($postID, 'listing_settings');

			if ( $atts['show_terms'] === 'listing_location' ){
				$terms = wp_get_post_terms($postID, 'listing_location', array('fields'=>'ids'));
			}elseif ( $atts['show_terms'] === 'listing_cat' ){
				$terms = wp_get_post_terms($postID, 'listing_cat', array('fields'=>'ids'));
			}else{
				$terms = wp_get_post_terms($postID, array('listing_location', 'listing_cat'), array('fields'=>'ids'));
			}

			if ( $isGetInfo ){
				$aListingLocation   = Wiloke::getPostTerms($post, 'listing_location');
				$aListingLocation   = WilokePublic::putTermLinks($aListingLocation);

				$aListingListingCat = Wiloke::getPostTerms($post, 'listing_cat');
				$aListingListingCat = WilokePublic::putTermLinks($aListingListingCat);

				$aListingTags       = Wiloke::getPostTerms($post, 'listing_tag');
				$aListingTags       = WilokePublic::putTermLinks($aListingTags);

				$aListingLocationIDs    = WilokePublic::getTermIDs($aListingLocation);
				$aListingListingCatIDs  = WilokePublic::getTermIDs($aListingListingCat);
				$aListingTagIDs         = WilokePublic::getTermIDs($aListingTags);
				$locationPlaceID = null;
				$firstLocationID = null;
				$parentLocation = null;

				if ( isset($aListingLocationIDs[0]) ){
					$locationPlaceID = get_term_meta($aListingLocationIDs[0], 'wiloke_listing_location_place_id', true);
					$parentLocation   = $aListingLocation[0]->parent;
					$firstLocationID = $aListingLocationIDs[0];
				}

				$aTerms = array_merge($aListingLocationIDs, $aListingListingCatIDs, $aListingTagIDs);
				$favoriteClass = '';
				if ( class_exists('WilokeListGoFunctionality\AlterTable\AlterTableFavirote') ){
					$favorite = \WilokeListgoFunctionality\AlterTable\AlterTableFavirote::getStatus($post->ID);
					$favoriteClass = !empty($favorite) ? ' active' : '';
				}

				$aFirstCatInfo = array();
				if ( isset($aListingListingCat[0]) ){
					$aFirstCatInfo = Wiloke::getTermOption($aListingListingCat[0]->term_id);
				}

				$aInfo = array(
					'ID'            => $post->ID,
					'link'          => get_permalink($post->ID),
					'title'         => $post->post_title,
					'is_featured_post' => get_post_meta($post->ID, 'wiloke_listgo_toggle_highlight', true),
					'post_except'   => Wiloke::wiloke_content_limit(200, $post, false, $post->post_content, true),
					'post_date'     => $post->post_date,
					'business_status'=>WilokePublic::businessStatus($post),
					'post_date_with_format' => get_the_date($post->ID),
					'average_rating'        => WilokePublic::calculateAverageRating($post),
					'featured_image'        => $aFeaturedImage,
					'thumbnail'        => $thumbnail,
					'favorite_class'   => $favoriteClass,
					'author'           => WilokeAuthor::getAuthorInfo($post->post_author, true),
					'author_id'        => $post->post_author,
					'comment_count'    => $post->comment_count,
					'listing_settings' => $aPageSettings,
					'terms_id'         => $aTerms,
					'listing_location' => $aListingLocation,
					'placeID'          => $locationPlaceID,
					'first_location_id'=> $firstLocationID,
					'parentLocationID' => $parentLocation,
					'listing_cat'      => $aListingListingCat,
					'listing_cat_settings' => $aFirstCatInfo,
					'listing_tag'      => $aListingTags
				);
			}
		}

		$termClasses = !empty($terms) && !is_wp_error($terms) ? implode(' ', $terms) : '';

		if ( file_exists(get_template_directory() . '/admin/public/template/vc/listing-layout/'.$atts['layout'].'.php') ){
			include get_template_directory() . '/admin/public/template/vc/listing-layout/'.$atts['layout'].'.php';
		}else{
			include get_template_directory() . '/admin/public/template/vc/listing-layout/rest-layouts.php';
		}
	}

	public static function listingInfo($post){
		if ( !empty(WilokePublic::$oListingCollections) && isset(WilokePublic::$oListingCollections[$post->ID]) ){
			return json_decode(WilokePublic::$oListingCollections[$post->ID], true);
		}

		if( !has_post_thumbnail($post->ID) || (!$aFeaturedImage = Wiloke::generateSrcsetImg(get_post_thumbnail_id($post->ID), 'large')) ){
			$aFeaturedImage['main']['src']      = get_template_directory_uri() . '/img/featured-image.jpg';
			$aFeaturedImage['main']['width']    = 1000;
			$aFeaturedImage['main']['height']   = 500;
			$aFeaturedImage['srcset']           = '';
			$aFeaturedImage['sizes']            = '';
		}

		$aPageSettings      = Wiloke::getPostMetaCaching($post->ID, 'listing_settings');
		$aListingLocation   = Wiloke::getPostTerms($post, 'listing_location');
		$aListingLocation   = WilokePublic::putTermLinks($aListingLocation);

		$aListingListingCat = Wiloke::getPostTerms($post, 'listing_cat');
		$aListingListingCat = WilokePublic::putTermLinks($aListingListingCat);
		$aFirstCatInfo = array();
		if ( isset($aListingListingCat[0]) ){
			$aFirstCatInfo = Wiloke::getTermOption($aListingListingCat[0]->term_id);
		}

		$aListingTags  = Wiloke::getPostTerms($post, 'listing_tag');
		$aListingTags  = WilokePublic::putTermLinks($aListingTags);

		$aListingLocationIDs    = WilokePublic::getTermIDs($aListingLocation);
		$aListingListingCatIDs  = WilokePublic::getTermIDs($aListingListingCat);
		$aListingTagIDs         = WilokePublic::getTermIDs($aListingTags);

		$locationPlaceID = '';
		$firstLocationID = '';
		$parentLocation = null;
		if ( isset($aListingLocationIDs[0]) ){
			$locationPlaceID = get_term_meta($aListingLocationIDs[0], 'wiloke_listing_location_place_id', true);
			$parentLocation = $aListingLocation[0]->parent;
			$firstLocationID = $aListingLocationIDs[0];
		}

		$aTerms = array_merge($aListingLocationIDs, $aListingListingCatIDs, $aListingTagIDs);

		return array(
			'ID'                    => $post->ID,
			'link'                  => get_permalink($post->ID),
			'title'                 => $post->post_title,
			'post_except'           => Wiloke::wiloke_content_limit(200, $post, false, $post->post_content, true),
			'post_date'             => $post->post_date,
			'post_date_with_format' => get_the_date($post->ID),
			'featured_image'        => $aFeaturedImage,
			'first_cat_info'        => $aFirstCatInfo,
			'author'                => WilokeAuthor::getAuthorInfo($post->post_author, true),
			'author_id'             => $post->post_author,
			'comment_count'         => $post->comment_count,
			'listing_settings'      => $aPageSettings,
			'terms_id'              => $aTerms,
			'listing_location'      => $aListingLocation,
			'placeID'               => $locationPlaceID,
			'first_location_id'     => $firstLocationID,
			'parentLocationID'      => $parentLocation,
			'listing_cat'           => $aListingListingCat,
			'listing_tag'           => $aListingTags,
			'business_hours'        => WilokePublic::businessStatus($post)
		);

	}

	public function fetch_listing(){
		$postsPerPage = isset($_POST['posts_per_page']) && absint($_POST['posts_per_page']) <= 30 ? $_POST['posts_per_page'] : 30;

		$aArgs = array(
			'post_type'        => 'listing',
			'posts_per_page'   => absint($postsPerPage),
			'post__not_in'     => $_POST['post__not_in'],
			'post_status'      => 'publish'
		);

		if ( !empty($_POST['term']) && $_POST['term'] !== 'all' ){
			$aArgs['tax_query'] = array(
				array(
					'taxonomy'  => $_POST['filter_by'],
					'field'     => 'slug',
					'terms'     => $_POST['term']
				)
			);
		}

		$query = new WP_Query($aArgs);

		$data = self::listingInfo($query);

		wp_send_json_success(array('content'=>$data));
	}

	public function loadmoreListings(){
		if ( !check_ajax_referer('wiloke-nonce', 'security', false) ){
			wp_send_json_error();
		}

		$aData = $_POST;
		$hasCat = false;
		if ( !$aData['is_focus_query'] && (!isset($aData['post__not_in']) || empty($aData['post__not_in'])) ){
			wp_send_json_error();
		}

		if ( $aData['is_open_now'] === 'true' || $aData['is_highest_rated'] === 'true' || (!empty($aData['price_segment']) && ($aData['price_segment'] !== 'all')) ){
			$this->complexSearching($aData);
		}else{
			$postsPerPage = isset($aData['posts_per_page']) && absint($aData['posts_per_page']) <= 30 ? $aData['posts_per_page'] : 30;
			$orderBy = isset($aData['order_by']) ? $aData['order_by']  : 'post_date';

			$aArgs = array(
				'post_type'        => 'listing',
				'posts_per_page'   => intval($postsPerPage),
				'post__not_in'     => $aData['post__not_in'],
				'post_status'      => 'publish',
				'order_by'         => $orderBy,
				'order'            => 'DESC'
			);

			if ( !empty($aData['paged']) && ($aData['atts']['display_style'] === 'pagination') ){
				$aArgs['paged'] = $aData['paged'];
				unset($aArgs['post__not_in']);
			}

			if ( empty($aData['filter_by']) || ($aData['filter_by'] == 'all') ){
				if ( isset($aData['latLng']) && !empty($aData['latLng']) ){
					$aLatLng = explode(',', $aData['latLng']);
					$distance = isset($aData['sWithin']) ? abs($aData['sWithin']) :  10;
					$unit = isset($aData['sUnit']) ? trim($aData['sUnit']) :  'km';
					$aListingInRadius = GeoPosition::searchLocationWithin(trim($aLatLng[0]), trim($aLatLng[1]), $distance, $unit);

					if ( empty($aListingInRadius) ){
						wp_send_json_error();
					}

					$aArgs['post__in'] = $aListingInRadius['IDs'];
				}else{
					if ( !$aData['atts']['isTax'] && (($aData['atts']['get_posts_from'] == 'listing_location') || ($aData['atts']['get_posts_from'] == 'listing_cat')) ){
						$aArgs['tax_query'][] = array(
							'taxonomy'  => $aData['atts']['get_posts_from'],
							'field'     => 'term_id',
							'terms'     =>  $aData['atts'][$aData['atts']['get_posts_from']]
						);
					}else{
						if ( $aData['atts']['get_posts_from'] == 'the_both_listing_location_and_listing_cat' ){
							if ( isset($aData['atts']['listing_location']) && !empty($aData['atts']['listing_location']) ){
								$aArgs['tax_query'][] = array(
									'taxonomy' => 'listing_location',
									'field'    => 'term_id',
									'terms'    => explode(',', $aData['atts']['listing_location'])
								);
							}

							if ( isset($aData['atts']['listing_cat']) && !empty($aData['atts']['listing_cat']) ){
								$aArgs['tax_query'][] = array(
									'taxonomy' => 'listing_cat',
									'field'    => 'term_id',
									'terms'    => explode(',', $aData['atts']['listing_cat'])
								);
							}

							if ( count($aArgs['tax_query']) ){
								$aArgs['tax_query']['relation'] = 'AND';
							}
						}else{
							$aLocationData = WilokePublic::parseLocationQuery($aData);

							if ( empty($aLocationData) ){
								wp_send_json_error();
							}
							$aArgs['tax_query'][] = $aLocationData;

							if ( !empty($aData['listing_cats']) ){
								$hasCat = true;
								$aArgs['tax_query'][] = array(
									'taxonomy'  => 'listing_cat',
									'field'     => 'term_id',
									'terms'     => is_array($aData['listing_cats']) ? array_map('absint', $aData['listing_cats']) : absint($aData['listing_cats'])
								);
							}
						}
					}
				}
			}else{
				$aArgs['tax_query'][] = array(
					'taxonomy'  => $aData['filter_by'],
					'field'     => 'term_id',
					'terms'     =>  absint($aData['term'])
				);
			}

			if ( !empty($aData['listing_tags']) ){
				$aArgs['tax_query'][] = array(
					'taxonomy'  => 'listing_tag',
					'field'     => 'term_id',
					'terms'     => is_array($aData['listing_tags']) ? array_map('absint', $aData['listing_tags']) : absint($aData['listing_tags'])
				);
			}

			if ( !empty($aData['listing_cats']) ){
				$aArgs['tax_query'][] = array(
					'taxonomy'  => 'listing_cat',
					'field'     => 'term_id',
					'terms'     => is_array($aData['listing_cats']) ? array_map('absint', $aData['listing_cats']) : absint($aData['listing_cats'])
				);
			}

			if ( isset($aArgs['tax_query']) && !empty($aArgs['tax_query']) && (count($aArgs['tax_query']) > 1) ){
				$aArgs['tax_query']['relation'] = 'AND';
			}

			if ( !empty($aData['s']) && !$hasCat ){
				$aArgs['s'] = $aData['s'];
			}

			if ( isset($aArgs['paged']) && !empty($aArgs['paged']) ){
				unset($aArgs['post__not_in']);
			}

			if ( isset($aListingInRadius) && !empty($aListingInRadius) ){
				$aArgs['orderby'] = 'post__in';
			}else{
				$aArgs['orderby'] = 'menu_order post_date';
			}
			
			$query = new WP_Query($aArgs);

			if ( $query->have_posts() ){
				global $wiloke;
				$mapPage = isset($aData['atts']['map_page']) && !empty($aData['atts']['map_page']) ? get_permalink($aData['atts']['map_page']) : get_permalink($wiloke->aThemeOptions['header_search_map_page']);

				ob_start();
				while ( $query->have_posts() ){
					$query->the_post();
					self::listingQuery($aData['atts'], $mapPage, true);
				}
				$content = ob_get_clean();
				wp_send_json_success(array('content'=>$content, 'type'=>'db', 'total'=>$query->found_posts));
			}else{
				wp_send_json_error();
			}
		}

	}

	public function getTimezoneByGeoCode($geocode){
		global $wiloke;
		$aTimeZone = wp_remote_get(esc_url_raw('https://maps.googleapis.com/maps/api/timezone/json?location='.$geocode.'&timestamp='.time().'&key='.$wiloke->aThemeOptions['general_map_api']));

		if ( is_wp_error($aTimeZone)  ){
			return '';
		}else{
			$oTimeZone = json_decode($aTimeZone['body']);
			return $oTimeZone->timeZoneId;
		}
	}

	public function complexSearching($aData){
		global $wpdb, $wiloke;
		$termRelationShipsTbl   = $wpdb->prefix . 'term_relationships';
		$businessHoursTbl       = $wpdb->prefix . WilokeBusinessHoursTbl::$tblName;
		$priceSegmentTbl        = $wpdb->prefix . WilokePriceSegmentTbl::$tblName;
		$postMetaTbl            = $wpdb->prefix . 'postmeta';
		$postsTbl               = $wpdb->prefix . 'posts';

		$select = "SELECT DISTINCT $postsTbl.ID, $postsTbl.menu_order";
		$join = "";
		$timezone = '';
		$concat = " WHERE ";
		$conditional = "";
		$joinedTerm = false;
		$ignoreParseLocation = false;

		if ( isset($aData['latLng']) && !empty($aData['latLng']) ){
			$aLatLng = explode(',', $aData['latLng']);

			$distance = isset($aData['sWithin']) ? abs($aData['sWithin']) :  10;
			$unit = isset($aData['sUnit']) ? trim($aData['sUnit']) :  'km';

			$aListingInRadius = GeoPosition::searchLocationWithin(trim($aLatLng[0]), trim($aLatLng[1]), $distance, $unit);
			if ( empty($aListingInRadius) ){
				wp_send_json_error();
			}
			
			$ignoreParseLocation = true;
			$aListingIDs = $aListingInRadius['IDs'];
		}else{
			if ( !empty($aData['listing_locations']) ){
				$aParseLocation = WilokePublic::parseLocationQuery($aData, true);
				if ( empty($aParseLocation) ){
					wp_send_json_error();
				}
				$aData['listing_locations'] = $aParseLocation['terms'];
			}
		}

		if ( !empty($aData['listing_tags']) ){
			$aTagIDs = array_map('absint', $aData['listing_tags']);
			$postIDsInTags = "SELECT $termRelationShipsTbl.object_id FROM $termRelationShipsTbl WHERE $termRelationShipsTbl.term_taxonomy_id IN (".implode(',', $aTagIDs).")";
			$join .= " LEFT JOIN $termRelationShipsTbl ON ($termRelationShipsTbl.object_ID = $postsTbl.ID)";
			$conditional .= $concat . "$postsTbl.ID IN(".$postIDsInTags. ")";
			$concat = " AND ";
			$joinedTerm = true;
		}

		if ( !empty($aData['listing_cats']) ){
			$aCatIDs = explode(',', $aData['listing_cats']);
			$aCatChildren = get_term_children($aData['listing_cats'], 'listing_cat');
			if ( !empty($aCatChildren) && !is_wp_error($aCatChildren) ){
				$aCatIDs = array_merge($aCatIDs, $aCatChildren);
			}

			$aCatIDs = array_map('absint', $aCatIDs);
			if ( !$joinedTerm ){
				$join .= " LEFT JOIN $termRelationShipsTbl ON ($termRelationShipsTbl.object_ID = $postsTbl.ID)";
				$joinedTerm = true;
			}
			$postIDsInCats = "SELECT $termRelationShipsTbl.object_id FROM $termRelationShipsTbl WHERE $termRelationShipsTbl.term_taxonomy_id IN (".implode(',', $aCatIDs).")";
			$conditional .= $concat . "$postsTbl.ID IN(".$postIDsInCats. ")";
			$concat = " AND ";
		}

		if ( !empty($aData['listing_locations']) ){
			if ( !$ignoreParseLocation ){
				if ( is_array($aData['listing_locations']) ){
					$locationIDs =  implode(',', $aData['listing_locations']);
				}else{
					$listingLocationID =  absint($aData['listing_locations']);
					$aLocationChildren = get_term_children($listingLocationID, 'listing_location');

					if ( !empty($aLocationChildren) && !is_wp_error($aLocationChildren) ){
						$locationIDs = array_merge($aLocationChildren, array($listingLocationID));
						$locationIDs = implode(',', $locationIDs);
					}else{
						$locationIDs = $listingLocationID;
					}
				}

				if ( !$joinedTerm ){
					$join .= " LEFT JOIN $termRelationShipsTbl ON ($termRelationShipsTbl.object_ID = $postsTbl.ID)";
				}

				$conditional .= $concat . "$termRelationShipsTbl.term_taxonomy_id IN (".$locationIDs. ")";
				$concat = " AND ";
			}

			if ( $aData['is_open_now'] !== 'false' ){
				if ( !empty($aData['latLng']) ){
					$timezone = $this->getTimezoneByGeoCode($aData['latLng']);
				}else{
					$aLocationIDs = explode(',', $locationIDs);
					foreach ($aLocationIDs as $locationID) {
						$aLocationSettings = Wiloke::getTermOption($locationID);
						if ( !empty($aLocationSettings['timezone']) ){
							$timezone = $aLocationSettings['timezone'];
							break;
						}
					}
				}
				
				if ( empty($timezone) ){
					$now = date('H:i:s', current_time('timestamp', true));
					date_default_timezone_set('UTC');
					$today = date('N', time());

					$join .= " LEFT JOIN $businessHoursTbl ON ($businessHoursTbl.post_ID = $postsTbl.ID)";
					$conditional .= $concat . $wpdb->prepare("( ($businessHoursTbl.always_open = %s) OR ($businessHoursTbl.day_of_week = %d AND  ( ( %s >= $businessHoursTbl.open_time_utc AND $businessHoursTbl.close_time_utc > %s) || ( (%s >= $businessHoursTbl.open_time_utc || $businessHoursTbl.close_time_utc > %s) AND ($businessHoursTbl.close_time_utc < %s)) ) ) )",
							'yes', $today, $now, $now, '6:00:00', $now, $now, '6:00:00'
						);
				}else{
					date_default_timezone_set($timezone);
					$now = date('H:i:s', time());
					$today = date('N', time());

					$join .= " LEFT JOIN $businessHoursTbl ON ($businessHoursTbl.post_ID = $postsTbl.ID)";
					$conditional .= $concat . $wpdb->prepare("( ($businessHoursTbl.always_open = %s) OR ($businessHoursTbl.day_of_week = %d AND  ( ( %s >= $businessHoursTbl.open_time AND $businessHoursTbl.close_time > %s AND $businessHoursTbl.close_time > %s) || ( (%s >= $businessHoursTbl.open_time || $businessHoursTbl.close_time > %s) AND ($businessHoursTbl.close_time < %s)) ) ) )",
							'yes', $today, $now, $now, '6:00:00', $now, $now, '6:00:00'
						);
				}
				
				$concat = " AND ";
			}
		}

		if ( $aData['is_highest_rated'] !== 'false' ){
			$select .= ", $postMetaTbl.meta_value as average_rating";
			$join .= " LEFT JOIN $postMetaTbl ON ($postMetaTbl.post_id = $postsTbl.ID)";
			$conditional .= $concat . $wpdb->prepare(
				"$postMetaTbl.meta_key=%s AND $postMetaTbl.meta_value >= %d",
				FrontendRating::$averageRatingMetaKey, 2
			);
			$concat = " AND ";
		}

		if ( !empty($aData['price_segment']) && ($aData['price_segment'] !== 'all') ){
			$join .= " LEFT JOIN $priceSegmentTbl ON ($priceSegmentTbl.post_ID = $postsTbl.ID)";
			$conditional .= $concat . $wpdb->prepare(
					"$priceSegmentTbl.segment=%s",
					$aData['price_segment']
				);
			$concat = " AND ";
		}

		if ( !empty($aData['post__not_in']) ){
			if ( isset($aListingIDs) && !empty($aListingIDs) ){
				$aListingIDs = array_diff($aListingIDs, $aData['post__not_in']);
			}else{
				$aData['post__not_in'] = array_map('absint', $aData['post__not_in']);
				$conditional .=  $concat . "$postsTbl.ID NOT IN(".implode(',', $aData['post__not_in']) . ")";
				$concat = " AND ";
			}
		}

		$select .= " FROM $postsTbl";

		if ( $ignoreParseLocation ){
			$conditional .= $concat . $wpdb->prepare(
					"$postsTbl.post_type=%s AND $postsTbl.post_status=%s AND $postsTbl.ID IN (".implode(",", $aListingIDs) . ")",
					'listing', 'publish'
				);
		}else{
			$conditional .= $concat . $wpdb->prepare(
					"$postsTbl.post_type=%s AND $postsTbl.post_status=%s",
					'listing', 'publish'
				);
		}

		$postsPerPage = isset($aData['posts_per_page']) && absint($aData['posts_per_page']) <= 30 ? absint($aData['posts_per_page']) : 30;
		$conditional = trim($conditional) . " GROUP BY $postsTbl.ID";

		if ( !$ignoreParseLocation ){
			$sqlCountTotal = "SELECT COUNT(DISTINCT $postsTbl.ID) FROM $postsTbl" . $join . " " . $conditional;
			$total = $wpdb->query($sqlCountTotal);
		}else{
			$total = count($aListingIDs);
		}

		if ( $aData['is_highest_rated'] === 'true' ){
			$conditional .= " ORDER BY average_rating DESC";
		}else if ( $ignoreParseLocation ){
			$conditional .=  " ORDER BY FIELD($postsTbl.ID, ".implode(',', $aListingIDs).")";
		}else{
			$conditional .=  " ORDER BY $postsTbl.menu_order DESC, $postsTbl.post_date DESC";
		}
		
		if ( !$ignoreParseLocation ){
			$limit = " LIMIT " . $postsPerPage;
		}else{
			$limit = "";
		}
		$sqlMainQuery = $select . $join . " " . $conditional . $limit;

		$aResults = $wpdb->get_results($sqlMainQuery);
		
		if ( !empty($aResults) ){
			$mapPage = isset($aData['atts']['map_page']) && !empty($aData['atts']['map_page']) ? get_permalink($aData['atts']['map_page']) : get_permalink($wiloke->aThemeOptions['header_search_map_page']);
			ob_start();
			foreach ( $aResults as $oResult ){
				self::listingQuery($aData['atts'], $mapPage, true, $oResult->ID);
			}
			$content = ob_get_clean();
			wp_send_json_success(array('content'=>$content, 'type'=>'db', 'total'=>$total));
		}else{
			wp_send_json_error();
		}
	}
}