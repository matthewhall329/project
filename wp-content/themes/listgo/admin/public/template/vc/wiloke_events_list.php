<?php
use WilokeListGoFunctionality\Frontend\FrontendListingManagement as WilokeFrontendListingManagement;
use WilokeListGoFunctionality\Frontend\FrontendEvents as WilokeFrontendEvents;

function wiloke_shortcode_events_list($atts){
	$atts = shortcode_atts(
		array(
			'layout'              => 'listing--list',
			'include'             => '',
			'event_description'   => esc_html__('Event Description', 'listgo'),
			'events_per_page'     => 10,
			'limit_character'     => 100,
			'image_size'          => 'large',
			'display_style'       => 'pagination',
			'btn_name'            => esc_html__('Load More', 'listgo'),
			'css'                 => '',
			'extract_class'       => ''
		),
		$atts
	);

	if ( strpos($atts['image_size'], ',') ){
		$atts['image_size'] = array_map('trim', explode(',', $atts['image_size']));
	}

	$wrapperClass = 'wiloke-listgo-event listing-event ' . ' ' . $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');

	if ( empty($atts['events_per_page']) ){
		$atts['events_per_page'] = get_option('posts_per_page');
	}

	$aArgs = array(
		'post_type'   => 'event',
		'post_status' => 'publish',
		'paged'       => get_query_var('paged', 1),
		'orderby'     => 'menu_order date',
		'meta_query'  => array(
			'key'     => 'toggle_show_events_on_event_widget',
			'value'   => '=',
			'compare' => 'enable'
		),
		'posts_per_page'  => $atts['events_per_page']
	);

	$query = new WP_Query($aArgs);

	ob_start();
	if ( $query->have_posts() ) :
	?>
    <div class="<?php echo esc_attr(trim($wrapperClass)); ?>" data-configuration='<?php echo esc_attr(json_encode($atts)); ?>'>
        <div class="listing-event-form">
            <div class="row">
                <?php WilokeFrontendEvents::searchForm(); ?>
            </div>
        </div>
        <div class="listgo-event-items">
            <?php
            global $post;
            while ($query->have_posts()) :
                $query->the_post();
                include WILOKE_PUBLIC_DIR . 'template/vc/events-layout/default.php';
            endwhile;
            ?>
        </div>
        <div id="wiloke-event-pagination" class="nav-links text-center" data-maxposts="<?php echo esc_attr($query->found_posts); ?>" data-limitcharacter="<?php echo esc_attr($atts['limit_character']); ?>"></div>
    </div>
	<?php
    else:
	    WilokeFrontendListingManagement::message(
            array(
                'message' => esc_html__('There are no events yet.', 'listgo')
            )
        );
    endif;
	wp_reset_postdata();
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}