<?php
return array(
	'shortcodes' => array(
		// Row
		array(
			'name'                      => esc_html__( 'Row', 'listgo' ),
			'is_container'              => true,
			'icon'                      => 'icon-wpb-row',
			'base'                      => 'vc_row',
			'is_use_default_shortcode'  => true,
			'show_settings_on_create'   => false,
			'category'                  => esc_html__( 'Content', 'listgo' ),
			'class'                     => 'vc_main-sortable-element',
			'description'               => esc_html__( 'Place content elements inside the row', 'listgo' ),
			'params' => array(
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Row stretch', 'listgo' ),
					'param_name'    => 'full_width',
					'value' => array(
						esc_html__( 'Default', 'listgo' ) => '',
						esc_html__( 'Stretch row', 'listgo' ) => 'stretch_row',
						esc_html__( 'Stretch row and content', 'listgo' ) => 'stretch_row_content',
						esc_html__( 'Stretch row and content (no paddings)', 'listgo' ) => 'stretch_row_content_no_spaces',
					),
					'description' => esc_html__( 'Select stretching options for row and content (Note: stretched may not work properly if parent container has "overflow: hidden" CSS property).', 'listgo' ),
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Columns gap', 'listgo' ),
					'param_name'    => 'gap',
					'value' => array(
						'0px'   => '0',
						'1px'   => '1',
						'2px'   => '2',
						'3px'   => '3',
						'4px'   => '4',
						'5px'   => '5',
						'10px'  => '10',
						'15px'  => '15',
						'20px'  => '20',
						'25px'  => '25',
						'30px'  => '30',
						'35px'  => '35',
					),
					'std'           => '0',
					'description'   => esc_html__( 'Select gap between columns in row.', 'listgo' ),
				),
				array(
					'type'          => 'checkbox',
					'heading'       => esc_html__( 'Full height row?', 'listgo' ),
					'param_name'    => 'full_height',
					'description'   => esc_html__( 'If checked row will be set to full height.', 'listgo' ),
					'value'         => array( esc_html__( 'Yes', 'listgo' ) => 'yes' ),
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Columns position', 'listgo' ),
					'param_name'    => 'columns_placement',
					'value' => array(
						esc_html__( 'Middle', 'listgo' )     => 'middle',
						esc_html__( 'Top', 'listgo' )        => 'top',
						esc_html__( 'Bottom', 'listgo' )     => 'bottom',
						esc_html__( 'Stretch', 'listgo' )    => 'stretch',
					),
					'description'   => esc_html__( 'Select columns position within row.', 'listgo' ),
					'dependency'    => array(
						'element'   => 'full_height',
						'not_empty' => true,
					),
				),
				array(
					'type'          => 'checkbox',
					'heading'       => esc_html__( 'Use video background?', 'listgo' ),
					'param_name'    => 'video_bg',
					'description'   => esc_html__( 'If checked, video will be used as row background.', 'listgo' ),
					'value'         => array( esc_html__( 'Yes', 'listgo' ) => 'yes' ),
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__( 'YouTube link', 'listgo' ),
					'param_name'    => 'video_bg_url',
					'value'         => 'https://www.youtube.com/watch?v=lMJXxhRFO1k',
					// default video url
					'description'   => esc_html__( 'Add YouTube link.', 'listgo' ),
					'dependency'    => array(
						'element'   => 'video_bg',
						'not_empty' => true,
					),
				),
				array(
					'type'          => 'checkbox',
					'heading'       => esc_html__( 'Equal height', 'listgo' ),
					'param_name'    => 'equal_height',
					'description'   => esc_html__( 'If checked columns will be set to equal height.', 'listgo' ),
					'value' => array( esc_html__( 'Yes', 'listgo' ) => 'yes' ),
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__( 'Content position', 'listgo' ),
					'param_name'    => 'content_placement',
					'value' => array(
						esc_html__( 'Default', 'listgo' )    => '',
						esc_html__( 'Top', 'listgo' )        => 'top',
						esc_html__( 'Middle', 'listgo' )     => 'middle',
						esc_html__( 'Bottom', 'listgo' )     => 'bottom',
					),
					'description' => esc_html__( 'Select content position within columns.', 'listgo' ),
				),
				array(
					'type'          => 'wiloke_colorpicker',
					'heading'       => esc_html__( 'Overlay Color', 'listgo' ),
					'param_name'    => 'overlay_color',
					'value'         => ''
				),
				array(
					'type'          => 'wiloke_colorpicker',
					'heading'       => esc_html__( 'Background Gradient - Left.', 'listgo' ),
					'description'   => esc_html__( 'Notice: If you want to use Background Gradient for the hero section, the both Background Gradient Left and Background Gradient Right are required. This setting will override the Overlay Color setting above.', 'listgo' ),
					'param_name'    => 'bg_gradient_left',
					'value'         => ''
				),
				array(
					'type'          => 'wiloke_colorpicker',
					'heading'       => esc_html__( 'Background Gradient - Right', 'listgo' ),
					'param_name'    => 'bg_gradient_right',
					'value'         => ''
				),
				array(
					'type'          => 'el_id',
					'heading'       => esc_html__( 'Row ID', 'listgo' ),
					'param_name'    => 'el_id',
					'description'   => sprintf( __( 'Enter row ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">w3c specification</a>).', 'listgo' ), 'http://www.w3schools.com/tags/att_global_id.asp' ),
				),
				array(
					'type'          => 'checkbox',
					'heading'       => esc_html__( 'Disable row', 'listgo' ),
					'param_name'    => 'disable_element',
					// Inner param name.
					'description'   => esc_html__( 'If checked the row won\'t be visible on the public side of your website. You can switch it back any time.', 'listgo' ),
					'value' => array( esc_html__( 'Yes', 'listgo' ) => 'yes' ),
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__( 'Extra class name', 'listgo' ),
					'param_name'    => 'el_class',
					'description'   => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'listgo' ),
				)
			),
			'js_view' => 'VcRowView',
		),
		// Hero
		array(
			'name'          => esc_html__('Hero', 'listgo'),
			'description'   => esc_html__('This shortcode should be used at the top of the page', 'listgo'),
			'base'          => 'wiloke_hero',
			'icon'          => '',
			'show_settings_on_create' => true,
			'category'      => WILOKE_THEMENAME,
			'controls'      => true,
			'params'        => array(
				array(
					'type'          => 'wiloke_description',
					'heading'       => esc_html__('Following instructions', 'listgo'),
					'param_name'    => 'following_instructions',
					'description'   => Wiloke::wiloke_kses_simple_html( __('Please following our instructions to know how to  <a href="https://goo.gl/jEkify" target="_blank">create a hero for your site</a>', 'listgo'), true)
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Sup title', 'listgo'),
					'param_name'    => 'sup_title',
					'std'           => 'Made by Wiloke',
					'save_always'   => true
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Title', 'listgo'),
					'param_name'    => 'title',
					'std'           => 'Listgo The Best Travel WordPress Theme',
					'save_always'   => true
				),
				array(
					'type'          => 'textarea',
					'heading'       => esc_html__('Description', 'listgo'),
					'param_name'    => 'description',
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Button Name', 'listgo'),
					'param_name'    => 'button_name',
					'std'           => 'Learn more',
					'save_always'   => true
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Button Link', 'listgo'),
					'param_name'    => 'button_link',
					'std'           => '#',
					'save_always'   => true
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Toggle Browsing Listing Categories', 'listgo'),
					'description'   => esc_html__('Adding a list of categories under the button above. To show the icon like our demo, please click on Listings -> Listing Location -> Upload Map Marker icon for each category', 'listgo'),
					'param_name'    => 'toggle_browsing_listing_category',
					'std'           => 'disable',
					'value'         => array(
						esc_html__('Enable', 'listgo')  => 'enable',
						esc_html__('Disable', 'listgo') => 'disable'
					),
					'save_always'   => true
				),
				
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Browsing Title', 'listgo'),
					'description'   => esc_html__('Similar to "Return" in keyboard phone.', 'listgo'),
					'param_name'    => 'browsing_title',
					'std'           => esc_html__('Or browse the highlights', 'listgo'),
					'save_always'   => true,
					'dependency'    => array('element'=>'toggle_browsing_listing_category', 'value'=>array('enable')),
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Get Listing Categories By', 'listgo'),
					'param_name'    => 'get_listing_category_by',
					'dependency'    => array('element'=>'toggle_browsing_listing_category', 'value'=>array('enable')),
					'std'           => 'id',
					'value'         => array(
						esc_html__('Date - Latest Categories', 'listgo') => 'id',
						esc_html__('Random', 'listgo') => 'random',
						esc_html__('Count - Displaying terms that have the most listings', 'listgo') => 'count',
						esc_html__('Specify Category IDs', 'listgo') => 'specify'
					),
					'save_always'   => true
				),
				array(
					'type'          => 'wiloke_get_list_of_terms',
					'heading'       => esc_html__('Which categories that you want to show.', 'listgo'),
					'description'   => esc_html__('It should smaller or enqual to 6 categories', 'listgo'),
					'taxonomy'      => 'listing_cat',
					'is_multiple'   => true,
					'param_name'    => 'specify_browsing_categories',
					'save_always'   => true,
					'std'           => '',
					'dependency'    => array('element'=>'get_listing_category_by', 'value'=>array('specify')),
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Number of categories', 'listgo'),
					'description'   => esc_html__('It should smaller or enqual to 6 categories', 'listgo'),
					'param_name'    => 'number_of_browsing_categories',
					'save_always'   => true,
					'std'           => 6,
					'dependency'    => array('element'=>'get_listing_category_by', 'value'=>array('id', 'random', 'count')),
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Toggle Search Form', 'listgo'),
					'param_name'    => 'toggle_search_form',
					'std'           => 'enable',
					'value'         => array(
						esc_html__('Enable', 'listgo')  => 'enable',
						esc_html__('Disable', 'listgo') => 'disable'
					),
					'save_always'   => true
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Search Form Title', 'listgo'),
					'param_name'    => 'search_form_title',
					'std'           => esc_html__('Search ListGo and Beyond', 'listgo'),
					'dependency'    => array('element'=>'alignment', 'value'=>array('not_center', 'not_center_2', 'not_center_3')),
					'save_always'   => true
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Search Field Title', 'listgo'),
					'param_name'    => 'search_field_title',
					'std'           => esc_html__('Address, city or select suggestion category', 'listgo'),
					'dependency'    => array('element'=>'toggle_search_form', 'value'=>array('enable')),
					'save_always'   => true
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Location Title', 'listgo'),
					'param_name'    => 'location_field_title',
					'std'           => esc_html__('Location', 'listgo'),
					'dependency'    => array('element'=>'toggle_search_form', 'value'=>array('enable')),
					'save_always'   => true
				),
				array(
					'type'          => 'wiloke_list_of_posts',
					'heading'       => esc_html__('Search page', 'listgo'),
					'description'   => esc_html__('A search page is required by search form. When someone click on Search button, it will redirect to this page. Leave empty to use the default setting: Appearance  -> Theme Options -> Listing Settings', 'listgo'),
					'param_name'    => 'map_page',
					'post_type'     => 'page',
					'dependency'    => array('element'=>'toggle_search_form', 'value'=>array('enable')),
					'std'           => ''
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Alignment', 'listgo'),
					'param_name'    => 'alignment',
					'std'           => 'center',
					'value'         => array(
						esc_html__('Center Style 1', 'listgo') => 'center',
						esc_html__('Center Style 2', 'listgo') => 'center2',
						esc_html__('Center Style 3', 'listgo') => 'center3',
						esc_html__('Text on right and Search Form on Left Style 1', 'listgo') => 'not_center',
						esc_html__('Text on right and Search Form on Left Style 2', 'listgo') => 'not_center_2',
						esc_html__('Text on right and Search Form on Left Style 3', 'listgo') => 'not_center_3',
					),
					'save_always'   => true
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Height', 'listgo'),
					'param_name'    => 'height',
					'value'         => '',
					'description'   => esc_html__('Enter height of hero. Default set full screen.', 'listgo'),
				),
			)
		),
		// Heading
		array(
			'name'  => esc_html__('Heading', 'listgo'),
			'base'  => 'wiloke_heading',
			'icon'  => '',
			'show_settings_on_create' => true,
			'category'  => WILOKE_THEMENAME,
			'controls'  => true,
			'params'    => array(
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Block Name', 'listgo'),
					'param_name'    => 'blockname',
					'std'           => 'Landmarks',
					'value'         => 'Landmarks',
					'save_always'   => true
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Title', 'listgo'),
					'param_name'    => 'title',
					'std'           => 'New Listings',
					'value'         => 'New Listings',
					'save_always'   => true
				),
				array(
					'type'          => 'textarea',
					'heading'       => esc_html__('Description', 'listgo'),
					'param_name'    => 'description',
					'std'           => 'Enter in a description for this block',
					'value'         => 'Enter in a description for this block',
					'save_always'   => true
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Alignment', 'listgo'),
					'param_name'    => 'alignment',
					'std'           => 'text-left',
					'value'         => array(
						esc_html__('Left', 'listgo')     => 'text-left',
						esc_html__('Right', 'listgo')    => 'text-right',
						esc_html__('Center', 'listgo')   => 'text-center'
					),
					'save_always'   => true
				),
				array(
					'type'          => 'wiloke_colorpicker',
					'heading'       => esc_html__('Blog name color', 'listgo'),
					'param_name'    => 'blogname_color'
				),
				array(
					'type'          => 'wiloke_colorpicker',
					'heading'       => esc_html__('Title Color', 'listgo'),
					'param_name'    => 'title_color'
				),
				array(
					'type'          => 'wiloke_colorpicker',
					'heading'       => esc_html__('Description Color', 'listgo'),
					'param_name'    => 'description_color'
				),
			)
		),
		// Wiloke Design Tool
		array(
			'name'	=> esc_html__('Wiloke Design Tool', 'listgo'),
			'base'	=> 'wiloke_design_portfolio',
			'icon'	=> '',
			'show_settings_on_create'	=> true,
			'has_autocomplete'	        => true,
			'category'					=> WILOKE_THEMENAME,
			'controls'					=> true,
			'admin_enqueue_js'          => array(get_template_directory_uri().'/admin/asset/js/packery.pkgd.min.js'),
			'params'    => array(
				array(
					'type'  => 'wiloke_description',
					'title' => esc_html__('Following our instructions', 'listgo'),
					'param_name' => 'following_instructions',
					'description'   => Wiloke::wiloke_kses_simple_html(__('Please following our instructions to know how to use <a href="https://goo.gl/elaFy3" target="_blank">Wiloke Design Tool</a>', 'listgo'), true)
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Get Listings By', 'listgo'),
					'description'   => esc_html__('Featured Listings: To set a listing as Featured Listing, please click on Listings -> Your Listing -> Select Enable Featured Listing.', 'listgo'),
					'param_name'    => 'get_posts_from',
					'value'         => array(
						esc_html__('Listing Category', 'listgo')        => 'listing_cat',
						esc_html__('Listing Location', 'listgo')        => 'listing_location',
						esc_html__('Featured Listings', 'listgo')       => 'featured_listings',
						esc_html__('Specify Listing IDs', 'listgo')     => 'custom'
					),
					'std'           => 'listing_cat' // Set default layout
				),
				array(
					'type'          => 'wiloke_get_list_of_terms',
					'heading'       => esc_html__('Pickup Listing Categories - Holding Ctrl to select multiple items.', 'listgo'),
					'taxonomy'      => 'listing_cat',
					'is_multiple'   => true,
					'description'   => esc_html__('Choosing the categories where posts will be get from.', 'listgo'),
					'param_name'    => 'listing_cat',
					'save_always'   => true,
					'std'           => '',
					'dependency'    => array('element'=>'get_posts_from', 'value'=>array('listing_cat'))
				),
				array(
					'type'          => 'wiloke_get_list_of_terms',
					'heading'       => esc_html__('Pickup Locations - Holding Ctrl to select multiple items', 'listgo'),
					'taxonomy'      => 'listing_location',
					'is_multiple'   => true,
					'description'   => esc_html__('Choosing the categories where posts will be get from.', 'listgo'),
					'param_name'    => 'listing_location',
					'save_always'   => true,
					'std'           => '',
					'dependency'    => array('element'=>'get_posts_from', 'value'=>array('listing_location'))
				),
				array(
					'type'        => 'autocomplete',
					'heading'     => esc_html__('Specify Post IDs', 'listgo'),
					'description' => esc_html__('Enter in the title for searching.', 'listgo'),
					'param_name'  => 'include',
					'settings'    => array(
						'multiple' => true,
						'sortable' => true,
						'groups'   => true,
					),
					'admin_label'   => true,
					'dependency'    => array('element' => 'get_posts_from', 'value'   => array('custom')),
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Item Type', 'listgo'),
					'description'   => esc_html__('Taxonomy: Showing themselves Locations / Categories. It is very useful if you want, for example, to create a block of continentals such as Asia, American, European, Africa. Listing: Showing articles that are children of the picked categories/locations.', 'listgo'),
					'param_name'    => 'item_type',
					'std'           => 'listing',
					'value'         => array(
						esc_html__('Listings', 'listgo')  => 'listing',
						esc_html__('Taxonomies', 'listgo') => 'taxonomy'
					),
					'dependency'    => array('element'=>'get_posts_from', 'value'=>array('listing_cat', 'listing_location')),
					'save_always'   => true
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Text Subfix after number of articles', 'listgo'),
					'description'   => esc_html__('The text after number of articles. For example: We have 123 articles in Asia Listing Location. If you enter in a subfix as "Landmarks", it should be show 123 Landmarks on the front-page. Note that you can also use Landmark|Landmarks. Landmark will be used if, in case, there is only one post in the category. ', 'listgo'),
					'param_name'    => 'subfix_after_number_of_articles',
					'dependency'    => array('element'=>'item_type', 'value'=>array('taxonomy')),
					'std'           => 'Landmarks',
					'save_always'   => true
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Show terms', 'listgo'),
					'description'   => esc_html__('Choosing kind of terms will be shown on each project item.', 'listgo'),
					'param_name'    => 'show_terms',
					'std'           => 'both',
					'value'         => array(
						esc_html__('Listing Locations and Listing Categories', 'listgo') => 'both',
						esc_html__('Only Listing Locations', 'listgo')                   => 'listing_location',
						esc_html__('Only Listing Categories', 'listgo')                  => 'listing_cat'
					),
					'save_always'   => true
				),
				array(
					'type'          => 'wiloke_design_portfolio_choose_layout',
					'heading'       => '',
					'param_name'    => 'wiloke_design_portfolio_choose_layout',
					'options'       => array(
						'grid' => array(
							'heading'     => esc_html__('Grid', 'listgo'),
							'img_url'     => get_template_directory_uri() . '/admin/source/design-layout/img/1.jpg',
							'is_customize' => 'no',
							'value' => 'eyJpdGVtc19zaXplIjoiY3ViZSwgY3ViZSwgY3ViZSwgY3ViZSwgY3ViZSwgY3ViZSwgY3ViZSwgY3ViZSwgY3ViZSJ9'
						),
						'masonry' => array(
							'heading'     => esc_html__('Masonry', 'listgo'),
							'img_url'     => get_template_directory_uri() . '/admin/source/design-layout/img/3.jpg',
							'is_customize' => 'no',
							'value' => 'eyJpdGVtc19zaXplIjoiY3ViZSwgbGFyZ2UsIHdpZGUsIGN1YmUsIGhpZ2gsIGN1YmUsIGV4dHJhLWxhcmdlLCB3aWRlLCBjdWJlIn0='
						),
						'creative' => array(
							'heading'     => esc_html__('Creative', 'listgo'),
							'img_url'     => get_template_directory_uri() . '/admin/source/design-layout/img/5.jpg',
							'is_customize' => 'yes',
							'value' => 'eyJpdGVtc19zaXplIjoiY3ViZSwgbGFyZ2UsIHdpZGUsIGN1YmUsIGhpZ2gsIGN1YmUsIGV4dHJhLWxhcmdlLCB3aWRlLCBjdWJlIn0='
						)
					),
					'group'         => esc_html__( 'Choose Layout', 'listgo'),
					'save_always'   => true,
					'std'           => 'creative' // Set default layout
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Order By', 'listgo'),
					'param_name'    => 'order_by',
					'std'           => 'date',
					'value'         => array(
						esc_html__('Listing Date', 'listgo')     => 'post_date',
						esc_html__('Listing Title', 'listgo')    => 'post_title',
						esc_html__('Listing Author', 'listgo')   => 'post_author',
						esc_html__('Comments Count', 'listgo')   => 'comment_count',
						esc_html__('Random', 'listgo')           => 'rand',
						esc_html__('Premium Listing First', 'listgo') => 'menu_order'
					),
					'save_always'   => true
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Sort the result-set in', 'listgo'),
					'param_name'    => 'sort_in',
					'std'           => 'DESC',
					'value'         => array(
						esc_html__('Descending Order (DESC)', 'listgo')  => 'DESC',
						esc_html__('Ascending Order (ASC)', 'listgo')    => 'ASC'
					),
					'save_always'   => true
				),
				array(
					'type'             => 'wiloke_design_portfolio_layout',
					'heading'          => esc_html__('Portfolio', 'listgo'),
					'general_settings' => array(
						'number_of_posts' => 9
					),
					'devices_settings' => array(
						'large' => array(
							'items_per_row'   => 4,
							'horizontal'      => 0,
							'vertical'        => 0
						),
						'medium' => array(
							'items_per_row'   => 3,
							'horizontal'      => 0,
							'vertical'        => 0
						),
						'small' => array(
							'items_per_row'   => 2,
							'horizontal'      => 0,
							'vertical'        => 0
						),
						'extra_small' => array(
							'items_per_row'   => 1,
							'horizontal'      => 0,
							'vertical'        => 0
						)
					),
					'param_name'       => 'wiloke_portfolio_layout',
					'options'          => array(
						'creative'     => array(
							'heading'       => esc_html__('Creative', 'listgo'),
							'img_url'       => get_template_directory_uri() . '/admin/source/design-layout/img/3.jpg',
							'is_dragdrop'   => 'yes',
							'is_add_sub_btn'=> 'yes',
							'params' => array(
								array(
									'type'          => 'select',
									'param_name'    => 'items_per_row',
									'heading'       => esc_html__('Items Per Row', 'listgo'),
									'description'   => esc_html__('How many items per row?', 'listgo'),
									'options'       => array(
										5 => 5,
										4 => 4,
										3 => 3,
										2 => 2,
										1 => 1
									)
								),
								array(
									'type'          => 'number',
									'param_name'    => 'horizontal',
									'heading'       => esc_html__('Horizontal Spacing', 'listgo'),
									'description'   => ''
								),
								array(
									'type'          => 'number',
									'param_name'    => 'vertical',
									'heading'       => esc_html__('Vertical Spacing', 'listgo'),
									'description'   => ''
								),
								array(
									'type'          => 'number',
									'param_name'    => 'amount_of_loadmore',
									'heading'       => esc_html__('Amount of Loadmore', 'listgo'),
									'description'   => esc_html__('In the case Load more projects functionality (General Tab) to be used, this setting is effected. Leave empty means it is equal to number of posts.', 'listgo')
								),
							),
							'std'   => array(
								'items_size' => 'large,cube,cube,cube,cube,cube,large,cube,cube'
							)
						)
					),
					'group'         => 'Design Layout',
					'save_always'   => true,
					'std'           => 'creative' // Set default layout
				)
			)
		),
		// List Layout
		array(
			'name'	=> esc_html__('Listing Layout', 'listgo'),
			'base'	=> 'wiloke_listing_layout',
			'icon'	=> '',
			'show_settings_on_create'	=> true,
			'has_autocomplete'	=> true,
			'category'					=> WILOKE_THEMENAME,
			'controls'					=> true,
			'params'    => array(
				array(
					'type'  => 'wiloke_description',
					'title' => esc_html__('Following our instructions', 'listgo'),
					'param_name' => 'following_instructions',
					'description'   => Wiloke::wiloke_kses_simple_html(__('Please following our instructions to know how to use <a href="https://goo.gl/mBLnlm" target="_blank">Listing Layout Shortcode</a>', 'listgo'), true)
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Layout', 'listgo'),
					'param_name'    => 'layout',
					'std'           => 'listing-list',
					'save_always'   => true,
					'value'         => array(
						esc_html__('Grid', 'listgo')     => 'listing--grid',
						esc_html__('Grid 2', 'listgo')   => 'listing--grid1',
						esc_html__('Grid 3', 'listgo')   => 'listing-grid2',
						esc_html__('Grid 4', 'listgo')   => 'listing-grid3',
						esc_html__('Grid 5', 'listgo')   => 'listing-grid4',
						esc_html__('List', 'listgo')     => 'listing--list',
						esc_html__('List 2', 'listgo')   => 'listing--list1',
						esc_html__('List Circle Thumbnail (New)', 'listgo')   => 'circle-thumbnail',
						esc_html__('List Creative Rectangle (New)', 'listgo')   => 'creative-rectangle'
					)
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Get Listings By', 'listgo'),
					'param_name'    => 'get_posts_from',
					'value'         => array(
						esc_html__('Listing Category', 'listgo')     => 'listing_cat',
						esc_html__('Listing Location', 'listgo')     => 'listing_location',
						esc_html__('Specify Listings IDs', 'listgo')  => 'custom',
						esc_html__('Latest Articles', 'listgo')      => 'latest_posts',
						esc_html__('Post Authors', 'listgo')         => 'post_author'
					),
					'std'           => 'latest_posts' // Set default layout
				),
				array(
					'type'          => 'wiloke_get_post_authors',
					'heading'       => esc_html__('Set Post Authors', 'listgo'),
					'role'          => 'any',
					'is_select2'    => false,
					'is_multiple'   => true,
					'description'   => esc_html__('Enter in author username here', 'listgo'),
					'param_name'    => 'post_authors',
					'save_always'   => true,
					'std'           => '',
					'dependency'    => array('element'=>'get_posts_from', 'value'=>array('post_author'))
				),
				array(
					'type'          => 'wiloke_get_list_of_terms',
					'heading'       => esc_html__('Pickup Listing Categories', 'listgo'),
					'taxonomy'      => 'listing_cat',
					'is_multiple'   => true,
					'description'   => esc_html__('Choosing the categories where posts will be get from.', 'listgo'),
					'param_name'    => 'listing_cat',
					'save_always'   => true,
					'std'           => '',
					'dependency'    => array('element'=>'get_posts_from', 'value'=>array('listing_cat'))
				),
				array(
					'type'          => 'wiloke_get_list_of_terms',
					'heading'       => esc_html__('Pickup Listing Locations', 'listgo'),
					'taxonomy'      => 'listing_location',
					'is_multiple'   => true,
					'description'   => esc_html__('Choosing the categories where posts will be get from.', 'listgo'),
					'param_name'    => 'listing_location',
					'save_always'   => true,
					'std'           => '',
					'dependency'    => array('element'=>'get_posts_from', 'value'=>array('listing_location'))
				),
				array(
					'type'        => 'autocomplete',
					'heading'     => esc_html__('Specify Listings IDs', 'listgo'),
					'description' => esc_html__('Enter in the title for searching.', 'listgo'),
					'param_name'  => 'include',
					'settings'    => array(
						'multiple' => true,
						'sortable' => true,
						'groups'   => true,
					),
					'admin_label'   => true,
					'dependency'    => array('element' => 'get_posts_from', 'value'   => array('custom')),
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Show terms', 'listgo'),
					'description'   => esc_html__('Choosing kind of terms will be shown on each project item. Note that Circle Layout, Creative Rectangle does not support render the both Listing Locations and Listing Categories ', 'listgo'),
					'param_name'    => 'show_terms',
					'std'           => 'both',
					'save_always'   => true,
					'value'         => array(
						esc_html__('Listing Locations and Listing Categories', 'listgo') => 'both',
						esc_html__('Only Listing Locations', 'listgo')                   => 'listing_location',
						esc_html__('Only Listing Categories', 'listgo')                  => 'listing_cat'
					)
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Filter Type', 'listgo'),
					'description'   => esc_html__('Showing Filter Navigation at the top of the block. Filter items are Listing Categories or Listing Locations - it depents on your "Get listings by" setting', 'listgo'),
					'param_name'    => 'filter_type',
					'save_always'   => true,
					'value'         => array(
						esc_html__('Navigation', 'listgo') => 'navigation',
						esc_html__('Drop down', 'listgo')  => 'dropdown',
						esc_html__('None', 'listgo')       => 'none',
					),
					'dependency'    => array('element'=>'get_posts_from', 'value'=>array('listing_location', 'listing_cat')),
					'std'           => 'navigation' // Set default layout
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Filter Result Description', 'listgo'),
					'description'   => esc_html__('This setting describes the result from a filter action. %found_listing%: Number of listings are matched with filter pattern: %result_text%: If we found smaller than 2 results, the text before | will be used. %total_listing%: Showing number of listings currently. If you enter a structure like this *open_result*%found_listing% %result_text="Result|Results"% *end_result* in %total_listing% Destinations", The front-end should show like this: 33 Results in 123 Destinations.', 'listgo'),
					'param_name'    => 'filter_result_description',
					'std'           => '*open_result* %found_listing% %result_text=Result|Results% *end_result* in %total_listing% Destinations',
					'save_always'   => true,
					'dependency'    => array('element'=>'filter_type', 'value'=>array('dropdown'))
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Order By', 'listgo'),
					'param_name'    => 'order_by',
					'save_always'   => true,
					'std'           => 'date',
					'value'         => array(
						esc_html__('Date', 'listgo')          => 'post_date',
						esc_html__('Title', 'listgo')         => 'post_title',
						esc_html__('Author', 'listgo')        => 'post_author',
						esc_html__('Comment Count', 'listgo') => 'comment_count',
						esc_html__('Random', 'listgo')        => 'rand',
						esc_html__('Featured Listings First', 'listgo') => 'menu_order',
						esc_html__('Promotion First - Then Latest Article', 'listgo')      => 'promotion_first_then_latest_article',
					),
					'dependency'    => array('element'=>'get_posts_from', 'value'=>array('listing_cat', 'listing_location'))
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Number of Articles', 'listgo'),
					'param_name'    => 'posts_per_page',
					'std'           => 10,
					'save_always'   => true,
					'dependency'    => array('element'=>'get_posts_from', 'value'=>array('listing_cat', 'listing_location', 'latest_posts'))
				),
				array(
					'heading'   => esc_html__('Order By', 'listgo'),
					'type'      => 'dropdown',
					'param_name'=> 'order_by',
					'save_always'  => true,
					'value'     => array(
						esc_html__('Post Date', 'listgo')    => 'post_date',
						esc_html__('Post Title', 'listgo')   => 'post_title',
						esc_html__('Post ID', 'listgo')      => 'ID',
						esc_html__('Post Author', 'listgo')  => 'post_author',
						esc_html__('Random', 'listgo')       => 'rand',
						esc_html__('Premium Listings First', 'listgo') => 'menu_order'
					)
				),
				array(
					'heading'   => esc_html__('Order', 'listgo'),
					'type'      => 'dropdown',
					'param_name'=> 'order',
					'save_always'  => true,
					'value'     => array(
						esc_html__('DESC', 'listgo')  => 'DESC',
						esc_html__('ACS', 'listgo')   => 'ASC'
					)
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Image Size', 'listgo'),
					'description'   => esc_html__('Set image size for the feature image. You can use one of the following keywords: large, medium, thumbnail or specify size by following this structure w,h, for example: 1000,400, it means you want to display a featured image of 1000 width x 4000 height.', 'listgo'),
					'param_name'    => 'image_size',
					'std'           => 'medium',
					'save_always'   => true
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Display Style', 'listgo'),
					'param_name'    => 'display_style',
					'save_always'   => true,
					'std'           => 'all',
					'value'         => array(
						esc_html__('Show All', 'listgo')   => 'all',
						esc_html__('Load more', 'listgo')  => 'loadmore',
						esc_html__('Link to Listings page', 'listgo')  => 'link_to_page',
						esc_html__('Pagination', 'listgo') => 'pagination'
					),
					'dependency'    => array('element'=>'get_posts_from', 'value'=>array('listing_cat', 'listing_location', 'latest_posts'))
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__( 'Button name', 'listgo'),
					'description'   => esc_html__('Enter in a name for the button. Maybe Load more or View more', 'listgo'),
					'param_name'    => 'btn_name',
					'std'           => 'Load More',
					'dependency'    => array('element'=>'display_style', 'value'=>array('loadmore', 'link_to_page')),
					'save_always'   => true
				),
				array(
					'type'          => 'wiloke_list_of_posts',
					'post_type'     => array('page'),
					'heading'       => esc_html__('Set Page Link', 'listgo'),
					'param_name'    => 'viewmore_page_link',
					'std'           => '#',
					'dependency'    => array('element'=>'display_style', 'value'=>array('link_to_page')),
					'save_always'   => true
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Button alignment', 'listgo'),
					'description'   => esc_html__('Set a position for the button', 'listgo'),
					'param_name'    => 'btn_position',
					'std'           => 'text-center',
					'dependency'    => array('element'=>'display_style', 'value'=>array('loadmore', 'link_to_page')),
					'value'         => array(
						esc_html__('Text Center', 'listgo')  => 'text-center',
						esc_html__('Text Left', 'listgo')    => 'text-left',
						esc_html__('Text Right', 'listgo')   => 'text-right'
					),
					'save_always'   => true
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Button Style', 'listgo'),
					'description'   => esc_html__('Set a style for the button', 'listgo'),
					'param_name'    => 'btn_style',
					'std'           => 'listgo-btn--default',
					'dependency'    => array('element'=>'display_style', 'value'=>array('loadmore', 'link_to_page')),
					'value'         => array(
						esc_html__('Default', 'listgo')    => 'listgo-btn--default',
						esc_html__('Round', 'listgo')    => 'listgo-btn--round'
					),
					'save_always'   => true
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Button Size', 'listgo'),
					'description'   => esc_html__('Set a position for the button', 'listgo'),
					'param_name'    => 'btn_size',
					'std'           => 'listgo-btn--small',
					'dependency'    => array('element'=>'display_style', 'value'=>array('loadmore', 'link_to_page')),
					'value'         => array(
						esc_html__('Large', 'listgo')    => 'listgo-btn--full',
						esc_html__('Small', 'listgo')    => 'listgo-btn--small'
					),
					'save_always'   => true
				),
				array(
					'type'       => 'dropdown',
					'group'      => esc_html__('Meta Data', 'listgo'),
					'heading'    => esc_html__('Toggle Author Avatar', 'listgo'),
					'param_name' => 'toggle_render_author',
					'value'         => array(
						esc_html__('Enable', 'listgo')   => 'enable',
						esc_html__('Disable', 'listgo')  => 'disable'
					),
					'std'        => 'enable',
					'save_always'=> true
				),
				array(
					'type'       => 'dropdown',
					'group'      => esc_html__('Meta Data', 'listgo'),
					'heading'    => esc_html__('Toggle Average Rating', 'listgo'),
					'param_name' => 'toggle_render_rating',
					'value'         => array(
						esc_html__('Enable', 'listgo')   => 'enable',
						esc_html__('Disable', 'listgo')  => 'disable'
					),
					'std'        => 'enable',
					'save_always'=> true
				),
				array(
					'type'       => 'dropdown',
					'group'      => esc_html__('Meta Data', 'listgo'),
					'heading'    => esc_html__('Toggle Favorite Icon', 'listgo'),
					'param_name' => 'toggle_render_favorite',
					'value'         => array(
						esc_html__('Enable', 'listgo')   => 'enable',
						esc_html__('Disable', 'listgo')  => 'disable'
					),
					'std'        => 'enable',
					'save_always'=> true
				),
				array(
					'type'       => 'textfield',
					'group'      => esc_html__('Meta Data', 'listgo'),
					'heading'    => esc_html__('Favorite Text', 'listgo'),
					'param_name' => 'favorite_description',
					'dependency' => array('element'=>'toggle_render_favorite', 'value'=>array('enable')),
					'std'        => 'Save',
					'save_always'=> true
				),
				array(
					'type'       => 'dropdown',
					'group'      => esc_html__('Meta Data', 'listgo'),
					'heading'    => esc_html__('Toggle Listing Excerpt', 'listgo'),
					'param_name' => 'toggle_render_post_excerpt',
					'value'         => array(
						esc_html__('Enable', 'listgo')   => 'enable',
						esc_html__('Disable', 'listgo')  => 'disable'
					),
					'std'        => 'enable',
					'save_always'=> true
				),
				array(
					'type'          => 'textfield',
					'group'      => esc_html__('Meta Data', 'listgo'),
					'heading'       => esc_html__('Post Excerpt', 'listgo'),
					'description'   => esc_html__('Maximum of character will be shown on the list item.', 'listgo'),
					'param_name'    => 'limit_character',
					'dependency' => array('element'=>'toggle_render_post_excerpt', 'value'=>array('enable')),
					'std'           => 100,
					'save_always'   => true
				),
				array(
					'type'       => 'dropdown',
					'group'      => esc_html__('Meta Data', 'listgo'),
					'heading'    => esc_html__('Toggle Listing Address', 'listgo'),
					'param_name' => 'toggle_render_address',
					'value'         => array(
						esc_html__('Enable', 'listgo')   => 'enable',
						esc_html__('Disable', 'listgo')  => 'disable'
					),
					'std'        => 'enable',
					'save_always'=> true
				),
				array(
					'type'       => 'dropdown',
					'group'      => esc_html__('Meta Data', 'listgo'),
					'heading'    => esc_html__('Toggle View Detail Button', 'listgo'),
					'param_name' => 'toggle_render_view_detail',
					'dependency' => array('element'=>'layout', 'value'=>array('listing--grid', 'listing--list', 'landmark--grid')),
					'value'         => array(
						esc_html__('Enable', 'listgo')   => 'enable',
						esc_html__('Disable', 'listgo')  => 'disable'
					),
					'std'        => 'enable',
					'save_always'=> true
				),
				array(
					'type'       => 'textfield',
					'group'      => esc_html__('Meta Data', 'listgo'),
					'heading'    => esc_html__('View Detail Text', 'listgo'),
					'param_name' => 'view_detail_text',
					'dependency' => array('element'=>'toggle_render_view_detail', 'value'=>array('enable')),
					'std'        => esc_html__('View Detail', 'listgo')
				),
				array(
					'type'       => 'dropdown',
					'group'      => esc_html__('Meta Data', 'listgo'),
					'heading'    => esc_html__('Toggle Get Directions', 'listgo'),
					'param_name' => 'toggle_render_find_direction',
					'dependency' => array('element'=>'layout', 'value'=>array('listing--grid', 'listing--list')),
					'value'         => array(
						esc_html__('Enable', 'listgo')   => 'enable',
						esc_html__('Disable', 'listgo')  => 'disable'
					),
					'std'        => 'enable',
					'save_always'=> true
				),
				array(
					'type'       => 'textfield',
					'group'      => esc_html__('Meta Data', 'listgo'),
					'heading'    => esc_html__('Get Directions Description', 'listgo'),
					'param_name' => 'find_direction_text',
					'dependency' => array('element'=>'toggle_render_find_direction', 'value'=>array('enable')),
					'std'        => esc_html__('Get Directions', 'listgo')
				),
				array(
					'type'       => 'dropdown',
					'group'      => esc_html__('Meta Data', 'listgo'),
					'heading'    => esc_html__('Toggle Link To Map Page', 'listgo'),
					'description'=> esc_html__('If you want to use this feature, please ensure that a Map page has been set to: Appearance -> Theme Options -> Listing Settings -> Map Page.', 'listgo'),
					'param_name' => 'toggle_render_link_to_map_page',
					'value'         => array(
						esc_html__('Enable', 'listgo')   => 'enable',
						esc_html__('Disable', 'listgo')  => 'disable'
					),
					'std'        => 'enable',
					'save_always'=> true
				),
				array(
					'type'       => 'textfield',
					'group'      => esc_html__('Meta Data', 'listgo'),
					'heading'    => esc_html__('Link To Map Page Description', 'listgo'),
					'param_name' => 'link_to_map_page_text',
					'dependency' => array('element'=>'toggle_render_link_to_map_page', 'value'=>array('enable')),
					'std'        => esc_html__('Go to map', 'listgo'),
					'save_always'=> true
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Block ID', 'listgo'),
					'description'   => esc_html__('This field SHOULD NOT empty and an unique id is required. This id will be used as a caching key', 'listgo'),
					'param_name'    => 'block_id',
					'std'           => uniqid('listing_layout_'),
					'save_always'   => true
				),
				array(
					'type'          => 'hidden',
					'heading'       => '',
					'param_name'    => 'created_at',
					'std'           => time(),
					'save_always'   => ''
				)
			)
		),
		// Map
		array(
			'name'                      => esc_html__('Listings In Map', 'listgo'),
			'icon'                      => '',
			'base'                      => 'wiloke_awesome_map',
			'category'                  => WILOKE_THEMENAME,
			'controls'                  => true,
			'show_settings_on_create'   => true,
			'params'                    => array(
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Source Map', 'listgo'),
					'param_name'    => 'source_map',
					'save_always'   => true,
					'std'           => 'all',
					'value'         => array(
						esc_html__('All Taxonomies', 'listgo')   => 'all',
						esc_html__('Listing Cat', 'listgo')      => 'listing_cat',
						esc_html__('Listing Location', 'listgo') => 'listing_location'
					)
				),
				array(
					'type'          => 'wiloke_get_list_of_terms',
					'heading'       => esc_html__('Listing Locations', 'listgo'),
					'description'   => esc_html__('Leave empty to get all locations', 'listgo'),
					'param_name'    => 'listing_location',
					'is_multiple'   => true,
					'std'           => '',
					'taxonomy'      => 'listing_location',
					'dependency'    => array('element'=>'source_map', 'value'=>array('listing_location'))
				),
				array(
					'type'          => 'wiloke_get_list_of_terms',
					'heading'       => esc_html__('Listing Cats', 'listgo'),
					'description'   => esc_html__('Leave empty to get all locations', 'listgo'),
					'param_name'    => 'listing_cat',
					'is_multiple'   => true,
					'std'           => '',
					'taxonomy'      => 'listing_cat',
					'dependency'    => array('element'=>'source_map', 'value'=>array('listing_cat'))
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Map Center', 'listgo'),
					'description'   => esc_html__('Set coordinate you want display as the map center. For example:21.027764,105.834160.  Leave empty to get the first article', 'listgo'),
					'param_name'    => 'center',
					'std'           => ''
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Map Theme', 'listgo'),
					'description'   => Wiloke::wiloke_kses_simple_html(__('Leave empty to use the Theme Options setting. Please refer to Wiloke Guide -> FAQs -> How to create a Map page to know more.', 'listgo'), true),
					'param_name'    => 'map_theme',
					'std'           => ''
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Disable draggable when init', 'listgo'),
					'description'   => esc_html__('This feature is useful, in case, you want to use Map shortcode combile with the other shortcodes. The user can Enable draggable by clicking on Draggable button', 'listgo'),
					'param_name'    => 'disable_draggable_when_init',
					'std'           => 'disable',
					'value'         => array(
						esc_html__('Enable', 'listgo')  => 'enable',
						esc_html__('Disable', 'listgo') => 'disable'
					)
				),

				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Order By', 'listgo'),
					'param_name'    => 'order_by',
					'std'           => 'post_date',
					'value'         => array(
						esc_html__('Listing Date', 'listgo')     => 'post_date',
						esc_html__('Listing Title', 'listgo')    => 'post_title',
						esc_html__('Listing Author', 'listgo')   => 'post_author',
						esc_html__('Comments Count', 'listgo')   => 'comment_count',
						esc_html__('Random', 'listgo')           => 'rand',
						esc_html__('Featured Listings', 'listgo') => 'highlight',
					),
					'save_always'   => true
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Sort the result-set in', 'listgo'),
					'param_name'    => 'order',
					'std'           => 'DESC',
					'value'         => array(
						esc_html__('Descending Order (DESC)', 'listgo')  => 'DESC',
						esc_html__('Ascending Order (ASC)', 'listgo')    => 'ASC'
					),
					'save_always'   => true
				),
			)
		),
		array(
		 	'name'                      => esc_html__('Listings In Map 2 (Half Map)', 'listgo'),
		 	'icon'                      => '',
		 	'base'                      => 'wiloke_half_map',
		 	'category'                  => WILOKE_THEMENAME,
		 	'controls'                  => true,
		 	'show_settings_on_create'   => true,
		 	'params'                    => array(
			    array(
				    'type'          => 'textfield',
				    'heading'       => esc_html__('Map Maximum Zoom (Desktop)', 'listgo'),
				    'description'   => esc_html__('If you want to build a map page only, We recommend using Half Map template instead', 'listgo'),
				    'param_name'    => 'max_zoom',
				    'std'           => 4,
				    'save_always'   => true
			    ),
			    array(
				    'type'          => 'textfield',
				    'heading'       => esc_html__('Map Minimum Zoom (Desktop)', 'listgo'),
				    'description'   => esc_html__('A negative number is allowable', 'listgo'),
				    'param_name'    => 'min_zoom',
				    'std'           => -1,
				    'save_always'   => true
			    ),
			    array(
				    'type'          => 'textfield',
				    'heading'       => esc_html__('Map Center Zoom', 'listgo'),
				    'param_name'    => 'center_zoom',
				    'std'           => 10,
				    'save_always'   => true
			    ),
		 		array(
		 			'type'          => 'textfield',
		 			'heading'       => esc_html__('Map Center', 'listgo'),
		 			'description'   => esc_html__('Set coordinate you want display as the map center. For example:21.027764,105.834160.  Leave empty to get the first article', 'listgo'),
		 			'param_name'    => 'center',
		 			'std'           => ''
		 		),
			    array(
				    'type'          => 'textfield',
				    'param_name'    => 'max_cluster_radius',
				    'heading'       => esc_html__('Map Cluster Radius', 'listgo'),
				    'description'   => Wiloke::wiloke_kses_simple_html(__('The maximum radius that a cluster will cover from the central marker', 'listgo'), true),
				    'std'           => 60,
				    'save_always'   => true
			    ),
			    array(
				    'type'        => 'textfield',
				    'param_name'  => 'posts_per_page',
				    'heading'     => esc_html__('Listings per page', 'listgo'),
				    'std'         => 4,
				    'save_always' => true
			    ),
			    array(
				    'type'          => 'dropdown',
				    'heading'       => esc_html__('Show terms', 'listgo'),
				    'description'   => esc_html__('Choosing kind of terms will be shown on each project item.', 'listgo'),
				    'param_name'    => 'show_terms',
				    'std'           => 'both',
				    'value'         => array(
					    esc_html__('Listing Locations and Listing Categories', 'listgo') => 'both',
					    esc_html__('Only Listing Locations', 'listgo')                   => 'listing_location',
					    esc_html__('Only Listing Categories', 'listgo')                  => 'listing_cat'
				    ),
				    'save_always'   => true
			    )
		 	)
		 ),
		array(
			'name'                      => esc_html__('Grid rotator', 'listgo'),
			'icon'                      => '',
			'base'                      => 'wiloke_grid_rotator',
			'category'                  => WILOKE_THEMENAME,
			'controls'                  => true,
			'show_settings_on_create'   => true,
			'has_autocomplete'	=> true,
			'params'                    => array(
				array(
					'type'         => 'dropdown',
					'heading'      => esc_html__('Get Listings by', 'listgo'),
					'param_name'   => 'get_listings_by',
					'save_always'  => 'latest_posts',
					'value'        => array(
						esc_html__('Latest Listings', 'listgo')      => 'latest_posts',
						esc_html__('Specify Locations', 'listgo')    => 'listing_location',
						esc_html__('Specify Categories', 'listgo')   => 'listing_cat',
						esc_html__('Upload Images', 'listgo')        => 'upload_images',
					)
				),
				array(
					'type'         => 'wiloke_get_list_of_terms',
					'heading'      => esc_html__('Select categories', 'listgo'),
					'param_name'   => 'listing_cat',
					'taxonomy'     => 'listing_cat',
					'dependency'   => array(
						'element' => 'get_listings_by',
						'value'   => array('listing_cat')
					),
					'is_multiple'  => true,
					'save_always'  => true
				),
				array(
					'type'         => 'wiloke_get_list_of_terms',
					'heading'      => esc_html__('Select Locations', 'listgo'),
					'param_name'   => 'listing_location',
					'taxonomy'     => 'listing_location',
					'dependency'   => array(
						'element' => 'get_listings_by',
						'value'   => array('listing_location')
					),
					'is_multiple'  => true,
					'save_always'  => true
				),
				array(
					'type'         => 'dropdown',
					'heading'      => esc_html__('Display Type', 'listgo'),
					'param_name'   => 'display_type',
					'dependency'   => array(
						'element' => 'get_listings_by',
						'value'   => array('listing_cat', 'listing_location')
					),
					'value'        => array(
						esc_html__('Listings belong to Locations/Categories', 'listgo') => 'listing',
						esc_html__('Self-Locations/Categories', 'listgo') => 'terms'
					),
					'save_always'  => true
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Header', 'listgo'),
					'description'  => esc_html__('An overlay image will automatically appear if you enter in a text.', 'listgo'),
					'param_name'   => 'header',
					'save_always'  => true,
					'dependency'   => array(
						'element' => 'get_listings_by',
						'value'   => array('upload_images')
					),
					'std'          => ''
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Description', 'listgo'),
					'description'  => esc_html__('An overlay image will automatically appear if you enter in a text.', 'listgo'),
					'param_name'   => 'description',
					'save_always'  => true,
					'dependency'   => array(
						'element' => 'get_listings_by',
						'value'   => array('upload_images')
					),
					'std'          => ''
				),
				array(
					'type'         => 'attach_images',
					'heading'      => esc_html__('Upload Images', 'listgo'),
					'param_name'   => 'upload_images',
					'dependency'   => array(
						'element' => 'get_listings_by',
						'value'   => array('upload_images')
					)
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Number of listings', 'listgo'),
					'description'  => esc_html__('Maximum listings are got in the shortcodes. It should bigger than ItemsPerRow*Columns because We need items for replacing.', 'listgo'),
					'param_name'   => 'number_of_listings',
					'group'        => esc_html__('Configurations', 'listgo'),
					'save_always'  => true,
					'std'          => 30
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Max Step', 'listgo'),
					'description'  => esc_html__('Maximum number of listings that are replaced at the same time. Note that the number of listings should be ', 'listgo'),
					'param_name'   => 'max_step',
					'std'          => 3,
					'group'        => esc_html__('Configurations', 'listgo'),
					'save_always'  => true
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Listing Per Row On Desktop Devices', 'listgo'),
					'param_name'   => 'items_per_row_on_desktop',
					'group'        => esc_html__('Configurations', 'listgo'),
					'save_always'  => true,
					'std'          => 6
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Number Of Rows On Desktop Devices', 'listgo'),
					'param_name'   => 'number_of_rows_on_desktop',
					'group'        => esc_html__('Configurations', 'listgo'),
					'save_always'  => true,
					'std'          => 3
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Listing Per Row On Tablet Devices', 'listgo'),
					'param_name'   => 'items_per_row_on_tablet',
					'group'        => esc_html__('Configurations', 'listgo'),
					'save_always'  => true,
					'std'          => 5
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Number Of Rows On Tablet Devices', 'listgo'),
					'param_name'   => 'number_of_rows_on_tablet',
					'group'        => esc_html__('Configurations', 'listgo'),
					'save_always'  => true,
					'std'          => 3
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Listing Per Row On Mobile Devices', 'listgo'),
					'param_name'   => 'items_per_row_on_mobile',
					'std'          => 3,
					'group'        => esc_html__('Configurations', 'listgo'),
					'save_always'  => true
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Number Of Rows On Mobile Devices', 'listgo'),
					'param_name'   => 'number_of_rows_on_mobile',
					'group'        => esc_html__('Configurations', 'listgo'),
					'save_always'  => true,
					'std'          => 3
				),
				array(
					'type'         => 'dropdown',
					'heading'      => esc_html__('Animate Type', 'listgo'),
					'param_name'   => 'animate_type',
					'group'        => esc_html__('Configurations', 'listgo'),
					'save_always'  => true,
					'std'          => 'random',
					'value'        => array(
						'showHide'          => 'showHide',
						'fadeInOut'         => 'fadeInOut',
						'slideLeft'         => 'slideLeft',
						'slideRight'        => 'slideRight',
						'slideTop'          => 'slideTop',
						'slideBottom'       => 'slideBottom',
						'rotateLeft'        => 'rotateLeft',
						'rotateRight'       => 'rotateRight',
						'rotateTop'         => 'rotateTop',
						'rotateBottom'      => 'rotateBottom',
						'scale'             => 'scale',
						'rotate3d'          => 'rotate3d',
						'rotateLeftScale'   => 'rotateLeftScale',
						'rotateRightScale'  => 'rotateRightScale',
						'rotateTopScale'    => 'rotateTopScale',
						'rotateBottomScale' => 'rotateBottomScale',
						'random'            => 'random'
					)
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Animation Speed', 'listgo'),
					'description'  => esc_html__('Note 1000 means 1s', 'listgo'),
					'param_name'   => 'animation_speed',
					'group'        => esc_html__('Configurations', 'listgo'),
					'save_always'  => true,
					'std'          => 500
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Set Interval', 'listgo'),
					'description'  => esc_html__('The item(s) will be replaced every x seconds. Note 1000 means 1s', 'listgo'),
					'param_name'   => 'interval',
					'group'        => esc_html__('Configurations', 'listgo'),
					'save_always'  => true,
					'std'          => 3000
				)
			)
		),
		// Promotion
		array(
			'name' => esc_html__('Confirmation Page Settings', 'listgo'),
			'base' => 'wiloke_confirmation',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'category' => WILOKE_THEMENAME,
			'params'   => array(
				array(
					'type'         => 'textarea',
					'heading'      => esc_html__('Your account has been activated', 'listgo'),
					'param_name'   => 'valid_code',
					'save_always'  => true,
					'std'          => esc_html__('Congrats! Your account has been activated', 'listgo')
				),
				array(
					'type'         => 'textarea',
					'heading'      => esc_html__('Invalid confirmation code', 'listgo'),
					'param_name'   => 'in_valid_code',
					'save_always'  => true,
					'std'          => esc_html__('Invalid confirmation code. The code may have expired.', 'listgo')
				)
			)
		),
		// Promotion
		array(
			'name' => esc_html__('Promotion', 'listgo'),
			'base' => 'wiloke_promotion',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'category' => WILOKE_THEMENAME,
			'params'   => array(
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Heading', 'listgo'),
					'param_name'   => 'heading',
					'save_always'  => true,
					'std'          => esc_html__('Do you need some books for your traveling?', 'listgo')
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Description', 'listgo'),
					'param_name'   => 'desc',
					'save_always'  => true,
					'std'          => esc_html__('Preparing for your traveling is very important. Our book store has lots of e-books, it might useful for you', 'listgo')
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Button name', 'listgo'),
					'param_name'   => 'btn_name',
					'save_always'  => true,
					'std'          => esc_html__('Go to book store', 'listgo')
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Button Link', 'listgo'),
					'param_name'   => 'btn_link',
					'save_always'  => true,
					'std'          => '#'
				),
				array(
					'type'         => 'dropdown',
					'heading'      => esc_html__('Click action', 'listgo'),
					'description'  => esc_html__('Select an action when user click on this button', 'listgo'),
					'param_name'   => 'type',
					'save_always'  => true,
					'std'          => '_self',
					'value'        => array(
						esc_html__('Refresh this page', 'listgo') => '_self',
						esc_html__('Open a new tab', 'listgo') => '_blank',
					)
				),
				array(
					'type'         => 'colorpicker',
					'heading'      => esc_html__('Heading Color', 'listgo'),
					'param_name'   => 'heading_color',
					'save_always'  => true,
					'std'          => ''
				),
				array(
					'type'         => 'colorpicker',
					'heading'      => esc_html__('Description Color', 'listgo'),
					'param_name'   => 'desc_color',
					'save_always'  => true,
					'std'          => ''
				),
				array(
					'type'         => 'colorpicker',
					'heading'      => esc_html__('Button Background Color', 'listgo'),
					'param_name'   => 'btn_bg_color',
					'save_always'  => true,
					'std'          => ''
				),
				array(
					'type'         => 'colorpicker',
					'heading'      => esc_html__('Button Text Color', 'listgo'),
					'param_name'   => 'btn_color',
					'save_always'  => true,
					'std'          => ''
				),
				array(
					'type'         => 'colorpicker',
					'heading'      => esc_html__('Background Color', 'listgo'),
					'param_name'   => 'bg_color',
					'save_always'  => true,
					'std'          => '#f5af02'
				)
			)
		),
		array(
			'name' => esc_html__('Registration Form', 'listgo'),
			'base' => 'wiloke_registration_form',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'category' => WILOKE_THEMENAME,
			'params'   => array(
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Heading', 'listgo'),
					'param_name'   => 'heading',
					'save_always'  => false,
					'std'          => esc_html__('Register Now', 'listgo')
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Email Label', 'listgo'),
					'param_name'   => 'email_label',
					'save_always'  => false,
					'std'          => esc_html__('Email Address', 'listgo')
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Username Label', 'listgo'),
					'param_name'   => 'username_label',
					'save_always'  => false,
					'std'          => esc_html__('User Name', 'listgo')
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Password Label', 'listgo'),
					'param_name'   => 'password_label',
					'save_always'  => false,
					'std'          => esc_html__('Password', 'listgo')
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Button Text', 'listgo'),
					'param_name'   => 'btn_text',
					'save_always'  => false,
					'std'          => esc_html__('Register', 'listgo')
				)
			)
		),

		// Post Slider
		array(
			'name' => esc_html__('Articles Slider', 'listgo'),
			'base' => 'wiloke_posts_slider',
			'icon' => '',
			'show_settings_on_create' => true,
			'has_autocomplete'	=> true,
			'category' => WILOKE_THEMENAME,
			'controls' => true,
			'params' => array(
				array(
					'heading'       => esc_html__('Post type', 'listgo'),
					'description'   => esc_html__('Where do you want to get from?', 'listgo'),
					'param_name'    => 'post_type',
					'type'          => 'dropdown',
					'std'           => 'post',
					'save_always'   => true,
					'value'         => array(
						esc_html__('Post', 'listgo') => 'post',
						esc_html__('Listing', 'listgo') => 'listing',
						esc_html__('Page', 'listgo') => 'page',
						esc_html__('Specify Listing IDs', 'listgo') => 'include'
					)
				),
				array(
					'type'         	=> 'wiloke_get_list_of_terms',
					'heading'      	=> esc_html__('Pickup categories', 'listgo'),
					'dependency'	=> array('element'=>'post_type', 'value'=>array('post')),
					'param_name'   	=> 'categories',
					'taxonomy'     	=> 'category',
					'is_multiple'  	=> true,
					'save_always'  	=> true
				),
				array(
					'heading'   => esc_html__('Select Post IDs', 'listgo'),
					'description'=> esc_html__('Enter in the title for searching', 'listgo'),
					'type'      => 'textfield',
					'param_name'=> 'include',
					'save_always'=> true,
					'dependency'=> array('element'=>'post_type', 'value'=>array('include'))
				),
				array(
					'heading'   => esc_html__('Order By', 'listgo'),
					'type'      => 'dropdown',
					'param_name'=> 'order_by',
					'save_always'  => true,
					'dependency'=> array('element'=>'post_type', 'value'=>array('post', 'page', 'listing')),
					'value'     => array(
						esc_html__('Post Date', 'listgo')    => 'post_date',
						esc_html__('Post Title', 'listgo')   => 'post_title',
						esc_html__('Post ID', 'listgo')      => 'ID',
						esc_html__('Post Author', 'listgo')  => 'post_author',
						esc_html__('Random', 'listgo')       => 'rand'
					)
				),
				array(
					'heading'   => esc_html__('Number of posts', 'listgo'),
					'description'=> esc_html__('Set maximum posts will be got from source.', 'listgo'),
					'type'      => 'textfield',
					'param_name'=> 'posts_per_page',
					'save_always'  => true,
					'dependency'=> array('element'=>'post_type', 'value'=>array('post', 'page', 'listing')),
					'std'       => 5
				),
				array(
					'heading'   => esc_html__('Show posts', 'listgo'),
					'description'=> esc_html__('How many posts do you want to show at the same time?', 'listgo'),
					'type'      => 'textfield',
					'param_name'=> 'show_posts',
					'save_always'  => true,
					'dependency'=> array('element'=>'post_type', 'value'=>array('post', 'page', 'listing')),
					'std'       => 3
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Image Size', 'listgo'),
					'description'   => esc_html__('Set image size for the feature image. You can use one of the following keywords: large, medium, thumbnail or specify size by following this structure w,h, for example: 1000,400, it means you want to display a featured image of 1000 width x 4000 height.', 'listgo'),
					'param_name'    => 'image_size',
					'std'           => '370,280',
					'save_always'   => true
				)
			)
		),

		// Categories Covers
		array(
			'name' => esc_html__('Categories Cover', 'listgo'),
			'base' => 'wiloke_categories_cover',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'category' => WILOKE_THEMENAME,
			'params'   => array(
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Heading', 'listgo'),
					'param_name'   => 'heading',
					'save_always'  => true
				),
				array(
					'type'         => 'iconpicker',
					'heading'      => esc_html__('Icon', 'listgo'),
					'param_name'   => 'icon',
					'std'          => 'fa fa-cutlery',
					'save_always'  => true
				),
				array(
					'type'         => 'attach_image',
					'heading'      => esc_html__('Heading background', 'listgo'),
					'param_name'   => 'heading_background',
					'save_always'  => true
				),
				array(
					'type'         => 'wiloke_get_list_of_terms',
					'heading'      => esc_html__('Pickup categories', 'listgo'),
					'param_name'   => 'listing_cat',
					'taxonomy'     => 'listing_cat',
					'is_multiple'  => true,
					'save_always'  => true
				),
				array(
					'type'         => 'wiloke_get_list_of_terms',
					'heading'      => esc_html__('Pickup Locations', 'listgo'),
					'param_name'   => 'listing_location',
					'taxonomy'     => 'listing_location',
					'is_multiple'  => true,
					'save_always'  => true
				)
			)
		),

		// Listings Cover
		array(
			'name' => esc_html__('Listings Cover', 'listgo'),
			'base' => 'wiloke_listings_cover',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'category' => WILOKE_THEMENAME,
			'params'   => array(
				array(
					'type'         => 'dropdown',
					'heading'      => esc_html__('Get listings by', 'listgo'),
					'param_name'   => 'get_listings_by',
					'value'        => array(
						esc_html__('Listing Location', 'listgo') => 'listing_location',
						esc_html__('Listing Category', 'listgo') => 'listing_cat'
					),
					'std'          => 'listing_location',
					'save_always'  => true
				),
				array(
					'type'         => 'iconpicker',
					'heading'      => esc_html__('Icon', 'listgo'),
					'param_name'   => 'icon',
					'std'          => 'fa fa-cutlery',
					'save_always'  => true
				),
				array(
					'type'         => 'attach_image',
					'heading'      => esc_html__('Heading background', 'listgo'),
					'param_name'   => 'heading_background',
					'save_always'  => true
				),
				array(
					'type'         => 'wiloke_get_list_of_terms',
					'heading'      => esc_html__('Pickup categories', 'listgo'),
					'param_name'   => 'listing_cat',
					'taxonomy'     => 'listing_cat',
					'dependency'   => array('element'=>'get_listings_by', 'value'=>array('listing_cat')),
					'save_always'  => true
				),
				array(
					'type'         => 'wiloke_get_list_of_terms',
					'heading'      => esc_html__('Pickup Locations', 'listgo'),
					'dependency'   => array('element'=>'get_listings_by', 'value'=>array('listing_location')),
					'param_name'   => 'listing_location',
					'taxonomy'     => 'listing_location',
					'save_always'  => true
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Number of listings', 'listgo'),
					'description'  => esc_html__('Set number of listings will be shown on this place', 'listgo'),
					'param_name'   => 'posts_per_page',
					'std'          => 5,
					'save_always'  => true
				),
				array(
					'type'         => 'dropdown',
					'heading'      => esc_html__('Order By', 'listgo'),
					'param_name'   => 'order_by',
					'std'          => 'post_date',
					'value'        => array(
						esc_html__('Post Date', 'listgo')        => 'post_date',
						esc_html__('Post Title', 'listgo')       => 'post_title',
						esc_html__('Post Author', 'listgo')      => 'post_author',
						esc_html__('Post ID', 'listgo')          => 'ID',
						esc_html__('Comment Count', 'listgo')    => 'comment_count',
						esc_html__('Random', 'listgo')           => 'rand'
					),
					'save_always'  => true
				)
			)
		),

		// Testimonial
		array(
			'name' => esc_html__('Testimonials', 'listgo'),
			'base' => 'wiloke_testimonials',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'category' => WILOKE_THEMENAME,
			
		),

		// event
		array(
			'name' => esc_html__('Events Carousel', 'listgo'),
			'base' => 'wiloke_events',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'category' => WILOKE_THEMENAME,
			'params'   => array(
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Description', 'listgo'),
					'param_name'   => 'description',
					'std'          => esc_html__('Event Information', 'listgo'),
					'save_always'  => false
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Limit character', 'listgo'),
					'description'  => esc_html__('Leave empty to show full post content or set maximum character will be shown.', 'listgo'),
					'param_name'   => 'limit_character',
					'std'          => '',
					'save_always'  => true
				),
				array(
					'type'         => 'textarea',
					'heading'      => esc_html__('View all events description', 'listgo'),
					'param_name'   => 'view_all_events_description',
					'std'          => esc_html__('Click on this link to view all upcoming events and ongoing events', 'listgo'),
					'save_always'  => true
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('View all events Button name', 'listgo'),
					'param_name'   => 'view_all_events_button_name',
					'std'          => esc_html__('View All Events', 'listgo'),
					'save_always'  => true
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('View all events Link', 'listgo'),
					'description'  => esc_html__('You can create an events page by creating a new page and set that page to Events template.', 'listgo'),
					'param_name'   => 'view_all_events_button_link',
					'std'          => '',
					'save_always'  => true
				)
			)
		),

		array(
			'name' => esc_html__('Events List', 'listgo'),
			'base' => 'wiloke_events_list',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'category' => WILOKE_THEMENAME,
			'params'   => array(
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Events Per page', 'listgo'),
					'param_name'   => 'events_per_page',
					'std'          => 10,
					'save_always'  => true
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Excerpt Length', 'listgo'),
					'param_name'   => 'limit_character',
					'std'          => 100,
					'save_always'  => true
				),
				array(
					'type'         => 'textarea',
					'heading'      => esc_html__('Event Description', 'listgo'),
					'param_name'   => 'event_description',
					'std'          => esc_html__('Event Detail', 'listgo'),
					'save_always'  => true
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Thumbnail Size', 'listgo'),
					'param_name'   => 'image_size',
					'std'          => 'wiloke_listgo_740x370',
					'save_always'  => true
				)
			)
		),

		// Pricing Table
		array(
			'name' => esc_html__('Packages', 'listgo'),
			'base' => 'wiloke_pricing',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'category' => WILOKE_THEMENAME,
			'params'   => array(
				array(
					'type'         => 'wiloke_list_of_posts',
					'heading'      => esc_html__('Select Packages', 'listgo'),
					'description'  => esc_html__('From ListGo 1.0.9, You have to go to Wiloke Submission -> Settings -> Customer Plans to add your plans to the pricing page. It means this setting is no longer available.', 'listgo'),
					'param_name'   => 'post_ids',
					'post_type'    => 'pricing',
					'is_multiple'  => true,
					'std'          => '',
				),
				array(
					'type'         => 'dropdown',
					'heading'      => esc_html__('Items per rown', 'listgo'),
					'param_name'   => 'items_per_row',
					'std'          => 'col-md-4',
					'value'        => array(
						esc_html__('2 items per rown', 'listgo') => 'col-md-6',
						esc_html__('3 items per rown', 'listgo') => 'col-md-4',
						esc_html__('4 items per rown', 'listgo') => 'col-md-3'
					)
				),
				array(
					'type'         => 'textfield',
					'heading'      => esc_html__('Check out button', 'listgo'),
					'description'  => esc_html__('Ensure that the check out page is set. If you have not done it yet, Please go to Pricing -> Settings to set check out page.', 'listgo'),
					'param_name'   => 'btn_name',
					'std'          => esc_html__('Get Now', 'listgo'),
					'save_always'  => true
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Image Size', 'listgo'),
					'description'   => esc_html__('Set image size for the feature image. You can use one of the following keywords: large, medium, thumbnail or specify size by following this structure w,h, for example: 1000,400, it means you want to display a featured image of 1000 width x 4000 height.', 'listgo'),
					'param_name'    => 'image_size',
					'std'           => 'medium',
					'save_always'   => true
				),
				array(
					'type'      => 'wiloke_colorpicker',
					'param_name' => 'overlay_color',
					'heading'   => esc_html__('Overlay Color', 'listgo'),
					'std'       => '',
					'save_always'  => true
				)
			)
		),

		// Our Members
		array(
			'name' => esc_html__('Our members', 'listgo'),
			'base' => 'wiloke_our_members',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'category' => WILOKE_THEMENAME,
			'params'   => array(
				array(
					'type'         => 'wiloke_list_of_roles',
					'heading'      => esc_html__('All members who has role are listed below will be shown. Leave empty to show all members.', 'listgo'),
					'param_name'   => 'roles',
					'is_multiple'  => true,
					'std'          => '',
				),
				array(
					'type'         => 'dropdown',
					'heading'      => esc_html__('Members per row', 'listgo'),
					'param_name'   => 'members_per_row',
					'value'        => array(
						esc_html__('2 members / row', 'listgo') => 'col-sm-6',
						esc_html__('3 members / row', 'listgo') => 'col-sm-6 col-lg-4',
						esc_html__('4 members / row', 'listgo') => 'col-sm-6 col-lg-3',
					),
					'std'          => 'col-sm-6 col-lg-4',
				)
			)
		),

		// Our Members
		array(
			'name' => esc_html__('Our team', 'listgo'),
			'base' => 'wiloke_our_team',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'category' => WILOKE_THEMENAME,
			'params'   => array(
				array(
					'type'         => 'textarea_html',
					'heading'      => esc_html__('Let intro about your team', 'listgo'),
					'param_name'   => 'content',
					'std'          => '',
				),
				array(
					'type'         => 'attach_image',
					'heading'      => esc_html__('Cover Image', 'listgo'),
					'param_name'   => 'cover_image',
					'std'          => '',
				),
				array(
					'type'         => 'dropdown',
					'heading'      => esc_html__('Get members by', 'listgo'),
					'group'        => esc_html__('Members', 'listgo'),
					'param_name'   => 'get_members_by',
					'value'        => array(
						esc_html__('Roles', 'listgo')             => 'roles',
						esc_html__('Specify Members', 'listgo')  => 'custom'
					),
					'std'          => 'roles',
				),
				array(
					'type'         => 'wiloke_list_of_roles',
					'heading'      => esc_html__('All members who has role are listed below will be shown. Leave empty to show all members.', 'listgo'),
					'group'        => esc_html__('Members', 'listgo'),
					'param_name'   => 'roles',
					'is_multiple'  => true,
					'dependency'   => array(
						'value'    => array('roles'),
						'element'  => 'get_members_by'
					),
					'std'          => '',
				),
				array(
					'type'         => 'wiloke_list_of_users',
					'heading'      => esc_html__('Select members', 'listgo'),
					'group'        => esc_html__('Members', 'listgo'),
					'param_name'   => 'member_ids',
					'is_multiple'  => true,
					'dependency'   => array(
						'value'    => array('custom'),
						'element'  => 'get_members_by'
					),
					'std'          => '',
				)
			)
		),

		// Box Icon
		array(
			'name' => esc_html__('Content Boxes', 'listgo'),
			'base' => 'wiloke_boxes_icon',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'category' => WILOKE_THEMENAME,
			'params'   => array(

				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Style', 'listgo'),
					'param_name'    => 'style',
					'value'         => array(
						esc_html__('Style 1', 'listgo') => 'style1',
						esc_html__('Style 2', 'listgo') => 'style2',
					),
					'save_always'	=> true,
					'std'           => 'style1',
				),

				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Toggle numerical order list', 'listgo'),
					'param_name'    => 'toggle_numerical_order_list',
					'value'         => array(
						esc_html__('Enable', 'listgo')  => 'enable',
						esc_html__('Disable', 'listgo') => 'disable'
					),
					'std'           => 'enable',
				),
				array(
					'type'      => 'param_group',
					'heading'   => esc_html__('Box Group', 'listgo'),
					'param_name'=> 'boxes',
					'params'    => array(
						array(
							'type'         => 'iconpicker',
							'heading'      => esc_html__('Icon', 'listgo'),
							'param_name'   => 'icon',
							'is_multiple'  => true,
							'std'          => 'iconbox__icon icon_documents_alt',
						),
						array(
							'type'         => 'textfield',
							'heading'      => esc_html__('Heading', 'listgo'),
							'param_name'   => 'heading',
							'value'        => '',
							'std'          => 'Heading',
						),
						array(
							'type'         => 'textarea',
							'heading'      => esc_html__('Description', 'listgo'),
							'description'  => esc_html__('Allow to use the following html tags: &lt;li>, &lt;strong>, &lt;br> &lt;i>, &lt;a>', 'listgo'),
							'param_name'   => 'description',
							'value'        => '',
							'std'          => 'Write something here',
						)
					)
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Items per row', 'listgo'),
					'param_name'    => 'items_per_row',
					'value'         => array(
						esc_html__('2 Items / Row', 'listgo') => 'col-md-6',
						esc_html__('3 Items / Row', 'listgo') => 'col-md-4',
						esc_html__('4 Items / Row', 'listgo') => 'col-md-3'
					),
					'std'           => 'col-md-6',
				),
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Alignment', 'listgo'),
					'param_name'    => 'alighment',
					'value'         => array(
						esc_html__('Center', 'listgo')   => 'text-center',
						esc_html__('Left', 'listgo')     => 'text-left',
						esc_html__('Right', 'listgo')    => 'text-right'
					),
					'std'           => 'text-left',
				)
			)
		),

		// Map
		array(
			'name' => esc_html__('Custom Map', 'listgo'),
			'base' => 'wiloke_map',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'category' => WILOKE_THEMENAME,
			'params'   => array(
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Enter in your Latitude & Longitude (*)', 'listgo'),
					'description'   => Wiloke::wiloke_kses_simple_html(__('For example: 21.027764,105.834160. You can find your Lat&Long here <a href="http://www.latlong.net/" target="_blank">www.latlong.net</a>', 'listgo'), true),
					'param_name'    => 'latlng',
					'value'         => '',
					'std'           => '',
				),
				array(
					'type'          => 'attach_image',
					'heading'       => esc_html__('Map marker', 'listgo'),
					'param_name'    => 'marker',
					'value'         => '',
					'std'           => '',
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Map Info', 'listgo'),
					'param_name'    => 'info',
					'value'         => '',
					'std'           => '',
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Map Height', 'listgo'),
					'param_name'    => 'height',
					'value'         => '',
					'std'           => '',
				)
			)
		),

		array(
			'name' => esc_html__('Mega Menu - List Listings', 'listgo'),
			'base' => 'wiloke_list_of_listings_on_mega_menu',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'has_autocomplete'	=> true,
			'category' => 'Wiloke Mega Menu',
			'params'   => array(
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Get Listings by', 'listgo'),
					'param_name'    => 'get_posts_by',
					'value'         => array(
						esc_html__('Latest listings', 'listgo')            => 'latest_posts',
						esc_html__('Top Rated', 'listgo')                  => 'top_rated',
						esc_html__('Specify listings', 'listgo')           => 'custom',
						esc_html__('Specify listing categories', 'listgo') => 'listing_cat',
						esc_html__('Specify listing locations', 'listgo')  => 'listing_location'
					),
					'std'           => 'latest_posts',
				),
				array(
					'type'          => 'wiloke_get_list_of_terms',
					'heading'       => esc_html__('Pickup Listing Categories', 'listgo'),
					'taxonomy'      => 'listing_cat',
					'is_multiple'   => true,
					'description'   => esc_html__('Choosing the categories where posts will be get from.', 'listgo'),
					'param_name'    => 'listing_cat',
					'save_always'   => true,
					'std'           => '',
					'dependency'    => array('element'=>'get_posts_by', 'value'=>array('listing_cat'))
				),
				array(
					'type'          => 'wiloke_get_list_of_terms',
					'heading'       => esc_html__('Pickup Listing Locations', 'listgo'),
					'taxonomy'      => 'listing_location',
					'is_multiple'   => true,
					'description'   => esc_html__('Choosing the categories where posts will be get from.', 'listgo'),
					'param_name'    => 'listing_location',
					'save_always'   => true,
					'std'           => '',
					'dependency'    => array('element'=>'get_posts_by', 'value'=>array('listing_location'))
				),
				array(
					'type'        => 'autocomplete',
					'heading'     => esc_html__('Specify Listings', 'listgo'),
					'param_name'  => 'include',
					'settings'    => array(
						'multiple' => true,
						'sortable' => true,
						'groups'   => true,
					),
					'admin_label'   => true,
					'dependency'    => array('element' => 'get_posts_by', 'value'   => array('custom')),
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Number of listings', 'listgo'),
					'param_name'    => 'number_of_listings',
					'admin_label'   => true,
					'std'           => 4,
					'dependency'    => array('element' => 'get_posts_by', 'value'   => array('listing_location', 'listing_cat', 'latest_posts', 'top_rated')),
				),
				array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Display Style', 'listgo'),
                    'param_name'  => 'display',
                    'save_always'	=> true,
                    'value'       => array(
                        esc_html__('Simple List', 'listgo') => 'simple',
                        esc_html__('Grid', 'listgo') => 'grid',
                        esc_html__('Slider', 'listgo')  => 'slider',
                    ),
                    'description' => esc_html__('Display style grid or slider. Default set grid.', 'listgo'),
                ),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Image Size', 'listgo'),
					'is_multiple'   => true,
					'description'   => esc_html__('Enter thumbnail, medium, wiloke_listgo_370x370, full keyword or specify the width and height like this: 500,500', 'listgo'),
					'param_name'    => 'image_size',
					'save_always'   => true,
					'std'           => 'medium'
				),
                array(
                    'type'       => 'checkbox',
                    'heading'    => esc_html__('Nav', 'listgo'),
                    'param_name' => 'nav',
                    'value'      => array(
                        esc_html__('Show next/prev buttons.', 'listgo') => 'yes',
                    ),
                    'dependency'    => array(
                        'element'   => 'display',
                        'value'     => array('slider')
                    )
                ),

                array(
                    'type'       => 'checkbox',
                    'heading'    => esc_html__('Dots', 'listgo'),
                    'param_name' => 'dots',
                    'value'      => array(
                        esc_html__('Show dots navigation.', 'listgo') => 'yes',
                    ),
                    'dependency'    => array(
                        'element'   => 'display',
                        'value'     => array('slider')
                    )
                ),

                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Items per row (≥1200px)', 'listgo'),
                    'param_name'  => 'xl_per_row',
                    'dependency'    => array(
	                    'element'   => 'display',
	                    'value'     => array('slider', 'grid')
                    ),
                    'std'         => 4,
                    'value'       => array(
                        esc_html__('1', 'listgo')  => 1,
                        esc_html__('2', 'listgo')  => 2,
                        esc_html__('3', 'listgo')  => 3,
                        esc_html__('4', 'listgo')  => 4,
                        esc_html__('5', 'listgo')  => 5,
                        esc_html__('6', 'listgo')  => 6,
                        esc_html__('7', 'listgo')  => 7,
                        esc_html__('8', 'listgo')  => 8,
                        esc_html__('9', 'listgo')  => 9,
                        esc_html__('10', 'listgo') => 10,
                    )
                ),

                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Items per row (≥992px)', 'listgo'),
                    'param_name'  => 'lg_per_row',
                    'dependency'    => array(
	                    'element'   => 'display',
	                    'value'     => array('slider', 'grid')
                    ),
                    'std'         => 4,
                    'value'       => array(
                        esc_html__('1', 'listgo')  => 1,
                        esc_html__('2', 'listgo')  => 2,
                        esc_html__('3', 'listgo')  => 3,
                        esc_html__('4', 'listgo')  => 4,
                        esc_html__('5', 'listgo')  => 5,
                        esc_html__('6', 'listgo')  => 6,
                        esc_html__('7', 'listgo')  => 7,
                        esc_html__('8', 'listgo')  => 8,
                        esc_html__('9', 'listgo')  => 9,
                        esc_html__('10', 'listgo') => 10,
                    )
                ),

                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Items per row (≥768px)', 'listgo'),
                    'param_name'  => 'md_per_row',
                    'dependency'    => array(
	                    'element'   => 'display',
	                    'value'     => array('slider', 'grid')
                    ),
                    'std'         => 3,
                    'value'       => array(
                        esc_html__('1', 'listgo')  => 1,
                        esc_html__('2', 'listgo')  => 2,
                        esc_html__('3', 'listgo')  => 3,
                        esc_html__('4', 'listgo')  => 4,
                        esc_html__('5', 'listgo')  => 5,
                        esc_html__('6', 'listgo')  => 6,
                        esc_html__('7', 'listgo')  => 7,
                        esc_html__('8', 'listgo')  => 8,
                        esc_html__('9', 'listgo')  => 9,
                        esc_html__('10', 'listgo') => 10,
                    )
                ),

                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Items per row (≥576px)', 'listgo'),
                    'dependency'    => array(
	                    'element'   => 'display',
	                    'value'     => array('slider', 'grid')
                    ),
                    'param_name'  => 'sm_per_row',
                    'std'         => 2,
                    'value'       => array(
                        esc_html__('1', 'listgo')  => 1,
                        esc_html__('2', 'listgo')  => 2,
                        esc_html__('3', 'listgo')  => 3,
                        esc_html__('4', 'listgo')  => 4,
                        esc_html__('5', 'listgo')  => 5,
                        esc_html__('6', 'listgo')  => 6,
                        esc_html__('7', 'listgo')  => 7,
                        esc_html__('8', 'listgo')  => 8,
                        esc_html__('9', 'listgo')  => 9,
                        esc_html__('10', 'listgo') => 10,
                    )
                ),

                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Items per row (<576px)', 'listgo'),
                    'dependency'    => array(
	                    'element'   => 'display',
	                    'value'     => array('slider', 'grid')
                    ),
                    'param_name'  => 'xs_per_row',
                    'std'         => 1,
                    'value'       => array(
                        esc_html__('1', 'listgo')  => 1,
                        esc_html__('2', 'listgo')  => 2,
                        esc_html__('3', 'listgo')  => 3,
                        esc_html__('4', 'listgo')  => 4,
                        esc_html__('5', 'listgo')  => 5,
                        esc_html__('6', 'listgo')  => 6,
                        esc_html__('7', 'listgo')  => 7,
                        esc_html__('8', 'listgo')  => 8,
                        esc_html__('9', 'listgo')  => 9,
                        esc_html__('10', 'listgo') => 10,
                    )
                ),

                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Space', 'listgo'),
                    'dependency'    => array(
	                    'element'   => 'display',
	                    'value'     => array('slider', 'grid')
                    ),
                    'param_name'  => 'space',
                    'std'         => 20,
                    'value'       => array(
                        esc_html__('5', 'listgo')  => 5,
                        esc_html__('10', 'listgo') => 10,
                        esc_html__('15', 'listgo') => 15,
                        esc_html__('20', 'listgo') => 20,
                        esc_html__('25', 'listgo') => 25,
                        esc_html__('30', 'listgo') => 30,
                        esc_html__('35', 'listgo') => 35,
                        esc_html__('40', 'listgo') => 40,
                        esc_html__('45', 'listgo') => 45,
                        esc_html__('50', 'listgo') => 50,
                    ),
                    'description' => esc_html__('Set space for items.', 'listgo'),
                ),
			)
		),

		array(
			'name' => esc_html__('Mega Menu - Listing Locations+Categories', 'listgo'),
			'base' => 'wiloke_list_of_terms_on_mega_menu',
			'icon' => '',
			'show_settings_on_create' => true,
			'controls' => true,
			'category' => 'Wiloke Mega Menu',
			'params'   => array(
				array(
					'type'          => 'dropdown',
					'heading'       => esc_html__('Get taxonomy by', 'listgo'),
					'param_name'    => 'taxonomy',
					'value'         => array(
						esc_html__('Specify listing categories', 'listgo') => 'listing_cat',
						esc_html__('Specify listing locations', 'listgo')  => 'listing_location'
					),
					'std'           => 'listing_cat',
				),
				array(
					'type'          => 'wiloke_get_list_of_terms',
					'heading'       => esc_html__('Pickup Listing Categories', 'listgo'),
					'taxonomy'      => 'listing_cat',
					'is_multiple'   => true,
					'dependency'	=> array('element'=>'taxonomy', 'value'=>array('listing_cat')),
					'description'   => esc_html__('Choosing the categories where posts will be get from.', 'listgo'),
					'param_name'    => 'listing_cat',
					'save_always'   => true,
					'std'           => ''
				),
				array(
					'type'          => 'wiloke_get_list_of_terms',
					'heading'       => esc_html__('Pickup Listing Locations', 'listgo'),
					'taxonomy'      => 'listing_location',
					'is_multiple'   => true,
					'dependency'	=> array('element'=>'taxonomy', 'value'=>array('listing_location')),
					'description'   => esc_html__('Choosing the categories where posts will be get from.', 'listgo'),
					'param_name'    => 'listing_location',
					'save_always'   => true,
					'std'           => ''
				),
				array(
					'type'          => 'textfield',
					'heading'       => esc_html__('Image Size', 'listgo'),
					'is_multiple'   => true,
					'description'   => esc_html__('Enter thumbnail, medium, wiloke_listgo_370x370, full keyword or specify the width and height like this: 500,500', 'listgo'),
					'param_name'    => 'image_size',
					'save_always'   => true,
					'std'           => 'wiloke_listgo_370x370'
				),
				array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Display', 'listgo'),
                    'param_name'  => 'display',
                    'value'       => array(
                        esc_html__('Grid', 'listgo') => 'grid',
                        esc_html__('Slider', 'listgo')  => 'slider',
                    ),
                    'description' => esc_html__('Display style grid or slider. Default set grid.', 'listgo'),
                ),

                array(
                    'type'       => 'checkbox',
                    'heading'    => esc_html__('Nav', 'listgo'),
                    'param_name' => 'nav',
                    'value'      => array(
                        esc_html__('Show next/prev buttons.', 'listgo') => 'yes',
                    ),
                    'dependency'    => array(
                        'element'   => 'display',
                        'value'     => array('slider')
                    )
                ),

                array(
                    'type'       => 'checkbox',
                    'heading'    => esc_html__('Dots', 'listgo'),
                    'param_name' => 'dots',
                    'value'      => array(
                        esc_html__('Show dots navigation.', 'listgo') => 'yes',
                    ),
                    'dependency'    => array(
                        'element'   => 'display',
                        'value'     => array('slider')
                    )
                ),

                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Items per row (≥1200px)', 'listgo'),
                    'param_name'  => 'xl_per_row',
                    'std'         => 4,
                    'value'       => array(
                        esc_html__('1', 'listgo')  => 1,
                        esc_html__('2', 'listgo')  => 2,
                        esc_html__('3', 'listgo')  => 3,
                        esc_html__('4', 'listgo')  => 4,
                        esc_html__('5', 'listgo')  => 5,
                        esc_html__('6', 'listgo')  => 6,
                        esc_html__('7', 'listgo')  => 7,
                        esc_html__('8', 'listgo')  => 8,
                        esc_html__('9', 'listgo')  => 9,
                        esc_html__('10', 'listgo') => 10,
                    )
                ),

                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Items per row (≥992px)', 'listgo'),
                    'param_name'  => 'lg_per_row',
                    'std'         => 4,
                    'value'       => array(
                        esc_html__('1', 'listgo')  => 1,
                        esc_html__('2', 'listgo')  => 2,
                        esc_html__('3', 'listgo')  => 3,
                        esc_html__('4', 'listgo')  => 4,
                        esc_html__('5', 'listgo')  => 5,
                        esc_html__('6', 'listgo')  => 6,
                        esc_html__('7', 'listgo')  => 7,
                        esc_html__('8', 'listgo')  => 8,
                        esc_html__('9', 'listgo')  => 9,
                        esc_html__('10', 'listgo') => 10,
                    )
                ),

                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Items per row (≥768px)', 'listgo'),
                    'param_name'  => 'md_per_row',
                    'std'         => 3,
                    'value'       => array(
                        esc_html__('1', 'listgo')  => 1,
                        esc_html__('2', 'listgo')  => 2,
                        esc_html__('3', 'listgo')  => 3,
                        esc_html__('4', 'listgo')  => 4,
                        esc_html__('5', 'listgo')  => 5,
                        esc_html__('6', 'listgo')  => 6,
                        esc_html__('7', 'listgo')  => 7,
                        esc_html__('8', 'listgo')  => 8,
                        esc_html__('9', 'listgo')  => 9,
                        esc_html__('10', 'listgo') => 10,
                    )
                ),

                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Items per row (≥576px)', 'listgo'),
                    'param_name'  => 'sm_per_row',
                    'std'         => 2,
                    'value'       => array(
                        esc_html__('1', 'listgo')  => 1,
                        esc_html__('2', 'listgo')  => 2,
                        esc_html__('3', 'listgo')  => 3,
                        esc_html__('4', 'listgo')  => 4,
                        esc_html__('5', 'listgo')  => 5,
                        esc_html__('6', 'listgo')  => 6,
                        esc_html__('7', 'listgo')  => 7,
                        esc_html__('8', 'listgo')  => 8,
                        esc_html__('9', 'listgo')  => 9,
                        esc_html__('10', 'listgo') => 10,
                    )
                ),

                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Items per row (<576px)', 'listgo'),
                    'param_name'  => 'xs_per_row',
                    'std'         => 1,
                    'value'       => array(
                        esc_html__('1', 'listgo')  => 1,
                        esc_html__('2', 'listgo')  => 2,
                        esc_html__('3', 'listgo')  => 3,
                        esc_html__('4', 'listgo')  => 4,
                        esc_html__('5', 'listgo')  => 5,
                        esc_html__('6', 'listgo')  => 6,
                        esc_html__('7', 'listgo')  => 7,
                        esc_html__('8', 'listgo')  => 8,
                        esc_html__('9', 'listgo')  => 9,
                        esc_html__('10', 'listgo') => 10,
                    )
                ),

                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__('Space', 'listgo'),
                    'param_name'  => 'space',
                    'std'         => 20,
                    'value'       => array(
                        esc_html__('5', 'listgo')  => 5,
                        esc_html__('10', 'listgo') => 10,
                        esc_html__('15', 'listgo') => 15,
                        esc_html__('20', 'listgo') => 20,
                        esc_html__('25', 'listgo') => 25,
                        esc_html__('30', 'listgo') => 30,
                        esc_html__('35', 'listgo') => 35,
                        esc_html__('40', 'listgo') => 40,
                        esc_html__('45', 'listgo') => 45,
                        esc_html__('50', 'listgo') => 50,
                    ),
                    'description' => esc_html__('Set space for items.', 'listgo'),
                ),
			)
		)
	)
);
