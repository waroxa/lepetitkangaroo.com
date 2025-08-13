<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

/**
 * Add Builders support
 */
class Essential_Grid_Builders {

	/**
	 * Instance of this class.
	 * @var null|object
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {
		new Essential_Grid_Builders_WPBakery();
		new Essential_Grid_Builders_Elementor();
		new Essential_Grid_Builders_Gutenberg();

		if ( $this->check_pagenow() ) {
			add_action( 'in_admin_footer', [ 'Essential_Grid_Dialogs', 'essgrid_add_shortcode_builder' ] );
			add_action( 'customize_controls_print_footer_scripts', [ 'Essential_Grid_Dialogs', 'essgrid_add_shortcode_builder' ] );
		}
	}

	/**
	 * check if current page should have shortcode builder
	 *
	 * @return bool
	 */
	public function check_pagenow() {
		global $pagenow;

		return in_array( $pagenow, [ 'widgets.php', 'customize.php', 'site-editor.php' ] );
	}
}
