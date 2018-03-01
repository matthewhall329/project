<?php
global $wiloke;
$shopSidebar = 'no';
$sidebarClass = 'woocommerce-without-sidebar';
if ( !empty($wiloke->aThemeOptions) ){
	$shopSidebar = $wiloke->aThemeOptions['woocommerce_sidebar'];
}

switch ($shopSidebar){
	case 'left':
		$sidebarClass = 'col-md-4 col-md-pull-8 col-lg-3 col-lg-pull-9';
		break;
	case 'right':
		$sidebarClass = 'col-md-4 col-lg-3';
		break;
	default;
}

if ( $shopSidebar === 'no' ){
	return false;
}

$sidebarStyle = isset($wiloke->aThemeOptions['woocommerce_sidebar_style']) ? $wiloke->aThemeOptions['woocommerce_sidebar_style'] : 'sidebar'; ?>

<div class="<?php echo esc_attr($sidebarClass); ?>">

	<div class="<?php echo esc_attr($sidebarStyle); ?>">

		<?php if ( is_active_sidebar('wiloke-woocommerce-sidebar') ) {
			dynamic_sidebar('wiloke-woocommerce-sidebar');
		} ?>

	</div>

</div>
