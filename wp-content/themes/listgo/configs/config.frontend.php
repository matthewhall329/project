<?php

return array(
    'scripts' => array(
        'bootstrap'             => array('css', 'bootstrap.min.'),
        'underscore'            => array('js', 'underscore', 'is_wp_store'=>true),
        'autocomplete'          => array('js', 'jquery-ui-autocomplete', 'is_wp_store'=>true),
        'imagesloaded'          => array('js', 'imagesloaded', 'is_wp_store'=>true),
        'googlemap'             => array('js', 'is_url'=>true, 'is_google_map'=>true),
        'leaflet'               => array('both', 'leaflet.min.'),
        'select2'               => array('both', 'select2.min.'),
        'jquery-ui-min'         => array('both', 'jquery-ui.min.'),
        'touch-punch'           => array('js', 'jquery.ui.touch-punch.min.', 'conditional'=>'wp_is_mobile'),
        'mapbox'                => array('both', 'mapbox.min.'),
        'theia-sticky-sidebar'  => array('js', 'theia-sticky-sidebar.min.'),
        'textfill'              => array('js', 'jquery.textfill.min.'),
        'leaflet-markercluster' => array('both', 'leaflet.markercluster.min.'),
        'font-awesome'          => array('css', 'font-awesome.min.'),
        'font-elegant'          => array('css', 'font-elegant.min.'),
        'jquery-isotope'        => array('js', 'isotope.pkgd.min.'),
        'magnific-popup'        => array('both', 'jquery.magnific-popup.min.'),
        'owlcarousel'           => array('both', 'jquery.owl.carousel.min.'),
        'modernizr'             => array('js', 'modernizr.min.'),
        'jquerylazy'            => array('js', 'jquery.lazy.min.'),
        'perfect-scrollbar'     => array('both', 'perfect-scrollbar.min.'),
        'YTPlayer'              => array('js', 'YTPlayer.min.'),
        'hoverdir'              => array('js', 'jquery.hoverdir.min.'),
        'gridrotator'           => array('both', 'jquery.gridrotator.min.'),
        'listgo-googlefont'     => array('Poppins:400,600|Questrial', 'is_googlefont'=>true),
        'listgo-main-style'     => array('css', 'style.', 'default'=>true),
        'listgo-halfmap'        => array('js', 'halfmap.', 'default'=>true, 'is_register_only'=>true, 'ignore_compress'=>true, 'is_ignore_minify'=>true),
        'listgo-script'         => array('js', 'scripts.', 'default'=>true),
        'listgo-woocommerce'    => array('js', 'woocommerce.', 'default'=>true, 'conditional'=>'listgoIsWooCommerce'),
        'listgo-signupsignin'   => array('js', 'signupsignin.', 'default'=>true, 'conditional'=>'listgoIsUserNotLoggedIn'),
//        'listgo-script'        => array('js', 'test-scripts.', 'default'=>true)
    ),
    'register_nav_menu'  => array(
        'menu'  => array(
            array(
                'key'   => 'listgo_menu',
                'name'  => esc_html__('ListGo Menu', 'listgo'),
            )
        ),
        'config'=> array(
            'listgo_menu'=> array(
                'theme_location'  => 'listgo_menu',
                'name'            => esc_html__('ListGo Menu', 'listgo'),
                'menu'            => '',
                'container'       => '',
                'container_class' => 'wil-navigation',
                'container_id'    => 'wiloke-listgo-menu',
                'menu_class'      => 'wil-menu-list menu',
                'menu_id'         => '',
                'echo'            => true,
                'before'          => '',
                'after'           => '',
                'link_before'     => '',
                'link_after'      => '',
                'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                'depth'           => 0,
                'walker'          => ''
            )
        )
    ),
    'portfolio_item_sizes' => array(
        'fg' => 'large',
        'sg' => 'large',
        'tg' => array(
            'cube'          => 'wiloke_460_460',
            'large'         => 'wiloke_925_925',
            'high'          => 'wiloke_460_925',
            'wide'          => 'wiloke_925_460',
            'extra-large'   => 'large'
        ),
        'extra_small' => 'wiloke_460_460'
    ),
	'listing' => array(
		'business_hours' => array(
			'days'=> array(
				esc_html__('Monday', 'listgo'),
				esc_html__('Tuesday', 'listgo'),
				esc_html__('Wednesday', 'listgo'),
				esc_html__('Thursday', 'listgo'),
				esc_html__('Friday', 'listgo'),
				esc_html__('Saturday', 'listgo'),
				esc_html__('Sunday', 'listgo')
			),
			'default' => array(
				'start_hour'    => 8,
				'start_minutes' => 00,
				'start_format'  => 'AM',
				'close_hour'    => 11,
				'close_minutes' => 30,
				'close_format'  => 'PM',
				'closed'        => 0
			)
		)
	),
	'price_segmentation' => array(
		'cheap'         => esc_html__('$ - Cheap', 'listgo'),
		'moderate'      => esc_html__('$$ - Moderate', 'listgo'),
		'expensive'     => esc_html__('$$$ - Expensive', 'listgo'),
		'ultra_high'    => esc_html__('$$$$ - Ultra high', 'listgo')
	),
    'anphabets' => array('a,b,c,d', 'e,f,g,h', 'j,k,l,m', 'n,o,p,q', 'r,s,t,u', 'vwxyz'),
	'color_picker' => array('#ff0000', '#fbb034', '#ffdd00', '#c1d82f', '#00a4e4', '#00a4e4', '#6a737b')
);