<?php

/**
 * Class WPML_Elementor_Trx_Sc_Socials
 */
class WPML_Elementor_Trx_Sc_Socials extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'icons';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'title', 'link' );
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		$sc = __( 'Social item', 'trx_addons' );
		switch( $field ) {
			case 'title':
				return esc_html( sprintf( __( '%s: title', 'trx_addons' ), $sc ) );

			case 'link':
				return esc_html( sprintf( __( '%s: link URL', 'trx_addons' ), $sc ) );

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

			case 'link':
				return 'LINE';

			default:
				return '';
		}
	}

}
