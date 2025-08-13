<?php
/* Revolution Slider support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'kidscare_revslider_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'kidscare_revslider_theme_setup9', 9 );
	function kidscare_revslider_theme_setup9() {
		if ( is_admin() ) {
			add_filter( 'kidscare_filter_tgmpa_required_plugins', 'kidscare_revslider_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'kidscare_revslider_tgmpa_required_plugins' ) ) {
	
	function kidscare_revslider_tgmpa_required_plugins( $list = array() ) {
		if ( kidscare_storage_isset( 'required_plugins', 'revslider' ) && kidscare_storage_get_array( 'required_plugins', 'revslider', 'install' ) !== false && kidscare_is_theme_activated() ) {
			$path = kidscare_get_plugin_source_path( 'plugins/revslider/revslider.zip' );
			if ( ! empty( $path ) || kidscare_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => kidscare_storage_get_array( 'required_plugins', 'revslider', 'title' ),
					'slug'     => 'revslider',
        	'version'  => '6.7.28',
					'source'   => ! empty( $path ) ? $path : 'upload://revslider.zip',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Check if RevSlider installed and activated
if ( ! function_exists( 'kidscare_exists_revslider' ) ) {
	function kidscare_exists_revslider() {
		return function_exists( 'rev_slider_shortcode' );
	}
}