<?php
/*
 |--------------------------------------------------------------------------
 | Template name: Listing Template
 |--------------------------------------------------------------------------
 |
 |
 */

get_header();
    $isAuthor = is_author();
    global $wilokeSidebarPosition, $wiloke;
    if ( $isAuthor ){
        $authorID = get_queried_object_id();
	    WilokePublic::accountHeaderBg($authorID);
	    $totalPosts = count_user_posts($authorID, 'listing');
	    $suffix = $totalPosts > 1 ? esc_html__( 'Listings', 'listgo') : esc_html__( 'Listing', 'listgo');
	    $totalPosts = $totalPosts . ' ' . $suffix;
	    $profileUrl = WilokePublic::getPaymentField('myaccount', true);
	    $profileUrl = !empty($profileUrl) ? WilokePublic::addQueryToLink($profileUrl, 'mode=profile&user='.$authorID) : '#';
	    $colMd = ' col-md-8';
    }else{
	    WilokePublic::headerPage();
	    $colMd = ' col-md-9';
    }

    $author = '';
    if ( $isAuthor ){
        $author = get_query_var('author');
	    $listingLocations = '';
	    $listingCats = '';
	    $aTemplateSettings['sidebar_position'] = $wiloke->aThemeOptions['listing_sidebar_position'];
	    $aTemplateSettings['layout'] = 'listing--list';
	    $aTemplateSettings['display_style'] = 'loadmore';
	    $aTemplateSettings['posts_per_page'] = get_option('posts_per_page');
	    $aTemplateSettings['order_by'] = 'post_date';
	    $aTemplateSettings['image_size'] = 'medium';
	    $aTemplateSettings['get_posts_from'] = 'post_author';
	    $aTemplateSettings['show_terms'] = 'listing_location';
	    $wrapperClass =  ' is-author-page';
    }else{
	    $aTemplateSettings = Wiloke::getPostMetaCaching($post->ID, 'template_settings');
	    $listingLocations = !empty($aTemplateSettings['listing_location']) ? implode(',', $aTemplateSettings['listing_location']) : '';
	    $listingCats = !empty($aTemplateSettings['listing_cat']) ? implode(',', $aTemplateSettings['listing_cat']) : '';
	    $aTemplateSettings['sidebar_position'] = isset($aTemplateSettings['sidebar_position']) && $aTemplateSettings['sidebar_position'] !== 'inherit' ? $aTemplateSettings['sidebar_position'] : $wiloke->aThemeOptions['listing_sidebar_position'];
	    $aTemplateSettings['layout'] = !isset($aTemplateSettings['layout']) ? 'listing--list' : $aTemplateSettings['layout'];
        if ( is_tax('listing_cat') ){
	        $aTemplateSettings['get_posts_from'] = 'listing_cat';
        }else if(is_tax('listing_location')){
	        $aTemplateSettings['get_posts_from'] = 'listing_location';
        }else{
            $aTemplateSettings['get_posts_from'] = 'the_both_listing_location_and_listing_cat';
        }

        if ( $aTemplateSettings['display_style'] === 'all' ){
            $aTemplateSettings['posts_per_page'] = -1;
        }
	    $wrapperClass =  '';
    }

    switch ( $aTemplateSettings['sidebar_position'] ){
        case 'left':
            $mainClass = $colMd.' col-md-push-3';
            $wilokeSidebarPosition = 'left';
            break;
        case 'right':
            $mainClass = $colMd;
            $wilokeSidebarPosition = 'right';
            break;
        default:
            $mainClass = 'col-md-12';
            $wilokeSidebarPosition = 'no';
            break;
    }
	?>
    <div class="section<?php echo esc_attr($wrapperClass); ?>">
        <div class="container">
            <div class="row">
                <div class="<?php echo esc_attr($mainClass); ?>">

                    <?php if ( $isAuthor ) :
                        $aAuthorMeta = Wiloke::getUserMeta(get_queried_object()->ID);
                        if ( !empty($aAuthorMeta['description']) ): ?>
                            <div class="listing__author-description">

                                <?php echo wpautop($aAuthorMeta['description']); ?>

                                <?php if ( !empty(WilokePublic::$oUserInfo) && WilokePublic::$oUserInfo->ID == $aAuthorMeta['ID'] ) : ?>
                                    <a style="color: red" href="<?php echo esc_url(admin_url('user-edit.php?user_id='.WilokePublic::$oUserInfo->ID)); ?>"><?php esc_html_e('Edit profile', 'listgo'); ?></a>
                                <?php endif; ?>

                            </div>
                            
                        <?php endif ?>

                        <h2 class="listing__author-result">
                            <?php echo esc_html($totalPosts); ?>  <?php esc_html_e('By', 'listgo'); ?> <a href="<?php echo esc_url($profileUrl); ?>"><?php echo esc_html($aAuthorMeta['display_name']); ?></a>
                        </h2>

                    <?php endif; ?>

                    <div class="listgo-listlayout-on-page-template">
                        <?php if ( !$isAuthor ) : ?>
                        <div class="from-wide-listing">
                            <div class="from-wide-listing__header">
                                <span class="from-wide-listing__header-title"><?php echo esc_html__('Filter', 'listgo') ?></span>
                                <span class="from-wide-listing__header-close"><span>×</span> <?php echo esc_html__('Close', 'listgo') ?></span>
                            </div>
                            <?php WilokeSearchSystem::searchForm(null, true, array(), 'listing-template'); ?>
                            <div id="listgo-mobile-search-only" class="from-wide-listing__footer">
                                <span><?php echo esc_html__('Apply', 'listgo') ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php echo do_shortcode('[wiloke_listing_layout post_authors="'.$author.'" layout="'.$aTemplateSettings['layout'].'" order_by="'.$aTemplateSettings['order_by'].'" order="'.$aTemplateSettings['order'].'" show_terms="'.$aTemplateSettings['show_terms'].'" image_size="'.$aTemplateSettings['image_size'].'" display_style="'.$aTemplateSettings['display_style'].'" get_posts_from="'.$aTemplateSettings['get_posts_from'].'" posts_per_page="'.$aTemplateSettings['posts_per_page'].'" filter_type="none" sidebar="'. $wilokeSidebarPosition .'"]'); ?>
                    </div>
                </div>
                <?php
                if ( $wilokeSidebarPosition !== 'no' ) {
                    get_sidebar('taxlisting');
                }
                ?>
            </div>
        </div>
    </div>
<?php
get_footer();