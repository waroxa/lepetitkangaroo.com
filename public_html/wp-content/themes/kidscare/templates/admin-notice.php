<?php
/**
 * The template to display Admin notices
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0.1
 */

$kidscare_theme_obj = wp_get_theme();
?>
<div class="kidscare_admin_notice kidscare_welcome_notice update-nag">
	<?php
	// Theme image
	$kidscare_theme_img = kidscare_get_file_url( 'screenshot.jpg' );
	if ( '' != $kidscare_theme_img ) {
		?>
		<div class="kidscare_notice_image"><img src="<?php echo esc_url( $kidscare_theme_img ); ?>" alt="<?php esc_attr_e( 'Theme screenshot', 'kidscare' ); ?>"></div>
		<?php
	}

	// Title
	?>
	<h3 class="kidscare_notice_title">
		<?php
		echo esc_html(
			sprintf(
				// Translators: Add theme name and version to the 'Welcome' message
				__( 'Welcome to %1$s v.%2$s', 'kidscare' ),
				$kidscare_theme_obj->name . ( KIDSCARE_THEME_FREE ? ' ' . __( 'Free', 'kidscare' ) : '' ),
				$kidscare_theme_obj->version
			)
		);
		?>
	</h3>
	<?php

	// Description
	?>
	<div class="kidscare_notice_text">
		<p class="kidscare_notice_text_description">
			<?php
			echo str_replace( '. ', '.<br>', wp_kses_data( $kidscare_theme_obj->description ) );
			?>
		</p>
		<p class="kidscare_notice_text_info">
			<?php
			echo wp_kses_data( __( 'Attention! Plugin "ThemeREX Addons" is required! Please, install and activate it!', 'kidscare' ) );
			?>
		</p>
	</div>
	<?php

	// Buttons
	?>
	<div class="kidscare_notice_buttons">
		<?php
		// Link to the page 'About Theme'
		?>
		<a href="<?php echo esc_url( admin_url() . 'themes.php?page=kidscare_about' ); ?>" class="button button-primary"><i class="dashicons dashicons-nametag"></i> 
			<?php
			echo esc_html__( 'Install plugin "ThemeREX Addons"', 'kidscare' );
			?>
		</a>
		<?php		
		// Dismiss this notice
		?>
		<a href="#" class="kidscare_hide_notice"><i class="dashicons dashicons-dismiss"></i> <span class="kidscare_hide_notice_text"><?php esc_html_e( 'Dismiss', 'kidscare' ); ?></span></a>
	</div>
</div>
