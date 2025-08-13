<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

/**
 * Add Gutenberg support
 */
class Essential_Grid_Builders_Gutenberg {

	/**
	 * path to build files
	 * 
	 * @var string
	 */
	protected $build_path;
	
	/**
	 * build files URL
	 * 
	 * @var string
	 */
	protected $build_url;

	public function __construct() {
		$this->build_path = ESG_PLUGIN_PATH . 'admin/includes/builders/gutenberg/build/';
		$this->build_url  = ESG_PLUGIN_URL . 'admin/includes/builders/gutenberg/build/';
		
		add_action( 'enqueue_block_assets', [ $this, 'enqueue_block_styles' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
	}

	/**
	 * enqueue styles for site editor iframe
	 *
	 * @return void
	 */
	public function enqueue_block_styles() {
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'essgrid-blocks-editor-css', $this->build_url . 'index.css', '', filemtime( $this->build_path . 'index.css' ) );
	}

	/**
	 * Enqueue Gutenberg editor blocks styles and scripts
	 *
	 * @return void
	 */
	public function enqueue_block_editor_assets() {
		global $pagenow;

		$this->enqueue_block_styles();

		//do not include wp-editor on widgets page 
		$deps = [ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components' ];
		if ( 'widgets.php' !== $pagenow ) {
			$deps[] = 'wp-editor';
		}

		wp_enqueue_script( 'essgrid-blocks-js', $this->build_url . 'index.js', $deps, filemtime( $this->build_path . 'index.js' ), [ 'in_footer' => true ] );
		wp_localize_script( 'essgrid-blocks-js', 'EssGridOptions', [ 'pluginurl' => ESG_PLUGIN_URL ] );
	}

}
