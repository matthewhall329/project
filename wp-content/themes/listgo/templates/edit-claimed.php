<?php
/*
 * Template name: Edit Claimed
 */
use WilokeListGoFunctionality\Frontend\FrontendClaimListing as WilokeFrontendClaimListing;

$msg = esc_html__('You do not have permission to access this page', 'listgo');
if ( !isset($_REQUEST['listing_id']) || empty($_REQUEST['listing_id']) || !is_user_logged_in() ){
	wp_die($msg);
}

$postID = absint($_REQUEST['listing_id']);

if ( !WilokeFrontendClaimListing::verifyClaim($postID) ){
	wp_die($msg);
}

global $wiloke;

get_header();
	$oListingInfo     = get_post($postID);
	$listingContent   = $oListingInfo->post_content;
	$title            = $oListingInfo->post_title;
	$aSocialMedia     = Wiloke::getPostMetaCaching($oListingInfo->ID, 'listing_social_media');

	$aSocials = array(
		'facebook', 'twitter', 'google-plus', 'linkedin', 'tumblr', 'instagram', 'pinterest', 'vimeo', 'youtube', 'whatsapp'
	);

	$aPriceSegment = array_merge(
		array(
			''   => esc_html__('Rather not say', 'listgo')
		),
		$wiloke->aConfigs['frontend']['price_segmentation']
	);

	$aPrice = Wiloke::getPostMetaCaching($postID, 'listing_price');

	$aPrice = wp_parse_args(
		$aPrice,
		array(
			'price_segment' => '',
			'price_from'  => '',
			'price_to'    => ''
		)
	);
    $aToggleBusinessHours = array(
        'enable' => esc_html__('Enable', 'listgo'),
        'disable' => esc_html__('Disable', 'listgo')
    );
    $toggleBusinessHoursStatus = get_post_meta($postID, 'wiloke_toggle_business_hours', true);
	$featuredImageID  = get_post_thumbnail_id($postID);
	$featuredImageUrl = !empty($featuredImageID) ? get_the_post_thumbnail_url($postID) : get_template_directory_uri() . '/img/featured-image.jpg';

	$aBusinessHours     = Wiloke::getPostMetaCaching($postID, 'wiloke_listgo_business_hours');
	$aGeneralSettings   = Wiloke::getPostMetaCaching($postID, 'listing_settings');
	$aGalleries         = Wiloke::getPostMetaCaching($postID, 'gallery_settings');

	$template           = get_page_template_slug($postID);
	$template           = !empty($template) ? $template : 'templates/single-listing-creative.php';
	$aCurrentTags       = Wiloke::getPostTerms($oListingInfo, 'listing_tag');

    if ( !empty($aCurrentTags) ){
        $aCurrentTags = array_map(function($oTerm){
            return $oTerm->slug;
        }, $aCurrentTags);
    }else{
	    $aCurrentTags = array();
    }
