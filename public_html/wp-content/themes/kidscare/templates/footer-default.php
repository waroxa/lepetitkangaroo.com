<?php
/**
 * The template to display default site footer
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0.10
 */

?>
<footer class="footer_wrap footer_default
<?php
$kidscare_footer_scheme = kidscare_get_theme_option( 'footer_scheme' );
if ( ! empty( $kidscare_footer_scheme ) && ! kidscare_is_inherit( $kidscare_footer_scheme  ) ) {
	echo ' scheme_' . esc_attr( $kidscare_footer_scheme );
}
?>
				">
	<?php

	// Footer widgets area
	get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/footer-widgets' ) );

	// Logo
	get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/footer-logo' ) );

	// Socials
	get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/footer-socials' ) );

	// Menu
	get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/footer-menu' ) );

	// Copyright area
	get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/footer-copyright' ) );

	?>
</footer><!-- /.footer_wrap -->
