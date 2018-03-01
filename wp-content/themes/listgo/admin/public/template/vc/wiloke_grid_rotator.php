<?php
function wiloke_shortcode_grid_rotator($atts){
	$atts = shortcode_atts(
		array(
			'get_listings_by'           => 'latest_posts',
			'display_type'              => 'listing',
			'listing_cat'               => '',
			'header'                    => '',
			'description'               => '',
			'listing_location'          => '',
			'upload_images'             => '',
			'number_of_listings'        => 30,
			'max_step'                  => 3,
			'items_per_row_on_desktop'  => 6,
			'number_of_rows_on_desktop' => 3,
			'items_per_row_on_tablet'   => 5,
			'number_of_rows_on_tablet'  => 3,
			'items_per_row_on_mobile'   => 3,
			'number_of_rows_on_mobile'  => 3,
			'animate_type'              => 'random',
			'animation_speed'           => 500,
			'interval'                  => 3000,
			'css'                       => '',
			'extract_class'             => ''
		),
		$atts
	);

	$gridRotaror = array(
		'rows'      => $atts['number_of_rows_on_desktop'],
		'columns'   => $atts['items_per_row_on_desktop'],
		'w1400'     => array(
			'rows'      => $atts['number_of_rows_on_desktop'],
			'columns'   => $atts['items_per_row_on_desktop']
		),
		'w1024' => array(
			'rows'      => $atts['number_of_rows_on_desktop'],
			'columns'   => $atts['items_per_row_on_desktop']
		),
		'w768'  => array(
			'rows'      => $atts['number_of_rows_on_tablet'],
			'columns'   => $atts['items_per_row_on_tablet']
		),
		'w480'  => array(
			'rows'      => $atts['number_of_rows_on_mobile'],
			'columns'   => $atts['items_per_row_on_mobile']
		),
		'w320'  => array(
			'rows'      => $atts['number_of_rows_on_mobile'],
			'columns'   => $atts['items_per_row_on_mobile']
		),
		'w240'  => array(
			'rows'      => $atts['number_of_rows_on_mobile'],
			'columns'   => $atts['items_per_row_on_mobile']
		),
		'step' => 'random',
		'maxStep'   => $atts['max_step'],
		'animType'  => $atts['animate_type'],
		'animSpeed' => $atts['animation_speed'],
		'interval'  => $atts['interval'],
		'preventClick' => $atts['get_listings_by'] === 'upload_images' ? true : false
	);

	$wrapperClass = $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');
	ob_start();
	?>
    <div class="wil-gridratio-wrap">
        <?php if ( !empty($atts['header']) || !empty($atts['description']) ) : ?>
        <div class="wil-gridratio__hero">
            <div class="tb">
                <div class="tb__cell">
                    <div class="wil-gridratio__hero-content">
                        <h2 class="wil-gridratio__hero-title"><?php echo esc_html($atts['header']); ?></h2>
                        <p class="wil-gridratio__hero-description"><?php Wiloke::wiloke_kses_simple_html($atts['description']); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

	    <div class="<?php echo esc_attr($wrapperClass); ?>">
        <div class="wil-gridratio ri-grid" data-configuration="<?php echo esc_attr(json_encode($gridRotaror)); ?>">
            <ul class="wil-gridratio__list">
			<?php
			if ( $atts['get_listings_by'] === 'latest_posts' ){
				if ( Wiloke::$wilokePredis && Wiloke::$wilokePredis->exists(Wiloke::$prefix."listing_ids") ){
					$aListings   = Wiloke::$wilokePredis->sscan(Wiloke::$prefix.'listing_ids', 0, array('count'=>$atts['number_of_listings']));
					if ( isset($aListings[1]) && !empty($aListings[1]) ){
						foreach ( $aListings[1] as $postID ){
							wiloke_shortcode_grid_rotator_render_item($postID);
						}
					}
				}else{
					$query = new WP_Query(
						array(
							'post_type'      => 'listing',
							'posts_per_page' => $atts['number_of_listings'],
							'post_status'    => 'publish'
						)
					);

					if ( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$query->the_post();
							wiloke_shortcode_grid_rotator_render_item($query->post->ID);
						}
					}
					wp_reset_postdata();
				}
			}else if ( $atts['get_listings_by'] === 'upload_images' ){
				$aImgIDs = explode(',',$atts['upload_images']);
				foreach ( $aImgIDs as $attachID ){
					wiloke_shortcode_grid_rotator_render_item($attachID, true);
				}
			}else{
			    if ( isset($atts['display_type']) && $atts['display_type'] == 'terms' ){
				    if ( $atts[$atts['get_listings_by']] ){
					    $aTerms = explode(',', $atts[$atts['get_listings_by']]);
					    foreach ( $aTerms as $termID ){
					        $oTerm = get_term($termID);
						    if ( !empty($oTerm) && !is_wp_error($oTerm) ){
							    $aTermOptions = Wiloke::getTermOption($termID);
							    wiloke_shortcode_grid_rotator_render_term($aTermOptions, $oTerm);
						    }
                        }
                    }else{
					    $oTerms = get_terms( array(
						    'taxonomy'   => $atts['get_listings_by'],
						    'hide_empty' => true,
					    ) );
					    if ( !empty($oTerms) && !is_wp_error($oTerms) ){
					        foreach ($oTerms as $oTerm){
						        $aTermOptions = Wiloke::getTermOption($oTerm->term_id);
						        wiloke_shortcode_grid_rotator_render_term($aTermOptions, $oTerm);
                            }
                        }
                    }

                }else{
				    $query = new WP_Query(
					    array(
						    'post_type'      => 'listing',
						    'tax_query'      => array(
							    array(
								    'taxonomy' => $atts['get_listings_by'],
								    'term_id'  => 'term_id',
								    'terms'    => explode(',', $atts[$atts['get_listings_by']])
							    )
						    ),
						    'posts_per_page' => $atts['number_of_listings'],
						    'post_status'    => 'publish'
					    )
				    );

				    if ( $query->have_posts() ) {
					    while ( $query->have_posts() ) {
						    $query->the_post();
						    wiloke_shortcode_grid_rotator_render_item($query->post->ID);
					    }
				    }
				    wp_reset_postdata();
                }
			}
			?>
			</ul>
        </div>
	</div>
    </div>
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

