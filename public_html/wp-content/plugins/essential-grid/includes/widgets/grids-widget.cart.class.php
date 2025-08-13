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
 * ESG Cart Widget
 */
class Essential_Grids_Widget_Cart extends Essential_Grids_Widget_Abstract {

	public function __construct() {
		parent::__construct(
			'ess-grid-widget-cart',
			esc_attr__( 'Essential Grid WooCommerce Cart', 'essential-grid' ),
			[
				'classname'   => 'widget_ess_grid_cart',
				'description' => esc_attr__( 'Display the WooCommerce Cart of a certain Grid (Grid Navigation Settings in Navigations tab of the Grid has to be set to Widget)', 'essential-grid' )
			]
		);
	}

	/**
	 * @inheritdoc 
	 */
	protected function _widget_body( $alias, $instance ) {
		if ( Essential_Grid_Base::is_shortcode_with_handle_exist( $alias ) ) {
			$eg_nav = new Essential_Grid_Navigation();
			return $eg_nav->output_cart();
		}
		return '';
	}
}
