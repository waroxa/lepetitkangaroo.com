<?php
/* QuickCal support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'kidscare_quickcal_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'kidscare_quickcal_theme_setup9', 9 );
	function kidscare_quickcal_theme_setup9() {
		if ( kidscare_exists_quickcal() ) {
			add_action( 'wp_enqueue_scripts', 'kidscare_quickcal_frontend_scripts', 1100 );
			add_filter( 'kidscare_filter_merge_styles', 'kidscare_quickcal_merge_styles' );
		}
		if ( is_admin() ) {
			add_filter( 'kidscare_filter_tgmpa_required_plugins', 'kidscare_quickcal_tgmpa_required_plugins' );
			add_filter( 'kidscare_filter_theme_plugins', 'kidscare_quickcal_theme_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'kidscare_quickcal_tgmpa_required_plugins' ) ) {
	
	function kidscare_quickcal_tgmpa_required_plugins( $list = array() ) {
		if ( kidscare_storage_isset( 'required_plugins', 'quickcal' ) && kidscare_storage_get_array( 'required_plugins', 'quickcal', 'install' ) !== false && kidscare_is_theme_activated() ) {
			$path = kidscare_get_plugin_source_path( 'plugins/quickcal/quickcal.zip' );
			if ( ! empty( $path ) || kidscare_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => kidscare_storage_get_array( 'required_plugins', 'quickcal', 'title' ),
					'slug'     => 'quickcal',
					'source'   => ! empty( $path ) ? $path : 'upload://quickcal.zip',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Filter theme-supported plugins list
if ( ! function_exists( 'kidscare_quickcal_theme_plugins' ) ) {
	
	function kidscare_quickcal_theme_plugins( $list = array() ) {
		if ( ! empty( $list['quickcal']['group'] ) ) {
			foreach ( $list as $k => $v ) {
				if ( substr( $k, 0, 6 ) == 'quickcal' ) {
					if ( empty( $v['group'] ) ) {
						$list[ $k ]['group'] = $list['quickcal']['group'];
					}
					if ( ! empty( $list['quickcal']['logo'] ) ) {
						$list[ $k ]['logo'] = strpos( $list['quickcal']['logo'], '//' ) !== false
												? $list['quickcal']['logo']
												: kidscare_get_file_url( "plugins/quickcal/{$list['quickcal']['logo']}" );
					}
				}
			}
		}
		return $list;
	}
}



// Check if plugin installed and activated
if ( ! function_exists( 'kidscare_exists_quickcal' ) ) {
	function kidscare_exists_quickcal() {
		return class_exists( 'quickcal_plugin' );
	}
}


// Enqueue styles for frontend
if ( ! function_exists( 'kidscare_quickcal_frontend_scripts' ) ) {
	
	function kidscare_quickcal_frontend_scripts() {
		if ( kidscare_is_on( kidscare_get_theme_option( 'debug_mode' ) ) ) {
			$kidscare_url = kidscare_get_file_url( 'plugins/quickcal/quickcal.css' );
			if ( '' != $kidscare_url ) {
				wp_enqueue_style( 'kidscare-quickcal', $kidscare_url, array(), null );
			}
		}
	}
}


// Merge custom styles
if ( ! function_exists( 'kidscare_quickcal_merge_styles' ) ) {
	
	function kidscare_quickcal_merge_styles( $list ) {
		$list[] = 'plugins/quickcal/quickcal.css';
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( kidscare_exists_quickcal() ) {
	require_once KIDSCARE_THEME_DIR . 'plugins/quickcal/quickcal-styles.php';
}