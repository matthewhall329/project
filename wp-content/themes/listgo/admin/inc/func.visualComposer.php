<?php
/**
 * Visual Composer Function
 *
 * @category Shortcodes
 * @package Wiloke Framework
 * @author Wiloke
 * @version 1.0
 */

if ( !defined('ABSPATH') )
{
    exit;
}

if ( !function_exists('vc_map') ){
    return;
}


/**
 * Visual Map
 * @param $args - Array
 */
function wiloke_vc_map($args)
{
    vc_map(
        $args
    );

    $args['base'] = explode("_", $args['base']);

    $className = '';

    foreach ( $args['base'] as $name )
    {
        $className .= ucfirst($name) . '_';
    }
}

/**
 * Register Visual Shortcode
 * @params $shortcodeName
 * $callback - the callback function be contained in template-builder->vc folder.
 */
function wiloke_vc_add_sc($shortcodeName, $aAtts)
{
    global $wiloke;

    $fileName = $shortcodeName . '.php';

    try {
        if ( strpos($aAtts['base'], 'wiloke_') !== false )
        {

            if ( $wiloke->isFileExists(get_stylesheet_directory().'/template/vc/', $fileName) ){
                include get_stylesheet_directory().'/template/vc/' . $fileName;
                $callback = 'wiloke_shortcode' . str_replace( array('wiloke', '.php'), array('', ''), $fileName);
                $wilokeAddShortCode = 'add_';
                $wilokeAddShortCode = $wilokeAddShortCode . 'shortcode';
                $wilokeAddShortCode($shortcodeName, $callback);
            }elseif( $wiloke->isFileExists(Wiloke::$public_path.'template/vc/', $fileName) )
            {
                include Wiloke::$public_path.'template/vc/' . $fileName;
                $callback = 'wiloke_shortcode' . str_replace( array('wiloke', '.php'), array('', ''), $fileName);
                $wilokeAddShortCode = 'add_';
                $wilokeAddShortCode = $wilokeAddShortCode . 'shortcode';
                $wilokeAddShortCode($shortcodeName, $callback);
            }
        }else{
            if( function_exists( 'vc_set_shortcodes_templates_dir' ) )
            {
	            vc_set_shortcodes_templates_dir( Wiloke::$public_path.'template/vc/' );
            }
        }
    }catch (Exception $e)
    {
        Wiloke::$list_of_errors['error'][] = $e->getMessage();
    }
}


function wiloke_vc_init()
{
    global $wiloke;

    $wiloke->aConfigs['vc']['shortcodes'] = apply_filters('wiloke/vc/filter_shortcode_configuration', $wiloke->aConfigs['vc']['shortcodes']);

    if ( isset($wiloke->aConfigs['vc']['shortcodes']) )
    {
        foreach ( $wiloke->aConfigs['vc']['shortcodes'] as $aVcMap )
        {
            if ( isset($aVcMap['has_autocomplete']) && $aVcMap['has_autocomplete'] ) {

                add_filter( 'vc_autocomplete_'.$aVcMap['base'].'_include_callback', 'wiloke_include_field_search', 10, 3 );
                add_filter( 'vc_autocomplete_'.$aVcMap['base'].'_include_render', 'wiloke_include_field_render', 10, 1 );

//                add_filter( 'vc_autocomplete_'.$aVcMap['base'].'_taxonomies_callback', 'wiloke_autocomplete_taxonomies_field_search', 10, 1 );
//                add_filter( 'vc_autocomplete_'.$aVcMap['base'].'_taxonomies_render', 'wiloke_autocomplete_taxonomies_field_render', 10, 1 );
//
//                add_filter( 'vc_autocomplete_'.$aVcMap['base'].'_exclude_filter_callback', 'wiloke_autocomplete_taxonomies_field_search', 10, 1 );
//                add_filter( 'vc_autocomplete_'.$aVcMap['base'].'_exclude_filter_render', 'wiloke_autocomplete_taxonomies_field_render', 10, 1 );
//
//                add_filter( 'vc_autocomplete_'.$aVcMap['base'].'_exclude_callback', 'vc_exclude_field_search', 10, 1 );
//                add_filter( 'vc_autocomplete_'.$aVcMap['base'].'_exclude_render', 'vc_exclude_field_render', 10, 1 );
            }

            $aVcMap['params'][] = array(
                'type'       => 'textfield',
                'heading'    => esc_html__('Extract Class', 'listgo'),
                'param_name' => 'extract_class',
                'value'      => '',
                'std'        => ''
            );

            if ( !isset($aVcMap['is_remove_css_editor']) || !$aVcMap['is_remove_css_editor'] )
            {
                $aVcMap['params'][] = array(
                    'type'          => 'css_editor',
                    'heading'       => esc_html__('Css', 'listgo'),
                    'param_name'    => 'css',
                    'group'         => esc_html__('Design Options', 'listgo'),
                );
            }

            if ( $aVcMap['base'] === 'wiloke_design_portfolio' ) {
                $aVcMap['params'][] = array(
                    'type'          => 'textfield',
                    'heading'       => esc_html__('General Settings', 'listgo'),
                    'param_name'    => 'general_settings',
                    'group'         => esc_html__('Design Options', 'listgo'),
                );
            }

            wiloke_vc_map($aVcMap);

	        wiloke_vc_add_sc($aVcMap['base'], $aVcMap);

        }
    }
}

add_action('init', 'wiloke_vc_init');

/**
 * @param $search_string
 *
 * @return array
 */
function wiloke_include_field_search( $search_string, $tag, $aParam ) {
	$query = $search_string;
	$data = array();
	$args = array(
		's' => $query,
		'post_type' => 'any',
	);
	$args['vc_search_by_title_only'] = true;
	$args['numberposts'] = - 1;
	if ( 0 === strlen( $args['s'] ) ) {
		unset( $args['s'] );
	}

	$posts = get_posts( $args );
	if ( is_array( $posts ) && ! empty( $posts ) ) {
		foreach ( $posts as $post ) {
			$data[] = array(
				'value' => $post->ID,
				'label' => $post->post_title,
				'group' => $post->post_type,
			);
		}
	}

	return $data;
}

