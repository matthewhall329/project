<?php
/**
 * WilokeUser Class
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

class WilokeUser
{
    public static $redisKey = 'wiloke_user';
    public static $redisRoles = 'wiloke_roles';
    public static $aListOfUserMeta = array('nickname', 'first_name', 'last_name', 'description', 'wp_capabilities', 'wiloke_cover_image', 'wiloke_profile_picture', 'wiloke_color_overlay', 'wiloke_user_socials', 'wiloke_address', 'wiloke_phone');

    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('personal_options', array($this, 'profile_fields'));
        add_action('personal_options_update', array($this, 'profile_update'));
        add_action('edit_user_profile_update', array($this, 'profile_update'));
        add_action('profile_update', array($this, 'profile_update'), 10, 1);
        add_action('after_switch_theme', array($this, 'updateUserToRedis'));
	    add_action('user_register', array($this, 'addNewUser'), 10, 1);
        add_filter('user_contactmethods', array($this, 'wiloke_add_user_contact_method'), 10, 2);
    }

    //https://developer.wordpress.org/reference/functions/wp_get_user_contact_methods/
    public function wiloke_add_user_contact_method($methods, $user){
        $methods['wiloke_address'] = esc_html__('Address', 'listgo');
        $methods['wiloke_city'] = esc_html__('City', 'listgo');
        $methods['wiloke_state'] = esc_html__('State', 'listgo');
        $methods['wiloke_zipcode'] = esc_html__('Zipcode', 'listgo');
        $methods['wiloke_country'] = esc_html__('Country', 'listgo');
        $methods['wiloke_phone'] = esc_html__('Phone', 'listgo');
        return $methods;
    }

    public function updateUserToRedis(){
        if ( Wiloke::$wilokePredis ){
	        $aUsers = get_users();
	        foreach ($aUsers as $oUser){
	            $oUserDate = get_userdata($oUser->ID);
		        $aUserDate = get_object_vars($oUserDate);
		        self::putUserToRedis($aUserDate);
	        }
        }
    }

    public static function putUserToRedis($aUserData){
        if ( !Wiloke::$wilokePredis ){
            return false;
        }

	    $aUser['ID']            = $aUserData['ID'];
	    $aUser['user_nicename'] = $aUserData['data']->user_nicename;
	    $aUser['user_email']    = $aUserData['data']->user_email;
	    $aUser['user_url']      = $aUserData['data']->user_url;
	    $aUser['user_status']   = $aUserData['data']->user_status;
	    $aUser['display_name']  = $aUserData['data']->display_name;
	    $aUser['role']          = isset($aUserData['roles'][0]) ? $aUserData['roles'][0] : '';
	    $aUser['description']   = get_user_meta($aUser['ID'], 'description', true);
	    $aUserMeta = array();
	    foreach ( self::$aListOfUserMeta as $key ){
		    $aUserMeta[$key] = get_user_meta($aUserData['ID'], $key, true);
        }

	    $aUser['meta'] = $aUserMeta;
	    Wiloke::hSet(self::$redisKey, $aUserData['ID'], $aUser);
	    Wiloke::$wilokePredis->hmSet(self::$redisRoles.'|'.$aUser['role'], $aUser['ID'], json_encode($aUser));
    }

    public function profile_update($user_id)
    {
        if ( current_user_can('edit_user',$user_id) )
        {
	        if ( isset($_POST['wiloke_cover_image']) && !empty($_POST['wiloke_cover_image']) )
	        {
		        update_user_meta($user_id, 'wiloke_cover_image', $_POST['wiloke_cover_image']);
	        }

            if ( isset($_POST['wiloke_profile_picture']) && !empty($_POST['wiloke_profile_picture']) )
            {
                update_user_meta($user_id, 'wiloke_profile_picture', $_POST['wiloke_profile_picture']);
            }

	        if ( isset($_POST['wiloke_color_overlay']) && !empty($_POST['wiloke_color_overlay']) )
	        {
		        update_user_meta($user_id, 'wiloke_color_overlay', $_POST['wiloke_color_overlay']);
	        }

            if ( isset($_POST['wiloke_user_socials']) && !empty($_POST['wiloke_user_socials']) )
            {
                update_user_meta($user_id, "wiloke_user_socials", $_POST['wiloke_user_socials']);
            }

            if ( Wiloke::$wilokePredis ){
	            $aUserData = get_object_vars(get_userdata($user_id));
                self::putUserToRedis($aUserData);
            }

            if ( isset($_POST['wiloke_facebook_api']) || isset($_POST['wiloke_facebook_fanpageid']) ){
	            update_user_meta($user_id, 'wiloke_fb_chat', array(
                    'apiID' => sanitize_text_field($_POST['wiloke_facebook_api']),
                    'fanpageID' => sanitize_text_field($_POST['wiloke_facebook_fanpageid'])
                ));
            }
        }
    }

	public function addNewUser($userID){
		$aUserData = get_object_vars(get_userdata($userID));
		self::putUserToRedis($aUserData);
	}

    public function admin_enqueue_scripts($page)
    {
        if ( isset($page) && ( $page == 'user-edit.php' || $page == 'profile.php' ) )
        {
	        wp_enqueue_script('spectrum', WILOKE_AD_ASSET_URI . 'js/spectrum.js', array('jquery'), false, true);
	        wp_enqueue_style('spectrum', WILOKE_AD_ASSET_URI . 'css/spectrum.css', array(), null);
            // Check Wiloke Post Format plugin is activating or not
            if (!wp_script_is('wiloke_post_format', 'enqueued')) {
                wp_enqueue_media();
                wp_enqueue_script('wiloke_post_format_ui', WILOKE_AD_SOURCE_URI . 'js/wiloke_post_format.js', array('jquery'), false, true);
                wp_enqueue_style('wiloke_post_format_ui', WILOKE_AD_SOURCE_URI . 'css/wiloke_post_format.css');

                wp_enqueue_script('wiloke_taxonomy', WILOKE_AD_SOURCE_URI . 'js/taxonomy.js', array('jquery', 'wiloke_post_format_ui'), false, true);
            }
        }
    }

    public function profile_fields( $user )
    {
        $coverImg   = get_user_meta($user->ID, 'wiloke_cover_image', true );
        $colorOverlay= get_user_meta($user->ID, 'wiloke_color_overlay', true );
        $avatar     = get_user_meta($user->ID, 'wiloke_profile_picture', true );
        $aSocials   = get_user_meta($user->ID, 'wiloke_user_socials', true);
        $aFacebookChat   = get_user_meta($user->ID, 'wiloke_fb_chat', true);
	    $aFacebookChat = wp_parse_args(
		    $aFacebookChat,
            array(
                'fanpageID' => '',
                'apiID' => ''
            )
        );
        ?>
        <table class="form-table">
            <tbody>
                <tr class="user-rich-editing-wrap">
                    <?php WilokeHtmlHelper::wiloke_render_media_field( array('id'=>'wiloke_cover_image', 'value'=>$coverImg, 'name'=>esc_html__('Cover Image', 'listgo'), 'description'=>esc_html__('Upload a cover image for your profile page', 'listgo'))); ?>
                    <?php WilokeHtmlHelper::wiloke_render_colorpicker_field( array('id'=>'wiloke_color_overlay', 'value'=>$colorOverlay, 'name'=>esc_html__('Color Overlay', 'listgo'), 'description'=>esc_html__('Set a blur color on the cover image', 'listgo'))); ?>
                </tr>
            </tbody>
        </table>

        <table class="form-table">
            <tbody>
                <tr class="user-rich-editing-wrap">
                    <?php WilokeHtmlHelper::wiloke_render_media_field( array('id'=>'wiloke_profile_picture', 'value'=>$avatar, 'name'=>esc_html__('Profile Picture', 'listgo'), 'description'=>esc_html__('We strongly recommend using an image of 400x400px', 'listgo'))); ?>
                </tr>
            </tbody>
        </table>

        <table class="form-table">
            <thead><?php esc_html_e('Social Networks', 'listgo'); ?></thead>
            <tbody>
                <?php
                foreach ( WilokeSocialNetworks::$aSocialNetworks as $social ){
                    $aField['name']  = ucfirst($social);
                    $aField['id']    = 'wiloke_user_socials['.$social.']';
                    $aField['value'] = isset($aSocials[$social]) ? $aSocials[$social] : '';
                    WilokeHtmlHelper::wiloke_render_text_field($aField);
                }
                ?>
            </tbody>
        </table>

        <table class="form-table">
            <thead><?php esc_html_e('Facebook Chat', 'listgo'); ?></thead>
            <tbody>
                <tr class="user-rich-editing-wrap">
	                <?php WilokeHtmlHelper::wiloke_render_text_field( array('id'=>'wiloke_facebook_api', 'value'=>$aFacebookChat['apiID'], 'name'=>esc_html__('Facebook APP API', 'listgo'))); ?>
	                <?php WilokeHtmlHelper::wiloke_render_text_field( array('id'=>'wiloke_facebook_fanpageid', 'value'=>$aFacebookChat['fanpageID'], 'name'=>esc_html__('Facebook Fanpage ID', 'listgo'))); ?>
                </tr>
            </tbody>
        </table>

        <?php
    }
}