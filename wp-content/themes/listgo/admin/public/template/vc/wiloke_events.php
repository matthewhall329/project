<?php
function wiloke_shortcode_events($atts){
	$atts = shortcode_atts(
		array(
			'description'           => esc_html__('Event Information', 'listgo'),
			'extract_class'         => '',
			'limit_character'       => '',
			'view_all_events_description'       => '',
			'view_all_events_button_name'       => '',
			'view_all_events_button_link'       => '',
			'css'                   => ''
		),
		$atts
	);

	$query = new WP_Query(
		array(
			'post_type'         => 'event',
			'posts_per_page'    => 50,
			'post_status'       => 'publish',
			'orderby'           => 'menu_order date',
			'meta_query'  => array(
				'key'     => 'toggle_show_events_on_event_carousel',
				'value'   => '=',
				'compare' => 'enable'
			),
			'order'             => 'DESC',
		)
	);

	ob_start();
		$wrapperClass = 'container listgo-event-container' . ' ' . $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');
	if ( $query->have_posts() ) :
	?>
		<div class="<?php echo esc_attr($wrapperClass); ?>">
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<div class="events-carousel owl-carousel nav-middle">
						<?php
                        while ($query->have_posts()) : $query->the_post(); $aSettings = Wiloke::getPostMetaCaching($query->post->ID, 'event_settings');
                                $target = '_self';

                                if ( !empty($aSettings['belongs_to']) ){
	                                $linkToEvent = get_permalink($aSettings['belongs_to']) . '#tab-event--goto-event-'.$query->post->ID;
                                }elseif (!empty($aSettings['event_link'])){
                                    $linkToEvent = $aSettings['event_link'];
                                    $target = '_blank';
                                }

                                $postThumbnail = get_the_post_thumbnail_url($query->post->ID, 'large'); ?>
                                <div class="event-item">
                                    <?php if ( has_post_thumbnail($query->post->ID) ) : ?>
                                    <div class="event-item__media bg-scroll" style="background-image: url(<?php echo esc_url($postThumbnail); ?>)"></div>
                                    <?php endif; ?>

                                    <div class="event-item__body">
                                        <?php if ( !empty($linkToEvent) ) : ?>
                                            <h3 class="event-item__title"><a href="<?php echo esc_url($linkToEvent); ?>" target="<?php echo esc_attr($target); ?>"><?php echo get_the_title($query->post->ID); ?></a></h3>
                                        <?php else: ?>
                                            <h3 class="event-item__title"><?php echo get_the_title($query->post->ID); ?></h3>
                                        <?php endif; ?>

                                        <div class="event-item__content">
                                            <?php if ( empty($atts['limit_character']) ) : ?>
                                                <p><?php echo strip_shortcodes(get_the_content($query->post->ID));  ?></p>
                                            <?php else: ?>
                                                <p><?php Wiloke::wiloke_content_limit($atts['limit_character'], $query->post, false, $query->post->post_content, false);  ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <div class="listing-event__start">
                                            <span class="listing-event__label"><?php esc_html_e('This event starts:', 'listgo'); ?></span>
                                            <table class="listing-event__table">
                                                <thead>
                                                    <tr>
                                                        <th><?php esc_html_e('Address', 'listgo'); ?></th>
                                                        <th><?php esc_html_e('From', 'listgo'); ?></th>
                                                        <th><?php esc_html_e('To', 'listgo'); ?></th>
                                                    </tr>
                                                </thead>
                                                <tr>
                                                    <td class="listing-event__address" title="<?php esc_html_e('Address', 'listgo'); ?>">
                                                        <p>
                                                            <i class="color-yelow icon_pin_alt"></i> <?php echo esc_html($aSettings['place_detail']); ?>
                                                        </p>
                                                    </td>
                                                    <td class="listing-event__from"  title="From">
                                                        <p>
                                                            <?php if ( !empty($aSettings['start_at']) ) : ?>
                                                            <i class="color-green icon_clock_alt"></i> <?php echo esc_html(apply_filters('wiloke/listgo/admin/public/template/vc/wiloke_events/start_at', $aSettings['start_at'])); ?> <br>
                                                            <?php endif; ?>
                                                            <i class="color-green icon_table"></i> <?php echo esc_html(listgoRenderDate($aSettings['start_on'])); ?>
                                                        </p>
                                                    </td>
                                                    <td class="listing-event__to"  title="To">
                                                        <p>
                                                            <?php if ( !empty($aSettings['end_at']) ) : ?>
                                                            <i class="color-red icon_clock_alt"></i> <?php echo esc_html(apply_filters('wiloke/listgo/admin/public/template/vc/wiloke_events/end_at', $aSettings['end_at'])); ?> <br>
                                                            <?php endif; ?>
                                                            <i class="color-red icon_table"></i> <?php echo esc_html(listgoRenderDate($aSettings['end_on'])); ?>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>

                                    </div>
                                </div>
						<?php endwhile; ?>
					</div>
					<?php if ( !empty($atts['view_all_events_button_link']) ) : ?>
                        <div class="listgo-view-all-events-wrapper">
							<?php if ( !empty($atts['view_all_events_description']) ) : ?>
                                <p class="listgo-view-all-event-desc"><?php Wiloke::wiloke_kses_simple_html($atts['view_all_events_description']); ?></p>
							<?php endif; ?>
                            <a href="<?php echo esc_url($atts['view_all_events_button_link']) ?>" class="listgo-btn listgo-btn btn-primary listgo-btn--md listgo-btn--round"><?php echo esc_html($atts['view_all_events_button_name']); ?></a>
                        </div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php
	endif; wp_reset_postdata();
	$content = ob_get_clean();
	return $content;
}