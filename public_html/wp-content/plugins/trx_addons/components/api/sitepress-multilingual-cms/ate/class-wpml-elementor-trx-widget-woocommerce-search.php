<?php

/**
 * Class WPML_Elementor_Trx_Widget_Woocommerce_Search
 */
class WPML_Elementor_Trx_Widget_Woocommerce_Search extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'fields';
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
		$sc = __( 'WooCommerce search field', 'trx_addons' );
		switch( $field ) {
			case 'text':
				return esc_html( sprintf( __( '%s: field text', 'trx_addons' ), $sc ) );

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
