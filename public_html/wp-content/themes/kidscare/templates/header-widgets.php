<?php
/**
 * The template to display the widgets area in the header
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */

// Header sidebar
$kidscare_header_name    = kidscare_get_theme_option( 'header_widgets' );
$kidscare_header_present = ! kidscare_is_off( $kidscare_header_name ) && is_active_sidebar( $kidscare_header_name );
if ( $kidscare_header_present ) {
	kidscare_storage_set( 'current_sidebar', 'header' );
	$kidscare_header_wide = kidscare_get_theme_option( 'header_wide' );
	ob_start();
	if ( is_active_sidebar( $kidscare_header_name ) ) {
		dynamic_sidebar( $kidscare_header_name );
	}
	$kidscare_widgets_output = ob_get_contents();
	ob_end_clean();
	if ( ! empty( $kidscare_widgets_output ) ) {
		$kidscare_widgets_output = preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $kidscare_widgets_output );
		$kidscare_need_columns   = strpos( $kidscare_widgets_output, 'columns_wrap' ) === false;
		if ( $kidscare_need_columns ) {
			$kidscare_columns = max( 0, (int) kidscare_get_theme_option( 'header_columns' ) );
			if ( 0 == $kidscare_columns ) {
				$kidscare_columns = min( 6, max( 1, kidscare_tags_count( $kidscare_widgets_output, 'aside' ) ) );
			}
			if ( $kidscare_columns > 1 ) {
				$kidscare_widgets_output = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $kidscare_columns ) . ' widget', $kidscare_widgets_output );
			} else {
				$kidscare_need_columns = false;
			}
		}
		?>
		<div class="header_widgets_wrap widget_area<?php echo ! empty( $kidscare_header_wide ) ? ' header_fullwidth' : ' header_boxed'; ?>">
			<div class="header_widgets_inner widget_area_inner">
				<?php
				if ( ! $kidscare_header_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $kidscare_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'kidscare_action_before_sidebar' );
				kidscare_show_layout( $kidscare_widgets_output );
				do_action( 'kidscare_action_after_sidebar' );
				if ( $kidscare_need_columns ) {
					?>
					</div>	<!-- /.columns_wrap -->
					<?php
				}
				if ( ! $kidscare_header_wide ) {
					?>
					</div>	<!-- /.content_wrap -->
					<?php
				}
				?>
			</div>	<!-- /.header_widgets_inner -->
		</div>	<!-- /.header_widgets_wrap -->
		<?php
	}
}
