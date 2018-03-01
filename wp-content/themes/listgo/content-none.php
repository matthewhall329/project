<?php
global $wiloke;
$bg = isset($wiloke->aThemeOptions['general_pagenotfound_bg']) && isset($wiloke->aThemeOptions['general_pagenotfound_bg']['id']) ? wp_get_attachment_image_url($wiloke->aThemeOptions['general_pagenotfound_bg']['id'], 'large') : '';
?>
<div class="wil-content-none col-md-12 <?php echo !empty($bg) ? 'wil-404-bg' : '' ?>" style="background-image:url(<?php echo esc_url($bg); ?>)">
	<div class="tb">
		<div class="tb__cell">
			<div class="container">
				<div class="wil-404-content text-center">
					<h4><?php esc_html_e( 'Nothing Found', 'listgo' ); ?></h4>
					<p><?php Wiloke::wiloke_kses_simple_html(sprintf( __('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'listgo'), get_query_var('s') )); ?></p>
					<?php get_search_form(); ?>
				</div>
			</div>
		</div>
	</div>
</div>