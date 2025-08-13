<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Add Elementor support
 */
class Essential_Grid_Builders_Elementor {

	const MIN_PHP_VER = '7.0';
	const MIN_ELEMENTOR_VER = '2.0.0';

	public function __construct() {
		add_action( 'init', [ $this, 'maybe_init' ] );
	}

	/**
	 * maybe init Elementor
	 *
	 * @return void
	 */
	public function maybe_init() {

		if ( 
			version_compare( PHP_VERSION, self::MIN_PHP_VER, '<' )
			|| ! did_action( 'elementor/loaded' )
			|| ! version_compare( ELEMENTOR_VERSION, self::MIN_ELEMENTOR_VER, '>=' ) 
		) {
			return;
		}

		add_filter( 'essgrid_get_js_to_footer', [ $this, 'disable_js_to_footer' ] );
		add_action( 'essgrid_output_grid_javascript_options', [ $this, 'disable_wait_for_viewport' ] );

		if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '<' ) ) {
			add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
		} else {
			add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		}

		// Register Styles/Scripts
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- not required here
		if ( isset($_GET['action']) && 'elementor' == $_GET['action'] ) {
			$assets = Essential_Grid_Assets::get_instance();
			add_action( 'elementor/editor/after_enqueue_styles', [ $assets, 'enqueue_styles' ], 20 );
			add_action( 'elementor/editor/after_enqueue_scripts', [ $assets, 'enqueue_scripts' ], 20 );
			add_filter( 'elementor/editor/footer', [ 'Essential_Grid_Dialogs', 'essgrid_add_shortcode_builder' ], 20 );
		}
	}

	/**
	 * Register widget
	 * 
	 * @return void
	 */
	public function register_widgets() {
		require_once( ESG_PLUGIN_ADMIN_PATH . '/includes/builders/elementor/widget.class.php' );
		
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '<' ) ) {
			$widgets_manager->register_widget_type( new Essential_Grid_Builders_Elementor_Widget() );
		} else {
			$widgets_manager->register( new Essential_Grid_Builders_Elementor_Widget() );
		}
	}

	/**
	 * @return bool
	 */
	private function _is_elementor_preview() {
		return ( isset( $_GET['action'] ) && 'elementor' == $_GET['action'] ) // phpcs:ignore WordPress.Security.NonceVerification -- not required here
		       || ( isset( $_POST['action'] ) && 'elementor_ajax' == $_POST['action'] ) // phpcs:ignore WordPress.Security.NonceVerification -- not required here
		       || isset( $_GET['elementor-preview'] ); // phpcs:ignore WordPress.Security.NonceVerification -- not required here
	}
	
	/**
	 * disable js in footer for elementor preview cause ESG wont work in elementor frontend editor
	 * 
	 * @param string $value
	 * @return string
	 */
	public function disable_js_to_footer( $value ) {
		return $this->_is_elementor_preview() ? 'false' : $value;
	}
	
	/**
	 * disable wait for viewport to init grid immediately
	 * 
	 * @param Essential_Grid $grid
	 * @return void
	 */
	public function disable_wait_for_viewport( $grid ) {
		if ( ! $this->_is_elementor_preview() ) return;
		
		echo ",\n";
		echo '        waitForViewport:"off"';
	}

}
