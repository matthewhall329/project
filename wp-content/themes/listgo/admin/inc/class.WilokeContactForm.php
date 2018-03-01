<?php
/**
 * WilokeContactForm Class
 *
 * @category Helper
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
    exit;
}

class WilokeContactForm
{
    public function __construct()
    {
        add_action('wpcf7_admin_footer', array($this, 'wiloke_contactfom7_helper'));
        add_action('admin_enqueue_scripts', array($this, 'wiloke_enqueue_scripts'));
    }

    public function wiloke_enqueue_scripts()
    {
        wp_enqueue_style('wiloke_contactform7', WILOKE_AD_SOURCE_URI . 'css/contactform7.css', array(), null);
        wp_enqueue_script('wiloke_contactform7', WILOKE_AD_SOURCE_URI . 'js/contactform7.js', array('jquery'), null, true);
    }

    public function wiloke_contactfom7_helper()
    {
        global $wiloke;

        ?>
        <div id="wilokecontactform7" class="contactform7">
            <div class="postbox-container">
                <div class="postbox">
                    <h2 class="hndle"><?php esc_html_e('Contact form 7 Option', 'listgo'); ?></h2>
                    <div class="inside">
                        <p><?php esc_html_e('Click Import button make this contact form look like demo.', 'listgo'); ?></p>
                        <?php 
                            $cssClass = count($wiloke->aConfigs['contactform7']) > 1 ? '' : 'hidden';
                           
                            echo '<select class="'.esc_attr($cssClass).'" name="contactform7">';
                                foreach ( $wiloke->aConfigs['contactform7'] as $key => $val )
                                {
                                    echo '<option value="'.$key.'">'.esc_html($key).'</option>';
                                }
                            echo '</select>';
                        ?>
                        <button id="wiloke-import-contactform7" class="button button-primary"><?php esc_html__('Import', 'listgo'); ?><?php esc_html_e('Import', 'listgo'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}