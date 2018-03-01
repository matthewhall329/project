<?php
function wiloke_shortcode_list_of_terms_on_mega_menu($atts) {
	$atts = shortcode_atts(
		array(
			'taxonomy'          => 'listing_cat',
			'listing_cat'       => '',
			'listing_location'  => '',
			'image_size'		=> 'medium',
			'css'               => '',
			'display'        	=> 'grid',
            'xl_per_row'        => 5,
            'lg_per_row'        => 4,
            'md_per_row'        => 3,
            'sm_per_row'        => 2,
            'xs_per_row'        => 1,
            'space'             => 20,
            'nav'               => '',
            'dots'              => '',
			'extract_class'     => ''
		),
		$atts
	);

	if ( empty($atts[$atts['taxonomy']]) ){
		return '';
	}

	$aTermIDs = explode(',', $atts[$atts['taxonomy']]);

	$aTerms = Wiloke::getTermCaching($atts['taxonomy'], $aTermIDs);

	if ( empty($aTerms) || is_wp_error($aTerms) ){
	    return false;
    }

	$aDataAtts = array();
    $class     = 'wiloke-menu-' . $atts['display'];

    if ($atts['display'] === 'grid') {
        $class .= ' wiloke-menu-col-xl-' . $atts['xl_per_row'];
        $class .= ' wiloke-menu-col-lg-' . $atts['lg_per_row'];
        $class .= ' wiloke-menu-col-md-' . $atts['md_per_row'];
        $class .= ' wiloke-menu-col-sm-' . $atts['sm_per_row'];
        $class .= ' wiloke-menu-col-xs-' . $atts['xs_per_row'];
        $class .= ' wiloke-menu-space-' . $atts['space'];
    }elseif($atts['display'] === 'slider') {
        $class     .= ' owl-carousel';
	    $aDataAtts['data-col-xl'] = $atts['xl_per_row'];
	    $aDataAtts['data-col-lg'] = $atts['lg_per_row'];
	    $aDataAtts['data-col-md'] = $atts['md_per_row'];
	    $aDataAtts['data-col-sm'] = $atts['sm_per_row'];
	    $aDataAtts['data-col-xs'] = $atts['xs_per_row'];
	    $aDataAtts['data-space']  = $atts['space'];
	    $aDataAtts['data-nav']    = filter_var($atts['nav'], FILTER_VALIDATE_BOOLEAN);
	    $aDataAtts['data-dots']   = filter_var($atts['dots'], FILTER_VALIDATE_BOOLEAN);
    }

    if (!empty($atts['extract_class'])) {
        $class .= ' ' . $atts['extract_class'];
    }

    $atts['image_size'] = strpos($atts['image_size'], ',') === false ? $atts['image_size'] : explode(',', $atts['image_size']);

	ob_start(); ?>
	
	<div class="wiloke-menu-terms <?php echo esc_attr(trim($class)) ?>" <?php WilokePublic::render_attributes($aDataAtts); ?>>
		<?php foreach ( $aTerms as $oTerm ) :
            if ( !empty($oTerm) && !is_wp_error($oTerm) ) :
			$data = Wiloke::getTermOption($oTerm->term_id); ?>
			<div class="wiloke-menu-term">
				<a href="<?php echo esc_url($oTerm->link); ?>">
					<?php if ($data) : 

						$url = wp_get_attachment_image_src($data['featured_image'], $atts['image_size']); ?>
                        <div class="wiloke-menu-term__thumbnail <?php echo $atts['display'] == 'slider' ? 'owl-lazy' : 'lazy' ?>" data-src="<?php echo $url ? esc_url($url[0]) : '' ?>">
                            <?php echo wp_get_attachment_image($data['featured_image'], $atts['image_size']); ?>
                        </div>
					<?php endif ?>
					<h4 class="wiloke-menu-term__title"><?php echo esc_html($oTerm->name) ?></h4>
				</a>
			</div>
		<?php endif; endforeach; ?>
	</div>
	<?php 
	return ob_get_clean();
}