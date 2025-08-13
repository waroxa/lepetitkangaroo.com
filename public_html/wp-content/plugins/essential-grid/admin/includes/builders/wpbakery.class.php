<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

/**
 * Add WPBakery support
 */
class Essential_Grid_Builders_WPBakery {
	
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'maybe_init' ] );
	}

	/**
	 * maybe init wpbakery
	 *
	 * @return void
	 */
	public function maybe_init() {
		if ( ! function_exists( 'is_user_logged_in' ) || ! is_user_logged_in() ) {
			return;
		}

		add_action( 'vc_before_init', [ $this, 'init' ] );
	}

	/**
	 * add shortcode to wpbakery
	 *
	 * @return void
	 */
	public function init() {
		if ( ! defined( 'WPB_VC_VERSION' ) || ! function_exists( 'vc_map' ) ) {
			return;
		}

		vc_map( [
			'name'                    => esc_attr__( 'Essential Grid', 'essential-grid' ),
			'base'                    => 'ess_grid',
			'icon'                    => 'icon-wpb-ess-grid',
			'category'                => esc_attr__( 'Content', 'essential-grid' ),
			'show_settings_on_create' => false,
			'js_view'                 => 'VcEssentialGrid',
			'params'                  => [
				[
					'type'        => 'ess_grid_shortcode',
					'heading'     => esc_attr__( 'Alias', 'essential-grid' ),
					'param_name'  => 'alias',
					'admin_label' => true,
					'value'       => ''
				],
				[
					'type'        => 'ess_grid_shortcode',
					'heading'     => esc_attr__( 'Settings', 'essential-grid' ),
					'param_name'  => 'settings',
					'admin_label' => true,
					'value'       => ''
				],
				[
					'type'        => 'ess_grid_shortcode',
					'heading'     => esc_attr__( 'Layers', 'essential-grid' ),
					'param_name'  => 'layers',
					'admin_label' => true,
					'value'       => ''
				],
				[
					'type'        => 'ess_grid_shortcode',
					'heading'     => esc_attr__( 'Special', 'essential-grid' ),
					'param_name'  => 'special',
					'admin_label' => true,
					'value'       => ''
				]
			]
		] );
	}
	
}
