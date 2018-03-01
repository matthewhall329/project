<?php
/**
 * WilokeAdminGeneral Class
 *
 * @category General
 * @package Wiloke Framework
 * @author Wiloke Team
 * @version 1.0.1
 */

if ( !defined('ABSPATH') )
{
    exit;
}

class WilokeAdminGeneral
{
    public $aOptions;

    public function __construct() {
        add_action('wp_ajax_select2_get_posts', array($this, 'getPosts'));
        add_action('wp_ajax_select_user_via_ajax', array($this, 'getUsers'));
        add_action('admin_init', array($this, 'installWilokeFunctionalityPlugin'));
    }

    public function addAdditionalScripts($aScripts){
        if ( empty($aScripts['additional_js']) && empty($aScripts['additional_css']) ){
	        $aScripts['additional_js'] = array(
                'wpb_composer_front_js=>js_composer/assets/js/dist/js_composer_front.min.js',
                'wiloke-megamenu=>wiloke-mega-menu/assets/js/lib/jquery.wiloke.megamenu.min.js',
                'wiloke-megamenu.script=>wiloke-mega-menu/assets/js/script.min.js'
            );

		    $aScripts['additional_css'] = array(
			    'contact-form-7=>contact-form-7/includes/css/styles.css',
			    'wiloke-login-with-social=>login-with-social/public/source/css/style.css',
			    'js_composer_front=>js_composer/assets/css/js_composer.min.css',
			    'animated=>wiloke-mega-menu/assets/css/lib/animated.min.css',
			    'wiloke-megamenu=>wiloke-mega-menu/assets/css/style.css'
		    );
	    }

	    return $aScripts;
    }

    public function filterMegaMenuShortcodes($aMenu){
        unset($aMenu['shortcode_posts']);
        unset($aMenu['shortcode_image_carousel']);
        unset($aMenu['shortcode_list']);
        return $aMenu;
    }

	protected function _unZipFile($package, $isLive=false){
        if ( !function_exists('WP_Filesystem') ){
	        require_once  ABSPATH . 'wp-admin/includes/file.php';
        }
		WP_Filesystem();
		$status = unzip_file( $package,  ABSPATH.'/wp-content/plugins');
		if ( $isLive ){
			@unlink($package);
		}
		if ( $status ){
			return true;
		}

		return false;
	}

	public function installWilokeFunctionalityPlugin(){
	    if ( !get_option('wiloke_listgo_is_firstime_setup_theme') ){
		    $aActivatingPlugins = get_option('active_plugins');
		    if ( !in_array('wiloke-listgo-functionality/wiloke-listgo-functionality.php', $aActivatingPlugins) ){
		        if ( !is_file(WP_PLUGIN_DIR . '/wiloke-listgo-functionality/wiloke-listgo-functionality.php') ){
			        if ( $this->_unZipFile(get_template_directory() . '/plugins/wiloke-listgo-functionality.zip') ) {
				        $aActivatingPlugins[] = 'wiloke-listgo-functionality/wiloke-listgo-functionality.php';
			        }
                }else{
			        $aActivatingPlugins[] = 'wiloke-listgo-functionality/wiloke-listgo-functionality.php';
                }
                update_option( 'active_plugins', $aActivatingPlugins );
                do_action('wiloke_trigger_installed_wiloke_service');
                update_option('wiloke_submission_need_add_role_now', true);
		    }
		    update_option('wiloke_listgo_is_firstime_setup_theme', true);
		    header('Location: '.esc_url(admin_url('admin.php?page=wiloke-welcome')));
		    exit();
        }
    }

    public function highlight_box_in_listing(){
	    add_meta_box( 'wiloke-listgo-toggle-highlight', esc_html__( 'Featured Listing', 'listgo' ), array($this, 'highlightSettings'), 'listing', 'side', 'low' );
    }

    public function highlightSettings($post){
        $val = get_post_meta($post->ID, 'wiloke_listgo_toggle_highlight', true);
	    $val = $val ? $val : 0;
        ?>
        <select name="wiloke_listgo_toggle_highlight" id="wiloke_listgo_toggle_highlight">
            <option value="1" <?php selected($val, 1); ?>><?php esc_html_e('Enable', 'listgo'); ?></option>
            <option value="0" <?php selected($val, 0); ?>><?php esc_html_e('Disable', 'listgo'); ?></option>
        </select>
        <?php
    }

