<?php
/**
 * Setup theme-specific fonts and colors
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0.22
 */

// If this theme is a free version of premium theme
if ( ! defined( 'KIDSCARE_THEME_FREE' ) ) {
	define( 'KIDSCARE_THEME_FREE', false );
}
if ( ! defined( 'KIDSCARE_THEME_FREE_WP' ) ) {
	define( 'KIDSCARE_THEME_FREE_WP', false );
}

// If this theme is a part of Envato Elements
if ( ! defined( 'KIDSCARE_THEME_IN_ENVATO_ELEMENTS' ) ) {
	define( 'KIDSCARE_THEME_IN_ENVATO_ELEMENTS', false );
}

// If this theme uses multiple skins
if ( ! defined( 'KIDSCARE_ALLOW_SKINS' ) ) {
	define( 'KIDSCARE_ALLOW_SKINS', false );
}
if ( ! defined( 'KIDSCARE_DEFAULT_SKIN' ) ) {
	define( 'KIDSCARE_DEFAULT_SKIN', 'default' );
}



// Theme storage
// Attention! Must be in the global namespace to compatibility with WP CLI
//-------------------------------------------------------------------------
$GLOBALS['KIDSCARE_STORAGE'] = array(

	// Key validator: market[env|loc]-vendor[axiom|ancora|themerex]
	'theme_pro_key'      => 'env-axiom',

	// Generate Personal token from Envato to automatic upgrade theme
	'upgrade_token_url'  => '//build.envato.com/create-token/?default=t&purchase:download=t&purchase:list=t',

	// Theme-specific URLs (will be escaped in place of the output)
	'theme_demo_url'     => '//kidscare.axiomthemes.com/',
	'theme_doc_url'      => '//kidscare.axiomthemes.com/doc',
	
	
	'theme_rate_url'     => '//themeforest.net/download',

	'theme_custom_url' => '//themerex.net/offers/?utm_source=offers&utm_medium=click&utm_campaign=themedash',

	'theme_download_url' => '//themeforest.net/item/kidscare-multipurpose-children-site-template/14428209',         // Axiom

	'theme_support_url'  => '//themerex.net/support/',                              // Axiom

	'theme_video_url'    => '//www.youtube.com/channel/UCBjqhuwKj3MfE3B6Hg2oA8Q',   // Axiom

	'theme_privacy_url'  => '//axiomthemes.com/privacy-policy/',                    // Axiom

	// Comma separated slugs of theme-specific categories (for get relevant news in the dashboard widget)
	// (i.e. 'children,kindergarten')
	'theme_categories'   => '',

	// Responsive resolutions
	// Parameters to create css media query: min, max
	'responsive'         => array(
		// By size
		'xxl'        => array( 'max' => 1679 ),
		'xl'         => array( 'max' => 1439 ),
		'lg'         => array( 'max' => 1279 ),
		'md_over'    => array( 'min' => 1024 ),
		'md'         => array( 'max' => 1023 ),
		'sm'         => array( 'max' => 767 ),
		'sm_wp'      => array( 'max' => 600 ),
		'xs'         => array( 'max' => 479 ),
		// By device
		'wide'       => array(
			'min' => 2160
		),
		'desktop'    => array(
			'min' => 1680,
			'max' => 2159,
		),
		'notebook'   => array(
			'min' => 1280,
			'max' => 1679,
		),
		'tablet'     => array(
			'min' => 768,
			'max' => 1279,
		),
		'not_mobile' => array(
			'min' => 768
		),
		'mobile'     => array(
			'max' => 767
		),
	),
);



// THEME-SUPPORTED PLUGINS
// If plugin not need - remove its settings from next array
//----------------------------------------------------------
$kidscare_theme_required_plugins_group = esc_html__( 'Core', 'kidscare' );
$kidscare_theme_required_plugins = array(
	// Section: "CORE" (required plugins)
	// DON'T COMMENT OR REMOVE NEXT LINES!
	'trx_addons'         => array(
								'title'       => esc_html__( 'ThemeREX Addons', 'kidscare' ),
								'description' => esc_html__( "Will allow you to install recommended plugins, demo content, and improve the theme's functionality overall with multiple theme options", 'kidscare' ),
								'required'    => true,
								'logo'        => 'logo.png',
								'group'       => $kidscare_theme_required_plugins_group,
							),
);

// Section: "PAGE BUILDERS"
$kidscare_theme_required_plugins_group = esc_html__( 'Page Builders', 'kidscare' );
$kidscare_theme_required_plugins['elementor'] = array(
	'title'       => esc_html__( 'Elementor', 'kidscare' ),
	'description' => esc_html__( "Is a beautiful PageBuilder, even the free version of which allows you to create great pages using a variety of modules.", 'kidscare' ),
	'required'    => false,
	'logo'        => 'logo.png',
	'group'       => $kidscare_theme_required_plugins_group,
);
$kidscare_theme_required_plugins['gutenberg'] = array(
	'title'       => esc_html__( 'Gutenberg', 'kidscare' ),
	'description' => esc_html__( "It's a posts editor coming in place of the classic TinyMCE. Can be installed and used in parallel with Elementor", 'kidscare' ),
	'required'    => false,
	'install'     => false,      // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
	'logo'        => 'logo.png',
	'group'       => $kidscare_theme_required_plugins_group,
);
if ( ! KIDSCARE_THEME_FREE ) {
	$kidscare_theme_required_plugins['js_composer']          = array(
		'title'       => esc_html__( 'WPBakery PageBuilder', 'kidscare' ),
		'description' => esc_html__( "Popular PageBuilder which allows you to create excellent pages", 'kidscare' ),
		'required'    => false,
		'install'     => false,      // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'logo.jpg',
		'group'       => $kidscare_theme_required_plugins_group,
	);
}

