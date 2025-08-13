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
 * Adds Sorting Widgets
 * @since 1.0.6
 */
class Essential_Grids_Widget_Sorting extends Essential_Grids_Widget_Abstract {

	public function __construct() {
		parent::__construct(
			'ess-grid-widget-sorting',
			esc_attr__( 'Essential Grid Sorting', 'essential-grid' ),
			[
				'classname'   => 'widget_ess_grid_sorting',
				'description' => esc_attr__( 'Display the Sorting of a certain Grid (Grid Navigation Settings in Navigations tab of the Grid has to be set to Widget)', 'essential-grid' )
			]
		);
	}

	/**
	 * @inheritdoc
	 */
	protected function _widget_body( $alias, $instance ) {
		if ( Essential_Grid_Base::is_shortcode_with_handle_exist( $alias ) ) {
			$grid = new Essential_Grid();
			$my_grid = $grid->init_by_id( $instance['ess_grid'] );
			if ( ! $my_grid ) return false;
			$grid->output_grid_sorting();
		}
		return '';
	}
}

