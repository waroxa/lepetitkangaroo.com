<?php
/**
 * The template to display default site header
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */

$kidscare_header_css   = '';
$kidscare_header_image = get_header_image();
$kidscare_header_video = kidscare_get_header_video();
if ( ! empty( $kidscare_header_image ) && kidscare_trx_addons_featured_image_override( is_singular() || kidscare_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$kidscare_header_image = kidscare_get_current_mode_image( $kidscare_header_image );
}

?><header class="top_panel top_panel_default
	<?php
	echo ! empty( $kidscare_header_image ) || ! empty( $kidscare_header_video ) ? ' with_bg_image' : ' without_bg_image';
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

	// Main menu
	if ( kidscare_get_theme_option( 'menu_style' ) == 'top' ) {
		get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/header-navi' ) );
	}

	// Mobile header
	if ( kidscare_is_on( kidscare_get_theme_option( 'header_mobile_enabled' ) ) ) {
		get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/header-mobile' ) );
	}

    // Page title and breadcrumbs area
    get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/header-title' ) );

	if ( !is_single() || ( kidscare_get_theme_option( 'post_header_position' ) == 'default' && kidscare_get_theme_option( 'post_thumbnail_type' ) == 'default' ) ) {
		// Display featured image in the header on the single posts
		// Comment next line to prevent show featured image in the header area
		// and display it in the post's content
		get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/header-single' ) );
	}

	// Header widgets area
	get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/header-widgets' ) );
	?>
</header>