// Section: "CONTENT"
$kidscare_theme_required_plugins_group = esc_html__( 'Content', 'kidscare' );
$kidscare_theme_required_plugins['contact-form-7'] = array(
	'title'       => esc_html__( 'Contact Form 7', 'kidscare' ),
	'description' => esc_html__( "CF7 allows you to create an unlimited number of contact forms", 'kidscare' ),
	'required'    => false,
	'logo'        => 'logo.jpg',
	'group'       => $kidscare_theme_required_plugins_group,
);
$kidscare_theme_required_plugins['date-time-picker-field'] = array(
	'title'       => esc_html__( 'Date Time Picker Field', 'kidscare' ),
	'description' => esc_html__( "Convert any input field on your website into a date time picker field using CSS selectors", 'kidscare' ),
	'required'    => false,
	'logo'        => 'logo.jpg',
	'group'       => $kidscare_theme_required_plugins_group,
);
if ( ! KIDSCARE_THEME_FREE ) {
	$kidscare_theme_required_plugins['essential-grid']             = array(
		'title'       => esc_html__( 'Essential Grid', 'kidscare' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'logo.png',
		'group'       => $kidscare_theme_required_plugins_group,
	);
	$kidscare_theme_required_plugins['revslider']                  = array(
		'title'       => esc_html__( 'Revolution Slider', 'kidscare' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'logo.png',
		'group'       => $kidscare_theme_required_plugins_group,
	);
	$kidscare_theme_required_plugins['sitepress-multilingual-cms'] = array(
		'title'       => esc_html__( 'WPML - Sitepress Multilingual CMS', 'kidscare' ),
		'description' => esc_html__( "Allows you to make your website multilingual", 'kidscare' ),
		'required'    => false,
		'install'     => false,      // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'logo.png',
		'group'       => $kidscare_theme_required_plugins_group,
	);
}
// Section: "E-COMMERCE"
$kidscare_theme_required_plugins_group = esc_html__( 'E-Commerce', 'kidscare' );
$kidscare_theme_required_plugins['woocommerce']              = array(
	'title'       => esc_html__( 'WooCommerce', 'kidscare' ),
	'description' => esc_html__( "Connect the store to your website and start selling now", 'kidscare' ),
	'required'    => false,
	'logo'        => 'logo.png',
	'group'       => $kidscare_theme_required_plugins_group,
);
$kidscare_theme_required_plugins['elegro-payment']              = array(
    'title'       => esc_html__( 'Elegro Crypto Payment', 'kidscare' ),
    'description' => esc_html__( "Extends WooCommerce Payment Gateways with an elegro Crypto Payment", 'kidscare' ),
    'required'    => false,
		'install'     => false,      // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
    'logo'        => 'elegro-payment.png',
    'group'       => $kidscare_theme_required_plugins_group,
);

// Section: "SOCIALS & COMMUNITIES"
$kidscare_theme_required_plugins_group = esc_html__( 'Socials and Communities', 'kidscare' );
$kidscare_theme_required_plugins['mailchimp-for-wp'] = array(
	'title'       => esc_html__( 'MailChimp for WP', 'kidscare' ),
	'description' => esc_html__( "Allows visitors to subscribe to newsletters", 'kidscare' ),
	'required'    => false,
	'logo'        => 'logo.png',
	'group'       => $kidscare_theme_required_plugins_group,
);

// Section: "EVENTS & TIMELINES"
$kidscare_theme_required_plugins_group = esc_html__( 'Events and Appointments', 'kidscare' );
if ( ! KIDSCARE_THEME_FREE ) {
	$kidscare_theme_required_plugins['booked']                 = array(
		'title'       => esc_html__( 'Booked Appointments', 'kidscare' ),
		'description' => '',
		'required'    => false,
		'install'     => false,
		'logo'        => 'logo.png',
		'group'       => $kidscare_theme_required_plugins_group,
	);
	$kidscare_theme_required_plugins['quickcal']                 = array(
		'title'       => esc_html__( 'Quickcal Appointments', 'kidscare' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'logo.png',
		'group'       => $kidscare_theme_required_plugins_group,
	);
	$kidscare_theme_required_plugins['the-events-calendar']    = array(
		'title'       => esc_html__( 'The Events Calendar', 'kidscare' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'logo.png',
		'group'       => $kidscare_theme_required_plugins_group,
	);
}

// Section: "OTHER"
$kidscare_theme_required_plugins_group = esc_html__( 'Other', 'kidscare' );
$kidscare_theme_required_plugins['wp-gdpr-compliance'] = array(
	'title'       => esc_html__( 'Cookie Information', 'kidscare' ),
	'description' => esc_html__( "Allow visitors to decide for themselves what personal data they want to store on your site", 'kidscare' ),
	'required'    => false,
	'install'	  => false,
	'logo'        => 'logo.png',
	'group'       => $kidscare_theme_required_plugins_group,
);
$kidscare_theme_required_plugins['gdpr-framework'] = array(
	'title'       => esc_html__( 'The GDPR Framework', 'kidscare' ),
	'description' => esc_html__( "Tools to help make your website GDPR-compliant", 'kidscare' ),
	'required'    => false,
	'install'	  => false,
	'logo'        => 'gdpr-framework.png',
	'group'       => $kidscare_theme_required_plugins_group,
);
$kidscare_theme_required_plugins['trx_updater'] = array(
	'title'       => esc_html__( 'ThemeREX Updater', 'kidscare' ),
	'description' => esc_html__( "Update theme and theme-specific plugins from developer's upgrade server.", 'kidscare' ),
	'required'    => false,
	'logo'        => 'trx_updater.png',
	'group'       => $kidscare_theme_required_plugins_group,
);


// Add plugins list to the global storage
$GLOBALS['KIDSCARE_STORAGE']['required_plugins'] = $kidscare_theme_required_plugins;



// THEME-SPECIFIC BLOG LAYOUTS
//----------------------------------------------
$kidscare_theme_blog_styles = array(
	'excerpt' => array(
		'title'   => esc_html__( 'Standard', 'kidscare' ),
		'archive' => 'index-excerpt',
		'item'    => 'content-excerpt',
		'styles'  => 'excerpt',
	),
	'classic' => array(
		'title'   => esc_html__( 'Classic', 'kidscare' ),
		'archive' => 'index-classic',
		'item'    => 'content-classic',
		'columns' => array( 2, 3 ),
		'styles'  => 'classic',
	),
);
if ( ! KIDSCARE_THEME_FREE ) {
	$kidscare_theme_blog_styles['masonry']   = array(
		'title'   => esc_html__( 'Masonry', 'kidscare' ),
		'archive' => 'index-classic',
		'item'    => 'content-classic',
		'columns' => array( 2, 3 ),
		'styles'  => 'masonry',
	);
	$kidscare_theme_blog_styles['portfolio'] = array(
		'title'   => esc_html__( 'Portfolio', 'kidscare' ),
		'archive' => 'index-portfolio',
		'item'    => 'content-portfolio',
		'columns' => array( 2, 3, 4 ),
		'styles'  => 'portfolio',
	);
	$kidscare_theme_blog_styles['gallery']   = array(
		'title'   => esc_html__( 'Gallery', 'kidscare' ),
		'archive' => 'index-portfolio',
		'item'    => 'content-portfolio-gallery',
		'columns' => array( 2, 3, 4 ),
		'styles'  => array( 'portfolio', 'gallery' ),
	);
	$kidscare_theme_blog_styles['chess']     = array(
		'title'   => esc_html__( 'Chess', 'kidscare' ),
		'archive' => 'index-chess',
		'item'    => 'content-chess',
		'columns' => array( 1, 2, 3 ),
		'styles'  => 'chess',
	);
}

// Add list of blog styles to the global storage
$GLOBALS['KIDSCARE_STORAGE']['blog_styles'] = $kidscare_theme_blog_styles;


// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp_loaded'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)

if ( ! function_exists( 'kidscare_customizer_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'kidscare_customizer_theme_setup1', 1 );
	function kidscare_customizer_theme_setup1() {

		// -----------------------------------------------------------------
		// -- ONLY FOR PROGRAMMERS, NOT FOR CUSTOMER
		// -- Internal theme settings
		// -----------------------------------------------------------------
		kidscare_storage_set(
			'settings', array(

				'duplicate_options'      => 'child',                    // none  - use separate options for the main and the child-theme
																		// child - duplicate theme options from the main theme to the child-theme only
																		// both  - sinchronize changes in the theme options between main and child themes

				'customize_refresh'      => 'auto',                     // Refresh method for preview area in the Appearance - Customize:
																		// auto - refresh preview area on change each field with Theme Options
																		// manual - refresh only obn press button 'Refresh' at the top of Customize frame

				'max_load_fonts'         => 5,                          // Max fonts number to load from Google fonts or from uploaded fonts

				'comment_after_name'     => true,                       // Place 'comment' field after the 'name' and 'email'

				'show_author_avatar'     => true,                       // Display author's avatar in the post meta

				'icons_selector'         => 'internal',                 // Icons selector in the shortcodes:
																		// internal - internal popup with plugin's or theme's icons list (fast and support images and svg)

				'icons_type'             => 'icons',                    // Type of icons (if 'icons_selector' is 'internal'):
																		// icons  - use font icons to present icons
																		// images - use images from theme's folder trx_addons/css/icons.png
																		// svg    - use svg from theme's folder trx_addons/css/icons.svg

				'socials_type'           => 'icons',                    // Type of socials icons (if 'icons_selector' is 'internal'):
																		// icons  - use font icons to present social networks
																		// images - use images from theme's folder trx_addons/css/icons.png
																		// svg    - use svg from theme's folder trx_addons/css/icons.svg

				'check_min_version'      => true,                       // Check if exists a .min version of .css and .js and return path to it
																		// instead the path to the original file
																		// (if debug_mode is on and modification time of the original file < time of the .min file)

				'autoselect_menu'        => false,                      // Show any menu if no menu selected in the location 'main_menu'
																		// (for example, the theme is just activated)

				'disable_jquery_ui'      => false,                      // Prevent loading custom jQuery UI libraries in the third-party plugins

				'use_mediaelements'      => true,                       // Load script "Media Elements" to play video and audio

				'tgmpa_upload'           => false,                      // Allow upload not pre-packaged plugins via TGMPA

				'allow_no_image'         => false,                      // Allow to use theme-specific image placeholder if no image present in the blog, related posts, post navigation, etc.

				'separate_schemes'       => true,                       // Save color schemes to the separate files __color_xxx.css (true) or append its to the __custom.css (false)

				'allow_fullscreen'       => false,                      // Allow cases 'fullscreen' and 'fullwide' for the body style in the Theme Options
																		// In the Page Options this styles are present always
																		// (can be removed if filter 'kidscare_filter_allow_fullscreen' return false)

				'attachments_navigation' => false,                      // Add arrows on the single attachment page to navigate to the prev/next attachment

				'gutenberg_safe_mode'    => array(),                    // 'vc', 'elementor' - Prevent simultaneous editing of posts for Gutenberg and other PageBuilders (VC, Elementor)

				'gutenberg_add_context'  => false,                      // Add context to the Gutenberg editor styles with our method (if true - use if any problem with editor styles) or use native Gutenberg way via add_editor_style() (if false - used by default)

				'allow_gutenberg_blocks' => true,                       // Allow our shortcodes and widgets as blocks in the Gutenberg (not ready yet - in the development now)

				'subtitle_above_title'   => true,                       // Put subtitle above the title in the shortcodes

				'add_hide_on_xxx'        => 'replace',                  // Add our breakpoints to the Responsive section of each element
																		// 'add' - add our breakpoints after Elementor's
																		// 'replace' - add our breakpoints instead Elementor's
																		// 'none' - don't add our breakpoints (using only Elementor's)
			)
		);

		// -----------------------------------------------------------------
		// -- Theme fonts (Google and/or custom fonts)
		// -----------------------------------------------------------------

		// Fonts to load when theme start
		// It can be Google fonts or uploaded fonts, placed in the folder /css/font-face/font-name inside the theme folder
		// Attention! Font's folder must have name equal to the font's name, with spaces replaced on the dash '-'
		
		kidscare_storage_set(
			'load_fonts', array(
				// Google font
				array(
					'name'   => 'Ubuntu',
					'family' => 'sans-serif',
					'styles' => '300,300i,400,400i,500,500i,700,700i',     // Parameter 'style' used only for the Google fonts
				),
                array(
                    'name'   => 'Fredoka One',
                    'family' => 'cursive',
                    'styles' => '400',     // Parameter 'style' used only for the Google fonts
                ),
				// Font-face packed with theme
				array(
					'name'   => 'Montserrat',
					'family' => 'sans-serif',
				),
			)
		);

		// Characters subset for the Google fonts. Available values are: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese
		kidscare_storage_set( 'load_fonts_subset', 'latin,latin-ext' );

		kidscare_storage_set(
			'theme_fonts', array(
				'p'       => array(
					'title'           => esc_html__( 'Main text', 'kidscare' ),
					'description'     => esc_html__( 'Font settings of the main text of the site. Attention! For correct display of the site on mobile devices, use only units "rem", "em" or "ex"', 'kidscare' ),
					'font-family'     => '"Ubuntu",sans-serif',
					'font-size'       => '1rem',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '-0.17px',
					'margin-top'      => '0em',
					'margin-bottom'   => '1.1em',
				),
				'h1'      => array(
					'title'           => esc_html__( 'Heading 1', 'kidscare' ),
					'font-family'     => '"Fredoka One",cursive',
					'font-size'       => '3.056em',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.2em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.64em',
					'margin-bottom'   => '1.05em',
				),
				'h2'      => array(
					'title'           => esc_html__( 'Heading 2', 'kidscare' ),
                    'font-family'     => '"Fredoka One",cursive',
					'font-size'       => '2.222em',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.21em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '2.1em',
					'margin-bottom'   => '1em',
				),
				'h3'      => array(
					'title'           => esc_html__( 'Heading 3', 'kidscare' ),
                    'font-family'     => '"Fredoka One",cursive',
					'font-size'       => '1.667em',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.19em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '2.2em',
					'margin-bottom'   => '1.44em',
				),
				'h4'      => array(
					'title'           => esc_html__( 'Heading 4', 'kidscare' ),
                    'font-family'     => '"Ubuntu",sans-serif',
					'font-size'       => '1.333em',
					'font-weight'     => '700',
					'font-style'      => 'normal',
					'line-height'     => '1.4em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '2.8em',
					'margin-bottom'   => '1.3em',
				),
				'h5'      => array(
					'title'           => esc_html__( 'Heading 5', 'kidscare' ),
                    'font-family'     => '"Fredoka One",cursive',
					'font-size'       => '1.222em',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.23em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '2.14em',
					'margin-bottom'   => '1.055em',
				),
				'h6'      => array(
					'title'           => esc_html__( 'Heading 6', 'kidscare' ),
                    'font-family'     => '"Ubuntu",sans-serif',
					'font-size'       => '1.111em',
					'font-weight'     => '700',
					'font-style'      => 'normal',
					'line-height'     => '1.22em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '2.5em',
					'margin-bottom'   => '0.7em',
				),
				'logo'    => array(
					'title'           => esc_html__( 'Logo text', 'kidscare' ),
					'description'     => esc_html__( 'Font settings of the text case of the logo', 'kidscare' ),
                    'font-family'     => '"Ubuntu",sans-serif',
					'font-size'       => '1.8em',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.25em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '1px',
				),
				'button'  => array(
					'title'           => esc_html__( 'Buttons', 'kidscare' ),
                    'font-family'     => '"Fredoka One",cursive',
					'font-size'       => '18px',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '22px',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '0',
				),
				'input'   => array(
					'title'           => esc_html__( 'Input fields', 'kidscare' ),
					'description'     => esc_html__( 'Font settings of the input fields, dropdowns and textareas', 'kidscare' ),
					'font-family'     => 'inherit',
					'font-size'       => '1em',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.4em', // Attention! Firefox don't allow line-height less then 1.5em in the select
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
				'info'    => array(
					'title'           => esc_html__( 'Post meta', 'kidscare' ),
					'description'     => esc_html__( 'Font settings of the post meta: date, counters, share, etc.', 'kidscare' ),
					'font-family'     => 'inherit',
					'font-size'       => '12px',  // Old value '13px' don't allow using 'font zoom' in the custom blog items
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '0.6px',
					'margin-top'      => '0.4em',
					'margin-bottom'   => '',
				),
				'menu'    => array(
					'title'           => esc_html__( 'Main menu', 'kidscare' ),
					'description'     => esc_html__( 'Font settings of the main menu items', 'kidscare' ),
                    'font-family'     => '"Ubuntu",sans-serif',
					'font-size'       => '13px',
					'font-weight'     => '700',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '0px',
				),
				'submenu' => array(
					'title'           => esc_html__( 'Dropdown menu', 'kidscare' ),
					'description'     => esc_html__( 'Font settings of the dropdown menu items', 'kidscare' ),
                    'font-family'     => '"Ubuntu",sans-serif',
					'font-size'       => '13px',
					'font-weight'     => '700',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
			)
		);

		// -----------------------------------------------------------------
		// -- Theme colors for customizer
		// -- Attention! Inner scheme must be last in the array below
		// -----------------------------------------------------------------
		kidscare_storage_set(
			'scheme_color_groups', array(
				'main'    => array(
					'title'       => esc_html__( 'Main', 'kidscare' ),
					'description' => esc_html__( 'Colors of the main content area', 'kidscare' ),
				),
				'alter'   => array(
					'title'       => esc_html__( 'Alter', 'kidscare' ),
					'description' => esc_html__( 'Colors of the alternative blocks (sidebars, etc.)', 'kidscare' ),
				),
				'extra'   => array(
					'title'       => esc_html__( 'Extra', 'kidscare' ),
					'description' => esc_html__( 'Colors of the extra blocks (dropdowns, price blocks, table headers, etc.)', 'kidscare' ),
				),
				'inverse' => array(
					'title'       => esc_html__( 'Inverse', 'kidscare' ),
					'description' => esc_html__( 'Colors of the inverse blocks - when link color used as background of the block (dropdowns, blockquotes, etc.)', 'kidscare' ),
				),
				'input'   => array(
					'title'       => esc_html__( 'Input', 'kidscare' ),
					'description' => esc_html__( 'Colors of the form fields (text field, textarea, select, etc.)', 'kidscare' ),
				),
			)
		);
		kidscare_storage_set(
			'scheme_color_names', array(
				'bg_color'    => array(
					'title'       => esc_html__( 'Background color', 'kidscare' ),
					'description' => esc_html__( 'Background color of this block in the normal state', 'kidscare' ),
				),
				'bg_hover'    => array(
					'title'       => esc_html__( 'Background hover', 'kidscare' ),
					'description' => esc_html__( 'Background color of this block in the hovered state', 'kidscare' ),
				),
				'bd_color'    => array(
					'title'       => esc_html__( 'Border color', 'kidscare' ),
					'description' => esc_html__( 'Border color of this block in the normal state', 'kidscare' ),
				),
				'bd_hover'    => array(
					'title'       => esc_html__( 'Border hover', 'kidscare' ),
					'description' => esc_html__( 'Border color of this block in the hovered state', 'kidscare' ),
				),
				'text'        => array(
					'title'       => esc_html__( 'Text', 'kidscare' ),
					'description' => esc_html__( 'Color of the plain text inside this block', 'kidscare' ),
				),
				'text_dark'   => array(
					'title'       => esc_html__( 'Text dark', 'kidscare' ),
					'description' => esc_html__( 'Color of the dark text (bold, header, etc.) inside this block', 'kidscare' ),
				),
				'text_light'  => array(
					'title'       => esc_html__( 'Text light', 'kidscare' ),
					'description' => esc_html__( 'Color of the light text (post meta, etc.) inside this block', 'kidscare' ),
				),
				'text_link'   => array(
					'title'       => esc_html__( 'Link', 'kidscare' ),
					'description' => esc_html__( 'Color of the links inside this block', 'kidscare' ),
				),
				'text_hover'  => array(
					'title'       => esc_html__( 'Link hover', 'kidscare' ),
					'description' => esc_html__( 'Color of the hovered state of links inside this block', 'kidscare' ),
				),
				'text_link2'  => array(
					'title'       => esc_html__( 'Link 2', 'kidscare' ),
					'description' => esc_html__( 'Color of the accented texts (areas) inside this block', 'kidscare' ),
				),
				'text_hover2' => array(
					'title'       => esc_html__( 'Link 2 hover', 'kidscare' ),
					'description' => esc_html__( 'Color of the hovered state of accented texts (areas) inside this block', 'kidscare' ),
				),
				'text_link3'  => array(
					'title'       => esc_html__( 'Link 3', 'kidscare' ),
					'description' => esc_html__( 'Color of the other accented texts (buttons) inside this block', 'kidscare' ),
				),
				'text_hover3' => array(
					'title'       => esc_html__( 'Link 3 hover', 'kidscare' ),
					'description' => esc_html__( 'Color of the hovered state of other accented texts (buttons) inside this block', 'kidscare' ),
				),
			)
		);
		$schemes = array(

			// Color scheme: 'default'
			'default' => array(
				'title'    => esc_html__( 'Default', 'kidscare' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#ffffff', //ok
					'bd_color'         => '#f0f0f0', //ok

					// Text and links colors
					'text'             => '#4a4f53', //ok
					'text_light'       => '#969899', //ok
					'text_dark'        => '#292929', //ok
					'text_link'        => '#ef5d50', //ok
					'text_hover'       => '#09539a', //ok
					'text_link2'       => '#ffb400', //ok
					'text_hover2'      => '#70bc4f', //ok
					'text_link3'       => '#fff267', //ok
					'text_hover3'      => '#8ed1fc', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#f0f0f0', //ok
					'alter_bg_hover'   => '#fbfbfb', //ok
					'alter_bd_color'   => '#ffffff', //ok
					'alter_bd_hover'   => '#e3e3e3', //ok
					'alter_text'       => '#333333',
					'alter_light'      => '#b7b7b7',
					'alter_dark'       => '#023661', //ok
					'alter_link'       => '#ef5d50', //ok
					'alter_hover'      => '#09539a', //ok
					'alter_link2'      => '#6ad8d4', //ok
					'alter_hover2'     => '#ff9229', //ok
					'alter_link3'      => '#08c7c0', //ok
					'alter_hover3'     => '#0abdb6', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#ef5d50', //ok
					'extra_bg_hover'   => '#28272e',
					'extra_bd_color'   => '#f77469', //ok
					'extra_bd_hover'   => '#3d3d3d',
					'extra_text'       => '#bfbfbf',
					'extra_light'      => '#afafaf',
					'extra_dark'       => '#ffffff',
					'extra_link'       => '#72cfd5',
					'extra_hover'      => '#fe7259',
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => '#ffffff', //ok
					'input_bg_hover'   => '#ffffff', //ok
					'input_bd_color'   => '#f0f0f0', //ok
					'input_bd_hover'   => '#ef5d50', //ok
					'input_text'       => '#969899', //ok
					'input_light'      => '#969899', //ok
					'input_dark'       => '#2b2e30', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#67bcc1',
					'inverse_bd_hover' => '#5aa4a9',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#292929',
					'inverse_link'     => '#ffffff',
					'inverse_hover'    => '#1d1d1d',
				),
			),

			// Color scheme: 'dark'
			'dark'    => array(
				'title'    => esc_html__( 'Dark', 'kidscare' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#2b2e30', //ok
					'bd_color'         => '#474747', //ok

					// Text and links colors
					'text'             => '#969899', //ok
					'text_light'       => '#b6b9ba', //ok
					'text_dark'        => '#ffffff',
                    'text_link'        => '#ef5d50', //ok
                    'text_hover'       => '#09539a', //ok
                    'text_link2'       => '#ffb400', //ok
                    'text_hover2'      => '#70bc4f', //ok
                    'text_link3'       => '#fff267', //ok
                    'text_hover3'      => '#8ed1fc', //ok

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#252729', //ok
					'alter_bg_hover'   => '#333333',
					'alter_bd_color'   => '#464646',
					'alter_bd_hover'   => '#4a4a4a',
					'alter_text'       => '#969899', //ok
					'alter_light'      => '#b6b9ba', //ok
					'alter_dark'       => '#ffffff',
                    'alter_link'       => '#ef5d50', //ok
                    'alter_hover'      => '#09539a', //ok
                    'alter_link2'      => '#6ad8d4', //ok
                    'alter_hover2'     => '#ff9229', //ok
                    'alter_link3'      => '#08c7c0', //ok
                    'alter_hover3'     => '#0abdb6', //ok

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#ef5d50', //ok
					'extra_bg_hover'   => '#28272e',
					'extra_bd_color'   => '#464646',
					'extra_bd_hover'   => '#4a4a4a',
					'extra_text'       => '#a6a6a6',
					'extra_light'      => '#6f6f6f',
					'extra_dark'       => '#ffffff',
                    'extra_link'       => '#72cfd5',
                    'extra_hover'      => '#fe7259',
                    'extra_link2'      => '#80d572',
                    'extra_hover2'     => '#8be77c',
                    'extra_link3'      => '#ddb837',
                    'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => '#252729', //ok
					'input_bg_hover'   => '#252729', //ok
					'input_bd_color'   => '#252729', //ok
					'input_bd_hover'   => '#4a4f53', //ok
                    'input_text'       => '#b6b9ba', //ok
                    'input_light'      => '#b6b9ba', //ok
                    'input_dark'       => '#ffffff', //ok

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#e36650',
					'inverse_bd_hover' => '#cb5b47',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#6f6f6f',
					'inverse_dark'     => '#292929',
					'inverse_link'     => '#ffffff',
					'inverse_hover'    => '#1d1d1d',
				),
			),
		);
		kidscare_storage_set( 'schemes', $schemes );
		kidscare_storage_set( 'schemes_original', $schemes );
		
		// Simple scheme editor: lists the colors to edit in the "Simple" mode.
		// For each color you can set the array of 'slave' colors and brightness factors that are used to generate new values,
		// when 'main' color is changed
		// Leave 'slave' arrays empty if your scheme does not have a color dependency
		kidscare_storage_set(
			'schemes_simple', array(
				'text_link'        => array(),
				'text_hover'       => array(),
				'text_link2'       => array(),
				'text_hover2'      => array(),
				'text_link3'       => array(),
				'text_hover3'      => array(),
				'alter_link'       => array(),
				'alter_hover'      => array(),
				'alter_link2'      => array(),
				'alter_hover2'     => array(),
				'alter_link3'      => array(),
				'alter_hover3'     => array(),
				'extra_link'       => array(),
				'extra_hover'      => array(),
				'extra_link2'      => array(),
				'extra_hover2'     => array(),
				'extra_link3'      => array(),
				'extra_hover3'     => array(),
				'inverse_bd_color' => array(),
				'inverse_bd_hover' => array(),
			)
		);

		// Additional colors for each scheme
		// Parameters:	'color' - name of the color from the scheme that should be used as source for the transformation
		//				'alpha' - to make color transparent (0.0 - 1.0)
		//				'hue', 'saturation', 'brightness' - inc/dec value for each color's component
		kidscare_storage_set(
			'scheme_colors_add', array(
				'bg_color_0'        => array(
					'color' => 'bg_color',
					'alpha' => 0,
				),
				'bg_color_02'       => array(
					'color' => 'bg_color',
					'alpha' => 0.2,
				),
				'bg_color_07'       => array(
					'color' => 'bg_color',
					'alpha' => 0.7,
				),
				'bg_color_08'       => array(
					'color' => 'bg_color',
					'alpha' => 0.8,
				),
				'bg_color_09'       => array(
					'color' => 'bg_color',
					'alpha' => 0.9,
				),
				'alter_bg_color_07' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.7,
				),
				'alter_bg_color_04' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.4,
				),
				'alter_bg_color_00' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0,
				),
				'alter_bg_color_02' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.2,
				),
				'alter_bd_color_02' => array(
					'color' => 'alter_bd_color',
					'alpha' => 0.2,
				),
				'alter_link_02'     => array(
					'color' => 'alter_link',
					'alpha' => 0.2,
				),
				'alter_link_07'     => array(
					'color' => 'alter_link',
					'alpha' => 0.7,
				),
				'extra_bg_color_05' => array(
					'color' => 'extra_bg_color',
					'alpha' => 0.5,
				),
				'extra_bg_color_07' => array(
					'color' => 'extra_bg_color',
					'alpha' => 0.7,
				),
				'extra_link_02'     => array(
					'color' => 'extra_link',
					'alpha' => 0.2,
				),
				'extra_link_07'     => array(
					'color' => 'extra_link',
					'alpha' => 0.7,
				),
				'text_dark_01'      => array(
					'color' => 'text_dark',
					'alpha' => 0.1,
				),
				'text_dark_04'      => array(
					'color' => 'text_dark',
					'alpha' => 0.4,
				),
				'text_dark_07'      => array(
						'color' => 'text_dark',
						'alpha' => 0.7,
				),
				'text_link_02'      => array(
					'color' => 'text_link',
					'alpha' => 0.2,
				),
				'text_link_07'      => array(
					'color' => 'text_link',
					'alpha' => 0.7,
				),
                'text_hover_04'      => array(
                    'color' => 'text_hover',
                    'alpha' => 0.4,
                ),
                'text_hover_08'      => array(
                    'color' => 'text_hover',
                    'alpha' => 0.8,
                ),
				'text_link_blend'   => array(
					'color'      => 'text_link',
					'hue'        => 2,
					'saturation' => -5,
					'brightness' => 5,
				),
				'alter_link_blend'  => array(
					'color'      => 'alter_link',
					'hue'        => 2,
					'saturation' => -5,
					'brightness' => 5,
				),
			)
		);

		// Parameters to set order of schemes in the css
		kidscare_storage_set(
			'schemes_sorted', array(
				'color_scheme',
				'header_scheme',
				'menu_scheme',
				'sidebar_scheme',
				'footer_scheme',
			)
		);

		// -----------------------------------------------------------------
		// -- Theme specific thumb sizes
		// -----------------------------------------------------------------
		kidscare_storage_set(
			'theme_thumbs', apply_filters(
				'kidscare_filter_add_thumb_sizes', array(
					// Width of the image is equal to the content area width (without sidebar)
					// Height is fixed
					'kidscare-thumb-huge'        => array(
						'size'  => array( 1170, 658, true ),
						'title' => esc_html__( 'Huge image', 'kidscare' ),
						'subst' => 'trx_addons-thumb-huge',
					),
					// Width of the image is equal to the content area width (with sidebar)
					// Height is fixed
					'kidscare-thumb-big'         => array(
						'size'  => array( 760, 428, true ),
						'title' => esc_html__( 'Large image', 'kidscare' ),
						'subst' => 'trx_addons-thumb-big',
					),

					// Width of the image is equal to the 1/3 of the content area width (without sidebar)
					// Height is fixed
					'kidscare-thumb-med'         => array(
						'size'  => array( 370, 208, true ),
						'title' => esc_html__( 'Medium image', 'kidscare' ),
						'subst' => 'trx_addons-thumb-medium',
					),

					// Small square image (for avatars in comments, etc.)
					'kidscare-thumb-tiny'        => array(
						'size'  => array( 90, 90, true ),
						'title' => esc_html__( 'Small square avatar', 'kidscare' ),
						'subst' => 'trx_addons-thumb-tiny',
					),

					// Width of the image is equal to the content area width (with sidebar)
					// Height is proportional (only downscale, not crop)
					'kidscare-thumb-masonry-big' => array(
						'size'  => array( 760, 0, false ),     // Only downscale, not crop
						'title' => esc_html__( 'Masonry Large (scaled)', 'kidscare' ),
						'subst' => 'trx_addons-thumb-masonry-big',
					),

					// Width of the image is equal to the 1/3 of the full content area width (without sidebar)
					// Height is proportional (only downscale, not crop)
					'kidscare-thumb-masonry'     => array(
						'size'  => array( 370, 0, false ),     // Only downscale, not crop
						'title' => esc_html__( 'Masonry (scaled)', 'kidscare' ),
						'subst' => 'trx_addons-thumb-masonry',
					),

                    'kidscare-thumb-height'         => array(
                        'size'  => array( 270, 340, true ),
                        'title' => esc_html__( 'Height image', 'kidscare' ),
                        'subst' => 'trx_addons-thumb-height',
                    ),
                    'kidscare-thumb-extra'         => array(
                        'size'  => array( 370, 243, true ),
                        'title' => esc_html__( 'Extra image', 'kidscare' ),
                        'subst' => 'trx_addons-thumb-extra',
                    ),
				)
			)
		);
	}
}




//------------------------------------------------------------------------
// One-click import support
//------------------------------------------------------------------------

// Set theme specific importer options
if ( ! function_exists( 'kidscare_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'kidscare_importer_set_options', 9 );
	function kidscare_importer_set_options( $options = array() ) {
		if ( is_array( $options ) ) {
			// Save or not installer's messages to the log-file
			$options['debug'] = false;
			// Allow import/export functionality
			$options['allow_import'] = true;
			$options['allow_export'] = false;
			// Prepare demo data
			$options['demo_url'] = esc_url( kidscare_get_protocol() . '://demofiles.axiomthemes.com/kidscare-new/' );
			// Required plugins
			$options['required_plugins'] = array_keys( kidscare_storage_get( 'required_plugins' ) );
			// Set number of thumbnails (usually 3 - 5) to regenerate at once when its imported (if demo data was zipped without cropped images)
			// Set 0 to prevent regenerate thumbnails (if demo data archive is already contain cropped images)
			$options['regenerate_thumbnails'] = 0;
			// Default demo
			$options['files']['default']['title']       = esc_html__( 'KidsCare Demo', 'kidscare' );
			$options['files']['default']['domain_dev']  = kidscare_add_protocol( '' );                // Developers domain
			$options['files']['default']['domain_demo'] = kidscare_add_protocol( kidscare_storage_get( 'theme_demo_url' ) );   // Demo-site domain

			// The array with theme-specific banners, displayed during demo-content import.
			// If array with banners is empty - the banners are uploaded directly from demo-content server.
			$options['banners'] = array();
		}
		return $options;
	}
}


//------------------------------------------------------------------------
// OCDI support
//------------------------------------------------------------------------

// Set theme specific OCDI options
if ( ! function_exists( 'kidscare_ocdi_set_options' ) ) {
	add_filter( 'trx_addons_filter_ocdi_options', 'kidscare_ocdi_set_options', 9 );
	function kidscare_ocdi_set_options( $options = array() ) {
		if ( is_array( $options ) ) {
			// Prepare demo data
			$options['demo_url'] = esc_url( kidscare_get_protocol() . '://demofiles.axiomthemes.com/kidscare-new/' );
			// Required plugins
			$options['required_plugins'] = array_keys( kidscare_storage_get( 'required_plugins' ) );
			// Demo-site domain
			$options['files']['ocdi']['title']       = esc_html__( 'KidsCare OCDI Demo', 'kidscare' );
			$options['files']['ocdi']['domain_demo'] = esc_url( kidscare_get_protocol() . '://kidscare.axiomthemes.com' );
		}
		return $options;
	}
}


// -----------------------------------------------------------------
// -- Theme options for customizer
// -----------------------------------------------------------------
if ( ! function_exists( 'kidscare_create_theme_options' ) ) {

	function kidscare_create_theme_options() {

		// Message about options override.
		// Attention! Not need esc_html() here, because this message put in wp_kses_data() below
		$msg_override = esc_html__( 'Attention! Some of these options can be overridden in the following sections (Blog, Plugins settings, etc.) or in the settings of individual pages. If you changed such parameter and nothing happened on the page, this option may be overridden in the corresponding section or in the Page Options of this page. These options are marked with an asterisk (*) in the title.', 'kidscare' );

		// Color schemes number: if < 2 - hide fields with selectors
		$hide_schemes = count( kidscare_storage_get( 'schemes' ) ) < 2;

		kidscare_storage_set(

			'options', array(

				// 'Logo & Site Identity'
				//---------------------------------------------
				'title_tagline'                 => array(
					'title'    => esc_html__( 'Logo & Site Identity', 'kidscare' ),
					'desc'     => '',
					'priority' => 10,
					'type'     => 'section',
				),
				'logo_info'                     => array(
					'title'    => esc_html__( 'Logo Settings', 'kidscare' ),
					'desc'     => '',
					'priority' => 20,
					'qsetup'   => esc_html__( 'General', 'kidscare' ),
					'type'     => 'info',
				),
				'logo_text'                     => array(
					'title'    => esc_html__( 'Use Site Name as Logo', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Use the site title and tagline as a text logo if no image is selected', 'kidscare' ) ),
					'class'    => 'kidscare_column-1_2 kidscare_new_row',
					'priority' => 30,
					'std'      => 1,
					'qsetup'   => esc_html__( 'General', 'kidscare' ),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
				'logo_retina_enabled'           => array(
					'title'    => esc_html__( 'Allow retina display logo', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Show fields to select logo images for Retina display', 'kidscare' ) ),
					'class'    => 'kidscare_column-1_2',
					'priority' => 40,
					'refresh'  => false,
					'std'      => 0,
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
				'logo_zoom'                     => array(
					'title'   => esc_html__( 'Logo zoom', 'kidscare' ),
					'desc'    => wp_kses(
									__( 'Zoom the logo (set 1 to leave original size).', 'kidscare' )
									. ' <br>'
									. __( 'Attention! For this parameter to affect images, their max-height should be specified in "em" instead of "px" when creating a header.', 'kidscare' )
									. ' <br>'
									. __( 'In this case maximum size of logo depends on the actual size of the picture.', 'kidscare' ), 'kidscare_kses_content'
								),
					'std'     => 1,
					'min'     => 0.2,
					'max'     => 2,
					'step'    => 0.1,
					'refresh' => false,
					'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'slider',
				),
				// Parameter 'logo' was replaced with standard WordPress 'custom_logo'
				'logo_retina'                   => array(
					'title'      => esc_html__( 'Logo for Retina', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'kidscare' ) ),
					'class'      => 'kidscare_column-1_2',
					'priority'   => 70,
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'image',
				),
				'logo_mobile_header'            => array(
					'title' => esc_html__( 'Logo for the mobile header', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Select or upload site logo to display it in the mobile header (if enabled in the section "Header - Header mobile"', 'kidscare' ) ),
					'class' => 'kidscare_column-1_2 kidscare_new_row',
					'std'   => '',
					'type'  => 'image',
				),
				'logo_mobile_header_retina'     => array(
					'title'      => esc_html__( 'Logo for the mobile header on Retina', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'kidscare' ) ),
					'class'      => 'kidscare_column-1_2',
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'image',
				),
				'logo_mobile'                   => array(
					'title' => esc_html__( 'Logo for the mobile menu', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Select or upload site logo to display it in the mobile menu', 'kidscare' ) ),
					'class' => 'kidscare_column-1_2 kidscare_new_row',
					'std'   => '',
					'type'  => 'image',
				),
				'logo_mobile_retina'            => array(
					'title'      => esc_html__( 'Logo mobile on Retina', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'kidscare' ) ),
					'class'      => 'kidscare_column-1_2',
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'image',
				),
				'logo_side'                     => array(
					'title' => esc_html__( 'Logo for the side menu', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Select or upload site logo (with vertical orientation) to display it in the side menu', 'kidscare' ) ),
					'class' => 'kidscare_column-1_2 kidscare_new_row',
					'std'   => '',
					'type'  => 'hidden',
				),
				'logo_side_retina'              => array(
					'title'      => esc_html__( 'Logo for the side menu on Retina', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo (with vertical orientation) to display it in the side menu on Retina displays (if empty - use default logo from the field above)', 'kidscare' ) ),
					'class'      => 'kidscare_column-1_2',
					'dependency' => array(
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
                    'type'  => 'hidden',
				),



				// 'General settings'
				//---------------------------------------------
				'general'                       => array(
					'title'    => esc_html__( 'General', 'kidscare' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 20,
					'type'     => 'section',
				),

				'general_layout_info'           => array(
					'title'  => esc_html__( 'Layout', 'kidscare' ),
					'desc'   => '',
					'qsetup' => esc_html__( 'General', 'kidscare' ),
					'type'   => 'info',
				),
				'body_style'                    => array(
					'title'    => esc_html__( 'Body style', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select width of the body content', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'qsetup'   => esc_html__( 'General', 'kidscare' ),
					'refresh'  => false,
					'std'      => 'wide',
					'options'  => kidscare_get_list_body_styles( true, true ),
					'type'     => 'select',
				),
				'page_width'                    => array(
					'title'      => esc_html__( 'Page width', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Total width of the site content and sidebar (in pixels). If empty - use default width', 'kidscare' ) ),
					'dependency' => array(
						'body_style' => array( 'boxed', 'wide' ),
					),
					'std'        => 1170,
					'min'        => 1000,
					'max'        => 1600,
					'step'       => 10,
					'refresh'    => false,
					'customizer' => 'page',               // SASS variable's name to preview changes 'on fly'
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'slider',
				),
				'page_boxed_extra'             => array(
					'title'      => esc_html__( 'Boxed page extra spaces', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Width of the extra side space on boxed pages', 'kidscare' ) ),
					'dependency' => array(
						'body_style' => array( 'boxed' ),
					),
					'std'        => 60,
					'min'        => 0,
					'max'        => 150,
					'step'       => 10,
					'refresh'    => false,
					'customizer' => 'page_boxed_extra',   // SASS variable's name to preview changes 'on fly'
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'slider',
				),
				'boxed_bg_image'                => array(
					'title'      => esc_html__( 'Boxed bg image', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select or upload image, used as background in the boxed body', 'kidscare' ) ),
					'dependency' => array(
						'body_style' => array( 'boxed' ),
					),
					'override'   => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'std'        => '',
					'qsetup'     => esc_html__( 'General', 'kidscare' ),
					'type'       => 'image',
				),
				'remove_margins'                => array(
					'title'    => esc_html__( 'Remove margins', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Remove margins above and below the content area', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'refresh'  => false,
					'std'      => 0,
					'type'     => 'checkbox',
				),

				'general_sidebar_info'          => array(
					'title' => esc_html__( 'Sidebar', 'kidscare' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position'              => array(
					'title'    => esc_html__( 'Sidebar position', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select position to show sidebar', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_single'
						'section' => esc_html__( 'Widgets', 'kidscare' ),
					),
					'std'      => 'right',
					'qsetup'   => esc_html__( 'General', 'kidscare' ),
					'options'  => array(),
					'type'     => 'switch',
				),
				'sidebar_position_ss'       => array(
					'title'    => esc_html__( 'Sidebar position on the small screen', 'kidscare' ),
					'desc'     => wp_kses_data( __( "Select position to move sidebar (if it's not hidden) on the small screen - above or below the content", 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_position_ss_single'
						'section' => esc_html__( 'Widgets', 'kidscare' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
					),
					'std'      => 'below',
					'qsetup'   => esc_html__( 'General', 'kidscare' ),
					'options'  => array(),
					'type'     => 'switch',
				),
				'sidebar_widgets'               => array(
					'title'      => esc_html__( 'Sidebar widgets', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page',		// Override parameters for single posts moved to the 'sidebar_widgets_single'
						'section' => esc_html__( 'Widgets', 'kidscare' ),
					),
					'dependency' => array(
						'sidebar_position' => array( '^hide' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'qsetup'     => esc_html__( 'General', 'kidscare' ),
					'type'       => 'select',
				),
				'sidebar_width'                 => array(
					'title'      => esc_html__( 'Sidebar width', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Width of the sidebar (in pixels). If empty - use default width', 'kidscare' ) ),
					'std'        => 370,
					'min'        => 150,
					'max'        => 500,
					'step'       => 10,
					'refresh'    => false,
					'customizer' => 'sidebar',      // SASS variable's name to preview changes 'on fly'
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'slider',
				),
				'sidebar_gap'                   => array(
					'title'      => esc_html__( 'Sidebar gap', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Gap between content and sidebar (in pixels). If empty - use default gap', 'kidscare' ) ),
					'std'        => 40,
					'min'        => 0,
					'max'        => 100,
					'step'       => 1,
					'refresh'    => false,
					'customizer' => 'gap',          // SASS variable's name to preview changes 'on fly'
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'slider',
				),
				'expand_content'                => array(
					'title'   => esc_html__( 'Expand content', 'kidscare' ),
					'desc'    => wp_kses_data( __( 'Expand the content width if the sidebar is hidden', 'kidscare' ) ),
					'refresh' => false,
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'kidscare' ),
					),
					'std'     => 1,
					'type'    => 'checkbox',
				),

				'general_widgets_info'          => array(
					'title' => esc_html__( 'Additional widgets', 'kidscare' ),
					'desc'  => '',
					'type'  => KIDSCARE_THEME_FREE ? 'hidden' : 'info',
				),
				'widgets_above_page'            => array(
					'title'    => esc_html__( 'Widgets at the top of the page', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'kidscare' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
				),
				'widgets_above_content'         => array(
					'title'    => esc_html__( 'Widgets above the content', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'kidscare' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
				),
				'widgets_below_content'         => array(
					'title'    => esc_html__( 'Widgets below the content', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'kidscare' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
				),
				'widgets_below_page'            => array(
					'title'    => esc_html__( 'Widgets at the bottom of the page', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'kidscare' ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
				),

				'general_effects_info'          => array(
					'title' => esc_html__( 'Design & Effects', 'kidscare' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'border_radius'                 => array(
					'title'      => esc_html__( 'Border radius', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Specify the border radius of the form fields and buttons in pixels', 'kidscare' ) ),
					'std'        => 0,
					'min'        => 0,
					'max'        => 20,
					'step'       => 1,
					'refresh'    => false,
					'customizer' => 'rad',      // SASS name to preview changes 'on fly'
                    'type'       => 'hidden',
				),

				'general_misc_info'             => array(
					'title' => esc_html__( 'Miscellaneous', 'kidscare' ),
					'desc'  => '',
					'type'  => KIDSCARE_THEME_FREE ? 'hidden' : 'info',
				),
				'seo_snippets'                  => array(
					'title' => esc_html__( 'SEO snippets', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Add structured data markup to the single posts and pages', 'kidscare' ) ),
					'std'   => 0,
					'type'  => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
				'privacy_text' => array(
					"title" => esc_html__("Text with Privacy Policy link", 'kidscare'),
					"desc"  => wp_kses_data( __("Specify text with Privacy Policy link for the checkbox 'I agree ...'", 'kidscare') ),
					"std"   => wp_kses( __( 'I agree that my submitted data is being collected and stored.', 'kidscare'), 'kidscare_kses_content' ),
					"type"  => "text"
				),



				// 'Header'
				//---------------------------------------------
				'header'                        => array(
					'title'    => esc_html__( 'Header', 'kidscare' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 30,
					'type'     => 'section',
				),

				'header_style_info'             => array(
					'title' => esc_html__( 'Header style', 'kidscare' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type'                   => array(
					'title'    => esc_html__( 'Header style', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'kidscare' ),
					),
					'std'      => 'default',
					'options'  => kidscare_get_list_header_footer_types(),
					'type'     => KIDSCARE_THEME_FREE || ! kidscare_exists_trx_addons() ? 'hidden' : 'switch',
				),
				'header_style'                  => array(
					'title'      => esc_html__( 'Select custom layout', 'kidscare' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'kidscare' ), 'kidscare_kses_content' ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'kidscare' ),
					),
					'dependency' => array(
						'header_type' => array( 'custom' ),
					),
					'std'        => 'header-custom-elementor-header-default',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position'               => array(
					'title'    => esc_html__( 'Header position', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'kidscare' ),
					),
					'std'      => 'default',
					'options'  => array(),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
				),
				'header_fullheight'             => array(
					'title'    => esc_html__( 'Header fullheight', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Enlarge header area to fill the whole screen. Used only if the header has a background image', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'kidscare' ),
					),
					'std'      => 0,
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
				'header_wide'                   => array(
					'title'      => esc_html__( 'Header fullwidth', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'kidscare' ),
					),
					'dependency' => array(
						'header_type' => array( 'default' ),
					),
					'std'        => 1,
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
				'header_zoom'                   => array(
					'title'   => esc_html__( 'Header zoom', 'kidscare' ),
					'desc'    => wp_kses_data( __( 'Zoom the header title. 1 - original size', 'kidscare' ) ),
					'std'     => 1,
					'min'     => 0.2,
					'max'     => 2,
					'step'    => 0.1,
					'refresh' => false,
					'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'slider',
				),

				'header_widgets_info'           => array(
					'title' => esc_html__( 'Header widgets', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Here you can place a widget slider, advertising banners, etc.', 'kidscare' ) ),
					'type'  => 'info',
				),
				'header_widgets'                => array(
					'title'    => esc_html__( 'Header widgets', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select set of widgets to show in the header on each page', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'kidscare' ),
						'desc'    => wp_kses_data( __( 'Select set of widgets to show in the header on this page', 'kidscare' ) ),
					),
					'std'      => 'hide',
					'options'  => array(),
					'type'     => 'select',
				),
				'header_columns'                => array(
					'title'      => esc_html__( 'Header columns', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the Header. If 0 - autodetect by the widgets count', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'kidscare' ),
					),
					'dependency' => array(
						'header_widgets' => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => kidscare_get_list_range( 0, 6 ),
					'type'       => 'select',
				),

				'menu_info'                     => array(
					'title' => esc_html__( 'Main menu', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Select main menu style, position and other parameters', 'kidscare' ) ),
					'type'  => KIDSCARE_THEME_FREE ? 'hidden' : 'info',
				),
				'menu_style'                    => array(
					'title'    => esc_html__( 'Menu position', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select position of the main menu', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'kidscare' ),
					),
					'std'      => 'top',
					'options'  => array(
						'top'   => esc_html__( 'Top', 'kidscare' ),
					),
					'type'     => KIDSCARE_THEME_FREE || ! kidscare_exists_trx_addons() ? 'hidden' : 'switch',
				),
				'menu_side_stretch'             => array(
					'title'      => esc_html__( 'Stretch sidemenu', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Stretch sidemenu to window height (if menu items number >= 5)', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'kidscare' ),
					),
					'dependency' => array(
						'menu_style' => array( 'left', 'right' ),
					),
					'std'        => 0,
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
				'menu_side_icons'               => array(
					'title'      => esc_html__( 'Iconed sidemenu', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Get icons from anchors and display it in the sidemenu or mark sidemenu items with simple dots', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Header', 'kidscare' ),
					),
					'dependency' => array(
						'menu_style' => array( 'left', 'right' ),
					),
					'std'        => 1,
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
				'menu_mobile_fullscreen'        => array(
					'title' => esc_html__( 'Mobile menu fullscreen', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Display mobile and side menus on full screen (if checked) or slide narrow menu from the left or from the right side (if not checked)', 'kidscare' ) ),
					'std'   => 1,
					'type'  => 'hidden',
				),

				'header_image_info'             => array(
					'title' => esc_html__( 'Header image', 'kidscare' ),
					'desc'  => '',
					'type'  => KIDSCARE_THEME_FREE ? 'hidden' : 'info',
				),
				'header_image_override'         => array(
					'title'    => esc_html__( 'Header image override', 'kidscare' ),
					'desc'     => wp_kses_data( __( "Allow override the header image with the page's/post's/product's/etc. featured image", 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Header', 'kidscare' ),
					),
					'std'      => 0,
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),

				'header_mobile_info'            => array(
					'title'      => esc_html__( 'Mobile header', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Configure the mobile version of the header', 'kidscare' ) ),
					'priority'   => 500,
					'dependency' => array(
						'header_type' => array( 'default' ),
					),
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'info',
				),
				'header_mobile_enabled'         => array(
					'title'      => esc_html__( 'Enable the mobile header', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Use the mobile version of the header (if checked) or relayout the current header on mobile devices', 'kidscare' ) ),
					'dependency' => array(
						'header_type' => array( 'default' ),
					),
					'std'        => 0,
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
				'header_mobile_additional_info' => array(
					'title'      => esc_html__( 'Additional info', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Additional info to show at the top of the mobile header', 'kidscare' ) ),
					'std'        => '',
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'refresh'    => false,
					'teeny'      => false,
					'rows'       => 20,
					'type'       => 'hidden',
				),
				'header_mobile_hide_info'       => array(
					'title'      => esc_html__( 'Hide additional info', 'kidscare' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'type'       => 'hidden',
				),
				'header_mobile_hide_logo'       => array(
					'title'      => esc_html__( 'Hide logo', 'kidscare' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
				'header_mobile_hide_search'     => array(
					'title'      => esc_html__( 'Hide search', 'kidscare' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
				'header_mobile_hide_cart'       => array(
					'title'      => esc_html__( 'Hide cart', 'kidscare' ),
					'std'        => 0,
					'dependency' => array(
						'header_type'           => array( 'default' ),
						'header_mobile_enabled' => array( 1 ),
					),
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
                'header_mobile_hide_login'      => array(
                    'title'      => esc_html__( 'Hide login/logout', 'kidscare' ),
                    'std'        => 1,
                    'dependency' => array(
                        'header_type'           => array( 'default' ),
                        'header_mobile_enabled' => array( 1 ),
                    ),
                    'type'       => 'hidden',
                ),



				// 'Footer'
				//---------------------------------------------
				'footer'                        => array(
					'title'    => esc_html__( 'Footer', 'kidscare' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 50,
					'type'     => 'section',
				),
				'footer_type'                   => array(
					'title'    => esc_html__( 'Footer style', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'kidscare' ),
					),
					'std'      => 'default',
					'options'  => kidscare_get_list_header_footer_types(),
					'type'     => KIDSCARE_THEME_FREE || ! kidscare_exists_trx_addons() ? 'hidden' : 'switch',
				),
				'footer_style'                  => array(
					'title'      => esc_html__( 'Select custom layout', 'kidscare' ),
					'desc'       => wp_kses( __( 'Select custom footer from Layouts Builder', 'kidscare' ), 'kidscare_kses_content' ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'kidscare' ),
					),
					'dependency' => array(
						'footer_type' => array( 'custom' ),
					),
					'std'        => 'footer-custom-elementor-footer-default',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_widgets'                => array(
					'title'      => esc_html__( 'Footer widgets', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select set of widgets to show in the footer', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'kidscare' ),
					),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 'footer_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_columns'                => array(
					'title'      => esc_html__( 'Footer columns', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'kidscare' ),
					),
					'dependency' => array(
						'footer_type'    => array( 'default' ),
						'footer_widgets' => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => kidscare_get_list_range( 0, 6 ),
					'type'       => 'select',
				),
				'footer_wide'                   => array(
					'title'      => esc_html__( 'Footer fullwidth', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the footer to the entire window width?', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page,post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Footer', 'kidscare' ),
					),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 0,
					'type'       => 'checkbox',
				),
				'logo_in_footer'                => array(
					'title'      => esc_html__( 'Show logo', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Show logo in the footer', 'kidscare' ) ),
					'refresh'    => false,
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 0,
					'type'       => 'checkbox',
				),
				'logo_footer'                   => array(
					'title'      => esc_html__( 'Logo for footer', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select or upload site logo to display it in the footer', 'kidscare' ) ),
					'dependency' => array(
						'footer_type'    => array( 'default' ),
						'logo_in_footer' => array( 1 ),
					),
					'std'        => '',
					'type'       => 'image',
				),
				'logo_footer_retina'            => array(
					'title'      => esc_html__( 'Logo for footer (Retina)', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select or upload logo for the footer area used on Retina displays (if empty - use default logo from the field above)', 'kidscare' ) ),
					'dependency' => array(
						'footer_type'         => array( 'default' ),
						'logo_in_footer'      => array( 1 ),
						'logo_retina_enabled' => array( 1 ),
					),
					'std'        => '',
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'image',
				),
				'socials_in_footer'             => array(
					'title'      => esc_html__( 'Show social icons', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Show social icons in the footer (under logo or footer widgets)', 'kidscare' ) ),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'std'        => 0,
					'type'       => ! kidscare_exists_trx_addons() ? 'hidden' : 'checkbox',
				),
				'copyright'                     => array(
					'title'      => esc_html__( 'Copyright', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Copyright text in the footer. Use {Y} to insert current year and press "Enter" to create a new line', 'kidscare' ) ),
					'translate'  => true,
					'std'        => esc_html__( 'AxiomThemes &copy; {Y}. All rights reserved.', 'kidscare' ),
					'dependency' => array(
						'footer_type' => array( 'default' ),
					),
					'refresh'    => false,
					'type'       => 'textarea',
				),



				// 'Mobile version'
				//---------------------------------------------
				'mobile'                        => array(
					'title'    => esc_html__( 'Mobile', 'kidscare' ),
					'desc'     => wp_kses_data( $msg_override ),
					'priority' => 55,
					'type'     => 'section',
				),

				'mobile_header_info'            => array(
					'title' => esc_html__( 'Header on the mobile device', 'kidscare' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type_mobile'            => array(
					'title'    => esc_html__( 'Header style', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use on mobile devices: the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'kidscare' ) ),
					'std'      => 'inherit',
					'options'  => kidscare_get_list_header_footer_types( true ),
					'type'     => KIDSCARE_THEME_FREE || ! kidscare_exists_trx_addons() ? 'hidden' : 'switch',
				),
				'header_style_mobile'           => array(
					'title'      => esc_html__( 'Select custom layout', 'kidscare' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'kidscare' ), 'kidscare_kses_content' ),
					'dependency' => array(
						'header_type_mobile' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position_mobile'        => array(
					'title'    => esc_html__( 'Header position', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'kidscare' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
				),

				'mobile_sidebar_info'           => array(
					'title' => esc_html__( 'Sidebar on the mobile device', 'kidscare' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position_mobile'       => array(
					'title'    => esc_html__( 'Sidebar position on mobile', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select position to show sidebar on mobile devices', 'kidscare' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'type'     => 'switch',
				),
				'sidebar_widgets_mobile'        => array(
					'title'      => esc_html__( 'Sidebar widgets', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar on mobile devices', 'kidscare' ) ),
					'dependency' => array(
						'sidebar_position_mobile' => array( '^hide' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'expand_content_mobile'         => array(
					'title'   => esc_html__( 'Expand content', 'kidscare' ),
					'desc'    => wp_kses_data( __( 'Expand the content width if the sidebar is hidden on mobile devices', 'kidscare' ) ),
					'refresh' => false,
					'dependency' => array(
						'sidebar_position_mobile' => array( 'hide', 'inherit' ),
					),
					'std'     => 'inherit',
					'options'  => kidscare_get_list_checkbox_values( true ),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
				),

				'mobile_footer_info'           => array(
					'title' => esc_html__( 'Footer on the mobile device', 'kidscare' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'footer_type_mobile'           => array(
					'title'    => esc_html__( 'Footer style', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use on mobile devices: the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'kidscare' ) ),
					'std'      => 'inherit',
					'options'  => kidscare_get_list_header_footer_types( true ),
					'type'     => KIDSCARE_THEME_FREE || ! kidscare_exists_trx_addons() ? 'hidden' : 'switch',
				),
				'footer_style_mobile'          => array(
					'title'      => esc_html__( 'Select custom layout', 'kidscare' ),
					'desc'       => wp_kses( __( 'Select custom footer from Layouts Builder', 'kidscare' ), 'kidscare_kses_content' ),
					'dependency' => array(
						'footer_type_mobile' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_widgets_mobile'        => array(
					'title'      => esc_html__( 'Footer widgets', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select set of widgets to show in the footer', 'kidscare' ) ),
					'dependency' => array(
						'footer_type_mobile' => array( 'default' ),
					),
					'std'        => 'footer_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'footer_columns_mobile'        => array(
					'title'      => esc_html__( 'Footer columns', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'kidscare' ) ),
					'dependency' => array(
						'footer_type_mobile'    => array( 'default' ),
						'footer_widgets_mobile' => array( '^hide' ),
					),
					'std'        => 0,
					'options'    => kidscare_get_list_range( 0, 6 ),
					'type'       => 'select',
				),



				// 'Blog'
				//---------------------------------------------
				'blog'                          => array(
					'title'    => esc_html__( 'Blog', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Options of the the blog archive', 'kidscare' ) ),
					'priority' => 70,
					'type'     => 'panel',
				),


				// Blog - Posts page
				//---------------------------------------------
				'blog_general'                  => array(
					'title' => esc_html__( 'Posts page', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Style and components of the blog archive', 'kidscare' ) ),
					'type'  => 'section',
				),
				'blog_general_info'             => array(
					'title'  => esc_html__( 'Posts page settings', 'kidscare' ),
					'desc'   => '',
					'qsetup' => esc_html__( 'General', 'kidscare' ),
					'type'   => 'info',
				),
				'blog_style'                    => array(
					'title'      => esc_html__( 'Blog style', 'kidscare' ),
					'desc'       => '',
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'std'        => 'excerpt',
					'qsetup'     => esc_html__( 'General', 'kidscare' ),
					'options'    => array(),
					'type'       => 'select',
				),
				'first_post_large'              => array(
					'title'      => esc_html__( 'First post large', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Make your first post stand out by making it bigger', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
						'blog_style' => array( 'classic', 'masonry' ),
					),
					'std'        => 0,
					'type'       => 'hidden',
				),
				'blog_content'                  => array(
					'title'      => esc_html__( 'Posts content', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Display either post excerpts or the full post content', 'kidscare' ) ),
					'std'        => 'excerpt',
					'dependency' => array(
						'blog_style' => array( 'excerpt' ),
					),
					'options'    => array(
						'excerpt'  => esc_html__( 'Excerpt', 'kidscare' ),
						'fullpost' => esc_html__( 'Full post', 'kidscare' ),
					),
					'type'       => 'switch',
				),
				'excerpt_length'                => array(
					'title'      => esc_html__( 'Excerpt length', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Length (in words) to generate excerpt from the post content. Attention! If the post excerpt is explicitly specified - it appears unchanged', 'kidscare' ) ),
					'dependency' => array(
						'blog_style'   => array( 'excerpt' ),
						'blog_content' => array( 'excerpt' ),
					),
					'std'        => 40,
					'type'       => 'text',
				),
				'blog_columns'                  => array(
					'title'   => esc_html__( 'Blog columns', 'kidscare' ),
					'desc'    => wp_kses_data( __( 'How many columns should be used in the blog archive (from 2 to 4)?', 'kidscare' ) ),
					'std'     => 2,
					'options' => kidscare_get_list_range( 2, 4 ),
					'type'    => 'hidden',      // This options is available and must be overriden only for some modes (for example, 'shop')
				),
				'post_type'                     => array(
					'title'      => esc_html__( 'Post type', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select post type to show in the blog archive', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'linked'     => 'parent_cat',
					'refresh'    => false,
					'hidden'     => true,
					'std'        => 'post',
					'options'    => array(),
					'type'       => 'select',
				),
				'parent_cat'                    => array(
					'title'      => esc_html__( 'Category to show', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select category to show in the blog archive', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'refresh'    => false,
					'hidden'     => true,
					'std'        => '0',
					'options'    => array(),
					'type'       => 'select',
				),
				'posts_per_page'                => array(
					'title'      => esc_html__( 'Posts per page', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'How many posts will be displayed on this page', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'hidden'     => true,
					'std'        => '',
					'type'       => 'text',
				),
				'blog_pagination'               => array(
					'title'      => esc_html__( 'Pagination style', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Show Older/Newest posts or Page numbers below the posts list', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'std'        => 'pages',
					'qsetup'     => esc_html__( 'General', 'kidscare' ),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'options'    => array(
						'pages'    => esc_html__( 'Page numbers', 'kidscare' ),
						'links'    => esc_html__( 'Older/Newest', 'kidscare' ),
						'more'     => esc_html__( 'Load more', 'kidscare' ),
						'infinite' => esc_html__( 'Infinite scroll', 'kidscare' ),
					),
					'type'       => 'select',
				),
				'blog_animation'                => array(
					'title'      => esc_html__( 'Post animation', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select animation to show posts in the blog. Attention! Do not use any animation on pages with the "wheel to the anchor" behaviour (like a "Chess 2 columns")!', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'std'        => 'none',
					'options'    => array(),
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
				),
				'show_filters'                  => array(
					'title'      => esc_html__( 'Show filters', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Show categories as tabs to filter posts', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
						'blog_style'     => array( 'portfolio', 'gallery' ),
					),
					'hidden'     => true,
					'std'        => 0,
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
				'open_full_post_in_blog'        => array(
					'title'      => esc_html__( 'Open full post in blog', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Allow to open the full version of the post directly in the blog feed. Attention! Applies only to 1 column layouts!', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'std'        => 0,
					'type'       => 'hidden',
				),
				'open_full_post_hide_author'    => array(
					'title'      => esc_html__( 'Hide author bio', 'kidscare' ),
					'desc'       => wp_kses_data( __( "Hide author bio after post content when open the full version of the post directly in the blog feed.", 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'open_full_post_in_blog' => array( 1 ),
					),
					'std'        => 1,
					'type'       => 'hidden',
				),
				'open_full_post_hide_related'   => array(
					'title'      => esc_html__( 'Hide related posts', 'kidscare' ),
					'desc'       => wp_kses_data( __( "Hide related posts after post content when open the full version of the post directly in the blog feed.", 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'open_full_post_in_blog' => array( 1 ),
					),
					'std'        => 1,
                    'type'       => 'hidden',
				),

				'blog_header_info'              => array(
					'title' => esc_html__( 'Header', 'kidscare' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type_blog'              => array(
					'title'    => esc_html__( 'Header style', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'kidscare' ) ),
					'std'      => 'inherit',
					'options'  => kidscare_get_list_header_footer_types( true ),
					'type'     => KIDSCARE_THEME_FREE || ! kidscare_exists_trx_addons() ? 'hidden' : 'switch',
				),
				'header_style_blog'             => array(
					'title'      => esc_html__( 'Select custom layout', 'kidscare' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'kidscare' ), 'kidscare_kses_content' ),
					'dependency' => array(
						'header_type_blog' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position_blog'          => array(
					'title'    => esc_html__( 'Header position', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'kidscare' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
				),
				'header_fullheight_blog'        => array(
					'title'    => esc_html__( 'Header fullheight', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Enlarge header area to fill whole screen. Used only if header have a background image', 'kidscare' ) ),
					'std'      => 'inherit',
					'options'  => kidscare_get_list_checkbox_values( true ),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
				),
				'header_wide_blog'              => array(
					'title'      => esc_html__( 'Header fullwidth', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'kidscare' ) ),
					'dependency' => array(
						'header_type_blog' => array( 'default' ),
					),
					'std'      => 'inherit',
					'options'  => kidscare_get_list_checkbox_values( true ),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
				),

				'blog_sidebar_info'             => array(
					'title' => esc_html__( 'Sidebar', 'kidscare' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position_blog'         => array(
					'title'   => esc_html__( 'Sidebar position', 'kidscare' ),
					'desc'    => wp_kses_data( __( 'Select position to show sidebar', 'kidscare' ) ),
					'std'     => 'inherit',
					'options' => array(),
					'qsetup'     => esc_html__( 'General', 'kidscare' ),
					'type'    => 'switch',
				),
				'sidebar_position_ss_blog'  => array(
					'title'    => esc_html__( 'Sidebar position on the small screen', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select position to move sidebar on the small screen - above or below the content', 'kidscare' ) ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
					),
					'std'      => 'inherit',
					'qsetup'   => esc_html__( 'General', 'kidscare' ),
					'options'  => array(),
					'type'     => 'switch',
				),
				'sidebar_widgets_blog'          => array(
					'title'      => esc_html__( 'Sidebar widgets', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar', 'kidscare' ) ),
					'dependency' => array(
						'sidebar_position_blog' => array( '^hide' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'qsetup'     => esc_html__( 'General', 'kidscare' ),
					'type'       => 'select',
				),
				'expand_content_blog'           => array(
					'title'   => esc_html__( 'Expand content', 'kidscare' ),
					'desc'    => wp_kses_data( __( 'Expand the content width if the sidebar is hidden', 'kidscare' ) ),
					'refresh' => false,
					'std'     => 'inherit',
					'options'  => kidscare_get_list_checkbox_values( true ),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
				),

				'blog_widgets_info'             => array(
					'title' => esc_html__( 'Additional widgets', 'kidscare' ),
					'desc'  => '',
					'type'  => KIDSCARE_THEME_FREE ? 'hidden' : 'info',
				),
				'widgets_above_page_blog'       => array(
					'title'   => esc_html__( 'Widgets at the top of the page', 'kidscare' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'kidscare' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
				),
				'widgets_above_content_blog'    => array(
					'title'   => esc_html__( 'Widgets above the content', 'kidscare' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'kidscare' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
				),
				'widgets_below_content_blog'    => array(
					'title'   => esc_html__( 'Widgets below the content', 'kidscare' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'kidscare' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
				),
				'widgets_below_page_blog'       => array(
					'title'   => esc_html__( 'Widgets at the bottom of the page', 'kidscare' ),
					'desc'    => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'kidscare' ) ),
					'std'     => 'hide',
					'options' => array(),
					'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
				),

				'blog_advanced_info'            => array(
					'title' => esc_html__( 'Advanced settings', 'kidscare' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'no_image'                      => array(
					'title' => esc_html__( 'Image placeholder', 'kidscare' ),
					'desc'  => wp_kses_data( __( "Select or upload an image used as placeholder for posts without a featured image. Placeholder is used on the blog stream page only (no placeholder in single post), and only in those styles of it where non-using featured image doesn't seem appropriate.", 'kidscare' ) ),
					'std'   => '',
					'type'  => 'image',
				),
				'time_diff_before'              => array(
					'title' => esc_html__( 'Easy Readable Date Format', 'kidscare' ),
					'desc'  => wp_kses_data( __( "For how many days to show the easy-readable date format (e.g. '3 days ago') instead of the standard publication date", 'kidscare' ) ),
					'std'   => 5,
					'type'  => 'text',
				),
				'sticky_style'                  => array(
					'title'   => esc_html__( 'Sticky posts style', 'kidscare' ),
					'desc'    => wp_kses_data( __( 'Select style of the sticky posts output', 'kidscare' ) ),
					'std'     => 'inherit',
					'options' => array(
						'inherit' => esc_html__( 'Decorated posts', 'kidscare' ),
						'columns' => esc_html__( 'Mini-cards', 'kidscare' ),
					),
					'type'    => 'hidden',
				),
				'meta_parts'                    => array(
					'title'      => esc_html__( 'Post meta', 'kidscare' ),
					'desc'       => wp_kses_data( __( "If your blog page is created using the 'Blog archive' page template, set up the 'Post Meta' settings in the 'Theme Options' section of that page. Post counters and Share Links are available only if plugin ThemeREX Addons is active", 'kidscare' ) )
								. '<br>'
								. wp_kses_data( __( '<b>Tip:</b> Drag items to change their order.', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'compare' => 'or',
						'#page_template' => array( 'blog.php' ),
						'.components-select-control:not(.post-author-selector) select' => array( 'blog.php' ),
						'.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					'dir'        => 'vertical',
					'sortable'   => true,
					'std'        => 'categories=1|date=1|views=1|comments=1|likes=1|author=1|share=0|edit=0',
					'options'    => kidscare_get_list_meta_parts(),
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'checklist',
				),


				// Blog - Single posts
				//---------------------------------------------
				'blog_single'                   => array(
					'title' => esc_html__( 'Single posts', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Settings of the single post', 'kidscare' ) ),
					'type'  => 'section',
				),

				'blog_single_header_info'       => array(
					'title' => esc_html__( 'Header', 'kidscare' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'header_type_single'            => array(
					'title'    => esc_html__( 'Header style', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'kidscare' ) ),
					'std'      => 'inherit',
					'options'  => kidscare_get_list_header_footer_types( true ),
					'type'     => KIDSCARE_THEME_FREE || ! kidscare_exists_trx_addons() ? 'hidden' : 'switch',
				),
				'header_style_single'           => array(
					'title'      => esc_html__( 'Select custom layout', 'kidscare' ),
					'desc'       => wp_kses( __( 'Select custom header from Layouts Builder', 'kidscare' ), 'kidscare_kses_content' ),
					'dependency' => array(
						'header_type_single' => array( 'custom' ),
					),
					'std'        => 'inherit',
					'options'    => array(),
					'type'       => 'select',
				),
				'header_position_single'        => array(
					'title'    => esc_html__( 'Header position', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select position to display the site header', 'kidscare' ) ),
					'std'      => 'inherit',
					'options'  => array(),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
				),
				'header_fullheight_single'      => array(
					'title'    => esc_html__( 'Header fullheight', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Enlarge header area to fill whole screen. Used only if header have a background image', 'kidscare' ) ),
					'std'      => 'inherit',
					'options'  => kidscare_get_list_checkbox_values( true ),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
				),
				'header_wide_single'            => array(
					'title'      => esc_html__( 'Header fullwidth', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Do you want to stretch the header widgets area to the entire window width?', 'kidscare' ) ),
					'dependency' => array(
						'header_type_single' => array( 'default' ),
					),
					'std'      => 'inherit',
					'options'  => kidscare_get_list_checkbox_values( true ),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
				),

				'blog_single_sidebar_info'      => array(
					'title' => esc_html__( 'Sidebar', 'kidscare' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'sidebar_position_single'       => array(
					'title'   => esc_html__( 'Sidebar position', 'kidscare' ),
					'desc'    => wp_kses_data( __( 'Select position to show sidebar on the single posts', 'kidscare' ) ),
					'std'     => 'hide',
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'kidscare' ),
					),
					'options' => array(),
					'type'    => 'switch',
				),
				'sidebar_position_ss_single'=> array(
					'title'    => esc_html__( 'Sidebar position on the small screen', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select position to move sidebar on the single posts on the small screen - above or below the content', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'kidscare' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
					),
					'std'      => 'below',
					'options'  => array(),
					'type'     => 'switch',
				),
				'sidebar_widgets_single'        => array(
					'title'      => esc_html__( 'Sidebar widgets', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select default widgets to show in the sidebar on the single posts', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post,product,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Widgets', 'kidscare' ),
					),
					'dependency' => array(
						'sidebar_position_single' => array( '^hide' ),
					),
					'std'        => 'sidebar_widgets',
					'options'    => array(),
					'type'       => 'select',
				),
				'expand_content_single'           => array(
					'title'   => esc_html__( 'Expand content', 'kidscare' ),
					'desc'    => wp_kses_data( __( 'Expand the content width on the single posts if the sidebar is hidden', 'kidscare' ) ),
					'refresh' => false,
					'std'     => 'inherit',
					'options'  => kidscare_get_list_checkbox_values( true ),
					'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
				),

				'blog_single_title_info'      => array(
					'title' => esc_html__( 'Featured image and title', 'kidscare' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'hide_featured_on_single'       => array(
					'title'    => esc_html__( 'Hide featured image on the single post', 'kidscare' ),
					'desc'     => wp_kses_data( __( "Hide featured image on the single post's pages", 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page,post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'std'      => 0,
					'type'     => 'checkbox',
				),
				'post_thumbnail_type'      => array(
					'title'      => esc_html__( 'Type of post thumbnail', 'kidscare' ),
					'desc'       => wp_kses_data( __( "Select type of post thumbnail on the single post's pages", 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'hide_featured_on_single' => array( 'is_empty', 0 ),
					),
					'std'        => 'default',
					'options'    => array(
						'fullwidth'   => esc_html__( 'Fullwidth', 'kidscare' ),
						'boxed'       => esc_html__( 'Boxed', 'kidscare' ),
						'default'     => esc_html__( 'Default', 'kidscare' ),
					),
					'type'       => 'hidden',
				),
				'post_header_position'          => array(
					'title'      => esc_html__( 'Post header position', 'kidscare' ),
					'desc'       => wp_kses_data( __( "Select post header position on the single post's pages", 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'hide_featured_on_single' => array( 'is_empty', 0 )
					),
					'std'        => 'under',
					'options'    => array(
						'above'      => esc_html__( 'Above the post thumbnail', 'kidscare' ),
						'under'      => esc_html__( 'Under the post thumbnail', 'kidscare' ),
						'default'    => esc_html__( 'Default', 'kidscare' ),
					),
					'type'       => 'hidden',
				),
				'post_header_align'             => array(
					'title'      => esc_html__( 'Align of the post header', 'kidscare' ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'post_header_position' => array( 'on_thumb' ),
					),
					'std'        => 'mc',
					'options'    => array(
						'ts' => esc_html__('Top Stick Out', 'kidscare'),
						'tl' => esc_html__('Top Left', 'kidscare'),
						'tc' => esc_html__('Top Center', 'kidscare'),
						'tr' => esc_html__('Top Right', 'kidscare'),
						'ml' => esc_html__('Middle Left', 'kidscare'),
						'mc' => esc_html__('Middle Center', 'kidscare'),
						'mr' => esc_html__('Middle Right', 'kidscare'),
						'bl' => esc_html__('Bottom Left', 'kidscare'),
						'bc' => esc_html__('Bottom Center', 'kidscare'),
						'br' => esc_html__('Bottom Right', 'kidscare'),
						'bs' => esc_html__('Bottom Stick Out', 'kidscare'),
					),
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
				),
				'post_subtitle'                 => array(
					'title' => esc_html__( 'Post subtitle', 'kidscare' ),
					'desc'  => wp_kses_data( __( "Specify post subtitle to display it under the post title.", 'kidscare' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'std'   => '',
					'hidden' => true,
					'type'  => 'text',
				),
				'show_post_meta'                => array(
					'title' => esc_html__( 'Show post meta', 'kidscare' ),
					'desc'  => wp_kses_data( __( "Display block with post's meta: date, categories, counters, etc.", 'kidscare' ) ),
					'std'   => 1,
					'type'  => 'checkbox',
				),
				'meta_parts_single'             => array(
					'title'      => esc_html__( 'Post meta', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Meta parts for single posts. Post counters and Share Links are available only if plugin ThemeREX Addons is active', 'kidscare' ) )
								. '<br>'
								. wp_kses_data( __( '<b>Tip:</b> Drag items to change their order.', 'kidscare' ) ),
					'dependency' => array(
						'show_post_meta' => array( 1 ),
					),
					'dir'        => 'vertical',
					'sortable'   => true,
					'std'        => 'categories=1|date=1|views=1|likes=1|comments=1|author=0|share=0|edit=0',
					'options'    => kidscare_get_list_meta_parts(),
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'checklist',
				),
				'show_share_links'              => array(
					'title' => esc_html__( 'Show share links', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Display share links on the single post', 'kidscare' ) ),
					'std'   => 1,
					'type'  => ! kidscare_exists_trx_addons() ? 'hidden' : 'checkbox',
				),
				'show_author_info'              => array(
					'title' => esc_html__( 'Show author info', 'kidscare' ),
					'desc'  => wp_kses_data( __( "Display block with information about post's author", 'kidscare' ) ),
					'std'   => 1,
					'type'  => 'checkbox',
				),

				'blog_single_related_info'      => array(
					'title' => esc_html__( 'Related posts', 'kidscare' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'show_related_posts'            => array(
					'title'    => esc_html__( 'Show related posts', 'kidscare' ),
					'desc'     => wp_kses_data( __( "Show section 'Related posts' on the single post's pages", 'kidscare' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'std'      => 1,
					'type'     => 'checkbox',
				),
				'related_style'                 => array(
					'title'      => esc_html__( 'Related posts style', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select style of the related posts output', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 'classic',
					'options'    => array(
						'modern'  => esc_html__( 'Modern', 'kidscare' ),
						'classic' => esc_html__( 'Classic', 'kidscare' ),
						'wide'    => esc_html__( 'Wide', 'kidscare' ),
						'list'    => esc_html__( 'List', 'kidscare' ),
						'short'   => esc_html__( 'Short', 'kidscare' ),
					),
					'type'       => 'hidden',
				),
				'related_position'              => array(
					'title'      => esc_html__( 'Related posts position', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Select position to display the related posts', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 'below_content',
					'options'    => array (
						'inside'        => esc_html__( 'Inside the content (fullwidth)', 'kidscare' ),
						'inside_left'   => esc_html__( 'At left side of the content', 'kidscare' ),
						'inside_right'  => esc_html__( 'At right side of the content', 'kidscare' ),
						'below_content' => esc_html__( 'After the content', 'kidscare' ),
						'below_page'    => esc_html__( 'After the content & sidebar', 'kidscare' ),
					),
                    'type'       => 'hidden',
				),
				'related_position_inside'       => array(
					'title'      => esc_html__( 'Before # paragraph', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Before what paragraph should related posts appear? If 0 - randomly.', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_position' => array( 'inside', 'inside_left', 'inside_right' ),
					),
					'std'        => 2,
					'options'    => kidscare_get_list_range( 0, 9 ),
                    'type'       => 'hidden',
				),
				'related_posts'                 => array(
					'title'      => esc_html__( 'Related posts', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'How many related posts should be displayed in the single post? If 0 - no related posts are shown.', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 2,
					'min'        => 1,
					'max'        => 9,
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'slider',
				),
				'related_columns'               => array(
					'title'      => esc_html__( 'Related columns', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'How many columns should be used to output related posts in the single page?', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_position' => array( 'inside', 'below_content', 'below_page' ),
					),
					'std'        => 2,
					'options'    => kidscare_get_list_range( 1, 6 ),
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
				),
				'related_slider'                => array(
					'title'      => esc_html__( 'Use slider layout', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Use slider layout in case related posts count is more than columns count', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
					),
					'std'        => 0,
                    'type'       => 'hidden',
				),
				'related_slider_controls'       => array(
					'title'      => esc_html__( 'Slider controls', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Show arrows in the slider', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_slider' => array( 1 ),
					),
					'std'        => 'none',
					'options'    => array(
						'none'    => esc_html__('None', 'kidscare'),
						'side'    => esc_html__('Side', 'kidscare'),
						'outside' => esc_html__('Outside', 'kidscare'),
						'top'     => esc_html__('Top', 'kidscare'),
						'bottom'  => esc_html__('Bottom', 'kidscare')
					),
                    'type'       => 'hidden',
				),
				'related_slider_pagination'       => array(
					'title'      => esc_html__( 'Slider pagination', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Show bullets after the slider', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_slider' => array( 1 ),
					),
					'std'        => 'bottom',
					'options'    => array(
						'none'    => esc_html__('None', 'kidscare'),
						'bottom'  => esc_html__('Bottom', 'kidscare')
					),
                    'type'       => 'hidden',
				),
				'related_slider_space'          => array(
					'title'      => esc_html__( 'Space', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Space between slides', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Content', 'kidscare' ),
					),
					'dependency' => array(
						'show_related_posts' => array( 1 ),
						'related_slider' => array( 1 ),
					),
					'std'        => 30,
                    'type'       => 'hidden',
				),
				'posts_navigation_info'      => array(
					'title' => esc_html__( 'Posts navigation', 'kidscare' ),
					'desc'  => '',
					'type'  => 'info',
				),
				'posts_navigation'           => array(
					'title'   => esc_html__( 'Show posts navigation', 'kidscare' ),
					'desc'    => wp_kses_data( __( "Show posts navigation on the single post's pages", 'kidscare' ) ),
					'std'     => 'links',
					'options' => array(
						'none'   => esc_html__('None', 'kidscare'),
						'links'  => esc_html__('Prev/Next links', 'kidscare'),
					),
					'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
				),
				'posts_navigation_fixed'     => array(
					'title'      => esc_html__( 'Fixed posts navigation', 'kidscare' ),
					'desc'       => wp_kses_data( __( "Make posts navigation fixed position. Display it when the content of the article is inside the window.", 'kidscare' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'links' ),
					),
					'std'        => 0,
                    'type'       => 'hidden',
				),
				'posts_navigation_scroll_hide_author'  => array(
					'title'      => esc_html__( 'Hide author bio', 'kidscare' ),
					'desc'       => wp_kses_data( __( "Hide author bio after post content when infinite scroll is used.", 'kidscare' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'        => 0,
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
				'posts_navigation_scroll_hide_related'  => array(
					'title'      => esc_html__( 'Hide related posts', 'kidscare' ),
					'desc'       => wp_kses_data( __( "Hide related posts after post content when infinite scroll is used.", 'kidscare' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'        => 0,
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
				'posts_navigation_scroll_hide_comments' => array(
					'title'      => esc_html__( 'Hide comments', 'kidscare' ),
					'desc'       => wp_kses_data( __( "Hide comments after post content when infinite scroll is used.", 'kidscare' ) ),
					'dependency' => array(
						'posts_navigation' => array( 'scroll' ),
					),
					'std'        => 1,
					'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'checkbox',
				),
				'posts_banners_info'      => array(
					'title' => esc_html__( 'Posts banners', 'kidscare' ),
					'desc'  => '',
					'hidden' => true,
					'type'  => 'info',
				),
				'header_banner_link'     => array(
					'title' => esc_html__( 'Header banner link', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Insert URL of the banner', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Banners', 'kidscare' ),
					),
					'std'   => '',
                    'type'  => 'hidden',
				),
				'header_banner_img'     => array(
					'title' => esc_html__( 'Header banner image', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Select image to display at the backgound', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Banners', 'kidscare' ),
					),
					'std'        => '',
                    'type'  => 'hidden',
				),
				'header_banner_height'  => array(
					'title' => esc_html__( 'Header banner height', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Specify minimal height of the banner (in "px" or "em"). For example: 15em', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Banners', 'kidscare' ),
					),
					'std'        => '',
                    'type'  => 'hidden',
				),
				'header_banner_code'     => array(
					'title'      => esc_html__( 'Header banner code', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Embed html code', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Banners', 'kidscare' ),
					),
					'std'        => '',
					'allow_html' => true,
                    'type'  => 'hidden',
				),
				'footer_banner_link'     => array(
					'title' => esc_html__( 'Footer banner link', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Insert URL of the banner', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Banners', 'kidscare' ),
					),
					'std'   => '',
                    'type'  => 'hidden',
				),
				'footer_banner_img'     => array(
					'title' => esc_html__( 'Footer banner image', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Select image to display at the backgound', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Banners', 'kidscare' ),
					),
					'std'        => '',
                    'type'  => 'hidden',
				),
				'footer_banner_height'  => array(
					'title' => esc_html__( 'Footer banner height', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Specify minimal height of the banner (in "px" or "em"). For example: 15em', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Banners', 'kidscare' ),
					),
					'std'        => '',
                    'type'  => 'hidden',
				),
				'footer_banner_code'     => array(
					'title'      => esc_html__( 'Footer banner code', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Embed html code', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Banners', 'kidscare' ),
					),
					'std'        => '',
					'allow_html' => true,
                    'type'  => 'hidden',
				),
				'sidebar_banner_link'     => array(
					'title' => esc_html__( 'Sidebar banner link', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Insert URL of the banner', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Banners', 'kidscare' ),
					),
					'std'   => '',
                    'type'  => 'hidden',
				),
				'sidebar_banner_img'     => array(
					'title' => esc_html__( 'Sidebar banner image', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Select image to display at the backgound', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Banners', 'kidscare' ),
					),
					'std'        => '',
                    'type'  => 'hidden',
				),
				'sidebar_banner_code'     => array(
					'title'      => esc_html__( 'Sidebar banner code', 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Embed html code', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Banners', 'kidscare' ),
					),
					'std'        => '',
					'allow_html' => true,
                    'type'  => 'hidden',
				),
				'background_banner_link'     => array(
					'title' => esc_html__( "Post's background banner link", 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Insert URL of the banner', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Banners', 'kidscare' ),
					),
					'std'   => '',
                    'type'  => 'hidden',
				),
				'background_banner_img'     => array(
					'title' => esc_html__( "Post's background banner image", 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Select image to display at the backgound', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Banners', 'kidscare' ),
					),
					'std'        => '',
                    'type'  => 'hidden',
				),
				'background_banner_code'     => array(
					'title'      => esc_html__( "Post's background banner code", 'kidscare' ),
					'desc'       => wp_kses_data( __( 'Embed html code', 'kidscare' ) ),
					'override'   => array(
						'mode'    => 'post',
						'section' => esc_html__( 'Banners', 'kidscare' ),
					),
					'std'        => '',
					'allow_html' => true,
                    'type'  => 'hidden',
				),
				'blog_end'                      => array(
					'type' => 'panel_end',
				),



				// 'Colors'
				//---------------------------------------------
				'panel_colors'                  => array(
					'title'    => esc_html__( 'Colors', 'kidscare' ),
					'desc'     => '',
					'priority' => 300,
					'type'     => 'section',
				),

				'color_schemes_info'            => array(
					'title'  => esc_html__( 'Color schemes', 'kidscare' ),
					'desc'   => wp_kses_data( __( 'Color schemes for various parts of the site. "Inherit" means that this block is used the Site color scheme (the first parameter)', 'kidscare' ) ),
					'hidden' => $hide_schemes,
					'type'   => 'info',
				),
				'color_scheme'                  => array(
					'title'    => esc_html__( 'Site Color Scheme', 'kidscare' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'kidscare' ),
					),
					'std'      => 'default',
					'options'  => array(),
					'refresh'  => false,
					'type'     => $hide_schemes ? 'hidden' : 'switch',
				),
				'header_scheme'                 => array(
					'title'    => esc_html__( 'Header Color Scheme', 'kidscare' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'kidscare' ),
					),
					'std'      => 'inherit',
					'options'  => array(),
					'refresh'  => false,
					'type'     => $hide_schemes ? 'hidden' : 'switch',
				),
				'menu_scheme'                   => array(
					'title'    => esc_html__( 'Sidemenu Color Scheme', 'kidscare' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'kidscare' ),
					),
					'std'      => 'inherit',
					'options'  => array(),
					'refresh'  => false,
					'type'     => 'hidden',
				),
				'sidebar_scheme'                => array(
					'title'    => esc_html__( 'Sidebar Color Scheme', 'kidscare' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'kidscare' ),
					),
					'std'      => 'inherit',
					'options'  => array(),
					'refresh'  => false,
					'type'     => $hide_schemes ? 'hidden' : 'switch',
				),
				'footer_scheme'                 => array(
					'title'    => esc_html__( 'Footer Color Scheme', 'kidscare' ),
					'desc'     => '',
					'override' => array(
						'mode'    => 'page,cpt_team,cpt_services,cpt_dishes,cpt_competitions,cpt_rounds,cpt_matches,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
						'section' => esc_html__( 'Colors', 'kidscare' ),
					),
					'std'      => 'dark',
					'options'  => array(),
					'refresh'  => false,
					'type'     => $hide_schemes ? 'hidden' : 'switch',
				),

				'color_scheme_editor_info'      => array(
					'title' => esc_html__( 'Color scheme editor', 'kidscare' ),
					'desc'  => wp_kses_data( __( 'Select color scheme to modify. Attention! Only those sections in the site will be changed which this scheme was assigned to', 'kidscare' ) ),
					'type'  => 'info',
				),
				'scheme_storage'                => array(
					'title'       => esc_html__( 'Color scheme editor', 'kidscare' ),
					'desc'        => '',
					'std'         => '$kidscare_get_scheme_storage',
					'refresh'     => false,
					'colorpicker' => 'tiny',
					'type'        => 'scheme_editor',
				),

				// Internal options.
				// Attention! Don't change any options in the section below!
				// Use huge priority to call render this elements after all options!
				'reset_options'                 => array(
					'title'    => '',
					'desc'     => '',
					'std'      => '0',
					'priority' => 10000,
					'type'     => 'hidden',
				),

				'last_option'                   => array(     // Need to manually call action to include Tiny MCE scripts
					'title' => '',
					'desc'  => '',
					'std'   => 1,
					'type'  => 'hidden',
				),

			)
		);



		// Prepare panel 'Fonts'
		// -------------------------------------------------------------
		$fonts = array(

			// 'Fonts'
			//---------------------------------------------
			'fonts'             => array(
				'title'    => esc_html__( 'Typography', 'kidscare' ),
				'desc'     => '',
				'priority' => 200,
				'type'     => 'panel',
			),

			// Fonts - Load_fonts
			'load_fonts'        => array(
				'title' => esc_html__( 'Load fonts', 'kidscare' ),
				'desc'  => wp_kses_data( __( 'Specify fonts to load when theme start. You can use them in the base theme elements: headers, text, menu, links, input fields, etc.', 'kidscare' ) )
						. '<br>'
						. wp_kses_data( __( 'Attention! Press "Refresh" button to reload preview area after the all fonts are changed', 'kidscare' ) ),
				'type'  => 'section',
			),
			'load_fonts_subset' => array(
				'title'   => esc_html__( 'Google fonts subsets', 'kidscare' ),
				'desc'    => wp_kses_data( __( 'Specify comma separated list of the subsets which will be load from Google fonts', 'kidscare' ) )
						. '<br>'
						. wp_kses_data( __( 'Available subsets are: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese', 'kidscare' ) ),
				'class'   => 'kidscare_column-1_3 kidscare_new_row',
				'refresh' => false,
				'std'     => '$kidscare_get_load_fonts_subset',
				'type'    => 'text',
			),
		);

		for ( $i = 1; $i <= kidscare_get_theme_setting( 'max_load_fonts' ); $i++ ) {
			if ( kidscare_get_value_gp( 'page' ) != 'theme_options' ) {
				$fonts[ "load_fonts-{$i}-info" ] = array(
					// Translators: Add font's number - 'Font 1', 'Font 2', etc
					'title' => esc_html( sprintf( __( 'Font %s', 'kidscare' ), $i ) ),
					'desc'  => '',
					'type'  => 'info',
				);
			}
			$fonts[ "load_fonts-{$i}-name" ]   = array(
				'title'   => esc_html__( 'Font name', 'kidscare' ),
				'desc'    => '',
				'class'   => 'kidscare_column-1_3 kidscare_new_row',
				'refresh' => false,
				'std'     => '$kidscare_get_load_fonts_option',
				'type'    => 'text',
			);
			$fonts[ "load_fonts-{$i}-family" ] = array(
				'title'   => esc_html__( 'Font family', 'kidscare' ),
				'desc'    => 1 == $i
							? wp_kses_data( __( 'Select font family to use it if font above is not available', 'kidscare' ) )
							: '',
				'class'   => 'kidscare_column-1_3',
				'refresh' => false,
				'std'     => '$kidscare_get_load_fonts_option',
				'options' => array(
					'inherit'    => esc_html__( 'Inherit', 'kidscare' ),
					'serif'      => esc_html__( 'serif', 'kidscare' ),
					'sans-serif' => esc_html__( 'sans-serif', 'kidscare' ),
					'monospace'  => esc_html__( 'monospace', 'kidscare' ),
					'cursive'    => esc_html__( 'cursive', 'kidscare' ),
					'fantasy'    => esc_html__( 'fantasy', 'kidscare' ),
				),
				'type'    => 'select',
			);
			$fonts[ "load_fonts-{$i}-styles" ] = array(
				'title'   => esc_html__( 'Font styles', 'kidscare' ),
				'desc'    => 1 == $i
							? wp_kses_data( __( 'Font styles used only for the Google fonts. This is a comma separated list of the font weight and styles. For example: 400,400italic,700', 'kidscare' ) )
								. '<br>'
								. wp_kses_data( __( 'Attention! Each weight and style increase download size! Specify only used weights and styles.', 'kidscare' ) )
							: '',
				'class'   => 'kidscare_column-1_3',
				'refresh' => false,
				'std'     => '$kidscare_get_load_fonts_option',
				'type'    => 'text',
			);
		}
		$fonts['load_fonts_end'] = array(
			'type' => 'section_end',
		);

		// Fonts - H1..6, P, Info, Menu, etc.
		$theme_fonts = kidscare_get_theme_fonts();
		foreach ( $theme_fonts as $tag => $v ) {
			$fonts[ "{$tag}_section" ] = array(
				'title' => ! empty( $v['title'] )
								? $v['title']
								// Translators: Add tag's name to make title 'H1 settings', 'P settings', etc.
								: esc_html( sprintf( __( '%s settings', 'kidscare' ), $tag ) ),
				'desc'  => ! empty( $v['description'] )
								? $v['description']
								// Translators: Add tag's name to make description
								: wp_kses( sprintf( __( 'Font settings of the "%s" tag.', 'kidscare' ), $tag ), 'kidscare_kses_content' ),
				'type'  => 'section',
			);

			foreach ( $v as $css_prop => $css_value ) {
				if ( in_array( $css_prop, array( 'title', 'description' ) ) ) {
					continue;
				}
				$options    = '';
				$type       = 'text';
				$load_order = 1;
				$title      = ucfirst( str_replace( '-', ' ', $css_prop ) );
				if ( 'font-family' == $css_prop ) {
					$type       = 'select';
					$options    = array();
					$load_order = 2;        // Load this option's value after all options are loaded (use option 'load_fonts' to build fonts list)
				} elseif ( 'font-weight' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit' => esc_html__( 'Inherit', 'kidscare' ),
						'100'     => esc_html__( '100 (Light)', 'kidscare' ),
						'200'     => esc_html__( '200 (Light)', 'kidscare' ),
						'300'     => esc_html__( '300 (Thin)', 'kidscare' ),
						'400'     => esc_html__( '400 (Normal)', 'kidscare' ),
						'500'     => esc_html__( '500 (Semibold)', 'kidscare' ),
						'600'     => esc_html__( '600 (Semibold)', 'kidscare' ),
						'700'     => esc_html__( '700 (Bold)', 'kidscare' ),
						'800'     => esc_html__( '800 (Black)', 'kidscare' ),
						'900'     => esc_html__( '900 (Black)', 'kidscare' ),
					);
				} elseif ( 'font-style' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit' => esc_html__( 'Inherit', 'kidscare' ),
						'normal'  => esc_html__( 'Normal', 'kidscare' ),
						'italic'  => esc_html__( 'Italic', 'kidscare' ),
					);
				} elseif ( 'text-decoration' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit'      => esc_html__( 'Inherit', 'kidscare' ),
						'none'         => esc_html__( 'None', 'kidscare' ),
						'underline'    => esc_html__( 'Underline', 'kidscare' ),
						'overline'     => esc_html__( 'Overline', 'kidscare' ),
						'line-through' => esc_html__( 'Line-through', 'kidscare' ),
					);
				} elseif ( 'text-transform' == $css_prop ) {
					$type    = 'select';
					$options = array(
						'inherit'    => esc_html__( 'Inherit', 'kidscare' ),
						'none'       => esc_html__( 'None', 'kidscare' ),
						'uppercase'  => esc_html__( 'Uppercase', 'kidscare' ),
						'lowercase'  => esc_html__( 'Lowercase', 'kidscare' ),
						'capitalize' => esc_html__( 'Capitalize', 'kidscare' ),
					);
				}
				$fonts[ "{$tag}_{$css_prop}" ] = array(
					'title'      => $title,
					'desc'       => '',
					'class'      => 'kidscare_column-1_5',
					'refresh'    => false,
					'load_order' => $load_order,
					'std'        => '$kidscare_get_theme_fonts_option',
					'options'    => $options,
					'type'       => $type,
				);
			}

			$fonts[ "{$tag}_section_end" ] = array(
				'type' => 'section_end',
			);
		}

		$fonts['fonts_end'] = array(
			'type' => 'panel_end',
		);

		// Add fonts parameters to Theme Options
		kidscare_storage_set_array_before( 'options', 'panel_colors', $fonts );

		// Add Header Video if WP version < 4.7
		// -----------------------------------------------------
		if ( ! function_exists( 'get_header_video_url' ) ) {
			kidscare_storage_set_array_after(
				'options', 'header_image_override', 'header_video', array(
					'title'    => esc_html__( 'Header video', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select video to use it as background for the header', 'kidscare' ) ),
					'override' => array(
						'mode'    => 'page',
						'section' => esc_html__( 'Header', 'kidscare' ),
					),
					'std'      => '',
					'type'     => 'video',
				)
			);
		}

		// Add option 'logo' if WP version < 4.5
		// or 'custom_logo' if current page is not 'Customize'
		// ------------------------------------------------------
		if ( ! function_exists( 'the_custom_logo' ) || ! kidscare_check_url( 'customize.php' ) ) {
			kidscare_storage_set_array_before(
				'options', 'logo_retina', function_exists( 'the_custom_logo' ) ? 'custom_logo' : 'logo', array(
					'title'    => esc_html__( 'Logo', 'kidscare' ),
					'desc'     => wp_kses_data( __( 'Select or upload the site logo', 'kidscare' ) ),
					'class'    => 'kidscare_column-1_2 kidscare_new_row',
					'priority' => 60,
					'std'      => '',
					'qsetup'   => esc_html__( 'General', 'kidscare' ),
					'type'     => 'image',
				)
			);
		}

	}
}


// Returns a list of options that can be overridden for CPT
if ( ! function_exists( 'kidscare_options_get_list_cpt_options' ) ) {
	function kidscare_options_get_list_cpt_options( $cpt, $title = '' ) {
		if ( empty( $title ) ) {
			$title = ucfirst( $cpt );
		}
		return array(
			"content_info_{$cpt}"           => array(
				'title' => esc_html__( 'Content', 'kidscare' ),
				'desc'  => '',
				'type'  => 'info',
			),
			"body_style_{$cpt}"             => array(
				'title'    => esc_html__( 'Body style', 'kidscare' ),
				'desc'     => wp_kses_data( __( 'Select width of the body content', 'kidscare' ) ),
				'std'      => 'inherit',
				'options'  => kidscare_get_list_body_styles( true ),
				'type'     => 'select',
			),
			"boxed_bg_image_{$cpt}"         => array(
				'title'      => esc_html__( 'Boxed bg image', 'kidscare' ),
				'desc'       => wp_kses_data( __( 'Select or upload image, used as background in the boxed body', 'kidscare' ) ),
				'dependency' => array(
					"body_style_{$cpt}" => array( 'boxed' ),
				),
				'std'        => 'inherit',
				'type'       => 'image',
			),
			"header_info_{$cpt}"            => array(
				'title' => esc_html__( 'Header', 'kidscare' ),
				'desc'  => '',
				'type'  => 'info',
			),
			"header_type_{$cpt}"            => array(
				'title'   => esc_html__( 'Header style', 'kidscare' ),
				'desc'    => wp_kses_data( __( 'Choose whether to use the default header or header Layouts (available only if the ThemeREX Addons is activated)', 'kidscare' ) ),
				'std'     => 'inherit',
				'options' => kidscare_get_list_header_footer_types( true ),
				'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
			),
			"header_style_{$cpt}"           => array(
				'title'      => esc_html__( 'Select custom layout', 'kidscare' ),
				// Translators: Add CPT name to the description
				'desc'       => wp_kses_data( sprintf( __( 'Select custom layout to display the site header on the %s pages', 'kidscare' ), $title ) ),
				'dependency' => array(
					"header_type_{$cpt}" => array( 'custom' ),
				),
				'std'        => 'inherit',
				'options'    => array(),
				'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
			),
			"header_position_{$cpt}"        => array(
				'title'   => esc_html__( 'Header position', 'kidscare' ),
				// Translators: Add CPT name to the description
				'desc'    => wp_kses_data( sprintf( __( 'Select position to display the site header on the %s pages', 'kidscare' ), $title ) ),
				'std'     => 'inherit',
				'options' => array(),
				'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
			),
			"header_image_override_{$cpt}"  => array(
				'title'   => esc_html__( 'Header image override', 'kidscare' ),
				'desc'    => wp_kses_data( __( "Allow override the header image with the post's featured image", 'kidscare' ) ),
				'std'     => 'inherit',
				'options' => array(
					'inherit' => esc_html__( 'Inherit', 'kidscare' ),
					1         => esc_html__( 'Yes', 'kidscare' ),
					0         => esc_html__( 'No', 'kidscare' ),
				),
				'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
			),
			"header_widgets_{$cpt}"         => array(
				'title'   => esc_html__( 'Header widgets', 'kidscare' ),
				// Translators: Add CPT name to the description
				'desc'    => wp_kses_data( sprintf( __( 'Select set of widgets to show in the header on the %s pages', 'kidscare' ), $title ) ),
				'std'     => 'hide',
				'options' => array(),
				'type'    => 'select',
			),

			"sidebar_info_{$cpt}"           => array(
				'title' => esc_html__( 'Sidebar', 'kidscare' ),
				'desc'  => '',
				'type'  => 'info',
			),
			"sidebar_position_{$cpt}"       => array(
				'title'   => sprintf( __( 'Sidebar position on the %s list', 'kidscare' ), $title ),
				// Translators: Add CPT name to the description
				'desc'    => wp_kses_data( sprintf( __( 'Select position to show sidebar on the %s list', 'kidscare' ), $title ) ),
				'std'     => 'left',
				'options' => array(),
				'type'    => 'switch',
			),
			"sidebar_position_ss_{$cpt}"=> array(
				'title'    => sprintf( __( 'Sidebar position on the %s list on the small screen', 'kidscare' ), $title ),
				'desc'     => wp_kses_data( __( 'Select position to move sidebar on the small screen - above or below the content', 'kidscare' ) ),
				'std'      => 'below',
				'dependency' => array(
					"sidebar_position_{$cpt}" => array( '^hide' ),
				),
				'options'  => array(),
				'type'     => 'switch',
			),
			"sidebar_widgets_{$cpt}"        => array(
				'title'      => sprintf( esc_html__( 'Sidebar widgets on the %s list', 'kidscare' ), $title ),
				// Translators: Add CPT name to the description
				'desc'       => wp_kses_data( sprintf( __( 'Select sidebar to show on the %s list', 'kidscare' ), $title ) ),
				'dependency' => array(
					"sidebar_position_{$cpt}" => array( '^hide' ),
				),
				'std'        => 'hide',
				'options'    => array(),
				'type'       => 'select',
			),
			"sidebar_position_single_{$cpt}"       => array(
				'title'   => sprintf( esc_html__( 'Sidebar position on the single post', 'kidscare' ), $title ),
				// Translators: Add CPT name to the description
				'desc'    => wp_kses_data( sprintf( __( 'Select position to show sidebar on the single posts of the %s', 'kidscare' ), $title ) ),
				'std'     => 'left',
				'options' => array(),
				'type'    => 'switch',
			),
			"sidebar_position_ss_single_{$cpt}"=> array(
				'title'    => esc_html__( 'Sidebar position on the single post on the small screen', 'kidscare' ),
				'desc'     => wp_kses_data( __( 'Select position to move sidebar on the small screen - above or below the content', 'kidscare' ) ),
				'dependency' => array(
					"sidebar_position_single_{$cpt}" => array( '^hide' ),
				),
				'std'      => 'below',
				'options'  => array(),
				'type'     => 'switch',
			),
			"sidebar_widgets_single_{$cpt}"        => array(
				'title'      => sprintf( esc_html__( 'Sidebar widgets on the single post', 'kidscare' ), $title ),
				// Translators: Add CPT name to the description
				'desc'       => wp_kses_data( sprintf( __( 'Select widgets to show in the sidebar on the single posts of the %s', 'kidscare' ), $title ) ),
				'dependency' => array(
					"sidebar_position_single_{$cpt}" => array( '^hide' ),
				),
				'std'        => 'hide',
				'options'    => array(),
				'type'       => 'select',
			),
			"expand_content_{$cpt}"         => array(
				'title'   => esc_html__( 'Expand content', 'kidscare' ),
				'desc'    => wp_kses_data( __( 'Expand the content width if the sidebar is hidden', 'kidscare' ) ),
				'refresh' => false,
				'std'     => 'inherit',
				'options'  => kidscare_get_list_checkbox_values( true ),
				'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
			),
			"expand_content_single_{$cpt}"         => array(
				'title'   => esc_html__( 'Expand content on the single post', 'kidscare' ),
				'desc'    => wp_kses_data( __( 'Expand the content width on the single post if the sidebar is hidden', 'kidscare' ) ),
				'refresh' => false,
				'std'     => 'inherit',
				'options'  => kidscare_get_list_checkbox_values( true ),
				'type'     => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
			),

			"footer_info_{$cpt}"            => array(
				'title' => esc_html__( 'Footer', 'kidscare' ),
				'desc'  => '',
				'type'  => 'info',
			),
			"footer_type_{$cpt}"            => array(
				'title'   => esc_html__( 'Footer style', 'kidscare' ),
				'desc'    => wp_kses_data( __( 'Choose whether to use the default footer or footer Layouts (available only if the ThemeREX Addons is activated)', 'kidscare' ) ),
				'std'     => 'inherit',
				'options' => kidscare_get_list_header_footer_types( true ),
				'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'switch',
			),
			"footer_style_{$cpt}"           => array(
				'title'      => esc_html__( 'Select custom layout', 'kidscare' ),
				'desc'       => wp_kses_data( __( 'Select custom layout to display the site footer', 'kidscare' ) ),
				'std'        => 'inherit',
				'dependency' => array(
					"footer_type_{$cpt}" => array( 'custom' ),
				),
				'options'    => array(),
				'type'       => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
			),
			"footer_widgets_{$cpt}"         => array(
				'title'      => esc_html__( 'Footer widgets', 'kidscare' ),
				'desc'       => wp_kses_data( __( 'Select set of widgets to show in the footer', 'kidscare' ) ),
				'dependency' => array(
					"footer_type_{$cpt}" => array( 'default' ),
				),
				'std'        => 'footer_widgets',
				'options'    => array(),
				'type'       => 'select',
			),
			"footer_columns_{$cpt}"         => array(
				'title'      => esc_html__( 'Footer columns', 'kidscare' ),
				'desc'       => wp_kses_data( __( 'Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'kidscare' ) ),
				'dependency' => array(
					"footer_type_{$cpt}"    => array( 'default' ),
					"footer_widgets_{$cpt}" => array( '^hide' ),
				),
				'std'        => 0,
				'options'    => kidscare_get_list_range( 0, 6 ),
				'type'       => 'select',
			),
			"footer_wide_{$cpt}"            => array(
				'title'      => esc_html__( 'Footer fullwidth', 'kidscare' ),
				'desc'       => wp_kses_data( __( 'Do you want to stretch the footer to the entire window width?', 'kidscare' ) ),
				'dependency' => array(
					"footer_type_{$cpt}" => array( 'default' ),
				),
				'std'        => 0,
				'type'       => 'checkbox',
			),

			"widgets_info_{$cpt}"           => array(
				'title' => esc_html__( 'Additional panels', 'kidscare' ),
				'desc'  => '',
				'type'  => KIDSCARE_THEME_FREE ? 'hidden' : 'info',
			),
			"widgets_above_page_{$cpt}"     => array(
				'title'   => esc_html__( 'Widgets at the top of the page', 'kidscare' ),
				'desc'    => wp_kses_data( __( 'Select widgets to show at the top of the page (above content and sidebar)', 'kidscare' ) ),
				'std'     => 'hide',
				'options' => array(),
				'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
			),
			"widgets_above_content_{$cpt}"  => array(
				'title'   => esc_html__( 'Widgets above the content', 'kidscare' ),
				'desc'    => wp_kses_data( __( 'Select widgets to show at the beginning of the content area', 'kidscare' ) ),
				'std'     => 'hide',
				'options' => array(),
				'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
			),
			"widgets_below_content_{$cpt}"  => array(
				'title'   => esc_html__( 'Widgets below the content', 'kidscare' ),
				'desc'    => wp_kses_data( __( 'Select widgets to show at the ending of the content area', 'kidscare' ) ),
				'std'     => 'hide',
				'options' => array(),
				'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
			),
			"widgets_below_page_{$cpt}"     => array(
				'title'   => esc_html__( 'Widgets at the bottom of the page', 'kidscare' ),
				'desc'    => wp_kses_data( __( 'Select widgets to show at the bottom of the page (below content and sidebar)', 'kidscare' ) ),
				'std'     => 'hide',
				'options' => array(),
				'type'    => KIDSCARE_THEME_FREE ? 'hidden' : 'select',
			),
		);
	}
}


// Return lists with choises when its need in the admin mode
if ( ! function_exists( 'kidscare_options_get_list_choises' ) ) {
	add_filter( 'kidscare_filter_options_get_list_choises', 'kidscare_options_get_list_choises', 10, 2 );
	function kidscare_options_get_list_choises( $list, $id ) {
		if ( is_array( $list ) && count( $list ) == 0 ) {
			if ( strpos( $id, 'header_style' ) === 0 ) {
				$list = kidscare_get_list_header_styles( strpos( $id, 'header_style_' ) === 0 );
			} elseif ( strpos( $id, 'header_position' ) === 0 ) {
				$list = kidscare_get_list_header_positions( strpos( $id, 'header_position_' ) === 0 );
			} elseif ( strpos( $id, 'header_widgets' ) === 0 ) {
				$list = kidscare_get_list_sidebars( strpos( $id, 'header_widgets_' ) === 0, true );
			} elseif ( strpos( $id, '_scheme' ) > 0 ) {
				$list = kidscare_get_list_schemes( 'color_scheme' != $id );
			} elseif ( strpos( $id, 'sidebar_widgets' ) === 0 ) {
				$list = kidscare_get_list_sidebars( 'sidebar_widgets_single' != $id && ( strpos( $id, 'sidebar_widgets_' ) === 0 || strpos( $id, 'sidebar_widgets_single_' ) === 0 ), true );
			} elseif ( strpos( $id, 'sidebar_position_ss' ) === 0 ) {
				$list = kidscare_get_list_sidebars_positions_ss( strpos( $id, 'sidebar_position_ss_' ) === 0 );
			} elseif ( strpos( $id, 'sidebar_position' ) === 0 ) {
				$list = kidscare_get_list_sidebars_positions( strpos( $id, 'sidebar_position_' ) === 0 );
			} elseif ( strpos( $id, 'widgets_above_page' ) === 0 ) {
				$list = kidscare_get_list_sidebars( strpos( $id, 'widgets_above_page_' ) === 0, true );
			} elseif ( strpos( $id, 'widgets_above_content' ) === 0 ) {
				$list = kidscare_get_list_sidebars( strpos( $id, 'widgets_above_content_' ) === 0, true );
			} elseif ( strpos( $id, 'widgets_below_page' ) === 0 ) {
				$list = kidscare_get_list_sidebars( strpos( $id, 'widgets_below_page_' ) === 0, true );
			} elseif ( strpos( $id, 'widgets_below_content' ) === 0 ) {
				$list = kidscare_get_list_sidebars( strpos( $id, 'widgets_below_content_' ) === 0, true );
			} elseif ( strpos( $id, 'footer_style' ) === 0 ) {
				$list = kidscare_get_list_footer_styles( strpos( $id, 'footer_style_' ) === 0 );
			} elseif ( strpos( $id, 'footer_widgets' ) === 0 ) {
				$list = kidscare_get_list_sidebars( strpos( $id, 'footer_widgets_' ) === 0, true );
			} elseif ( strpos( $id, 'blog_style' ) === 0 ) {
				$list = kidscare_get_list_blog_styles( strpos( $id, 'blog_style_' ) === 0 );
			} elseif ( strpos( $id, 'post_type' ) === 0 ) {
				$list = kidscare_get_list_posts_types();
			} elseif ( strpos( $id, 'parent_cat' ) === 0 ) {
				$list = kidscare_array_merge( array( 0 => esc_html__( '- Select category -', 'kidscare' ) ), kidscare_get_list_categories() );
			} elseif ( strpos( $id, 'blog_animation' ) === 0 ) {
				$list = kidscare_get_list_animations_in();
			} elseif ( 'color_scheme_editor' == $id ) {
				$list = kidscare_get_list_schemes();
			} elseif ( strpos( $id, '_font-family' ) > 0 ) {
				$list = kidscare_get_list_load_fonts( true );
			}
		}
		return $list;
	}
}