/**
 * @param $value
 *
 * @return array|bool
 */
function wiloke_include_field_render( $value ) {
	$post = get_post( $value['value'] );

	return is_null( $post ) ? false : array(
		'label' => $post->post_title,
		'value' => $post->ID,
		'group' => $post->post_type,
	);
}

/**
 * @param $data_arr
 *
 * @return array
 */
function wiloke_exclude_field_search( $data_arr ) {
	$query = isset( $data_arr['query'] ) ? $data_arr['query'] : null;
	$term = isset( $data_arr['term'] ) ? $data_arr['term'] : '';
	$data = array();
	$args = ! empty( $query ) ? array(
		's' => $term,
		'post_type' => $query,
	) : array(
		's' => $term,
		'post_type' => 'any',
	);
	$args['vc_search_by_title_only'] = true;
	$args['numberposts'] = - 1;
	if ( 0 === strlen( $args['s'] ) ) {
		unset( $args['s'] );
	}
	add_filter( 'posts_search', 'vc_search_by_title_only', 500, 2 );
	$posts = get_posts( $args );
	if ( is_array( $posts ) && ! empty( $posts ) ) {
		foreach ( $posts as $post ) {
			$data[] = array(
				'value' => $post->ID,
				'label' => $post->post_title,
				'group' => $post->post_type,
			);
		}
	}

	return $data;
}

/**
 * @param $value
 *
 * @return array|bool
 */
function wiloke_exclude_field_render( $value ) {
	$post = get_post( $value['value'] );

	return is_null( $post ) ? false : array(
		'label' => $post->post_title,
		'value' => $post->ID,
		'group' => $post->post_type,
	);
}

/**
 * Register Icon Param
 */

