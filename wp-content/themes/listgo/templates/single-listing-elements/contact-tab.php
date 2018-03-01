<?php
if ( WilokePublic::toggleTabStatus('listing_toggle_tab_contact_and_map') === 'enable' ) :
	$hasContactForm = isset($wiloke->aThemeOptions['listing_contactform7']) && !empty($wiloke->aThemeOptions['listing_contactform7']) && defined('WPCF7_PLUGIN');
	if ( isset($aSettings['map']['latlong']) ){
		$className = $hasContactForm ? 'col-md-6' : 'col-md-12';
		$coordinate = $aSettings['map']['latlong'];
	}else{
		$className = 'col-md-12';
	}
?>
	<div id="tab-contact" class="tab__panel">
		<div class="listing-single__contact-map">
			<div class="row">
				<div class="<?php echo esc_attr($className); ?>">
					<div class="listing-single__contact form">
						<?php
						if ( $hasContactForm ){
							echo do_shortcode('[contact-form-7 id="'.esc_attr($wiloke->aThemeOptions['listing_contactform7']).'" title="Contact Form"]');
						}else{
							WilokeAlert::render_alert( esc_html__('Please go to Appearance -> Theme Options -> Listing Settings and assign a conform to Set Contact Form 7 setting.', 'listgo'), 'warning' );
						}
						?>

					</div>
				</div>
				<?php
				if ( isset($coordinate) ) :
					$marker = '';
					$aListingListingCat = Wiloke::getPostTerms($post, 'listing_cat');
					if ( isset($aListingListingCat[0]) && !empty($aListingListingCat[0]) ){
						$aCatOptions = Wiloke::getTermOption($aListingListingCat[0]->term_id);
						if ( isset($aCatOptions['map_marker_image']) ){
							$marker = $aCatOptions['map_marker_image'];
						}
					}
					?>
					<div class="<?php echo esc_attr($className); ?>">
						<div id="listing-single__map" class="listing-single__map" data-map="<?php echo esc_attr($coordinate); ?>" data-marker="<?php echo esc_url($marker); ?>"></div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endif; ?>