
</section> <!-- End / Main -->

<?php
global $wiloke;
if ( !empty($wiloke->aThemeOptions) && !is_page_template('templates/half-map.php') ) :
	$bgUrl = isset($wiloke->aThemeOptions['footer_bg']['id']) ? wp_get_attachment_image_url($wiloke->aThemeOptions['footer_bg']['id'], 'large'): ''; ?>
	<footer id="footer" class="bg-scroll" style="background-image: url(<?php echo esc_url($bgUrl); ?>)">
		<div class="container">
			<?php if ( isset($wiloke->aThemeOptions['footer_toggle_widgets']) && $wiloke->aThemeOptions['footer_toggle_widgets'] === 'enable' ) : ?>
				<div class="widget__row footer__widget">
					<div class="widget__col">
						<?php
						if ( is_active_sidebar('wiloke-footer-1') ){
							dynamic_sidebar('wiloke-footer-1');
						}
						?>
					</div>

					<div class="widget__col">
						<?php
						if ( is_active_sidebar('wiloke-footer-2') ){
							dynamic_sidebar('wiloke-footer-2');
						}
						?>
					</div>

					<?php
					if (  $wiloke->aThemeOptions['footer_style'] == 'footer-style2' || $wiloke->aThemeOptions['footer_style'] == 'footer-style3' ) : ?>
						<div class="widget__col">
							<?php
							if ( is_active_sidebar('wiloke-footer-3') ){
								dynamic_sidebar('wiloke-footer-3');
							}
							?>
						</div>
						<?php
					endif;
					?>

					<?php
					if ( $wiloke->aThemeOptions['footer_style'] == 'footer-style3' ) : ?>
                    <div class="widget__col">
                        <?php
                        if ( is_active_sidebar('wiloke-footer-4') ){
                            dynamic_sidebar('wiloke-footer-4');
                        }
                        ?>
                    </div>
                    <div class="widget__col">
                        <?php
                        if ( is_active_sidebar('wiloke-footer-5') ){
                            dynamic_sidebar('wiloke-footer-5');
                        }
                        ?>
                    </div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( !empty($wiloke->aThemeOptions['footer_logo']['url']) ) : ?>
				<div class="footer__logo text-center">
					<a href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php echo esc_url($wiloke->aThemeOptions['footer_logo']['url']); ?>" alt="<?php echo esc_attr(get_option('blogname')); ?>" /></a>
				</div>
			<?php endif; ?>
		</div>

		<div class="wo__container footer__bottom">

			<div class="row">
				<div class="col-sm-6 col-sm-push-6">
					<div class="social_footer">
						<?php
						WilokeSocialNetworks::render_socials($wiloke->aThemeOptions);
						?>
					</div>
				</div>
				<div class="col-sm-6 col-sm-pull-6">
					<div class="copyright">
						<?php
						Wiloke::wiloke_kses_simple_html($wiloke->aThemeOptions['footer_copyright']);
						?>
					</div>
				</div>
			</div>

		</div>
		<?php if ( isset($wiloke->aThemeOptions['footer_overlay']['rgba']) ) : ?>
			<div class="overlay" style="background-color: <?php echo esc_attr($wiloke->aThemeOptions['footer_overlay']['rgba']); ?>"></div>
		<?php endif; ?>
	</footer>
<?php endif; ?>

</div>

<div class="wil-scroll-top"><i class="arrow_up"></i></div>

<!-- Modal Popup claim -->
<?php if ( is_singular('listing') && isset($wiloke->aThemeOptions['listing_toggle_claim_listings']) && ($wiloke->aThemeOptions['listing_toggle_claim_listings'] === 'enable') ) : ?>
	<div id="wiloke-form-claim-information-wrapper" class="wil-modal wil-modal--fade">
		<div class="wil-modal__wrap">
			<div class="wil-modal__content">
				<div class="claim-form">
					<h2 class="claim-form-title"><?php esc_html_e('Claim This Listing', 'listgo'); ?></h2>
					<div class="claim-form-content">
						<form id="wiloke-form-claim-information" method="POST" class="form" action="<?php echo esc_url(get_permalink($post->ID)); ?>">
							<p><?php echo esc_html($wiloke->aThemeOptions['listing_claim_popup_description']); ?></p>
							<p class="hidden message error" style="color: red"></p>
							<p>
								<label for="claimer-phone"><?php esc_html_e('Business Phone', 'listgo'); ?></label>
								<input id="claimer-phone" name="claimer_phone" type="text">
								<input id="claim-id" name="claim_id" type="hidden" value="<?php echo esc_attr($post->ID); ?>">
							</p>
							<input type="submit" value="<?php esc_html_e('Contact Me', 'listgo'); ?>">
						</form>
					</div>
					<div class="wil-modal__close"></div>
				</div>
			</div>
		</div>
		<div class="wil-modal__overlay"></div>
	</div>
<?php endif; ?>
<!-- End / Modal Popup claim -->

<?php wp_footer(); ?>
</body>
</html>
