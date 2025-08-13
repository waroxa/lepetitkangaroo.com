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

class Essential_Grids_Widget extends Essential_Grids_Widget_Abstract {

	public function __construct() {
		parent::__construct(
			'ess-grid-widget',
			esc_attr__( 'Essential Grid', 'essential-grid' ),
			[
				'classname'   => 'widget_ess_grid',
				'description' => esc_attr__( 'Displays certain Essential Grid on the page', 'essential-grid' )
			]
		);
	}

	/**
	 * the form
	 */
	public function form( $instance ) {
		$arrGrids = Essential_Grid_Db::get_entity( 'grids' )->get_grids_column( 'id', 'name' );
		if ( empty( $arrGrids ) ) {
			echo esc_attr__( "No Essential Grids found, Please create at least one!", 'essential-grid' );
		} else {
			$field      = "ess_grid";
			$fieldPages = "ess_grid_pages";
			$fieldCheck = "ess_grid_homepage";
			$fieldTitle = "ess_grid_title";

			$gridID     = isset( $instance[ $field ] ) ? $instance[ $field ] : '';
			$homepage   = isset( $instance[ $fieldCheck ] ) ? $instance[ $fieldCheck ] : '';
			$pagesValue = isset( $instance[ $fieldPages ] ) ? $instance[ $fieldPages ] : '';
			$title      = isset( $instance[ $fieldTitle ] ) ? $instance[ $fieldTitle ] : '';

			$fieldID   = $this->get_field_id( $field );
			$fieldName = $this->get_field_name( $field );

			$fieldID_check   = $this->get_field_id( $fieldCheck );
			$fieldName_check = $this->get_field_name( $fieldCheck );

			$fieldPages_ID   = $this->get_field_id( $fieldPages );
			$fieldPages_Name = $this->get_field_name( $fieldPages );

			$fieldTitle_ID   = $this->get_field_id( $fieldTitle );
			$fieldTitle_Name = $this->get_field_name( $fieldTitle );

			?>
			<div class="div13"></div>
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
			<div class="div13"></div>

			<label for="<?php echo esc_attr( $fieldID_check ); ?>">
				<?php esc_html_e( 'Home Page Only', 'essential-grid' ); ?>:
			</label>
			<input type="checkbox" class="esg-widget-checkbox" name="<?php echo esc_attr( $fieldName_check ); ?>"
				   id="<?php echo esc_attr( $fieldID_check ); ?>" <?php checked( $homepage, 'on' ); ?> >
			<div class="div13"></div>
			
			<label for="<?php echo esc_attr( $fieldPages_ID ); ?>">
				<?php esc_html_e( 'Pages: (example: 3,8,15)', 'essential-grid' ); ?>
			</label>
			<input type="text" name="<?php echo esc_attr( $fieldPages_Name ); ?>"
				   id="<?php echo esc_attr( $fieldPages_ID ); ?>" value="<?php echo esc_attr( $pagesValue ); ?>">
			<div class="div13"></div>
			<?php
		}
	}

	/**
	 * widget output
	 */
	public function widget( $args, $instance ) {

		// check if widget should be shown
		$pages = isset( $instance['ess_grid_homepage'] ) && 'on' == $instance['ess_grid_homepage'] ? 'homepage' : '';
		if ( ! empty( $instance['ess_grid_pages'] ) ) {
			$pages .= ( empty( $pages ) ? '' : ',' ) . $instance['ess_grid_pages'];
		}
		if ( ! empty( $pages ) && ! $this->_check_pages($pages) ) {
			return false;
		}

		parent::widget( $args, $instance );
	}

	/**
	 * @inheritdoc
	 */
	protected function _widget_body( $alias, $instance ) {
		return Essential_Grid::register_shortcode( [ 'alias' => $alias ] );
	}

	/**
	 * check the current page against widget settings
	 * 
	 * @param string $pages
	 * @return bool
	 */
	private function _check_pages( $pages ) {
		if ( empty( $pages ) ) return false;
		
		$pages_list = array_filter( explode( ',', $pages ), function( $i ) { return is_numeric($i) || 'homepage' == $i; } );
		
		return array_search( $this->_get_current_page_id(), $pages_list ) !== false;
	}

	/**
	 * get the current page id
	 * 
	 * @return string
	 **/
	private function _get_current_page_id() {
		$id = '';

		if ( is_front_page() == true || is_home() == true ) {
			$id = 'homepage';
		} else {
			global $post;
			$id = ( isset( $post->ID ) ) ? $post->ID : $id;
		}

		return $id;
	}
	
}
