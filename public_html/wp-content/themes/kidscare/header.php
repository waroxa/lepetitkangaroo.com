<?php
/**
 * The Header: Logo and main menu
 *
 * @package WordPress
 * @subpackage KIDSCARE
 * @since KIDSCARE 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js
									<?php
										// Class scheme_xxx need in the <html> as context for the <body>!
										echo ' scheme_' . esc_attr( kidscare_get_theme_option( 'color_scheme' ) );
									?>
										">
<head>
	<?php wp_head(); ?>
</head>

<body <?php	body_class(); ?>>
    <?php wp_body_open(); ?>

	<?php do_action( 'kidscare_action_before_body' ); ?>

	<div class="body_wrap">

		<div class="page_wrap">
			<?php
			// Desktop header
			$kidscare_header_type = kidscare_get_theme_option( 'header_type' );
			if ( 'custom' == $kidscare_header_type && ! kidscare_is_layouts_available() ) {
				$kidscare_header_type = 'default';
			}
			get_template_part( apply_filters( 'kidscare_filter_get_template_part', "templates/header-{$kidscare_header_type}" ) );

			// Side menu
			if ( in_array( kidscare_get_theme_option( 'menu_style' ), array( 'left', 'right' ) ) ) {
				get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/header-navi-side' ) );
			}

			// Mobile menu
			get_template_part( apply_filters( 'kidscare_filter_get_template_part', 'templates/header-navi-mobile' ) );
			
			// Single posts banner after header
			kidscare_show_post_banner( 'header' );
			?>

			<div class="page_content_wrap">
				<?php
				// Single posts banner on the background
				if ( is_singular( 'post' ) || is_singular( 'attachment' ) ) {

					kidscare_show_post_banner( 'background' );

					$kidscare_post_thumbnail_type  = kidscare_get_theme_option( 'post_thumbnail_type' );
					$kidscare_post_header_position = kidscare_get_theme_option( 'post_header_position' );
					$kidscare_post_header_align    = kidscare_get_theme_option( 'post_header_align' );

					// Boxed post thumbnail
					if ( in_array( $kidscare_post_thumbnail_type, array( 'boxed', 'fullwidth') ) ) {
						ob_start();
						?>
						<div class="header_content_wrap header_align_<?php echo esc_attr( $kidscare_post_header_align ); ?>">
							<?php
							if ( 'boxed' === $kidscare_post_thumbnail_type ) {
								?>
								<div class="content_wrap">
								<?php
							}

							// Post title and meta
							if ( 'above' === $kidscare_post_header_position ) {
								kidscare_show_post_title_and_meta();
							}

							// Featured image
							kidscare_show_post_featured_image();

							// Post title and meta
							if ( in_array( $kidscare_post_header_position, array( 'under', 'on_thumb' ) ) ) {
								kidscare_show_post_title_and_meta();
							}

							if ( 'boxed' === $kidscare_post_thumbnail_type ) {
								?>
								</div>
								<?php
							}
							?>
						</div>
						<?php
						$kidscare_post_header = ob_get_contents();
						ob_end_clean();
						if ( strpos( $kidscare_post_header, 'post_featured' ) !== false || strpos( $kidscare_post_header, 'post_title' ) !== false ) {
							kidscare_show_layout( $kidscare_post_header );
						}
					}
				}

				// Widgets area above page content
				$kidscare_body_style   = kidscare_get_theme_option( 'body_style' );
				$kidscare_widgets_name = kidscare_get_theme_option( 'widgets_above_page' );
				$kidscare_show_widgets = ! kidscare_is_off( $kidscare_widgets_name ) && is_active_sidebar( $kidscare_widgets_name );
				if ( $kidscare_show_widgets ) {
					if ( 'fullscreen' != $kidscare_body_style ) {
						?>
						<div class="content_wrap">
							<?php
					}
					kidscare_create_widgets_area( 'widgets_above_page' );
					if ( 'fullscreen' != $kidscare_body_style ) {
						?>
						</div><!-- </.content_wrap> -->
						<?php
					}
				}

				// Content area
				?>
				<div class="content_wrap<?php echo 'fullscreen' == $kidscare_body_style ? '_fullscreen' : ''; ?>">

					<div class="content">
						<?php
						// Widgets area inside page content
						kidscare_create_widgets_area( 'widgets_above_content' );
