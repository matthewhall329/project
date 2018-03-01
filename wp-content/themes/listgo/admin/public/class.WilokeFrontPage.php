<?php
/**
 * WO_FrontEnd Class
 *
 * @category Front end
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
    exit;
}

class WilokeFrontPage
{
	public $mainStyle = '';
	public $minifyStyle = 'wiloke_minify_theme_css';
    public function __construct()
    {
        add_action('wp_print_scripts', array($this, 'dequeue_scripts'), 20 );
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueCustomScripts'), 99999);
    }

    public function dequeue_scripts()
    {
        wp_dequeue_script('isotope');
        wp_dequeue_script('isotope-css');
    }

    public static function fonts_url($fonts)
    {
        $font_url = '';

        /*
        Translators: If there are characters in your language that are not supported
        by chosen font(s), translate this to 'off'. Do not translate into your own language.
         */
        if ( 'off' !== _x( 'on', 'Google font: on or off', 'listgo' ) ) {
            $font_url = add_query_arg( 'family', urlencode( $fonts ), "//fonts.googleapis.com/css" );
        }
        return $font_url;
    }

	public static function hex2rgb( $colour ) {
		if ( $colour[0] == '#' ) {
			$colour = substr( $colour, 1 );
		}
		if ( strlen( $colour ) == 6 ) {
			list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
		} elseif ( strlen( $colour ) == 3 ) {
			list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
		} else {
			return false;
		}
		$r = hexdec( $r );
		$g = hexdec( $g );
		$b = hexdec( $b );
		return array( 'red' => $r, 'green' => $g, 'blue' => $b );
	}

    /**
     * Enqueue scripts into front end
     */
    public function enqueue_scripts()
    {
        global $wiloke, $post;

        $themeSlug = 'wiloke_'.$wiloke->aConfigs['general']['theme_slug'];

        do_action('wiloke_action_before_enqueue_scripts');

        if ( isset($wiloke->aConfigs['frontend']) && isset($wiloke->aConfigs['frontend']['scripts']) )
        {
            if ( is_singular() )
            {
                wp_enqueue_script('comment-reply');
            }

            $aScripts = $wiloke->aConfigs['frontend']['scripts'];

            foreach ( $aScripts as $key => $aVal )
            {
                $isGoodConditional = true;
                if ( !wp_script_is($key, 'enqueued') )
                {
                    if ( isset($aVal['conditional']) && function_exists($aVal['conditional']) )
                    {
                        if ( call_user_func($aVal['conditional'], $key) )
                        {
                            $isGoodConditional = true;
                        }else{
                            $isGoodConditional = false;
                        }
                    }

                    if ( $isGoodConditional ) {
                        if (isset($aVal['is_url']) && $aVal['is_url'] === true) {
                            if ($aVal[0] == 'css') {
                                $aVal['required'] = !isset($aVal['required']) ? array() : $aVal['required'];
                                wp_enqueue_style($key, $aVal[1], $aVal['required'], WILOKE_THEMEVERSION);
                            } else {
                                $aVal['required'] = !isset($aVal['required']) ? array('jquery') : $aVal['required'];
                                if ( $aVal['is_google_map'] ){
                                    wp_enqueue_script('googlemap', esc_url('https://maps.googleapis.com/maps/api/js?key='.$wiloke->aThemeOptions['general_map_api']).'&libraries=places');
                                }else{
                                    wp_enqueue_script($key, $aVal[1], $aVal['required'], WILOKE_THEMEVERSION, true);
                                }
                            }
                        }elseif(isset($aVal['is_wp_store']) && $aVal['is_wp_store']){
	                        if ($aVal[0] == 'css') {
	                        	wp_enqueue_style($aVal[1]);
	                        }else{
		                        wp_enqueue_script($aVal[1]);
	                        }
                        }elseif(isset($aVal['is_googlefont']) && $aVal['is_googlefont'] === true) {
                            wp_enqueue_style($key, self::fonts_url($aVal[0]));
                        }elseif ($aVal[0] === 'is_custom_css'){
                            if ( isset($wiloke->aThemeOptions[$aVal[2]]) && !empty($wiloke->aThemeOptions[$aVal[2]]) )
                            {
                                wp_add_inline_style($aVal[1], $wiloke->aThemeOptions[$aVal[2]]);
                            }
                        }elseif ($aVal[0] === 'is_custom_js'){
                            if ( isset($wiloke->aThemeOptions[$aVal[2]]) && !empty($wiloke->aThemeOptions[$aVal[2]]) )
                            {
                                if ( function_exists('wp_add_inline_script') )
                                {
                                    wp_add_inline_script($aVal[1], $wiloke->aThemeOptions[$aVal[2]]);
                                }
                            }
                        }else{
                            $concat = '/lib/';

                            if ( isset($aVal['default']) &&  $aVal['default'] === true )
                            {
                                $concat = '/';
                            }

                            if ( $aVal[0] == 'both' || $aVal[0] == 'css' )
                            {
                                $aVal['required'] = !isset($aVal['required']) ? array() : $aVal['required'];
                                $this->mainStyle = $key;

	                            if ( isset($aVal['is_register_only']) ){
		                            wp_register_style($key, WILOKE_THEME_URI . 'css' . $concat . $aVal[1].'css', $aVal['required'], WILOKE_THEMEVERSION);
	                            }else{
		                            wp_enqueue_style($key, WILOKE_THEME_URI . 'css' . $concat . $aVal[1].'css', $aVal['required'], WILOKE_THEMEVERSION);
	                            }
                            }

                            if ( $aVal[0] == 'both' || $aVal[0] == 'js'  )
                            {
                                $aVal['required'] = !isset($aVal['required']) ? array('jquery') : $aVal['required'];
                                if ( isset($aVal['is_register_only']) ){
	                                wp_register_script($key, WILOKE_THEME_URI . 'js' . $concat . $aVal[1].'js', $aVal['required'], WILOKE_THEMEVERSION, true);
                                }else{

	                                wp_enqueue_script($key, WILOKE_THEME_URI . 'js' . $concat . $aVal[1].'js', $aVal['required'], WILOKE_THEMEVERSION, true);
                                }
                            }
                        }
                    }
                }
            }
        }

	    wp_register_script('timekit', '//cdn.timekit.io/booking-js/v1/booking.min.js', array('jquery'), WILOKE_THEMEVERSION, true);
	    wp_register_script('listgo-timekit', get_template_directory_uri() . '/js/listgo-timekit.js', array('timekit'), WILOKE_THEMEVERSION, true);

	    wp_enqueue_style($themeSlug, get_stylesheet_uri(), array(), WILOKE_THEMEVERSION );
    }

    public function enqueueCustomScripts(){
    	global $post, $wiloke;
        $customColor = '';
        $colorFile = 'default';
	    $themeSlug = 'wiloke_'.$wiloke->aConfigs['general']['theme_slug'];

	    if ( isset($post->ID) && ($post->post_type == 'page') ){
		    $aPageSettings = Wiloke::getPostMetaCaching($post->ID, 'page_settings');
		    $colorFile = isset($aPageSettings['page_color']) && ($aPageSettings['page_color'] !== 'default') ? $aPageSettings['page_color'] : 'default';
		    if ( $colorFile === 'custom' ){
			    $customColor = $aPageSettings['custom_page_color'];
		    }

	    }
        if ( $colorFile === 'default' ) {
            $colorFile = isset($wiloke->aThemeOptions['advanced_main_color']) && !empty($wiloke->aThemeOptions['advanced_main_color']) ? $wiloke->aThemeOptions['advanced_main_color'] : 'default';

            if ( $colorFile === 'custom' ){
                $customColor = $wiloke->aThemeOptions['advanced_custom_main_color']['rgba'];
            }
        }

	    if ( $colorFile !== 'default' ) {
		    if ( is_file(get_template_directory() . '/css/color/'.$colorFile.'.css') ) {
			    wp_enqueue_style('customizemaincolor', get_template_directory_uri() . '/css/color/'.$colorFile.'.css', array(), WILOKE_THEMEVERSION );
		    } elseif ( !empty($customColor) ) {
			    ob_start();
			    include get_template_directory() . '/css/color/custom_color.css';
			    $color = ob_get_clean();
			    $color = str_replace('#f5af02', $customColor, $color);
                $customColor = str_replace(')', '', $customColor);
                $customColor = str_replace('rgb', 'rgba', $customColor);
			    $color = str_replace('rgba(245, 175, 2', $customColor, $color);
			    wp_add_inline_style($this->mainStyle, $color);
			    wp_add_inline_style($this->minifyStyle, $color);
		    }
	    }
	    if ( isset($wiloke->aThemeOptions['advanced_css_code']) && !empty($wiloke->aThemeOptions['advanced_css_code']) )
	    {
            wp_add_inline_style($themeSlug, $wiloke->aThemeOptions['advanced_css_code']);
		    wp_add_inline_style($this->minifyStyle, $wiloke->aThemeOptions['advanced_css_code']);
	    }

	    if ( isset($wiloke->aThemeOptions['advanced_js_code']) && !empty($wiloke->aThemeOptions['advanced_js_code']) )
	    {
		    wp_add_inline_script('jquery-migrate', $wiloke->aThemeOptions['advanced_js_code']);
	    }

	    if ( $wiloke->aThemeOptions['advanced_google_fonts'] == 'general' )
	    {
		    if ( !empty($wiloke->aThemeOptions['advanced_general_google_fonts']) )
		    {
			    $aParseFont = explode('css?family=', $wiloke->aThemeOptions['advanced_general_google_fonts']);

			    wp_enqueue_style('wiloke_general_google_fonts', self::fonts_url($aParseFont[1]));

			    ob_start();
			    include WILOKE_THEME_DIR . 'css/ggfont-general-custom.css';
			    $font = ob_get_clean();
			    $font = str_replace('#googlefont_general', $wiloke->aThemeOptions['advanced_general_google_fonts_css_rules'], $font);
			    wp_add_inline_style('wiloke_general_google_fonts', $font);
		    }
	    }

	    do_action('wiloke_action_after_enqueue_scripts');
    }
}