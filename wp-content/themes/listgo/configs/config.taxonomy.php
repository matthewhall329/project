<?php
// Configure Taxonomy here
return array(
	'category' => array(
		array(
			'type'          => 'media',
			'name'          => esc_html__('Featured Image', 'listgo'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'featured_image'
		),
		array(
			'type'          => 'colorpicker',
			'name'          => esc_html__('Header Overlay', 'listgo'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'header_overlay'
		)
	),
	'post_tag' => array(
		array(
			'type'          => 'media',
			'name'          => esc_html__('Featured Image', 'listgo'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'featured_image'
		),
		array(
			'type'          => 'colorpicker',
			'name'          => esc_html__('Header Overlay', 'listgo'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'header_overlay'
		)
	),
	'listing_location' => array(
		array(
			'type'          => 'media',
			'name'          => esc_html__('Featured Image', 'listgo'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'featured_image'
		),
		array(
			'type'          => 'colorpicker',
			'name'          => esc_html__('Header Overlay', 'listgo'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'header_overlay'
		),
		array(
			'type'          => 'timezone',
			'name'          => esc_html__('Location Timezone', 'listgo'),
			'description'   => esc_html__('Enter your timezone in the input field, You can also find your timezone by using the select suggestion field. This setting is useful to detect whether a resaturent, a bar is closed or opening. Leave empty to use the default settings in Settings -> General.', 'listgo'),
			'id'            => 'timezone'
		),
		array(
			'type'          => 'text',
			'save_type'     => 'term_meta',
			'name'          => esc_html__('Place ID', 'listgo'),
			'description'   => Wiloke::wiloke_kses_simple_html( __('This feature available in ListGo 1.0.8 and higher. You can find the place here <a href="https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder" target="_blank">PlaceID finder</a>', 'listgo'), true),
			'is_add_to_column' => true,
			'id'            => 'placeid'
		),
	),
	'listing_cat' => array(
		array(
			'type'          => 'media',
			'name'          => esc_html__('Featured Image', 'listgo'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'featured_image'
		),
		array(
			'type'          => 'colorpicker',
			'name'          => esc_html__('Header Overlay', 'listgo'),
			'description'   => '',
			'return'        => 'id',
			'id'            => 'header_overlay'
		),
		array(
			'type'          => 'media',
			'name'          => esc_html__('Map Marker', 'listgo'),
			'description'   => Wiloke::wiloke_kses_simple_html(__('You can download Wiloke Map Icons here <a href="https://www.dropbox.com/s/8uvb7t407mowwqw/marker.zip?dl=0" target="_blank">Download Map Icons</a>', 'listgo'), true),
			'return'        => 'url',
			'is_add_to_column' => true,
			'id'            => 'map_marker_image'
		)
	),
	'product_cat' => array(
		array(
			'type'          => 'colorpicker',
			'name'          => esc_html__('Header Overlay', 'listgo'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'header_overlay'
		)
	),
	'product_tag' => array(
		array(
			'type'          => 'media',
			'name'          => esc_html__('Featured Image', 'listgo'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'featured_image'
		),
		array(
			'type'          => 'colorpicker',
			'name'          => esc_html__('Header Overlay', 'listgo'),
			'description'   => '',
			'return'        => 'id',
			'is_add_to_column' => true,
			'id'            => 'header_overlay'
		)
	)
);