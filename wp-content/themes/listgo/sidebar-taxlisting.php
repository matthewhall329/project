<?php
global $wiloke;
global $wiloke, $wilokeSidebarPosition;
$sidebarStyle = isset($wiloke->aThemeOptions['listing_sidebar_style']) ? $wiloke->aThemeOptions['listing_sidebar_style'] : 'sidebar';
if ( is_author() ){
	$sidebarClass = $wilokeSidebarPosition === 'left' ? 'col-md-4 col-md-pull-8' : 'col-md-4';
	$sidebarStyle .= ' sidebar-background';
}else{
	$sidebarClass = $wilokeSidebarPosition === 'left' ? 'col-md-3 col-md-pull-9' : 'col-md-3';
}
?>
<div class="<?php echo esc_attr($sidebarClass); ?>">
	<div class="<?php echo esc_attr($sidebarStyle); ?>">
		<?php
		if ( is_active_sidebar('wiloke-listing-sidebar') ){
			dynamic_sidebar('wiloke-listing-sidebar');
		}
		?>
	</div>
</div>
