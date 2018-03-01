<?php
global $wiloke;
$shopSidebar = 'no';
$mainClass = 'woocommerce-without-sidebar';
if ( !empty($wiloke->aThemeOptions) ){
	$shopSidebar = $wiloke->aThemeOptions['woocommerce_sidebar'];
}
switch ($shopSidebar){
	case 'left':
		$mainClass = 'col-md-8 col-lg-9 col-md-push-4 col-lg-push-3';
		break;
	case 'right':
		$mainClass = 'col-md-8 col-lg-9';
		break;
	default;
}
?>
<div class="<?php echo esc_attr($mainClass); ?>">