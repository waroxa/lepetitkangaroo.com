<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */
 
if( !defined( 'ABSPATH') ) exit();

if(!class_exists('ThemePunch_Fonts')) {
	 
	class ThemePunch_Fonts {

		/**
		 * register icon fonts
		 */
		public static function register_icon_fonts() {
			wp_register_style( 'tp-font-awesome', ESG_PLUGIN_URL . 'public/assets/font/font-awesome/css/fontawesome.css', [], ESG_REVISION );
			wp_register_style( 'tp-font-awesome-solid', ESG_PLUGIN_URL . 'public/assets/font/font-awesome/css/solid.css', [], ESG_REVISION );
			wp_register_style( 'tp-font-awesome-brands', ESG_PLUGIN_URL . 'public/assets/font/font-awesome/css/brands.css', [], ESG_REVISION );
			wp_register_style( 'tp-font-awesome-esg-shims', ESG_PLUGIN_URL . 'public/assets/font/font-awesome/css/esg-shims.css', [], ESG_REVISION );

			wp_register_style( 'tp-fontello', ESG_PLUGIN_URL . 'public/assets/font/fontello/css/fontello.css', [], ESG_REVISION );
			wp_register_style( 'tp-stroke-7', ESG_PLUGIN_URL . 'public/assets/font/pe-icon-7-stroke/css/pe-icon-7-stroke.css', [], ESG_REVISION );
		}

		/**
		 * enqueue font awesome
		 */
		public static function enqueue_font_awesome() {
			wp_enqueue_style( 'tp-font-awesome' );
			wp_enqueue_style( 'tp-font-awesome-solid' );
			wp_enqueue_style( 'tp-font-awesome-brands' );
			wp_enqueue_style( 'tp-font-awesome-esg-shims' );
		}
		
		/**
		 * enqueue all fonts
		 * 
		 * @param string $area  backend / frontend
		 */
		public static function enqueue_icon_fonts( $area ) {
			$enable_fontello = get_option( 'tp_eg_global_enable_fontello', 'backfront' );
			$enable_font_awesome = get_option( 'tp_eg_global_enable_font_awesome', 'false' );
			$enable_pe7 = get_option( 'tp_eg_global_enable_pe7', 'false' );
			
			if ( "admin" == $area ) {
				wp_enqueue_style( 'tp-fontello' );
				if ( "false" != $enable_pe7 ) wp_enqueue_style( 'tp-stroke-7' );
				if ( "false" != $enable_font_awesome ) self::enqueue_font_awesome();
			} else {
				if ( "backfront" == $enable_fontello ) wp_enqueue_style( 'tp-fontello' );
				if ( "backfront" == $enable_font_awesome ) self::enqueue_font_awesome();
				if ( "backfront" == $enable_pe7 ) wp_enqueue_style( 'tp-stroke-7' );
			}
		}
		
		/**
		 * register all fonts
		 */
		public static function propagate_default_fonts($networkwide = false){
			global $wpdb;
			
			$default = [];
			$default = apply_filters('essgrid_add_default_fonts', $default); // will be obsolete soon, use tp_add_default_fonts instead
			$default = apply_filters('tp_add_default_fonts', $default);
			
			if (function_exists('is_multisite') && is_multisite() && $networkwide) { 
				// do for each existing site
				// Get all blog ids and create tables
				$sites = get_sites();
				foreach ($sites as $site) {
					switch_to_blog($site->blog_id);
					self::_propagate_default_fonts($default);
					// 2.2.5
					restore_current_blog();
				}
			} else {
				self::_propagate_default_fonts($default);
			}
		}
		
		/**
		 * register all fonts modified for multisite
		 * @since: 1.5.0
		 */
		public static function _propagate_default_fonts($default){
			$fonts = get_option('tp-google-fonts', []);
			if (empty($fonts)) {
				update_option('tp-google-fonts', $default);
				self::invalidate_privacy();
			}
		}

		/**
		 * real cookie banner: invalidate presets cache so Google Fonts gets shown in scanner
		 */
		protected static function invalidate_privacy() {
			if (function_exists('wp_rcb_invalidate_presets_cache')) {
				wp_rcb_invalidate_presets_cache();
			}
		}
		
	}
}
