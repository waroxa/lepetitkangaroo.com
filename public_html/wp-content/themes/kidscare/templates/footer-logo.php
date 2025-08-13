<?php
/**
 * The template to display the site logo in the footer
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0.10
 */

// Logo
if ( kidscare_is_on( kidscare_get_theme_option( 'logo_in_footer' ) ) ) {
	$kidscare_logo_image = kidscare_get_logo_image( 'footer' );
	$kidscare_logo_text  = get_bloginfo( 'name' );
	if ( ! empty( $kidscare_logo_image['logo'] ) || ! empty( $kidscare_logo_text ) ) {
		?>
		<div class="footer_logo_wrap">
			<div class="footer_logo_inner">
				<?php
				if ( ! empty( $kidscare_logo_image['logo'] ) ) {
					$kidscare_attr = kidscare_getimagesize( $kidscare_logo_image['logo'] );
					echo '<a href="' . esc_url( home_url( '/' ) ) . '">'
							. '<img src="' . esc_url( $kidscare_logo_image['logo'] ) . '"'
								. ( ! empty( $kidscare_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $kidscare_logo_image['logo_retina'] ) . ' 2x"' : '' )
								. ' class="logo_footer_image"'
								. ' alt="' . esc_attr__( 'Site logo', 'kidscare' ) . '"'
								. ( ! empty( $kidscare_attr[3] ) ? ' ' . wp_kses_data( $kidscare_attr[3] ) : '' )
							. '>'
						. '</a>';
				} elseif ( ! empty( $kidscare_logo_text ) ) {
					echo '<h1 class="logo_footer_text">'
							. '<a href="' . esc_url( home_url( '/' ) ) . '">'
								. esc_html( $kidscare_logo_text )
							. '</a>'
						. '</h1>';
				}
				?>
			</div>
		</div>
		<?php
	}
}
