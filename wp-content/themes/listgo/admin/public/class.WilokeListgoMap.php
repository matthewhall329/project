<?php
class WilokeListgoMap{
	public function __construct() {
		add_action('wp_ajax_wiloke_loadmore_map', array($this, 'loadmoreMap'));
		add_action('wp_ajax_nopriv_wiloke_loadmore_map', array($this, 'loadmoreMap'));
	}

	public static function hasLocation($postID){
		$aMapSettings = Wiloke::getPostMetaCaching($postID, 'listing_settings');
		if ( empty($aMapSettings) || empty($aMapSettings['map']) || empty($aMapSettings['map']['latlong']) ){
			return false;
		}
		return true;
	}

	public static function getMap($aAtts, $isFocusSearch=false){
		$aListings = array();
		if ( $isFocusSearch && !empty($aAtts['s']) ){
			$aArgs = array(
				'posts_per_page'    => 1,
				'post_type'         => 'listing',
				's'                 => $aAtts['s'],
				'post_status'       => 'publish'
			);
			$query = new WP_Query($aArgs);
			if ( $query->have_posts() ){
				while ($query->have_posts()){
					$query->the_post();
					if ( self::hasLocation($query->post->ID) ){
						$aListings[] = WilokePublic::createListingInfo($query->post);
					}
				}
				wp_reset_postdata();
			}

			return $aListings;
		}

		if ( Wiloke::$wilokePredis && Wiloke::$wilokePredis->exists(Wiloke::$prefix."listing_ids") && ($aAtts['source_map']==='all') ){
			$cursor = isset($aAtts['cursor']) ? absint($aAtts['cursor']) : 0;
			$aListingIDs = Wiloke::$wilokePredis->sscan(Wiloke::$prefix."listing_ids", $cursor, array('COUNT'=>100000));
			if ( isset($aListingIDs[1]) ){
				foreach ( $aListingIDs[1] as $listingID ){
					if ( isset($aAtts['post__not_in']) && !empty($aAtts['post__not_in']) && in_array(absint($listingID), $aAtts['post__not_in']) ){
						continue;
					}
					$post = json_decode(Wiloke::$wilokePredis->hGet(Wiloke::$prefix."listing|$listingID", 'post_data'), true);
					$post['post_type'] = 'listing';
					$post = (object)$post;

					if ( self::hasLocation($post->ID) ){
						$aListings[] = WilokePublic::createListingInfo($post);
					}
				}
			}
		}else{
			$aArgs = array(
				'posts_per_page'    => 10,
				'post_type'         => 'listing',
				'orderby'           => 'post_date',
				'post_status'       => 'publish',
				'orderby'			=> $aAtts['order_by'],
				'order'				=> $aAtts['order']
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

			$aTaxQuery = array();
			if ( !empty($aAtts['s_current_cat']) || !empty($aAtts['s_current_location']) ){
				if ( !empty($aAtts['s_current_cat']) ){
					$aTaxQuery[] = array(
						'taxonomy' => 'listing_cat',
						'field'    => 'term_id',
						'terms'    => $aAtts['s_current_cat']
					);
				}

				if ( !empty($aAtts['s_current_tag']) ){
					$aTaxQuery[] = array(
						'taxonomy' => 'listing_tag',
						'field'    => 'term_id',
						'terms'    => $aAtts['s_current_tag']
					);
				}

				if ( isset($aAtts['s_current_location']) && !empty($aAtts['s_current_location']) ){
					$aAtts['listing_locations'] = $aAtts['s_current_location'];
					$aTaxQuery[] = WilokePublic::parseLocationQuery($aAtts);
				}

				if ( !empty($aTaxQuery) ){
					$aTaxQuery['relation'] = 'AND';
					$aArgs['tax_query'] = $aTaxQuery;
				}
			}else{
				if ( $aAtts['source_map'] === 'listing_cat' ){
					$aArgs['tax_query'] = array(
						array(
							'taxonomy' => 'listing_cat',
							'field'    => 'term_id',
							'terms'    => $aAtts['listing_cat_ids']
						)
					);
				}elseif( $aAtts['listing_location'] && ($aAtts['listing_location'] !== 'all') ){
					$aArgs['tax_query'] = array(
						array(
							'taxonomy' => 'listing_location',
							'field'    => 'term_id',
							'terms'    => $aAtts['listing_location_ids']
						)
					);
				}
			}

			if ( isset($aAtts['post__not_in']) ){
				$aArgs['post__not_in'] = $aAtts['post__not_in'];
			}

			$query = new WP_Query($aArgs);
			$aListings = array();

			if ( $query->have_posts() ){
				while ($query->have_posts()){
					$query->the_post();
					if ( self::hasLocation($query->post->ID) ){
						$aListings[] = WilokePublic::createListingInfo($query->post);
					}
				}
				wp_reset_postdata();
			}
		}
		return $aListings;
	}

	public function loadmoreMap(){
		if ( check_ajax_referer('wiloke-nonce', 'security', false) ){
			if ( !isset($_POST['post__not_in']) || empty($_POST['post__not_in']) || !isset($_POST['atts']) || empty($_POST['atts']) ){
				wp_send_json_error();
			}

			$aPostNotIn = array_map('absint', $_POST['post__not_in']);
			$aAtts = $_POST['atts'];
			$aAtts['post__not_in'] = $aPostNotIn;

			$aNewListing = self::getMap($aAtts);
			if ( empty($aNewListing) ){
				wp_send_json_error();
			}
			wp_send_json_success($aNewListing);
		}
	}
}