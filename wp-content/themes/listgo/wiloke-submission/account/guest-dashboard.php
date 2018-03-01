
<h2 class="author-page__title">
	<i class="icon_id"></i> <?php esc_html_e('Profile', 'listgo'); ?>
</h2>
<div class="row">
	<div class="col-md-8 col-md-push-4">
		<div class="account-page">
			<?php if ( isset($oUserInfo->description) && !empty($oUserInfo->description) ) : ?>
				<p><?php Wiloke::wiloke_kses_simple_html($oUserInfo->description); ?></p>
			<?php else: ?>
				<p>
					<?php
					if ( $isViewByMySelf ){
						esc_html_e('Your life is secret ;)! The other people do not know anything about you. You can change this status by clicking on the General Settings and updating your information.', 'listgo');
					}else{
						esc_html_e('My life is secret ;)!', 'listgo');
					}
					?>
				</p>
			<?php endif; ?>

		</div>
	</div>

	<div class="col-md-4 col-md-pull-8">

		<div class="sidebar-background sidebar-background--light">

			<div class="widget widget_author">

				<div class="widget_author__header">
					<div class="widget_author__avatar">
						<?php
						$avatar = WilokePublic::getUserAvatar($oUserInfo->ID, 'thumbnail');
						if ( strpos($avatar, 'profile-picture.jpg') === false ) {
							?>
							<img src="<?php echo esc_url(WilokePublic::getUserAvatar($oUserInfo->ID, 'thumbnail')); ?>" alt="<?php echo esc_attr($oUserInfo->display_name); ?>">
							<?php
						}else{
							$firstCharacter = strtoupper(substr($oUserInfo->display_name, 0, 1));
							echo '<span style="background-color: '.esc_attr(WilokePublic::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
						}
						?>
					</div>

					<div class="overflow-hidden">
						<h4 class="widget_author__name"><?php echo esc_html($oUserInfo->display_name); ?></h4>
						<?php WilokePublic::renderBadge($oUserInfo->role); ?>
					</div>
				</div>

				<?php if ( !empty($oUserInfo->meta) ) : ?>
					<div class="widget_author__content">
						<ul class="widget_author__address">
							<?php if ( !empty($oUserInfo->meta['wiloke_address']) ) : ?>
								<li>
									<i class="fa fa-map-marker"></i>
									<?php Wiloke::wiloke_kses_simple_html($oUserInfo->meta['wiloke_address']); ?>
								</li>
							<?php endif; ?>

							<?php if ( !empty($oUserInfo->meta['wiloke_phone']) ) : ?>
								<li>
									<i class="fa fa-phone"></i>
									<?php Wiloke::wiloke_kses_simple_html($oUserInfo->meta['wiloke_phone']); ?>
								</li>
							<?php endif; ?>

							<?php if ( !empty($oUserInfo->user_url) ) : ?>
								<li>
									<i class="fa fa-globe"></i>
									<a href="<?php echo esc_url($oUserInfo->user_url); ?>"><?php Wiloke::wiloke_kses_simple_html($oUserInfo->user_url); ?></a>
								</li>
							<?php endif; ?>
						</ul>
						<?php if ( isset($oUserInfo->meta['wiloke_user_socials']) && !empty($oUserInfo->meta['wiloke_user_socials']) ) : ?>
							<div class="widget_author__social">
								<?php
								foreach ( $oUserInfo->meta['wiloke_user_socials'] as $socialKey => $url ) :
									if ( !empty($url) ) :
										?>
										<a href="<?php echo esc_url($url); ?>"><i class="fa fa-<?php echo esc_attr($socialKey); ?>"></i></a>
										<?php
									endif;
								endforeach;
								?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>