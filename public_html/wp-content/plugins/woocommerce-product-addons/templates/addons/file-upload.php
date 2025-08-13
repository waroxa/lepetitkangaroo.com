<?php
/**
 * The Template for displaying upload field.
 *
 * @version 7.9.0
 * @package woocommerce-product-addons
 */

$field_name       = ! empty( $addon['field_name'] ) ? $addon['field_name'] : '';
$addon_key        = 'addon-' . sanitize_title( $field_name );
$adjust_price     = ! empty( $addon['adjust_price'] ) ? $addon['adjust_price'] : '';
$price            = ! empty( $addon['price'] ) ? $addon['price'] : '';
$price_type       = ! empty( $addon['price_type'] ) ? $addon['price_type'] : '';
$restriction_data = WC_Product_Addons_Helper::get_restriction_data( $addon );
$value            = ! empty( $value ) ? $value : '';

// phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
$price_raw = apply_filters( 'woocommerce_product_addons_price_raw', $adjust_price && $price ? $price : '', $addon );
// phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
$price_display = apply_filters(
	'woocommerce_product_addons_price',
	$adjust_price && $price_raw ? WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) : '',
	$addon,
	0,
	'file_upload'
);

if ( 'percentage_based' === $price_type ) {
	$price_display = $price_raw;
}
?>

<div class="form-row form-row-wide wc-pao-addon-wrap wc-pao-addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>">
	<input
		type="file"
		class="wc-pao-addon-file-upload input-text wc-pao-addon-field"
		data-raw-price="<?php echo esc_attr( $price_raw ); ?>"
		data-price="<?php echo esc_attr( $price_display ); ?>"
		data-price-type="<?php echo esc_attr( $price_type ); ?>"
		name="addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>"
		id="addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>"
		data-restrictions="<?php echo esc_attr( wp_json_encode( $restriction_data ) ); ?>"
		data-value="<?php echo esc_attr( $value ); ?>"
		/> <small>
		<?php
			// translators: %s file size.
			echo wp_kses_post( sprintf( __( '(max file size %s)', 'woocommerce-product-addons' ), $max_size ) );
		?>
		</small>

	<?php
	if ( ! empty( $value ) ) {
		$filename = basename( $value );
		?>
		<div class="wc-pao-addon-file-name" data-value="<?php echo esc_attr( $filename ); ?>">
			<small>
			<?php
			$filelink = '<a href="' . esc_url( $value ) . '" target="_blank">' . esc_html( $filename ) . '</a>';
			// translators: %s existing filename.
			echo wp_kses_post( sprintf( __( 'Existing file: %s', 'woocommerce-product-addons' ), $filelink ) );
			?>
			</small>
			<div>
			</div>
			<input
				type="hidden"
				name="addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>"
				id="addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>-value"
				value="<?php echo esc_attr( $value ); ?>" />
		</div>
		<?php
	}
	?>
	<small>
		<?php
		/**
		 * `woocommerce_pao_reset_file_link` filter.
		 *
		 * @since 7.2.0
		 * @param string $reset_file_link Reset file link.
		 */
		echo wp_kses_post( apply_filters( 'woocommerce_pao_reset_file_link', '<a class="reset_file ' . ( '' === $value ? 'inactive' : 'active' ) . '" href="#">' . esc_html__( 'Clear', 'woocommerce-product-addons' ) . '</a>' ) );
		?>
	</small>
</div>
