<?php
use \WilokeListgoFunctionality\Frontend\Notification as WilokeNotification;

global $totalNotifications;
?>
<div class="row">
	<div class="container">
		<h2 class="author-page__title">
			<i class="icon_lightbulb_alt"></i> <?php esc_html_e('Notifications', 'listgo'); ?> <span class="count wiloke-notifications-count">(<?php echo esc_attr($totalNotifications); ?>)</span>
		</h2>
		<div class="row">
			<div class="col-md-8">
				<div class="account-page">
					<div class="notifications wiloke-notifications-wrapper" data-total="<?php echo esc_attr($totalNotifications); ?>">
						<?php WilokeNotification::firstFetchNotification(); ?>
						<a href="#" id="wiloke-loadmore-notifications" class="text-center hidden"><?php esc_html_e('View more', 'listgo'); ?></a>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="sidebar-background sidebar-background--light">
					<div class="widget widget_notifi_settings">
						<h4 class="widget_title"><i class="icon_cog"></i> <?php esc_html_e('Filter Notifications', 'listgo'); ?></h4>
						<form action="#" id="wiloke-filter-notifications" method="GET">
							<div class="widget_notifi-settings">
								<h4><?php esc_html_e('Show all notifications belong to ', 'listgo'); ?></h4>
								<label class="input-toggle">
									<?php esc_html_e('Comments', 'listgo'); ?>
									<input id="filter_by_review" name="filter_by_review" value="1" type="checkbox" checked>
									<span></span>
								</label>
								<label class="input-toggle">
									<?php esc_html_e('Listings', 'listgo'); ?>
									<input id="filter_by_listing" name="filter_by_listing" value="1" type="checkbox" checked>
									<span></span>
								</label>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>