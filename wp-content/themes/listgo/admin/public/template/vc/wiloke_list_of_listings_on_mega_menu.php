<?php
function wiloke_shortcode_list_of_listings_on_mega_menu($atts){
	$atts = shortcode_atts(
		array(
			'get_posts_by'          => 'latest_listings',
			'listing_cat'           => '',
			'listing_location'      => '',
			'include'               => '',
			'image_size'			=> 'medium',
			'number_of_listings'    => 4,
			'display'        		=> 'grid',
            'xl_per_row'        	=> 5,
            'lg_per_row'        	=> 4,
            'md_per_row'        	=> 3,
            'sm_per_row'        	=> 2,
            'xs_per_row'        	=> 1,
            'space'             	=> 20,
            'nav'               	=> '',
            'dots'              	=> '',
			'css'                   => '',
			'extract_class'         => ''
		),
		$atts
	);

	$atts['image_size'] = strpos($atts['image_size'], ',') === false ? $atts['image_size'] : explode(',', $atts['image_size']);

	$aDataAtts = array();
    $class     = 'wiloke-menu-' . $atts['display'];

    if ( $atts['display'] === 'slider' ) {        
        $class     .= ' owl-carousel';
	    $aDataAtts['data-col-xl'] = $atts['xl_per_row'];
	    $aDataAtts['data-col-lg'] = $atts['lg_per_row'];
	    $aDataAtts['data-col-md'] = $atts['md_per_row'];
	    $aDataAtts['data-col-sm'] = $atts['sm_per_row'];
	    $aDataAtts['data-col-xs'] = $atts['xs_per_row'];
	    $aDataAtts['data-space']  = $atts['space'];
	    $aDataAtts['data-nav']    = filter_var($atts['nav'], FILTER_VALIDATE_BOOLEAN);
	    $aDataAtts['data-dots']   = filter_var($atts['dots'], FILTER_VALIDATE_BOOLEAN);
    } else if($atts['display'] === 'grid') {
        $class .= ' wiloke-menu-col-xl-' . $atts['xl_per_row'];
        $class .= ' wiloke-menu-col-lg-' . $atts['lg_per_row'];
        $class .= ' wiloke-menu-col-md-' . $atts['md_per_row'];
        $class .= ' wiloke-menu-col-sm-' . $atts['sm_per_row'];
        $class .= ' wiloke-menu-col-xs-' . $atts['xs_per_row'];
        $class .= ' wiloke-menu-space-' . $atts['space'];
    }

    if (!empty($atts['extract_class'])) {
        $class .= ' ' . $atts['extract_class'];
    }

    $class .= vc_shortcode_custom_css_class($atts['css']);

	ob_start(); ?>

	<div class="wiloke-menu-posts <?php echo esc_attr($class); ?>" <?php WilokePublic::render_attributes($aDataAtts); ?>>
		<?php
		if ( ( ($atts['get_posts_by'] === 'latest_listings') ||  ($atts['get_posts_by'] === 'custom')) && Wiloke::$wilokePredis && Wiloke::$wilokePredis->exists(Wiloke::$prefix.'listing_ids') ){
		    if ( $atts['get_posts_by'] === 'latest_listings'  ) {
			    $aPostIDs = Wiloke::$wilokePredis->sscan(Wiloke::$prefix.'listing_ids', 0, array('COUNT'=>absint($atts['number_of_listings'])));
			    $aPostIDs = isset($aPostIDs[1]) ? $aPostIDs[1] : '';
			    $aPostIDs = array_reverse($aPostIDs);
			    $aPostIDs = array_slice($aPostIDs, 0, $atts['number_of_listings']);
            }else {
		        $aPostIDs = explode(',', $atts['include']);
            }

            if ( empty($aPostIDs) ) {
		        return '';
            }

			foreach ( $aPostIDs as $postID ) {
				$aPost = Wiloke::$wilokePredis->hGet(Wiloke::$prefix."listing|".$postID, 'post_data');
				$aPost = json_decode($aPost, true);
				$aPost['featured_image'] = get_the_post_thumbnail_url($postID, $atts['image_size']);
				wiloke_shortcode_list_of_listings_on_mega_menu_get_item($aPost, $atts);
			}

		} elseif ($atts['get_posts_by']==='top_rated'){
			$aPosts = WilokeReview::getTopRatedListings($atts['number_of_listings']);
			if ( !empty($aPosts) ){
                foreach ( $aPosts as $aPost ){
	                $aPost['link'] = get_permalink($aPost['ID']);
	                $aPost['featured_image'] = get_the_post_thumbnail_url($aPost['ID'], $atts['image_size']);
	                wiloke_shortcode_list_of_listings_on_mega_menu_get_item($aPost, $atts);
                }
            }
        }else{
            $aArgs = array(
                'post_type'     => 'listing',
                'post_status'   => 'publish'
            );

            if ( $atts['get_posts_by'] === 'custom' ) {
                $aArgs['post__in'] = explode(',', $atts['include']);
            } else {
	            $aArgs['posts_per_page'] = $atts['number_of_listings'];
	            if (  $atts['get_posts_by'] !== 'latest_listings' ) {

		            $aArgs['tax_query'] = array(
                        array(
                            'taxonomy' => $atts['get_posts_by'],
                            'field'    => 'term_id',
                            'terms'    => explode(',', $atts[$atts['get_posts_by']])
                        )
                    );
                }
                $query = new WP_Query($aArgs);

	            if ( $query->have_posts() ) {
	                while ($query->have_posts()) {
	                    $query->the_post();
	                    $aPost['featured_image'] = get_the_post_thumbnail_url($query->post->ID, 'wiloke_listgo_370x370');
	                    $aPost['title'] = $query->post->post_title;
	                    $aPost['link'] = get_permalink($query->post->ID);
	                    $aPost['post_date'] = $query->post->post_date;
	                    wiloke_shortcode_list_of_listings_on_mega_menu_get_item($aPost, $atts);
                    }
                }
                wp_reset_postdata();
            }
		}
		?>
	</div>
	<?php
	return ob_get_clean();
}

function wiloke_shortcode_list_of_listings_on_mega_menu_get_item($aPost, $atts){ ?>
	<div class="wiloke-menu-post">
		<?php if ( $aPost['featured_image'] && ($atts['display'] !== 'simple') ): ?>
			<div class="wiloke-menu-post__thumbnail">
				<a href="<?php echo esc_url($aPost['link']) ?>">
					<img class="<?php echo $atts['display'] == 'slider' ? 'owl-lazy' : 'lazy' ?>" data-src="<?php echo esc_url($aPost['featured_image']); ?>" alt="<?php echo esc_attr($aPost['title']) ?>">
				</a>
			</div>
		<?php endif ?>
		<div class="wiloke-menu-post__body">
			<h2 class="wiloke-menu-post__title"><a href="<?php echo esc_url($aPost['link']) ?>"><?php echo esc_html($aPost['title']) ?></a></h2>
		</div>
	</div>
	<?php
}