if ( !function_exists('wiloke_elegant_icons_param_settings') )
{
    function wiloke_elegant_icons_param_settings($settings, $value)
    {
        $param_name = isset($settings['param_name']) ? $settings['param_name'] : '';
        $type       = isset($settings['type']) ? $settings['type'] : '';
        $class      = isset($settings['class']) ? $settings['class'] : '';
        $icons 		= array("arrow_up", "arrow_down", "arrow_left", "arrow_right", "arrow_left-up", "arrow_right-up", "arrow_right-down", "arrow_left-down", "arrow-up-down", "arrow_up-down_alt", "arrow_left-right_alt", "arrow_left-right", "arrow_expand_alt2", "arrow_expand_alt", "arrow_condense", "arrow_expand", "arrow_move", "arrow_carrot-up", "arrow_carrot-down", "arrow_carrot-left", "arrow_carrot-right", "arrow_carrot-2up", "arrow_carrot-2down", "arrow_carrot-2left", "arrow_carrot-2right", "arrow_carrot-up_alt2", "arrow_carrot-down_alt2", "arrow_carrot-left_alt2", "arrow_carrot-right_alt2", "arrow_carrot-2up_alt2", "arrow_carrot-2down_alt2", "arrow_carrot-2left_alt2", "arrow_carrot-2right_alt2", "arrow_triangle-up", "arrow_triangle-down", "arrow_triangle-left", "arrow_triangle-right", "arrow_triangle-up_alt2", "arrow_triangle-down_alt2", "arrow_triangle-left_alt2", "arrow_triangle-right_alt2", "arrow_back", "icon_minus-06", "icon_plus", "icon_close", "icon_check", "icon_minus_alt2", "icon_plus_alt2", "icon_close_alt2", "icon_check_alt2", "icon_zoom-out_alt", "icon_zoom-in_alt", "icon_search", "icon_box-empty", "icon_box-selected", "icon_minus-box", "icon_plus-box", "icon_box-checked", "icon_circle-empty", "icon_circle-slelected", "icon_stop_alt2", "icon_stop", "icon_pause_alt2", "icon_pause", "icon_menu", "icon_menu-square_alt2", "icon_menu-circle_alt2", "icon_ul", "icon_ol", "icon_adjust-horiz", "icon_adjust-vert", "icon_document_alt", "icon_documents_alt", "icon_pencil", "icon_pencil-edit_alt", "icon_pencil-edit", "icon_folder-alt", "icon_folder-open_alt", "icon_folder-add_alt", "icon_info_alt", "icon_error-oct_alt", "icon_error-circle_alt", "icon_error-triangle_alt", "icon_question_alt2", "icon_question", "icon_comment_alt", "icon_chat_alt", "icon_vol-mute_alt", "icon_volume-low_alt", "icon_volume-high_alt", "icon_quotations", "icon_quotations_alt2", "icon_clock_alt", "icon_lock_alt", "icon_lock-open_alt", "icon_key_alt", "icon_cloud_alt", "icon_cloud-upload_alt", "icon_cloud-download_alt", "icon_image", "icon_images", "icon_lightbulb_alt", "icon_gift_alt", "icon_house_alt", "icon_genius", "icon_mobile", "icon_tablet", "icon_laptop", "icon_desktop", "icon_camera_alt", "icon_mail_alt", "icon_cone_alt", "icon_ribbon_alt", "icon_bag_alt", "icon_creditcard", "icon_cart_alt", "icon_paperclip", "icon_tag_alt", "icon_tags_alt", "icon_trash_alt", "icon_cursor_alt", "icon_mic_alt", "icon_compass_alt", "icon_pin_alt", "icon_pushpin_alt", "icon_map_alt", "icon_drawer_alt", "icon_toolbox_alt", "icon_book_alt", "icon_calendar", "icon_film", "icon_table", "icon_contacts_alt", "icon_headphones", "icon_lifesaver", "icon_piechart", "icon_refresh", "icon_link_alt", "icon_link", "icon_loading", "icon_blocked", "icon_archive_alt", "icon_heart_alt", "icon_star_alt", "icon_star-half_alt", "icon_star", "icon_star-half", "icon_tools", "icon_tool", "icon_cog", "icon_cogs", "arrow_up_alt", "arrow_down_alt", "arrow_left_alt", "arrow_right_alt", "arrow_left-up_alt", "arrow_right-up_alt", "arrow_right-down_alt", "arrow_left-down_alt", "arrow_condense_alt", "arrow_expand_alt3", "arrow_carrot_up_alt", "arrow_carrot-down_alt", "arrow_carrot-left_alt", "arrow_carrot-right_alt", "arrow_carrot-2up_alt", "arrow_carrot-2dwnn_alt", "arrow_carrot-2left_alt", "arrow_carrot-2right_alt", "arrow_triangle-up_alt", "arrow_triangle-down_alt", "arrow_triangle-left_alt", "arrow_triangle-right_alt", "icon_minus_alt", "icon_plus_alt", "icon_close_alt", "icon_check_alt", "icon_zoom-out", "icon_zoom-in", "icon_stop_alt", "icon_menu-square_alt", "icon_menu-circle_alt", "icon_document", "icon_documents", "icon_pencil_alt", "icon_folder", "icon_folder-open", "icon_folder-add", "icon_folder_upload", "icon_folder_download", "icon_info", "icon_error-circle", "icon_error-oct", "icon_error-triangle", "icon_question_alt", "icon_comment", "icon_chat", "icon_vol-mute", "icon_volume-low", "icon_volume-high", "icon_quotations_alt", "icon_clock", "icon_lock", "icon_lock-open", "icon_key", "icon_cloud", "icon_cloud-upload", "icon_cloud-download", "icon_lightbulb", "icon_gift", "icon_house", "icon_camera", "icon_mail", "icon_cone", "icon_ribbon", "icon_bag", "icon_cart", "icon_tag", "icon_tags", "icon_trash", "icon_cursor", "icon_mic", "icon_compass", "icon_pin", "icon_pushpin", "icon_map", "icon_drawer", "icon_toolbox", "icon_book", "icon_contacts", "icon_archive", "icon_heart", "icon_profile", "icon_group", "icon_grid-2x2", "icon_grid-3x3", "icon_music", "icon_pause_alt", "icon_phone", "icon_upload", "icon_download", "social_facebook", "social_twitter", "social_pinterest", "social_googleplus", "social_tumblr", "social_tumbleupon", "social_wordpress", "social_instagram", "social_dribbble", "social_vimeo", "social_linkedin", "social_rss", "social_deviantart", "social_share", "social_myspace", "social_skype", "social_youtube", "social_picassa", "social_googledrive", "social_flickr", "social_blogger", "social_spotify", "social_delicious", "social_facebook_circle", "social_twitter_circle", "social_pinterest_circle", "social_googleplus_circle", "social_tumblr_circle", "social_stumbleupon_circle", "social_wordpress_circle", "social_instagram_circle", "social_dribbble_circle", "social_vimeo_circle", "social_linkedin_circle", "social_rss_circle", "social_deviantart_circle", "social_share_circle", "social_myspace_circle", "social_skype_circle", "social_youtube_circle", "social_picassa_circle", "social_googledrive_alt2", "social_flickr_circle", "social_blogger_circle", "social_spotify_circle", "social_delicious_circle", "social_facebook_square", "social_twitter_square", "social_pinterest_square", "social_googleplus_square", "social_tumblr_square", "social_stumbleupon_square", "social_wordpress_square", "social_instagram_square", "social_dribbble_square", "social_vimeo_square", "social_linkedin_square", "social_rss_square", "social_deviantart_square", "social_share_square", "social_myspace_square", "social_skype_square", "social_youtube_square", "social_picassa_square", "social_googledrive_square", "social_flickr_square", "social_blogger_square", "social_spotify_square", "social_delicious_square", "icon_printer", "icon_calulator", "icon_building", "icon_floppy", "icon_drive", "icon_search-2", "icon_id", "icon_id-2", "icon_puzzle", "icon_like", "icon_dislike", "icon_mug", "icon_currency", "icon_wallet", "icon_pens", "icon_easel", "icon_flowchart", "icon_datareport", "icon_briefcase", "icon_shield", "icon_percent", "icon_globe", "icon_globe-2", "icon_target", "icon_hourglass", "icon_balance", "icon_rook", "icon_printer-alt", "icon_calculator_alt", "icon_building_alt", "icon_floppy_alt", "icon_drive_alt", "icon_search_alt", "icon_id_alt", "icon_id-2_alt", "icon_puzzle_alt", "icon_like_alt", "icon_dislike_alt", "icon_mug_alt", "icon_currency_alt", "icon_wallet_alt", "icon_pens_alt", "icon_easel_alt", "icon_flowchart_alt", "icon_datareport_alt", "icon_briefcase_alt", "icon_shield_alt", "icon_percent_alt", "icon_globe_alt", "icon_clipboard");

        $output =  '<input type="hidden" name="'.$param_name.'" class="wpb_vc_param_value '.$param_name.' '.$type.' '.$class.'" value="'.$value.'" id="trace"/>
				   <div class="icon-preview"><i class="icon '.$value.'"></i></div>';
        $output .= '<input class="search" type="text" placeholder="Search" />';
        $output .= '<div id="icon-dropdown" >';
        $output .= '<div class="pi_toggle"><i title="Click me to open/close list icons" class="icon icon_grid-3x3"></i></div>';
        $output .= '<ul class="icon-list hidden">';
        $icon_number = 1;
        foreach($icons as $icon)
        {
            $selected = ($icon == $value) ? 'class="selected"' : '';
            $id = 'icon-'.$icon_number;
            $output .= '<li '.$selected.' data-iconname="'.$icon.'"><i class="icon '.$icon.'"></i><label class="wiloke-icon-label">'.$icon.'</label></li>';
            $icon_number++;
        }
        $output .='</ul>';
        $output .='</div>';
        $output .= '
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery(".search").keyup(function(){
			 		var filter = jQuery(this).val(), count = 0;
					jQuery(".icon-list li").each(function(){
						if (jQuery(this).text().search(new RegExp(filter, "i")) < 0) {
							jQuery(this).fadeOut();
						} else {
							jQuery(this).show();
							count++;
						}
					});
				});
			});
			jQuery("#icon-dropdown li").click(function() {
				jQuery(this).attr("class","selected").siblings().removeAttr("class");
				var icon = jQuery(this).attr("data-iconname");
				jQuery("#trace").val(icon);
				jQuery(".icon-preview").html("<i class=\'icon "+icon+"\'></i>");
			});

			jQuery("#icon-dropdown .pi_toggle").click(function(){
				jQuery(this).next().toggleClass("hidden");
			});
		</script>';
        return $output;
    }

    $wilokeAddShortCodeParam = 'vc_add_';
    $wilokeAddShortCodeParam = $wilokeAddShortCodeParam . 'shortcode_param';
    $wilokeAddShortCodeParam('wiloke_elegant_icons' , 'wiloke_elegant_icons_param_settings');
    $wilokeAddShortCodeParam('wiloke_colorpicker' , 'wiloke_vc_colorpicker_settings');
}

