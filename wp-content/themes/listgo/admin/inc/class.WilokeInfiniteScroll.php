<?php
/**
 * WilokeInfiniteScroll Class
 *
 * @category plugins
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
    exit;
}

class WilokeInfiniteScroll
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 69);
    }

    public function enqueue_scripts()
    {
        $min = !defined('SCRIPT_DEBUG') ? '.min.' : '.';

        wp_enqueue_script( 'waypoint-inifite-inview', Wiloke::$public_url . 'lib/js/jquery.waypoint-inview-infinite.min.js', array('jquery'), '1.0', true );
        wp_enqueue_script( 'wilokeinfinitescroll', Wiloke::$public_url .'source/js/wiloke.infinite-scroll'.$min.'js', array('jquery', 'waypoint-inifite-inview'), '1.0', true );
    }

    static public function render_nav_filter($aTerms=array(), $atts=array())
    {
        do_action('wiloke_hook_before_render_infinite_scroll_nav_filter', $aTerms, $atts);
            if ( !has_filter('wiloke_filter_before_render_infinite_scroll_nav_filter') )
            {

            }else{
                echo apply_filters('wiloke_filter_before_render_infinite_scroll_nav_filter', $aTerms, $atts);
            }
        do_action('wiloke_hook_after_render_infinite_scroll_nav_filter', $aTerms, $atts);
    }

    static public function render_item($postID, $atts=array())
    {
        echo apply_filters('wiloke_filter_render_infinite_scroll_item', $postID, $atts);
    }
}