<?php
if($portfolioMode === 'creative'){
    $layout = $aLayouts[$i];
    
    if ( $layout === 'cube' ){
        $size = 'wiloke_listgo_370x370';
    }elseif ($layout==='wide'){
        $size = 'wiloke_listgo_740x370';
    }else{
        $size = 'wiloke_listgo_740x740';
    }
}

if( !has_post_thumbnail($query->post->ID) || (!$aFeaturedImage = Wiloke::generateSrcsetImg(get_post_thumbnail_id($query->post->ID), $size)) ){
   $aFeaturedImage = $aDefaultFeatureImg;
}

?>
<div class="grid-item <?php echo esc_attr($layout); ?>">
    <div class="grid-item__inner">
        <div class="grid-item__content-wrapper">
            <div class="listing listing--box">

                <div class="listing__media bg-scroll lazy" data-src="<?php echo esc_url($aFeaturedImage['main']['src']); ?>">
                    <a href="<?php echo esc_url(get_permalink($query->post->ID)); ?>">
                        <img src="<?php echo esc_url($aFeaturedImage['main']['src']); ?>" alt="<?php echo esc_attr(get_the_title($query->post->ID)); ?>"  width="<?php echo esc_attr($aFeaturedImage['main']['width']); ?>" height="<?php echo esc_attr($aFeaturedImage['main']['height']); ?>" />
                    </a>
                    <?php WilokePublic::renderFeaturedIcon($query->post); ?>
                    <?php WilokePublic::renderListingStatus($query->post); ?>
                </div>

                <div class="listing__header">
                    <div class="listing__cat">
                        <?php
                        if ( $atts['show_terms'] === 'listing_location' ){
	                        WilokePublic::renderTaxonomy($query->post->ID, 'listing_location');
                        }elseif( $atts['show_terms'] === 'listing_cat' ){
	                        WilokePublic::renderTaxonomy($query->post->ID, 'listing_cat');
                        }else{
	                        WilokePublic::renderTaxonomy($query->post->ID, array('listing_cat','listing_location'));
                        }
                        ?>
                    </div>
                    <h3 class="listing__title"><a href="<?php echo esc_url(get_permalink($query->post->ID)); ?>"><?php echo esc_html($query->post->post_title); ?></a></h3>
                </div>

                <div class="listing__body">
                    <?php WilokePublic::renderAuthor($query->post); ?>
                    <?php WilokePublic::renderContent($query->post); ?>
                    <?php WilokePublic::renderAverageRating($query->post); ?>

                    <div class="item__actions">
                        <div class="tb">
	                        <?php WilokePublic::renderFavorite($query->post); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
