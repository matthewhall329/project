<?php
$aConfigureMailchimp = array();

return array(
    'menu_name' => 'Theme Options',
    'menu_slug' => 'wiloke',
    'redux'     => array(
        'args'      => array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'             => 'wiloke_options',
                // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'         => 'wiloke',
                // Name that appears at the top of your panel
                'display_version'      => WILOKE_THEMEVERSION,
                // Version that appears at the top of your panel
                'menu_type'            => 'submenu',
                //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'       => false,
                // Show the sections below the admin menu item or not
                'menu_title'           =>  'Theme Options',
                'page_title'           =>  'Theme Options',
                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key'       => '',
                // Set it you want google fonts to update weekly. A google_api_key value is required.
                'google_update_weekly' => false,
                // Must be defined to add google fonts to the typography module
                'async_typography'     => true,
                // Use a asynchronous font on the front end or font string
                //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
                'admin_bar'            => true,
                // Show the panel pages on the admin bar
                'admin_bar_icon'     => 'dashicons-portfolio',
                // Choose an icon for the admin bar menu
                'admin_bar_priority' => 50,
                // Choose an priority for the admin bar menu
                'global_variable'      => '',
                // Set a different name for your global variable other than the opt_name
                'dev_mode'             => WP_DEBUG ? true : false,
                // Show the time the page took to load, etc
                'update_notice'        => true,
                // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
                'customizer'           => false,
                // Enable basic customizer support
                //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                // OPTIONAL -> Give you extra features
                'page_priority'        => null,
                // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'          => 'themes.php',
                // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'     => 'manage_options',
                // Permissions needed to access the options panel.
                'menu_icon'            => '',
                // Specify a custom URL to an icon
                'last_tab'             => '',
                // Force your panel to always open to a specific tab (by id)
                'page_icon'            => 'icon-themes',
                // Icon displayed in the admin panel next to your menu_title
                'page_slug'            => '',
                // Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided
                'save_defaults'        => true,
                // On load save the defaults to DB before user clicks save or not
                'default_show'         => false,
                // If true, shows the default value next to each field that is not the default value.
                'default_mark'         => '',
                // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export'   => true,
                // Shows the Import/Export panel when not used as a field.

                // CAREFUL -> These options are for advanced use only
                'transient_time'       => 60 * MINUTE_IN_SECONDS,
                'output'               => true,
                // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'           => true,
                // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'             => '',
                // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info'          => false,
                // REMOVE

                // HINTS
                'hints'                => array(
                    'icon'          => 'el el-question-sign',
                    'icon_position' => 'right',
                    'icon_color'    => 'lightgray',
                    'icon_size'     => 'normal',
                    'tip_style'     => array(
                        'color'   => 'light',
                        'shadow'  => true,
                        'rounded' => false,
                        'style'   => '',
                    ),
                    'tip_position'  => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect'    => array(
                        'show' => array(
                            'effect'   => 'slide',
                            'duration' => '500',
                            'event'    => 'mouseover',
                        ),
                        'hide' => array(
                            'effect'   => 'slide',
                            'duration' => '500',
                            'event'    => 'click mouseleave',
                        ),
                    ),
                )
        ),
        'sections'  => array(
            // General Settings
            array(
                'title'            => 'General',
                'id'               => 'general_settings',
                'subsection'       => false,
                'customizer_width' => '500px',
                'pages'            => array('page'),
                'fields'           => array(
                    array(
                        'id'       => 'general_logo',
                        'type'     => 'media',
                        'title'    =>  'Logo',
                        'subtitle' =>  'Upload a logo for this site. This logo will be displayed at the top right of header.',
                        'default'  => array(
                            'url'  => WILOKE_THEME_URI . 'img/logo.png'
                        )
                    ),
                    array(
                        'id'       => 'general_retina_logo',
                        'type'     => 'media',
                        'title'    =>  'Retina Logo',
                        'subtitle' =>  'Upload a logo for retina-display devices.',
                        'default'  => ''
                    ),
                    array(
                        'id'       => 'general_favicon',
                        'type'     => 'media',
                        'title'    =>  'Upload favicon',
                        'default'  => ''
                    ),
	                array(
		                'id'       => 'general_menu_mobile_at',
		                'type'     => 'text',
		                'title'    =>  'Mobile Menu',
		                'subtitle' =>  'The menu will automatically switch to Mobile style if the screen is smaller  than or enqual to x px',
		                'default'  => 1024
	                ),
	                array(
                        'id'       => 'is_preloader',
                        'type'     => 'select',
                        'title'    =>  'Enable Pre-loader',
                        'default'  => 'no',
                        'options'  => array(
                            'yes' => 'Yes',
                            'no'  => 'Thanks, but no thanks',
                        )
                    ),
	                array(
		                'type'         => 'text',
		                'id'           => 'general_content_limit',
		                'title'        => 'Excerpt Length',
		                'default'      => 115
	                ),
	                array(
		                'type'         => 'media',
		                'id'           => 'general_pagenotfound_bg',
		                'title'        => '404 Background Image',
		                'default'      => ''
	                ),
                    array(
                        'id'    => 'open_map_settings_section',
                        'type'  => 'section',
                        'indent'=> true,
                        'title' => 'Map General Settings',
	                    'subtitle' => 'If you are using any cache plugin, You need to flush cache after the settings is changed.',
                    ),
                    array(
                        'type'         => 'text',
                        'id'           => 'general_map_api',
                        'title'        => 'Google Map API (*)',
                        'subtitle'     => 'It is required if you wanna use Google MAP. Please go to this link to generate your <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Google API key</a>. Once the Google Map is activated, make sure that the following things are enabled <a href="https://landing.wiloke.com/listgo/wiloke-guide/enable-google-api.png"></a>',
                        'default'      => ''
                    ),
	                array(
		                'type'         => 'text',
		                'id'           => 'general_mapbox_api',
		                'title'        => 'MapBox Token (*)',
		                'subtitle'     => 'It is required if you wanna use Map Shortcode. Please go to this link to generate your <a href="https://www.mapbox.com/studio/account/tokens" target="_blank">Mapbox Token</a>',
		                'default'      => ''
	                ),
	                array(
		                'type'         => 'text',
		                'id'           => 'general_map_theme',
		                'title'        => 'MapBox Theme (*)',
		                'subtitle'     => 'This setting is required. Note that you have to use LEFTLEFT theme instead of the default Mapbox theme. Please click on Wiloke Guide -> FAQs -> How to create a map page? -> Setup Mapbox theme to know more.',
		                'default'      => ''
	                ),
	                array(
		                'id'    => 'close_map_general_settings_section',
		                'type'  => 'section',
		                'indent'=> false
	                ),
	                array(
		                'id'    => 'open_map_template_section',
		                'type'  => 'section',
		                'indent'=> true,
		                'title' => 'Map Template Settings and Map Shortcode Settings',
	                ),
                    array(
                        'type'         => 'text',
                        'id'           => 'general_map_max_zoom',
                        'title'        => 'Map Maximum Zoom (Desktop)',
                        'default'      => 4
                    ),
                    array(
                        'type'         => 'text',
                        'id'           => 'general_map_min_zoom',
                        'title'        => 'Map Minimum Zoom (Desktop)',
                        'desc'         => 'A negative number is allowable',
                        'default'      => -1
                    ),
	                array(
		                'type'         => 'text',
		                'id'           => 'general_map_center_zoom',
		                'title'        => 'Map Center Zoom',
		                'default'      => 10
	                ),
                    array(
                        'type'         => 'text',
                        'id'           => 'general_map_cluster_radius',
                        'title'        => 'Map Cluster Radius',
                        'subtitle'     => 'The maximum radius that a cluster will cover from the central marker',
                        'default'      => 60
                    ),
                    array(
                        'id'    => 'close_map_template_section',
                        'type'  => 'section',
                        'indent'=> false
                    ),
	                array(
		                'id'    => 'open_map_single_settings',
		                'type'  => 'section',
		                'indent'=> true,
		                'title' => 'Map on Single Listing settings',
	                ),
	                array(
		                'type'         => 'text',
		                'id'           => 'general_map_single_max_zoom',
		                'title'        => 'Map Maximum Zoom (Desktop)',
		                'default'      => 10
	                ),
	                array(
		                'type'         => 'text',
		                'id'           => 'general_map_single_min_zoom',
		                'title'        => 'Map Minimum Zoom (Desktop)',
		                'desc'         => 'A negative number is allowable',
		                'default'      => -1
	                ),
	                array(
		                'type'         => 'text',
		                'id'           => 'general_map_single_center_zoom',
		                'title'        => 'Map Center Zoom',
		                'default'      => 5
	                ),
	                array(
		                'id'    => 'close_map_single_settings',
		                'type'  => 'section',
		                'indent'=> false
	                ),
                )
            ),

            array(
	            'title'            => 'Header',
	            'id'               => 'header_settings',
	            'subsection'       => false,
	            'customizer_width' => '500px',
	            'icon'             => 'dashicons dashicons-align-right',
	            'fields'           => array(
		            array(
			            'id'    => 'open_header_image_section',
			            'type'  => 'section',
			            'indent'=> true,
			            'title' => 'Header Image',
		            ),
		            array(
			            'id'        => 'header_cover_image',
			            'type'      => 'media',
			            'title'     => 'Cover Image',
			            'description'  => 'Set the default image to Profile page',
			            'default'   => ''
		            ),
		            array(
			            'id'        => 'blog_header_image',
			            'type'      => 'media',
			            'title'     => 'Blog Page',
			            'description'  => 'This image will be used on blog page, post page, category page and archive page. But if a post / a category / a tag is EXISTING a Featured Image, that image will be used instead. In other word, You can override this image.',
			            'default'   => ''
		            ),
		            array(
			            'id'        => 'blog_header_overlay',
			            'type'      => 'color_rgba',
			            'title'     => 'Blog Overlay Color',
			            'default'   => ''
		            ),
		            array(
			            'id'        => 'listing_header_image',
			            'type'      => 'media',
			            'title'     => 'Listing Page',
			            'description'  => 'If the featured image is empty, this image will be used.',
			            'default'   => ''
		            ),
		            array(
			            'id'        => 'listing_header_overlay',
			            'type'      => 'color_rgba',
			            'title'     => 'Listing Overlay Color',
			            'default'   => ''
		            ),
		            array(
			            'id'        => 'woocommerce_header_image',
			            'type'      => 'media',
			            'title'     => 'WooCommerce Page',
			            'description'  => 'This image will be used on wooocommerce pages',
			            'default'   => ''
		            ),
		            array(
			            'id'        => 'woocommerce_header_overlay',
			            'type'      => 'color_rgba',
			            'title'     => 'WooCommerce Overlay Color',
			            'default'   => ''
		            ),
		            array(
			            'id'    => 'close_header_image_section',
			            'type'  => 'section',
			            'indent'=> false
		            ),
		            array(
			            'id'        => 'header_nav_style',
			            'type'      => 'select',
			            'title'     => 'Navigation Background Color',
			            'description' => 'Note that some special pages such as Listing Creative Layout always keep Transparent Background.',
			            'options'     => array(
			            	'header--transparent' => 'Transparent',
			            	'header--background'  => 'Black',
			            	'header--custombg'    => 'Custom Background',
			            ),
			            'default'   => 'header--background'
		            ),
		            array(
			            'id'        => 'header_custom_nav_bg',
			            'type'      => 'color_rgba',
			            'required'   => array('header_nav_style', '=', 'header--custombg'),
			            'title'     => 'Custom Background Color',
			            'default'   => ''
		            ),
		            array(
			            'id'        => 'header_custom_nav_color',
			            'type'      => 'color_rgba',
			            'required'   => array('header_nav_style', '=', 'header--custombg'),
			            'title'     => 'Custom Menu Item Color',
			            'default'   => ''
		            ),
	            )
            ),

            array(
	            'title'            => 'Facebook Settings',
	            'id'               => 'facebook_settings',
	            'subsection'       => false,
	            'customizer_width' => '500px',
	            'icon'             => 'dashicons dashicons-facebook-alt',
	            'fields'           => array(
		            array(
			            'id'    => 'fb_customchat_section',
			            'type'  => 'section',
			            'indent'=> true,
			            'title' => 'Facebook Chat',
		            ),
		            array(
			            'id'        => 'fb_app_api_id',
			            'type'      => 'text',
			            'title'     => 'FB API ID *',
			            'description'=> '<a href="https://blog.wiloke.com/setup-facebook-chat-listgo/" target="_blank">How to setup Facebook Chat?</a>',
			            'default'   => ''
		            ),
		            array(
			            'id'        => 'fb_app_panpage_id',
			            'type'      => 'text',
			            'title'     => 'FB Fanpage ID *',
			            'default'   => ''
		            ),
		            array(
			            'id'    => 'fb_close_customchat_section',
			            'type'  => 'section',
			            'indent'=> false
		            )
	            )
            ),

            // Login Register Review
	        array(
		        'title'            => 'Sign Up, Sign in, Review',
		        'id'               => 'sign_up_sign_in_review',
		        'subsection'       => false,
		        'customizer_width' => '500px',
		        'icon'             => 'dashicons dashicons-smiley',
		        'fields'           => array(
			        array(
				        'id'        => 'toggle_user_send_user_activation_key',
				        'type'      => 'select',
				        'title'     => 'Toggle Send User Confirmation Key',
				        'description' => 'After user registered an account on your site, he/she should receive a registration confirmation email.',
				        'options'   => array(
					        'disable'  => 'Disable',
					        'enable'   => 'Enable',
				        ),
				        'default'   => 'disable'
			        ),
			        array(
				        'id'        => 'confirmation_page',
				        'type'      => 'select',
				        'data'      => 'page',
				        'required'  => array('toggle_user_send_user_activation_key', '=', 'enable'),
				        'args'      => array(
					        'posts_per_page'    => -1,
					        'meta_key'          => '_wp_page_template',
					        'meta_value'        => 'templates/homepage.php',
					        'post_status'       => 'publish'
				        ),
				        'title'       => 'Confirmation Page',
				        'description' => '<a href="https://www.youtube.com/watch?v=wscAHp-r35c" target="_blank">How to setup Confirmation page?</a>'
			        ),
			        array(
				        'id'        => 'sign_in_desc',
				        'type'      => 'textarea',
				        'title'     => 'Sign Up Description',
				        'default'   => 'By creating an account you agree to our <a href="#">Terms and Conditions</a> and our <a href="#">Privacy Policy</a>',
			        ),
			        array(
				        'id'        => 'toggle_google_recaptcha',
				        'type'      => 'select',
				        'title'     => 'Toggle Google reCAPTCHA',
				        'options'   => array(
					        'disable'  => 'Disable',
					        'enable'   => 'Enable',
				        ),
				        'default'   => 'disable'
			        ),
			        array(
				        'id'        => 'google_recaptcha_site_key',
				        'type'      => 'text',
				        'required'  => array('toggle_google_recaptcha', '=', 'enable'),
				        'title'     => 'Google reCAPTCHA - Site Key',
				        'description'=> '<a href="https://blog.wiloke.com/get-google-recaptcha-keys/" target="_blank">How to get Google reCAPTCHA keys?</a>',
				        'default'   => ''
			        ),
			        array(
				        'id'        => 'google_recaptcha_site_secret',
				        'type'      => 'text',
				        'required'  => array('toggle_google_recaptcha', '=', 'enable'),
				        'title'     => 'Google reCAPTCHA - Secret Key',
				        'description'=> '<a href="https://blog.wiloke.com/get-google-recaptcha-keys/" target="_blank">How to get Google reCAPTCHA keys?</a>',
				        'default'   => ''
			        ),
			        array(
				        'id'        => 'toggle_approved_review_immediately',
				        'type'      => 'select',
				        'title'     => 'Before a review appears',
				        'options'   => array(
					        'enable'  => 'The review will be approved immediately',
					        'disable' => 'The review must be manually approved',
				        ),
			        ),
		        )
	        ),
            // Sidebar
            array(
                'title'            => 'Sidebar',
                'id'               => 'sidebar_settings',
                'subsection'       => false,
                'customizer_width' => '500px',
                'icon'             => 'dashicons dashicons-align-right',
                'fields'           => array(
                    array(
                        'id'        => 'blog_sidebar',
                        'type'      => 'select',
                        'options'   => array(
                            'left'  => 'Left Sidebar',
                            'right' => 'Right Sidebar',
                            'no'    => 'No Sidebar',
                        ),
                        'default'   => 'right',
                        'title'     => 'Blog Sidebar',
                    ),
	                array(
		                'id'        => 'blog_sidebar_style',
		                'type'      => 'select',
		                'options'   => array(
			                'sidebar'            => 'All widgets in a box',
			                'sidebar-background' => 'Each item is separated by a box',
		                ),
		                'default'   => 'sidebar',
		                'title'     => 'Blog Sidebar style ',
		                'description'=> 'This setting will be used on the category page, tag page, post page',
	                ),
                    array(
                        'id'        => 'page_sidebar',
                        'type'      => 'select',
                        'options'   => array(
                            'left'  => 'Left Sidebar',
                            'right' => 'Right Sidebar',
                            'no'    => 'No Sidebar',
                        ),
                        'default'   => 'no',
                        'title'     => 'Page Sidebar',
                    ),
                    array(
                        'id'        => 'archive_search_sidebar',
                        'type'      => 'select',
                        'options'   => array(
                            'left'  => 'Left Sidebar',
                            'right' => 'Right Sidebar',
                            'no'    => 'No Sidebar',
                        ),
                        'default'   => 'right',
                        'title'     => 'Archive, Home, Search ',
                    ),
	                array(
		                'id'        => 'listing_location_category_sidebar',
		                'type'      => 'select',
		                'options'   => array(
			                'left'  => 'Left Sidebar',
			                'right' => 'Right Sidebar',
			                'no'    => 'No Sidebar',
		                ),
		                'default'   => 'right',
		                'title'     => 'Listing Location Sidebar & Listing Category Sidebar ',
	                ),
	                array(
		                'id'        => 'listing_sidebar_position',
		                'type'      => 'select',
		                'options'   => array(
			                'left'  => 'Left Sidebar',
			                'right' => 'Right Sidebar',
			                'no'    => 'No Sidebar',
		                ),
		                'default'   => 'leff',
		                'title'     => 'Listing Sidebar Position ',
		                'description'=> 'To set the listing sidebar, please go to Appearance -> Widgets -> Dragging widget items into Listing Sidebar area.',
	                ),
	                array(
		                'id'        => 'events_sidebar_position',
		                'type'      => 'select',
		                'options'   => array(
			                'left'  => 'Left Sidebar',
			                'right' => 'Right Sidebar',
			                'no'    => 'No Sidebar',
		                ),
		                'default'   => 'right',
		                'title'     => 'Event Sidebar',
	                ),
	                array(
                        'id'        => 'woocommerce_sidebar',
                        'type'      => 'select',
                        'options'   => array(
                            'left'  => 'Left Sidebar',
                            'right' => 'Right Sidebar',
                            'no'    => 'No Sidebar',
                        ),
                        'default'   => 'no',
                        'title'     => 'WooCommerce Sidebar ',
                    ),
	                array(
		                'id'        => 'woocommerce_sidebar_style',
		                'type'      => 'select',
		                'options'   => array(
			                'sidebar'            => 'All widgets in a box',
			                'sidebar-background' => 'Each item is separated by a box',
		                ),
		                'default'   => 'sidebar-background',
		                'title'     => 'WooCommerce Sidebar style ',
	                ),
                )
            ),

            // Blog Single
            array(
                'title'            => 'Blog',
                'id'               => 'blog_single',
                'icon'             => 'dashicons dashicons-media-spreadsheet',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => array(
                    array(
                        'type'        => 'section',
                        'id'          => 'section_blog_section',
                        'title'       => 'General Settings',
                        'indent'      => true
                    ),
                    array(
                        'type'        => 'select',
                        'id'          => 'blog_layout',
                        'title'       => 'Blog Layout',
                        'options'     => array(
                            'post__grid'      => 'Grid',
                            'post__standard'  => 'Standard',
                        ),
                        'default'     => 'post__standard'
                    ),
	                array(
		                'type'        => 'select',
		                'id'          => 'blog_layout_grid_on_desktops',
		                'title'       => 'Articles per row on Desktops',
		                'options'     => array(
			                'col-md-4'      => '3 articles / row',
			                'col-md-3'      => '4 articles / row',
			                'col-md-6'      => '2 articles / row',
		                ),
		                'required'    => array('blog_layout', '=', 'post__grid'),
		                'default'     => 'col-md-4'
	                ),
	                array(
		                'type'        => 'select',
		                'id'          => 'blog_layout_grid_on_smalls',
		                'title'       => 'Articles per row on Tablets',
		                'options'     => array(
			                'col-sm-6'      => '2 articles / row',
			                'col-sm-4'      => '3 articles / row',
			                'col-sm-3'      => '4 articles / row',
		                ),
		                'required'    => array('blog_layout', '=', 'post__grid'),
		                'default'     => 'col-sm-6'
	                ),
                    array(
                        'type'        => 'section',
                        'id'          => 'section_blog_section_close',
                        'title'       => '',
                        'indent'      => false
                    ),
                    array(
                        'type'        => 'section',
                        'id'          => 'section_single_post',
                        'title'       => 'Article Settings',
                        'indent'      => true
                    ),
                    array(
                        'type'        => 'select',
                        'id'          => 'single_post_toggle_related_posts',
                        'title'       => 'Related Posts',
                        'options'     => array(
                            'enable'  => 'Enable',
                            'disable' => 'Disable',
                        ),
                        'default'     => 'enable'
                    ),
                    array(
                        'type'        => 'text',
                        'id'          => 'single_post_related_posts_title',
                        'title'       => 'Title',
                        'default'     => 'You may also like'
                    ),
                    array(
                        'type'        => 'select',
                        'id'          => 'single_post_related_number_of_articles',
                        'title'       => 'Number of articles',
                        'options'     => array(
                            'col-md-4'  => '3 Articles',
                            'col-md-6' => '2 Articles',
                        ),
                        'default'     => 'col-md-6'
                    )
                ),
            ),

            // Listing Settings
	        array(
	            'title' => 'Listing Settings',
	            'icon'             => 'dashicons dashicons-lightbulb',
	            'subsection'       => false,
	            'customizer_width' => '500px',
	            'fields'           => array(
		            array(
			            'title'     => 'Listing Slug settings',
			            'id'       => 'open_listing_slug_section',
			            'type'     => 'section',
			            'indent'   => true
		            ),
		            array(
			            'id'      => 'custom_listing_location_slug',
			            'type'    => 'text',
			            'title'   => 'Listing Location Slug',
			            'description' => 'Leave empty to use the default setting. Warning: Please click on Settings -> Permalinks -> Select Post Name and click Save Changes button',
			            'default' => ''
		            ),
		            array(
			            'id'      => 'custom_listing_cat_slug',
			            'type'    => 'text',
			            'title'   => 'Listing Category Slug',
			            'description' => 'Leave empty to use the default setting. Warning: Please click on Settings -> Reading -> Select Post Name and click Save Changes button',
			            'default' => ''
		            ),
		            array(
			            'id'      => 'custom_listing_tag_slug',
			            'type'    => 'text',
			            'title'   => 'Listing Tag Slug',
			            'description' => 'Leave empty to use the default setting. Warning: Please click on Settings -> Permalinks -> Select Post Name and click Save Changes button',
			            'default' => ''
		            ),
		            array(
			            'id'      => 'custom_listing_single_slug',
			            'type'    => 'text',
			            'title'   => 'Single Listing Slug',
			            'description' => 'Leave empty to use the default setting. Warning: Please click on Settings -> Permalinks -> Select Post Name and click Save Changes button',
			            'default' => ''
		            ),
		            array(
			            'id'       => 'close_listing_slug_section',
			            'type'     => 'section',
			            'indent'   => false
		            ),
		            array(
			            'title'     => 'Add Listing',
			            'id'       => 'open_add_listing_page_section',
			            'type'     => 'section',
			            'indent'   => true
		            ),
		            array(
			            'id'        => 'toggle_add_listing_btn_on_mobile',
			            'type'      => 'select',
			            'title'     => 'Toggle Add Listing Button on the mobile',
			            'default'   => 'disable',
			            'options'   => array(
				            'disable' => 'Disable',
				            'enable'  => 'Enable',
			            )
		            ),
		            array(
			            'id'        => 'add_listing_select_location_type',
			            'type'      => 'select',
			            'title'     => 'Select Location Type',
			            'desc'      => 'Warning: This setting is not available if you are using Wiloke Design Listing plugin',
			            'default'   => 'default',
			            'options'   => array(
				            'default' => 'Add Locations by admin only',
				            'google'  => 'Automatically Add Location By Google',
			            )
		            ),
		            array(
			            'id'        => 'add_listing_select_tag_type',
			            'type'      => 'select',
			            'title'     => 'Select Tag Type',
			            'default'   => 'add_by_user',
			            'options'   => array(
				            'add_by_admin' => 'Add Tags by admin only',
				            'add_by_user'  => 'User can add any tag they want',
			            )
		            ),
		            array(
			            'id'       => 'close_add_listing_page_section',
			            'type'     => 'section',
			            'indent'   => false
		            ),
		            array(
			            'title' => 'Listing Category and Listing Location Settings',
			            'id'    => 'open_listings_category_and_listing_location_section',
			            'type'  => 'section',
			            'indent'=> true
		            ),
		            array(
			            'id'        => 'listing_taxonomy_layout',
			            'type'      => 'select',
			            'title'     => 'Listing Category & Listing Location Layout',
			            'subtitle'  => 'Set a layout for listing category page & listing location page.',
			            'default'   => 'listing--list',
			            'options'   => array(
				            'listing--grid'     => 'Grid',
                            'listing--grid1'    => 'Grid 2',
                            'listing-grid2'     => 'Grid 3',
                            'listing-grid3'     => 'Grid 4',
				            'listing-grid4'     => 'Grid 5',
				            'listing--list'     => 'List',
				            'listing--list1'    => 'List 2',
				            'circle-thumbnail'  => 'List Circle Thumbnail (New)',
				            'creative-rectangle'=> 'List Creative Rectangle (New)'
			            )
		            ),
		            array(
			            'title' => '',
			            'id'    => 'close_listings_category_and_listing_location_section',
			            'type'  => 'section',
			            'indent'=> false
		            ),
		            array(
			            'title' => 'Search Form Settings',
			            'id'    => 'open_search_form_section',
			            'type'  => 'section',
			            'indent'=> true
		            ),
		            array(
			            'id'    => 'listing_search_page',
			            'type'  => 'select',
			            'title' => 'Search Form Action',
			            'default' => 'self',
			            'options' => array(
				            'map'    => 'Redirect To Search page',
				            'self'   => 'Show the search results on the self page.'
			            )
		            ),
                    array(
                        'id'    => 'listing_search_location_order_by',
                        'type'  => 'select',
                        'title' => 'Listing Location Order By',
                        'default' => 'self',
                        'options' => array(
                            'id'    => 'ID',
                            'count' => 'Count',
                            'name'  => 'Name',
                        )
                    ),
                    array(
                        'id'      => 'listing_search_number_of_locations',
                        'type'    => 'text',
                        'title'   => 'Number of Listing Locations',
                        'default' => 10
                    ),
                    array(
                        'id'      => 'listing_country_restriction',
                        'type'    => 'text',
                        'title'   => 'Country restriction',
                        'description'   => 'You can find your country code at <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2" target="_blank">https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2</a>',
                        'default' => ''
                    ),
		            array(
			            'id'    => 'header_search_map_page',
			            'type'  => 'select',
			            'data'  => 'pages',
			            'default'=> '',
			            'title' => 'Search Page',
			            'required' => array('listing_search_page', '=', 'map'),
			            'description' => 'When user click on Search button, it will redirect to this page. To create a search page, please go to Pages -> Add New -> Set that page as Listing template or Map page Template (This setting should under Page Attributes box)',
		            ),
		            array(
			            'id'        => 'header_search_keyword_label',
			            'type'      => 'text',
			            'title'     => 'Label for the keyword field',
			            'default'   => 'Address, city or select suggestion category',
		            ),
		            array(
			            'id'        => 'header_search_location_label',
			            'type'      => 'text',
			            'title'     => 'Label for the Location field',
			            'default'   => 'Location',
		            ),
		            array(
			            'id'    => 'listing_toggle_filter_price',
			            'type'  => 'select',
			            'title' => 'Toggle Filter By Price',
			            'default' => 'enable',
			            'options' => array(
				            'disable'  => 'Disable',
				            'enable'   => 'Enable',
			            )
		            ),
		            array(
			            'id'        => 'header_search_all_cost_label',
			            'type'      => 'text',
			            'required'  => array('listing_toggle_filter_price', '=', 'enable'),
			            'title'     => 'Label for the default cost',
			            'default'   => 'Cost - It doesn\'t matter',
		            ),
		            array(
			            'id'        => 'header_search_cheap_cost_label',
			            'type'      => 'text',
			            'title'     => 'Label for Cheap Segment',
			            'required'  => array('listing_toggle_filter_price', '=', 'enable'),
			            'default'   => '$ - Cheap',
		            ),
		            array(
			            'id'        => 'header_search_moderate_cost_label',
			            'type'      => 'text',
			            'title'     => 'Label for Moderate Segment',
			            'required'  => array('listing_toggle_filter_price', '=', 'enable'),
			            'default'   => '$$ - Moderate',
		            ),
		            array(
			            'id'        => 'header_search_expensive_cost_label',
			            'type'      => 'text',
			            'title'     => 'Label for Expensive Segment',
			            'required'  => array('listing_toggle_filter_price', '=', 'enable'),
			            'default'   => '$$$ - Expensive',
		            ),
		            array(
			            'id'        => 'header_search_ultra_high_cost_label',
			            'type'      => 'text',
			            'title'     => 'Label for  Ultra high',
			            'required'  => array('listing_toggle_filter_price', '=', 'enable'),
			            'default'   => '$$$$ - Ultra high',
		            ),
		            array(
			            'id'    => 'listing_toggle_search_by_tag',
			            'type'  => 'select',
			            'title' => 'Toggle Search By Tags',
			            'default' => 'enable',
			            'options' => array(
				            'disable'  => 'Disable',
				            'enable'   => 'Enable',
			            )
		            ),
		            array(
			            'id'    => 'listgo_search_max_radius',
			            'type'  => 'text',
			            'title' => 'Maximum Radius (*)',
			            'default' => 20
		            ),
		            array(
			            'id'    => 'listgo_search_min_radius',
			            'type'  => 'text',
			            'title' => 'Minimum Radius (*)',
			            'default' => 1
		            ),
		            array(
			            'id'    => 'listgo_search_default_radius',
			            'type'  => 'text',
			            'title' => 'Set Default Radius (*)',
			            'default' => 10
		            ),
		            array(
			            'id'        => 'listgo_search_default_unit',
			            'type'      => 'select',
			            'default'   => 'km',
			            'title'     => 'Set Default Unit',
			            'options'   => array(
			            	'km' => 'Kilometer',
			            	'mi' => 'Mile',
			            )
		            ),
		            array(
			            'title' => '',
			            'id'    => 'close_search_form_settings',
			            'type'  => 'section',
			            'indent'=> false
		            ),
	                array(
	                	'id'        => 'listing_layout',
		                'type'      => 'select',
		                'title'     => 'Single Listing Layout',
		                'subtitle'  => 'Set a layout for listing page. Note that you can override  this setting for each individual page by using Listing Template',
		                'default'   => 'templates/single-listing-traditional.php',
		                'options'   => array(
			                'templates/single-listing-traditional.php'      => 'Traditional',
		                	'templates/single-listing-creative.php'         => 'Creative',
		                	'templates/single-listing-creative-sidebar.php' => 'Creative Sidebar',
		                	'templates/single-listing-lively.php'           => 'Lively',
		                	'templates/single-listing-blurbehind.php'       => 'Blur Behind',
		                	'templates/single-listing-lisa.php'             => 'Lisa',
		                	'templates/single-listing-howard-roark.php'     => 'Roark',
		                )
	                ),
		            array(
			            'id'        => 'listing_contactform7',
			            'type'      => 'select',
			            'data'      => 'posts',
			            'args'      => array(
				            'post_type'         => 'wpcf7_contact_form',
				            'posts_per_page'    => -1,
				            'orderby'           => 'post_date',
				            'post_status'       => 'publish'
			            ),
			            'title'       => 'Set Contact Form 7',
			            'description' => 'This contact form will be used on the Contact & Map tab and on the Contact Widget',
		            ),
		            array(
			            'title' => 'Claim Settings',
			            'id'    => 'open_claim_section',
			            'type'  => 'section',
			            'indent'=> true
		            ),
		            array(
			            'id'    => 'listing_toggle_claim_listings',
			            'type'  => 'select',
			            'title' => 'Toggle Claim Listings',
			            'default' => 'enable',
			            'options' => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'        => 'listing_claim_title',
			            'type'      => 'text',
			            'title'     => 'Claim Title',
			            'default'   => 'Is this your business?',
			            'required'  => array('listing_toggle_claim_listings', '=', 'enable'),
		            ),
		            array(
			            'id'        => 'listing_claim_description',
			            'type'      => 'textarea',
			            'title'     => 'Claim Description',
			            'default'   => 'Claim listing is the best way to manage and protect your business',
			            'required'  => array('listing_toggle_claim_listings', '=', 'enable'),
		            ),
		            array(
			            'id'        => 'listing_claim_popup_description',
			            'type'      => 'textarea',
			            'title'     => 'Claim Popup Description',
			            'default'   => 'Claim your listing in order to manage the listing page. You will get access to the listing dashboard, where you can upload photos, change the listing content and much more.',
			            'required'  => array('listing_toggle_claim_listings', '=', 'enable'),
		            ),
		            array(
			            'id'    => 'listing_toggle_claim_required_phone',
			            'type'  => 'select',
			            'title' => 'Toggle Required Supply Business Phone',
			            'default' => 'enable',
			            'options' => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'    => 'listing_claimed_template',
			            'type'  => 'select',
			            'data'  => 'posts',
			            'args'      => array(
				            'post_type'         => 'page',
				            'posts_per_page'    => -1,
				            'post_status'       => 'publish',
				            'meta_key'          => '_wp_page_template',
				            'meta_value'        => 'templates/edit-claimed.php'
			            ),
			            'required'      => array('listing_toggle_claim_listings', '=', 'enable'),
			            'title'         => 'Edit Claimed Page',
			            'subtitle'      => 'Where claimer guy could be edit his Listing',
			            'description'   => '<strong>Please go to Pages -> Add New -> Create a new page then assign the page to Edit Claimed template </strong>',
			            'default'       => ''
		            ),
		            array(
			            'id'    => 'close_claim_section',
			            'type'  => 'section',
			            'indent'=> false
		            ),
		            array(
			            'title' => 'Listings Near By You',
			            'id'    => 'open_geocode_section',
			            'type'  => 'section',
			            'indent'=> true
		            ),
		            array(
			            'id'    => 'listing_toggle_ask_for_geocode',
			            'type'  => 'select',
			            'title' => 'Ask For Current User Position',
			            'default' => 'enable',
			            'options' => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'    => 'close_geocode_section',
			            'type'  => 'section',
			            'indent'=> false
		            ),
		            array(
		            	'id'        => 'open_listing_tab_section',
			            'type'      => 'section',
			            'indent'    => true,
			            'title'     => 'Tab Settings',
			            'desc'      => 'Warning: Tab Settings is not available if you are using Wiloke Design Add Addlisting plugin. We will use Design Addlisting -> Single Listing feature instead.'
		            ),
		            array(
			            'id'        => 'listing_toggle_tab_desc',
			            'type'      => 'select',
			            'title'     => 'Toggle Description Tab',
			            'default'   => 'enable',
			            'options'   => array(
			            	'enable'    => 'Enable',
			            	'disable'   => 'Disable',
			            )
		            ),
		            array(
		                'id'        => 'listing_tab_desc',
			            'type'      => 'text',
			            'title'     => 'Description Tab',
			            'required'  => array('listing_toggle_tab_desc', '=', 'enable'),
			            'default'   => 'Description'
		            ),
		            array(
			            'id'        => 'listing_toggle_tab_contact_and_map',
			            'type'      => 'select',
			            'title'     => 'Toggle Contact And Map Tab',
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'        => 'listing_tab_contact_and_map',
			            'type'      => 'text',
			            'title'     => 'Contact And Map Tab',
			            'required'  => array('listing_toggle_tab_contact_and_map', '=', 'enable'),
			            'default'   => 'Contact & Map'
		            ),
		            array(
			            'id'        => 'listing_toggle_tab_review_and_rating',
			            'type'      => 'select',
			            'title'     => 'Toggle Review and rating',
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'        => 'listing_tab_review_and_rating',
			            'type'      => 'text',
			            'title'     => 'Review And Rating Tab',
			            'required'  => array('listing_toggle_tab_review_and_rating', '=', 'enable'),
			            'default'   => 'Review & Rating'
		            ),
		            array(
			            'id'        => 'listing_toggle_add_photo_in_review_tab',
			            'type'      => 'select',
			            'title'     => 'Allow user to add photos in their review',
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'        => 'close_listing_tab_section',
			            'type'      => 'section',
			            'indent'    => false
		            ),
		            array(
			            'id'        => 'open_listing_meta_data_section',
			            'type'      => 'section',
			            'indent'    => true,
			            'title'     => 'Toggle Meta Data',
		            ),
		            array(
			            'id'        => 'listing_toggle_posted_on',
			            'type'      => 'select',
			            'title'     => 'Posted On',
			            'subtitle'  => 'Showing when the article was created',
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'        => 'listing_toggle_categories',
			            'type'      => 'select',
			            'title'     => 'Toggle Categories',
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'        => 'listing_toggle_locations',
			            'type'      => 'select',
			            'title'     => 'Toggle Locations',
			            'default'   => 'disable',
			            'options'   => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'        => 'listing_toggle_tags',
			            'type'      => 'select',
			            'title'     => 'Toggle Tags',
			            'default'   => 'disable',
			            'options'   => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'        => 'listing_toggle_rating_result',
			            'type'      => 'select',
			            'title'     => 'Rating Result',
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'        => 'listing_toggle_following_author',
			            'type'      => 'select',
			            'title'     => 'Following Author\'s Article',
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'        => 'listing_toggle_report',
			            'type'      => 'select',
			            'title'     => 'Report',
			            'subtitle'  => 'If this feature is enabled, A bad article could be report to admin by reader.',
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'        => 'listing_toggle_sharing_posts',
			            'type'      => 'select',
			            'title'     => 'Sharing Article',
			            'description' => 'Important: Wiloke Sharing Posts plugin is required by this feature.',
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'            => 'listing_toggle_add_to_favorite',
			            'type'          => 'select',
			            'title'         => 'Add To My Favorite',
			            'description'   => 'Important: Listgo Functionality plugin is required by this feature.',
			            'default'       => 'enable',
			            'options'       => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'        => 'close_listing_meta_data_section',
			            'type'      => 'section',
			            'indent'    => false
		            ),
		            array(
		            	'title'     => 'Related Listings',
			            'id'        => 'open_related_listing_section',
			            'type'      => 'section',
			            'indent'    => true
		            ),
		            array(
			            'id'        => 'listing_toggle_related_listings',
			            'type'      => 'select',
			            'title'     => 'Toggle Related Listings',
			            'default'   => 'enable',
			            'options'   => array(
				            'enable'    => 'Enable',
				            'disable'   => 'Disable',
			            )
		            ),
		            array(
			            'id'        => 'listing_related_listings_title',
			            'type'      => 'text',
			            'required'  => array('listing_toggle_related_listings', '=', 'enable'),
			            'title'     => 'Related Listing Title',
			            'default'   => 'More Listings By %author%'
		            ),
		            array(
			            'id'        => 'listing_related_listings_by',
			            'type'      => 'select',
			            'required'  => array('listing_toggle_related_listings', '=', 'enable'),
			            'title'     => 'Get Related Listings By',
			            'default'   => 'author',
			            'options'   => array(
				            'author'            => 'Author',
				            'listing_cat'       => 'Listing Category',
                            'listing_location'  => 'Listing Location',
				            'listing_nearby_and_location_fb'  => 'Showing Listing Near By Users - Listing Location FallBack'
			            )
		            ),
		            array(
			            'id'        => 'close_related_listing_section',
			            'type'      => 'section',
			            'indent'    => false
		            ),
                )
	        ),
	        array(
		        'title'            => 'WooCommerce',
		        'id'               => 'woocommerce_settings',
		        'subsection'       => false,
		        'customizer_width' => '500px',
		        'icon'             => 'dashicons dashicons-cart',
		        'fields'           => array(
			        array(
				        'type'        => 'select',
				        'id'          => 'woo_products_per_row_on_desktops',
				        'title'       => 'Products per row on large Desktops',
				        'options'     => array(
					        'col-md-4'      => '3 products / row',
					        'col-md-3'      => '4 products / row',
					        'col-md-6'      => '2 products / row',
				        ),
				        'default'     => 'col-md-4'
			        ),
			        array(
				        'type'        => 'select',
				        'id'          => 'woo_products_per_row_on_tablets',
				        'title'       => 'Products per row on small Tablets',
				        'options'     => array(
					        'col-sm-6'      => '2 products / row',
					        'col-sm-4'      => '3 products / row',
					        'col-sm-3'      => '4 products / row',
				        ),
				        'default'     => 'col-sm-6'
			        ),
		        )
	        ),

            // Footer Settings
            array(
                'title'            => 'Footer',
                'id'               => 'footer_settings',
                'subsection'       => false,
                'customizer_width' => '500px',
                'icon'             => 'dashicons dashicons-hammer',
                'fields'           => array(
	                array(
		                'id'        => 'footer_toggle_widgets',
		                'type'      => 'select',
		                'title'     => 'Footer Widgets',
		                'subtitle'   => 'Select Enable if you want to use this feature. To set Footer Widgets, please go to Appearance -> Widgets -> Dragging your widgets into Footer 1 area and Footer 2 area.',
		                'default'   => 'enable',
		                'options'   => array(
		                	'enable'  => 'Enable',
		                	'disable' => 'Disable',
		                )
	                ),
	                array(
		                'id'        => 'footer_style',
		                'type'      => 'select',
		                'required'  => array('footer_toggle_widgets', '=', 'enable'),
		                'title'     => 'Footer Style',
		                'default'   => 'footer-style1',
		                'options'   => array(
			                'footer-style1'  => 'Footer Style 1 (1 Column)',
			                'footer-style2'  => 'Footer Style 2 (3 Columns)',
			                'footer-style3'  => 'Footer Style 3 (5 Columns)',
		                )
	                ),
                	array(
                        'id'        => 'footer_bg',
                        'type'      => 'media',
                        'title'     => 'Footer Background',
                        'default'   => ''
                    ),
	                array(
		                'id'        => 'footer_overlay',
		                'type'      => 'color_rgba',
		                'title'     => 'Overlay Color',
		                'default'   => ''
	                ),
                    array(
                        'id'            => 'footer_logo',
                        'type'          => 'media',
                        'title'         => 'Footer Logo',
                        'description'   => 'Leave empty to use the logo at General Section',
                    ),
                    array(
                        'type'        => 'textarea',
                        'id'          => 'footer_copyright',
                        'title'       => 'Copyright',
                        'default'     => ''
                    )
                )
            ),

	        // Social networks
	        array(
		        'title'            => 'Social Networks',
		        'id'               => 'social_network_settings',
		        'subsection'       => false,
		        'icon'             => 'dashicons dashicons-share',
		        'customizer_width' => '500px',
		        'fields'           => WilokeSocialNetworks::render_setting_field()
	        ),

	        // SEO
	        array(
		        'title'            => 'SEO',
		        'id'               => 'seo_settings',
		        'subsection'       => false,
		        'customizer_width' => '500px',
		        'icon'             => 'dashicons dashicons-search',
		        'fields'           => array(
			        array(
				        'id'        => 'seo_open_graph_meta',
				        'type'      => 'select',
				        'options'   => array(
					        'enable'  => 'Enable',
					        'disable' => 'Disable',
				        ),
				        'default'  => 'enable',
				        'title'    => 'Open Graph Meta',
				        'subtitle' => 'Elements that describe the object in different ways and are represented by meta tags included on the object page',
			        ),
			        array(
				        'id'       => 'seo_og_image',
				        'type'     => 'media',
				        'title'    =>  'Image',
				        'subtitle' =>  'This image represent your website within the social graph. It should use a 1200x1200px  or large square image.',
				        'default'  => ''
			        ),
			        array(
				        'type'     => 'select',
				        'id'       => 'seo_toggle_custom_meta_tags',
				        'title'    => 'Toggle Meta Tags',
				        'subtitle' => 'We recommend disabling this feature if you are using a SEO plugin',
				        'default'  => 'enable',
				        'options'   => array(
					        'enable'   => 'Enable',
					        'disable'  => 'Disable',
				        ),
			        ),
			        array(
				        'type'     => 'text',
				        'id'       => 'seo_home_custom_title',
				        'title'    => 'Homepage custom title',
				        'subtitle' => 'The title will be displayed in homepage between &lt;title>&lt;/title> tags',
				        'required' => array('seo_toggle_custom_meta_tags', '=', 'enable'),
				        'default'  => get_option('blogname')
			        ),
			        array(
				        'id'        => 'seo_home_title_format',
				        'type'      => 'select',
				        'options'   => array(
					        'blogname_blogdescription'  => 'Blog Name | Blog Description',
					        'blogdescription_blogname'  => 'Blog Description | Blog Name',
					        'blogname' => 'Blog Name Only',
				        ),
				        'default'  => 'blogname_blogdescription',
				        'required' => array('seo_toggle_custom_meta_tags', '=', 'enable'),
				        'title'    => 'Home Title Format',
				        'subtitle' => 'If Homepage custom title not set',
			        ),
			        array(
				        'id'        => 'seo_archive_title_format',
				        'type'      => 'select',
				        'required'  => array('seo_toggle_custom_meta_tags', '=', 'enable'),
				        'options'   => array(
					        'categoryname_blogname'  => 'Category Name | Blog Name',
					        'blogname_categoryname'  => 'Blog Name | Category Name',
					        'category' => 'Category Name Only',
				        ),
				        'default'     => 'categoryname_blogname',
				        'title'       => 'Category Title Format',
				        'subtitle'    => 'If Homepage custom title not set',
			        ),
			        array(
				        'id'        => 'seo_single_post_page_title_format',
				        'type'      => 'select',
				        'required'  => array('seo_toggle_custom_meta_tags', '=', 'enable'),
				        'options'   => array(
					        'posttitle_blogname'  => 'Post Title | Blog Name',
					        'blogname_posttitle'  => 'Blog Name | Post Title',
					        'posttitle' => 'Post Title Only',
				        ),
				        'default'     => 'posttitle_blogname',
				        'title'       => 'Single Post Page Title Format',
			        ),
			        array(
				        'id'       => 'seo_home_meta_keywords',
				        'required' => array('seo_toggle_custom_meta_tags', '=', 'enable'),
				        'type'     => 'textarea',
				        'default'  => '',
				        'title'    => 'Home Meta Keywords',
				        'subtitle' => 'Add tags for the search engines and especially Google',
			        ),
			        array(
				        'id'    => 'seo_home_meta_description',
				        'required' => array('seo_toggle_custom_meta_tags', '=', 'enable'),
				        'type'  => 'textarea',
				        'title' => 'Home Meta Description',
				        'default'  => get_option('blogdescription')
			        ),
			        array(
				        'id'     => 'seo_author_meta_description',
				        'required' => array('seo_toggle_custom_meta_tags', '=', 'enable'),
				        'type'   => 'textarea',
				        'title'  => 'Author Meta Description',
				        'default'=>'wiloke.com'
			        ),
			        array(
				        'id'     => 'seo_contact_meta_description',
				        'required' => array('seo_toggle_custom_meta_tags', '=', 'enable'),
				        'type'   => 'textarea',
				        'title'  => 'Contact Meta Description',
				        'default'=>'piratesmorefun@gmail.com'
			        ),
			        array(
				        'id'     => 'seo_other_meta_keywords',
				        'required' => array('seo_toggle_custom_meta_tags', '=', 'enable'),
				        'type'   => 'textarea',
				        'title'  => 'Other Meta Keywords',
				        'default'=>''
			        ),
			        array(
				        'id'     => 'seo_other_meta_description',
				        'required' => array('seo_toggle_custom_meta_tags', '=', 'enable'),
				        'type'   => 'textarea',
				        'title'  => 'Other Meta Description',
				        'default'=>''
			        )
		        )
	        ),

	        // Advanced Settings
            array(
                'title'            => 'Advanced Settings',
                'id'               => 'advanced_settings',
                'icon'             => 'dashicons dashicons-lightbulb',
                'subsection'       => false,
                'customizer_width' => '500px',
                'fields'           => array(
                    array(
                        'id'        => 'advanced_google_fonts',
                        'type'      => 'select',
                        'title'     => 'Google Fonts',
                        'options'   => array(
                            'default'   => 'Default',
                            'general'   => 'Custom',
                            // 'detail'    => 'Detail Custom',
                        ),
                        'default'   => 'default'
                    ),
                    array(
                        'id'            => 'advanced_general_google_fonts',
                        'type'          => 'text',
                        'title'         => 'Google Fonts',
                        'required'      => array('advanced_google_fonts', '=', 'general'),
                        'description'   => 'The theme allows replace current Google Fonts with another Google Fonts. Go to https://fonts.google.com/specimen to get a the Font that you want. For example: https://fonts.googleapis.com/css?family=Prompt',
                    ),
                    array(
                        'id'            => 'advanced_general_google_fonts_css_rules',
                        'type'          => 'text',
                        'required'      => array('advanced_google_fonts', '=', 'general'),
                        'title'         => 'Css Rules',
                        'description'   => 'This code shoule be under Google Font link. For example: \'Prompt\', sans-serif;',
                    ),
                    array(
                        'id'        => 'advanced_main_color',
                        'type'      => 'select',
                        'title'     => 'Theme Color',
                        'options'   => array(
                            ''        => 'Default',
                            'green'   => 'Green',
                            'lime'    => 'Lime',
                            'pink'    => 'Pink',
                            'yellow'  => 'Yellow',
                            'custom'  => 'Custom',
                        ),
                        'default'   => ''
                    ),
                    array(
                        'id'        => 'advanced_custom_main_color',
                        'type'      => 'color_rgba',
                        'title'     => 'Custom Color',
                        'required'  => array('advanced_main_color', '=', 'custom')
                    ),
	                array(
		                'id'          => 'widget_caching',
		                'type'        => 'text',
		                'title'       => 'Widget Caching',
		                'description' => 'Leave empty mean no caching. But We highly recommend using this feature. Unit is hour, it means if you enter in 1, the widget will be cached in 1 hour.',
		                'default'     => ''
	                ),
	                array(
		                'id'          => 'sidebar_additional',
		                'type'        => 'text',
		                'title'       => 'Add More Sidebar',
		                'description' => 'You can add more sidebar by entering in your sidebar id here. For example: my_custom_sidebar_1,my_custom_sidebar_2',
		                'default'     => ''
	                ),
                    array(
                        'id'        => 'advanced_css_code',
                        'type'      => 'ace_editor',
                        'title'     => 'Custom CSS Code',
                        'mode'      => 'css',
                        'theme'    => 'monokai'
                    ),
                    array(
                        'id'        => 'advanced_js_code',
                        'type'      => 'ace_editor',
                        'title'     => 'Custom Javascript Code',
                        'mode'      => 'javascript',
                        'default'   => ''
                    ),
                )
            )
        )
    )
);