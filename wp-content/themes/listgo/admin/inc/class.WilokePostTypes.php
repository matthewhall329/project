<?php
/**
 * WilokePostTypes Class
 *
 * @category Post
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
    exit;
}

class WilokePostTypes
{
    public function __construct()
    {
        add_action('init', array($this, 'register_post_types'));
    }

    public function register_post_types()
    {

    }
}