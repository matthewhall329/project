<?php
Wiloke::sessionStart();

if( class_exists('acf') && ( is_page_template('wiloke-submission/addlisting.php') || is_page_template('wiloke-submission/addlisting-old.php') ) ){
    acf_form_head();
}

use WilokeListGoFunctionality\Frontend\Notification as WilokeNotification;
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WilokeThemes
 * @subpackage Listgo
 * @since  1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php do_action('wiloke/listgo/after_body'); ?>
    <div id="fb-root"></div>
    <?php
    global $wiloke;
    $menuLocation = $wiloke->aConfigs['frontend']['register_nav_menu']['menu'][0]['key'];
    $aPageSettings = array();
    $menuClass = 'header--background';

    if ( is_page() ){
	    $aPageSettings = Wiloke::getPostMetaCaching($post->ID, 'page_settings');
    }else if ( is_singular('listing') ){
        if ( WilokePublic::inListingTemplates(array('templates/single-listing-creative.php', 'templates/single-listing-howard-roark.php')) ){
	        $menuClass = 'header--transparent';
        }
    }

    $customNavBg = '';
    $customNavColor = '';

    if ( !empty($aPageSettings) && ($aPageSettings['nav_style'] !== 'inherit') ){
	    $menuClass = $aPageSettings['nav_style'];
	    if ( strpos($menuClass, 'header--custombg') !== false ){
		    $customNavBg = $aPageSettings['custom_nav_bg'];
		    $customNavColor = $aPageSettings['custom_nav_color'];
		    $menuClass .= ' header-style-header--background';
	    }
    }else{
	    $menuClass = $wiloke->aThemeOptions['header_nav_style'];

	    if ( strpos($menuClass, 'header--custombg') !== false ){
		    $customNavBg = $wiloke->aThemeOptions['header_custom_nav_bg']['rgba'];
		    $customNavColor = $wiloke->aThemeOptions['header_custom_nav_color']['rgba'];
		    $menuClass .= ' header-style-header--background';
	    }else if ( (!is_front_page() && is_home()) ){
		    $menuClass = 'header--background header-style-header--background';
        }
    }

    ?>
    
    <?php if ( has_nav_menu($menuLocation) && !class_exists('WilokeMenu') ) : ?>
        <nav id="header-mobile" class="header-mobile">
            <?php wp_nav_menu($wiloke->aConfigs['frontend']['register_nav_menu']['config'][$menuLocation]); ?>
        </nav>
    <?php endif; ?>

    <div id="wrap-page" class="header-style-<?php echo esc_attr($menuClass); ?>">
        <header id="header" class="header <?php echo esc_attr($menuClass); ?> <?php echo wp_is_mobile() ? 'header-responsive' : '' ?>" data-break-mobile="<?php echo !empty($wiloke->aThemeOptions) ? esc_attr($wiloke->aThemeOptions['general_menu_mobile_at']) : 1400; ?>" data-navcolor="<?php echo esc_attr($customNavColor); ?>" style="background-color: <?php echo esc_attr($customNavBg); ?>">
            <div class="header__inner">
                <div class="wo__container">
                    <div class="header__content <?php echo wp_is_mobile() ? 'wiloke-menu-responsive' : '' ?>">
                        <div class="header__logo">
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                               <?php
                               if ( !empty($wiloke->aThemeOptions['general_logo']['url']) ){
                                   ?>
                                   <img src="<?php echo esc_url($wiloke->aThemeOptions['general_logo']['url']); ?>" alt="<?php echo esc_attr(get_option('blogname')); ?>" />
                                   <?php
                               }else{
                                   echo '<h1>'.esc_html(get_option('blogname')).'</h1>';
                               }
                               ?>
                            </a>
                        </div>

                        <div class="header__actions">
                            <?php
                            if ( class_exists('\WilokeListgoFunctionality\Controllers\AddListingBtnUrlController') ){
                                \WilokeListgoFunctionality\Controllers\AddListingBtnUrlController::quickAddListingBtn();
                            }
                            
                            if ( class_exists('WilokeListGoFunctionality\Frontend\Notification') ){
	                            WilokeNotification::renderNotification();
                            }
                            WilokePublic::quickUserInformation();
                            \WilokeListgoFunctionality\Frontend\FrontendRegisterLogin::quickLoginRegisters();
                            ?>
                            <?php if ( !class_exists('WilokeMenu') ) : ?>
                                <div class="header__toggle">
                                    <div class="tb">
                                        <div class="tb__cell">
                                            <span class="header__toggle-icon icon_ul"></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ( has_nav_menu($menuLocation) ) : ?>
                            <?php wp_nav_menu($wiloke->aConfigs['frontend']['register_nav_menu']['config'][$menuLocation]); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>
        <section id="main">
            <?php do_action('wiloke/listgo/header/after_main_open'); ?>

