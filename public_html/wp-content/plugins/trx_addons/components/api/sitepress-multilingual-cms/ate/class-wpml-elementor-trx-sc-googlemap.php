<?php

/**
 * Class WPML_Elementor_Trx_Sc_Googlemap
 */
class WPML_Elementor_Trx_Sc_Googlemap extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'markers';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'address', 'link' => array( 'url' ), 'html', 'title', 'description' );
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		$sc = __( 'Google map marker', 'trx_addons' );
		switch( $field ) {
			case 'address':
				return esc_html( sprintf( __( '%s: address or Lat,Lng', 'trx_addons' ), $sc ) );

			case 'html':
				return esc_html( sprintf( __( '%s: custom HTML', 'trx_addons' ), $sc ) );

			case 'title':
				return esc_html( sprintf( __( '%s: title', 'trx_addons' ), $sc ) );

			case 'description':
				return esc_html( sprintf( __( '%s: description', 'trx_addons' ), $sc ) );

			case 'url':
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
			case 'address':
				return 'LINE';

			case 'html':
				return 'VISUAL';

			case 'title':
				return 'LINE';

			case 'description':
				return 'VISUAL';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}

}
