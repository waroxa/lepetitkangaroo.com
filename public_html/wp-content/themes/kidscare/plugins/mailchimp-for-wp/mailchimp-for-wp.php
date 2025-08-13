<?php
/* Mail Chimp support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'kidscare_mailchimp_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'kidscare_mailchimp_theme_setup9', 9 );
	function kidscare_mailchimp_theme_setup9() {
		if ( kidscare_exists_mailchimp() ) {
			add_action( 'wp_enqueue_scripts', 'kidscare_mailchimp_frontend_scripts', 1100 );
			add_filter( 'kidscare_filter_merge_styles', 'kidscare_mailchimp_merge_styles' );
		}
		if ( is_admin() ) {
			add_filter( 'kidscare_filter_tgmpa_required_plugins', 'kidscare_mailchimp_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'kidscare_mailchimp_tgmpa_required_plugins' ) ) {
	
	function kidscare_mailchimp_tgmpa_required_plugins( $list = array() ) {
		if ( kidscare_storage_isset( 'required_plugins', 'mailchimp-for-wp' ) && kidscare_storage_get_array( 'required_plugins', 'mailchimp-for-wp', 'install' ) !== false ) {
			$list[] = array(
				'name'     => kidscare_storage_get_array( 'required_plugins', 'mailchimp-for-wp', 'title' ),
				'slug'     => 'mailchimp-for-wp',
				'required' => false,
			);
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'kidscare_exists_mailchimp' ) ) {
	function kidscare_exists_mailchimp() {
		return function_exists( '__mc4wp_load_plugin' ) || defined( 'MC4WP_VERSION' );
	}
}



// Custom styles and scripts
//------------------------------------------------------------------------

// Enqueue styles for frontend
if ( ! function_exists( 'kidscare_mailchimp_frontend_scripts' ) ) {
	
	function kidscare_mailchimp_frontend_scripts() {
		if ( kidscare_is_on( kidscare_get_theme_option( 'debug_mode' ) ) ) {
			$kidscare_url = kidscare_get_file_url( 'plugins/mailchimp-for-wp/mailchimp-for-wp.css' );
			if ( '' != $kidscare_url ) {
				wp_enqueue_style( 'kidscare-mailchimp', $kidscare_url, array(), null );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'kidscare_mailchimp_merge_styles' ) ) {
	
	function kidscare_mailchimp_merge_styles( $list ) {
		$list[] = 'plugins/mailchimp-for-wp/mailchimp-for-wp.css';
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( kidscare_exists_mailchimp() ) {
	require_once KIDSCARE_THEME_DIR . 'plugins/mailchimp-for-wp/mailchimp-for-wp-styles.php';
}

