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
 * Abstract class for ESG Widgets
 */
abstract class Essential_Grids_Widget_Abstract extends WP_Widget {

	/**
	 * @param string $slug
	 * @param string $title
	 * @param array  $widget_ops
	 */
	public function __construct( $slug, $title, $widget_ops ) {
		parent::__construct( $slug, $title, $widget_ops );
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @param array $instance Current settings.
	 * @return string Default return is 'noform'.
	 */
	public function form( $instance ) {
		$arrGrids = Essential_Grid_Db::get_entity( 'grids' )->get_grids_column( 'id', 'name' );
		if ( empty( $arrGrids ) ) {
			echo esc_attr__( "No Essential Grids found, Please create at least one!", 'essential-grid' );
		} else {
			$field      = "ess_grid";
			$fieldTitle = "ess_grid_title";

			$gridID = isset( $instance[ $field ] ) ? $instance[ $field ] : '';
			$title  = isset( $instance[ $fieldTitle ] ) ? $instance[ $fieldTitle ] : '';

			$fieldID   = $this->get_field_id( $field );
			$fieldName = $this->get_field_name( $field );

			$fieldTitle_ID   = $this->get_field_id( $fieldTitle );
			$fieldTitle_Name = $this->get_field_name( $fieldTitle );

			?>
			<label for="<?php echo esc_attr( $fieldTitle_ID ); ?>">
				<?php esc_html_e( 'Title', 'essential-grid' ); ?>:
			</label>
			<input type="text" name="<?php echo esc_attr( $fieldTitle_Name ); ?>"
				   id="<?php echo esc_attr( $fieldTitle_ID ); ?>" value="<?php echo esc_attr( $title ); ?>"
				   class="widefat">
			<div class="div13"></div>

			<label for="<?php echo esc_attr( $fieldID ); ?>">
				<?php esc_html_e( 'Choose Essential Grid', 'essential-grid' ); ?>:
			</label>
			<select name="<?php echo esc_attr( $fieldName ); ?>" id="<?php echo esc_attr( $fieldID ); ?>">
			<?php
			foreach ( $arrGrids as $id => $name ) {
				printf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $id ),
					selected( $id, $gridID ),
					esc_html( $name )
				);
			}
			?>
			</select>
			<div class="esg-widget-separator"></div>
			<?php
		}

		return 'noform';
	}

	/**
	 * widget output
	 * 
	 * @param array $args      An array of widget arguments
	 * @param array $instance  The current widget instance's settings.
	 */
	public function widget( $args, $instance ) {
		if ( empty( $instance['ess_grid'] ) ) {
			return false;
		}

		$alias = Essential_Grid_Db::get_entity( 'grids' )->get_alias_by_id( $instance['ess_grid'] );
		if ( empty( $alias ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- These arguments contain arbitrary HTML and cannot be properly escaped
		echo $args["before_widget"];

		if ( ! empty( $instance['ess_grid_title'] ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- These arguments contain arbitrary HTML and cannot be properly escaped
			echo $args["before_title"] . esc_html( $instance['ess_grid_title'] ) . $args["after_title"];
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- These arguments contain arbitrary HTML and cannot be properly escaped
		echo $this->_widget_body( $alias, $instance );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- These arguments contain arbitrary HTML and cannot be properly escaped
		echo $args["after_widget"];
	}

	/**
	 * @param string $alias
	 * @param array $instance  The current widget instance's settings.
	 *
	 * @return string
	 */
	abstract protected function _widget_body( $alias, $instance );

}
