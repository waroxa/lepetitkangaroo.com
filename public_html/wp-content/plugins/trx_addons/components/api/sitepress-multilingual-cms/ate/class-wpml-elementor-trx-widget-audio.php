<?php

/**
 * Class WPML_Elementor_Trx_Widget_Audio
 */
class WPML_Elementor_Trx_Widget_Audio extends WPML_Elementor_Trx_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'media';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		// This way need class 'WPML_Elementor_Trx_Module_With_Items'
		// (allow using subkeys in the inner array)
//		return array( 'url', 'embed', 'caption', 'author', 'description', 'cover' => array( 'cover_url' => 'url' ) );

		// This way based on core WPML class 'WPML_Elementor_Module_With_Items'
		// (not support subkeys in the inner array)
		return array( 'url', 'embed', 'caption', 'author', 'description' );
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		$sc = __( 'Audio item', 'trx_addons' );
		switch( $field ) {
			case 'url':
				return esc_html( sprintf( __( '%s: audio URL', 'trx_addons' ), $sc ) );

			case 'embed':
				return esc_html( sprintf( __( '%s: embed code', 'trx_addons' ), $sc ) );

			case 'caption':
				return esc_html( sprintf( __( '%s: caption', 'trx_addons' ), $sc ) );

			case 'author':
				return esc_html( sprintf( __( '%s: author', 'trx_addons' ), $sc ) );

			case 'description':
				return esc_html( sprintf( __( '%s: description', 'trx_addons' ), $sc ) );

			case 'cover_url':
				return esc_html( sprintf( __( '%s: cover image URL', 'trx_addons' ), $sc ) );

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
			case 'url':
				return 'LINE';

			case 'embed':
				return 'AREA';

			case 'caption':
				return 'LINE';

			case 'author':
				return 'LINE';

			case 'description':
				return 'AREA';

			case 'cover_url':
				return 'LINK';

			default:
				return '';
		}
	}

}
