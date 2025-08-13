<div class="front_page_section front_page_section_contacts<?php
	$kidscare_scheme = kidscare_get_theme_option( 'front_page_contacts_scheme' );
	if ( ! empty( $kidscare_scheme ) && ! kidscare_is_inherit( $kidscare_scheme ) ) {
		echo ' scheme_' . esc_attr( $kidscare_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( kidscare_get_theme_option( 'front_page_contacts_paddings' ) );
?>"
		<?php
		$kidscare_css      = '';
		$kidscare_bg_image = kidscare_get_theme_option( 'front_page_contacts_bg_image' );
		if ( ! empty( $kidscare_bg_image ) ) {
			$kidscare_css .= 'background-image: url(' . esc_url( kidscare_get_attachment_url( $kidscare_bg_image ) ) . ');';
		}
		if ( ! empty( $kidscare_css ) ) {
			echo ' style="' . esc_attr( $kidscare_css ) . '"';
		}
		?>
>
<?php
	// Add anchor
	$kidscare_anchor_icon = kidscare_get_theme_option( 'front_page_contacts_anchor_icon' );
	$kidscare_anchor_text = kidscare_get_theme_option( 'front_page_contacts_anchor_text' );
if ( ( ! empty( $kidscare_anchor_icon ) || ! empty( $kidscare_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_contacts"'
									. ( ! empty( $kidscare_anchor_icon ) ? ' icon="' . esc_attr( $kidscare_anchor_icon ) . '"' : '' )
									. ( ! empty( $kidscare_anchor_text ) ? ' title="' . esc_attr( $kidscare_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_contacts_inner
	<?php
	if ( kidscare_get_theme_option( 'front_page_contacts_fullheight' ) ) {
		echo ' kidscare-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$kidscare_css      = '';
			$kidscare_bg_mask  = kidscare_get_theme_option( 'front_page_contacts_bg_mask' );
			$kidscare_bg_color_type = kidscare_get_theme_option( 'front_page_contacts_bg_color_type' );
			if ( 'custom' == $kidscare_bg_color_type ) {
				$kidscare_bg_color = kidscare_get_theme_option( 'front_page_contacts_bg_color' );
			} elseif ( 'scheme_bg_color' == $kidscare_bg_color_type ) {
				$kidscare_bg_color = kidscare_get_scheme_color( 'bg_color', $kidscare_scheme );
			} else {
				$kidscare_bg_color = '';
			}
			if ( ! empty( $kidscare_bg_color ) && $kidscare_bg_mask > 0 ) {
				$kidscare_css .= 'background-color: ' . esc_attr(
					1 == $kidscare_bg_mask ? $kidscare_bg_color : kidscare_hex2rgba( $kidscare_bg_color, $kidscare_bg_mask )
				) . ';';
			}
			if ( ! empty( $kidscare_css ) ) {
				echo ' style="' . esc_attr( $kidscare_css ) . '"';
			}
			?>
	>
		<div class="front_page_section_content_wrap front_page_section_contacts_content_wrap content_wrap">
			<?php

			// Title and description
			$kidscare_caption     = kidscare_get_theme_option( 'front_page_contacts_caption' );
			$kidscare_description = kidscare_get_theme_option( 'front_page_contacts_description' );
			if ( ! empty( $kidscare_caption ) || ! empty( $kidscare_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				// Caption
				if ( ! empty( $kidscare_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<h2 class="front_page_section_caption front_page_section_contacts_caption front_page_block_<?php echo ! empty( $kidscare_caption ) ? 'filled' : 'empty'; ?>">
					<?php
						echo wp_kses( $kidscare_caption, 'kidscare_kses_content' );
					?>
					</h2>
					<?php
				}

				// Description
				if ( ! empty( $kidscare_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<div class="front_page_section_description front_page_section_contacts_description front_page_block_<?php echo ! empty( $kidscare_description ) ? 'filled' : 'empty'; ?>">
					<?php
						echo wp_kses( wpautop( $kidscare_description ), 'kidscare_kses_content' );
					?>
					</div>
					<?php
				}
			}

			// Content (text)
			$kidscare_content = kidscare_get_theme_option( 'front_page_contacts_content' );
			$kidscare_layout  = kidscare_get_theme_option( 'front_page_contacts_layout' );
			if ( 'columns' == $kidscare_layout && ( ! empty( $kidscare_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				<div class="front_page_section_columns front_page_section_contacts_columns columns_wrap">
					<div class="column-1_3">
				<?php
			}

			if ( ( ! empty( $kidscare_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				<div class="front_page_section_content front_page_section_contacts_content front_page_block_<?php echo ! empty( $kidscare_content ) ? 'filled' : 'empty'; ?>">
				<?php
					echo wp_kses( $kidscare_content, 'kidscare_kses_content' );
				?>
				</div>
				<?php
			}

			if ( 'columns' == $kidscare_layout && ( ! empty( $kidscare_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				</div><div class="column-2_3">
				<?php
			}

			// Shortcode output
			$kidscare_sc = kidscare_get_theme_option( 'front_page_contacts_shortcode' );
			if ( ! empty( $kidscare_sc ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_output front_page_section_contacts_output front_page_block_<?php echo ! empty( $kidscare_sc ) ? 'filled' : 'empty'; ?>">
				<?php
					kidscare_show_layout( do_shortcode( $kidscare_sc ) );
				?>
				</div>
				<?php
			}

			if ( 'columns' == $kidscare_layout && ( ! empty( $kidscare_content ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) ) {
				?>
				</div></div>
				<?php
			}
			?>

		</div>
	</div>
</div>
