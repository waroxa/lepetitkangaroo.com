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
 * Adds Pagination Widgets
 * @since 1.0.6
 */
class Essential_Grids_Widget_Pagination_Left extends Essential_Grids_Widget_Abstract {

	public function __construct() {
		parent::__construct(
			'ess-grid-widget-pagination-left',
			esc_attr__( 'Essential Grid Pagination Left', 'essential-grid' ),
			[
				'classname'   => 'widget_ess_grid_pagination_left',
				'description' => esc_attr__( 'Display the Left Icon for pagination of a certain Grid (Grid Navigation Settings in Navigations tab of the Grid has to be set to Widget)', 'essential-grid' )
			]
		);
	}

	/**
	 * @inheritdoc
	 */
	protected function _widget_body( $alias, $instance ) {
		if ( Essential_Grid_Base::is_shortcode_with_handle_exist( $alias ) ) {
			$eg_nav = new Essential_Grid_Navigation();
			return $eg_nav->output_navigation_left();
		}
		return '';
	}

}