/**
 * Spectrum
 * @since 1.0
 */
if ( !function_exists('wiloke_vc_colorpicker_settings') )
{
    function wiloke_vc_colorpicker_settings($settings, $value)
    {
        $param_name = isset($settings['param_name']) ? $settings['param_name'] : '';
        $type       = isset($settings['type']) ? $settings['type'] : '';
        $class      = isset($settings['class']) ? $settings['class'] : '';
        $id         = uniqid('wiloke_vc_colorpicker');
        $pattern    = isset($settings['pattern']) ? json_encode($settings['pattern']) : '';


        $output  = '<div id="'.esc_attr($id).'" data-pattern="'.esc_attr($pattern).'" class="pi-posttypes-wrapper">';

        $output  .= '<input type="text" name="'.$param_name.'" class="wiloke_vc_addon_colorpicker wpb-textinput wpb_vc_param_value textfield '.$param_name.' '.$type.' '.$class.'" value="'.esc_attr($value).'" />';

        $output .= '</div>';

        $output .= '<script type="text/javascript">jQuery("#'.esc_js($id).' .wiloke_vc_addon_colorpicker").spectrum({showInput: true,showAlpha: true,allowEmpty:true,pattern: jQuery("#'.esc_js($id).'").data(\'pattern\'),change: function(color) {if (color!==null){jQuery("#'.esc_js($id).' .wiloke_vc_addon_colorpicker").attr(\'value\', color.toRgbString()).trigger(\'keypress\');  }else{jQuery("#'.esc_js($id).' .wiloke_vc_addon_colorpicker").attr(\'value\', \'\').trigger(\'keypress\');}}})</script>';

        return $output;
    }
}

/**
 * Term Tabs
 */
if ( !function_exists('wiloke_vc_term_tabs') )
{
    function wiloke_vc_term_tabs($settings, $value)
    {
        $param_name = isset($settings['param_name']) ? $settings['param_name'] : '';
        $type       = isset($settings['type']) ? $settings['type'] : '';
        $multiple	= isset($settings['is_multiple']) && $settings['is_multiple']  ? 'multiple' : '';
        $class      = isset($settings['class']) ? $settings['class'] : '';
        $aTerms     = isset($settings['terms']) ? $settings['terms'] : '';

        $aTerms      = apply_filters('pi_vc_filter_posttypes', $aTerms);

        $output  = '<div class="pi-posttypes-wrapper">';

        $output  .= '<input type="hidden" name="'.$param_name.'" class="wpb_vc_param_value '.$param_name.' '.$type.' '.$class.'" />';

        $value      = $value ? explode(",", $value) : array();

        $output .= '<div class="wiloke-shortcode-param-nav '.esc_attr($multiple).'">';
        foreach ( $aTerms as  $term => $name )
        {
            if ( taxonomy_exists($term) )
            {
                $active = in_array($term, $aTerms) ? ' vc_btn-grace' : '';
                //onclick="wiloke_term_tabs_callback(this)
                $output .= '<button  data-value="' . esc_attr($term) . '" class="button button-primary ' . esc_attr($active) . '">' . esc_html($name) . '</button>';
            }
        }
        $output .= '</div>';

        $output .= '</div>';


        return $output;
    }

    $wilokeAddShortCodeParam = 'vc_add_';
    $wilokeAddShortCodeParam = $wilokeAddShortCodeParam . 'shortcode_param';
    $wilokeAddShortCodeParam('wiloke_term_tabs', 'wiloke_vc_term_tabs');
}

if ( !function_exists('wiloke_vc_get_post_authors') )
{
	function wiloke_vc_get_post_authors($settings, $value)
	{
		$param_name = isset($settings['param_name']) ? $settings['param_name'] : '';
		$multiple	= isset($settings['is_multiple']) && $settings['is_multiple']  ? 'multiple' : '';

        $value      = !is_array($value) ? explode(",", $value) : $value;
		$className = $param_name;
        if ( !isset($settings['is_select2']) || !$settings['is_select2'] ){
            global $wpdb;
            $tblName = $wpdb->prefix . users;

            $aOptions = $wpdb->get_results(
                $wpdb->prepare(
                        "SELECT display_name, ID FROM $tblName LIMIT %d",
                    20
                ),
                ARRAY_A
            );
        }else{
            $className .= ' js_select2_user';
        }
        $id = uniqid('js_select2_user');
        ob_start();
        ?>
        <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($param_name); ?>" class="wpb_vc_param_value <?php echo esc_attr($className); ?>" <?php echo esc_attr($multiple); ?>>
            <?php
                if ( !empty($aOptions) ) :
	                foreach ( $aOptions as $aOption ) :
                        $selected = !empty($value) && in_array($aOption['ID'], $value) ? 'selected' : '';
            ?>
                        <option value="<?php echo esc_attr($aOption['ID']); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_attr($aOption['display_name']); ?></option>
            <?php
                    endforeach;
                elseif ( !empty($value) ) :
                    foreach ($value as $aOption) :
            ?>
                    <option value="<?php echo esc_attr($aOption['ID']); ?>" selected><?php echo esc_attr($aOption['display_name']); ?></option>
            <?php
                    endforeach;
                endif;
            ?>
        </select>
        <?php if ( !isset($settings['is_select2']) && $settings['is_select2'] ) : ?>
        <script type="text/javascript">
        jQuery('#<?php echo esc_attr($id); ?>').select2({
	        ajax: {
		        type: 'GET',
		        url: ajaxurl,
		        delay: 250,
		        minimumInputLength: 2,
		        data: function (params) {
			        return {
				        action: 'select_user_via_ajax',
				        s: params.term
			        }
		        },
		        processResults: function (data, params) {
			        return {
				        results: data.data
			        };
		        },
		        cache: true
	        }
        });
        </script>
        <?php
        endif;
        return ob_get_clean();
	}

	$wilokeAddShortCodeParam = 'vc_add_';
	$wilokeAddShortCodeParam = $wilokeAddShortCodeParam . 'shortcode_param';
	$wilokeAddShortCodeParam('wiloke_get_post_authors', 'wiloke_vc_get_post_authors');
}

