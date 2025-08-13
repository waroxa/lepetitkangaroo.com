<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */

							// Widgets area inside page content
							kidscare_create_widgets_area( 'widgets_below_content' );
							?>
						</div><!-- </.content> -->
					<?php

					// Show main sidebar
					get_sidebar();

					$kidscare_body_style = kidscare_get_theme_option( 'body_style' );
					?>
					</div><!-- </.content_wrap> -->
					<?php

					// Widgets area below page content and related posts below page content
					$kidscare_widgets_name = kidscare_get_theme_option( 'widgets_below_page' );
					$kidscare_show_widgets = ! kidscare_is_off( $kidscare_widgets_name ) && is_active_sidebar( $kidscare_widgets_name );
					$kidscare_show_related = is_single() && kidscare_get_theme_option( 'related_position' ) == 'below_page';
					if ( $kidscare_show_widgets || $kidscare_show_related ) {
						if ( 'fullscreen' != $kidscare_body_style ) {
							?>
							<div class="content_wrap">
							<?php
						}
						// Show related posts before footer
						if ( $kidscare_show_related ) {
							do_action( 'kidscare_action_related_posts' );
						}

						// Widgets area below page content
						if ( $kidscare_show_widgets ) {
							kidscare_create_widgets_area( 'widgets_below_page' );
						}
						if ( 'fullscreen' != $kidscare_body_style ) {
							?>
							</div><!-- </.content_wrap> -->
							<?php
						}
					}
					?>
			</div><!-- </.page_content_wrap> -->

			<?php
			// Single posts banner before footer
			if ( is_singular( 'post' ) ) {
				kidscare_show_post_banner('footer');
			}
			// Footer
			$kidscare_footer_type = kidscare_get_theme_option( 'footer_type' );
			if ( 'custom' == $kidscare_footer_type && ! kidscare_is_layouts_available() ) {
				$kidscare_footer_type = 'default';
			}
			get_template_part( apply_filters( 'kidscare_filter_get_template_part', "templates/footer-{$kidscare_footer_type}" ) );
			?>

		</div><!-- /.page_wrap -->

	</div><!-- /.body_wrap -->

	<?php wp_footer(); ?>

</body>
</html>