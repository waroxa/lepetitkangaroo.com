<?php
/**
 * The template to display the widgets area in the footer
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0.10
 */

// Footer sidebar
$kidscare_footer_name    = kidscare_get_theme_option( 'footer_widgets' );
$kidscare_footer_present = ! kidscare_is_off( $kidscare_footer_name ) && is_active_sidebar( $kidscare_footer_name );
if ( $kidscare_footer_present ) {
	kidscare_storage_set( 'current_sidebar', 'footer' );
	$kidscare_footer_wide = kidscare_get_theme_option( 'footer_wide' );
	ob_start();
	if ( is_active_sidebar( $kidscare_footer_name ) ) {
		dynamic_sidebar( $kidscare_footer_name );
	}
	$kidscare_out = trim( ob_get_contents() );
	ob_end_clean();
	if ( ! empty( $kidscare_out ) ) {
		$kidscare_out          = preg_replace( "/<\\/aside>[\r\n\s]*<aside/", '</aside><aside', $kidscare_out );
		$kidscare_need_columns = true;   //or check: strpos($kidscare_out, 'columns_wrap')===false;
		if ( $kidscare_need_columns ) {
			$kidscare_columns = max( 0, (int) kidscare_get_theme_option( 'footer_columns' ) );			
			if ( 0 == $kidscare_columns ) {
				$kidscare_columns = min( 4, max( 1, kidscare_tags_count( $kidscare_out, 'aside' ) ) );
			}
			if ( $kidscare_columns > 1 ) {
				$kidscare_out = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $kidscare_columns ) . ' widget', $kidscare_out );
			} else {
				$kidscare_need_columns = false;
			}
		}
		?>
		<div class="footer_widgets_wrap widget_area<?php echo ! empty( $kidscare_footer_wide ) ? ' footer_fullwidth' : ''; ?> sc_layouts_row sc_layouts_row_type_normal">
			<div class="footer_widgets_inner widget_area_inner">
				<?php
				if ( ! $kidscare_footer_wide ) {
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
				kidscare_show_layout( $kidscare_out );
				do_action( 'kidscare_action_after_sidebar' );
				if ( $kidscare_need_columns ) {
					?>
					</div><!-- /.columns_wrap -->
					<?php
				}
				if ( ! $kidscare_footer_wide ) {
					?>
					</div><!-- /.content_wrap -->
					<?php
				}
				?>
			</div><!-- /.footer_widgets_inner -->
		</div><!-- /.footer_widgets_wrap -->
		<?php
	}
}
