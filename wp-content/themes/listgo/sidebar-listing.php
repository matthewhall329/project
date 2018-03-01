<?php
global $wiloke, $wilokeSidebarPosition;

$sidebarColumn = 'col-md-4 listgo-single-listing-sidebar-wrapper';
$sidebarClass = 'sidebar-background listgo-single-listing-sidebar';

if ( WilokePublic::inListingTemplates(array('templates/single-listing-creative-sidebar.php', 'templates/single-listing-lively.php')) ){
	$sidebarClass = 'sidebar-background--light';
}

if ( WilokePublic::inListingTemplates(array('templates/single-listing-creative-sidebar.php')) || ($wilokeSidebarPosition == 'left') ) {
	$sidebarColumn = 'col-md-4 col-md-pull-8';
}
?>

<div class="<?php echo esc_attr($sidebarColumn); ?>">
	<div class="<?php echo esc_attr($sidebarClass) ?>">
		<?php
		if ( is_active_sidebar('wiloke-singular-listing-sidebar') ){
			dynamic_sidebar('wiloke-singular-listing-sidebar');
		}
		?>
	</div>
</div>
