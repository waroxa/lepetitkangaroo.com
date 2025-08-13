<?php
/**
 * The template to display the logo or the site name and the slogan in the Header
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */

$kidscare_args = get_query_var( 'kidscare_logo_args' );

// Site logo
$kidscare_logo_type   = isset( $kidscare_args['type'] ) ? $kidscare_args['type'] : '';
$kidscare_logo_image  = kidscare_get_logo_image( $kidscare_logo_type );
$kidscare_logo_text   = kidscare_is_on( kidscare_get_theme_option( 'logo_text' ) ) ? get_bloginfo( 'name' ) : '';
$kidscare_logo_slogan = get_bloginfo( 'description', 'display' );
if ( ! empty( $kidscare_logo_image['logo'] ) || ! empty( $kidscare_logo_text ) ) {
	?><a class="sc_layouts_logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php
		if ( ! empty( $kidscare_logo_image['logo'] ) ) {
			if ( empty( $kidscare_logo_type ) && function_exists( 'the_custom_logo' ) && is_numeric( $kidscare_logo_image['logo'] ) && (int)$kidscare_logo_image['logo'] > 0 ) {
				the_custom_logo();
			} else {
				$kidscare_attr = kidscare_getimagesize( $kidscare_logo_image['logo'] );
				echo '<img src="' . esc_url( $kidscare_logo_image['logo'] ) . '"'
						. ( ! empty( $kidscare_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $kidscare_logo_image['logo_retina'] ) . ' 2x"' : '' )
						. ' alt="' . esc_attr( $kidscare_logo_text ) . '"'
						. ( ! empty( $kidscare_attr[3] ) ? ' ' . wp_kses_data( $kidscare_attr[3] ) : '' )
						. '>';
			}
		} else {
			kidscare_show_layout( kidscare_prepare_macros( $kidscare_logo_text ), '<span class="logo_text">', '</span>' );
			kidscare_show_layout( kidscare_prepare_macros( $kidscare_logo_slogan ), '<span class="logo_slogan">', '</span>' );
		}
		?>
	</a>
	<?php
}
