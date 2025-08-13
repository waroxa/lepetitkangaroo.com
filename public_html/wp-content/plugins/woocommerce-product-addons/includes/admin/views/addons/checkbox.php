<?php
/**
 * The Template for displaying checkbox field.
 *
 * @version 7.9.0
 * @package woocommerce-product-addons
 *
 * phpcs:disable WordPress.Security.NonceVerification.Missing
 */

$addon_required = WC_Product_Addons_Helper::is_addon_required( $addon );
$value          = ! empty( $value ) ? (array) $value : array();
?>

<div class="form-row form-row-wide wc-pao-addon-wrap">

	<?php
	foreach ( $addon['options'] as $i => $option ) {
		$option_price      = ! empty( $option['price'] ) ? $option['price'] : '';
		$option_price_type = ! empty( $option['price_type'] ) ? $option['price_type'] : '';
		$price_prefix      = 0 < $option_price ? '+' : '';
		$price_type        = $option_price_type;
		$price_raw         = apply_filters( 'woocommerce_product_addons_option_price_raw', $option_price, $option );
		$field_name        = ! empty( $addon['field_name'] ) ? $addon['field_name'] : '';
		$option_label      = ( '0' === $option['label'] ) || ! empty( $option['label'] ) ? $option['label'] : '';
		$price_display     = WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw );
		$restriction_data  = WC_Product_Addons_Helper::get_restriction_data( $addon );
		$is_checked        = in_array( $option_label, $value, true );

		if ( 'percentage_based' === $price_type ) {
			$price_display     = $price_raw;
			$price_for_display = $price_raw ? '(' . $price_prefix . $price_raw . '%)' : '';
		} else {
			$price_for_display = $price_raw ? '(' . $price_prefix . wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) ) . ')' : '';
		}

		$selected = isset( $_POST[ 'addon-' . sanitize_title( $field_name ) ] ) ? wc_clean( wp_unslash( $_POST[ 'addon-' . sanitize_title( $field_name ) ] ) ) : array();
		if ( ! is_array( $selected ) ) {
			$selected = array( $selected );
		}
		$current_value = ( in_array( sanitize_title( $option_label ), $selected, true ) ) ? 1 : 0;

		$option_id = sanitize_title( $field_name ) . '-' . $i;
		?>

		<div class="wc-pao-addon-item wc-pao-addon-<?php echo esc_attr( sanitize_title( $field_name ) . '-' . $i ); ?>">
			<input
				type="checkbox"
				id="<?php echo esc_attr( $option_id ); ?>"
				data-restrictions="<?php echo esc_attr( wp_json_encode( $restriction_data ) ); ?>"
				class="wc-pao-addon-field wc-pao-addon-checkbox"
				name="addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>[]"
				data-raw-price="<?php echo esc_attr( $price_raw ); ?>"
				data-price="<?php echo esc_attr( $price_display ); ?>"
				data-price-type="<?php echo esc_attr( $price_type ); ?>"
				value="<?php echo esc_attr( sanitize_title( $option_label ) ); ?>"
				data-label="<?php echo esc_attr( wptexturize( $option_label ) ); ?>"
				<?php echo $is_checked ? 'checked' : ''; ?>
			/>
			<label for="<?php echo esc_attr( $option_id ); ?>">
				<?php echo wp_kses_post( wptexturize( $option_label ) ); ?><?php echo ! empty( $price_for_display ) ? '<span class="wc-pao-addon-price">' . wp_kses_post( wptexturize( $price_for_display ) ) . '</span>' : ''; ?>
			</label>
		</div>
	<?php } ?>

</div>
