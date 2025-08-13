<?php
/* Booked Appointments support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'kidscare_booked_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'kidscare_booked_theme_setup9', 9 );
	function kidscare_booked_theme_setup9() {
		if ( kidscare_exists_booked() ) {
			add_action( 'wp_enqueue_scripts', 'kidscare_booked_frontend_scripts', 1100 );
			add_filter( 'kidscare_filter_merge_styles', 'kidscare_booked_merge_styles' );
		}
		if ( is_admin() ) {
			add_filter( 'kidscare_filter_tgmpa_required_plugins', 'kidscare_booked_tgmpa_required_plugins' );
			add_filter( 'kidscare_filter_theme_plugins', 'kidscare_booked_theme_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'kidscare_booked_tgmpa_required_plugins' ) ) {
	
	function kidscare_booked_tgmpa_required_plugins( $list = array() ) {
		if ( kidscare_storage_isset( 'required_plugins', 'booked' ) && kidscare_storage_get_array( 'required_plugins', 'booked', 'install' ) !== false && kidscare_is_theme_activated() ) {
			$path = kidscare_get_plugin_source_path( 'plugins/booked/booked.zip' );
			if ( ! empty( $path ) || kidscare_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => kidscare_storage_get_array( 'required_plugins', 'booked', 'title' ),
					'slug'     => 'booked',
					'source'   => ! empty( $path ) ? $path : 'upload://booked.zip',
					'required' => false,
				);
			}
			if(false) {
                $path = kidscare_get_plugin_source_path('plugins/booked/booked-calendar-feeds.zip');
                if (!empty($path) || kidscare_get_theme_setting('tgmpa_upload')) {
                    $list[] = array(
                        'name' => esc_html__('Booked Calendar Feeds', 'kidscare'),
                        'slug' => 'booked-calendar-feeds',
                        'source' => !empty($path) ? $path : 'upload://booked-calendar-feeds.zip',
                        'version' => '1.1.5',
                        'required' => false,
                    );
                }
                $path = kidscare_get_plugin_source_path('plugins/booked/booked-frontend-agents.zip');
                if (!empty($path) || kidscare_get_theme_setting('tgmpa_upload')) {
                    $list[] = array(
                        'name' => esc_html__('Booked Front-End Agents', 'kidscare'),
                        'slug' => 'booked-frontend-agents',
                        'source' => !empty($path) ? $path : 'upload://booked-frontend-agents.zip',
                        'version' => '1.1.15',
                        'required' => false,
                    );
                }
                if (kidscare_storage_isset('required_plugins', 'woocommerce')) {
                    $path = kidscare_get_plugin_source_path('plugins/booked/booked-woocommerce-payments.zip');
                    if (!empty($path) || kidscare_get_theme_setting('tgmpa_upload')) {
                        $list[] = array(
                            'name' => esc_html__('Booked Payments with WooCommerce', 'kidscare'),
                            'slug' => 'booked-woocommerce-payments',
                            'source' => !empty($path) ? $path : 'upload://booked-woocommerce-payments.zip',
                            'version' => '1.4.9',
                            'required' => false,
                        );
                    }
                }
            }
		}
		return $list;
	}
}

// Filter theme-supported plugins list
if ( ! function_exists( 'kidscare_booked_theme_plugins' ) ) {
	
	function kidscare_booked_theme_plugins( $list = array() ) {
		if ( ! empty( $list['booked']['group'] ) ) {
			foreach ( $list as $k => $v ) {
				if ( substr( $k, 0, 6 ) == 'booked' ) {
					if ( empty( $v['group'] ) ) {
						$list[ $k ]['group'] = $list['booked']['group'];
					}
					if ( ! empty( $list['booked']['logo'] ) ) {
						$list[ $k ]['logo'] = strpos( $list['booked']['logo'], '//' ) !== false
												? $list['booked']['logo']
												: kidscare_get_file_url( "plugins/booked/{$list['booked']['logo']}" );
					}
				}
			}
		}
		return $list;
	}
}



// Check if plugin installed and activated
if ( ! function_exists( 'kidscare_exists_booked' ) ) {
	function kidscare_exists_booked() {
		return class_exists( 'booked_plugin' );
	}
}


// Enqueue styles for frontend
if ( ! function_exists( 'kidscare_booked_frontend_scripts' ) ) {
	
	function kidscare_booked_frontend_scripts() {
		if ( kidscare_is_on( kidscare_get_theme_option( 'debug_mode' ) ) ) {
			$kidscare_url = kidscare_get_file_url( 'plugins/booked/booked.css' );
			if ( '' != $kidscare_url ) {
				wp_enqueue_style( 'kidscare-booked', $kidscare_url, array(), null );
			}
		}
	}
}


// Merge custom styles
if ( ! function_exists( 'kidscare_booked_merge_styles' ) ) {
	
	function kidscare_booked_merge_styles( $list ) {
		$list[] = 'plugins/booked/booked.css';
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( kidscare_exists_booked() ) {
	require_once KIDSCARE_THEME_DIR . 'plugins/booked/booked-styles.php';
}