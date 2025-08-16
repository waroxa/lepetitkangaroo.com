<?php
/**
 * The template to show mobile menu
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */
?>
<div class="menu_mobile_overlay"></div>
<div class="menu_mobile menu_mobile_<?php echo esc_attr( kidscare_get_theme_option( 'menu_mobile_fullscreen' ) > 0 ? 'fullscreen' : 'narrow' ); ?> scheme_dark">
	<div class="menu_mobile_inner">
		<a class="menu_mobile_close theme_button_close"><span class="theme_button_close_icon"></span></a>
		<?php

		// Logo
		set_query_var( 'kidscare_logo_args', array( 'type' => 'mobile' ) );
		get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/header-logo' ) );
		set_query_var( 'kidscare_logo_args', array() );

		// Mobile menu
		$kidscare_menu_mobile = kidscare_get_nav_menu( 'menu_mobile' );
		if ( empty( $kidscare_menu_mobile ) ) {
			$kidscare_menu_mobile = apply_filters( 'kidscare_filter_get_mobile_menu', '' );
			if ( empty( $kidscare_menu_mobile ) ) {
				$kidscare_menu_mobile = kidscare_get_nav_menu( 'menu_main' );
				if ( empty( $kidscare_menu_mobile ) ) {
					$kidscare_menu_mobile = kidscare_get_nav_menu();
				}
			}
		}
		if ( ! empty( $kidscare_menu_mobile ) ) {
			$kidscare_menu_mobile = str_replace(
				array( 'menu_main',   'id="menu-',        'sc_layouts_menu_nav', 'sc_layouts_menu ', 'sc_layouts_hide_on_mobile', 'hide_on_mobile' ),
				array( 'menu_mobile', 'id="menu_mobile-', '',                    ' ',                '',                          '' ),
				$kidscare_menu_mobile
			);
			if ( strpos( $kidscare_menu_mobile, '<nav ' ) === false ) {
				$kidscare_menu_mobile = sprintf( '<nav class="menu_mobile_nav_area">%s</nav>', $kidscare_menu_mobile );
			}
			kidscare_show_layout( apply_filters( 'kidscare_filter_menu_mobile_layout', $kidscare_menu_mobile ) );
		}

		// Social icons
		kidscare_show_layout( kidscare_get_socials_links(), '<div class="socials_mobile">', '</div>' );
		?>
	</div>
</div>
