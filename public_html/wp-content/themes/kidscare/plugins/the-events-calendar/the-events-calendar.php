<?php
/* Tribe Events Calendar support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if ( ! function_exists( 'kidscare_tribe_events_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'kidscare_tribe_events_theme_setup1', 1 );
	function kidscare_tribe_events_theme_setup1() {
		add_filter( 'kidscare_filter_list_sidebars', 'kidscare_tribe_events_list_sidebars' );
	}
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'kidscare_tribe_events_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'kidscare_tribe_events_theme_setup3', 3 );
	function kidscare_tribe_events_theme_setup3() {
		if ( kidscare_exists_tribe_events() ) {

			// Section 'Tribe Events'
			kidscare_storage_merge_array(
				'options', '', array_merge(
					array(
						'events' => array(
							'title' => esc_html__( 'Events', 'kidscare' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the events pages', 'kidscare' ) ),
							'type'  => 'section',
						),
					),
					kidscare_options_get_list_cpt_options( 'events' )
				)
			);
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'kidscare_tribe_events_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'kidscare_tribe_events_theme_setup9', 9 );
	function kidscare_tribe_events_theme_setup9() {
		if ( kidscare_exists_tribe_events() ) {
			add_action( 'wp_enqueue_scripts', 'kidscare_tribe_events_frontend_scripts', 1100 );
			add_action( 'wp_enqueue_scripts', 'kidscare_tribe_events_responsive_styles', 2000 );
			add_filter( 'kidscare_filter_merge_styles', 'kidscare_tribe_events_merge_styles' );
			add_filter( 'kidscare_filter_merge_styles_responsive', 'kidscare_tribe_events_merge_styles_responsive' );
			add_filter( 'kidscare_filter_post_type_taxonomy', 'kidscare_tribe_events_post_type_taxonomy', 10, 2 );
			if ( ! is_admin() ) {
				add_filter( 'kidscare_filter_detect_blog_mode', 'kidscare_tribe_events_detect_blog_mode' );
				add_filter( 'kidscare_filter_get_post_categories', 'kidscare_tribe_events_get_post_categories' );
				add_filter( 'kidscare_filter_get_post_date', 'kidscare_tribe_events_get_post_date' );
			}
		}
		if ( is_admin() ) {
			add_filter( 'kidscare_filter_tgmpa_required_plugins', 'kidscare_tribe_events_tgmpa_required_plugins' );
		}

	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'kidscare_tribe_events_tgmpa_required_plugins' ) ) {
	
	function kidscare_tribe_events_tgmpa_required_plugins( $list = array() ) {
		if ( kidscare_storage_isset( 'required_plugins', 'the-events-calendar' ) && kidscare_storage_get_array( 'required_plugins', 'the-events-calendar', 'install' ) !== false ) {
			$list[] = array(
				'name'     => kidscare_storage_get_array( 'required_plugins', 'the-events-calendar', 'title' ),
				'slug'     => 'the-events-calendar',
				'required' => false,
			);
		}
		return $list;
	}
}

// Remove 'Tribe Events' section from Customizer
if ( ! function_exists( 'kidscare_tribe_events_customizer_register_controls' ) ) {
	add_action( 'customize_register', 'kidscare_tribe_events_customizer_register_controls', 100 );
	function kidscare_tribe_events_customizer_register_controls( $wp_customize ) {
		$wp_customize->remove_panel( 'tribe_customizer' );
	}
}


// Check if Tribe Events is installed and activated
if ( ! function_exists( 'kidscare_exists_tribe_events' ) ) {
	function kidscare_exists_tribe_events() {
		return class_exists( 'Tribe__Events__Main' );
	}
}

// Return true, if current page is any tribe_events page
if ( ! function_exists( 'kidscare_is_tribe_events_page' ) ) {
	function kidscare_is_tribe_events_page() {
		$rez = false;
		if ( kidscare_exists_tribe_events() ) {
			if ( ! is_search() ) {
				$rez = tribe_is_event() || tribe_is_event_query() || tribe_is_event_category() || tribe_is_event_venue() || tribe_is_event_organizer();
			}
		}
		return $rez;
	}
}

// Detect current blog mode
if ( ! function_exists( 'kidscare_tribe_events_detect_blog_mode' ) ) {
	
	function kidscare_tribe_events_detect_blog_mode( $mode = '' ) {
		if ( kidscare_is_tribe_events_page() ) {
			$mode = 'events';
		}
		return $mode;
	}
}

// Return taxonomy for current post type
if ( ! function_exists( 'kidscare_tribe_events_post_type_taxonomy' ) ) {
	
	function kidscare_tribe_events_post_type_taxonomy( $tax = '', $post_type = '' ) {
		if ( kidscare_exists_tribe_events() && Tribe__Events__Main::POSTTYPE == $post_type ) {
			$tax = Tribe__Events__Main::TAXONOMY;
		}
		return $tax;
	}
}

// Show categories of the current event
if ( ! function_exists( 'kidscare_tribe_events_get_post_categories' ) ) {
	
	function kidscare_tribe_events_get_post_categories( $cats = '' ) {
		if ( get_post_type() == Tribe__Events__Main::POSTTYPE ) {
			$cats = kidscare_get_post_terms( ', ', get_the_ID(), Tribe__Events__Main::TAXONOMY );
		}
		return $cats;
	}
}

// Return date of the current event
if ( ! function_exists( 'kidscare_tribe_events_get_post_date' ) ) {
	
	function kidscare_tribe_events_get_post_date( $dt = '' ) {
		if ( get_post_type() == Tribe__Events__Main::POSTTYPE ) {
			$dt = tribe_events_event_schedule_details( get_the_ID(), '', '' );
		}
		return $dt;
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'kidscare_tribe_events_frontend_scripts' ) ) {
	
	function kidscare_tribe_events_frontend_scripts() {
		if ( kidscare_is_tribe_events_page() ) {
			if ( kidscare_is_on( kidscare_get_theme_option( 'debug_mode' ) ) ) {
				$kidscare_url = kidscare_get_file_url( 'plugins/the-events-calendar/the-events-calendar.css' );
				if ( '' != $kidscare_url ) {
					wp_enqueue_style( 'kidscare-tribe-events', $kidscare_url, array(), null );
				}
			}
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'kidscare_tribe_events_responsive_styles' ) ) {
	
	function kidscare_tribe_events_responsive_styles() {
		if ( kidscare_is_tribe_events_page() ) {
			if ( kidscare_is_on( kidscare_get_theme_option( 'debug_mode' ) ) ) {
				$kidscare_url = kidscare_get_file_url( 'plugins/the-events-calendar/the-events-calendar-responsive.css' );
				if ( '' != $kidscare_url ) {
					wp_enqueue_style( 'kidscare-tribe-events-responsive', $kidscare_url, array(), null );
				}
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'kidscare_tribe_events_merge_styles' ) ) {
	
	function kidscare_tribe_events_merge_styles( $list ) {
		$list[] = 'plugins/the-events-calendar/the-events-calendar.css';
		return $list;
	}
}


// Merge responsive styles
if ( ! function_exists( 'kidscare_tribe_events_merge_styles_responsive' ) ) {
	
	function kidscare_tribe_events_merge_styles_responsive( $list ) {
		$list[] = 'plugins/the-events-calendar/the-events-calendar-responsive.css';
		return $list;
	}
}



// Add Tribe Events specific items into lists
//------------------------------------------------------------------------

// Add sidebar
if ( ! function_exists( 'kidscare_tribe_events_list_sidebars' ) ) {
	
	function kidscare_tribe_events_list_sidebars( $list = array() ) {
		$list['tribe_events_widgets'] = array(
			'name'        => esc_html__( 'Tribe Events Widgets', 'kidscare' ),
			'description' => esc_html__( 'Widgets to be shown on the Tribe Events pages', 'kidscare' ),
		);
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( kidscare_exists_tribe_events() ) {
	require_once KIDSCARE_THEME_DIR . 'plugins/the-events-calendar/the-events-calendar-styles.php'; 
}
