<?php
function wiloke_shortcode_hero($atts){
    $atts = shortcode_atts(
        array(
            'title'         =>  '',
            'sup_title'     =>  '',
            'description'   => '',
            'button_name'   =>  '',
            'button_link'   =>  '',
            'toggle_search_form'  => '',
            'search_field_title'   => '',
            'search_form_title'   => '',
            'location_field_title' => '',
            'bg_type'       => 'image',
            'toggle_mute'   => '',
            'map_page'      => '',
            'height'        => '',
            'youtube_url'   => '',
            'alignment'     => '',
            // Category
            'toggle_browsing_listing_category'=>'disable',
            'browsing_title'=> __('Or browse the highlights', 'listgo'),
            'get_listing_category_by'=>'id',
            'specify_browsing_categories'=>'',
            'number_of_browsing_categories'=>6,
            'css'           => '',
            'extract_class' => ''
        ),
        $atts
    );

    $style = '';
    $mapPageUrl = '';
    $searchStatus = 'header-page-search-disable';
    $bannerStatus = 'header-page-banner-disable';

    $wrapperClass = 'header-page-form header-page-form-' . $atts['alignment'];

    if ( $atts['toggle_browsing_listing_category'] != 'disable' ) {
        $wrapperClass .=' header-page-form-has-category';
    }

    if ( $atts['alignment'] === 'center' || $atts['alignment'] === 'center2' || $atts['alignment'] === 'center3' ) {
        $formAlignment = 'form-wide';
        $wrapperClass .= ' header-page--wide';
    } else {
        $formAlignment = 'form-high';
        $wrapperClass .= ' header-page--high';
    }
    
    if ( !empty($atts['map_page']) ){
        $mapPageUrl = get_permalink($atts['map_page']);
    }

    if ( !empty($atts['sup_title']) || !empty($atts['title']) || !empty($atts['button_name']) || !empty($atts['description']) || $atts['toggle_browsing_listing_category'] != 'disable' ) {
        $bannerStatus = 'header-page-banner-enable';
    }

    if ( $atts['toggle_search_form'] === 'enable' ) {
        $searchStatus = 'header-page-search-enable';
    }

    $wrapperClass .= ' ' . $bannerStatus . ' ' . $searchStatus;

    $wrapperClass .= ' ' . $atts['extract_class'] . ' ' . vc_shortcode_custom_css_class($atts['css'], ' ');

    if ( !empty($atts['height']) ) {
        $style = 'style="min-height: '. esc_attr($atts['height']) .'"';
    }

    $aTerms = array();

    $classCategory = 'header-page__categories';

    if ( $atts['toggle_browsing_listing_category'] == 'enable' ) {

        if ( $atts['get_listing_category_by'] == 'random' ) {

            $aRawTerms = get_terms( array(
                'taxonomy'      => 'listing_cat',
                'hide_empty'    => true,
                'order'         => 'DESC'
            ) );

            shuffle( $aRawTerms );

            $aTerms = array_slice( $aRawTerms, 0, $atts['number_of_browsing_categories'] );

        } else if ( $atts['get_listing_category_by'] == 'specify' && !empty( $atts['specify_browsing_categories'] ) ) {

            $aTerms = get_terms( array(
                'taxonomy'      => 'listing_cat',
                'hide_empty'    => true,
                'order'         => 'DESC',
                'include'       => explode(',', $atts['specify_browsing_categories'])
            ));

        } else {

            $aTerms = get_terms( array(
                'taxonomy'      => 'listing_cat',
                'hide_empty'    => true,
                'order'         => 'DESC',
                'orderby'       => $atts['get_listing_category_by'],
                'number'        => $atts['number_of_browsing_categories']
            ));

        }

        // class category
        switch ($atts['alignment']) {
            case 'center':
                $classCategory .=' header-page__categories-s4';
                break;

            case 'center2':
                $classCategory .=' header-page__categories-s3';
                break;

            case 'center3':
                $classCategory .=' header-page__categories-s5';
                break;

            case 'not_center_3':
                $classCategory .=' header-page__categories-s2';
                break;

            default:
                $classCategory .=' header-page__categories-s1';
                break;
        }
    }

    ob_start(); ?>

    <div class="<?php echo esc_attr(trim($wrapperClass)); ?>" <?php print $style; ?>>

        <div class="header-page-form__inner">
        
            <?php if ( $bannerStatus == 'header-page-banner-enable' ) : ?>
            
                <div class="header-page-form-banner">
            
                    <div class="header-page__banner">
            
                        <?php if ( !empty($atts['sup_title']) ) : ?>
                        <h5 class="banner__subtitle"><?php Wiloke::wiloke_kses_simple_html($atts['sup_title']); ?></h5>
                        <?php endif; ?>
            
                        <?php if ( !empty($atts['title']) ) : ?>
                        <h1 class="banner__title"><?php Wiloke::wiloke_kses_simple_html($atts['title']); ?></h1>
                        <?php endif; ?>
            
                        <?php if ( !empty($atts['description']) ) : ?>
                        <p class="banner__description"><?php Wiloke::wiloke_kses_simple_html($atts['description']); ?></p>
                        <?php endif; ?>
            
                        <?php if ( !empty($atts['button_name']) ) : ?>
                        <a href="<?php echo esc_url($atts['button_link']); ?>" class="listgo-btn btn-primary"><?php echo esc_html($atts['button_name']); ?> <i class="fa fa-arrow-circle-right"></i></a>
                        <?php endif; ?>
            
                    </div>
            
                    <?php

                        if ( !empty($aTerms) && !is_wp_error($aTerms) && $atts['alignment'] != 'center3' ) : ?>
        
                            <div class="<?php echo esc_attr($classCategory); ?>">

                                <div class="header-page__categories-inner">

                                    <?php if ( !empty($atts['browsing_title']) ) : ?>

                                        <span class="header-page__categories-label">
                                            <?php Wiloke::wiloke_kses_simple_html($atts['browsing_title']); ?> <img src="<?php echo esc_url(get_template_directory_uri() . '/img/icon-arrow-round.png'); ?>" alt="<?php esc_html_e('Icon', 'listgo'); ?>">
                                        </span>

                                    <?php endif; ?>

                                    <?php

                                        foreach ($aTerms as $oTerm) :
                                            $aSettings = Wiloke::getTermOption($oTerm->term_id); ?>
                                            <a href="<?php echo esc_url(get_term_link($oTerm->term_id)); ?>">
                                                <?php if ( !empty($aSettings['map_marker_image']) ) : ?>
                                                    <span class="header-page__category-icon"><img src="<?php echo esc_url($aSettings['map_marker_image']); ?>" alt="<?php echo esc_attr($oTerm->name); ?>"></span>
                                                <?php endif; ?>
                                                <span class="header-page__category-title"><?php echo esc_html($oTerm->name); ?></span>

                                            </a>
                                            <?php
                                        endforeach;
                                    ?>
                                </div>

                            </div>

                            <?php

                        endif;
                    ?>
            
                </div>
            
            <?php endif ?>
            
            <?php if ( $searchStatus === 'header-page-search-enable' ) : ?>

                <div class="header-page-form-search">

                    <div class="<?php echo esc_attr($formAlignment); ?> form-transparent is-saprated-searchform">
                        <?php WilokeSearchSystem::searchForm($mapPageUrl, false, $atts); ?>
                    </div>

                </div>

            <?php endif; ?>

            <?php

                if ( !empty($aTerms) && !is_wp_error($aTerms) && $atts['alignment'] == 'center3' ) : ?>

                    <div class="<?php echo esc_attr($classCategory); ?>">

                        <div class="header-page__categories-inner">

                            <?php if ( !empty($atts['browsing_title']) ) : ?>

                                <span class="header-page__categories-label">
                                    <?php Wiloke::wiloke_kses_simple_html($atts['browsing_title']); ?> <img src="<?php echo esc_url(get_template_directory_uri() . '/img/icon-arrow-round.png'); ?>" alt="<?php esc_html_e('Icon', 'listgo'); ?>">
                                </span>

                            <?php endif; ?>

                            <?php

                                foreach ($aTerms as $oTerm) :

                                    $aSettings = Wiloke::getTermOption($oTerm->term_id); ?>

                                    <a href="<?php echo esc_url(get_term_link($oTerm->term_id)); ?>">

                                        <?php if ( !empty($aSettings['map_marker_image']) ) : ?>

                                            <span class="header-page__category-icon"><img src="<?php echo esc_url($aSettings['map_marker_image']); ?>" alt="<?php echo esc_attr($oTerm->name); ?>"></span>

                                        <?php endif; ?>

                                        <span class="header-page__category-title"><?php echo esc_html($oTerm->name); ?></span>

                                    </a>

                                    <?php

                                endforeach;
                            ?>

                        </div>

                    </div>

                    <?php

                endif;
            ?>
            
            <?php if ( $atts['toggle_browsing_listing_category'] != 'disable' && ($atts['alignment'] == 'not_center_3' || $atts['alignment'] == 'not_center_2') ): ?>
                <div class="header-page__scrolldown" data-scroll-down="#abc"><i class="arrow_carrot-down"></i></div>
            <?php endif ?>

        </div>

    </div>

    <?php

    $content = ob_get_contents();

    ob_end_clean();

    return $content;
}