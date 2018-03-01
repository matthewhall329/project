<?php
$aTermSettings = Wiloke::getTermOption($oTerm->term_id);

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

if ( isset($aTermSettings['featured_image']) && !empty($aTermSettings['featured_image']) ){
	$aFeaturedImage = strpos($aTermSettings['featured_image'], 'http') !== false ? $aTermSettings['featured_image'] : Wiloke::generateSrcsetImg($aTermSettings['featured_image'], $size);
}else{
	$aFeaturedImage['main']['src']      = get_template_directory_uri() . '/img/featured-image.jpg';
	$aFeaturedImage['main']['width']    = 1000;
	$aFeaturedImage['main']['height']   = 500;
	$aFeaturedImage['srcset']           = '';
	$aFeaturedImage['sizes']            = '';
}
$aTermChildrenID = get_term_children($oTerm->term_id, $oTerm->taxonomy);

if ( !empty($aTermChildrenID) && !is_wp_error($aTermChildrenID) ){
	global $wpdb;
	$tblName = $wpdb->prefix . 'term_relationships';
	$totalPosts = $wpdb->get_var("SELECT COUNT(DISTINCT object_id), object_id FROM $tblName WHERE term_taxonomy_id IN (".implode(',',$aTermChildrenID).")");
}else{
    $totalPosts = $oTerm->count;
}


$prefix = $totalPosts > 1 ? $pluralPrefix : $singularPrefix;
?>
<div class="grid-item <?php echo esc_attr($layout); ?>">
	<div class="grid-item__inner">
		<div class="grid-item__content-wrapper">
			<div class="listing listing--box">
				<div class="listing__media bg-scroll" style="background-image: url(<?php echo isset($aFeaturedImage['main']['src']) ? esc_url($aFeaturedImage['main']['src']) : ''; ?>)">
					<a href="<?php echo esc_url($oTerm->link); ?>">
                        <img src="<?php echo isset($aFeaturedImage['main']['src']) ? esc_url($aFeaturedImage['main']['src']) : ''; ?>" srcset="<?php echo isset($aFeaturedImage['srcset']) ? esc_attr($aFeaturedImage['srcset']) : ''; ?>" alt="<?php echo esc_attr($oTerm->name); ?>" width="<?php echo esc_attr($aFeaturedImage['main']['width']); ?>" height="<?php echo esc_attr($aFeaturedImage['main']['height']); ?>" sizes="<?php echo isset($aFeaturedImage['sizes']) ? esc_attr($aFeaturedImage['sizes']) : ''; ?>" />
					</a>
				</div>

				<div class="listing__header">
					<div class="listing__cat">
                        <a href="<?php echo esc_url($oTerm->link); ?>"><?php echo esc_html($oTerm->name); ?></a>
					</div>
					<h3 class="listing__title"><a href="<?php echo esc_url($oTerm->link); ?>"><?php echo esc_attr($totalPosts); ?> <span><?php echo esc_attr($prefix); ?></span></a></h3>
				</div>
			</div>
		</div>
	</div>
</div>