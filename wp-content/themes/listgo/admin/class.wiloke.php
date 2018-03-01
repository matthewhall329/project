<?php
/**
 * @package Wiloke Framework
 * @category Core
 * @author WilokeTeam
 */

if ( !defined('ABSPATH') )
{
    exit; // Exit If accessed directly
}

if ( !class_exists('Wiloke') ) :
    /**
     * Main Wiloke Class
     * @class Wiloke
     * @version 1.0.2
     */

    class Wiloke{

	    /**
	     * Prefix
	     * @since 1.0
	     */
	    public static $prefix = 'wiloke_listgo_';

        /**
         * First time Installation theme theme
         * @since 1.0
         */
        public static $firsTimeInstallation = 'wiloke_first_time_theme_installation';

        /**
         * @var string
         */
        public $version = '1.6.4.0';

        /**
         * @var string
         * @since 1.0.1
         */
        public static $wilokeDesignPortfolioDemos = 'wiloke_design_portfolio_demos';

        /**
         * @var $aConfigs - an Array contains all of configs
         */

        /**
         * @var Wiloke The single instance of the class
         * @since 1.0
         */
        protected static $_instance = null;

        /**
         * @var WilokeThemeOptions $aThemeOptions
         */
        public $aThemeOptions = null;

        /**
         * Caching User Data
         * @since 1.0
         */
        public static $aUsersData = array();

        /**
         * Main Wiloke Instance
         *
         * Ensures only one instance of Wiloke is loaded or can be loaded.
         *
         * @since 1.0
         * @var static
         * @see Wiloke()
         * @return Wiloke - Main Instance
         */
        public static function instance()
        {
            if ( is_null(self::$_instance) )
            {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * An instance of WilokeLoader class
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        protected $_loader;

        /**
         * An instance of WO_Ajax class
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        protected $_ajax;


        /**
         * He knows everything about WO_ThemeOptions
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        protected $_themeOptions;

        /**
         * Register Sidebar
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        protected $_registerSidebar;

        /**
         * An instance of WO_AdminGeneral
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        protected $_adminGeneral;

        /**
         * An instance of WilokePublic
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        protected $_public;
        protected $instSearchSystem;
        public $frontEnd;

        /*
         * WooCommerce
         * @since 1.1.2
         */
        public $woocommerce;

        /**
         * An instance of Mobile_Detect
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        public static $mobile_detect;

        /**
         * An instance of Mobile_Detect
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        public static $public_path;

        /**
         * An instance of WO_Taxonomy
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        public $_taxonomy;

        /**
         * An instance of Mobile_Detect
         *
         * @since 1.0
         * @access protected
         * @return object
         */
        public static $public_url;

        /**
         * Predis Instance
         *
         * @since 1.0
         * @access public
         * @return object
         */
        public static $wilokePredis = null;

        public static $wilokeRedisTimeCaching = 86400;

        /**
         * List of errors
         *
         * @since 1.0
         * @static
         * @return array
         */
        public static $list_of_errors;

        /**
         * Caching All terms here
         * @since 1.0.1
         */
        public static $aWilokeTerms;

	    /**
	     * Caching Post Terms Here
	     * @since 1.0.1
	     */
	    public static $aPostTerms;

        /**
         * Caching post meta
         * @since 1.0.1
         */
        public static $aPostMeta;

        /**
         * Variable Caching
         * @since 1.0.3
         */
        public static $aVariableCaching = array();

        public static $aAllParentTerms = array();

        /**
         * Register autoload
         * @since 1.0.1
         */
        public static function autoload($name){
            if ( strpos($name, 'Wiloke') === false ){
                return;
            }

            $parseFileName = 'class.' . $name . '.php';

            if ( is_file( get_template_directory() . '/admin/inc/' . $parseFileName ) ) {
                include  get_template_directory() . '/admin/inc/' . $parseFileName;
            }elseif ( is_file( get_template_directory() . '/admin/public/' . $parseFileName ) ){
                include get_template_directory() . '/admin/public/' . $parseFileName;
            }
        }

        /**
         * Wiloke Constructor.
         */
        public function __construct()
        {
            self::$public_path = get_template_directory() . '/admin/public/';
            self::$public_url  = get_template_directory_uri() . '/admin/public/';

            do_action('wiloke_action_before_framework_init');

            $this->defineConstants();
            $this->configs();
            $this->predis_init();
            $this->include_modules();

            do_action('wiloke_action_after_framework_loaded');
            add_action('after_setup_theme', array($this, 'run_after_theme_loaded'));
            add_action('after_switch_theme', array($this, 'after_switch_theme'));
            add_action('updated_postmeta', array($this, 'updatePostMetaRedisCaching'), 10, 4);
        }

	    /**
	     * Get Options
	     * @since 1.0.3
	     *
	     */
	    public static function getOption($name, $isReturnArray=true){
	        if ( isset(self::$aVariableCaching[$name]) ){
	            return self::$aVariableCaching[$name];
            }

//		    if ( Wiloke::$wilokePredis && Wiloke::$wilokePredis->exists($name) ){
//			    $val = Wiloke::$wilokePredis->get($name);
//			    if ( empty($val) ){
//				    $val = get_option($name);
//				    Wiloke::$wilokePredis->set($name, json_encode($val));
//			    }
//		    }else{
//		        $val = get_option($name);
//            }
		    $val = get_option($name);
            if ( empty($val) ){
		        return false;
            }

		    if ( is_array($val) ){
		        return $val;
            }

		    $val = $isReturnArray ? json_decode($val, true) : json_decode($val);
		    self::$aVariableCaching[$name] = $val;
		    return $val;
	    }

	    public static function getTaxonomyHierarchy( $taxonomy, $parent = 0, $isFocus=false ) {
		    // only 1 taxonomy
		    $taxonomy = is_array( $taxonomy ) ? array_shift( $taxonomy ) : $taxonomy;
		    // get all direct decendants of the $parent
            if ( !$isFocus ){
                if ( empty(self::$aAllParentTerms) ){
	                self::$aAllParentTerms = get_terms(
		                array(
			                'taxonomy'   => $taxonomy,
			                'parent'     => $parent,
			                'hide_empty' => false
		                )
	                );
	                $aTerms = self::$aAllParentTerms;;
                }else{
	                $aTerms = self::$aAllParentTerms;
                }
            }else{
	            $aTerms = get_terms(
		            array(
			            'taxonomy'   => $taxonomy,
			            'parent'     => $parent,
			            'hide_empty' => false
		            )
	            );
            }

		    // prepare a new array.  these are the children of $parent
		    // we'll ultimately copy all the $terms into this new array, but only after they
		    // find their own children
		    $aResult = array();
		    // go through all the direct decendants of $parent, and gather their children
		    foreach ( $aTerms as $oTerm ){
			    // recurse to get the direct decendants of "this" term
			    $aOptions = Wiloke::getOption('_wiloke_cat_settings_'.$oTerm->term_id);
			    $oTerm->icon = isset($aOptions['map_marker_image']) ? $aOptions['map_marker_image'] : '';
			    $oTerm->value = $oTerm->name;
			    $aResult[] = $oTerm;
			    $aChildren = self::getTaxonomyHierarchy( $taxonomy, $oTerm->term_id, true );
			    foreach ($aChildren as $oChild) {
				    $aOptions = Wiloke::getOption('_wiloke_cat_settings_'.$oChild->term_id);
				    $oChild->icon = isset($aOptions['map_marker_image']) ? $aOptions['map_marker_image'] : '';
				    $oChild->value = $oChild->name;
				    $aResult[] = $oChild;

				    if ( !empty($oChild->children) ){
					    foreach ($oChild->children as $oGrandChild) {
						    $aOptions = Wiloke::getOption('_wiloke_cat_settings_'.$oGrandChild->term_id);
						    $oGrandChild->icon = isset($aOptions['map_marker_image']) ? $aOptions['map_marker_image'] : '';
						    $oGrandChild->value = $oGrandChild->name;
						    $aResult[] = $oGrandChild;
					    }
				    }
			    }
		    }

		    // send the results back to the caller
		    return $aResult;
	    }

	    public static function updateOption($name, $aVal){
		    $val = json_encode($aVal);
		    if ( Wiloke::$wilokePredis ){
			    Wiloke::$wilokePredis->set($name, $val);
		    }
		    update_option($name, $val);
	    }

	    public static function setTermChildren($termID, $taxonomy,array $aChildren){
	        $name = self::$prefix.'_'.$taxonomy.'_'.$termID.'_our_children';
		    $aChildren = json_encode($aChildren);
		    if ( Wiloke::$wilokePredis ){
			    Wiloke::$wilokePredis->set($name, $aChildren);
		    }
		    update_option($name, $aChildren);
        }

	    public static function getTermChildren($termID, $taxonomy){
		    $name = self::$prefix.'_'.$taxonomy.'_'.$termID.'_our_children';
		    if ( Wiloke::$wilokePredis && Wiloke::$wilokePredis->exists($name) ){
			    $aTermChildren = Wiloke::$wilokePredis->get($name);

		    }else{
			    $aTermChildren = get_option($name);
            }

            if ( !empty($aTermChildren) && ($aTermChildren[0] !== -1) ){
		        return json_decode($aTermChildren, true);
            }

            $aTermChildren = get_term_children($termID, $taxonomy);
            if ( empty($aTermChildren) || is_wp_error($aTermChildren) ){
                $aTermChildren = array(-1);
            }

            self::setTermChildren($termID, $taxonomy, $aTermChildren);
            return $aTermChildren;
	    }

	    /**
	     * Get Term Option
         * @since 1.0
	     */
	    public static function getTermOption($termID){
		    return self::getOption('_wiloke_cat_settings_'.$termID, true);
        }

	    /**
	     * Set Transient
         * @since 1.0.3
         *
         * @param $duration int - minutes
         * @param $aVal array
	     */
	    public static function setTransient($name, $aVal, $duration){
		    $duration = absint($duration);
	        $val = json_encode($aVal);
            if ( Wiloke::$wilokePredis ){
                Wiloke::$wilokePredis->setEx($name, $duration, $val);
            }
            set_transient($name, $val, $duration);
        }

        /**
         * Auto Convert Name To Slug
         * @since 1.0.4
         */
        public static function convertNameToSlug($slug){
	        return sanitize_title($slug);
        }

        public static function deleteTransient($name){
	        if ( Wiloke::$wilokePredis ){
		        Wiloke::$wilokePredis->del($name);
	        }
            delete_transient($name);
        }

	    public static function getTransient($name, $isReturnArray=true){
		    if ( isset(self::$aVariableCaching[$name]) ){
			    return self::$aVariableCaching[$name];
		    }

		    if ( Wiloke::$wilokePredis ){
			    $val = Wiloke::$wilokePredis->get($name);
		    }else{
			    $val = get_transient($name);
		    }

		    if ( empty($val) ){
		        return false;
            }

		    $val = $isReturnArray ? json_decode($val, true) : json_decode($val);
		    self::$aVariableCaching[$name] = $val;
		    return $val;
	    }

        /**
         * After Switch to Wiloke's Theme
         * @since 1.0
         */
        public function after_switch_theme(){
            if ( get_option(self::$firsTimeInstallation) ){
                update_option(self::$firsTimeInstallation, wp_get_theme()->get('Name'));
            }
        }

        /**
         * Set session
         * @since 1.0.2
         */
	    public static function sessionStart(){
		    $sessionID = session_id();
		    if ( empty($sessionID) ){
			    session_start();
		    }
	    }

        public static function setSession($name, $value){
            $sessionID = session_id();
            if ( empty($sessionID) ){
	            session_start();
            }

            $_SESSION[$name] = $value;
        }

        public static function getSession($name){
            if ( !isset($_SESSION[$name]) ){
                return false;
            }

            return $_SESSION[$name];
        }

        public static function destroySession(){
            session_destroy();
        }

        public static function removeSession($name){
            if ( isset($_SESSION[$name]) ){
                unset($_SESSION[$name]);
            }
        }


        /**
         * Set Temporary session
         * @since 1.0
         */
        public static function setTemporarySession($key, $value){
            setcookie($key, $value, time()+3600, '/');
        }

        /**
         * Set Temporary session
         * @since 1.0
         */
        public static function getTemporarySession($key, $isEcho=false){
            $message = isset($_COOKIE[$key]) ? $_COOKIE[$key] : '';
            setcookie($key, 'delete', time()-3600, '/');

            if ( !$isEcho ){
                return $message;
            }else{
                self::wiloke_kses_simple_html($message);
            }
        }

        /**
         * Init Predis
         * @since 1.0.3
         */
        public function predis_init(){
            if ( defined('WILOKE_TURN_OFF_REDIS') && WILOKE_TURN_OFF_REDIS ){
	            self::$wilokePredis = false;
	            return false;
            }

            if ( is_file( WP_PLUGIN_DIR . '/wiloke-service/admin/predis-1x/vendor/autoload.php' ) ){
                $dir = WP_PLUGIN_DIR . '/wiloke-service/admin/predis-1x/vendor/autoload.php';
                $profile = '3.2';
            }else if ( is_file( WP_PLUGIN_DIR . '/wiloke-service/admin/predis/autoload.php' ) ){
	            $dir = WP_PLUGIN_DIR . '/wiloke-service/admin/predis/autoload.php';
	            $profile = '2.8';
            }

            if ( isset($dir) ) {
                include $dir;
                $aConfigs = array(
	                'name'   => defined('WILOKE_REDIS_KEY') ? WILOKE_REDIS_KEY : self::$prefix . wp_get_theme()->get('template'),
                    'scheme' => 'tcp',
                    'host'   => '127.0.0.1',
                    'port'   => 6379
                );

                try {
                    $redis = new Predis\Client($aConfigs, array('profile'=>$profile));
                    $redis->connect();
                    self::$wilokePredis = $redis;
                }catch(Predis\Connection\ConnectionException $e){
	                self::$wilokePredis = false;
                }
            }
        }

        public static function setRedisCache($key, $value, $echoBeforeSet=false){
            if ( $echoBeforeSet ) {
                Wiloke::wiloke_kses_simple_html($value);
            }

            if ( self::$wilokePredis ){
                if ( is_object($value) ) {
                    $value = get_object_vars($value);
                }elseif ( is_string($value) ) {
                    $value = array($value);
                }

                if ( is_array($value) ) {
                    $value = serialize($value);
                }

                self::$wilokePredis->setEx($key, self::$wilokeRedisTimeCaching, $value);
            }

            return false;
        }

        public static function getRedisCache($key, $isString = false){
            if ( self::$wilokePredis ){
                $content = self::$wilokePredis->get($key);

                if ( empty($content) ) {
                    return false;
                }

                $content =  unserialize($content);

                if ( $isString || ( (count($content) === 1) && key($content) === 0 ) ) {
                    $content = $content[0];
                }

                return $content;
            }

            return false;
        }

        public static function hGet($key, $field, $isReturnArr = false){
            if ( !self::$wilokePredis ){
                return false;
            }

            $get = self::$wilokePredis->hGet($key, $field);
            return !empty($get) ? json_decode($get, $isReturnArr) : false;
        }

	    public static function hSet($key, $field, $aData){
		    if ( !self::$wilokePredis ){
			    return false;
		    }

            self::$wilokePredis->hSet($key, $field, json_encode($aData));
	    }

	    public static function hDel($key, $field, $aData){
		    if ( !self::$wilokePredis ){
			    return false;
		    }

		    self::$wilokePredis->hdel($key, $field);
	    }

	    /**
         * Register hooks after theme loaded
         * @since 1.0
         */
        public function run_after_theme_loaded()
        {
            $this->run_modules();
            $this->general_hooks();
            $this->admin_hooks();
            $this->public_hooks();
            $this->run();
        }

	    public static function timeStampToMinutes($timeStamp){
		    return $timeStamp/60;
	    }

        public static function timeStampToHours($timeStamp){
            return self::timeStampToMinutes($timeStamp)/60;
        }

	    public static function timeStampToDays($timeStamp){
		    return self::timeStampToHours($timeStamp)/24;
	    }

        /**
         * Define Wiloke Constants
         */
        public function defineConstants()
        {
            $this->define('WILOKE_THEME_URI', trailingslashit(get_template_directory_uri()));
            $this->define('WILOKE_THEME_DIR', trailingslashit(get_template_directory()));

            $this->define('WILOKE_AD_REDUX_DIR', trailingslashit(WILOKE_THEME_DIR . 'admin/inc/redux-extensions'));
            $this->define('WILOKE_AD_REDUX_URI', trailingslashit(WILOKE_THEME_URI . 'admin/inc/redux-extensions'));

            $this->define('WILOKE_AD_SOURCE_URI', trailingslashit(get_template_directory_uri()) . 'admin/source/');
            $this->define('WILOKE_AD_ASSET_URI', trailingslashit(get_template_directory_uri()) . 'admin/asset/');

            $this->define('WILOKE_INC_DIR', trailingslashit(get_template_directory() . '/admin/inc/'));
            $this->define('WILOKE_PUBLIC_DIR', trailingslashit(get_template_directory() . '/admin/public/'));
            $this->define('WILOKE_TPL_BUILDER', trailingslashit(get_template_directory() . '/template-builder/'));
            $this->define('WILOKE_THEMESLUG', 'listgo');
            $this->define('WILOKE_THEMENAME', 'ListGo');
            $this->define('WILOKE_THEMEVERSION', $this->version);
            $this->define('TEXT_DOMAIN', 'listgo');
        }

        /**
         * Includes
         */
        public function configs()
        {
            /**
             * Wiloke Configs
             */
            $aListOfConfigs = glob(get_template_directory().'/configs/*.php');

            foreach ( $aListOfConfigs as $file )
            {
                $parsekey = explode('/', $file);
                $parsekey = end($parsekey);
                $parsekey = str_replace(array('config.', '.php'), array('', ''), $parsekey);
                $this->aConfigs[$parsekey] = include $file;
            }

        }

        public function include_modules()
        {
            /**
             * Including Classes
             */
            do_action('wiloke_admin_hook_before_include_modules');
            include WILOKE_INC_DIR . 'func.visualComposer.php';

            // Front-end
            include WILOKE_INC_DIR . 'lib/Mobile-Detect/Mobile_Detect.php';

            do_action('wiloke_admin_hook_after_include_modules');
        }

        /**
         * Initialize Modules
         * @since 1.0
         */
        public function run_modules()
        {
            $this->_loader       = new WilokeLoader();
            $this->_public       = new WilokePublic();
            $this->instSearchSystem = new WilokeSearchSystem();
            new WilokeListingLayout();
            new WilokeReview();
            new WilokeListgoMap();
            new WilokeFavorite();
            $this->frontEnd      = $this->_public;

            new WilokeAlert;
            new WilokeSocialNetworks;
            new WilokeInstallPlugins;
            new WilokeHtmlHelper;
            new WilokeNavMenu();

            if ( !$this->kindofrequest('admin') )
            {
                self::$mobile_detect = new Mobile_Detect();
                new WilokeHead;
                new WilokeFrontPage;
            }

            $this->_themeOptions    = new WilokeThemeOptions();
            $this->_adminGeneral    = new WilokeAdminGeneral();

            new WilokeAdminBar;
            new WilokeReduxExtensions();
            new WilokeMetaboxes();
            $this->_registerSidebar = new WilokeWidget();
            $this->_taxonomy        = new WilokeTaxonomy();
            new WilokeContactForm();
            new WilokeUser();
//            new WilokeInfiniteScroll();

            $this->woocommerce = new WilokeWooCommerce();
            $this->_ajax            = new WilokeAjax();
            new WilokePostTypes;

            if ( class_exists('WilokeService') && class_exists('WilokeCaching') ){
                if ( !empty(WilokeCaching::$aOptions) ) {
                    self::$wilokeRedisTimeCaching = WilokeCaching::$aOptions['redis']['caching_interval'];
                }
            }
        }

        /**
         * Do you meet him ever?
         * @params $className
         */
        public function isClassExists($className, $autoMessage=true)
        {
            if ( !class_exists($className) )
            {
                if ( $this->kindofrequest('admin') )
                {
                    if ( $autoMessage )
                    {
                        $message = esc_html__('Sorry', 'listgo') . $className . esc_html__('Class doesn\'t exist!', 'listgo');
                    }else{
                        $message = true;
                    }
                }else{
                    $message = false;
                }


                throw new Exception($message);

            }else{
                return true;
            }
        }

        /**
         * Check this file whether it exists or not?
         */
        public function isFileExists($dir, $file)
        {
            if ( file_exists($dir.$file) )
            {
                return true;
            }else{
                $message = sprintf( __('The file with name %s doesn\'t exist. Please open a topic via support.wiloke.com to report this problem.', 'listgo'), $file );
                self::$list_of_errors['error'][] = $message;
            }
        }

        /**
         * Define constant if not already set
         * @param string $name
         * @param string|bool $value
         */
        public function define($name, $value)
        {
            if ( !defined($name) )
            {
                define($name, $value);
            }
        }

        public static function display_number($count, $zero, $one, $more)
        {
            $count = absint($count);

            switch ($count)
            {
                case 0:
                    $count = $zero;
                    break;
                case 1:
                    $count = $count . ' ' . $one;
                    break;
                default:
                    $count = $count . ' ' . $more;
                    break;
            }

            return $count;
        }

        /**
         * What kind of request is that?
         * @param $needle
         * @return bool
         */
        public function kindofrequest($needle='admin')
        {
            switch ( $needle )
            {
                case 'admin':
                    return is_admin() ? true : false;
                    break;

                default:
                    if ( !empty($needle) )
                    {
                        global $pagenow;

                        if ( $pagenow === $needle )
                            return true;
                    }

                    return false;
                    break;
            }
        }

        /**
         * Get terms by post id
         * @postId : integer
         * $taxonomy: string/array
         */
        public static function wiloke_get_terms_by_post_id($postID, $taxonomy='category')
        {
            return wp_get_post_terms($postID, $taxonomy);
        }

        /**
         * Get Term Slug For Portfolio
         * @since 1.0.1
         * @postID: integer
         * $taxonomy: string/array
         */
        public static function wiloke_terms_slug($postID, $taxonomy='category', $separated=' ', $oTerms=null)
        {
            $oTerms = is_null($oTerms) ? wp_get_post_terms($postID, $taxonomy) : $oTerms;

            $slug = '';

            if ( !empty($oTerms) && !is_wp_error($oTerms) )
            {
                foreach ( $oTerms as $oTerm )
                {
                    self::setTermsCaching($taxonomy, array($oTerm->term_id=>$oTerm));
                    $slug .= $oTerm->slug . $separated;
                }

                $slug = trim($slug, $separated);
            }

            return $slug;
        }

	    /**
	     * Get User Avatar
	     * @since 1.0
	     */
	    public static function getUserAvatar($userID, $aUserInfo=null, $size=''){
		    $aUserInfo = !empty($aUserInfo) ? $aUserInfo : self::getUserMeta($userID);
		    $size = !empty($size) ? $size :  array(65, 65);
		    $avatar = isset($aUserInfo['meta']['wiloke_profile_picture']) && !empty($aUserInfo['meta']['wiloke_profile_picture']) ? wp_get_attachment_image_url($aUserInfo['meta']['wiloke_profile_picture'],$size) : get_template_directory_uri() . '/img/profile-picture.jpg';
		    return $avatar;
	    }

	    /**
	     * Get User Info
	     * @since 1.0
	     */
	    public static function getUserMeta($userID, $field=''){
	        if ( empty($userID) ){
	            return false;
            }

		    if ( !isset($aUser) || empty($aUser) ){

			    if ( !empty($field) ){
				    if ( isset(self::$aUsersData[$userID]) ){
					    return self::$aUsersData[$userID][$field];
				    }else{
					    return get_user_meta($userID, $field, true);
				    }
			    }

			    if ( !isset(self::$aUsersData[$userID]) ){
				    $aWhatINeed = array('nickname', 'first_name', 'last_name', 'description', 'wp_capabilities', 'wiloke_cover_image', 'wiloke_color_overlay', 'wiloke_profile_picture', 'wiloke_user_socials', 'wiloke_address', 'wiloke_phone', 'wiloke_state', 'wiloke_city', 'wiloke_zipcode', 'wiloke_country');
				    $oUser = get_userdata($userID);
				    $aUser['user_email']    = isset($oUser->data->user_email) ? $oUser->data->user_email : '';

				    $aUser['user_nicename'] = isset($oUser->data->user_nicename) ? $oUser->data->user_nicename : '';
				    $aUser['user_website']  = isset($oUser->data->user_url) ? $oUser->data->user_url : '';
				    $aUser['nickname']      = isset($oUser->data->display_name) ? $oUser->data->display_name : '';
				    $aUser['display_name']  = isset($oUser->data->display_name) ? $oUser->data->display_name : '';
				    $aUser['ID']            = $oUser->data->ID;

				    $aUser['role']          = isset($oUser->roles[0]) ? $oUser->roles[0] : '';
				    $aUser['description']   = get_user_meta($userID, 'description', true);
				    foreach ( $aWhatINeed as $key ){
					    $aUser['meta'][$key] = get_user_meta($userID, $key, true);
				    }
				    self::$aUsersData[$userID] = $aUser;
			    }else{
				    $aUser = self::$aUsersData[$userID];
			    }
		    }else{
			    $aUser = json_decode($aUser, true);
			    if ( !empty($field) ){
				    return isset($aUser[$field]) ? $aUser[$field] : $aUser['meta'][$field];
			    }
		    }

		    return $aUser;
	    }

        /**
         * Caching Post Meta
         * @since 1.0
         */
        public static function setPostMetaCaching($postID, $key, $data, $isNotDecode = false){
            if ( isset(self::$aPostMeta[$postID]) && isset(self::$aPostMeta[$postID][$key]) ) {
                return;
            }

            self::$aPostMeta[$postID][$key] = !$isNotDecode ? json_encode($data) : $data;
            if ( self::$wilokePredis ){
                self::$wilokePredis->hSet($key, $postID, self::$aPostMeta[$postID][$key]);
            }
        }

        /**
         * Get Post Meta
         * @since 1.0
         */
        public static function getPostMetaCaching($postID, $key, $isNotDecode=false){
            if ( empty($postID) ){
                return false;
            }

//	        if ( self::$wilokePredis ){
//		        $aResult = self::$wilokePredis->hGet($key, $postID);
//		        if ( $isNotDecode ){
//		            return $aResult;
//                }
//
//		        if ( !empty($aResult) ){
//		            return !$isNotDecode ? json_decode($aResult, true) : $aResult;
//                }
//	        }

//	        if ( isset(self::$aPostMeta[$postID]) && isset(self::$aPostMeta[$postID][$key]) ) {
//		        return !$isNotDecode ? json_decode(self::$aPostMeta[$postID][$key], true) : self::$aPostMeta[$postID][$key];
//	        }
//

            if ( !$data = get_post_meta($postID, $key, true) ) {
                return false;
            }

//            self::setPostMetaCaching($postID, $key, $data, $isNotDecode);

            return $data;
        }

        public function updatePostMetaRedisCaching($metaID, $objectID, $metaKey, $metaValue){
            $metaValue = maybe_unserialize($metaValue);

            if ( !is_array($metaValue) ){
                return false;
            }

	        if ( strpos($metaKey, '_') !== 0  ){
	            self::setPostMetaCaching($objectID, $metaKey, $metaValue);
	        }
        }

        /**
         * Get Post Terms
         * @since 1.0.2
         */
        public static function getPostTerms($object, $taxonomy, $isTopParent=false){
            $oTerms = null;
            if ( is_numeric($object) ){
                $post = get_post($object);
            }else{
                $post = $object;
            }

	        if ( empty($post) ){
		        return false;
	        }
	        $oTerms = null;
	        
            if ( self::$wilokePredis && self::$wilokePredis->exists(self::$prefix."$post->post_type|termsinpost") ){
	            $oTerms = self::$wilokePredis->hGet(self::$prefix."$post->post_type|termsinpost", $post->ID.'_'.$taxonomy);
            }
	       
            $aTopParentIDs = array();
	        $aTopParents = array();
            $aAllTermIDs = array();
            if ( empty($oTerms) ){
                if ( isset(self::$aPostTerms[$post->ID]) && isset(self::$aPostTerms[$post->ID][$taxonomy]) ){
                    $oTerms = json_decode(self::$aPostTerms[$post->ID][$taxonomy]);
                }else{
	                $oTerms = wp_get_post_terms($post->ID, $taxonomy);
	                if ( !empty($oTerms) && !is_wp_error($oTerms) ){
	                    foreach ( $oTerms as $key => $oTerm ){
		                    $oTerm = get_object_vars($oTerm);
		                    $oTerm['link'] = get_term_link($oTerm['term_id']);
		                    $oTerm = (object)$oTerm;
		                    $oTerms[$key] = $oTerm;

		                    if ( $isTopParent ){
			                    $oTopParent = self::getTermTopMostParent($oTerm, $taxonomy);
			                    if ( $oTopParent && !in_array($oTopParent->term_id, $aTopParentIDs) && !in_array($oTopParent->term_id, $aAllTermIDs) ) {
				                    $aTopParentIDs[] = $oTopParent->term_id;
				                    $aTopParents[$oTopParent->term_id] = $oTopParent;
			                    }
                            }
		                    $aAllTermIDs[] = $oTerm->term_id;
                        }
		                self::setPostTerms($post, $taxonomy, $oTerms);
                    }else{
	                    return false;
                    }
                }
            }else{
	            $oTerms = json_decode($oTerms);
	            if ( $isTopParent ){
	                foreach ( $oTerms as $oTerm ){
		                $oTopParent = self::getTermTopMostParent($oTerm, $taxonomy);
		                if ( $oTopParent && !in_array($oTopParent->term_id, $aTopParentIDs) && !in_array($oTopParent->term_id, $aAllTermIDs) ) {
			                $aTopParentIDs[] = $oTopParent->term_id;
			                $aTopParents[$oTopParent->term_id] = $oTopParent;
		                }
		                $aAllTermIDs[] = $oTerm->term_id;
                    }
	            }
            }

	        if ( !empty($aTopParentIDs) ){
		        foreach ( $aTopParentIDs as $termID ){
			        if ( !in_array($termID, $aAllTermIDs) ){
				        $oTerms[$termID] = $aTopParents[$termID];
			        }
		        }
	        }
            return $oTerms;
        }

	    // determine the topmost parent of a term
	    public static function getTermTopMostParent($oTerm, $taxonomy){
		    // climb up the hierarchy until we reach a term with parent = '0'
            if ( empty($oTerm->parent) ){
                return false;
            }

            $oParent = get_term_by( 'term_taxonomy_id', $oTerm->parent, $taxonomy);
		    if ( is_wp_error($oParent) || empty($oParent) ){
	            return false;
            }

		    $oParent = get_object_vars($oParent);
		    $oParent['link'] = get_term_link($oParent['term_id']);
		    $oParent = (object)$oParent;
            return $oParent;
        }

        public static function setPostTerms($post, $taxonomy, $aData){
            self::$aPostTerms[$post->ID][$taxonomy] = json_encode($aData);
            if ( Wiloke::$wilokePredis ){
	            Wiloke::$wilokePredis->hSet(Wiloke::$prefix."$post->post_type|termsinpost", $post->ID.'_'.$taxonomy, json_encode($aData));
            }
        }

        /**
         * Get Term Name For Portfolio
         * @since 1.0.1
         * @postID: integer
         * $taxonomy: string/array
         */
        public static function wiloke_terms_name($postID, $taxonomy='category', $separated=', ', $oTerms=null)
        {
            $oTerms = is_null($oTerms) ? wp_get_post_terms($postID, $taxonomy) : $oTerms;

            $slug = '';

            if ( !empty($oTerms) && !is_wp_error($oTerms) )
            {
                foreach ( $oTerms as $oTerm )
                {
                    self::setTermsCaching($taxonomy, array($oTerm->term_id=>$oTerm));
                    $slug .= $oTerm->name . $separated;
                }

                $slug = trim($slug, $separated);
            }

            return $slug;
        }

        /**
         * Set Terms Caching
         *
         * @aData Array
         * @taxonomy String - Taxonomy key
         * @since 1.0.1
         */
        public static function setTermsCaching($taxonomy, $aData){
            if ( empty($taxonomy) || empty($aData) ){
                return;
            }

            foreach ( $aData as $key => $val ) {
                self::$aWilokeTerms[$taxonomy][$key] = json_encode($val);
            }
        }

        /**
         * Get Term Caching
         *
         * @since 1.0.1
         * @taxonomy Taxonomy Key
         * @termID Array
         */
        public static function getTermCaching($taxonomy, $termID=null, $aOtherArgs=array()){
	        $hasTermID = !empty($termID);
	        $oTermCaching = null;

	        if ( $hasTermID ) {
		        if ( is_array($termID) ) {
			        foreach ( $termID as $id ){
				        if ( isset(self::$aWilokeTerms[$taxonomy][$id]) ) {
					        $oTermCaching[] = json_decode(self::$aWilokeTerms[$taxonomy][$id]);
					        $deleteHim = array_search($id, $termID);
					        unset($termID[$deleteHim]);
				        }
			        }
		        }else{
			        if ( isset(self::$aWilokeTerms[$taxonomy][$termID]) ) {
				        $oTermCaching = json_decode(self::$aWilokeTerms[$taxonomy][$termID]);
				        $termID = null;
			        }
		        }

		        if ( empty($termID) ) {
			        return (object)$oTermCaching;
		        }
            }

            $aArgs = array(
	            'taxonomy'   => $taxonomy,
	            'hide_empty' => true
            );

            if ( !empty($aOtherArgs) ){
                $aArgs = array_merge($aArgs, $aOtherArgs);
            }

            if ( !empty($termID) ){
                $aArgs['include'] = $termID;
            }

	        $oTerms = get_terms($aArgs);

            if ( !empty($oTerms) && !is_wp_error($oTerms) ){
                foreach ( $oTerms as $key => $oTerm ) {
	                $oTerm = get_object_vars($oTerm);
	                $oTerm['link'] = get_term_link($oTerm['term_id']);

	                /**
	                 * @since 1.0.2
                     * Allowing filter get term caching
	                 */
	                $oTerm = apply_filters('wiloke/admin/get_term_caching', $oTerm);

	                $oTerm = (object)$oTerm;
	                $oTerms[$key] = $oTerm;
                    $aTerms[] = json_encode($oTerm);
                    self::setTermsCaching($taxonomy, array($oTerm->term_id=>$oTerm));
                }

                if ( is_array($termID) || !$hasTermID ) {
                    $oTerms = isset($oTermCaching) ? array_merge($oTerms, $oTermCaching) : $oTerms;
                    return (object)$oTerms;
                }else{
                    return (object)$oTerms[0];
                }

            }

            return false;
        }

        /**
         * The Term
         * @since 1.0.2
         */
        public static function theTerms($taxonomy, $post, $beforeTitle='', $title = '', $afterTitle='',  $separate='', $after='', $class=''){
            $aTerms = self::getPostTerms($post, $taxonomy);
            if ( !empty($aTerms) && !is_wp_error($aTerms) ) :
                $total = count($aTerms); $i = 1;
                if ( strpos($title, '|') ){
	                $title = explode('|', $title);
	                $singular = $title[0];
	                $plural = $title[1];
                }else{
                    $singular = $plural = $title;
                }

                if ( $total >  1 ){
                    $chooseTitle = $plural;
                }else{
                    $chooseTitle = $singular;
                }

                Wiloke::wiloke_kses_simple_html($beforeTitle . $chooseTitle . $afterTitle);
                foreach ( $aTerms as $oTerm ) :
	                Wiloke::wiloke_kses_simple_html('<a href="'.esc_url($oTerm->link).'" class="'.esc_attr($class).'">'.$oTerm->name.'</a>');
	                if ( $i !== $total ){
		                Wiloke::wiloke_kses_simple_html($separate);
	                }
                    $i++;
                endforeach;
	            Wiloke::wiloke_kses_simple_html($after);
            endif;
        }

        /**
         * Convert a pagebuilder array to a wordpress query args array
         * @author wiloke team
         * @since 1.0
         */
        static public function wiloke_query_args($atts='', $paged='')
        {
            $aWpQueryArgs['ignore_sticky_posts'] = 1;
            $aWpQueryArgs['post_status'] 		 = 'publish';

            extract(shortcode_atts(
                    array(
                        'paged'                 => 1,
                        'category_ids' 			=> '',
                        'category_id' 			=> '',
                        'tag_slug' 				=> '',
                        'sort' 					=> '',
                        'author_id' 			=> '',
                        'post_types' 			=> '',
                        'posts_per_page' 		=> '',
                        'offset' 				=> '',
                        'post__not_in'			=> '',
                        'order'		 	 		=> 'DESC',
                        'orderby'				=> '',
                        'cat__in'				=> '',
                        'tax_query'				=> array()
                    ),
                    $atts
                )
            );


            if ( !empty($category_ids) )
            {
                $aWpQueryArgs['cat'] = $category_ids;
            }

            if ( !empty($orderby) )
            {
                $aWpQueryArgs['orderby'] = $orderby;
            }

            if ( !empty($tag_slug) )
            {
                $aWpQueryArgs['tag'] = str_replace(' ', '-', $tag_slug);
            }

            if (!empty($author_id))
            {
                $aWpQueryArgs['author'] = $author_id;
            }

            if (!empty($post_types))
            {
                $aPostTypes 		= array();
                $aParsePostTypes 	= explode(',', $post_types);

                foreach ($aParsePostTypes as $postType)
                {
                    if (trim($postType) != '')
                    {
                        $aPostTypes[] = trim($postType);
                    }
                }

                $aWpQueryArgs['post_type'] = $aPostTypes;  // add post types to query args
            }


            //posts per page
            if ( empty($posts_per_page) )
            {
                $posts_per_page = get_option('posts_per_page');
            }
            $aWpQueryArgs['posts_per_page'] = $posts_per_page;

            //custom pagination
            if (!empty($paged)) {
                $aWpQueryArgs['paged'] = $paged;
            } else {
                $aWpQueryArgs['paged'] = 1;
            }

            // offset + custom pagination - if we have offset, wordpress overwrites the pagination and works with offset + limit
            if (!empty($offset) and $paged > 1) {
                $aWpQueryArgs['offset'] = $offset + ( ($paged - 1) * $limit) ;
            } else {
                $aWpQueryArgs['offset'] = $offset ;
            }

            if ( !empty($post__not_in) )
            {
                if ( is_string($post__not_in) )
                {
                    $post__not_in                 = trim($post__not_in, ',');
                    $aWpQueryArgs['post__not_in'] = explode(',', $post__not_in);
                }else{
                    $aWpQueryArgs['post__not_in'] = $post__not_in;
                }

                $aWpQueryArgs['post__not_in'] = array_map('intval', $aWpQueryArgs['post__not_in']);
            }

            if ( !empty($tax_query) )
            {
                $aWpQueryArgs['tax_query'] = $tax_query;
            }

            if ( !empty($order) )
            {
                $aWpQueryArgs['order'] = $order;
            }

            if ( !empty($cat__in) )
            {
                $aWpQueryArgs['cat__in'] = $cat__in;
            }

            return $aWpQueryArgs;
        }

        /**
         * Parse thumbnail size
         */
        static public function wiloke_parse_thumbnail_size($size)
        {
            if ( strpos($size, 'x') )
            {
                return explode('x', $size);
            }

            return $size;
        }

        /**
         * User Data
         *
         * @since 1.0
         * @return object
         */
        static public function get_userdata($field='')
        {
            if ( is_user_logged_in() )
            {
                if ( !empty($field) )
                {
                    return get_user_meta( get_current_user_id(), $field, true );
                }else{
                    return get_userdata( get_current_user_id() );
                }
            }
        }

        /**
         * Parse Template to key
         */
        static public function wiloke_parse_template_to_key($postID)
        {
            $target = get_page_template_slug($postID);
            $target = explode('/', $target);
            $target = end($target);

            $target = str_replace( array('', '.php'), array('', ''), $target );

            return $target;
        }

	    public static function removeVCSC( $excerpt ) {
		    $patterns = "/\[[\/]?vc_[^\]]*\]/";
		    return preg_replace_callback($patterns, function($matches){
                return '';
            }, $excerpt);
	    }

	/**
         * Truncate string
         */
        static public function wiloke_content_limit($limit=0, $post, $isFocusCutString=false, $content='', $isReturn = true, $dotted='')
        {
            if ( !empty($post->post_excerpt) ){
	            Wiloke::wiloke_kses_simple_html($post->post_excerpt . $dotted, $isReturn);
            }else{
	            if ( empty($limit) )
	            {
		            return null;
	            }

	            if ( empty($content) )
	            {
		            if ( !$isFocusCutString && !empty($post->post_excerpt) )
		            {
			            $content = $post->post_excerpt;
		            }else{
			            if ( isset($post->ID) )
			            {
				            $content = get_the_content($post->ID);
			            }else{
				            $content = null;
			            }
		            }
	            }

	            $content = strip_shortcodes(self::removeVCSC($content));
	            $content = strip_tags($content, '<script>,<style>');
	            $content = trim( preg_replace_callback('#<(s(cript|tyle)).*?</\1>#si', function(){
		            return '';
	            }, $content));

	            $content = str_replace('&nbsp;', '<br /><br />', $content);

	            $content = self::wiloke_truncate_pharse($content, $limit);

	            if ( $isReturn )
	            {
		            return $content . $dotted;
	            }else{
		            Wiloke::wiloke_kses_simple_html($content . $dotted, false);
	            }
            }
        }

        static public function wiloke_truncate_pharse($text, $max_characters)
        {
            $text = trim( $text );

            if(function_exists('mb_strlen') && function_exists('mb_strrpos'))
            {
                if ( mb_strlen( $text ) > $max_characters ) {
                    $text = mb_substr( $text, 0, $max_characters + 1 );
                    $text = trim( mb_substr( $text, 0, mb_strrpos( $text, ' ' ) ) );
                }
            }else{
                if ( strlen( $text ) > $max_characters ) {
                    $text = substr( $text, 0, $max_characters + 1 );
                    $text = trim( substr( $text, 0, strrpos( $text, ' ' ) ) );
                }
            }
            return $text;
        }

        static public function wiloke_kses_simple_html($content, $isReturn=false)
        {
            $allowed_html = array(
                'a' => array(
                    'href'  => array(),
                    'style' => array(
                        'color' => array()
                    ),
                    'title' => array(),
                    'target'=> array(),
                    'class' => array()
                ),
                'div'    => array('class'=>array()),
                'h1'     => array('class'=>array()),
                'h2'     => array('class'=>array()),
                'h3'     => array('class'=>array()),
                'h4'     => array('class'=>array()),
                'h5'     => array('class'=>array()),
                'h6'     => array('class'=>array()),
                'br'     => array('class' => array()),
                'p'      => array('class' => array(), 'style'=>array()),
                'em'     => array('class' => array()),
                'strong' => array('class' => array()),
                'span'   => array('data-typer-targets'=>array(), 'class' => array()),
                'i'      => array('class' => array()),
                'ul'     => array('class' => array()),
                'ol'     => array('class' => array()),
                'li'     => array('class' => array()),
                'code'   => array('class'=>array()),
                'pre'    => array('class' => array()),
                'iframe' => array('src'=>array(), 'width'=>array(), 'height'=>array(), 'class'=>array('embed-responsive-item')),
                'img'    => array('src'=>array(), 'width'=>array(), 'height'=>array(), 'class'=>array(), 'alt'=>array()),
                'embed'  => array('src'=>array(), 'width'=>array(), 'height'=>array(), 'class' => array()),
            );

            $content = str_replace('[wiloke_quotes]', '"', $content);

            if ( !$isReturn ) {
                echo wp_kses(wp_unslash($content), $allowed_html);
            }else{
                return wp_kses(wp_unslash($content), $allowed_html);
            }
        }

        public static function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0) {

            // search and remove comments like /* */ and //
            $json = preg_replace_callback("/(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t](//).*)/", '', $json);

            $phpVersion = phpversion();
            if(version_compare($phpVersion, '5.4.0', '>=')) {
                $json = json_decode($json, $assoc, $depth, $options);
            }
            elseif(version_compare($phpVersion, '5.3.0', '>=')) {
                $json = json_decode($json, $assoc, $depth);
            }
            else {
                $json = json_decode($json, $assoc);
            }

            return $json;
        }

        /**
         * Get Menu Object
         */
        static public function wiloke_get_nav_menus()
        {
            $aNavMenus          = wp_get_nav_menus();
            $aParseNavMenus     = array();

            if ( !empty($aNavMenus) )
            {
                $aParseNavMenus[-1] = esc_html__('Use default menu', 'listgo');
                foreach ($aNavMenus as $aMenu)
                {
                    $aParseNavMenus[$aMenu->term_id] = $aMenu->name;
                }
            }else{
                $aParseNavMenus[-1] = esc_html__('There are no menus', 'listgo');
            }

            return $aParseNavMenus;
        }

        /**
         * Render Date
         */
        static public function renderPostDate($postID, $isEcho = true)
        {
            $format = get_option('date_format');
            $date   = get_the_date($format, $postID);

            if ( $isEcho )
            {
                Wiloke::wiloke_kses_simple_html($date, false);
            }else{
                return $date;
            }
        }

        static public function wiloke_parse_inline_style($aArgs)
        {
            $style = null;
            foreach ( $aArgs as $key => $val )
            {
                if ( !empty($val) )
                {
                    $style .= $key . ':' . $val . ';';
                }
            }

            return $style;
        }

        static public function wiloke_parse_atts($aArgs)
        {
            $style = null;
            foreach ( $aArgs as $key => $val )
            {
                if ( !empty($val) )
                {
                    $style .= 'data-'. $key . '=' . $val . ' ';
                }
            }

            return $style;
        }

        static public function lazyLoad($src='', $cssClass='', $aAtributes=array(), $status = null, $isFocusRender = false)
        {
            $renderAttr = '';
            if ( !empty($aAtributes) )
            {
                foreach ( $aAtributes as $atts => $val )
                {
                    $renderAttr .= $atts . '=' . esc_attr($val) . ' ';
                }
            }

            if ( !$isFocusRender )
            {
                if ( $status === null )
                {
                    global $wiloke;
                    $status = !isset($wiloke->aThemeOptions['general_is_lazy_load']) || $wiloke->aThemeOptions['general_is_lazy_load'] ? true : false;
                }

                if ( $status ) :
                    $cssClass = trim($cssClass . ' lazy');
                    ?>
                    <img class="<?php echo esc_attr($cssClass); ?>" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" data-src="<?php echo esc_url($src); ?>" <?php echo esc_attr($renderAttr); ?> />
                    <noscript>
                        <img src="<?php echo esc_url($src); ?>" <?php echo esc_attr($renderAttr); ?>  />
                    </noscript>
                    <?php
                else :
                    ?>
                    <img src="<?php echo esc_url($src); ?>" <?php echo esc_attr($renderAttr); ?>  />
                    <?php
                endif;
            }else{
                ?>
                <img src="<?php echo esc_url($src); ?>" <?php echo esc_attr($renderAttr); ?>  />
                <?php
            }
        }

        static function wiloke_get_contact_form7()
        {
            $args       = array('post_type'=>'wpcf7_contact_form', 'posts_per_page'=>50, 'post_status'=>'publish');
            $query      = new WP_Query($args);
            $aValues    = array();

            if ( $query->have_posts() ) : while( $query->have_posts() ) : $query->the_post();
                $aValues[$query->post->ID] = $query->post->post_title;
            endwhile; endif;

            return $aValues;
        }

        static function wiloke_render_follow_us($cssClass='')
        {
            global $wiloke;

            if ( !empty(WilokeSocialNetworks::$aSocialNetworks) )
            {
                echo '<div class="'.esc_attr($cssClass).'">';
                foreach (WilokeSocialNetworks::$aSocialNetworks as $key )
                {
                    if ( !empty($wiloke->aThemeOptions['social_network_'.$key]) )
                    {
                        $icon  = str_replace('_', '-', $key);
                        $title = str_replace('_', ' ', $key);
                        $title = ucfirst($title);

                        echo '<a title="'.esc_attr($title).'" href="'.esc_url($wiloke->aThemeOptions['social_network_'.$key]).'" class="'.esc_attr($key).'"><i class="fa fa-'.esc_attr($icon).'"></i></a>';
                    }
                }
                echo '</div>';
            }
        }

        static public function wiloke_render_post_format_icon($postID)
        {
            $postFormat = get_post_format($postID);

            switch ($postFormat)
            {
                case 'image':
                    $icon = 'fa fa-image';
                    break;

                case 'gallery':
                    $icon = 'fa fa-picture-o';
                    break;

                case 'video';
                    $icon = 'fa fa-youtube-play';
                    break;

                case 'audio':
                    $icon = 'fa fa-music';
                    break;

                case 'quote':
                    $icon = 'fa fa-quote-right';
                    break;

                case 'link':
                    $icon = 'fa fa-link';
                    break;

                default:
                    $icon = 'fa fa-thumb-tack';
                    break;
            }

            return $icon;
        }

        /**
         * Get other templates (e.g. product attributes) passing attributes and including the file.
         *
         * @access public
         * @param string $template_name
         * @param array $args (default: array())
         * @param string $template_path (default: '')
         */
        public static function get_template($template_name, $args = array(), $template_path = '')
        {
            if ( ! empty( $args ) && is_array( $args ) )
            {
                extract( $args );
            }

            $located = self::locate_template($template_name, $template_path);

            if ( !file_exists( $located ) )
            {
                _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0' );
                return;
            }

            // Allow 3rd party plugin filter template file from their plugin.
            $located = apply_filters('wiloke_get_template', $located, $template_name, $args, $template_path);

            do_action( 'wiloke_before_template_part', $template_name, $template_path, $located, $args );

            include($located);

            do_action( 'wiloke_after_template_part', $template_name, $template_path, $located, $args );
        }

        /**
         * Locate a template and return the path for inclusion.
         *
         * This is the load order:
         *
         *		yourtheme		/	$template_path	/	$template_name
         *		yourtheme		/	$template_name
         *		$default_path	/	$template_name
         *
         * @access public
         * @param string $template_name
         * @param string $template_path (default: '')
         * @param string $default_path (default: '')
         * @return string
         */
        public static function locate_template($template_name, $template_path = '')
        {
            if ( !$template_path )
            {
                $template_path = 'admin/public/template/' ;
            }

            // Look within passed path within the theme - this is priority.
            $template = locate_template(
                array(
                    trailingslashit( $template_path ) . $template_name,
                    $template_name
                )
            );

            // Return what we found.
            return apply_filters( 'wiloke_locate_template', $template, $template_name, $template_path );
        }

        /**
         * Collection of hooks related to admin
         * @since 1.0
         */
        public function admin_hooks()
        {
            if ( is_file( WILOKE_THEME_DIR . 'hooks/admin.php' ) ) {
                require WILOKE_THEME_DIR . 'hooks/admin.php';
            }
        }

        /**
         * We care everything related to front-end
         * @since 1.0
         */
        public function public_hooks()
        {
            if ( is_file( WILOKE_THEME_DIR . 'hooks/public.php' ) ) {
                require WILOKE_THEME_DIR . 'hooks/public.php';
            }
        }

        /**
         * General Hooks, in other words, he works the both admin and front-end
         * @since 1.0
         */
        public function general_hooks()
        {
            if ( !empty($this->_themeOptions) )
            {
                $this->_loader->add_action('init', $this->_themeOptions, 'get_option');
            }

            if ( !empty($this->_registerSidebar) )
            {
                $this->_loader->add_action('widgets_init', $this->_registerSidebar, 'register_widgets');
            }
        }


        /**
         * Generate srcset and sizes
         *
         * @return: ['main'=>array('width', 'height', 'src'), 'srcset'=>array(), 'sizes'=>array()]
         * @since 1.0.1
         */
        public static function generateSrcsetImg($attachmentID, $size){
            if ( is_array($size) ){
	            $size = array_map('absint', $size);
            }
            $img = wp_get_attachment_image_src($attachmentID, $size);

            if ($img) {
                global $wiloke;

                $aLarge = wp_get_attachment_image_src($attachmentID, 'large');

                if ($aLarge){
                    $attr['large']['src']    = $aLarge[0];
                    $attr['large']['width']  = $aLarge[1];
                    $attr['large']['height'] = $aLarge[2];
                }

                if ( isset($wiloke->aConfigs['general']['img_sizes']) ){
                    foreach ( $wiloke->aConfigs['general']['img_sizes'] as $key => $aSize ){
                        $aImg = wp_get_attachment_image_src($attachmentID, $key);
                        $attr[$key]['src']    = $aImg[0];
                        $attr[$key]['width']  = $aImg[1];
                        $attr[$key]['height'] = $aImg[2];
                    }
                }

                list($src, $width, $height) = $img;
                $aImgData = wp_get_attachment_metadata($attachmentID);
                $attr['main']['src']    = $src;
                $attr['main']['width']  = $width;
                $attr['main']['height'] = $height;

                if (is_array($aImgData)) {
                    $aSize  = array(absint($width), absint($height));
                    $srcset = wp_calculate_image_srcset($aSize, $src, $aImgData, $attachmentID);
                    $sizes  = wp_calculate_image_sizes($aSize, $src, $aImgData, $attachmentID);

                    if ($srcset && ($sizes || !empty( $attr['sizes']))) {
                        $attr['srcset'] = $srcset;

                        if ( empty($attr['sizes']) ) {
                            $attr['sizes'] = $sizes;
                        }
                    }
                }

                return $attr;
            }

            return false;
        }

	    /**
	     * Get Client IP
	     * @since 1.0.1
	     */
	    public static function clientIP(){
		    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		    }else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
			    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		    }else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
			    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		    }else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		    }else if(isset($_SERVER['HTTP_FORWARDED'])){
			    $ipaddress = $_SERVER['HTTP_FORWARDED'];
		    }else if(isset($_SERVER['REMOTE_ADDR'])) {
			    $ipaddress = $_SERVER['REMOTE_ADDR'];
		    }else {
			    $ipaddress = false;
		    }
		    return $ipaddress;
	    }

	    /**
	     * Safely Enqueue Google Fonts
         * @since 1.0.2
         * @param $aFonts array a list of google font
         * @return string
	     */
	    public static function safelyGenerateGoogleFont($aFonts){
		    $subsets   = 'latin,latin-ext';
            $fonts_url = add_query_arg(array(
                'family' => urlencode( implode( '|', $aFonts ) ),
                'subset' => urlencode( $subsets ),
            ), 'https://fonts.googleapis.com/css' );

		    return esc_url_raw($fonts_url);
        }

        /**
         * List of actions and filters. We will run it soon
         * @since 1.0
         */
        public function run()
        {
            $this->_loader->run();
        }
    }

endif;