if ( !function_exists('wiloke_get_list_of_terms') )
{
    function wiloke_vc_get_list_of_terms($settings, $value)
    {
        $param_name  = isset($settings['param_name']) ? $settings['param_name'] : '';
        $taxonomy    = isset($settings['taxonomy']) ? $settings['taxonomy'] : '';
        $isMultiple  = isset($settings['is_multiple']) && $settings['is_multiple']  ?  'multiple' : '';
        $isHideEmpty = isset($settings['is_not_hide_empty']) && $settings['is_not_hide_empty']  ?  false : true;

        if ( !is_array($value) )
        {
            $value = explode(',', $value);
        }

        $aTerms   = get_terms(
           array(
               'taxonomy'      => $taxonomy,
               'hide_empty'    => $isHideEmpty
           )
        );

        ob_start();
        if ( !empty($aTerms) || !is_wp_error($aTerms) )
        {
            ?>
            <select name="<?php echo esc_attr($param_name); ?>" class="wpb_vc_param_value <?php echo esc_attr($param_name); ?>" <?php echo esc_attr($isMultiple); ?>>
                <?php
                    foreach ( $aTerms as $aTerm ) :
                        if ( in_array($aTerm->term_id, $value) )
                        {
                            $selected = 'selected';
                        }else{
                            $selected = '';
                        }
                ?>
                        <option <?php echo esc_attr($selected); ?> value="<?php echo esc_attr($aTerm->term_id); ?>"><?php echo esc_html($aTerm->name); ?></option>
                <?php
                    endforeach;
                ?>
            </select>
            <?php if ( !empty($isMultiple) ) : ?>
            <button style="margin-top: 20px;" class="button button-primary" id="wiloke-vc-listofterms-toggle-select"><?php esc_html_e('Toggle Select', 'listgo'); ?></button>
            <?php endif; ?>
            <?php
        }else{
            esc_html_e('There are no posts in this taxonomy', 'listgo');
        }

        $output = ob_get_clean();
        return $output;
    }

    $wilokeAddShortCodeParam = 'vc_add_';
    $wilokeAddShortCodeParam = $wilokeAddShortCodeParam . 'shortcode_param';
    $wilokeAddShortCodeParam('wiloke_get_list_of_terms', 'wiloke_vc_get_list_of_terms');
}

/**
 * Providing Layouts
 * @since 1.1
 */
