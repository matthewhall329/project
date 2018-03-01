<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */
get_header();

global $wiloke;

$bg = isset($wiloke->aThemeOptions['general_pagenotfound_bg']) && isset($wiloke->aThemeOptions['general_pagenotfound_bg']['id']) ? wp_get_attachment_image_url($wiloke->aThemeOptions['general_pagenotfound_bg']['id'], 'large') : ''; ?>

<div class="wil-404 <?php echo !empty($bg) ? 'wil-404-bg' : '' ?>" style="background-image:url(<?php echo esc_url($bg); ?>)">
	<div class="tb">
		<div class="tb__cell">
			<div class="container">
		        <div class="wil-404-content">
		        	<h2><?php esc_html_e( '404', 'listgo' ); ?></h2>
		        	<p><?php Wiloke::wiloke_kses_simple_html(sprintf( __('Sorry, We Couldn\'t find what you were looking for. Maybe try searching for an alternative?', 'listgo'), get_query_var('s') )); ?></p>
		        	<?php get_search_form(); ?>
	        	</div>
		    </div>
		</div>
	</div>
</div>

<?php get_footer();
