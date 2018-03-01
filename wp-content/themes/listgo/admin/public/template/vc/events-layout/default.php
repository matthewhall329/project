<?php
use WilokeListGoFunctionality\Frontend\FrontendEvents as WilokeFrontendEvents;

$aSettings = Wiloke::getPostMetaCaching($post->ID, 'event_settings');

if ( !empty($aSettings['belongs_to']) ){
	$linkToEvent = get_permalink($aSettings['belongs_to']) . '#tab-event--goto-event-'.$post->ID;
	$target = '_self';
}else{
	$linkToEvent = isset($aSettings['event_link']) ? $aSettings['event_link'] : '#';
	$target = '_blank';
}

$aStatus = WilokeFrontendEvents::checkEventStatus($aSettings);

$aListEventStatus = array(
	'expired'   => __('Expired', 'listgo'),
	'upcomming' => __('Upcoming', 'listgo'),
	'ongoing'   => __('Ongoing', 'listgo')
);

?>
<div class="listing-event" data-id="<?php echo esc_attr($post->ID); ?>">
	<a href="<?php echo esc_url($linkToEvent); ?>" target="<?php echo esc_attr($target); ?>">
		<div class="listing-event__media">
			<div class="listing-event__media-img lazy" data-src="<?php echo esc_url(get_the_post_thumbnail_url($post->ID, $atts['image_size'])); ?>"></div>
			<span class="listing-event__status <?php echo esc_attr($aStatus['status']); ?>"><?php echo esc_html($aListEventStatus[$aStatus['status']]); ?></span>
		</div>
		<div class="listing-event__body">
			<h2 class="listing-event__title"><?php the_title(); ?></h2>
			<p class="listing-event__desc"><?php Wiloke::wiloke_content_limit($atts['limit_character'], $post, true, $post->post_content, false); ?></p>
			<div class="listing-event__start">
				<span class="listing-event__label"><?php esc_html_e('Event Detail: ', 'listgo'); ?></span>
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
						<td class="listing-event__from" title="<?php esc_html_e('From', 'listgo'); ?>">
							<p>
								<?php if ( !empty($aSettings['start_at']) ) : ?>
									<i class="color-green icon_clock_alt"></i> <?php echo esc_html(apply_filters('wiloke/listgo/admin/public/template/vc/wiloke_events/start_at', $aSettings['start_at'])); ?> <br>
								<?php endif; ?>
								<i class="color-green icon_table"></i> <?php echo esc_html(listgoRenderDate($aSettings['start_on'])); ?>
							</p>
						</td>
						<td class="listing-event__to"  title="<?php esc_html_e('To', 'listgo'); ?>">
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
	</a>
</div>