<?php
add_action( 'wp_enqueue_scripts', 'wilokeListGoChildEnqueueScripts', 90 );
function wilokeListGoChildEnqueueScripts() {
	wp_enqueue_style( 'listgo-child',get_stylesheet_directory_uri() . '/style.css');
//	wp_enqueue_script('listgo-child', get_stylesheet_directory_uri() . '/script.js', array('jquery'), null, true); uncomment to enqueue script.js
}
