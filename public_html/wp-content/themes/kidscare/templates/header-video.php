<?php
/**
 * The template to display the background video in the header
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0.14
 */
$kidscare_header_video = kidscare_get_header_video();
$kidscare_embed_video  = '';
if ( ! empty( $kidscare_header_video ) && ! kidscare_is_from_uploads( $kidscare_header_video ) ) {
	if ( kidscare_is_youtube_url( $kidscare_header_video ) && preg_match( '/[=\/]([^=\/]*)$/', $kidscare_header_video, $matches ) && ! empty( $matches[1] ) ) {
		?><div id="background_video" data-youtube-code="<?php echo esc_attr( $matches[1] ); ?>"></div>
		<?php
	} else {
		?>
		<div id="background_video"><?php kidscare_show_layout( kidscare_get_embed_video( $kidscare_header_video ) ); ?></div>
		<?php
	}
}
