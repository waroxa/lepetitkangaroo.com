<div class="front_page_section front_page_section_woocommerce<?php
	$kidscare_scheme = kidscare_get_theme_option( 'front_page_woocommerce_scheme' );
	if ( ! empty( $kidscare_scheme ) && ! kidscare_is_inherit( $kidscare_scheme ) ) {
		echo ' scheme_' . esc_attr( $kidscare_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( kidscare_get_theme_option( 'front_page_woocommerce_paddings' ) );
?>"
		<?php
		$kidscare_css      = '';
		$kidscare_bg_image = kidscare_get_theme_option( 'front_page_woocommerce_bg_image' );
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
	$kidscare_anchor_icon = kidscare_get_theme_option( 'front_page_woocommerce_anchor_icon' );
	$kidscare_anchor_text = kidscare_get_theme_option( 'front_page_woocommerce_anchor_text' );
if ( ( ! empty( $kidscare_anchor_icon ) || ! empty( $kidscare_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_woocommerce"'
									. ( ! empty( $kidscare_anchor_icon ) ? ' icon="' . esc_attr( $kidscare_anchor_icon ) . '"' : '' )
									. ( ! empty( $kidscare_anchor_text ) ? ' title="' . esc_attr( $kidscare_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_woocommerce_inner
	<?php
	if ( kidscare_get_theme_option( 'front_page_woocommerce_fullheight' ) ) {
		echo ' kidscare-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$kidscare_css      = '';
			$kidscare_bg_mask  = kidscare_get_theme_option( 'front_page_woocommerce_bg_mask' );
			$kidscare_bg_color_type = kidscare_get_theme_option( 'front_page_woocommerce_bg_color_type' );
			if ( 'custom' == $kidscare_bg_color_type ) {
				$kidscare_bg_color = kidscare_get_theme_option( 'front_page_woocommerce_bg_color' );
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
		<div class="front_page_section_content_wrap front_page_section_woocommerce_content_wrap content_wrap woocommerce">
			<?php
			// Content wrap with title and description
			$kidscare_caption     = kidscare_get_theme_option( 'front_page_woocommerce_caption' );
			$kidscare_description = kidscare_get_theme_option( 'front_page_woocommerce_description' );
			if ( ! empty( $kidscare_caption ) || ! empty( $kidscare_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				// Caption
				if ( ! empty( $kidscare_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<h2 class="front_page_section_caption front_page_section_woocommerce_caption front_page_block_<?php echo ! empty( $kidscare_caption ) ? 'filled' : 'empty'; ?>">
					<?php
						echo wp_kses( $kidscare_caption, 'kidscare_kses_content' );
					?>
					</h2>
					<?php
				}

				// Description (text)
				if ( ! empty( $kidscare_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
					?>
					<div class="front_page_section_description front_page_section_woocommerce_description front_page_block_<?php echo ! empty( $kidscare_description ) ? 'filled' : 'empty'; ?>">
					<?php
						echo wp_kses( wpautop( $kidscare_description ), 'kidscare_kses_content' );
					?>
					</div>
					<?php
				}
			}

			// Content (widgets)
			?>
			<div class="front_page_section_output front_page_section_woocommerce_output list_products shop_mode_thumbs">
			<?php
				$kidscare_woocommerce_sc = kidscare_get_theme_option( 'front_page_woocommerce_products' );
			if ( 'products' == $kidscare_woocommerce_sc ) {
				$kidscare_woocommerce_sc_ids      = kidscare_get_theme_option( 'front_page_woocommerce_products_per_page' );
				$kidscare_woocommerce_sc_per_page = count( explode( ',', $kidscare_woocommerce_sc_ids ) );
			} else {
				$kidscare_woocommerce_sc_per_page = max( 1, (int) kidscare_get_theme_option( 'front_page_woocommerce_products_per_page' ) );
			}
				$kidscare_woocommerce_sc_columns = max( 1, min( $kidscare_woocommerce_sc_per_page, (int) kidscare_get_theme_option( 'front_page_woocommerce_products_columns' ) ) );
				echo do_shortcode(
					"[{$kidscare_woocommerce_sc}"
									. ( 'products' == $kidscare_woocommerce_sc
											? ' ids="' . esc_attr( $kidscare_woocommerce_sc_ids ) . '"'
											: '' )
									. ( 'product_category' == $kidscare_woocommerce_sc
											? ' category="' . esc_attr( kidscare_get_theme_option( 'front_page_woocommerce_products_categories' ) ) . '"'
											: '' )
									. ( 'best_selling_products' != $kidscare_woocommerce_sc
											? ' orderby="' . esc_attr( kidscare_get_theme_option( 'front_page_woocommerce_products_orderby' ) ) . '"'
												. ' order="' . esc_attr( kidscare_get_theme_option( 'front_page_woocommerce_products_order' ) ) . '"'
											: '' )
									. ' per_page="' . esc_attr( $kidscare_woocommerce_sc_per_page ) . '"'
									. ' columns="' . esc_attr( $kidscare_woocommerce_sc_columns ) . '"'
					. ']'
				);
				?>
			</div>
		</div>
	</div>
</div>
