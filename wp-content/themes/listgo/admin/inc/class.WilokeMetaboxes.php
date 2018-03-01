<?php

/**
 * WilokeMetaboxes
 * Custom Metabox
 *
 * We want to say thank you so much to WebDevStudios
 *
 * This category extended from their plugin
 *
 * @category Meta box
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
    die();
}

class WilokeMetaboxes
{
    public function __construct()
    {
        add_action( 'init', array($this, 'includes') );
        // add_action( 'edit_form_after_title', array($this, 'tweek_run_after_title_meta_boxes') );
        add_filter( 'cmb_meta_boxes', array($this, 'render') );
    }

    function tweek_run_after_title_meta_boxes() {
        global $post, $wp_meta_boxes;
        # Output the `below_title` meta boxes:
        do_meta_boxes( get_current_screen(), 'after_title', $post );
    }

    /**
     * Get post meta in single post
     * @since 1.0
     */
    public static function getPostMeta($key='')
    {
        global $post;

        if ( empty($post) )
        {
            return false;
        }

        if ( !empty($key) )
        {
            return get_post_meta($post->ID, $key, true);
        }else{
            if ( isset($post->post_type) && $post->post_type == 'post')
            {
                return get_post_meta($post->ID, 'single_post_settings', true);
            }elseif ( function_exists('is_woocommerce') )
            {
                if ( is_shop() )
                {
                    return get_post_meta(get_option('woocommerce_shop_page_id'), 'wiloke_page', true);
                }
            }

            return get_post_meta($post->ID, 'wiloke_page', true);
        }
    }

    /**
     * Register and render meta boxes
     */
    public function render($aMetaBoxes)
    {
        global $wiloke;
        $aMetaBoxes = $this->getMetaboxes();
        if ( !empty($aMetaBoxes) )
        {
            $aMetaBoxes = $this->replaceWithMetaboxKey($aMetaBoxes);
        }

        if ( isset($wiloke->aConfigs['metaboxes']) )
        {
            $aMetaBoxes = !empty($aMetaBoxes) ? array_merge($wiloke->aConfigs['metaboxes'], $aMetaBoxes) : $wiloke->aConfigs['metaboxes'];
        }

        $aMetaBoxes = apply_filters('wiloke_filter_metaboxes', $aMetaBoxes, $wiloke);

        return $aMetaBoxes;
    }

    public function replaceWithMetaboxKey($aMetaBoxes)
    {
        $aNewMetaBoxes = array();

        foreach ( $aMetaBoxes as $pKey => $aMetaBox )
        {
            $aMetaBox['title'] = isset($aMetaBox['metabox_title']) ? $aMetaBox['metabox_title'] : $aMetaBox['title'];
            $aMetaBox['id'] = isset($aMetaBox['metabox_id']) ? $aMetaBox['metabox_id'] : $aMetaBox['id'];
            $aNewMetaBoxes[$aMetaBox['id']] = $aMetaBox;

            foreach ( $aMetaBox['fields'] as $key => $aField )
            {
                if ( isset($aField['metabox_subtitle']) ) {
                    $aNewMetaBoxes[$aMetaBox['id']]['fields'][$key]['description'] = $aField['metabox_subtitle'];
                }elseif ( isset($aField['subtitle']) ) {
                    $aNewMetaBoxes[$aMetaBox['id']]['fields'][$key]['description'] = $aField['subtitle'];
                }

                $aNewMetaBoxes[$aMetaBox['id']]['fields'][$key]['name'] = isset($aField['metabox_title']) ? $aField['metabox_title'] : $aField['title'];

                if ( isset($aField['required']) )
                {
                   $aNewMetaBoxes[$aMetaBox['id']]['fields'][$key]['depedency']   = $aField['required'];
                }

                if ( $aField['type'] == 'media' || $aField['type'] == 'image_select' )
                {
                    $aNewMetaBoxes[$aMetaBox['id']]['fields'][$key]['type'] = 'file';
                    if ( isset($aField['default']['url']) )
                    {
                        $aNewMetaBoxes[$aMetaBox['id']]['fields'][$key]['default'] = $aField['default']['url'];
                    }
                }elseif ( $aField['type'] == 'select'  ) {
                    $aNewMetaBoxes[$aMetaBox['id']]['fields'][$key]['options']['inherit'] = esc_html__('Inherit Theme Options', 'listgo');
                    $aNewMetaBoxes[$aMetaBox['id']]['fields'][$key]['default'] = 'inherit';
                }elseif ( $aField['type'] == 'color_rgba' ) {
                    $aNewMetaBoxes[$aMetaBox['id']]['fields'][$key]['type'] = 'colorpicker';
                }

                if ( isset($aField['required']) ) {
                    $aNewMetaBoxes[$aMetaBox['id']]['fields'][$key]['dependency'] = $aField['required'];
                }

                unset($aNewMetaBoxes[$aMetaBox['id']]['fields'][$key]['title']);
                unset($aNewMetaBoxes[$aMetaBox['id']]['fields'][$key]['required']);
                unset($aNewMetaBoxes[$aMetaBox['id']]['fields'][$key]['subtitle']);
            }

            if ( !isset($aMetaBox['context']) ) {
                $aNewMetaBoxes['context'] = 'normal';
            }else{
                $aNewMetaBoxes['context'] = $aMetaBox['context'];
            }

            if ( !isset($aMetaBox['priority']) ) {
                $aNewMetaBoxes['priority'] = 'low';
            }else{
                $aNewMetaBoxes['priority'] = $aMetaBox['priority'];
            }

            if ( isset($aMetaBox['metabox_order']) ) {
                $aNewMetaBoxes['metabox_order'] = $aMetaBox['metabox_order'];
            }
        }

        $aNewMetaBoxes['show_names']              = true;
        $aNewMetaBoxes['show_settings_on_create'] = true;

        $aNewMetaBoxes = apply_filters('wiloke/admin/inc/metaboxes/filter_metabox_items', $aNewMetaBoxes, $aMetaBox['id']);

        return $aNewMetaBoxes;
    }

    public function getMetaboxes()
    {
        global $wiloke;
        return $this->filterPostMetaInThemeOptions($wiloke->aConfigs['themeoptions']['redux']['sections']);
    }

    public function filterPostMetaInThemeOptions($aSections)
    {
        $aMetaBoxes = array();

        foreach ( $aSections as $aSection )
        {
            if ( isset($aSection['with_meta_box']) )
            {
                $aMetaBoxes[] = $aSection;
            }else{
                $aMetaBoxFields = array();

                foreach ( $aSection['fields'] as $aField )
                {
                    if ( isset($aField['with_meta_box']) )
                    {
                        $aMetaBoxFields[] = $aField;
                    }
                }

                if ( !empty($aMetaBoxFields) )
                {
                    unset($aSection['fields']);
                    $aSection['fields'] = $aMetaBoxFields;
                    $aMetaBoxes[$aSection['id']] = $aSection;
                }

            }
        }

        return  $aMetaBoxes;
    }

    /**
     * Include external lib here
     */
    public function includes()
    {
        global $wiloke;
        if ( $wiloke->isFileExists(WILOKE_INC_DIR . 'lib/custom-metaboxes/', 'init.php') && !class_exists('cmb_Meta_Box') )
        {
            include WILOKE_INC_DIR . 'lib/custom-metaboxes/init.php';
        }
    }
}
