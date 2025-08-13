<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'kidscare_wpml_get_css' ) ) {
	add_filter( 'kidscare_filter_get_css', 'kidscare_wpml_get_css', 10, 2 );
	function kidscare_wpml_get_css( $css, $args ) {
		return $css;
	}
}

