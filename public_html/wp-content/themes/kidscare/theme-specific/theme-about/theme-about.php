<?php
/**
 * Information about this theme
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0.30
 */


// Redirect to the 'About Theme' page after switch theme
if ( ! function_exists( 'kidscare_about_after_switch_theme' ) ) {
	add_action( 'after_switch_theme', 'kidscare_about_after_switch_theme', 1000 );
	function kidscare_about_after_switch_theme() {
		update_option( 'kidscare_about_page', 1 );
	}
}
if ( ! function_exists( 'kidscare_about_after_setup_theme' ) ) {
	add_action( 'init', 'kidscare_about_after_setup_theme', 1000 );
	function kidscare_about_after_setup_theme() {
		if ( ! defined( 'WP_CLI' ) && get_option( 'kidscare_about_page' ) == 1 ) {
			update_option( 'kidscare_about_page', 0 );
			wp_safe_redirect( admin_url() . 'themes.php?page=kidscare_about' );
			exit();
		} else {
			if ( kidscare_get_value_gp( 'page' ) == 'kidscare_about' && kidscare_exists_trx_addons() ) {
				wp_safe_redirect( admin_url() . 'admin.php?page=trx_addons_theme_panel' );
				exit();
			}
		}
	}
}


// Add 'About Theme' item in the Appearance menu
if ( ! function_exists( 'kidscare_about_add_menu_items' ) ) {
	add_action( 'admin_menu', 'kidscare_about_add_menu_items' );
	function kidscare_about_add_menu_items() {
		if ( ! kidscare_exists_trx_addons() ) {
			$theme      = wp_get_theme();
			$theme_name = $theme->name . ( KIDSCARE_THEME_FREE ? ' ' . esc_html__( 'Free', 'kidscare' ) : '' );
			add_theme_page(
				// Translators: Add theme name to the page title
				sprintf( esc_html__( 'About %s', 'kidscare' ), $theme_name ),    //page_title
				// Translators: Add theme name to the menu title
				sprintf( esc_html__( 'About %s', 'kidscare' ), $theme_name ),    //menu_title
				'manage_options',                                               //capability
				'kidscare_about',                                                //menu_slug
				'kidscare_about_page_builder'                                   //callback
			);
		}
	}
}


// Load page-specific scripts and styles
if ( ! function_exists( 'kidscare_about_enqueue_scripts' ) ) {
	add_action( 'admin_enqueue_scripts', 'kidscare_about_enqueue_scripts' );
	function kidscare_about_enqueue_scripts() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
		if ( ! empty( $screen->id ) && false !== strpos( $screen->id, '_page_kidscare_about' ) ) {
			// Scripts
			if ( ! kidscare_exists_trx_addons() && function_exists( 'kidscare_plugins_installer_enqueue_scripts' ) ) {
				kidscare_plugins_installer_enqueue_scripts();
			}
			// Styles
			$fdir = kidscare_get_file_url( 'theme-specific/theme-about/theme-about.css' );
			if ( '' != $fdir ) {
				wp_enqueue_style( 'kidscare-about', $fdir, array(), null );
			}
		}
	}
}


// Build 'About Theme' page
if ( ! function_exists( 'kidscare_about_page_builder' ) ) {
	function kidscare_about_page_builder() {
		$theme = wp_get_theme();
		?>
		<div class="kidscare_about">

			<?php do_action( 'kidscare_action_theme_about_start', $theme ); ?>

			<?php do_action( 'kidscare_action_theme_about_before_logo', $theme ); ?>

			<div class="kidscare_about_logo">
				<?php
				$logo = kidscare_get_file_url( 'theme-specific/theme-about/icon.jpg' );
				if ( empty( $logo ) ) {
					$logo = kidscare_get_file_url( 'screenshot.jpg' );
				}
				if ( ! empty( $logo ) ) {
					?>
					<img src="<?php echo esc_url( $logo ); ?>">
					<?php
				}
				?>
			</div>

			<?php do_action( 'kidscare_action_theme_about_before_title', $theme ); ?>

			<h1 class="kidscare_about_title">
			<?php
				echo esc_html(
					sprintf(
						// Translators: Add theme name and version to the 'Welcome' message
						__( 'Welcome to %1$s %2$s v.%3$s', 'kidscare' ),
						$theme->name,
						KIDSCARE_THEME_FREE ? __( 'Free', 'kidscare' ) : '',
						$theme->version
					)
				);
			?>
			</h1>

			<?php do_action( 'kidscare_action_theme_about_before_description', $theme ); ?>

			<div class="kidscare_about_description">
				<p>
					<?php
					echo wp_kses_data( __( 'In order to continue, please install and activate <b>ThemeREX Addons plugin</b>.', 'kidscare' ) );
					?>
					<sup>*</sup>
				</p>
			</div>

			<?php do_action( 'kidscare_action_theme_about_before_buttons', $theme ); ?>

			<div class="kidscare_about_buttons">
				<?php kidscare_plugins_installer_get_button_html( 'trx_addons' ); ?>
			</div>

			<?php do_action( 'kidscare_action_theme_about_before_buttons', $theme ); ?>

			<div class="kidscare_about_notes">
				<p>
					<sup>*</sup>
					<?php
					echo wp_kses_data( __( "<i>ThemeREX Addons plugin</i> will allow you to install recommended plugins, demo content, and improve the theme's functionality overall with multiple theme options.", 'kidscare' ) );
					?>
				</p>
			</div>

			<?php do_action( 'kidscare_action_theme_about_end', $theme ); ?>

		</div>
		<?php
	}
}


// Hide TGMPA notice on the page 'About Theme'
if ( ! function_exists( 'kidscare_about_page_disable_tgmpa_notice' ) ) {
	add_filter( 'tgmpa_show_admin_notice_capability', 'kidscare_about_page_disable_tgmpa_notice' );
	function kidscare_about_page_disable_tgmpa_notice($cap) {
		if ( kidscare_get_value_gp( 'page' ) == 'kidscare_about' ) {
			$cap = 'unfiltered_upload';
		}
		return $cap;
	}
}

require_once KIDSCARE_THEME_DIR . 'includes/plugins-installer/plugins-installer.php';
