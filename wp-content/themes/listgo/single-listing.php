<?php
/**
 * The template for displaying all single posts
 *
 * @link https://wiloke.com
 *
 * @package Wiloke/Themes
 * @subpackage Listgo
 * @since 1.0
 * @version 1.0
 */
global $wiloke;
if ( strpos($wiloke->aThemeOptions['listing_layout'], 'templates') === false ){
	get_template_part('templates/single-listing-creative');
}else{
	$wiloke->aThemeOptions['listing_layout'] = isset($wiloke->aThemeOptions['listing_layout']) ? $wiloke->aThemeOptions['listing_layout'] : 'templates/single-listing-creative';
	$wiloke->aThemeOptions['listing_layout'] = str_replace('.php', '', $wiloke->aThemeOptions['listing_layout']);
	get_template_part($wiloke->aThemeOptions['listing_layout']);
}