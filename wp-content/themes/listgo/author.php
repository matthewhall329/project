<?php
/**
 * The template displays author profile
 *
 * @link https://wiloke.com
 *
 * @package Wiloke/Themes
 * @subpackage Listgo
 * @since 1.0
 * @version 1.0
 */

if ( !is_plugin_active('wiloke-listgo-functionality/wiloke-listgo-functionality.php') || ( isset($_REQUEST['target']) && ($_REQUEST['target'] == 'blog') ) ){
	get_template_part('index');
}else{
	get_template_part('templates/listing');
}
