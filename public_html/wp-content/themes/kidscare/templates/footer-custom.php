<?php
/**
 * The template to display default site footer
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0.10
 */

$kidscare_footer_id = kidscare_get_custom_footer_id();
$kidscare_footer_meta = get_post_meta( $kidscare_footer_id, 'trx_addons_options', true );
if ( ! empty( $kidscare_footer_meta['margin'] ) ) {
	kidscare_add_inline_css( sprintf( '.page_content_wrap{padding-bottom:%s}', esc_attr( kidscare_prepare_css_value( $kidscare_footer_meta['margin'] ) ) ) );
}
?>
<footer class="footer_wrap footer_custom footer_custom_<?php echo esc_attr( $kidscare_footer_id ); ?> footer_custom_<?php echo esc_attr( sanitize_title( get_the_title( $kidscare_footer_id ) ) ); ?>
						<?php
						$kidscare_footer_scheme = kidscare_get_theme_option( 'footer_scheme' );
						if ( ! empty( $kidscare_footer_scheme ) && ! kidscare_is_inherit( $kidscare_footer_scheme  ) ) {
							echo ' scheme_' . esc_attr( $kidscare_footer_scheme );
						}
						?>
						">
	<?php
	// Custom footer's layout
	do_action( 'kidscare_action_show_layout', $kidscare_footer_id );
	?>
</footer><!-- /.footer_wrap -->