if ( !function_exists('wiloke_design_portfolio_choose_layout') ) {
    function wiloke_design_portfolio_choose_layout($aSettings, $value){
        ob_start();

        if ( !class_exists('WilokeService') ) {
             WilokeAlert::render_alert( __('<p>Wiloke Service plugin is required. If the plugin doesn\'t install, please go to Appearance -> Install Plugins to do it. If this plugin has already been installed, please go to Plugins -> Looking for Wiloke Service and activate it.</p>', 'listgo'), 'alert' );
            return ob_get_clean();
        }

        if ( isset($aSettings['is_request_server']) && $aSettings['is_request_server'] ) :
        ?>
            <button id="wiloke-design-portfolio-request" class="vc_general vc_ui-button vc_ui-button-size-sm vc_ui-button-shape-rounded vc_ui-button-action vc_ui-access-library-btn"><?php esc_html_e('Refresh', 'listgo'); ?></button>
        <?php
        endif; // end is request server

        $aDemoCaching = get_transient(WilokeImport::$wilokePortfolioDesignStore);
        if ( !$aDemoCaching && !isset($aSettings['options']) ) {
            return ob_get_clean();
        }

        $param_name         = isset($aSettings['param_name']) ? $aSettings['param_name'] : '';
        $type               = isset($aSettings['type'])  ? $aSettings['type'] : '';
        $class              = isset($aSettings['class']) ? $aSettings['class'] : '';
        $defaultLayout      = isset($aSettings['std']) ? $aSettings['std'] : '';
        $aPortfolioLayouts  = $aSettings['options'];

        $func = 'base64_' . 'decode';
        
        if ( !empty($value) ) {
            $value = $func($value);
            $value = json_decode($value, true);
        }else{
            $value['layout'] = $aSettings['std'];
        }

        ?>
        <!-- param window header-->
        <div id="wiloke-print-wiloke-design-layout" class="design-images wo_wpb_select" style="margin-top: 30px;">
            <?php foreach ( $aPortfolioLayouts as $layout => $aConfiguration ) : ?>
                <input type="hidden" class="wpb_vc_param_value" name="<?php echo esc_attr($param_name); ?>" />
                <?php
                if ( $value['layout'] == $layout )
                {
                    $class   = $class . ' wo_portfolio_layout active' . $param_name . ' ' . $type;
                    $checked = 'wo_wpb_checked';
                }else{
                    $checked = '';
                    $class   = $class . ' wo_portfolio_layout' . $param_name . ' ' . $type;
                }

                ?>
                <label class="item <?php echo esc_attr($checked); ?>" data-is-customize="<?php echo esc_attr($aConfiguration['is_customize']); ?>">
                    <input name="<?php echo esc_attr($param_name.'[value]'); ?>" type="hidden" class="wo_portfolio_layout_settings <?php echo esc_attr($layout); ?>" value="<?php echo esc_attr($aConfiguration['value']); ?>" />
                    <input name="<?php echo esc_attr($param_name); ?>[layout]" class="wo_portfolio_layout_value" type="radio" value="<?php echo esc_attr($layout); ?>" />
                    <img src="<?php echo esc_url($aConfiguration['img_url']) ?>" alt="<?php echo esc_attr($aConfiguration['heading']); ?>" />
                    <span style="text-align:center; font-weight: bold;"><?php echo esc_attr($aConfiguration['heading']); ?></span>
                </label>
            <?php endforeach; ?>
            <?php
            if ( $aDemoCaching ) :
                $aDemoCaching = json_decode($aDemoCaching, true);
                foreach ( $aDemoCaching as $aConfiguration ) :
                    $layout = preg_replace_callback('/\s+/', function(){
                        return '';
                    }, $aConfiguration['name']);
                    $layout = strtolower($layout);
                    $value = get_transient(WilokeImport::$designPortfolioPrefix.$layout);
                    $value = $value ? $value : $aConfiguration['file'];
            ?>
                <input type="hidden" class="wpb_vc_param_value" name="<?php echo esc_attr($param_name); ?>" />
                <?php
                if ( $value['layout'] == $layout )
                {
                    $class   = $class . ' wo_portfolio_layout active' . $param_name . ' ' . $type;
                    $checked = 'checked';
                }else{
                    $checked = '';
                    $class   = $class . ' wo_portfolio_layout' . $param_name . ' ' . $type;
                }

                ?>
                <label class="item wiloke-service <?php echo esc_attr($checked); ?>">
                    <input name="<?php echo esc_attr($param_name.'[value]'); ?>" type="hidden" class="wo_portfolio_layout_settings" value="<?php echo esc_attr($value); ?>" />
                    <input name="<?php echo esc_attr($param_name); ?>[layout]" <?php echo esc_attr($checked); ?> class="wo_portfolio_layout_value" type="radio" value="<?php echo esc_attr($layout); ?>" />
                    <img src="<?php echo esc_url(WilokeService::$awsUrl.$aConfiguration['screenshot']) ?>" alt="<?php echo esc_attr($aConfiguration['name']); ?>" />
                    <?php if ( !empty($aConfiguration['url']) ) : ?>
                        <a href="<?php echo esc_url($aConfiguration['url']); ?>"><span style="text-align:center; font-weight: bold;"><?php echo esc_attr($aConfiguration['name']); ?></span></a>
                    <?php else : ?>
                        <span style="text-align:center; font-weight: bold;"><?php echo esc_attr($aConfiguration['name']); ?></span>
                    <?php endif; ?>
                </label>
            <?php endforeach;
            endif; ?>
        </div>
        <?php

        return ob_get_clean();
    }

    $wilokeAddShortCodeParam = 'vc_add_';
    $wilokeAddShortCodeParam = $wilokeAddShortCodeParam . 'shortcode_param';
    $wilokeAddShortCodeParam('wiloke_design_portfolio_choose_layout', 'wiloke_design_portfolio_choose_layout');
}

/**
 * Design Portfolio Layout
 * @since 1.0
 */
