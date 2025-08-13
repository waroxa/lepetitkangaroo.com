<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */

if ( kidscare_sidebar_present() ) {
	ob_start();
	$kidscare_sidebar_name = kidscare_get_theme_option( 'sidebar_widgets' );
	kidscare_storage_set( 'current_sidebar', 'sidebar' );
	if ( is_active_sidebar( $kidscare_sidebar_name ) ) {
		dynamic_sidebar( $kidscare_sidebar_name );
	}
	$kidscare_out = trim( ob_get_contents() );
	ob_end_clean();
	if ( ! empty( $kidscare_out ) ) {
		$kidscare_sidebar_position    = kidscare_get_theme_option( 'sidebar_position' );
		$kidscare_sidebar_position_ss = kidscare_get_theme_option( 'sidebar_position_ss' );
		?>
		<div class="sidebar widget_area
			<?php
			echo ' ' . esc_attr( $kidscare_sidebar_position );
			echo ' sidebar_' . esc_attr( $kidscare_sidebar_position_ss );

			if ( 'float' == $kidscare_sidebar_position_ss ) {
				echo ' sidebar_float';
			}
			$kidscare_sidebar_scheme = kidscare_get_theme_option( 'sidebar_scheme' );
			if ( ! empty( $kidscare_sidebar_scheme ) && ! kidscare_is_inherit( $kidscare_sidebar_scheme ) ) {
				echo ' scheme_' . esc_attr( $kidscare_sidebar_scheme );
			}
			?>
		" role="complementary">
			<?php
			// Single posts banner before sidebar
			kidscare_show_post_banner( 'sidebar' );
			// Button to show/hide sidebar on mobile
			if ( in_array( $kidscare_sidebar_position_ss, array( 'above', 'float' ) ) ) {
				$kidscare_title = apply_filters( 'kidscare_filter_sidebar_control_title', 'float' == $kidscare_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'kidscare' ) : '' );
				$kidscare_text  = apply_filters( 'kidscare_filter_sidebar_control_text', 'above' == $kidscare_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'kidscare' ) : '' );
				?>
				<a href="#" class="sidebar_control" title="<?php echo esc_attr( $kidscare_title ); ?>"><?php echo esc_html( $kidscare_text ); ?></a>
				<?php
			}
			?>
			<div class="sidebar_inner">
				<?php
				do_action( 'kidscare_action_before_sidebar' );
				kidscare_show_layout( preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $kidscare_out ) );
				do_action( 'kidscare_action_after_sidebar' );
				?>
			</div><!-- /.sidebar_inner -->
		</div><!-- /.sidebar -->
		<div class="clearfix"></div>
		<?php
	}
}
