<?php
/**
 * The class handle everything related to front-end
 *
 * @since       1.0
 * @link        http://wiloke.com
 * @author      Wiloke Team
 * @package     WilokeFramework
 * @subpackpage WilokeFramework/admin/front-end
 */
use WilokeListGoFunctionality\AlterTable\AlterTableFavirote;
use WilokeListGoFunctionality\Register\RegisterFollow;
use WilokeListGoFunctionality\AlterTable\AltertableFollowing;
use WilokeListGoFunctionality\Register\RegisterWilokeSubmission;
use WilokeListGoFunctionality\Register\RegisterWilokeSubmission as WilokeWilokeSubmission;
use WilokeListGoFunctionality\AlterTable\AlterTableReviews;
use WilokeListGoFunctionality\Register\RegisterBadges;
use WilokeListGoFunctionality\AlterTable\AlterTablePaymentHistory;
use WilokeListGoFunctionality\Frontend\FrontendClaimListing as WilokeFrontendClaimListing;
use WilokeListGoFunctionality\Frontend\FrontendManageSingleListing;


/**
 * Deny directly access
 * @since 1.0
 */
if ( !defined('ABSPATH') || !class_exists('WilokePublic') ){
    return false;
}

class WilokePublic
{
    public static $oUserInfo;
    public static $aUsersData;

    public static $aTemporaryCaching = array();

    public static $aPostStatusIcons = array(
        'renew'     => 'icon_cloud-upload_alt',
        'publish'   => 'icon_like',
        'expired'   => 'icon_lock_alt',
        'pending'   => 'icon_cloud-upload_alt',
        'processing'=> 'icon_loading',
        'temporary_closed' => 'fa fa-lock'
    );

    public static $current_layout = 'wiloke_current_layout';
    public static $single_post_settings = 'single_post_settings';
    public static $googleMap = 'https://www.google.com/maps/place/';

    public static $aListingTermRelationship = array();

    public static $oListingCollections;
    public static $aTermRelationships;
    protected static $aPaymentFields = array();
    public static $aClaimStatus = array();

    public function __construct() {
        add_action('init', array($this, 'init'), 1);
        add_action('wp_ajax_nopriv_wiloke_get_term_children', array($this, 'getTermChildren'));
        add_action('wp_ajax_wiloke_get_term_children', array($this, 'getTermChildren'));

        add_action('wp_ajax_wiloke_listgo_update_profile', array($this, 'updateProfile'));
        add_action('wp_ajax_wiloke_listgo_temporary_closed_listing', array($this, 'temporaryClosedListing'));


        add_filter('get_avatar', array($this, 'filterAvatar'), 1, 3);
        add_filter('embed_oembed_html', array($this, 'removeVideoOutOfContent'), 99, 1);

        add_action('wp_ajax_wiloke_listgo_get_listing_status', array($this, 'getListingStatusViaAjax'));
        // add_action('wp_ajax_wiloke_listgo_fetch_listing_nearby_visitors', array($this, 'fetchListingsNearByVisitor'));
        // add_action('wp_ajax_nopriv_wiloke_listgo_fetch_listing_nearby_visitors', array($this, 'fetchListingsNearByVisitor'));
    }

    public static function detectListingStatus($post, $ignoreClosein){
        $items = '';

        $toggleBusinessHour = get_post_meta($post->ID, 'wiloke_toggle_business_hours', true);
        if ( isset($toggleBusinessHour) && $toggleBusinessHour === 'disable' ){
            echo '';
        }else{
            $aBusinessStatus = self::businessStatus($post);
            if ( !empty($aBusinessStatus['closesininfo']) && !$ignoreClosein ){
                $items .= '<span class="closesin orange">'.$aBusinessStatus['closesininfo'].'</span>';
            }elseif($aBusinessStatus['status'] === 'opening'){
                $items .= '<span class="onopen green">'.esc_html__('Open now', 'listgo').'</span>';
            }else if ( $aBusinessStatus['status'] === 'closed' ){
                $items .= '<span class="onclose red">'.esc_html__('Closed now', 'listgo').'</span>';
                if ( !empty($aBusinessStatus['nextdayinfo']) ){
                    $items .= '<span class="onopensin yellow">'.$aBusinessStatus['nextdayinfo'].'</span>';
                }
            }

            if ( empty($items) ){
               echo '';
            }else{
                echo '<span class="ongroup">' . $items . '</span>';
            }
        }
    }

    public function getListingStatusViaAjax(){
        $post = get_post($_GET['listingID']);
        ob_start();
        self::detectListingStatus($post, $_GET['ignoreClosein']);
        $content = ob_get_contents();
        ob_end_clean();
        wp_send_json_success(
            array(
                'msg' => $content
            )
        );
    }

    public function init(){
        if ( is_admin() ){
            return false;
        }

        if ( is_user_logged_in() ){
            $userID = get_current_user_id();
            self::$oUserInfo = self::getUserMeta($userID);
            if ( Wiloke::$wilokePredis ){
               Wiloke::hSet(WilokeUser::$redisKey, $userID, self::$oUserInfo);
            }
            self::$oUserInfo = (object)self::$oUserInfo;
        }
    }

    public static function getColorByAnphabet($anphabet){
        global $wiloke;
        $anphabet = strtolower($anphabet);
        foreach ( $wiloke->aConfigs['frontend']['anphabets'] as $key => $aAnphabets ){
            $aAnphabets = explode(',', $aAnphabets);
            if ( in_array($anphabet, $aAnphabets) ){
                return $wiloke->aConfigs['frontend']['color_picker'][$key];
                break;
            }
        }

        return $wiloke->aConfigs['frontend']['color_picker'][$key];
    }

    public static function timezoneString($time=''){

        if ( !empty($time) ){

            if ( (strpos($time, 'UTC')  === false) ){
                return $time;
            }
            $time = str_replace(array('UTC', '+'), array('', ''), $time);

            if ( empty($time) ){
                return 'UTC';
            }

            $utc_offset = intval($time*3600);

            if ( $timezone = timezone_name_from_abbr( '', $utc_offset ) ) {
                return $timezone;
            }
        }else{
            // if site timezone string exists, return it
            if ( $timezone = get_option( 'timezone_string' ) ) {
                return $timezone;
            }

            // get UTC offset, if it isn't set then return UTC
            if ( 0 === ( $utc_offset = intval( get_option( 'gmt_offset', 0 ) ) ) ) {
                return 'UTC';
            }

            // adjust UTC offset from hours to seconds
            $utc_offset *= 3600;

            // attempt to guess the timezone string from the UTC offset
            if ( $timezone = timezone_name_from_abbr( '', $utc_offset ) ) {
                return $timezone;
            }
        }

        // last try, guess timezone string manually
        foreach ( timezone_abbreviations_list() as $abbr ) {
            foreach ( $abbr as $city ) {
                if ( (bool) date( 'I' ) === (bool) $city['dst'] && $city['timezone_id'] && intval( $city['offset'] ) === $utc_offset ) {
                    return $city['timezone_id'];
                }
            }
        }

        // fallback to UTC
        return 'UTC';
    }

    public static function businessStatus($post){
        $aPostTerms = Wiloke::getPostTerms($post, 'listing_location');
        $timeZone = '';

        if ( !empty($aPostTerms) && !is_wp_error($aPostTerms) ){
            if ( !isset($aPostTerms->errors) ){
                $oLastTerm = end($aPostTerms);
                $aTermSetting = Wiloke::getTermOption($oLastTerm->term_id);
                if ( isset($aTermSetting['timezone']) ){
                    $timeZone = $aTermSetting['timezone'];
                }
            }
        }

        date_default_timezone_set(self::timezoneString($timeZone));
        $aBusinessHours  = Wiloke::getPostMetaCaching($post->ID, 'wiloke_listgo_business_hours');
        $time = time();
        $today = date('N');
        $oNewDateTime = new DateTime();
        $today = absint($today) - 1;

        if ( $aBusinessHours[$today]['start_minutes'] == 0 ){
            $aBusinessHours[$today]['start_minutes'] = '00';
        }

        if ( $aBusinessHours[$today]['close_minutes'] == 0 ){
            $aBusinessHours[$today]['close_minutes'] = '00';
        }

        $opening = $aBusinessHours[$today]['start_hour'].':'.$aBusinessHours[$today]['start_minutes'] . ' ' . $aBusinessHours[$today]['start_format'];
        $closed  = $aBusinessHours[$today]['close_hour'].':'.$aBusinessHours[$today]['close_minutes'] . ' ' . $aBusinessHours[$today]['close_format'];
        $nextDayInfo = '';
        $closesInInfo = '';

        $currentTime = $oNewDateTime->setTimestamp($time);
        $startTime = DateTime::createFromFormat('h:i A', $opening);
        $endTime   = DateTime::createFromFormat('h:i A', $closed);
        $sixAM     = DateTime::createFromFormat('h:i A', '6:00:00 AM');

        $isSpecialEndTime = false;

        if ( isset($aBusinessHours[$today]['closed']) && !empty($aBusinessHours[$today]['closed']) ){
            $status = 'closed';
        }else{
            # We need to detect the format of closed time, it very important

            if ( ($aBusinessHours[$today]['close_format'] !== 'AM') || ( ($aBusinessHours[$today]['close_format'] === 'AM') && ($endTime > $sixAM) ) ){
                if ( ($currentTime < $startTime) || ($currentTime > $endTime) ){
                    $status = 'closed';
                }else{
                    $status = 'opening';
                }
            }else{
                $isSpecialEndTime = true;
                if ( ($currentTime > $endTime) && ($currentTime < $startTime) ){
                    $status = 'closed';
                }else{
                    $status = 'opening';
                }
            }
        }

        if ( $status === 'closed' ){
            if ( $isSpecialEndTime ){
                $nextDay = $today;
                $formatKey = 'close_format';
            }else{
                if ( $currentTime < $startTime ){
                    $nextDay = $today;
                }else{
                    $nextDay = $today == 6 ? 0 : absint($today) + 1;
                }
                $formatKey = 'start_format';
            }
            
            if ( isset($aBusinessHours[$nextDay]['start_hour']) && !isset($aBusinessHours[$nextDay]['closed']) ){
                $nextDayInfo = esc_html__('Opens at', 'listgo') . ' '. $aBusinessHours[$nextDay]['start_hour'].':'.$aBusinessHours[$nextDay]['start_minutes'] . ' ' . $aBusinessHours[$nextDay][$formatKey];
                $nextDayInfo = apply_filters('wiloke/listgo/admim/public/filterNextOpenIn', $nextDayInfo, $aBusinessHours, $nextDay);
                if ( !$isSpecialEndTime ){
                    if ( $currentTime < $startTime ){
                        $nextDayInfo .= ' ' . esc_html__('today', 'listgo');
                    }else{
                        $nextDayInfo .= ' ' . esc_html__('tomorrow', 'listgo');
                    }
                }else{
                    $nextDayInfo .= ' ' . esc_html__('today', 'listgo');
                }
            }
        }else{
            $oDiff = $endTime->diff($currentTime);
            if ( $oDiff->h >= 1 && $oDiff->h <= 2 ){
                $closesInInfo = esc_html__('Closes in', 'listgo') . ' ' . $oDiff->h . ' ' . esc_html__('hour', 'listgo') . ' ' . $oDiff->i . ' ' . esc_html__('minutes', 'listgo');
            }else if($oDiff->h == 0){
                $closesInInfo = esc_html__('Closes in', 'listgo') . ' ' . $oDiff->i . ' ' . esc_html__('minutes', 'listgo');
            }
        }

        return array(
            'status'       => $status,
            'nextdayinfo'  => $nextDayInfo,
            'closesininfo' => $closesInInfo
        );
    }

