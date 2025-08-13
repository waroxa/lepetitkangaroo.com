<?php

/**
 * Class WPML_Elementor_Trx_Sc_Skills
 */
class WPML_Elementor_Trx_Sc_Skills extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'values';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'title', 'value' );
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		$sc = __( 'Skill item', 'trx_addons' );
		switch( $field ) {
			case 'title':
				return esc_html( sprintf( __( '%s: title', 'trx_addons' ), $sc ) );

			case 'value':
				return esc_html( sprintf( __( '%s: value', 'trx_addons' ), $sc ) );

			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'title':
				return 'LINE';

			case 'value':
				return 'LINE';

			default:
				return '';
		}
	}

}
