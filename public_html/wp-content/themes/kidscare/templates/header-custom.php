<?php
/**
 * The template to display custom header from the ThemeREX Addons Layouts
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0.06
 */

$kidscare_header_css   = '';
$kidscare_header_image = get_header_image();
$kidscare_header_video = kidscare_get_header_video();
if ( ! empty( $kidscare_header_image ) && kidscare_trx_addons_featured_image_override( is_singular() || kidscare_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$kidscare_header_image = kidscare_get_current_mode_image( $kidscare_header_image );
}

$kidscare_header_id = kidscare_get_custom_header_id();
$kidscare_header_meta = get_post_meta( $kidscare_header_id, 'trx_addons_options', true );
if ( ! empty( $kidscare_header_meta['margin'] ) ) {
	kidscare_add_inline_css( sprintf( '.page_content_wrap{padding-top:%s}', esc_attr( kidscare_prepare_css_value( $kidscare_header_meta['margin'] ) ) ) );
}

?><header class="top_panel top_panel_custom top_panel_custom_<?php echo esc_attr( $kidscare_header_id ); ?> top_panel_custom_<?php echo esc_attr( sanitize_title( get_the_title( $kidscare_header_id ) ) ); ?>
				<?php
				echo ! empty( $kidscare_header_image ) || ! empty( $kidscare_header_video )
					? ' with_bg_image'
					: ' without_bg_image';
				if ( '' != $kidscare_header_video ) {
					echo ' with_bg_video';
				}
				if ( '' != $kidscare_header_image ) {
					echo ' ' . esc_attr( kidscare_add_inline_css_class( 'background-image: url(' . esc_url( $kidscare_header_image ) . ');' ) );
				}
				if ( is_single() && has_post_thumbnail() ) {
					echo ' with_featured_image';
				}
				if ( kidscare_is_on( kidscare_get_theme_option( 'header_fullheight' ) ) ) {
					echo ' header_fullheight kidscare-full-height';
				}
				$kidscare_header_scheme = kidscare_get_theme_option( 'header_scheme' );
				if ( ! empty( $kidscare_header_scheme ) && ! kidscare_is_inherit( $kidscare_header_scheme  ) ) {
					echo ' scheme_' . esc_attr( $kidscare_header_scheme );
				}
				?>
">
	<?php

	// Background video
	if ( ! empty( $kidscare_header_video ) ) {
		get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/header-video' ) );
	}

	// Custom header's layout
	do_action( 'kidscare_action_show_layout', $kidscare_header_id );

	// Header widgets area
	get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/header-widgets' ) );

	?>
</header>
