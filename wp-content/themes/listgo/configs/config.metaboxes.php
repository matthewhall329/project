<?php

global $wiloke;

return array(
	'page_settings' => array(
		'id'         => 'page_settings',
		'title'      =>  'Page Settings', 
		'pages'      => array('page'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'      => 'select',
				'id'        => 'page_color',
				'name'      => 'Page color',
				'description' => 'From ListGo 1.1.5 you can set the main color for each page. Note that you can also the theme color to whole site by clicking on Appearance -> Theme Options -> Advanced Settings.',
				'default'   => 'inherit',
				'options'   => array(
					'default' => 'Inherit From ThemeOptions',
					'green'   => 'Green',
					'lime'    => 'Lime',
					'pink'    => 'Pink',
					'yellow'  => 'Yellow',
					'custom'  => 'Custom', 
				)
			),
			array(
				'type'      => 'colorpicker',
				'id'        => 'custom_page_color',
				'name'      => 'Custom Page Color',
				'default'   => '',
				'dependency'  => array('page_color', '=', 'custom')
			),
			array(
				'type'      => 'select',
				'id'        => 'nav_style',
				'name'      => 'Menu Background',
				'default'   => 'inherit',
				'options'   => array(
					'inherit'               => 'Inherit From ThemeOptions',
					'header--transparent'   => 'Transparent',
					'header--background'    => 'Black',
					'header--custombg'      => 'Custom Color',
				)
			),
			array(
				'type'      => 'colorpicker',
				'id'        => 'custom_nav_bg',
				'name'      => 'Custom Page Color',
				'default'   => '',
				'dependency'  => array('nav_style', '=', 'header--custombg')
			),
			array(
				'type'      => 'colorpicker',
				'id'        => 'custom_nav_color',
				'name'      => 'Custom Menu Item Color',
				'default'   => '',
				'dependency'  => array('nav_style', '=', 'header--custombg')
			),
			array(
				'type' => 'select',
				'id'   => 'toggle_header_image',
				'name' => 'Toggle Header Image',
				'dependency_on_template' => array('contains', 'templates/homepage.php'),
				'description' => 'This feature is only available for Page Builder template. We recommend that you do not enable this feature if, in case, you want to build a Home Page. We recommend you do use Hero shortcode instead. But, in case, you want to build a page such as Pricing Table, Our team, this feature is recommended.',
				'default' => 'disable',
				'options' => array(
					'disable' => 'Disable',
					'enable' => 'Enbale', 
				)
			),
			array(
				'type' => 'file',
				'id'   => 'header_image',
				'name' => 'Header Image',
				'dependency_on_template' => array('not_contains', 'templates/listing-map.php'),
				'description' => 'We recommend an image of 1200px of the width', 
			),
			array(
				'type' => 'colorpicker',
				'id'   => 'header_overlay',
				'name' => 'Header Overlay',
				'dependency_on_template' => array('not_contains', 'templates/listing-map.php'),
				'description' => 'If you want to create a blur on the Header Image, this setting is useful for you.', 
			)
		)
	),
    'template_settings'             => array(
	    'id'    => 'template_settings',
	    'title' => 'Template Settings',
	    'pages' => array('page'), // Post type
	    'context'    => 'normal',
	    'dependency_on_template' => array('contains', 'templates/listing.php'),
	    'priority'   => 'low',
	    'show_names' => true, // Show field names on the left
	    'fields'     => array(
		    array(
			    'type'      => 'select',
			    'id'        => 'layout',
			    'name'      => 'Listing Layout',
			    'default'   => 'listing--list',
			    'options'   => array(
				    'listing--grid' => 'Grid',
				    'listing--grid1'=> 'Grid 2',
				    'listing-grid2'=> 'Grid 3',
				    'listing-grid3'=> 'Grid 4',
				    'listing-grid4'=> 'Grid 5',
				    'listing--list' => 'List',
				    'listing--list1'=> 'List 2',
				    'circle-thumbnail'  => 'List Circle Thumbnail (New)',
				    'creative-rectangle'=> 'List Creative Rectangle (New)', 
			    )
		    ),
		    array(
			    'type'      => 'select',
			    'id'        => 'order_by',
			    'name'      => 'Order by',
			    'default'   => 'date',
			    'options'   => array(
				    'post_date'     => 'Date',
				    'title'         => 'Title',
				    'comment_count' => 'Comment Count',
				    'author'        => 'Author',
				    'rand'          => 'Random',
				    'menu_order'    => 'Premium Listings First', 
			    )
		    ),
		    array(
			    'type'      => 'select',
			    'id'        => 'order',
			    'name'      => 'Order',
			    'default'   => 'DESC',
			    'options'   => array(
				    'DESC'     		=> 'DESC',
				    'ASC'         	=> 'ASC', 
			    )
		    ),
		    array(
			    'type'      => 'select',
			    'id'        => 'show_terms',
			    'name'      => 'Show Terms',
			    'description'   => 'Choosing kind of terms will be shown on each project item.',
			    'default'   => 'both',
			    'options'   => array(
				    'both'              => 'Listing Locations and Listing Categories',
				    'listing_location'  => 'Only Listing Locations',
				    'listing_cat'       => 'Only Listing Categories', 
			    )
		    ),
		    array(
			    'type'          => 'text',
			    'name'          => 'Image Size',
			    'id'            => 'image_size',
			    'description'   => 'Set image size for the feature image. You can use one of the following keywords: large, medium, thumbnail or specify size by following this structure w,h, for example: 1000,400, it means you want to display a featured image of 1000 width x 4000 height.',
			    'default'       => 'medium'
		    ),
		    array(
			    'type'      => 'select',
			    'id'        => 'sidebar_position',
			    'name'      => 'Sidebar Position',
			    'default'   => 'inherit',
			    'options'   => array(
				    'inherit'   => 'Inherit Theme Options',
				    'left'      => 'Left Sidebar',
				    'right'     => 'Right Sidebar',
				    'no'        => 'No Sidebar', 
			    )
		    ),
		    array(
			    'type'              => 'select',
			    'name'              => 'Display Style',
			    'id'                => 'display_style',
			    'default'           => 'all',
			    'options'           => array(
				    'all'           => 'Show all',
				    'pagination'    => 'Pagination',
				    'loadmore'      => 'Load more button', 
			    )
		    ),
		    array(
			    'type'          => 'text',
			    'name'          => 'Posts per page',
			    'id'            => 'posts_per_page',
			    'dependency'    => array('display_style', 'contains', 'pagination,loadmore'),
			    'description'   => 'Leave empty to use the general setting (General -> Settings -> Reading)',
			    'default'       => ''
		    ),
	    )
    ),
	'events_template_settings' => array(
		'id'    => 'events_template_settings',
		'title' => 'Events Template Settings',
		'pages' => array('page'), // Post type
		'context'    => 'normal',
		'dependency_on_template' => array('contains', 'templates/events-template.php'),
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'          => 'text',
				'name'          => 'Posts per page',
				'id'            => 'posts_per_page',
				'description'   => 'Leave empty to use the general setting (General -> Settings -> Reading)',
				'default'       => 10
			),
			array(
				'type'          => 'text',
				'name'          => 'Image Size',
				'id'            => 'image_size',
				'description'   => 'Set image size for the feature image. You can use one of the following keywords: large, medium, thumbnail or specify size by following this structure w,h, for example: 1000,400, it means you want to display a featured image of 1000 width x 4000 height.',
				'default'       => 'wiloke_listgo_740x370'
			),
			array(
				'type'          => 'text',
				'name'          => 'Excerpt Length',
				'id'            => 'limit_character',
				'default'       => 100
			),
		)
	),
	'map_template_settings'         => array(
		'id'    => 'map_template_settings',
		'title' => 'Map Settings',
		'pages' => array('page'), // Post type
		'context'    => 'normal',
		'dependency_on_template' => array('contains', 'templates/listing-map.php'),
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'      => 'text',
				'id'        => 'map_theme',
				'name'      => 'Map Theme',
				'description'      => 'Leave empty to use the Theme Options setting. Please refer to Wiloke Guide -> FAQs -> How to create a Map page to know more.',
				'default'   => ''
			),
			array(
				'type'      => 'text',
				'id'        => 'map_center',
				'name'      => 'Map Center',
				'description'=> 'Leave empty means select the first article',
				'default'   => ''
			),
			array(
			    'type'      => 'select',
			    'id'        => 'order_by',
			    'name'      => 'Order by',
			    'default'   => 'date',
			    'options'   => array(
				    'post_date'     => 'Date',
				    'title'         => 'Title',
				    'comment_count' => 'Comment Count',
				    'author'        => 'Author',
				    'rand'          => 'Random',
				    'menu_order'    => 'Premium Listings First', 
			    )
		    ),
		    array(
			    'type'      => 'select',
			    'id'        => 'order',
			    'name'      => 'Order',
			    'default'   => 'DESC',
			    'options'   => array(
				    'DESC'     		=> 'DESC',
				    'ASC'         	=> 'ASC', 
			    )
		    ),
		)
	),
    'listing_settings'  => array(
        'id'         => 'listing_settings',
        'title'      =>  'Listing Information', 
        'pages'      => array('listing'), // Post type
        'context'    => 'normal',
        'priority'   => 'low',
        'show_names' => true, // Show field names on the left
        'fields'     => array(
            array(
                'type'         => 'latlong',
                'id'           => 'map',
                'name'         => 'Map Settings',
                'description'  => 'Enter in post\'s Lat&Long. If the map does not work, please go to Appearance -> Theme Options -> General -> Google Map API key.',
                'placeholder'  => '21.027764,105.834160',
                'default'      => '',
            ),
	        array(
		        'type'         => 'text',
		        'id'           => 'contact_email',
		        'name'         => 'Contact Email',
		        'description'  => 'Leave empty to use the author email',
		        'default'      => '',
	        ),
            array(
                'type'         => 'text',
                'id'           => 'phone_number',
                'name'         => 'Phone',
                'description'  => 'Leave empty inherit Your profile settings',
                'default'      => '',
            ),
            array(
                'type'         => 'text',
                'id'           => 'website',
                'name'         => 'Website',
                'description'  => 'Leave empty inherit Your profile settings',
                'default'      => ''
	        )
        )
    ),
	'listing_open_table_settings'   => array(
		'id'         => 'listing_open_table_settings',
		'title'      =>  'Open Table Settings', 
		'description'=> 'You can find your restaurant id here <a href="https://www.otrestaurant.com/marketing/reservationwidget" target="_blank">https://www.otrestaurant.com</a>',
		'pages'      => array('listing'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'text',
				'id'           => 'restaurant_name',
				'name'         => 'Restaurant Name',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'restaurant_id',
				'name'         => 'Restaurant ID',
				'default'      => ''
			)
		)
	),
	'listing_claim'                 => array(
		'id'         => 'listing_claim',
		'title'      =>  'Listing Claim', 
		'pages'      => array('listing'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'select',
				'id'           => 'status',
				'name'         => 'Claim Status',
				'description'  => 'Note that you need to enable Claim System in Appearance -> Theme Options -> Listing Settings first.',
				'default'      => 'not_claimed',
				'options'      => array(
					'not_claimed' => 'Not Claimed',
					'claimed'     => 'Claimed',
				)
			)
		)
	),
	'listing_price'  => array(
		'id'         => 'listing_price',
		'title'      =>  'Price Segment', 
		'pages'      => array('listing'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'select',
				'id'           => 'price_segment',
				'name'         => 'Range Segment',
				'default'      => '',
				'options'      => array(
					''              => 'Thanks, but no thanks',
					'cheap'         => '$ - Cheap',
					'moderate'      => '$$ - Moderate',
					'expensive'     => '$$$ - Expensive',
					'ultra_high'    => '$$$$ - Ultra high', 
				)
			),
			array(
				'type'         => 'text_small',
				'id'           => 'price_from',
				'name'         => 'Price From',
				'default'      => '',
				'options'      => ''
			),
			array(
				'type'         => 'text_small',
				'id'           => 'price_to',
				'name'         => 'Price To',
				'default'      => '',
				'options'      => ''
			)
		)
	),
	'listing_coupon'  => array(
		'id'         => 'listing_coupon',
		'title'      =>  'Coupon', 
		'pages'      => array('listing'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'text',
				'id'           => 'description',
				'name'         => 'Description',
				'default'      => '',
				'placeholder'  => 'Save 10%',
				'options'      => ''
			),
			array(
				'type'         => 'text',
				'id'           => 'coupon_code',
				'name'         => 'Coupon Code',
				'default'      => '',
				'options'      => ''
			),
			array(
				'type'         => 'text',
				'id'           => 'referral_link',
				'name'         => 'Referral link',
				'default'      => '',
				'options'      => ''
			)
		)
	),
	'listing_social_media' => array(
		'id'         => 'listing_social_media',
		'title'      =>  'Social Media', 
		'pages'      => array('listing'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'text',
				'id'           => 'facebook',
				'name'         => 'Facebook',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'twitter',
				'name'         => 'Twitter',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'google-plus',
				'name'         => 'Google+',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'linkedin',
				'name'         => 'Linkedin',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'tumblr',
				'name'         => 'Tumblr',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'instagram',
				'name'         => 'Instagram',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'flickr',
				'name'         => 'Flickr',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'pinterest',
				'name'         => 'Pinterest',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'medium',
				'name'         => 'Medium',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'tripadvisor',
				'name'         => 'Tripadvisor',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'wikipedia',
				'name'         => 'Wikipedia',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'vimeo',
				'name'         => 'Vimeo',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'youtube',
				'name'         => 'Youtube',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'whatsapp',
				'name'         => 'Whatsapp',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'vk',
				'name'         => 'VK',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'odnoklassniki',
				'name'         => 'Odnoklassniki',
				'default'      => '',
			)
		)
	),
	'listing_facebook_fanpage' => array(
		'id'         => 'listing_facebook_fanpage',
		'title'      =>  'Facebook Fanpage Settings',
		'pages'      => array('listing'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'text',
				'id'           => 'iframe_src',
				'name'         => 'Iframe Source',
				'desc'         => '<a href="https://blog.wiloke.com/setup-facebook-fanpage-source-listgo/" target="_blank">How to get my Facebook Fanpage Source?</a>',
				'default'      => '',
			),
		)
	),
    'listing_timekit' => array(
	    'id'         => 'listing_timekit',
	    'title'      =>  'Timekit Settings', 
	    'pages'      => array('listing'), // Post type
	    'context'    => 'normal',
	    'priority'   => 'low',
	    'show_names' => true, // Show field names on the left
	    'fields'     => array(
		    array(
			    'type'         => 'text',
			    'id'           => 'app',
			    'name'         => 'App',
			    'desc'         => 'Your Timekit registered app slug',
			    'default'      => '',
		    ),
		    array(
			    'type'         => 'text',
			    'id'           => 'email',
			    'name'         => 'Email',
			    'desc'         => 'Your Timekit user\'s email (used for auth)',
			    'default'      => '',
		    ),
		    array(
			    'type'         => 'text',
			    'id'           => 'apiToken',
			    'name'         => 'Api Token',
			    'desc'         => 'Your Timekit user\'s apiToken (as generated through the wizard)',
			    'default'      => '',
		    ),
		    array(
			    'type'         => 'text',
			    'id'           => 'calendar',
			    'name'         => 'Calendar',
			    'desc'         => 'Your Timekit calendar ID that bookings should end up in',
			    'default'      => '',
		    ),
		    array(
			    'type'         => 'text',
			    'id'           => 'name',
			    'name'         => 'Name',
			    'desc'         => 'Display name to show in the header and timezone helper',
			    'default'      => '',
		    ),
		    array(
			    'type'         => 'file',
			    'id'           => 'avatar',
			    'name'         => 'Avatar',
			    'desc'         => 'Provide an image URL for a circular image avatar',
			    'default'      => '',
		    )
	    )
    ),
    'gallery_settings'              => array(
        'id'         => 'gallery_settings',
        'title'      =>  'Gallery', 
        'pages'      => array('listing'), // Post type
        'context'    => 'side',
        'priority'   => 'low',
        'show_names' => true, // Show field names on the left
        'fields'     => array(
            array(
                'type'         => 'file_list',
                'id'           => 'gallery',
                'query_args'   => array('type' => 'image' ),
                'preview_size' => array(50, 50),
                'name'         => 'Upload Images',
                'default'      => '',
            )
        )
    ),
	'listing_other_settings'        => array(
		'id'         => 'listing_other_settings',
		'title'      =>  'Other Settings', 
		'pages'      => array('listing'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'colorpicker',
				'id'           => 'header_overlay',
				'name'         => 'Overlay Color',
				'description'  => 'Leave empty to use the default setting in the ThemeOptions',
				'placeholder'  => '',
				'default'      => ''
			)
		)
	),
	'testimonial_settings'          => array(
		'id'         => 'testimonial_settings',
		'title'      =>  'Testimonial Settings', 
		'pages'      => array('testimonial'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'file',
				'id'           => 'profile_picture',
				'name'         => 'Profile picture',
				'description'  => 'We recommend an image of 128x128px',
				'default'      => '',
			),
			array(
				'type'         => 'text',
				'id'           => 'position',
				'name'         => 'Job Position',
				'default'      => '',
			),
		)
	),
	'event_settings'   => array(
		'id'         => 'event_settings',
		'title'      =>  'Settings', 
		'pages'      => array('event'), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'text',
				'id'           => 'place_detail',
				'name'         => 'Place Detail',
				'description'  => 'Enter the address where organize this event.',
				'default'      => '',
				'date_format'  => ''
			),
			array(
				'type'         => 'text',
				'id'           => 'latitude',
				'name'         => 'Latitude',
				'default'      => '',
				'required'     => true,
				'date_format'  => ''
			),
			array(
				'type'         => 'text',
				'id'           => 'longitude',
				'name'         => 'Longitude',
				'default'      => '',
				'required'     => true,
				'date_format'  => ''
			),
			array(
				'type'         => 'text_date',
				'id'           => 'start_on',
				'name'         => 'Event start on',
				'description'  => 'When does event start? Note that it will be show like July 16, 2017 on the front-end.',
				'default'      => '',
				'date_format'  => 'm/dd/yy'
			),
			array(
				'type'         => 'text',
				'id'           => 'start_at',
				'name'         => 'Event start at',
				'description'  => 'Showing exactly the time event is opening',
				'default'      => '8:30 AM',
			),
			array(
				'type'         => 'text',
				'id'           => 'end_at',
				'name'         => 'Event end at',
				'description'  => 'Showing exactly the time event is closed',
				'default'      => '8:30 PM',
			),
			array(
				'type'         => 'text_date',
				'id'           => 'end_on',
				'name'         => 'Event end on',
				'default'      => '',
				'date_format'  => 'm/dd/yy'
			),
			array(
				'type'         => 'post_type_select',
				'post_types'   => array('listing'),
				'id'           => 'belongs_to',
				'name'         => 'Event belongs to',
				'description'  => 'Set the listing, which this event belongs to',
				'default'      => ''
			),
			array(
				'type'         => 'text',
				'id'           => 'event_link',
				'name'         => 'Link to event',
				'description'  => 'For example: An agency pay for you to promote their event. On their website has an article about this event, you can put that link here, when your user click on this event, it redirects to the agency page.',
				'default'      => '',
			)
		)
	),
	'pricing_settings' => array(
		'id'        => 'pricing_settings',
		'title'     => 'Settings',
		'pages'     => array('pricing'),
		'context'   => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type' => 'desc',
				'id'   => 'description',
				'name' => 'Important',
				'desc' => 'If you are using 2Checkout Payment Gateway, Please read this tutorial <a href="https://blog.wiloke.com/setup-plan-for-2checkout-payment-gateway/" target="_blank">SETUP PLAN FOR 2CHECKOUT PAYMENT GATEWAY</a>'
			),
			array(
				'type' => 'desc',
				'id'   => 'description',
				'name' => 'Stripe gateway',
				'desc' => '<a href="https://blog.wiloke.com/learn-configure-stripe-gateway-listgo/" target="_blank">LEARN HOW TO CONFIGURE STRIPE GATEWAY IN LISTGO</a>'
			),
			array(
				'type' => 'desc',
				'id'   => 'description',
				'name' => 'Direct Bank Transfer gateway',
				'desc' => '<a href="https://blog.wiloke.com/learn-configure-direct-bank-transfer/" target="_blank">LEARN HOW TO CONFIGURE DIRECT BANK TRANSFER</a>'
			),
			array(
				'type' => 'text',
				'id'   => 'description',
				'name' => 'Description',
				'default' => 'Standard listing submission, active for 30 days.'
			),
			array(
				'type' => 'text_small',
				'id'   => 'duration',
				'name' => 'Duration (day)',
				'description' => 'Set duration of an article start from it is published. If this field is empty, it means unlimited time. <strong class="help red">This feature is only available for Non-recurring Payment</strong> or This is a <strong class="red help">Free</strong> package plan. Regarding <strong class="red help">Recurring Payment</strong>, the listing will be hidden after your user could not pay for their plan.',
			),
			array(
				'type' => 'text_small',
				'id'   => 'trial_price',
				'name' => 'Trial Price (number)', 
			),
			array(
				'type' => 'text_small',
				'id'   => 'price',
				'name' => 'Regular Price (number)',
				'description' => 'Leave empty to set this pricing table as free. Notice: Only enter number.', 
			),
			array(
				'type' => 'text_small',
				'id'   => 'regular_period',
				'name' => 'Regular Period (day)',
				'description' => 'Set a Billing Frequency. For example: If you set the value is 30, it means auto payment will be proceed each month. <strong class="help red">0 means Unlimited Availability package. Otherwise, Regular must be less than or equal to one year</strong>. <strong class="help red">This feature is available for Recurring Payment method</strong>'
			),
			array(
				'type' => 'text_small',
				'id'   => 'number_of_posts',
				'name' => 'Number of Listings',
				'description' => 'Set maximum listings will be created by this pricing. If you want to setup this plan as an unlimited add listing plan, please set Number of Listings to 10000000 or higher.', 
			),
			array(
				'type' => 'post_type_select',
				'post_types'=> array('event-pricing'),
				'default' => '',
				'id'   => 'event_pricing_package',
				'name' => 'Event Package',
				'description' => 'Once a client purchased this plan, they will be offered an event plan (on the first time billing only). The event plan allows creating events for their listing. You can create the event plan by clicking on Event Pricings -> Add New.', 
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_fb_customchat',
				'name'          => 'Toggle Facebook Chat',
				'description'   => 'Show/Hide Facebook on the listings that belongs to this plan.',
				'default'       => 'disable',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable',
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_contact_form',
				'name'          => 'Toggle Contact Form',
				'description'   => 'Show/Hide Contact Form on the listings that belongs to this plan.',
				'default'       => 'enable',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable',
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_facebook_fanpage',
				'name'          => 'Toggle Facebook Fanpage',
				'description'   => 'Show/Hide Facebook Fanpage on the listings that belongs to this plan.',
				'default'       => 'enable',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable',
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_timekit',
				'name'          => 'Toggle Timekit',
				'description'   => 'Show/Hide Timekit on the listings that belongs to this plan.',
				'default'       => 'enable',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable',
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'publish_on_map',
				'name'          => 'Publish on map',
				'description'   => 'When your customers purchase this pricing, their listings will be published/unpublish on Map',
				'default'       => 'enable',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable',
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_add_feature_listing',
				'name'          => 'Featured Listing',
				'description'   => 'When your customers purchase this pricing, A ribbon will be added to their listings.',
				'default'       => 'disable',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable',
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_allow_add_gallery',
				'name'          => 'Toggle add gallery on the sidebar',
				'description'   => 'When Allow / Not Allow ability to add a gallery onto the Listing sidebar',
				'default'       => 'enable',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable',
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_allow_embed_video',
				'name'          => 'Toggle Embed Video into Listing Content',
				'description'   => 'Allow / Not Allow ability to embed Video into Listing',
				'default'       => 'enable',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable',
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_listing_template',
				'name'          => 'Toggle Listing Template',
				'description'   => 'Allow using all listing template or just use the default template.',
				'default'       => 'enable',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable',
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_listing_shortcode',
				'name'          => 'Toggle Listing Shortcodes',
				'description'   => 'Allow using Accordion, Listing Features, Menu Prices shortcodes or not.',
				'default'       => 'enable',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable',
				)
			),
			array(
				'type' 			=> 'post_type_select',
				'post_types'	=> array('acf', 'acf-field-group'),
				'default' 		=> '',
				'id'            => 'afc_custom_field',
				'name'          => 'Custom Fields',
				'description'   => 'Select a Group Custom Field that will be used on this package. Note that this group must be set to "Post Type is equal to Listing" rule', 
			),
			array(
				'type' 			=> 'select',
				'default' 		=> 'disable',
				'id'            => 'toggle_open_table',
				'name'          => 'Toggle Open Table',
				'description'   => 'Allow your users to embed their Restaurant on www.opentable.com to their listing.',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable',
				)
			),
			array(
				'type' => 'select',
				'id'   => 'highlight',
				'name' => 'High Light',
				'description' => 'Set this pricing as highlight feature',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable',
				),
				'default' => 'disable'
			),
			array(
				'type' 			=> 'select',
				'default' 		=> 'enable',
				'id'            => 'toggle_coupon',
				'name'          => 'Toggle AddCoupon Feature',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable',
				)
			),
			array(
				'type'          => 'select',
				'id'            => 'toggle_schema_markup',
				'name'          => 'Toggle Schema Markup',
				'description'   => '<a href="https://blog.kissmetrics.com/get-started-using-schema/" target="_blank">What is Schema Markup?</a>',
				'default'       => 'enable',
				'options'       => array(
					'enable'    => 'Enable',
					'disable'   => 'Disable',
				)
			),
			array(
				'type'         => 'multicheck',
				'id'           => 'except_social_networks',
				'name'         => 'Except Social Networks',
				'description'  => 'Specifying what social networks are not available for this plan',
				'default'      => '',
				'options'      => array(
					'facebook'   => 'Facebook',
					'twitter'    => 'Twitter',
					'google-plus'=> 'Google+',
					'linkedin'   => 'linkedin',
					'tumblr'     => 'Tumbrl',
					'instagram'  => 'Instagram',
					'flickr'     => 'Flickr',
					'pinterest'  => 'pinterest',
					'medium'     => 'Medium',
					'tripadvisor'=> 'Tripadvisor',
					'wikipedia'  => 'Wikipedia',
					'vimeo'      => 'Vimeo',
					'youtube'    => 'Youtube',
					'whatsapp'   => 'Whatsapp'
				)
			)
		)
	),
	'event_pricing_settings'        => array(
		'id'        => 'event_pricing_settings',
		'title'     => 'Settings',
		'pages'     => array('event-pricing'),
		'context'   => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type' => 'desc',
				'id'   => 'event_pricing_description',
				'name' => 'How Add Event Listing works?',
				'desc' => 'Please read this article to know more <a href="https://blog.wiloke.com/event-plan-works/" target="_blank">HOW “EVENT PLAN” WORKS?</a>'
			),
			array(
				'type' => 'text_small',
				'id'   => 'price',
				'name' => 'Price',
				'description' => 'This price must be bigger than 0. You can create a relationship between Event Plan and Pricing Plan by going to Pricing -> For: Package A -> Assigning this plan to the Package A.', 
			),
			array(
				'type' => 'text_small',
				'id'   => 'number_of_posts',
				'name' => 'Number of Events',
				'description' => 'Set maximum listings will be created by this pricing. The number must be bigger than 0.', 
			)
		)
	),
	'review_settings'               => array(
		'id'        => 'review_settings',
		'title'     => 'Review Settings',
		'pages'     => array('review'),
		'context'   => 'normal',
		'priority'   => 'low',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'type'         => 'file_list',
				'id'           => 'gallery',
				'query_args'   => array('type' => 'image' ),
				'preview_size' => array(50, 50),
				'name'         => 'Gallery',
				'default'      => '',
			)
		)
	),
	'half_map_settings'             => array(
		'id'                        => 'half_map_settings',
		'title'                     =>  'Half Map Settings', 
		'dependency_on_template'    => array('contains', 'templates/half-map.php'),
		'pages'                     => array('page'), // Post type
		'context'                   => 'normal',
		'priority'                  => 'low',
		'show_names'                => true, // Show field names on the left
		'fields'                    => array(
			array(
				'type'     => 'text',
				'id'       => 'maxZoom',
				'name'     => 'Map Maximum Zoom (Desktop)',
				'default'  => 4
			),
			array(
				'type'     => 'text',
				'id'       => 'minZoom',
				'name'     => 'Map Minimum Zoom (Desktop)',
				'desc'     => 'A negative number is allowable',
				'default'  => -1
			),
			array(
				'type'     => 'text',
				'id'       => 'centerZoom',
				'name'     => 'Map Center Zoom',
				'default'  => 10
			),
			array(
				'type'     => 'text',
				'id'       => 'center',
				'name'     => 'Map Center',
				'desc'     => 'Eg: 38.8913,-77.02. Leave empty to set the first listing as the map center.',
				'default'  => ''
			),
			array(
				'type'     => 'text',
				'id'       => 'maxClusterRadius',
				'name'     => 'Map Cluster Radius',
				'subtitle' => 'The maximum radius that a cluster will cover from the central marker',
				'default'  => 60
			),
			array(
				'type'     => 'text',
				'id'       => 'posts_per_page',
				'name'     => 'Listings per page',
				'default'  => 4
			),
			array(
				'type'          => 'text',
				'name'          => 'Image Size',
				'id'            => 'image_size',
				'description'   => 'Set image size for the feature image. You can use one of the following keywords: large, medium, thumbnail or specify size by following this structure w,h, for example: 1000,400, it means you want to display a featured image of 1000 width x 4000 height.',
				'default'       => 'medium'
			),
			array(
				'type'      => 'select',
				'id'        => 'show_terms',
				'name'      => 'Show Terms',
				'description'   => 'Choosing kind of terms will be shown on each project item.',
				'default'   => 'both',
				'options'   => array(
					'both'              => 'Listing Locations and Listing Categories',
					'listing_location'  => 'Only Listing Locations',
					'listing_cat'       => 'Only Listing Categories'
				)
			)
		)
	)
);