?>
		<div class="page-addlisting">
			<div class="container">
				<div class="row">
					<div class="col-lg-10 col-lg-offset-1">
						<h4 class="author-page__title"><i class="icon_pencil-edit"></i> <?php esc_html_e('Edit Listing', 'listgo'); ?></h4>
						<div class="account-page">
							<div class="form form--profile">

								<div class="clearfix"></div>

								<form id="wiloke-form-claimed-listing" action="#" method="POST" data-url="<?php echo esc_url(get_permalink($postID)); ?>">
									<input type="hidden" name="post_id" value="<?php echo esc_attr($postID); ?>">
									<div class="row">
										<!-- Listing Title -->
										<div class="col-sm-6">
											<div class="form-item">
												<label for="listing_title" class="label"><?php esc_html_e('Listing Title', 'listgo'); ?> <sup>*</sup></label>
												<span class="input-text">
                                                    <input id="listing_title" type="text" name="listing_title" value="<?php echo esc_attr($title); ?>" required>
                                                </span>
											</div>
										</div>
										<!-- End / Listing Title -->
										<div class="col-sm-6">
											<div class="form-item">
												<label for="listing_style" class="label"><?php esc_html_e('Listing Style', 'listgo'); ?></label>
												<span class="input-select2">
                                                     <select id="listing_style" class="js_select2" name="listing_style" required>
                                                        <option value="templates/single-listing-creative.php" <?php selected($template, 'single-listing-creative.php'); ?>><?php esc_html_e('Creative', 'listgo'); ?></option>
                                                        <option value="templates/single-listing-traditional.php" <?php selected($template, 'single-listing-traditional.php'); ?>><?php esc_html_e('Traditional', 'listgo'); ?></option>
                                                        <option value="templates/single-listing-lisa.php" <?php selected($template, 'templates/single-listing-lisa.php'); ?>><?php esc_html_e('Lisa', 'listgo'); ?></option>
                                                        <option value="templates/single-listing-howard-roark.php" <?php selected($template, 'templates/single-listing-howard-roark.php'); ?>><?php esc_html_e('Howard Roark', 'listgo'); ?></option>
                                                    </select>
                                                </span>
											</div>
										</div>

										<!-- Featured -->
										<div class="col-sm-12">
											<div class="form-item">
												<!-- Featured Image And Header Image -->
												<label for="listing_cats" class="label"><?php esc_html_e('Featured Image', 'listgo'); ?></label>
												<div class="add-listing-img wiloke-js-upload">
													<img class="wiloke-preview" src="<?php echo esc_url($featuredImageUrl); ?>" alt="<?php esc_html_e('Featured Image', 'listgo'); ?>">
													<input type="hidden" id="wiloke_feature_image" class="wiloke-insert-id" name="featured_image" value="<?php echo esc_attr($featuredImageID); ?>">
												</div>
											</div>
										</div>
										<!-- End / Featured -->

										<!-- Listing Content -->
										<div class="col-sm-12">
											<div class="form-item">
												<label for="listing_content" class="label"><?php esc_html_e('Listing Content', 'listgo'); ?> <sup>*</sup></label>
												<span class="input-text">
                                                    <?php wp_editor($listingContent, 'listing_content'); ?>
                                                </span>
											</div>
										</div>
										<!-- End / Listing Content -->

										<div class="col-sm-6">
											<div class="form-item">
												<label for="listing_phone" class="label"><?php esc_html_e('Phone', 'listgo'); ?></label>
												<span class="input-text">
                                                    <input id="listing_phone" type="text" name="listing_phonenumber" value="<?php echo isset($aGeneralSettings['phone_number']) ? esc_html($aGeneralSettings['phone_number']) : ''; ?>">
                                                </span>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-item">
												<label for="listing_website" class="label"><?php esc_html_e('Website', 'listgo'); ?></label>
												<span class="input-text">
                                                    <input id="listing_website" type="text" name="listing_website" value="<?php echo isset($aGeneralSettings['website']) ? esc_url($aGeneralSettings['website']) : ''; ?>">
                                                </span>
											</div>
										</div>
										<div class="col-sm-12">
                                            <div class="form-item">
                                                <label for="wiloke-latlong" class="label"><?php esc_html_e('Listing Google Address', 'listgo'); ?></label>
                                                <div class="wiloke-latlongwrapper">
                                                    <input id="wiloke-location" type="text" class="text" placeholder="<?php esc_html_e('Listing Address, Eg:  Manchester, United Kingdom', 'listgo') ?>" name="listing_address" value="<?php echo isset($aGeneralSettings['map']) ? esc_html($aGeneralSettings['map']['location']) : ''; ?>" required>
                                                    <input id="wiloke-latlong" type="text" name="listing_latlng" value="<?php echo isset($aGeneralSettings['map']) ? esc_html($aGeneralSettings['map']['latlong']) : ''; ?>" placeholder="<?php esc_html_e('Latitude and Longitude, Eg: 53.480759,-2.242631', 'listgo') ?>" required>
                                                    <input id="wiloke-place-information" type="hidden" name="listing_place_information" required>
                                                    <div id="wiloke-map" class="wiloke-map"></div>
                                                </div>
                                            </div>
											<?php
											if ( !isset($wiloke->aThemeOptions['general_map_api']) || empty($wiloke->aThemeOptions['general_map_api']) ) {
												WilokeAlert::message(esc_html__('Please go to Appearance -> Theme Options -> General and supply your Google API key', 'listgo'), false);
											}
											?>
										</div>
									</div>

									<h4 class="profile-title"><?php esc_html_e('Price Ranger', 'listgo'); ?></h4>
									<div class="row">
										<div class="col-sm-4">
											<div class="form-item">
												<label for="price_range" class="label"><?php esc_html_e('Price Segment', 'listgo'); ?></label>
												<span>
                                                    <select id="price_range" name="listing_price[price_segment]">
                                                        <?php foreach ( $aPriceSegment as $segment => $definition ) : ?>
                                                            <option value="<?php echo esc_attr($segment); ?>" <?php selected($aPrice['price_segment'], $segment); ?>><?php echo esc_html($definition); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </span>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-item">
												<label for="price_from" class="label"><?php esc_html_e('Minimum Price', 'listgo'); ?></label>
												<span class="input-text">
                                                    <input id="price_from" type="text" name="listing_price[price_from]" value="<?php echo esc_attr($aPrice['price_from']); ?>" placeholder="100$">
                                                </span>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-item">
												<label for="price_to" class="label"><?php esc_html_e('Maximum Price', 'listgo'); ?></label>
												<span class="input-text">
                                                    <input id="price_to" type="text" name="listing_price[price_to]" value="<?php echo esc_attr($aPrice['price_to']); ?>" placeholder="200$">
                                                </span>
											</div>
										</div>
									</div>

									<!-- Business Hours -->
									<h4 class="profile-title"><?php esc_html_e('Business Hours', 'listgo'); ?></h4>
									<div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-item">
                                                <label for="wiloke-toggle-business-hours" class="label"><?php esc_html_e('Toggle Business Hours', 'listgo'); ?></label>
                                                <span>
                                                    <select id="wiloke-toggle-business-hours" name="toggle_business_hours">
                                                        <?php foreach ( $aToggleBusinessHours as $option => $name ) : ?>
                                                            <option value="<?php echo esc_attr($option); ?>" <?php selected($toggleBusinessHoursStatus, $option); ?>><?php echo esc_html($name); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </span>
                                            </div>
                                        </div>

										<div id="wiloke-tbl-business-hours" class="col-sm-12">
										
											<table class="table table-bordered profile-hour">
												<thead>
												<tr>
													<th><?php esc_html_e('Day', 'listgo'); ?></th>
													<th><?php esc_html_e('Start time', 'listgo'); ?></th>
													<th><?php esc_html_e('End time', 'listgo'); ?></th>
													<th><?php esc_html_e('Closed', 'listgo'); ?></th>
												</tr>
												</thead>

												<?php
												foreach ( $wiloke->aConfigs['frontend']['listing']['business_hours']['days'] as $key => $day ) :
													$aValues = isset($aBusinessHours[$key]) ? $aBusinessHours[$key] : $wiloke->aConfigs['frontend']['listing']['business_hours']['default'];
													?>
													<tr>
														<td class="business-day" data-title="<?php esc_html_e('Day', 'listgo'); ?>"><?php echo esc_html($day); ?></td>
														<td class="business-start" data-title="<?php esc_html_e('Start time', 'listgo'); ?>">
															<input type="number" name="listgo_bh[<?php echo esc_attr($key) ?>][start_hour]" max="12" min="0" value="<?php echo esc_attr($aValues['start_hour']); ?>"> :
															<input type="number" name="listgo_bh[<?php echo esc_attr($key) ?>][start_minutes]" max="60" min="0" value="<?php echo esc_attr($aValues['start_minutes']); ?>"> :
															<select name="listgo_bh[<?php echo esc_attr($key) ?>][start_format]">
																<option value="AM" <?php selected($aValues['start_format'], 'AM'); ?>><?php esc_html_e('AM', 'listgo'); ?></option>
																<option value="PM" <?php selected($aValues['start_format'], 'PM'); ?>><?php esc_html_e('PM', 'listgo'); ?></option>
															</select>
														</td>
														<td class="business-end" data-title="<?php esc_html_e('End time', 'listgo'); ?>">
															<input type="number" name="listgo_bh[<?php echo esc_attr($key) ?>][close_hour]" max="12" min="0" value="<?php echo esc_attr($aValues['close_hour']); ?>"> :
															<input type="number" name="listgo_bh[<?php echo esc_attr($key) ?>][close_minutes]" max="60" min="0" value="<?php echo esc_attr($aValues['close_minutes']); ?>"> :
															<select name="listgo_bh[<?php echo esc_attr($key) ?>][close_format]">
																<option value="AM" <?php selected($aValues['close_format'], 'AM'); ?>><?php esc_html_e('AM', 'listgo'); ?></option>
																<option value="PM" <?php selected($aValues['close_format'], 'PM'); ?>><?php esc_html_e('PM', 'listgo'); ?></option>
															</select>
														</td>
														<td class="business-close" data-title="<?php esc_html_e('Close', 'listgo'); ?>">
															<label for="bh-closed-<?php echo esc_attr($key); ?>" class="input-checkbox">
																<input id="bh-closed-<?php echo esc_attr($key); ?>" type="checkbox" name="listgo_bh[<?php echo esc_attr($key) ?>][closed]" value="1" <?php echo isset($aValues['closed']) && $aValues['closed'] === '1' ? 'checked' : ''; ?> value="1">
																<span></span>
															</label>
														</td>
													</tr>
												<?php endforeach; ?>

											</table>
										
										</div>
									</div>
									<!-- END / Business Hours -->

									<h4 class="profile-title"><?php esc_html_e('Social Media', 'listgo'); ?></h4>
									<!-- Social Media -->
									<div class="row">
										<?php foreach ($aSocials as $social) : $name = $social === 'google-plus' ? esc_html__('Google+', 'listgo') : ucfirst($social); ?>
											<div class="col-sm-6">
												<div class="form-item">
													<label for="<?php echo esc_attr($social); ?>" class="label"><?php echo esc_html($name); ?></label>
													<span class="input-text input-icon-left">
	                                                    <input id="<?php echo esc_attr($social); ?>" name="listing[social][<?php echo esc_attr($social); ?>]" type="text" value="<?php echo isset($aSocialMedia[$social]) ? esc_url($aSocialMedia[$social]) : ''; ?>">
	                                                    <i class="input-icon fa fa-<?php echo esc_attr($social); ?>"></i>
	                                                </span>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
									<!-- END / Social Media -->

									<!-- Gallery -->
									<div class="row">
										<div class="col-sm-12">
											<div class="form-item">
												<label for="listing_tags" class="label"><?php esc_html_e('Keywords', 'listgo'); ?></label>
												<span class="input-text">
                                                    <textarea name="listing_tags" id="listing_tags" rows="4" cols="10" placeholder="<?php esc_html_e('Each keyword is sereparated by a comma. Eg: wordpress,drupal', 'listgo'); ?>"><?php echo esc_textarea(implode(',', $aCurrentTags)); ?></textarea>
                                                </span>
											</div>
										</div>
										<div class="col-sm-12">
											<div class="form-item">
												<label class="label"><?php esc_html_e('Add Gallery', 'listgo'); ?></label>
												<div class="wil-addlisting-gallery">
													<ul id="wiloke-preview-gallery" class="wil-addlisting-gallery__list">
														<?php
														if ( isset($aGalleries['gallery']) && !empty($aGalleries['gallery']) ) :
															foreach ( $aGalleries['gallery'] as $id => $url  ) :
																if ( !empty($id) ) :
																	?>
																	<li data-id="<?php echo esc_attr($id); ?>" class="bg-scroll gallery-item" style="background-image: url(<?php echo esc_url(wp_get_attachment_image_url($id, 'thumbnail')) ?>);">
																		<span class="wil-addlisting-gallery__list-remove"><?php esc_html_e('Remove', 'listgo'); ?></span>
																	</li>
																<?php endif; endforeach;
														endif;
														?>
														<li id="wiloke-listgo-add-gallery" class="wil-addlisting-gallery__placeholder" title="<?php esc_html_e('Upload Gallery', 'listgo'); ?>">
															<button data-multiple="true" class="wiloke-js-upload"><i class="fa fa-camera"></i></button>
														</li>
													</ul>
												</div>
												<input type="hidden" id="listing_gallery" class="wiloke-insert-id" name="listing_gallery" value="">
											</div>
										</div>
									</div>
									<!-- END / Gallery -->

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="profile-actions">
                                                <div id="wiloke-print-msg-here" class="wiloke-print-msg-here"></div>
                                                <div class="profile-actions__right">
                                                        <button id="wiloke-listgo-edit-listing-claimed" type="submit" data-postid="<?php echo esc_attr($postID); ?>" href="#" class="listgo-btn btn-primary"><span><?php esc_html_e('Save Changes', 'listgo'); ?></span></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

								</form>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
get_footer();