    public function afterListingUpdated($postID, $postAfter){
        if ( !is_admin() ){
            return false;
        }

        if ( $postAfter->post_type !== 'listing' ){
            return false;
        }

	    if ( isset($_POST['wiloke_listgo_toggle_highlight']) && !empty($_POST['wiloke_listgo_toggle_highlight']) ){
		    if ( empty($postAfter->menu_order) ){
			    wp_update_post(
				    array(
					    'ID'            => $postID,
					    'menu_order'    => 1,
					    'post_type'     => 'listing'
				    )
			    );
		    }
	    }else{
		    if ( !empty($postAfter->menu_order) ){
			    wp_update_post(
				    array(
					    'ID' => $postID,
					    'menu_order' => 0,
					    'post_type' => 'listing'
				    )
			    );
		    }
        }
    }

    public function saveSettings($postID, $post){
        if ( isset($_POST['wiloke_listgo_toggle_highlight']) ){
            update_post_meta($postID, 'wiloke_listgo_toggle_highlight', absint($_POST['wiloke_listgo_toggle_highlight']));
        }
    }

	public function getUsers(){
		if ( !current_user_can('manage_options') || empty($_GET['s']) ){
			wp_send_json_error();
		}

		$search = '%'.$_GET['s'].'%';

		global $wpdb;
		$tblName = $wpdb->prefix . 'users';

		$aResults = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT user_login as text, ID as id FROM $tblName WHERE user_login LIKE %s LIMIT 0, 50",
				$search
			),
			ARRAY_A
		);

		if ( empty($aResults) ){
			wp_send_json_error();
		}

		wp_send_json_success($aResults);
	}

    public function getPosts(){
	    if ( !current_user_can('manage_options') || empty($_GET['s']) || empty($_GET['post_type']) ){
		    wp_send_json_error();
	    }

	    $search = '%'.$_GET['s'].'%';
	    $postType = esc_sql($_GET['post_type']);

	    global $wpdb;
	    $tblName = $wpdb->prefix . 'posts';

	    $aResults = $wpdb->get_results(
		    $wpdb->prepare(
			    "SELECT post_title as text, ID as id FROM $tblName WHERE post_title LIKE %s AND post_type=%s LIMIT 0, 50",
			    $search, $postType
		    ),
		    ARRAY_A
	    );

	    if ( empty($aResults) ){
		    wp_send_json_error();
	    }

	    wp_send_json_success($aResults);
    }

	public function get_options(){
        if ( is_admin() ) {
            return;
        }

        $this->aOptions = get_option('wiloke_permalink');
    }

    /**
     * Render admin notices
     * @since 1.0
     */
    public function notices()
    {
        if ( !empty(Wiloke::$list_of_errors) )
        {
            foreach ( Wiloke::$list_of_errors as $error => $aMessages )
            {
                foreach ( $aMessages as $message )
                {
                    ?>
                    <div class="notice notice-<?php echo esc_attr($error); ?> is-dismissible">
                        <p><?php Wiloke::wiloke_kses_simple_html($message); ?></p>
                    </div>
                    <?php
                }
            }
        }
    }

    /**
     * Enqueue scripts to admin
     * @since 1.0
     */
    public function enqueue_scripts($hook)
    {
        global $post, $wiloke;

        if ( !wp_script_is('wiloke_post_format', 'enqueued') )
        {
            wp_enqueue_media();

            wp_enqueue_script('wiloke_post_format_ui', WILOKE_AD_SOURCE_URI . 'js/wiloke_post_format.js', array('jquery'), false, true);
            wp_enqueue_style('wiloke_post_format_ui', WILOKE_AD_SOURCE_URI . 'css/wiloke_post_format.css');

            wp_enqueue_script('wiloke_taxonomy', WILOKE_AD_SOURCE_URI . 'js/taxonomy.js', array('jquery', 'wiloke_post_format_ui'), false, true);
        }

        wp_register_script('spectrum', WILOKE_AD_ASSET_URI . 'js/spectrum.js', array('jquery'), null, true);
        wp_register_style('spectrum', WILOKE_AD_ASSET_URI . 'css/spectrum.css');

        wp_enqueue_style('wiloke_ad_shortcodes', WILOKE_AD_SOURCE_URI . 'css/shortcode.css');
        wp_enqueue_style('wiloke_design_layout', WILOKE_AD_SOURCE_URI . 'design-layout/css/style.css');
        wp_enqueue_style('wiloke_admin_general', WILOKE_AD_SOURCE_URI . 'css/style.css');

        wp_enqueue_script('wiloke-global', WILOKE_AD_SOURCE_URI . 'js/scripts.js', array('jquery'), false, true);

	    wp_enqueue_script('googlemap', esc_url('//maps.googleapis.com/maps/api/js?key='.$wiloke->aThemeOptions['general_map_api']).'&libraries=places&sensor=false');

        if ( isset($post->post_type) && ($post->post_type === 'listing') ) {
            wp_enqueue_script('wiloke-mapextend', WILOKE_AD_SOURCE_URI . 'js/mapextend.js', array('jquery'), WILOKE_THEMEVERSION, true);
            wp_enqueue_style('wiloke-mapextend', WILOKE_AD_SOURCE_URI . 'css/mapextend.css', array(), WILOKE_THEMEVERSION);

            wp_enqueue_style('bootstrap-table', WILOKE_AD_ASSET_URI . 'css/bootstrap-table.css', array(), WILOKE_THEMEVERSION);
        }

        if ( isset($wiloke->aConfigs['tours']) && isset($wiloke->aConfigs['tours']['admin']) )
        {
            wp_localize_script('introjs', 'WILOKE_TOURS', $wiloke->aConfigs['tours']['admin']);
        }

        $aIntro = get_option('wiloke_ingore_intro');
        if ( !empty($aIntro) )
        {
            $aIntro = json_decode($aIntro, true);
            wp_localize_script('introjs', 'WILOKE_IGNORE_INTRO', $aIntro);
        }
        
        if ( isset($post->post_type) )
        {
            if ( is_file(get_template_directory() . '/admin/source/css/'.$post->post_type.'.css') )
            {
                wp_enqueue_style('wiloke_'.$post->post_type, WILOKE_AD_SOURCE_URI . 'css/'.$post->post_type.'.css');
            }

            if ( is_file(get_template_directory() . '/admin/source/js/'.$post->post_type.'.js') )
            {
                wp_enqueue_script('wiloke_'.$post->post_type, WILOKE_AD_SOURCE_URI . 'js/'.$post->post_type.'.js', array('jquery'), false, true);
            }
            
            if ( $post->post_type === 'page' ){
                wp_enqueue_style('semantic-select-ui', get_template_directory_uri() . '/admin/asset/css/semantic-select-field.min.css');
	            wp_enqueue_script('semantic-ui', get_template_directory_uri() . '/admin/asset/js/semantic.min.js', array('jquery'), false, true);
            }
        }

        wp_enqueue_script('base64', WILOKE_AD_ASSET_URI . 'js/base64.min.js', array('jquery'), null, true);
        wp_enqueue_script('wiloke-design-layout', WILOKE_AD_SOURCE_URI . 'design-layout/js/portfolio-layout.js', array('jquery', 'imagesloaded'), false, true);

	    wp_enqueue_style('select2', WILOKE_AD_ASSET_URI . 'css/select2.min.css');
        wp_enqueue_script('select2', WILOKE_AD_ASSET_URI . 'js/select2.min.js', array('jquery'), false, true);

        wp_add_inline_script('wiloke-global', $this->add_script_to_admin_head());
    }

    /**
     * Font formats
     * @since 1.0
     */
    public function add_font_setting_menus($buttons){
        array_unshift( $buttons, 'fontselect' ); // Add Font Select
        array_unshift( $buttons, 'fontsizeselect' ); // Add Font Size Select
        return $buttons;
    }
    public function fontsize_formats($initArray){
        $initArray['fontsize_formats'] = "9px 10px 12px 13px 14px 16px 18px 21px 24px 28px 32px 36px";
        return $initArray;
    }

    /**
     * Print scripts into head in the admin area
     * @since 1.0
     */
    public function add_script_to_admin_head()
    {
        global $wiloke;
        ob_start();
        ?>
        window.WilokeAdminGlobal = {};
        WilokeAdminGlobal.ajaxinfo = {};
        <?php
            if ( isset($wiloke->aConfigs['general']['color_picker']['palette']) && !empty($wiloke->aConfigs['general']['color_picker']['palette']) )
            {
                ?>
WilokeAdminGlobal.ColorPalatte = '<?php echo json_encode($wiloke->aConfigs['general']['color_picker']['palette'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP ); ?>';
                <?php
            }else{
                ?>
WilokeAdminGlobal.ColorPalatte = null;
                <?php
            }
        return ob_get_clean();
    }

    /**
     * Filter Term Params
     * @since 1.0
     */
    public function get_more_params_of_term($aTerm){
        if ( $aTerm['taxonomy'] === 'listing_location' ){
            $aTerm['settings'] = Wiloke::getTermOption($aTerm['term_id']);
            if ( !isset($aTerm['settings']['map_marker_image']) || empty($aTerm['settings']['map_marker_image']) ){
	            $aTerm['settings']['map_marker_image'] = get_template_directory_uri() . '/img/icon-marker.png';
            }
        }

        return $aTerm;
    }

    /**
     * Add Additional Settings for listings
     * @since 1.0
     */
    public function addAdditionalSettingsForListing(){
        add_meta_box(
            'business_hours',
            esc_html__('Business Hours', 'listgo'),
            array($this, 'businessHoursSettings'),
            'listing',
            'advanced'
        );
    }

    public function saveListing($postID, $post){
        if ( isset($_POST['listgo_bh']) ){
            $toggle = isset($_POST['wiloke_toggle_business_hours']) ? $_POST['wiloke_toggle_business_hours'] : 'disable';
            update_post_meta($postID, 'wiloke_listgo_business_hours', $_POST['listgo_bh']);
            update_post_meta($postID, 'wiloke_toggle_business_hours', $toggle);
        }
    }

    public function businessHoursSettings($post){
        global $wiloke;
        $aOptions = get_post_meta($post->ID, 'wiloke_listgo_business_hours', true);
        $toggle = get_post_meta($post->ID, 'wiloke_toggle_business_hours', true);
	    $toggle = $toggle ? $toggle : 'enable';
        ?>
        <table class="form-table cmb_metabox">
            <tbody>
                <tr class="cmb-type-select">
                    <th><label for="toggle_business_hour" style="text-align: left !important;"><?php esc_html_e('Toggle Business Hour', 'listgo'); ?></label></th>
                    <td>
                        <select id="toggle_business_hour" name="wiloke_toggle_business_hours" class="cmb_select">
                            <option value="enable" <?php selected($toggle, 'enable'); ?>><?php esc_html_e('Enable', 'listgo'); ?></option>
                            <option value="disable" <?php selected($toggle, 'disable'); ?>><?php esc_html_e('Disable', 'listgo'); ?></option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="table-responsive">
            <table class="table table-bordered profile-hour">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Day', 'listgo'); ?></th>
                        <th><?php esc_html_e('Start time', 'listgo'); ?></th>
                        <th><?php esc_html_e('End time', 'listgo'); ?></th>
                        <th><?php esc_html_e('Closed', 'listgo'); ?></th>
                    </tr>
                </thead>

                <?php
                    foreach ( $wiloke->aConfigs['frontend']['listing']['business_hours']['days'] as $key => $day ) :
                        $aValues = isset($aOptions[$key]) ? $aOptions[$key] : $wiloke->aConfigs['frontend']['listing']['business_hours']['default'];
                ?>
                        <tr>
                            <td><?php echo esc_html($day); ?></td>
                            <td>
                                <input type="number" name="listgo_bh[<?php echo esc_attr($key) ?>][start_hour]" max="12" min="0" value="<?php echo strlen($aValues['start_hour']) == 1 ? 0 . esc_attr($aValues['start_hour']) : esc_attr($aValues['start_hour']); ?>"> :
                                <input type="number" name="listgo_bh[<?php echo esc_attr($key) ?>][start_minutes]" max="60" min="0" value="<?php echo strlen($aValues['start_minutes']) == 1 ? 0 . esc_attr($aValues['start_minutes']) : esc_attr($aValues['start_minutes']); ?>"> :
                                <select name="listgo_bh[<?php echo esc_attr($key) ?>][start_format]">
                                    <option value="AM" <?php selected($aValues['start_format'], 'AM'); ?>><?php esc_html_e('AM', 'listgo'); ?></option>
                                    <option value="PM" <?php selected($aValues['start_format'], 'PM'); ?>><?php esc_html_e('PM', 'listgo'); ?></option>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="listgo_bh[<?php echo esc_attr($key) ?>][close_hour]" max="12" min="0" value="<?php echo strlen($aValues['close_hour']) == 1 ? 0 . esc_attr($aValues['close_hour']) : esc_attr($aValues['close_hour']); ?>"> :
                                <input type="number" name="listgo_bh[<?php echo esc_attr($key) ?>][close_minutes]" max="60" min="0" value="<?php echo strlen($aValues['close_minutes']) ? 0 . esc_attr($aValues['close_minutes']) : esc_attr($aValues['close_minutes']); ?>"> :
                                <select name="listgo_bh[<?php echo esc_attr($key) ?>][close_format]">
                                    <option value="AM" <?php selected($aValues['close_format'], 'AM'); ?>><?php esc_html_e('AM', 'listgo'); ?></option>
                                    <option value="PM" <?php selected($aValues['close_format'], 'PM'); ?>><?php esc_html_e('PM', 'listgo'); ?></option>
                                </select>
                            </td>
                            <td>
                                <label for="bh-closed-<?php echo esc_attr($key); ?>" class="input-checkbox">
                                    <input id="bh-closed-<?php echo esc_attr($key); ?>" type="checkbox" name="listgo_bh[<?php echo esc_attr($key) ?>][closed]" value="1" <?php echo isset($aValues['closed']) && $aValues['closed'] === '1' ? 'checked' : ''; ?> value="1">
                                    <span></span>
                                </label>
                            </td>
                        </tr>
                <?php endforeach; ?>

            </table>
        </div>
        <?php
    }
}