<?php
/**
 * The template to display the copyright info in the footer
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0.10
 */

// Copyright area
?> 
<div class="footer_copyright_wrap
<?php
$kidscare_copyright_scheme = kidscare_get_theme_option( 'copyright_scheme' );
if ( ! empty( $kidscare_copyright_scheme ) && ! kidscare_is_inherit( $kidscare_copyright_scheme  ) ) {
	echo ' scheme_' . esc_attr( $kidscare_copyright_scheme );
}
?>
				">
	<div class="footer_copyright_inner">
		<div class="content_wrap">
			<div class="copyright_text">
			<?php
				$kidscare_copyright = kidscare_get_theme_option( 'copyright' );
			if ( ! empty( $kidscare_copyright ) ) {
				// Replace {{Y}} or {Y} with the current year
				$kidscare_copyright = str_replace( array( '{{Y}}', '{Y}' ), date_i18n( 'Y' ), $kidscare_copyright );
				// Replace {{...}} and ((...)) on the <i>...</i> and <b>...</b>
				$kidscare_copyright = kidscare_prepare_macros( $kidscare_copyright );
				// Display copyright
				echo wp_kses( nl2br( $kidscare_copyright ), 'kidscare_kses_content' );
			}
			?>
			</div>
		</div>
	</div>
</div>
