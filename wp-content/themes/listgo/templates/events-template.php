<?php
/*
 |--------------------------------------------------------------------------
 | Template name: Events Template
 |--------------------------------------------------------------------------
 |
 |
 */

get_header();
	global $wilokeSidebarPosition, $wiloke;
	WilokePublic::headerPage();
	$colMd = ' col-md-9';
	$aTemplateSettings = Wiloke::getPostMetaCaching($post->ID, 'events_template_settings');
	$aTemplateSettings['sidebar_position'] = isset($wiloke->aThemeOptions['events_sidebar_position']) ? $wiloke->aThemeOptions['events_sidebar_position'] : 'right';

	$wrapperClass =  '';
	switch ( $aTemplateSettings['sidebar_position'] ){
		case 'left':
			$mainClass = $colMd.' col-md-push-3';
			$wilokeSidebarPosition = 'left';
		break;
		case 'right':
			$mainClass = $colMd;
			$wilokeSidebarPosition = 'right';
		break;
		default:
			$mainClass = 'col-md-12';
			$wilokeSidebarPosition = 'no';
		break;
	}
	?>
	<div class="section<?php echo esc_attr($wrapperClass); ?>">
		<div class="container">
			<div class="row">
				<div class="<?php echo esc_attr($mainClass); ?>">
					<div class="listgo-listlayout-on-page-template">
						<?php
							echo do_shortcode('[wiloke_events_list posts_per_page="'.esc_attr($aTemplateSettings['posts_per_page']).'" image_size="'.esc_attr($aTemplateSettings['image_size']).'" limit_character="'.esc_attr($aTemplateSettings['limit_character']).'"]');
						?>
					</div>
				</div>
				<?php
				if ( $wilokeSidebarPosition !== 'no' ) {
					get_sidebar('events');
				}
				?>
			</div>
		</div>
	</div>
<?php
get_footer();