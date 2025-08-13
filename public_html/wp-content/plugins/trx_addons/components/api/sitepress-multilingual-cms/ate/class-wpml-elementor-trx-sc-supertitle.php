<?php

/**
 * Class WPML_Elementor_Trx_Sc_Supertitle
 */
class WPML_Elementor_Trx_Sc_Supertitle extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'items';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'text' );
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		$sc = __( 'Super title item', 'trx_addons' );
		switch( $field ) {
			case 'text':
				return esc_html( sprintf( __( '%s: text', 'trx_addons' ), $sc ) );

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
			case 'text':
				return 'LINE';

			default:
				return '';
		}
	}

}
