<?php
/* Essential Grid support functions
------------------------------------------------------------------------------- */


// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'kidscare_essential_grid_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'kidscare_essential_grid_theme_setup9', 9 );
	function kidscare_essential_grid_theme_setup9() {
		if ( kidscare_exists_essential_grid() ) {
			add_action( 'wp_enqueue_scripts', 'kidscare_essential_grid_frontend_scripts', 1100 );
			add_filter( 'kidscare_filter_merge_styles', 'kidscare_essential_grid_merge_styles' );
		}
		if ( is_admin() ) {
			add_filter( 'kidscare_filter_tgmpa_required_plugins', 'kidscare_essential_grid_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'kidscare_essential_grid_tgmpa_required_plugins' ) ) {
	
	function kidscare_essential_grid_tgmpa_required_plugins( $list = array() ) {
		if ( kidscare_storage_isset( 'required_plugins', 'essential-grid' ) && kidscare_storage_get_array( 'required_plugins', 'essential-grid', 'install' ) !== false && kidscare_is_theme_activated() ) {
			$path = kidscare_get_plugin_source_path( 'plugins/essential-grid/essential-grid.zip' );
			if ( ! empty( $path ) || kidscare_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => kidscare_storage_get_array( 'required_plugins', 'essential-grid', 'title' ),
					'slug'     => 'essential-grid',
					'version'  => '3.1.7',
					'source'   => ! empty( $path ) ? $path : 'upload://essential-grid.zip',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( ! function_exists( 'kidscare_exists_essential_grid' ) ) {
	function kidscare_exists_essential_grid() {
		return defined( 'ESG_PLUGIN_PATH' ) || defined( 'EG_PLUGIN_PATH' );
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'kidscare_essential_grid_frontend_scripts' ) ) {
	
	function kidscare_essential_grid_frontend_scripts() {
		if ( kidscare_is_on( kidscare_get_theme_option( 'debug_mode' ) ) ) {
			$kidscare_url = kidscare_get_file_url( 'plugins/essential-grid/essential-grid.css' );
			if ( '' != $kidscare_url ) {
				wp_enqueue_style( 'kidscare-essential-grid', $kidscare_url, array(), null );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'kidscare_essential_grid_merge_styles' ) ) {
	
	function kidscare_essential_grid_merge_styles( $list ) {
		$list[] = 'plugins/essential-grid/essential-grid.css';
		return $list;
	}
}


// Check if Ess. Grid installed and activated
if ( !function_exists( 'kidscare_essgrids_get_popular_posts_query' ) ) {
  add_filter( 'essgrid_get_posts', 'kidscare_essgrids_get_popular_posts_query', 10, 2 );
  add_filter( 'essgrid_get_posts_by_ids_query', 'kidscare_essgrids_get_popular_posts_query', 10, 2 );
  add_filter( 'essgrid_get_popular_posts_query', 'kidscare_essgrids_get_popular_posts_query', 10, 2 );
  add_filter( 'essgrid_get_related_posts', 'kidscare_essgrids_get_popular_posts_query', 10, 2 );
  add_filter( 'essgrid_get_related_posts_query', 'kidscare_essgrids_get_popular_posts_query', 10, 2 );
  function kidscare_essgrids_get_popular_posts_query($args, $post_id) {
    if (kidscare_exists_tribe_events()) {
      $args['tribe_suppress_query_filters'] = true;
    }
    return $args;
  }
}