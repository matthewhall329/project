<?php
/**
 * Visual Wiloke Shortcodes
 *
 * @category Shortcodes
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
    exit;
}

function wiloke_include_shortcodes($shortcodeName)
{
    global $wiloke;

    $fileName = $shortcodeName . '.php';

    try {
        if( $wiloke->isFileExists(WILOKE_TPL_BUILDER.'shortcodes/', $fileName) )
        {
            include WILOKE_TPL_BUILDER.'shortcodes/'.$fileName;
            $callback = 'wiloke_shortcode' . str_replace( array('wiloke', '.php'), array('', ''), $fileName);
            
            $wilokeAddShortCode = 'add_';
            $wilokeAddShortCode = $wilokeAddShortCode . 'shortcode';

            $wilokeAddShortCode($shortcodeName, $callback);
        }
    }catch (Exception $e)
    {
        WilokeAlert::alert($e->getMessage(), 'error');
    }
}

if ( !function_exists('wiloke_shortcodes_init') )
{
    function wiloke_shortcodes_init()
    {
        global $wiloke;

        if ( isset($wiloke->aConfigs['shortcodes']) )
        {
            foreach ( $wiloke->aConfigs['shortcodes'] as $shortcode )
            {
                wiloke_include_shortcodes($shortcode);
            }
        }
    }

    wiloke_shortcodes_init();
}
