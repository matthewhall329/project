<?php
/**
 * WilokeReduxExtensions Class
 *
 * @category Widget
 * @package Wiloke Framework
 * @author Wiloke
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
    exit;
}

if ( !class_exists('Redux') )
{
    return false;
}

class WilokeReduxExtensions
{
    public $optionName;

    public function __construct()
    {
        $this->set_option_name();
        add_action("redux/extensions/before", array($this, 'redux_register_custom_extension_loader'), 0);
    }

    public function redux_register_custom_extension_loader($ReduxFramework)
    {
        global $wiloke;

        if ( !isset($wiloke->aConfigs['general']['redux_extensions']) ) {
            return;
        }

        foreach ( $wiloke->aConfigs['general']['redux_extensions'] as $folder => $className )
        {
            $className = 'ReduxFramework_Extension_'.$className;

            if ( !class_exists( $className ) )
            {
                // In case you wanted override your override, hah.
                $class_file = WILOKE_INC_DIR . 'redux-extensions/' . str_replace('_', '-', $folder) . '/class.' . $className . '.php';
                if ( $class_file )
                {
                    require_once( $class_file );
                }
            }

            if ( !isset( $ReduxFramework->extensions[$folder] ) )
            {
                $ReduxFramework->extensions[$folder] = new $className( $ReduxFramework );
            }

        }

    }

    public function set_option_name()
    {
        global $wiloke;
        $this->optionName = $wiloke->aConfigs['themeoptions']['redux']['args']['opt_name'];
    }
}

