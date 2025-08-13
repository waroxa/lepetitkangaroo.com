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
 * Adds Filter Widgets
 * @since 1.0.6
 */
class Essential_Grids_Widget_Filter extends Essential_Grids_Widget_Abstract {
	
	public function __construct() {
		parent::__construct(
			'ess-grid-widget-filter',
			esc_attr__( 'Essential Grid Filter', 'essential-grid' ),
			[
				'classname'   => 'widget_ess_grid_filter',
				'description' => esc_attr__( 'Display the filter of a certain Grid (Grid Navigation Settings in Navigations tab of the Grid has to be set to Widget)', 'essential-grid' )
			]
		);
	}

	/**
	 * @inheritdoc
	 */
	protected function _widget_body( $alias, $instance ) {
		if ( Essential_Grid_Base::is_shortcode_with_handle_exist( $alias ) ) {
			return Essential_Grid::register_shortcode_filter( [ 'alias' => $alias, 'id' => 'filter' ] );
		}
		return '';
	}

}