    /**
     * Render Listing status such as it's closed now or it's ads
     * @since 1.0
     */
    public static function renderListingStatus($post, $ignoreClosein = false){
        $isRender = apply_filters('wiloke/listgo/isRenderListingStatus', true, $post);
        if ( !$isRender ){
            return false;
        }
        $items = '';
        $aUser = Wiloke::getUserMeta($post->post_author);
        if ( $aUser === 'wiloke_submisison' ){
            $items .= '<span class="onads">'.esc_html__('Ad', 'listgo').'</span>';
        }

        $toggleBusinessHour = get_post_meta($post->ID, 'wiloke_toggle_business_hours', true);
        if ( isset($toggleBusinessHour) && $toggleBusinessHour === 'disable' ){
            return false;
        }

        $aBusinessStatus = self::businessStatus($post);

        if ( !empty($aBusinessStatus['closesininfo']) && !$ignoreClosein ){
            $items .= '<span class="closesin orange">'.$aBusinessStatus['closesininfo'].'</span>';
        }elseif($aBusinessStatus['status'] === 'opening'){
            $items .= '<span class="onopen green">'.esc_html__('Open now', 'listgo').'</span>';
        }else if ( $aBusinessStatus['status'] === 'closed' ){
            $items .= '<span class="onclose red">'.esc_html__('Closed now', 'listgo').'</span>';
            if ( !empty($aBusinessStatus['nextdayinfo']) ){
                $items .= '<span class="onopensin yellow">'.$aBusinessStatus['nextdayinfo'].'</span>';
            }
        }

        if ( empty($items) ){
            return '';
        }

        echo '<span class="ongroup">' . $items . '</span>';
    }

    public function addHiddenFilterForContactForm7OnListingPage($aFields){
        global $post;
        if ( isset($post->post_type) && $post->post_type === 'listing' ){
            $aFields = array(
                '_wiloke_post_author_email' => $post->ID
            );
        }
        return $aFields;
    }

    public function filterRecipientOfContactFormSeven($components, $aCurrent, $self){
        if ( isset($_POST['_wiloke_post_author_email']) && !empty($_POST['_wiloke_post_author_email']) ){
            $post = get_post(absint($_POST['_wiloke_post_author_email']));
            if ( !is_wp_error($post) && !empty($post) ){
                $aContactInfo = get_post_meta($post->ID, 'listing_settings', true);

                if ( isset($aContactInfo['contact_email']) && !empty($aContactInfo['contact_email']) ){
                    $components['recipient'] = $aContactInfo['contact_email'];
                }else{
                    $aUser = Wiloke::getUserMeta($post->post_author);
                    $components['recipient'] = $aUser['user_email'];
                    $aClaimerInfo = WilokeFrontendClaimListing::getClaimerInfo($post);
                    if ( isset($aClaimerInfo['user_email']) && !empty($aClaimerInfo['user_email']) ){
                        $components['recipient'] = $aClaimerInfo['user_email'];
                    }
                }

            }
        }
        return $components;
    }

