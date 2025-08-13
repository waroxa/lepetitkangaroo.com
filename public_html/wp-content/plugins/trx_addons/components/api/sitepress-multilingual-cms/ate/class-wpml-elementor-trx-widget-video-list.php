<?php

/**
 * Class WPML_Elementor_Trx_Widget_Video_List
 */
class WPML_Elementor_Trx_Widget_Video_List extends WPML_Elementor_Trx_Module_With_Items  {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'videos';
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return array( 'title', 'subtitle', 'meta', 'link' => array( 'url' ), 'video_url', 'video_embed' );
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		$sc = __( 'Video list item', 'trx_addons' );
		switch( $field ) {
			case 'title':
				return esc_html( sprintf( __( '%s: title', 'trx_addons' ), $sc ) );

			case 'subtitle':
				return esc_html( sprintf( __( '%s: subtitle', 'trx_addons' ), $sc ) );

			case 'meta':
				return esc_html( sprintf( __( '%s: meta', 'trx_addons' ), $sc ) );

			case 'url':
				return esc_html( sprintf( __( '%s: link URL', 'trx_addons' ), $sc ) );

			case 'video_url':
				return esc_html( sprintf( __( '%s: video URL', 'trx_addons' ), $sc ) );

			case 'video_embed':
				return esc_html( sprintf( __( '%s: video embed', 'trx_addons' ), $sc ) );

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

			case 'subtitle':
				return 'LINE';

			case 'meta':
				return 'LINE';

			case 'url':
				return 'LINK';

			case 'video_url':
				return 'LINE';

			case 'video_embed':
				return 'AREA';

			default:
				return '';
		}
	}

}
