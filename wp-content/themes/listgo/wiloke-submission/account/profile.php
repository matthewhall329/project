<?php
if ( isset($_REQUEST['user']) ){
	$oUserInfo = (object)Wiloke::getUserMeta($_REQUEST['user']);
	$isViewByMySelf = false;
}else{
	$oUserInfo = WilokePublic::$oUserInfo;
	$isViewByMySelf = true;
}
global $wiloke;

if ( is_user_logged_in() && !isset($_REQUEST['user']) || ($_REQUEST['user'] == get_current_user_id()) ){
	include get_template_directory_uri() . '/wiloke-submission/account/user-dashboard.php';
}else{
    include get_template_directory_uri() . '/wiloke-submission/account/guest-dashboard.php';
}
