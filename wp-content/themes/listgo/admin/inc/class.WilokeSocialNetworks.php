<?php
/**
 * WilokeSocialNetworks Class
 *
 * @category General
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
    exit;
}

class WilokeSocialNetworks
{
    static public $aSocialNetworks = array(
        'facebook', 'twitter', 'google-plus', 'tumblr', 'vk', 'odnoklassniki', 'youtube', 'vimeo', 'rutube', 'dribbble', 'instagram', 'flickr', 'pinterest', 'medium', 'tripadvisor', 'wikipedia', 'stumbleupon', 'livejournal', 'linkedin', 'skype', 'bloglovin', 'whatsapp'
    );

    static public function render_setting_field()
    {
        $aSocials = array();

        foreach ( self::$aSocialNetworks as $key )
        {
            if ( $key == 'google-plus' )
            {
                $socialName = 'Google+';
            }else{
                $socialName = ucfirst($key);
            }
            $key = 'social_network_'.$key;

            $aSocials[] = array(
                'id'       => $key,
                'type'     => 'text',
                'title'    => $socialName,
                'subtitle' => esc_html__( 'Social icon will not display if you leave empty', 'listgo'),
                'default'  => ''
            );
        }

        return $aSocials;
    }

    static public function render_socials($aData, $separated='')
    {
        if ( empty($aData) ) {
            return;
        }

        ob_start();
        foreach ( self::$aSocialNetworks as $key )
        {
            $socialIcon = 'fa fa-'.str_replace('_', '-', $key);

            $key = 'social_network_'.$key;
            if ( isset($aData[$key]) && !empty($aData[$key]) )
            {
                $separated = isset($last) && $last == $key ? '' : $separated;

                do_action('wiloke_hook_before_render_social_network');
                if ( has_filter('wiloke_filter_social_network') )
                {
                    echo apply_filters('wiloke_filter_social_network', $aData[$key], $socialIcon, $separated);
                }else {
                    ?>
                    <a href="<?php echo esc_url($aData[$key]); ?>" target="_blank"><i class="<?php echo esc_attr($socialIcon); ?>"></i></a><?php echo esc_html($separated); ?>
                    <?php
                }
                do_action('wiloke_hook_after_render_social_network');
            }
        }
        $content = ob_get_contents();
        ob_end_clean();
        $content = rtrim($content, $separated);
        Wiloke::wiloke_kses_simple_html($content);
    }
}
