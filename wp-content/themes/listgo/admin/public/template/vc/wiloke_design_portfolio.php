<?php
/**
 * Render Portfolio Layout here
 * @since 1.0
 */
function wiloke_shortcode_design_portfolio($atts)
{
    global $wiloke;

	$atts = shortcode_atts(
		array(
			'get_posts_from'                        => 'listing_cat',
			'listing_cat'                           => '',
			'listing_location'                      => '',
			'include'                               => '',
			'show_terms'                            => 'both',
			'item_type'                             => 'listing',
			'subfix_after_number_of_articles'       => 'Landmarks',
			'wiloke_design_portfolio_choose_layout' => '',
			'wiloke_portfolio_layout'               => '',
			'order_by'                              => 'post_date',
			'sort_in'                               => 'DESC',
			'extract_class'                         => '',
			'css'                                   => ''
		),
		$atts
	);
	$wrapperClass = 'listings listings--box m-bottom-0 ' . ' ' . $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');
	$func = 'base64_' . 'decode';
	$atts['wiloke_portfolio_layout'] = $func($atts['wiloke_portfolio_layout']);
	$aPortfolioSettings = json_decode($atts['wiloke_portfolio_layout'], true);
	$aLayouts = explode(',', $aPortfolioSettings['creative']['items_size']);
	$total = count($aLayouts);
	$wrapperClass = trim($wrapperClass);
	ob_start();
    ?>
    <div class="<?php echo esc_attr($wrapperClass); ?>">
        <div <?php WilokePublic::render_attributes(array(
                     'class'                => 'wil_masonry-wrapper wil_masonry-grid',
                     'data-lg-horizontal'   => $aPortfolioSettings['devices_settings']['large']['horizontal'],
                     'data-lg-vertical'     => $aPortfolioSettings['devices_settings']['large']['vertical'],
                     'data-col-lg'          => $aPortfolioSettings['devices_settings']['large']['items_per_row'],
                     'data-col-md'          => $aPortfolioSettings['devices_settings']['medium']['items_per_row'],
                     'data-md-horizontal'   => $aPortfolioSettings['devices_settings']['medium']['horizontal'],
                     'data-md-vertical'     => $aPortfolioSettings['devices_settings']['medium']['vertical'],
                     'data-col-sm'          => $aPortfolioSettings['devices_settings']['small']['items_per_row'],
                     'data-sm-horizontal'   => $aPortfolioSettings['devices_settings']['small']['horizontal'],
                     'data-sm-vertical'     => $aPortfolioSettings['devices_settings']['small']['vertical'],
                     'data-col-xs'          => $aPortfolioSettings['devices_settings']['extra_small']['items_per_row'],
                     'data-xs-horizontal'   => $aPortfolioSettings['devices_settings']['extra_small']['horizontal'],
                     'data-xs-vertical'     => $aPortfolioSettings['devices_settings']['extra_small']['vertical']
             )); ?>
             >
            <div class="wil_masonry">
                <div class="grid-sizer"></div>
                <?php
                if($aPortfolioSettings['layout'] === 'masonry'){
	                $portfolioMode = 'masonry';
	                $size = 'medium';
                }elseif($aPortfolioSettings['layout'] !== 'creative'){
	                $portfolioMode = 'grid';
	                $size = 'medium';
                }else{
	                $portfolioMode = 'creative';
                }

                if ( $atts['item_type'] === 'listing' ) {
	                if ( $atts['get_posts_from'] === 'custom' ) {
	                    if ( !empty($atts['include'])  ){
		                    $args = array(
			                    'post_status' => 'publish',
			                    'post_type'   => 'listing',
			                    'post__in'    => array_map( 'absint', explode( ',', $atts['include'] ) ),
			                    'ignore_sticky_posts'=>1
		                    );
                        }else{
		                    $args = array(
			                    'post_status' => 'publish',
			                    'post_type'   => 'listing',
			                    'posts_per_page' => absint( $aPortfolioSettings['general_settings']['number_of_posts'] ),
			                    'ignore_sticky_posts'=>1
		                    );
                        }
	                }else{
	                    if($atts['get_posts_from'] === 'featured_listings'){
                            $args = array(
                                'post_status'    => 'publish',
                                'post_type'      => 'listing',
                                'posts_per_page' => absint( $aPortfolioSettings['general_settings']['number_of_posts'] ),
                                'meta_key'        => 'wiloke_listgo_toggle_highlight',
                                'meta_value'      => 1,
                                'ignore_sticky_posts'=>1
                            );
                            if ( $atts['order_by'] !== 'highlight' ){
                                $args['orderby'] = $atts['order_by'];
                            }
                        }else {
		                    $args = array(
			                    'post_type'      => 'listing',
			                    'posts_per_page' => absint( $aPortfolioSettings['general_settings']['number_of_posts'] ),
			                    'post_status'    => 'publish'
		                    );

		                    if ( ( $atts['get_posts_from'] === 'listing_cat' ) && ! empty( $atts['listing_cat'] ) ) {
			                    $args['tax_query'] = array(
				                    array(
					                    'taxonomy' => $atts['get_posts_from'],
					                    'field'    => 'term_id',
					                    'terms'    => array_map( 'absint', explode( ',', $atts['listing_cat'] ) )
				                    )
			                    );
		                    } elseif ( ! empty( $atts['listing_locations'] ) ) {
			                    $args['tax_query'] = array(
				                    array(
					                    'taxonomy' => $atts['get_posts_from'],
					                    'field'    => 'term_id',
					                    'terms'    => array_map( 'absint', explode( ',', $atts['listing_location'] ) )
				                    )
			                    );
		                    }

		                    if ( $atts['order_by'] === 'highlight' ){
			                    if ( Wiloke::$wilokePredis ){
				                    $aRedisData = Wiloke::$wilokePredis->hGetAll('wiloke_listgo_toggle_highlight');
				                    if ( !empty($aRedisData) ){
					                    $aPostIDS = array_keys($aRedisData);
					                    $sArgs = $args;
					                    $sArgs['post__in'] = $aPostIDS;
					                    $metaQuery = new WP_Query(array(
						                    'post__in'  =>  $aPostIDS,
						                    'post_type' => 'listing'
					                    ));
				                    }
			                    }else{
				                    $sArgs = $args;
				                    $sArgs['meta_query'] = array(array(
					                    'key'       => 'wiloke_listgo_toggle_highlight',
					                    'value'     => 1,
					                    'compare'   => '='
				                    ));
				                    $metaQuery = new WP_Query($sArgs);
				                    $aPostIDS = [];
				                    if ( $metaQuery->have_posts() ){
					                    while ($metaQuery->have_posts()){
						                    $metaQuery->the_post();
						                    $aPostIDS[] = $metaQuery->post->ID;
					                    }
				                    }
			                    }
		                    }else{
			                    $args['orderby'] = $atts['order_by'];
		                    }
	                    }
	                }

                    $args['order'] = $atts['sort_in'];

	                if ( isset($aPostIDS) && !empty($aPostIDS) ){
		                $args['post__not_in'] = $aPostIDS;
		                $query = new WP_Query($args);
		                if ( isset($metaQuery) ){
			                $query->posts = array_merge($metaQuery->posts, $query->posts);
                        }
                    }else{
		                $query = new WP_Query($args);
                    }

	                if ( isset($wiloke->aThemeOptions['listing_header_image']['id']) && !empty($wiloke->aThemeOptions['listing_header_image']['id']) ){
		                $aDefaultFeatureImg['main']['src']      = wp_get_attachment_image_url($wiloke->aThemeOptions['listing_header_image']['id'], 'large');
		                $aDefaultFeatureImg['main']['width']    = 1000;
		                $aDefaultFeatureImg['main']['height']   = 500;
		                $aDefaultFeatureImg['srcset']           = '';
		                $aDefaultFeatureImg['sizes']            = '';
	                }else{
		                $aDefaultFeatureImg['main']['src']      = get_template_directory_uri() . '/img/featured-image.jpg';
		                $aDefaultFeatureImg['main']['width']    = 1000;
		                $aDefaultFeatureImg['main']['height']   = 500;
		                $aDefaultFeatureImg['srcset']           = '';
		                $aDefaultFeatureImg['sizes']            = '';
	                }

	                if ( $query->have_posts() ) {
		                $i = 0;
		                global $post;
		                while ( $query->have_posts() ) {
			                $query->the_post();
			                if ( $total === $i ) {
				                $i = 0;
			                }
			                include get_template_directory() . '/admin/public/template/vc/portfolio/item.php';
			                $i ++;
		                }
	                }
                }else {
	                $aIncludes = array();
	                if ( $atts['get_posts_from'] === 'listing_cat' ) {
		                if ( !empty($atts['listing_cat']) ) {
			                $aIncludes = explode(',', $atts['listing_cat']);
		                }else{
                            $aIncludes = wiloke_shortcode_design_portfolio_get_terms($atts, $aPortfolioSettings);
                        }
	                } elseif ( ! empty( $atts['listing_location'] ) ) {
		                if ( !empty($atts['listing_location']) ) {
			                $aIncludes = explode(',', $atts['listing_location']);
		                }else{
			                $aIncludes = wiloke_shortcode_design_portfolio_get_terms($atts, $aPortfolioSettings);
		                }
	                }

	                $aTerms = Wiloke::getTermCaching($atts['get_posts_from'], $aIncludes);
	                $atts['subfix_after_number_of_articles'] = explode('|', $atts['subfix_after_number_of_articles']);

	                if ( count($atts['subfix_after_number_of_articles']) === 2 ) {
	                    $singularPrefix = $atts['subfix_after_number_of_articles'][0];
	                    $pluralPrefix = $atts['subfix_after_number_of_articles'][1];
                    }else{
		                $singularPrefix = $pluralPrefix = $atts['subfix_after_number_of_articles'][0];
                    }

	                if ( !empty($aTerms) && !is_wp_error($aTerms) ){
                        $i = 0;
                        foreach ( $aTerms as $oTerm ){
	                        if ( $total === $i ) {
		                        $i = 0;
	                        }
	                        include get_template_directory() . '/admin/public/template/vc/portfolio/term.php';
	                        $i ++;
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <?php
    wp_reset_postdata();

	return ob_get_clean();
}

function wiloke_shortcode_design_portfolio_get_terms($atts, $aPortfolioSettings){
	$aTerms = get_terms( array(
		'taxonomy'   => $atts['get_posts_from'],
		'hide_empty' => true,
		'number'     => isset($aPortfolioSettings['general_settings']['number_of_posts']) ? $aPortfolioSettings['general_settings']['number_of_posts'] : 4
	));

	$aTermIDs = array();
	if ( !is_wp_error($aTerms) && !empty($aTerms) ){
	    foreach ( $aTerms as $oTerm ){
		    $aTermIDs[] = $oTerm->term_id;
        }
    }

    return $aTermIDs;
}