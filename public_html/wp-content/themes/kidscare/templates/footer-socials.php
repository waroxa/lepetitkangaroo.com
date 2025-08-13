<?php
/**
 * The template to display the socials in the footer
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0.10
 */


// Socials
if ( kidscare_is_on( kidscare_get_theme_option( 'socials_in_footer' ) ) ) {
	$kidscare_output = kidscare_get_socials_links();
	if ( '' != $kidscare_output ) {
		?>
		<div class="footer_socials_wrap socials_wrap">
			<div class="footer_socials_inner">
				<?php kidscare_show_layout( $kidscare_output ); ?>
			</div>
		</div>
		<?php
	}
}