function wiloke_shortcode_grid_rotator_render_term($aTermOptions, $oTerm){
	if ( !isset($aTermOptions['featured_image']) || empty($aTermOptions['featured_image']) ){
		return false;
	}

	$src = wp_get_attachment_image_url($aTermOptions['featured_image'], 'wiloke_listgo_370x370');

	if ( empty($src) ){
		return false;
	}

	?>
    <li>
        <a href="<?php echo esc_url(get_term_link($oTerm->term_id)); ?>">
			<?php

			Wiloke::lazyLoad($src, 'wiloke-grid-rotator-item', array('alt'=>$oTerm->name));
			?>
            <div class="wil-gridratio__caption">
                <div class="wil-gridratio__inner">
                    <h4><?php echo esc_html($oTerm->name); ?></h4>
                </div>
            </div>
        </a>
    </li>
	<?php
}

function wiloke_shortcode_grid_rotator_render_item($id, $isAttachID=false){
	if ( !has_post_thumbnail($id) && !$isAttachID ){
		return false;
	}
	$src = $isAttachID ? wp_get_attachment_image_url($id, 'wiloke_listgo_370x370') : get_the_post_thumbnail_url($id, 'wiloke_listgo_370x370');

	if ( empty($src) ){
		return false;
	}

	$title = get_the_title($id);
	?>
	<li>
		<a href="<?php echo esc_url(get_permalink($id)); ?>">
			<?php

            Wiloke::lazyLoad($src, 'wiloke-grid-rotator-item', array('alt'=>$title));
			?>
			<div class="wil-gridratio__caption">
				<div class="wil-gridratio__inner">
					<h4><?php echo esc_html($title); ?></h4>
				</div>
			</div>
		</a>
	</li>
	<?php
}