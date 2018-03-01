<?php
global $wiloke, $wilokeSidebarLayout;
$sidebarClass = $wilokeSidebarLayout === 'left' ? 'col-md-3 col-md-pull-9' : 'col-md-3';
$sidebarStyle = isset($wiloke->aThemeOptions['blog_sidebar_style']) ? $wiloke->aThemeOptions['blog_sidebar_style'] : 'sidebar';
?>
<div class="<?php echo esc_attr($sidebarClass); ?>">
	<div class="<?php echo esc_attr($sidebarStyle); ?>">
		<?php
		if ( is_front_page() || is_home() || is_category() || is_tag() || is_singular('post') || is_singular('page') || is_archive() || is_search() ){
			if ( is_active_sidebar('wiloke-blog-sidebar') ){
				dynamic_sidebar('wiloke-blog-sidebar');
			}
		}else if ( is_tax('listing_location') || is_tax('listing_category') ){
			if ( is_active_sidebar('wiloke-listing-sidebar') ){
				dynamic_sidebar('wiloke-listing-sidebar');
			}
		}
		?>
	</div>
</div>