    public static function checkAjaxSecurity($aData){
        if ( !isset($aData['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
            return false;
        }
        return true;
    }

    public static function parseLocationQuery($aData, $isFocusGetTerm=false){
        $aArgs = array();
        $isFoundIt = false;
        $aData['listing_locations'] = trim($aData['listing_locations']);
        if ( isset($aData['location_place_id']) && !empty($aData['location_place_id']) ){
            $aDetectLocations = get_terms(
                array(
                    'taxonomy'   => 'listing_location',
                    'hide_empty' => true,
                    'meta_key'   => 'wiloke_listing_location_place_id',
                    'meta_value' => $aData['location_place_id']
                )
            );

            if ( !empty($aDetectLocations) && !is_wp_error($aDetectLocations) ){
                foreach ( $aDetectLocations as $oDetectedLocation ){
                    $aLocationIDs[] = $oDetectedLocation->term_id;
                }
                $aArgs = array(
                    'taxonomy'  => 'listing_location',
                    'field'     => 'term_id',
                    'terms'     => $aLocationIDs
                );

                $isFoundIt = true;
            }
        }

        if ( !$isFoundIt ){
            if ( is_numeric($aData['listing_locations']) ){
                $aArgs = array(
                    'taxonomy'  => 'listing_location',
                    'field'     => 'term_id',
                    'terms'     => absint($aData['listing_locations'])
                );
            }else{
                $aDetectLocations = get_terms(array('taxonomy'=>'listing_location', 'name__like' => strtolower($aData['listing_locations'])));
                if ( !empty($aDetectLocations) && !is_wp_error($aDetectLocations) ){
                    foreach ( $aDetectLocations as $oDetectedLocation ){
                        $aLocationIDs[] = $oDetectedLocation->term_id;
                    }
                    $aArgs = array(
                        'taxonomy'  => 'listing_location',
                        'field'     => 'term_id',
                        'terms'     => $aLocationIDs
                    );
                }else{
                    $aDetectLocations = get_term_by('slug', sanitize_title($aData['listing_locations']),'listing_location');
                    if ( !empty($aDetectLocations) && !is_wp_error($aDetectLocations) ){
                        $aArgs = array(
                            'taxonomy'  => 'listing_location',
                            'field'     => 'slug',
                            'terms'     => $aDetectLocations->term_id
                        );
                    }else{
                        $aGetFirstObject = explode(',', $aData['listing_locations']);
                        if ( count($aGetFirstObject) > 1 ){
                            $aDetectLocations = get_terms(array('taxonomy'=>'listing_location', 'name__like' => strtolower($aGetFirstObject[0])));
                            if ( !empty($aDetectLocations) && !is_wp_error($aDetectLocations) ){
                                foreach ( $aDetectLocations as $oDetectedLocation ){
                                    $aLocationIDs[] = $oDetectedLocation->term_id;
                                }
                                $aArgs = array(
                                    'taxonomy'  => 'listing_location',
                                    'field'     => 'term_id',
                                    'terms'     => $aLocationIDs
                                );
                            }
                        }
                    }
                }
            }
        }

        return $aArgs;
    }

    public static function renderPriceSegment($post){
        $aPriceSettings = Wiloke::getPostMetaCaching($post->ID, 'listing_price');
        if ( empty($aPriceSettings) || empty($aPriceSettings['price_segment']) ){
            return false;
        }
        ?>
        <span class="listing__price">
            <label class="listing__price-label"><?php esc_html_e('Price:', 'listgo'); ?></label>
            <span class="listing__price-amount"><?php echo esc_html($aPriceSettings['price_from']); ?></span>
            <span class="listing__price-amount"><?php echo esc_html($aPriceSettings['price_to']); ?></span>
        </span>
        <?php
    }

    public static function renderFeaturedIcon($post){
        $isFeatured = get_post_meta($post->ID, 'wiloke_listgo_toggle_highlight', true);
        if ( !empty($isFeatured) ) :
        ?>
        <span class="onfeatued"><i class="fa fa-star-o"></i></span>
        <?php
        endif;
    }

    public static function renderCouponBadge($post){
        $aCoupon = \WilokeListgoFunctionality\Framework\Helpers\GetSettings::getPostMeta($post->ID, 'listing_coupon');
		if ( empty($aCoupon['coupon_code']) ){
            return '';
        }
        ?>
        <div class="ribbon">
            <div class="inner-ribbon">
                <span class="r-txt-1"><?php echo esc_html($aCoupon['description']); ?></span>
            </div>
        </div>
        <?php
    }

    public static function putTermLinks($aTerms){
        if ( is_wp_error($aTerms) || empty($aTerms) ){
            return false;
        }

        foreach ( $aTerms as $key => $oTerm ){
            $oTerm = get_object_vars($oTerm);
            $oTerm['link'] = get_term_link($oTerm['term_id']);
            $oTerm = (object)$oTerm;
            $aTerms[$key] = (object)$oTerm;
            Wiloke::setTermsCaching('listing_location', array($oTerm->term_id=>$oTerm));
        }

        return $aTerms;
    }

    public static function getTermIDs($aTerms){
        $aTermIDs = array();
        if ( !is_wp_error($aTerms) && !empty($aTerms) ){
            foreach ( $aTerms as $oTerm ){
                $aTermIDs[] = $oTerm->term_id;
            }
        }

        return $aTermIDs;
    }

    public static function getSetting($pageKey, $tfoKey, $aPageSettings){
        if ( !isset($aPageSettings[$pageKey]) || ($aPageSettings[$pageKey] === 'inherit') ){
            global $wiloke;
            $aValue = $wiloke->aThemeOptions[$tfoKey];
            if ( isset($aValue['rgba']) ){
                return $aValue['rgba'];
            }

            return $aValue;
        }

        return $aPageSettings[$pageKey];
    }

    public static function renderMenu(){
        global $wiloke;
        $menu = $wiloke->aConfigs['frontend']['register_nav_menu']['menu'][0]['key'];
        if ( has_nav_menu($menu) ){
            wp_nav_menu($wiloke->aConfigs['frontend']['register_nav_menu']['config'][$menu]);
        }
    }

    public static function getUserAvatar($userID=null, $size=null){
        $size = empty($size) ? array(65, 65) : $size;
        if ( !empty($userID) ){
            $aUserInfo = Wiloke::getUserMeta($userID);
        }else{
            $aUserInfo = get_object_vars(self::$oUserInfo);
        }

        $avatar = isset($aUserInfo['meta']['wiloke_profile_picture']) && !empty($aUserInfo['meta']['wiloke_profile_picture']) ? wp_get_attachment_image_url($aUserInfo['meta']['wiloke_profile_picture'], $size) : get_template_directory_uri() . '/img/profile-picture.jpg';
        return $avatar;
    }

	public static function renderAuthor($post, $atts=null){
        if ( isset($atts['toggle_render_author']) && $atts['toggle_render_author'] === 'disable' ){
            return false;
        }

	    $aAuthor = WilokeAuthor::getAuthorInfo($post->post_author);
		?>
        <div class="listing__author <?php echo esc_attr(strpos($aAuthor['avatar'], 'profile-picture.jpg') ? 'listing__author--no-avatar' : '') ?>">
            <a href="<?php echo esc_url($aAuthor['link']); ?>">
                <?php
                if ( strpos($aAuthor['avatar'], 'profile-picture.jpg') === false ) {
                    ?>
                    <img src="<?php echo esc_url($aAuthor['avatar']); ?>" alt="<?php echo esc_attr($aAuthor['nickname']); ?>" height="65" width="65" class="avatar">
                    <?php
                } else {
                    $firstCharacter = strtoupper(substr($aAuthor['nickname'], 0, 1));
                    echo '<span style="background-color: '.esc_attr(self::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
                }
                ?>
                <h6><?php echo esc_html($aAuthor['nickname']); ?></h6>
            </a>
        </div>
		<?php
	}

	public static function getRated($postID, $reviewID){
	    global $wpdb;
	    $tblName = $wpdb->prefix . AlterTableReviews::$tblName;

        return $wpdb->get_var(
	        $wpdb->prepare(
                "SELECT rating FROM $tblName WHERE post_ID=%d AND review_ID=%d",
                $postID, $reviewID
	        )
	    );
	}

    public static function renderContent($post, $atts=null){
        if ( empty($atts) ){
            $atts = array(
                'limit_character' => 100,
                'toggle_render_post_excerpt' => 'enable',
                'toggle_render_address' => 'enable'
            );
        }

	    $aListing = Wiloke::getPostMetaCaching($post->ID, 'listing_settings');
	    if ( !empty($aListing) ){
	        ?>
            <div class="listing__content">
                <?php
                
                if ( isset($atts['toggle_render_post_excerpt']) && ($atts['toggle_render_post_excerpt'] === 'enable') ){
                    echo '<p>';
                    Wiloke::wiloke_content_limit($atts['limit_character'], $post, false, $post->post_content, false);
                    echo '</p>';
                }else{
                    $postExcerpt = get_post_field('post_excerpt', $post->ID);
                    if ( !empty($postExcerpt) ){
                        echo '<p>'.esc_html($postExcerpt).'</p>';
                    }
                }

                if ( $atts['toggle_render_address'] === 'enable' ) :
                ?>
                    <div class="address">
                        <?php
                            if ( isset($aListing['map']['location']) && !empty($aListing['map']['location']) ){
                                echo '<span class="address-location"><strong>'.esc_html__('Location', 'listgo').':</strong> ' . $aListing['map']['location'] . '</span>';
                            }

                            if ( isset($aListing['website']) && !empty($aListing['website']) ){
                                echo '<span class="address-website"><strong>'.esc_html__('Website', 'listgo').':</strong> ' . '<a target="_blank" href="'.esc_url($aListing['website']).'">'.$aListing['website'].'</a>' . '</span>';
                            }

                            if ( isset($aListing['phone_number']) && !empty($aListing['phone_number']) ){
                                echo '<span class="address-phone_number"><strong>'.esc_html__('Phone', 'listgo').':</strong> <a href="tel:'.esc_attr($aListing['phone_number']).'">'.$aListing['phone_number'].'</a></span>';
                            }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }
    }

	public static function calculateAverageRating($post){
	    global $wpdb;
	    if ( !class_exists('WilokeListGoFunctionality\AlterTable\AlterTableReviews') ){
	        return 0;
	    }
	    $tblName = $wpdb->prefix . AlterTableReviews::$tblName;
	    $average = $wpdb->get_var(
	        $wpdb->prepare(
                "SELECT AVG(rating) FROM $tblName WHERE post_ID=%d",
                $post->ID
	        )
	    );

	    return number_format(round($average, 1), 1, '.', '');
	}

	public static function renderAverageRating($post, $atts=null){
	    if ( isset($atts['toggle_render_rating']) && $atts['toggle_render_rating'] === 'disable' ){
            return false;
        }
	    $average = self::calculateAverageRating($post);
        ?>
        <div class="listgo__rating">
            <span class="rating__star">
                <?php for ( $i = 1; $i <= 5; $i++) : ?>
                    <i class="<?php echo esc_attr(WilokeReview::getStarClass($average, $i)); ?>"></i>
                <?php endfor; ?>
            </span>
            <span class="rating__number"><?php echo esc_html($average); ?></span>
        </div>
        <?php
	}

    public static function renderFavorite($post, $atts=null, $additionalClass=''){
        $itemClass = 'tb__cell ' . $additionalClass;
        $itemClass = trim($itemClass);

        $text = esc_html__('Save', 'listgo');
        if ( !empty($atts) ){
            if ( $atts['toggle_render_favorite'] === 'disable' ){
                return false;
            }

            $text = !empty($atts['favorite_description']) ? $atts['favorite_description'] : $text;
        }
        $favoriteClass = 'js_favorite';

	    if ( class_exists('WilokeListGoFunctionality\AlterTable\AlterTableFavirote') ){
		    $favorite = AlterTableFavirote::getStatus($post->ID);
		    $favoriteClass .= !empty($favorite) ? ' active' : '';
	    }

        ?>
        <div class="<?php echo esc_attr($itemClass); ?>">
            <a href="#" class="<?php echo esc_attr($favoriteClass); ?>" data-postid="<?php echo esc_attr($post->ID); ?>" data-tooltip="<?php echo esc_attr($text); ?>">
                <i class="icon_heart_alt"></i>
            </a>
        </div>
        <?php
    }

    public static function showTermInGrid($aTerms){
        if ( !empty($aTerms) && !is_wp_error($aTerms) ) :
        ?>
        <div class="listing__cat">
            <a href="<?php echo esc_url(get_term_link($aTerms[0]->term_id)); ?>"><?php echo esc_html($aTerms[0]->name); ?></a>
            <?php
            unset($aTerms[0]);
            if ( !empty($aTerms) ) :
            ?>
            <span class="listing__cat-more">+</span>
            <ul class="listing__cats">
                <?php foreach ( $aTerms as $oTerm ) : ?>
                <li>
                    <a href="<?php echo esc_url(get_term_link($oTerm->term_id)); ?>"><?php echo esc_html($oTerm->name); ?></a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
        <?php
        endif;
    }

    public function render_sharing_box()
    {
        if ( class_exists('WilokeSharingPost') )
        {
            echo do_shortcode('[wiloke_sharing_post]');
        }
    }

	public static function render_attributes($aAttributes){
        if ( empty($aAttributes) ){
            return '';
        }

		$atts = '';
		foreach ( $aAttributes as $key => $val ) {
			$atts .= esc_attr($key) . '="' . esc_attr($val) . '" ';
		}

		echo trim($atts);
	}

    public function solve_stupid_idea_of_wordpressdotcom($aFields)
    {
        if ( !empty(self::$oUserInfo) )
        {
            return $aFields;
        }

        $aComments = $aFields['comment'];
        unset($aFields['comment']);

        return (array)$aFields + (array)$aComments;
    }

    public static function createListingInfo($post){
        $parentLocation = $firstLocationID = $locationPlaceID = null;
        $aListingCats        = Wiloke::getPostTerms($post, 'listing_cat');
        $aListingLocations   = Wiloke::getPostTerms($post, 'listing_location', true);
        $aTags               = Wiloke::getPostTerms($post, 'listing_tag');
        $aListingCatIDs      = array();
        $aListingLocationIDs = array();
        $aTagIDs             = array();
        foreach ( $aListingCats as $key => $oTerm ){
            $oTerm = get_object_vars($oTerm);
            $oTerm['link'] = get_term_link($oTerm['term_id']);
            $oTerm = (object)$oTerm;
            $aListingCats[$key] = (object)$oTerm;
            Wiloke::setTermsCaching('listing_cat', array($oTerm->term_id=>$oTerm));
            $aListingCatIDs[] = $oTerm->term_id;
        }

        foreach ( $aListingLocations as $key => $oTerm ){
            $oTerm = get_object_vars($oTerm);
            $oTerm['link'] = get_term_link($oTerm['term_id']);
            $oTerm = (object)$oTerm;
            $aListingLocations[$key] = (object)$oTerm;
            Wiloke::setTermsCaching('listing_location', array($oTerm->term_id=>$oTerm));
            $aListingLocationIDs[] = $oTerm->term_id;
        }

        foreach ( $aTags as $key => $oTerm ){
            $oTerm = get_object_vars($oTerm);
            $oTerm['link'] = get_term_link($oTerm['term_id']);
            $oTerm = (object)$oTerm;
            $aTags[$key] = (object)$oTerm;
            Wiloke::setTermsCaching('listing_tag', array($oTerm->term_id=>$oTerm));
            $aTagIDs[] = $oTerm->term_id;
        }

        $favorite = 0;
        if ( class_exists('WilokeListGoFunctionality\AlterTable\AlterTableFavirote') ){
            $favorite = AlterTableFavirote::getStatus($post->ID);
        }

        $aFirstCatInfo = array();
        if ( isset($aListingCatIDs[0]) ){
            $aFirstCatInfo = Wiloke::getTermOption($aListingCatIDs[0]);
        }

        $authorID = isset($post->post_author) ? $post->post_author : $post->author_id;
        if ( isset($aListingLocationIDs[0]) ){
            $locationPlaceID = get_term_meta($aListingLocationIDs[0], 'wiloke_listing_location_place_id', true);
            $parentLocation = $aListingLocations[0]->parent;
            $firstLocationID = $aListingLocationIDs[0];
        }

        $aListing = array(
            'ID'                    => $post->ID,
            'title'                 => isset($post->post_title) ? $post->post_title : $post->title,
            'link'                  => get_permalink($post->ID),
            'featured_image'        => get_the_post_thumbnail_url($post->ID, 'medium'),
            'thumbnail'             => get_the_post_thumbnail_url($post->ID, 'thumbnail'),
            'listing_settings'      => Wiloke::getPostMetaCaching($post->ID, 'listing_settings'),
            'listing_cat'           => $aListingCats,
            'average_rating'        => self::calculateAverageRating($post),
            'tag'                   => $aTags,
            'listing_location'      => $aListingLocations,
            'listing_cat_id'        => $aListingCatIDs,
            'listing_location_id'   => $aListingLocationIDs,
            'first_cat_info'        => $aFirstCatInfo,
            'placeID'               => $locationPlaceID,
            'first_location_id'     => $firstLocationID,
            'parentLocationID'      => $parentLocation,
            'tag_id'                => $aTagIDs,
            'author'                => WilokeAuthor::getAuthorInfo($authorID, true),
            'listing_cat_settings'  => $aFirstCatInfo,
            'business_status'       => self::businessStatus($post),
            'favorite'              => $favorite
        );

        return $aListing;
    }

    public static function renderBreadcrumb() { ?>
        <?php if ( !is_home() && !is_front_page() ) : ?>
        <div class="header-page__breadcrumb">
            <div class="container">
                <ol class="wo_breadcrumb">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'listgo'); ?></a></li>
                    <?php if ( is_author() ) : ?>
                    <li><span><?php echo esc_html(get_query_var('author_name')); ?></span></li>
                    <?php
                        elseif(is_tax() || is_archive() || is_search()) : $oTerm = get_queried_object();
                        if ( !empty($oTerm) ) :
                        ?>
                        <?php if ( !empty($oTerm->parent) ) : $oParent = Wiloke::getTermCaching($oTerm->taxonomy, $oTerm->parent); ?>
                            <li><a href="<?php echo esc_url($oParent->link); ?>"><?php echo esc_html($oParent->name); ?></a></li>
                        <?php endif; ?>
                        <li><span><?php echo esc_html($oTerm->name); ?></span></li>
                        <?php
                        else:
                            $title = is_search() ? esc_html__('Search', 'listgo') : get_the_archive_title();
                            if ( !empty($title) ) :
                        ?>
                            <li><span><?php echo esc_html($title); ?></span></li>
                        <?php endif; endif; ?>
                    <?php elseif ( is_page_template() ) : global $post; ?>
                    <li><span><?php echo esc_html($post->post_title); ?></span></li>
                    <?php elseif ( is_singular() && !is_page() ) : ?>
                        <?php  global $post; $taxName = $post->post_type === 'listing' ? 'listing_location' : 'category'; $oTerm = Wiloke::getPostTerms($post, $taxName); ?>
                        <?php if ( !empty($oTerm) && !is_wp_error($oTerm) ) : ?>
                            <?php if ( !empty($oTerm->parent) ) : $oParent = Wiloke::getTermCaching($taxName, $oTerm->term_id); ?>
                                <li><a href="<?php echo esc_url($oParent->link); ?>"><?php echo esc_html($oParent->name); ?></a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo esc_url($oTerm->link); ?>"><?php echo esc_html($oTerm->name); ?></a></li>
                        <?php endif; ?>
                            <li><span><?php echo esc_html($post->post_title); ?></span></li>
                    <?php endif; ?>
                </ol>
                <span class="header-page__breadcrumb-filter"><i class="fa fa-filter"></i> <?php echo esc_html__('Filter', 'listgo'); ?></span>
            </div>
        </div>
        <?php endif;
    }

    public static function getHeaderSettingFromTerm($oObject){
        global $wiloke;
        $aTaxSettings = Wiloke::getTermOption($oObject->term_id);
        if ( isset($aTaxSettings['featured_image']) && !empty($aTaxSettings['featured_image']) ){
            $headerURL = wp_get_attachment_image_url($aTaxSettings['featured_image'], 'large');
        }elseif(isset($wiloke->aThemeOptions['listing_header_image']['id'])){
            $headerURL = wp_get_attachment_image_url($wiloke->aThemeOptions['listing_header_image']['id'], 'large');
        }elseif(isset($wiloke->aThemeOptions['listing_header_image']['url'])){
            $headerURL = $wiloke->aThemeOptions['listing_header_image']['url'];
        }

        if ( isset($aTaxSettings['header_overlay']) && !empty($aTaxSettings['header_overlay']) ){
            $overlayColor = $aTaxSettings['header_overlay'];
        }else{
            $overlayColor = !empty($wiloke->aThemeOptions['listing_header_overlay']) ? $wiloke->aThemeOptions['listing_header_overlay']['rgba'] : '';
        }

        return array(
            'headerurl' => $headerURL,
            'overlaycolor' => $overlayColor
        );
    }

    public static function headerPage(){
        global $post, $wiloke;
        $headerURL = null; $overlayColor = '';
        $postTitle = '';
        $desc = '';

        if ( is_home() || is_front_page() ) {
            $postTitle = get_option('blogname');
            $aPageSettings = Wiloke::getPostMetaCaching($post->ID, 'page_settings');
            if (!isset($aPageSettings['toggle_header_image']) || ($aPageSettings['toggle_header_image'] === 'disable') ){
                return false;
            }
        }else if ( is_search() ){
            $postTitle = esc_html__('Search results for: ', 'listgo') . get_query_var('s');
        }else if ( is_category() ) {
            $oObject = get_queried_object();
            $postTitle = esc_html__('Category: ', 'listgo') . $oObject->name;
            $aHeaderSettings = self::getHeaderSettingFromTerm($oObject);
            $headerURL = $aHeaderSettings['headerurl'];
            $overlayColor = $aHeaderSettings['overlaycolor'];
            $desc = $oObject->description;
        }elseif(is_tag()) {
            $oObject = get_queried_object();
            $postTitle = esc_html__('Tag: ', 'listgo') . $oObject->name;
            $aHeaderSettings = self::getHeaderSettingFromTerm($oObject);
            $headerURL = $aHeaderSettings['headerurl'];
             $desc = $oObject->description;
            $overlayColor = $aHeaderSettings['overlaycolor'];
        }elseif( is_singular() || is_page_template('templates/homepage.php') ){
            $aPageSettings = Wiloke::getPostMetaCaching($post->ID, 'page_settings');
            if ( is_page_template('templates/homepage.php') && ( !isset($aPageSettings['toggle_header_image']) || ($aPageSettings['toggle_header_image'] === 'disable')) ){
                return false;
            }
            if ( !empty($aPageSettings['header_image']) ){
                $headerURL = $aPageSettings['header_image'];
            }else if( has_post_thumbnail($post->ID) ){
				$headerURL = get_the_post_thumbnail_url($post->ID, 'large');
			}

            if ( !empty($aPageSettings['header_overlay']) ){
                $overlayColor = $aPageSettings['header_overlay'];
            }
            $postTitle = $post->post_title;
        }elseif(is_tax()){
            $oObject = get_queried_object();
            $postTitle = $oObject->name;
            $aHeaderSettings = self::getHeaderSettingFromTerm($oObject);
            $headerURL = $aHeaderSettings['headerurl'];
            $overlayColor = $aHeaderSettings['overlaycolor'];
            $desc = $oObject->description;
        }elseif(is_author()){
            $authorID = get_query_var( 'author' );
            $aAuthorInfo = Wiloke::getUserMeta($authorID);
            $postTitle = esc_html__('All posts by: ', 'listgo') . $aAuthorInfo['display_name'];
            if ( !empty($aAuthorInfo['meta']) ){
                $headerURL = !empty($aAuthorInfo['meta']['wiloke_cover_image']) ? wp_get_attachment_image_url($aAuthorInfo['meta']['wiloke_cover_image'], 'large') : '';
                $overlayColor = !empty($aAuthorInfo['meta']['wiloke_color_overlay']) ? $aAuthorInfo['meta']['wiloke_color_overlay'] : '';
            }
        }else if(is_archive()){
            $postTitle = esc_html__('Archive: ', 'listgo') . get_the_archive_title();
        }else if( is_page() || is_singular() ){
            $postTitle = get_the_title();
        }

        if ( !empty($wiloke->aThemeOptions) ){
            if ( empty($headerURL) && !empty($wiloke->aThemeOptions['blog_header_image']) ){
               $headerURL = wp_get_attachment_image_url($wiloke->aThemeOptions['blog_header_image']['id'], 'large');
               $headerURL = !empty($headerURL) ? $headerURL : $wiloke->aThemeOptions['blog_header_image']['url'];
            }

            if ( empty($overlayColor) && !empty($wiloke->aThemeOptions['blog_header_overlay']) ){
                $overlayColor = $wiloke->aThemeOptions['blog_header_overlay']['rgba'];
            }
        }

        ?>
        <div class="header-page bg-scroll lazy" data-src="<?php echo esc_url($headerURL); ?>">
  
            <div class="header-page__inner">
                <h1 class="header-page__title"><?php echo esc_html($postTitle); ?></h1>
                <?php if ( !empty($desc) ) : ?>
                <p class="term-description"><?php Wiloke::wiloke_kses_simple_html($desc); ?></p>
                <?php endif; ?>
            </div>
      
            <?php self::renderBreadcrumb(); ?>
            <div class="overlay" style="background-color: <?php echo esc_attr($overlayColor); ?>"></div>
        </div>
        <?php
    }

    public static function renderTaxonomy($postID, $termName='listing_location', $onlyOne=false){
        $oTerms = wp_get_post_terms($postID, $termName);
        $termOrder = 1;
        if ( !empty($oTerms) && !is_wp_error($oTerms) ) :
            if ( $onlyOne ) :
            ?>
            <div class="listing__cat">
                <a href="<?php echo esc_url(get_term_link($oTerms[0]->term_id)); ?>"><?php echo esc_html($oTerms[0]->name); ?></a>
            </div>
            <?php
            else:
                $total = count($oTerms);
                foreach ( $oTerms as $order => $oTerm ) :
                    $oTerm          = get_object_vars($oTerm);
                    $oTerm['link']  = get_term_link($oTerm['term_id']);
                    $oTerm          = (object)$oTerm;
                    $oTerms[$order] = $oTerm;

                    if($termOrder === 2){
                        ?>
                        <ul class="listing__cats">
                        <?php
                    }

                    if ($termOrder === 1) :
                        ?>
                        <a href="<?php echo esc_url($oTerm->link); ?>"><?php echo esc_html($oTerm->name); ?></a>
                        <?php
                        if ( $total > 1 ) :
                        ?>
                            <span class="listing__cat-more">+</span>
                        <?php
                        endif;
                    else:
                    ?>
                        <li><a href="<?php echo esc_url($oTerm->link); ?>"><?php echo esc_html($oTerm->name); ?></a></li>
                    <?php
                    Wiloke::setTermsCaching($oTerm->taxonomy, array($oTerm->term_id=>$oTerm));
                    endif;

                    if ( $total > 1 && $total === $termOrder ){
                        echo '</ul>';
                    }

                    $termOrder++;
                endforeach;
            endif;
        endif;
    }

    public static function renderMapPage($search='',$mapPage='', $atts=null, $hasIcon = true, $additionalClass=''){
        $text = esc_html__('Go to map', 'listgo');
        $itemClass = 'tb__cell ' . $additionalClass . (isset($atts['link_to_map_page_additional_class']) ? ' ' . $atts['link_to_map_page_additional_class'] : '');
        $itemClass = trim($itemClass);

        if ( !empty($atts) ){
            if ( $atts['toggle_render_link_to_map_page'] === 'disable' ){
                return false;
            }

            $text = empty($atts['link_to_map_page_text']) ? esc_html__('Go to map', 'listgo') : $atts['link_to_map_page_text'];
        }

        if ( $hasIcon ){
            $tooltip = $text;
            $text = '<i class="icon_pin_alt"></i>';
        }else{
            $tooltip = '';
        }

	    global $wiloke;
        $mapPage = isset($wiloke->aThemeOptions['header_search_map_page']) && !empty($wiloke->aThemeOptions['header_search_map_page']) ? get_permalink($wiloke->aThemeOptions['header_search_map_page']) : $mapPage;
        ?>
        <div class="listgo-go-to-map-btn <?php echo esc_attr($itemClass); ?>">
            <?php if ( !empty($tooltip) ) : ?>
            <a href="<?php echo esc_url($mapPage . '?' . $search); ?>" data-tooltip="<?php echo esc_attr($tooltip); ?>">
                <?php Wiloke::wiloke_kses_simple_html($text); ?>
            </a>
            <?php else: ?>
            <a href="<?php echo esc_url($mapPage . '?' . $search); ?>">
                <?php Wiloke::wiloke_kses_simple_html($text); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php
    }

    public static function renderViewDetail($post, $atts=null, $additionalClass=''){
        $text = esc_html__('View Detail', 'listgo');
        $itemClass = 'tb__cell ' . $additionalClass;
        $itemClass = trim($itemClass);

        if ( !empty($atts) ){
            if ( $atts['toggle_render_view_detail'] === 'disable' ){
                return false;
            }
            $text = empty($atts['view_detail_text']) ? esc_html__('View Detail', 'listgo') : $atts['view_detail_text'];
        }

        ?>
        <div class="<?php echo esc_attr($itemClass); ?> listgo-view-detail-btn">
            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo esc_attr($text); ?></a>
        </div>
        <?php
    }

    public static function renderFindDirection($aPageSettings, $atts=null, $additionalClass=''){
        $text = esc_html__('Find directions', 'listgo');
        $itemClass = 'tb__cell ' . $additionalClass;
        $itemClass = trim($itemClass);

        if ( !empty($atts) ){
            if ( $atts['toggle_render_find_direction'] === 'disable' ){
                return false;
            }
            $text = empty($atts['find_direction_text']) ? esc_html__('Find Directions', 'listgo') : $atts['find_direction_text'];
        }

        if ( !$aPageSettings || !isset($aPageSettings['map']) ){
            return false;
        }
        ?>
        <div class="<?php echo esc_attr($itemClass); ?> listgo-redirect-to-btn">
            <a href="<?php echo esc_url(self::$googleMap . $aPageSettings['map']['location'] . '/' . $aPageSettings['map']['latlong']); ?>" target="_blank" data-tooltip="<?php echo esc_attr($text); ?>">
                <i class="arrow_left-right_alt"></i>
            </a>
        </div>
        <?php
    }

    public function getTermChildren(){
        if ( !isset($_GET['term_id']) || !isset($_GET['taxonomy']) ){
            wp_send_json_success(array(-1));
        }
        wp_send_json_success(Wiloke::getTermChildren($_GET['term_id'], $_GET['taxonomy']));
    }

    public static function singleHeaderBg($post, $aPageSettings){
        $bgUrl = '';
        if ( isset($aPageSettings['header_image_id']) && !empty($aPageSettings['header_image_id']) ){
            $bgUrl =  wp_get_attachment_image_url($aPageSettings['header_image_id'], 'large');
        }else if ( has_post_thumbnail($post->ID) ){
            $bgUrl =  get_the_post_thumbnail_url($post->ID, 'large');
        }
        $overlayColor = isset($aPageSettings['header_overlay']) ? $aPageSettings['header_overlay'] : '';
        ?>
        <div class="header-page bg-scroll lazy" data-src="<?php echo esc_url($bgUrl); ?>">

            <div class="container">
                <div class="header-page__inner">
                    <h2 class="header-page__title"><?php echo esc_html($post->post_title); ?></h2>
                </div>
            </div>

            <div class="header-page__breadcrumb">
                <div class="container">
                    <ol class="wo_breadcrumb">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'listgo'); ?></a></li>
                        <li><span><?php echo esc_html($post->post_title); ?></span></li>
                    </ol>
                </div>
            </div>
            <div class="overlay" style="background-color: <?php echo esc_attr($overlayColor); ?>"></div>
        </div>
        <?php
    }

    public static function getNumberOfFollowing($userID=null){
        global $wpdb;
        $userID = empty($userID) ? self::$oUserInfo->ID : $userID;
        if ( Wiloke::$wilokePredis && Wiloke::$wilokePredis->exists(RegisterFollow::$redisFollowing) ){
            $aFollowings = Wiloke::hGet(RegisterFollow::$redisFollowing, $userID);
            return $aFollowings ? count($aFollowings) : 0;
        }else{
            $tblUser = $wpdb->prefix . 'users';
			$tblFollowing = $wpdb->prefix . AltertableFollowing::$tblName;
            $total = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT($tblFollowing.user_ID) FROM $tblUser INNER JOIN $tblFollowing ON ($tblFollowing.user_ID = $tblUser.ID) WHERE $tblFollowing.follower_ID = %d",
					$userID
				)
			);

            return $total;
        }
    }

    public static function getNumberOfFollowers($userID=null){
        global $wpdb;
        $userID = empty($userID) ? self::$oUserInfo->ID : $userID;
        if ( Wiloke::$wilokePredis && Wiloke::$wilokePredis->exists(RegisterFollow::$redisFollower) ){
            $aFollowers = Wiloke::hGet(RegisterFollow::$redisFollower, $userID);
            return $aFollowers ? count($aFollowers) : 0;
        }else{
            $tblUser = $wpdb->prefix . 'users';
			$tblFollowing = $wpdb->prefix . AltertableFollowing::$tblName;
            $total = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT($tblFollowing.follower_ID) FROM $tblUser INNER JOIN $tblFollowing ON ($tblFollowing.follower_ID = $tblUser.ID) WHERE $tblFollowing.user_ID = %d",
					$userID
				)
			);
            return $total;
        }
    }

    public static function accountHeaderBg($authorID=null){
        global $wiloke;
        if ( empty($authorID) ){
            $authorID = isset($_REQUEST['user']) && !empty($_REQUEST['user']) ? $_REQUEST['user'] : '';
        }

        if ( empty($authorID) ){
            $oUserInfo = self::$oUserInfo;
            $authorID = self::$oUserInfo->ID;
        }else{
            $oUserInfo = (object)Wiloke::getUserMeta($authorID);
        }

        if (isset($oUserInfo->meta['wiloke_cover_image']) && !empty($oUserInfo->meta['wiloke_cover_image'])){
            $bgUrl =  wp_get_attachment_image_url($oUserInfo->meta['wiloke_cover_image'], 'large');
        }else{
            $bgUrl =  wp_get_attachment_image_url($wiloke->aThemeOptions['header_cover_image']['id'], 'large');
        }
        $status = self::isFollowingThisUser($authorID);
        $overlayColor = isset($oUserInfo->meta['wiloke_color_overlay']) ? $oUserInfo->meta['wiloke_color_overlay'] : '';
        $profileUrl = self::getPaymentField('myaccount', true);
        $followingUrl = !empty($profileUrl) ? self::addQueryToLink($profileUrl, 'mode=following&user='.$oUserInfo->ID) : '#';
        $followersUrl = !empty($profileUrl) ? self::addQueryToLink($profileUrl, 'mode=followers&user='.$oUserInfo->ID) : '#';
        if ( is_author() ){
            $profileUrl = !empty($profileUrl) ? self::addQueryToLink($profileUrl, 'mode=profile&user='.$authorID) : '#';
        }else{
            $profileUrl = get_author_posts_url($authorID);
        }
        ?>
        <div class="header-page header-page--account bg-scroll lazy" data-src="<?php echo esc_url($bgUrl); ?>">
            <div class="container">
                <div class="header-page__account">
                    <div class="header-page__account-avatar">
                        <a href="<?php echo esc_url($profileUrl); ?>">
                            <div class="header-page__account-avatar-img bg-scroll">
                                <?php
                                $avatar = Wiloke::getUserAvatar($oUserInfo->ID, null, array(65, 65));
                                if ( strpos($avatar, 'profile-picture.jpg') === false ) {
                                    ?>
                                    <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($oUserInfo->display_name); ?>" height="65" width="65" class="avatar">
                                    <?php
                                } else {
                                    $firstCharacter = strtoupper(substr($oUserInfo->display_name, 0, 1));
                                    echo '<span style="background-color: '.esc_attr(self::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
                                }
                                ?>
                            </div>
                            <h4 class="header-page__account-name"><?php echo esc_html($oUserInfo->display_name); ?></h4>
                            <?php self::renderBadge($oUserInfo->role); ?>
                        </a>
                    </div>
                    <div class="account-subscribe">
                        <span class="followers"><a href="<?php echo esc_url($followersUrl) ?>"><span class="count"><?php echo esc_html(self::getNumberOfFollowers($oUserInfo->ID)); ?></span> <?php esc_html_e('Followers', 'listgo'); ?></a></span>
                        <span class="following"><a href="<?php echo esc_url($followingUrl) ?>"><span class="count"><?php echo esc_html(self::getNumberOfFollowing($oUserInfo->ID)); ?></span> <?php esc_html_e('Following', 'listgo'); ?></a></span>
                        <?php if ( empty(self::$oUserInfo) || ( !empty($authorID) && (self::$oUserInfo->ID != $authorID) ) ) :  ?>
                        <a href="#" data-status="<?php echo esc_attr($status); ?>" class="listgo-btn listgo-btn--sm js_subscribe_on_profile" data-authorid="<?php echo esc_attr($authorID); ?>"><?php echo esc_attr($status) ? esc_html($wiloke->aConfigs['translation']['followingtext']) : esc_html($wiloke->aConfigs['translation']['unfollowingtext']); ?> <i class="fa fa-rss"></i></a>
                        <?php endif; ?>
                        <?php
                        if ( is_page_template('wiloke-submission/myaccount.php') ) :
                            $accountPage = WilokePublic::getPaymentField('myaccount', true); ?>
                        <a class="listgo-btn listgo-btn--sm" href="<?php echo esc_url(self::addQueryToLink($accountPage, 'mode=edit-profile')); ?>">
                            
                            <?php if (strpos($bgUrl, 'cover-image.jpeg') === false ): ?>
                                <i class="fa fa-edit"></i>
                            <?php endif ?>
                            
                            <?php esc_html_e('Edit Profile', 'listgo'); ?>
            
                            <?php if ( strpos($bgUrl, 'cover-image.jpeg') >= 0 ): ?>
                                <i class="fa fa-exclamation"></i> 
                            <?php endif ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="overlay" style="background-color: <?php echo esc_attr($overlayColor); ?>"></div>
        </div>
        <?php
    }

    public function editListingCat($termID){
        if ( Wiloke::$wilokePredis ){
            $oTerm = Wiloke::getTermCaching('listing_cat', $termID);
            if ( $oTerm->slug !== $_POST['slug'] ){
                $aListingsInTerm = Wiloke::$wilokePredis->zrange($oTerm->slug, 0, -1);
                if ( !empty($aListingsInTerm) ){
                    foreach ( $aListingsInTerm as $postID ){
                        Wiloke::$wilokePredis->zrem($oTerm->slug, $postID);
                        $aListingSettings = Wiloke::getPostMetaCaching($postID, 'listing_settings');
                        $aLatLng = explode(',', $aListingSettings['map']['latlong']);
                        Wiloke::$wilokePredis->geooadd($_POST['slug'], $aLatLng[1], $aLatLng[0], $postID);
                    }
                }
            }
        }
    }

    public function deleteListingCat($oTerm){
        if ( Wiloke::$wilokePredis ){
            $aListingsInTerm = Wiloke::$wilokePredis->zrange($oTerm->slug, 0, -1);
            if ( !empty($aListingsInTerm) ){
                foreach ( $aListingsInTerm as $postID ){
                    Wiloke::$wilokePredis->zrem($oTerm->slug, $postID);
                }
            }
        }
    }

    public static function getPaymentField($field='', $isUrl=false){
        if ( !class_exists('WilokeListGoFunctionality\Register\RegisterWilokeSubmission') ){
            return false;
        }

        if ( empty(self::$aPaymentFields) ){
            $aData = get_option(RegisterWilokeSubmission::$submissionConfigurationKey);
            self::$aPaymentFields = $aData;
        }else{
            $aData = self::$aPaymentFields;
        }

        if ( empty($aData) ){
            return false;
        }
        
        $aData = json_decode($aData, true);
        if ( empty($aData) ){
            $aData = json_decode(stripslashes($aData), true);
        }
        
        if ( empty($field) ){
            return $aData;
        }

        $val = isset($aData[$field]) ? $aData[$field] : '';

        if ( $isUrl ){
            return get_permalink($val);
        }else{
            return $val;
        }
    }

    public static function getRemainingOfPackage($planID, $aPostMeta){
        if ( !is_user_logged_in() ){
            return false;
        }

        $aUserPlanDetail = \WilokeListgoFunctionality\Model\UserModel::getDetailPlan($planID);
        if ( empty($aUserPlanDetail) ){
            return false;
        }
        
        return array(
            'purchased' => 1,
            'remaining' => $aUserPlanDetail['remainingItems'],
            'type'      => empty($aPostMeta['price']) ? 'free' : 'premium'
        );
    }

    public static function postMeta($post){
        global $wiloke;
    ?>
        <div class="listing-single__meta">
            <?php do_action('wiloke/listgo/admin/public/after_listing-single__meta_open', $post); ?>
            <?php if ( $wiloke->aThemeOptions['listing_toggle_posted_on'] === 'enable' ) : ?>
                <div class="listing-single__date">
                    <span class="listing-single__label"><?php esc_html_e('Posted on', 'listgo'); ?></span>
                    <?php Wiloke::renderPostDate($post->ID); ?>
                </div>
            <?php endif; ?>

            <?php if ( $wiloke->aThemeOptions['listing_toggle_categories'] === 'enable' ) : ?>
                <div class="listing__meta-cat">
                    <?php Wiloke::theTerms('listing_cat', $post, '<span class="listing-single__label">', esc_html__('Category|Categories', 'listgo'), '</span>', ', ', ''); ?>
                    <?php do_action('wiloke/listgo/admin/public/postMeta/listing__meta-cat', $post); ?>
                </div>
            <?php endif; ?>

            <?php if ( $wiloke->aThemeOptions['listing_toggle_locations'] === 'enable' ) : ?>
                <div class="listing__meta-cat listing__meta-location">
                    <?php Wiloke::theTerms('listing_location', $post, '<span class="listing-single__label">', esc_html__('Location|Locations', 'listgo'), '</span>', ', ', ''); ?>
                </div>
            <?php endif; ?>

            <?php if ( $wiloke->aThemeOptions['listing_toggle_tags'] === 'enable' ) : ?>
                <div class="listing__meta-cat listing__meta-tag">
                    <?php Wiloke::theTerms('listing_tag', $post, '<span class="listing-single__label">', esc_html__('Tag|Tags', 'listgo'), '</span>', ', ', ''); ?>
                </div>
            <?php endif; ?>

            <?php if ( $wiloke->aThemeOptions['listing_toggle_rating_result'] === 'enable' ) : ?>
            <div class="listing-single__review">
                <span class="listing-single__label"><?php esc_html_e('Rating', 'listgo'); ?></span>
                <?php self::renderAverageRating($post, array('toggle_render_rating'=>'enable')); ?>
            </div>
            <?php endif; ?>
            <?php
            ob_start();
            WilokePublic::renderListingStatus($post, true);
            $status = ob_get_clean();
            if ( !empty($status) ) :
            $toggleBusinessHour = get_post_meta($post->ID, 'wiloke_toggle_business_hours', true);
                if ( isset($toggleBusinessHour) && $toggleBusinessHour === 'enable' ) :
            ?>
            <div class="listing-single__status">
                <span class="listing-single__label"><?php esc_html_e('Status', 'listgo'); ?></span>
                <?php echo $status; ?>
            </div>
            <?php
                endif;
            endif;
            ?>
            <?php do_action('wiloke/listgo/admin/public/before_listing-single__meta_open', $post); ?>
        </div>
    <?php
    }

    public static function isFollowing($post){
        if ( empty(self::$oUserInfo) ){
            return false;
        }

        if ( Wiloke::$wilokePredis ){
            $aFollowers = Wiloke::hGet(RegisterFollow::$redisFollower, $post->post_author, true);
            if ( empty($aFollowers) || !in_array(self::$oUserInfo->ID, $aFollowers) ){
                return false;
            }

            return true;
        }else{
            global $wpdb;
            $tblName = $wpdb->prefix . AltertableFollowing::$tblName;

            $result = $wpdb->get_var($wpdb->prepare(
               "SELECT user_ID FROM $tblName WHERE user_ID=%d AND follower_ID=%d",
               $post->post_author,
               self::$oUserInfo->ID
            ));

            if ( empty($result) ){
                return false;
            }

            return true;
        }
    }

    public static function isFollowingThisUser($userID){
        if ( empty(self::$oUserInfo) || empty($userID) ){
            return false;
        }

        if ( Wiloke::$wilokePredis ){
            $aFollowers = Wiloke::hGet(RegisterFollow::$redisFollower, $userID, true);
            if ( empty($aFollowers) || !in_array(self::$oUserInfo->ID, $aFollowers) ){
                return false;
            }

            return true;
        }else{
            global $wpdb;
            $tblName = $wpdb->prefix . AltertableFollowing::$tblName;

            $result = $wpdb->get_var($wpdb->prepare(
               "SELECT user_ID FROM $tblName WHERE user_ID=%d AND follower_ID=%d",
               $userID,
               self::$oUserInfo->ID
            ));

            if ( empty($result) ){
                return false;
            }

            return true;
        }
    }

    public static function listingAction($post){
        global $wiloke;
        ?>
        <div class="listing-single__actions">
            <ul>
                <?php if ( $wiloke->aThemeOptions['listing_toggle_report'] === 'enable' ) : ?>
                    <li class="js_report action__report" data-tooltip="<?php esc_html_e('Report', 'listgo'); ?>">
                        <a href="#"><i class="icon_error-triangle_alt"></i></a>
                    </li>
                <?php endif; ?>

                <?php if ( ($wiloke->aThemeOptions['listing_toggle_sharing_posts'] === 'enable') && class_exists('WilokeSharingPost') ) : ?>
                    <li class="action__share" data-tooltip="<?php esc_html_e('Share', 'listgo'); ?>">
                        <a href="#"><i class="social_share"></i></a>
                        <?php echo do_shortcode('[wiloke_sharing_post]'); ?>
                    </li>
                <?php endif; ?>

                <?php
                if ( $wiloke->aThemeOptions['listing_toggle_add_to_favorite'] === 'enable' ) :
                    $class = 'js_favorite';
                    if ( class_exists('WilokeListGoFunctionality\AlterTable\AlterTableFavirote') ){
                        $favorite = AlterTableFavirote::getStatus($post->ID);
                        $class .= !empty($favorite) ? ' active' : '';
                    }
                    ?>
                    <li class="action__like" data-tooltip="<?php echo esc_attr($wiloke->aConfigs['translation']['addtofavorite']); ?>">
                        <a class="<?php echo esc_attr($class); ?>" href="#" data-postid="<?php echo esc_attr($post->ID); ?>"><i class="icon_heart_alt"></i></a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
        <?php
    }

    public static function insertAttachment($aFile, $parentPostID=0){
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        $upload_overrides = array('test_form' => false);
        $aMoveFile = wp_handle_upload($aFile, $upload_overrides );

        // Check the type of file. We'll use this as the 'post_mime_type'.
        $fileType = wp_check_filetype( basename($aMoveFile['file']), null );

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        // Prepare an array of post data for the attachment.
        $attachment = array(
            'guid'           => $wp_upload_dir['url'] . '/' . basename($aMoveFile['file']),
            'post_mime_type' => $fileType['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($aMoveFile['file']) ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );
        // Insert the attachment.
        $attachID = wp_insert_attachment($attachment, $aMoveFile['file'], $parentPostID);

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Generate the metadata for the attachment, and update the database record.
        $aAttachData = wp_generate_attachment_metadata($attachID, $aMoveFile['file']);
        wp_update_attachment_metadata($attachID, $aAttachData);
        return $attachID;
    }

    public function filterWilokeSharingPostsTitle(){
        return '';
    }

    public function filterWilokeSharingPostsCssClass($class){
        return $class . ' action__share-list';
    }

    public function filterWilokeSharingPostsRenderTitle($aAttribute){
        ob_start();
        ?>
         <i class="<?php echo esc_attr($aAttribute['name_class']); ?>"></i> <?php echo esc_html($aAttribute['title']); ?>
        <?php
        $content = ob_get_clean();
        return $content;
    }

    public static function renderUserBadge($role){
        $aBadge = RegisterBadges::getBadgeInfo($role);
        echo '<i class="fa fa-gitlab"></i>' . esc_html($aBadge['label']);
    }

    public static function getUserMeta($userID, $field=''){
        $aUser = Wiloke::getUserMeta($userID, $field);
        return $aUser;
    }

    public static function toggleTabStatus($name){
        global $wiloke;
        $status = !isset($wiloke->aThemeOptions[$name]) || ($wiloke->aThemeOptions[$name]  === 'enable') ? 'enable' : 'disable';
        return $status;
    }

    public static function renderListingTab($name){
        global $wiloke;
        $class = '';
        if ( $name === 'description' ){
            $status = self::toggleTabStatus('listing_toggle_tab_desc');
            $tabName = isset($wiloke->aThemeOptions['listing_tab_desc']) ? $wiloke->aThemeOptions['listing_tab_desc'] : esc_html__('Description', 'listgo');
            $class = 'active';
        }elseif ( $name === 'contact' ){
            $status = self::toggleTabStatus('listing_toggle_tab_contact_and_map');
	        $tabName = isset($wiloke->aThemeOptions['listing_tab_contact_and_map']) ? $wiloke->aThemeOptions['listing_tab_contact_and_map'] : esc_html__('Contact & Map', 'listgo');
        }else{
            $status = self::toggleTabStatus('listing_toggle_tab_review_and_rating');
	        $tabName = isset($wiloke->aThemeOptions['listing_tab_review_and_rating']) ? $wiloke->aThemeOptions['listing_tab_review_and_rating'] : esc_html__('Review & Rating', 'listgo');
        }

        if ( $status === 'enable' ) :
        ?>
        <li class="<?php echo esc_attr($class); ?>"><a href="#tab-<?php echo esc_attr($name); ?>"><?php echo esc_html($tabName); ?></a></li>
        <?php
        endif;
    }


    public function fetchListingsNearByVisitor(){
        $lngLng = explode(',', $aData['latlong']);
    }
    
    public static function renderRelatedPosts(){
        global $post, $wiloke;

        if ( isset($wiloke->aThemeOptions['listing_toggle_related_listings']) && ($wiloke->aThemeOptions['listing_toggle_related_listings'] === 'disable') ){
            return false;
        }

        if ( $wiloke->aThemeOptions['listing_related_listings_by'] != 'listing_nearby_and_location_fb' ){
            $aArgs = array(
                'post_type'     => $post->post_type,
                'post_status'   => 'publish',
                'post__not_in'  => array($post->ID),
                'orderby'       => 'menu_order rand'
            );

            $catName = '';

            if ( is_singular('post') ){
                $aCategories = get_the_category($post->ID);
                if ( empty($aCategories) || is_wp_error($aCategories) ){
                    return '';
                }
                foreach ( $aCategories as $oCat ){
                    $aArgs['category__in'][] = $oCat->term_id;
                }
            }else{
                if ( $wiloke->aThemeOptions['listing_related_listings_by'] == 'author' ){
                    $aArgs['author__in'] = array($post->post_author);
                }else{
                    $aCategories = wp_get_post_terms($post->ID, $wiloke->aThemeOptions['listing_related_listings_by']);

                    if ( empty($aCategories) || is_wp_error($aCategories) ){
                        return '';
                    }
                    foreach ( $aCategories as $oCat ){
                        $aTermIDs[] = $oCat->term_id;
                        $catName = $oCat->name;
                    }
                    $aArgs['tax_query'][] = array(
                        'taxonomy' => $wiloke->aThemeOptions['listing_related_listings_by'],
                        'field' => 'term_id',
                        'terms' => $aTermIDs,
                    );
                }
            }

            if ( $wiloke->aThemeOptions['single_post_related_number_of_articles'] == 'col-md-6' ){
                $postsPerPage = 2;
            }else{
                $postsPerPage = 3;
            }
            $aArgs['posts_per_page'] = $postsPerPage;

            if ( is_singular('listing') ){
                $heading = $wiloke->aThemeOptions['listing_related_listings_title'];
            }else{
                $heading = $wiloke->aThemeOptions['single_post_related_posts_title'];
            }
            
            $heading = str_replace(array('%author%', '%category%', '%location%'), array(get_the_author(), $catName, $catName), $heading);

            $query = new WP_Query($aArgs);
            $listingImage = null;
            $size = wp_is_mobile() ? array(345, 260) : array(460, 345);

            if ( $query->have_posts() ) :
            ?>
                <div class="listing-single__related">
                    <h3 class="listing-single__related-title"><?php Wiloke::wiloke_kses_simple_html($heading); ?></h3>
                    <div class="row row-clear-lines">
                        <?php
                        while ($query->have_posts()) : $query->the_post();
                        if ( has_post_thumbnail($query->post->ID) ){
                            $thumbnail = get_the_post_thumbnail_url($query->post->ID, $size);
                        }else{
                             $listingImage = $listingImage == null ? wp_get_attachment_image_url($wiloke->aThemeOptions['listing_header_image']['id'], $size) : $listingImage;
                             $thumbnail = $listingImage;
                        }
                        ?>
                        <div class="col-sm-6 <?php echo esc_attr($wiloke->aThemeOptions['single_post_related_number_of_articles']); ?>">
                           <div class="listing_related-item">
                               <a href="<?php echo esc_url(get_permalink($query->post->ID)); ?>">
                                    <div class="listing_related-item__media lazy" data-src="<?php echo esc_url($thumbnail); ?>">
                                       <img class="lazy" data-src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($query->post->post_title); ?>" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==">
                                   </div>
                                   
                                   <div class="listing_related-item__body">
                                       <h2 class="listing_related-item__title"><?php echo esc_html($query->post->post_title); ?></h2>
                                   </div>
                               </a>
                           </div>
                       </div>
                        <?php endwhile;?>
                    </div>
                </div>
            <?php
            endif;
            wp_reset_postdata();
        }else{
            $heading = $wiloke->aThemeOptions['listing_related_listings_title'];
            $heading = str_replace(array('%author%', '%category%', '%location%'), array(get_the_author(), '', ''), $heading);
            ?>
            <div class="listing-single__related">
                <h3 class="listing-single__related-title"><?php Wiloke::wiloke_kses_simple_html($heading); ?></h3>
                <div id="listing-single_listing_near_by" class="row row-clear-lines"></div>
            </div>
            <?php
        }
    }

    public static function addQueryToLink($link, $query){
        if ( strpos($link, '?') !== false ){
            $link .= '&'.$query;
        }else{
            $link .= '?'.$query;
        }

        return $link;
    }

    public static function totalMyListings(){
        if ( isset(self::$aTemporaryCaching['total_my_listings']) ){
            return self::$aTemporaryCaching['total_my_listings'];
        }

        global $wpdb;
        $tblName = $wpdb->prefix . 'posts';
        $totalListings = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(ID) FROM $tblName WHERE post_author=%d AND post_type=%s AND post_status !='trash' AND post_status!='auto-draft'",
                self::$oUserInfo->ID, 'listing'
            )
        );

        self::$aTemporaryCaching['total_my_listings'] = $totalListings;

        return self::$aTemporaryCaching['total_my_listings'];
    }

    public static function quickUserInformation(){
        if ( !class_exists('WilokeListGoFunctionality\Register\RegisterWilokeSubmission') ){
            return false;
        }

        if ( !empty(self::$oUserInfo) ) :
            global $woocommerce;
            $aWilokeSubmissionSettings = WilokeWilokeSubmission::getSettings();

            if ( !isset($aWilokeSubmissionSettings['toggle']) || $aWilokeSubmissionSettings['toggle'] === 'disable' ){
                return false;
            }

            $avatar = Wiloke::getUserAvatar(null, get_object_vars(self::$oUserInfo), 'thumbnail');

            $accountPage = get_permalink($aWilokeSubmissionSettings['myaccount']);
            if ( strpos($accountPage, '?') !== false ){
                $myListingPage  = $accountPage . '&mode=my-listings';
                $favouritesPage = $accountPage . '&mode=my-favorites';
            }else{
                $myListingPage  = $accountPage . '?mode=my-listings';
                $favouritesPage = $accountPage . '?mode=my-favorites';
            }

            $totalListings = self::totalMyListings();
            $totalFavorites = WilokeFavorite::totalMyFavorites();
            $myAccount = get_permalink($aWilokeSubmissionSettings['myaccount']);
            $aBadge = RegisterBadges::getBadgeInfo(self::$oUserInfo->role);
        ?>
            <div class="header__user">
                <div class="tb">
                    <div class="tb__cell">
                        <div class="user__avatar">
                            <?php
                            if ( strpos($avatar, 'profile-picture.jpg') === false ) {
                            ?>
                            <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr(self::$oUserInfo->display_name); ?>" height="150" width="150" class="avatar">
	                        <?php
	                        } else {
                                $firstCharacter = strtoupper(substr(self::$oUserInfo->display_name, 0, 1));
		                        echo '<span style="background-color: '.esc_attr(self::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
	                        }
	                        ?>
                        </div>
                    </div>
                </div>
                <div class="user__menu">
                    <ul>
                        <li class="user__menu__header wiloke-view-profile">
                            <div class="user__header__avatar">
                                <?php
                                    if ( strpos($avatar, 'profile-picture.jpg') === false ) {
                                    ?>
                                    <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr(self::$oUserInfo->display_name); ?>" height="150" width="150" class="avatar">
                                    <?php
                                    } else {
                                        $firstCharacter = strtoupper(substr(self::$oUserInfo->display_name, 0, 1));
                                        echo '<span style="background-color: '.esc_attr(self::getColorByAnphabet($firstCharacter)).'" class="widget_author__avatar-placeholder">'. esc_html($firstCharacter) .'</span>';
                                    }
                                ?>
                            </div>
                            <div class="user__header__info">
                                <a href="<?php echo esc_url($accountPage); ?>">
                                    <h6><?php echo esc_html(self::$oUserInfo->display_name); ?></h6>
                                    <span><?php echo esc_html($aBadge['label']); ?></span>
                                </a>
                            </div>
                        </li>
                        <li class="user__menu__item wiloke-view-dashboard">
                            <a href="<?php echo esc_url($accountPage); ?>">
                                <i class="fa fa-home"></i>
                                <?php esc_html_e('Dashboard', 'listgo'); ?>
                            </a>
                        </li>
                        <li class="user__menu__item wiloke-view-mylistings">
                            <a href="<?php echo esc_url($myListingPage); ?>">
                                <i class="fa fa-list"></i>
                                <?php esc_html_e('My listings', 'listgo'); ?>
                                <span class="count"><?php echo esc_html($totalListings); ?></span>
                            </a>
                        </li>
                        <li class="user__menu__item wiloke-view-favorites">
                            <a href="<?php echo esc_url($favouritesPage); ?>">
                                <i class="fa fa-heart-o"></i>
                                <?php esc_html_e('Favorites', 'listgo'); ?>
                                <span class="count"><?php echo esc_html($totalFavorites); ?></span>
                            </a>
                        </li>
                       <li class="user__menu__item wiloke-view-billing"><a href="<?php echo esc_url(WilokePublic::addQueryToLink($myAccount, 'mode=my-billing')); ?>"><i class="icon_creditcard"></i> <?php esc_html_e('Billing', 'listgo'); ?></a></li>
                         <?php if ( function_exists('is_woocommerce') ) : ?>
                        <li class="user__menu__item wiloke-view-woocommerce">
                            <a href="<?php echo esc_url(wc_get_cart_url()); ?>">
                                <i class="icon_creditcard"></i>
                                <?php  esc_html_e('My cart', 'listgo'); ?>
                                <span class="count"><?php echo esc_html($woocommerce->cart->cart_contents_count); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="user__menu__item  wiloke-view-logout">
                            <a href="<?php echo wp_logout_url(home_url('/')); ?>">
                                <i class="fa fa-arrow-circle-o-left"></i>
                                <?php esc_html_e('Logout', 'listgo'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        <?php
        endif;
    }

    public function updateProfile(){
        if ( !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
            wp_send_json_error(array(
                'message' => esc_html__('The security code is wrong.', 'listgo')
            ));
        }

        parse_str($_POST['data'], $aData);

        if ( !isset($aData['user_email']) || empty($aData['user_email']) || !is_email($aData['user_email']) ){
            wp_send_json_error(array(
                'user_email' => esc_html__('You entered an invalid email address.', 'listgo')
            ));
        }

        if ( !isset($aData['display_name']) || empty($aData['display_name']) ){
            wp_send_json_error(array(
                'display_name' => esc_html__('The display name is required.', 'listgo')
            ));
        }

        if ( !isset($aData['nickname']) || empty($aData['nickname']) ){
            wp_send_json_error(array(
                'nickname' => esc_html__('The Nickname is required.', 'listgo')
            ));
        }

        $userID = get_current_user_id();

        $aUserData = array(
            'ID'          => $userID,
            'nickname'    => $aData['nickname'],
            'display_name'=> $aData['display_name'],
            'user_email'  => $aData['user_email'],
            'description' => $aData['description'],
            'first_name'  => $aData['first_name'],
            'last_name'   => $aData['last_name'],
            'user_url'    => $aData['user_url']
        );

        if ( isset($aData['facebookchat']) && !empty($aData['facebookchat']) ){
            $aFbChat['fanpageID'] = sanitize_text_field($aData['facebookchat']['fanpageID']);
            $aFbChat['apiID'] = sanitize_text_field($aData['facebookchat']['apiID']);
            update_user_meta($userID, 'wiloke_fb_chat', $aFbChat);
        }

        if ( isset($aData['new_password']) && !empty($aData['new_password']) ){
            if ( empty($aData['current_password']) ){
                wp_send_json_error(array(
                    'current_password' => esc_html__('The current password is wrong.', 'listgo')
                ));
            }

            if ( empty($aData['confirm_new_password']) || ($aData['confirm_new_password'] !== $aData['new_password']) ){
                wp_send_json_error(array(
                    'confirm_new_password' => esc_html__('These password don\'t match.  Please try again!', 'listgo')
                ));
            }

            $oUserData = get_user_by('id', $userID);

            if ( !wp_check_password($aData['current_password'], $oUserData->user_pass, $userID) ){
                wp_send_json_error(array(
                    'current_password' => esc_html__('This password is wrong. Please try again!', 'listgo')
                ));
            }

            $aUserData['user_pass'] = $aData['new_password'];
        }
        update_user_meta($userID, 'wiloke_cover_image', $aData['wiloke_cover_image']);
        update_user_meta($userID, 'wiloke_profile_picture', $aData['wiloke_profile_picture']);
        update_user_meta($userID, 'wiloke_user_socials', $aData['wiloke_user_socials']);
        update_user_meta($userID, 'wiloke_color_overlay', $aData['wiloke_color_overlay']);
        update_user_meta($userID, 'wiloke_address', $aData['address']);
        update_user_meta($userID, 'wiloke_phone', $aData['wiloke_phone']);

        wp_update_user($aUserData);
        $aUserData = get_userdata($userID);
        $aUserData = get_object_vars($aUserData);

        WilokeUser::putUserToRedis($aUserData);

        wp_send_json_success(
            array(
                'message' => esc_html__('Congrats, Your information have been updated!', 'listgo')
            )
        );
    }

    public function fetchMyBillingHistory(){
        if ( !isset($_POST['security']) || !check_ajax_referer('wiloke-nonce', 'security', false) ){
            wp_send_json_error();
        }

        $postsPerPage = isset($_POST['postsperpage']) && absint($_POST['postsperpage']) <= 30 ?  $_POST['postsperpage'] : 10;
        $paged = isset($_POST['paged']) && !empty($_POST['paged']) ? absint($_POST['paged']) : 1;
        $offset = $paged === 1 ? 0 : $paged*$postsPerPage - 1;

        global $wpdb;
        $tblHistory = $wpdb->prefix . AlterTablePaymentHistory::$tblName;

        $aResults = $wpdb->get_results(
            $wpdb->prepare(
				"SELECT $tblHistory.* FROM $tblHistory WHERE $tblHistory.user_ID=%d AND (status = 'pending' OR status = 'completed') LIMIT $postsPerPage OFFSET $offset",
				WilokePublic::$oUserInfo->ID
			)
        );

        if ( !empty($aResults) && !is_wp_error($aResults) ){
            ob_start();
            foreach ( $aResults as $oResult ){
                self::renderBillingItem($oResult);
            }
            $content = ob_get_clean();
            wp_send_json_success(
                array(
                    'total'     => count($aResults),
                    'content'   => $content
                )
            );
        }else{
            wp_send_json_success(
                array(
                    'total'=> 0,
                    'content'   => esc_html__('There are no listings', 'listgo')
                )
            );
        }
    }

    public function temporaryClosedListing(){
        if ( !self::checkAjaxSecurity($_POST) || !isset($_POST['post_ID']) || empty($_POST['post_ID']) ){
            wp_send_json_error(
                esc_html__('You do not have permission to change the post status.', 'listgo')
            );
        }

        $userID = get_current_user_id();
        $postAuthor = get_post_field('post_author', $_POST['post_ID']);
        if (absint($postAuthor) !== absint($userID) ){
            wp_send_json_error(
                esc_html__('You are not author of the post.', 'listgo')
            );
        }

        $postStatus = get_post_field('post_status', $_POST['post_ID']);
        if ( $postStatus !== 'publish' && $postStatus !== 'temporary_closed' ){
            wp_send_json_error(
                esc_html__('You do not have permission to change the post status.', 'listgo')
            );
        }

        if ( $postStatus === 'publish' ){
            $newStatus = 'temporary_closed';
        }else{
            $newStatus = 'publish';
        }

        wp_update_post(
            array(
                'post_status'   => $newStatus,
                'post_type'     => 'listing',
                'post_author'   => $userID,
                'ID'            => $_POST['post_ID']
            )
        );

        wp_send_json_success($newStatus);
    }

    public function filterAvatar($avatar, $idOrEmail, $size){
        if ( is_numeric( $idOrEmail ) ){
            $newAvatar = Wiloke::getUserAvatar($idOrEmail, null, array($size, $size));
        }elseif( is_object( $idOrEmail ) ){
            if ( ! empty($idOrEmail->user_id) ) {
                $newAvatar = Wiloke::getUserAvatar($idOrEmail->user_id, null, array($size, $size));
            }
        }else{
            $oUser = get_user_by('email', $idOrEmail);
            if ( isset($oUser->ID) ){
                $newAvatar = Wiloke::getUserAvatar($oUser->ID, null, array($size, $size));
            }
        }

        if ( isset($newAvatar) && !empty($newAvatar) ){
            return '<img src="'.esc_url($newAvatar).'" alt="'.esc_html__('Avatar', 'listgo').'" width="'.esc_attr($size).'" height="'.esc_attr($size).'" class="avatar avatar-'.esc_attr($size).' photo">';
        }
        return $avatar;
    }

    public function putLoginRegisterToFooter(){
        if ( empty(self::$oUserInfo) ){
            include get_template_directory() . '/wiloke-submission/signup-signin-popup.php';
        }
    }

    public function addNewClassToTwitterButton($btnClass){
        return 'login__twitter';
    }

    public function addNewClassToFacebookButton($btnClass){
        return 'login__facebook';
    }
    
    public function addNewClassToGoogleButton($btnclass){
        return 'login__google';
    }

    public function beforeInsertUserWithSocialMediaLogin($aUserData){
        $aUserData['role'] = 'wiloke_submission';
        return $aUserData;
    }
    
    public function updateUserMeta($userID, $oUser, $method){
        if ( $method === 'twitter' ){
            update_user_meta($userID, 'wiloke_user_socials', array('twitter'=>'https://twitter.com/'.$oUser->screen_name));
            update_user_meta($userID, 'wiloke_address', $oUser->location);
        }else if ( $method === 'facebook' ){
            update_user_meta($userID, 'wiloke_user_socials', array('facebook'=>$oUser['link']));
        }else if ( $method === 'google' ){
            update_user_meta($userID, 'wiloke_user_socials', array('google-plus'=>'https://plus.google.com/u/0/'. $oUser['sub']));
        }

        $aUserData = get_userdata($userID);
        $aUserData = get_object_vars($aUserData);
        WilokeUser::putUserToRedis($aUserData);
    }
    
    public function afterLoggedWithSocialMediaRedirectTo($redirectTo, $isFirsTimeLogin){
        if ( $isFirsTimeLogin ){
            $myAccount = WilokePublic::getPaymentField('myaccount');
            if ( empty($myAccount) ){
                return '';
            }
            return get_permalink($myAccount);
        }
        global $wp;
        return home_url(add_query_arg(array(),$wp->request));
    }

    public static function renderBadge($input){
        if ( class_exists('WilokeListGoFunctionality\Register\RegisterBadges') ){
            if ( empty($input) ){
                $aBadge = RegisterBadges::getBadgeInfo(0);
            }else{
                if ( is_numeric($input) ){
                    $aUser = self::getUserMeta($input);
                    $role = $aUser['role'];
                }else{
                    $role = $input;
                }
                $aBadge = RegisterBadges::getBadgeInfo($role);
            }
            ?>
            <span class="member-item__role" style="color: <?php echo esc_attr($aBadge['color']); ?>">
                <?php if ( !empty($aBadge['image']) ) : ?>
                <img src="<?php echo esc_url($aBadge['image']); ?>" alt="<?php esc_html_e('Badge', 'listgo'); ?>">
                <?php else: ?>
                <i class="<?php echo esc_attr($aBadge['badge']); ?>"></i>
                <?php endif; ?>
                <?php echo esc_html($aBadge['label']); ?>
            </span>
            <?php
        }
    }

	public static function renderPostDateOnBlog(){
	    global $post;
        $aPostDate = get_the_date("d/M", $post->ID);
        $aPostDate = explode('/', $aPostDate);
	    ?>
        <div class="post__date">
            <span class="day"><?php echo esc_html($aPostDate[0]); ?></span>
            <span class="month"><?php echo esc_html($aPostDate[1]); ?></span>
        </div>
        <?php
	}

	public static function renderComment(){
	    $commentCount = get_comments_number();

	    if ( $commentCount <= 1 ) {
	        echo esc_html($commentCount) .  ' ' . esc_html__('Comment', 'listgo');
	    }else{
	        echo esc_html($commentCount) .  ' ' . esc_html__('Comments', 'listgo');
	    }
	}

	public static function renderPagination($wp_query=null, $atts=array()){
        if ( empty($wp_query) )
        {
            global $wp_query;
        }

        $cssClass = isset($atts['class']) ? 'nav-links ' . $atts['class'] :  'nav-links text-left';
        $paged = isset($atts['paged']) && !empty($atts['paged']) ? $atts['paged'] : get_query_var('paged', 1);
        ?>
        <div class="<?php echo esc_attr($cssClass); ?>">
            <?php
            $big = 999999999; // need an unlikely integer
            echo paginate_links( array(
                'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'    => '?paged=%#%',
                'show_all'  => false,
                'prev_next' => true,
                'prev_text' => esc_html__('Previous', 'listgo'),
                'next_text' => esc_html__('Next', 'listgo'),
                'current'   => max( 1, $paged ),
                'total'     => $wp_query->max_num_pages
            ) );
            ?>
        </div>
        <?php
	}

	public static function getBlogSettings(){
	    global $wiloke;
	    if ( !empty($wiloke->aThemeOptions) && class_exists('ReduxFrameworkPlugin') ){
            if ( is_page_template('templates/blog-standard.php') ){
                $aArgs['layout'] = 'post__standard';
            }elseif ( is_page_template('templates/blog-grid.php') ){
                $aArgs['layout'] = 'post__grid';
            }else{
                $aArgs['layout'] = $wiloke->aThemeOptions['blog_layout'];
            }

            $aArgs['sidebar'] = $wiloke->aThemeOptions['blog_sidebar'];
            switch ($aArgs['sidebar']){
                case 'left':
                    $aArgs['main_class'] = 'col-md-9 col-md-push-3';
                    break;
                case 'right':
                    $aArgs['main_class'] = 'col-md-9';
                    break;
                default:
                    $aArgs['main_class'] = $aArgs['layout'] === 'post__standard' ? 'col-md-8 col-md-offset-2' : 'col-md-12';
                    break;
            }

            if ( $aArgs['layout'] === 'post__standard' ){
                $aArgs['img_size']      = 'large';
                $aArgs['item_class']    = 'col-xs-12';
            }else{
                $aArgs['img_size']      = 'wiloke_listgo_455x340';
                $aArgs['item_class']    = $wiloke->aThemeOptions['blog_layout_grid_on_desktops'] . ' ' . $wiloke->aThemeOptions['blog_layout_grid_on_smalls'];
            }
            $aArgs['limit_character']   = $wiloke->aThemeOptions['general_content_limit'];
        }else{
            $aArgs['main_class']        = 'col-md-8 col-md-offset-2';
            $aArgs['layout']            = 'post__standard';
            $aArgs['sidebar']           = 'no';
            $aArgs['item_class']        = 'col-xs-12';
            $aArgs['img_size']          = 'large';
            $aArgs['limit_character']   = 115;
        }

	    return $aArgs;
    }

    public static function getMaxFileSize(){
        return ini_get('upload_max_filesize');
    }

    public function removeVideoOutOfContent($cached_html){
        if ( FrontendManageSingleListing::packageAllow('toggle_allow_embed_video') ){
            return $cached_html;
        }

        return '';
    }

    /**
	 * Add Location By
	 * @since 1.0s
	 */
	public static function addLocationBy(){
		global $wiloke;

		if ( empty($wiloke) ){
			return 'default';
		}

		if ( !isset($wiloke->aThemeOptions['add_listing_select_location_type']) ){
			return 'default';
		}

		return $wiloke->aThemeOptions['add_listing_select_location_type'];
	}

	public static function inListingTemplates($aTemplate){
	    global $wiloke, $post;
        if ( !is_array($aTemplate) ){
            $aTemplate = array($aTemplate);
        }

        if ( is_page_template('default') ){
            $currentTemplate = $wiloke->aThemeOptions['listing_layout'];
            $currentTemplate = strpos($currentTemplate, '.php') === false ? $currentTemplate . '.php' : $currentTemplate;
        }else{
            $currentTemplate = get_page_template_slug($post->ID);
        }

        return in_array($currentTemplate, $aTemplate);
	}

	public function addGooglereCAPTCHA(){
	    global $wiloke;
	    if ( !isset($wiloke->aThemeOptions['toggle_google_recaptcha']) || ($wiloke->aThemeOptions['toggle_google_recaptcha'] == 'disable') || is_user_logged_in() ){
            return '';
	    }
	    ?>
        <div class="form-item">
            <div class="g-recaptcha" data-sitekey="<?php echo esc_attr(trim($wiloke->aThemeOptions['google_recaptcha_site_key'])); ?>"></div>
        </div>
        <?php
	}

	public function addGooglereCAPTCHAToReviewForm(){
	    global $wiloke;
	    if ( !isset($wiloke->aThemeOptions['toggle_google_recaptcha']) || ($wiloke->aThemeOptions['toggle_google_recaptcha'] == 'disable') || is_user_logged_in() ){
            return '';
	    }
	    ?>
	    <div class="col-sm-12" style="margin-bottom: 30px;">
            <div class="g-recaptcha" data-sitekey="<?php echo esc_attr(trim($wiloke->aThemeOptions['google_recaptcha_site_key'])); ?>"></div>
        </div>
        <?php
	}
}