if ( !function_exists('wiloke_design_portfolio_layout') )
{
    function wiloke_design_portfolio_layout($aSettings, $value)
    {
        global $wiloke;

        $param_name         = isset($aSettings['param_name']) ? $aSettings['param_name'] : '';
        $type               = isset($aSettings['type'])       ? $aSettings['type'] : '';
        $class              = isset($aSettings['class'])      ? $aSettings['class'] : '';
        $defaultLayout      = isset($aSettings['std']) ? $aSettings['std'] : '';

        $aPortfolioLayouts = array_keys($aSettings['options']);

        if ( !is_array($value) )
        {
            $func  = 'base64_' . 'decode';
            $value = $func($value);
            $value = json_decode($value, true);
        }

        if ( !isset($value['layout']) )
        {
            $value['layout'] = $aSettings['std'];
        }

        if ( !isset($value['general_settings']) ) {
            $value['general_settings'] = $aSettings['general_settings'];
        }

        if ( !isset($value['devices_settings']) ) {
            $value['devices_settings'] = $aSettings['devices_settings'];
        }
        
        ob_start();
        ?>
        <div class="wiloke-portfolio-layout-wrapper">

            <div class="general-settings">
                <input type="hidden" id="wpa_general_settings" class="wpa_general_settings" value="<?php echo htmlentities(json_encode($value['general_settings'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)); ?>" />
                <input type="hidden" id="wpa_devices_settings" class="wpa_general_devices_settings" value="<?php echo htmlentities(json_encode($value['devices_settings'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)); ?>">
            </div>

            <div class="wo_wpb_wrap">
                <!-- param window header-->
                <div id="wiloke-js-init-wpa" class="wo_wpb_select js-parent hidden">
                    <input type="hidden" class="wpb_vc_param_value" name="<?php echo esc_attr($param_name); ?>" />
                    <?php
                        foreach ( $aPortfolioLayouts as $layout ) :
                            if ( $value['layout'] == $layout )
                            {
                                $class   = $class . ' wo_portfolio_layout active previous-active js_child ' . $param_name . ' ' . $type;
                                $checked = 'checked';
                            }else{
                                $checked = '';
                                $class   = $class . ' wo_portfolio_layout js_child ' . $param_name . ' ' . $type;
                            }

                            $settings = isset($value[$layout]) && !empty($value[$layout]) ? $value[$layout] : $aSettings['options'][$layout]['std'];
                            $settings = json_encode($settings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                            
                            $dragdropStatus = isset($aSettings['options'][$layout]['is_dragdrop']) && $aSettings['options'][$layout]['is_dragdrop'] == 'yes' ? 'yes' : 'no';
                    ?>
                        <label class="item">
                            <input name="<?php echo esc_attr($param_name.'[layout]'); ?>" <?php echo esc_attr($checked); ?> type="radio" value="<?php echo esc_attr($layout); ?>" data-settings="<?php echo htmlentities(json_encode($aSettings['options'][$layout]['params'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)); ?>" class="<?php echo esc_attr($class); ?>" data-isdragdrop="<?php echo esc_attr($dragdropStatus); ?>" />
                            <input name="<?php echo esc_attr($param_name.'['.$layout.'][settings]'); ?>" type="hidden" data-settings="<?php echo htmlentities($settings); ?>" class="wo_portfolio_layout_settings <?php echo esc_attr($layout); ?>" value="<?php echo htmlentities($settings); ?>" />
                            <img src="<?php echo esc_url($aSettings['options'][$layout]['img_url']) ?>" alt="<?php echo esc_attr($aSettings['options'][$layout]['heading']); ?>" />
                            <span><?php echo esc_attr($aSettings['options'][$layout]['heading']); ?></span>
                        </label>
                    <?php endforeach;
                    ?>
                </div>

                <ul class="wo_wpb_tab js-parent design-tab">
                    <?php
                    $i = 1;
                    foreach ( $wiloke->aConfigs['general']['wiloke_design_portfolio'] as $device => $aInfo ) :
                        if ( $i == 1 ) {
                            $active = 'active';
                        }else{
                            $active = '';
                        }
                    ?>
                        <li class="<?php echo esc_attr($active); ?> previous-active js_device_setting js_child" data-device="<?php echo esc_attr($device); ?>" data-desc="<?php echo esc_attr($aInfo['desc']); ?>"><img src="<?php echo esc_url(get_template_directory_uri() . '/admin/source/design-layout/img/icon-'.$device.'.png'); ?>" alt="<?php echo esc_attr($aInfo['title']); ?>" /> <span><?php echo esc_html($aInfo['title']); ?></span></li>
                    <?php $i++; endforeach; ?>
                </ul>

                <div class="wo_wpb_setting settings-packery-layout">

                    <!-- Settings Zone -->
                    <div class="wo_wpd_settings_zone packery">
                        <div class="wo_wpb_left">

                        </div>
                    </div>
                    <!-- End / Settings Zone -->

                    <!-- Emulate Zone -->
                    <div class="wo_wpb_right">
                        <div class="wiloke-portfolio-layout-wrapper">
                            <div class="wiloke-portfolio-layout-emulate wil_masonry-wrapper js_wiloke_pa_elumate_zone wil_masonry-grid wo_wpb_setting_layout">
                                <div class="wil_masonry wo_wpb_grid wo_wpb_grid--custom" data-gap="0" data-col-lg="3" data-col="3">
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- End / Emulate Zone -->

                </div>

            </div>

            <script type="text/javascript">
                jQuery(function ($) {
                    $('.vc_edit-form-tab-control').on('click', function () {
                        var $target = $(this).closest('.vc_ui-panel-window-inner').find('#wiloke-js-init-wpa'),
                            mode = $(this).children().html();
                        if ( !$target.data('wiloke-portfolio-loaded') ){

                            if ( mode == 'Design Layout' || mode == 'Choose Layout' )
                            {
                                $target.WilokePA();
                                $target.data('wiloke-portfolio-loaded', true);
                            }

                        }else{
                            if ( mode == 'Design Layout' || mode == 'Choose Layout' ) {
                                setTimeout(function () {
                                    $target.closest('.wo_wpb_wrap').find('.settings-packery-layout .wil_masonry').packery('layout');
                                }, 100);
                            }
                        }

                    });

                })
            </script>

        </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    $wilokeAddShortCodeParam = 'vc_add_';
    $wilokeAddShortCodeParam = $wilokeAddShortCodeParam . 'shortcode_param';
    $wilokeAddShortCodeParam('wiloke_design_portfolio_layout', 'wiloke_design_portfolio_layout');
}

/**
 * List Of Posts
 */
if ( !function_exists('wiloke_get_list_of_posts') )
{
	function wiloke_get_list_of_posts($aArgs, $value)
	{
		$param_name = isset($aArgs['param_name']) ? $aArgs['param_name'] : '';
		$multiple   = isset($aArgs['is_multiple']) && $aArgs['is_multiple'] ? 'multiple' : '';
		$aPosts = get_posts(
		    array(
                'post_type' => $aArgs['post_type'],
                'posts_per_page' => -1,
                'post_status' => 'publish'
            )
        );

		if ( empty($aPosts) || is_wp_error($aPosts) ){
		    return WilokeAlert::render_alert(esc_html__('We found no package. Please go to Pricings and create some', 'listgo'), 'warning', true);
        }

		ob_start();
		$id = uniqid('wiloke_');
		?>
        <div class="wiloke_vc_list_of_posts_wrapper">
            <div class="field wiloke_vc_list_of_posts">
                <div id="<?php echo esc_attr($id); ?>" class="ui <?php echo esc_attr($multiple); ?> search selection dropdown">
                    <input type="hidden" name="<?php echo esc_attr($param_name); ?>" class="wpb-textinput wpb_vc_param_value textfield <?php echo esc_attr($param_name); ?>" value="<?php echo esc_attr($value); ?>">
                    <i class="dropdown icon"></i>
                    <div class="default text"><?php echo isset($aArgs['placeholder']) ? esc_html($aArgs['placeholder']) : ''; ?></div>
                    <div class="menu">
						<?php foreach ( $aPosts as $oPost ) : ?>
                            <div class="item" data-value="<?php echo esc_attr($oPost->ID); ?>" data-text="<?php echo esc_attr($oPost->post_title); ?>">
								<?php echo esc_html($oPost->post_title); ?>
                            </div>
						<?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <script>
			jQuery(document).ready(function ($) {
				$('#<?php echo esc_attr($id); ?>').dropdown();
			});
        </script>
		<?php
		$output = ob_get_clean();
		return $output;
	}

	$wilokeAddShortCodeParam = 'vc_add_';
	$wilokeAddShortCodeParam = $wilokeAddShortCodeParam . 'shortcode_param';
	$wilokeAddShortCodeParam('wiloke_list_of_posts', 'wiloke_get_list_of_posts');
}


/**
 * List Of Roles
 */
if ( !function_exists('wiloke_get_list_of_roles') )
{
    function wiloke_get_list_of_roles($aArgs, $value)
    {
        $param_name = isset($aArgs['param_name']) ? $aArgs['param_name'] : '';
        $multiple   = isset($aArgs['is_multiple']) && $aArgs['is_multiple'] ? 'multiple' : '';
	    global $wp_roles;

        ob_start();
        $id = uniqid('wiloke_');
        ?>
        <div class="wiloke_vc_list_of_posts_wrapper">
            <div class="field wiloke_vc_list_of_posts">
                <div id="<?php echo esc_attr($id); ?>" class="ui <?php echo esc_attr($multiple); ?> search selection dropdown">
                    <input type="hidden" name="<?php echo esc_attr($param_name); ?>" class="wpb-textinput wpb_vc_param_value textfield <?php echo esc_attr($param_name); ?>" value="<?php echo esc_attr($value); ?>">
                    <i class="dropdown icon"></i>
                    <div class="default text"><?php echo isset($aArgs['placeholder']) ? esc_html($aArgs['placeholder']) : ''; ?></div>
                    <div class="menu">
                        <?php foreach ( $wp_roles->roles as $role => $oInfo ) : ?>
                            <div class="item" data-value="<?php echo esc_attr($role); ?>" data-text="<?php echo esc_attr($oInfo['name']); ?>">
                                <?php echo esc_html($oInfo['name']); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <script>
            jQuery(document).ready(function ($) {
	            $('#<?php echo esc_attr($id); ?>').dropdown();
            });
        </script>
        <?php
        $output = ob_get_clean();
        return $output;
    }

    $wilokeAddShortCodeParam = 'vc_add_';
    $wilokeAddShortCodeParam = $wilokeAddShortCodeParam . 'shortcode_param';
    $wilokeAddShortCodeParam('wiloke_list_of_roles', 'wiloke_get_list_of_roles');
}

/**
 * List Of members
 */
if ( !function_exists('wiloke_get_list_of_users') )
{
	function wiloke_get_list_of_users($aArgs, $value)
	{
		$param_name = isset($aArgs['param_name']) ? $aArgs['param_name'] : '';
		$multiple   = isset($aArgs['is_multiple']) && $aArgs['is_multiple'] ? 'multiple' : '';
		$aUsers = get_users();

		ob_start();
		$id = uniqid('wiloke_');
		?>
        <div class="wiloke_vc_list_of_posts_wrapper">
            <div class="field wiloke_vc_list_of_posts">
                <div id="<?php echo esc_attr($id); ?>" class="ui <?php echo esc_attr($multiple); ?> search selection dropdown">
                    <input type="hidden" name="<?php echo esc_attr($param_name); ?>" class="wpb-textinput wpb_vc_param_value textfield <?php echo esc_attr($param_name); ?>" value="<?php echo esc_attr($value); ?>">
                    <i class="dropdown icon"></i>
                    <div class="default text"><?php echo isset($aArgs['placeholder']) ? esc_html($aArgs['placeholder']) : ''; ?></div>
                    <div class="menu">
						<?php foreach ( $aUsers as $role => $oUser ) : ?>
                            <div class="item" data-value="<?php echo esc_attr($oUser->ID); ?>" data-text="<?php echo esc_attr($oUser->display_name); ?>">
								<?php echo esc_html($oUser->display_name); ?>
                            </div>
						<?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <script>
			jQuery(document).ready(function ($) {
				$('#<?php echo esc_attr($id); ?>').dropdown();
			});
        </script>
		<?php
		$output = ob_get_clean();
		return $output;
	}

	$wilokeAddShortCodeParam = 'vc_add_';
	$wilokeAddShortCodeParam = $wilokeAddShortCodeParam . 'shortcode_param';
	$wilokeAddShortCodeParam('wiloke_list_of_users', 'wiloke_get_list_of_users');
}

/**
 * List Of Posts
 */

if ( !function_exists('wiloke_description') )
{
	function wiloke_vc_description($aArgs)
	{
		ob_start();
		?>
        <div class="wiloke_vc_list_of_posts_wrapper">
            <?php if ( isset($aArgs['title']) ) : ?>
            <h3><?php echo esc_html($aArgs['title']); ?></h3>
            <?php endif; ?>
        </div>
		<?php
		$output = ob_get_clean();
		return $output;
	}

	$wilokeAddShortCodeParam = 'vc_add_';
	$wilokeAddShortCodeParam = $wilokeAddShortCodeParam . 'shortcode_param';
	$wilokeAddShortCodeParam('wiloke_description', 'wiloke_vc_description');
}

if (class_exists('WPBakeryShortCodesContainer'))
{
    class WPBakeryShortCode_wiloke_smart_switch extends WPBakeryShortCodesContainer {}
    class WPBakeryShortCode_wiloke_accordions extends WPBakeryShortCodesContainer {}
}

function wiloke_add_portfolio_design_layout_template()
{
    global $post;
    if ( isset($post->post_type) && $post->post_type == 'page' && is_file( get_template_directory() . '/admin/source/design-layout/helpers/fields.php' ) )
    {
        include get_template_directory() . '/admin/source/design-layout/helpers/fields.php';
        include get_template_directory() . '/admin/source/design-layout/helpers/emulates.php';
    }
}

add_action('admin_head', 'wiloke_add_portfolio_design_layout_template');

/**
 * Add Font Icons
 * @since 1.0
 */
//add_filter( 'vc_iconpicker-type-fontawesome', 'wiloke_iconpicker_type_fontawesome' );
function wiloke_iconpicker_type_fontawesome($icon)
{
    global $wiloke;
    return array_merge($icon, $wiloke->aConfigs['icon']);
}
