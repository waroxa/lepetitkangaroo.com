<?php

/**
 * Class WPML_Elementor_Trx_Sc_Icons
 */
class WPML_Elementor_Trx_Sc_Icons extends WPML_Elementor_Trx_Module_With_Items {

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
		// This way need class 'WPML_Elementor_Trx_Module_With_Items'
		// (allow using subkeys in the inner array)
//		return array( 'char', 'title', 'description', 'link' => array( 'url' ), 'image' => array( 'image_url' => 'url' ) );

		// This way based on core WPML class 'WPML_Elementor_Module_With_Items'
		// (not support subkeys in the inner array)
		return array( 'char', 'title', 'description', 'link' => array( 'url' ) );
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		$sc = __( 'Icons item', 'trx_addons' );
		switch( $field ) {
			case 'char':
				return esc_html( sprintf( __( '%s: char', 'trx_addons' ), $sc ) );

			case 'title':
				return esc_html( sprintf( __( '%s: title', 'trx_addons' ), $sc ) );

			case 'description':
				return esc_html( sprintf( __( '%s: description', 'trx_addons' ), $sc ) );

			case 'url':
				return esc_html( sprintf( __( '%s: link URL', 'trx_addons' ), $sc ) );

			case 'image_url':
				return esc_html( sprintf( __( '%s: image URL', 'trx_addons' ), $sc ) );

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
			case 'char':
				return 'LINE';

			case 'title':
				return 'LINE';

			case 'description':
				return 'AREA';

			case 'url':
				return 'LINK';

			case 'image_url':
				return 'LINK';

			default:
				return '';
		}
	}

}
