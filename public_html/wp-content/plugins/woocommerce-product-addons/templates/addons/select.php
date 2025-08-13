<?php
/**
 * The Template for displaying select field.
 *
 * @version 7.9.0
 * @package woocommerce-product-addons
 */

/**
 * Injections:
 *
 * @var array $addon
 * @var mixed $value
 */

global $product;

$loop             = 0;
$field_name       = ! empty( $addon['field_name'] ) ? $addon['field_name'] : '';
$restriction_data = WC_Product_Addons_Helper::get_restriction_data( $addon );
$required         = ! empty( $addon['required'] ) ? $addon['required'] : '';
$value            = ! empty( $value ) ? $value : '';

?>
<div class="form-row form-row-wide wc-pao-addon-wrap wc-pao-addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>">
	<select
		class="wc-pao-addon-field wc-pao-addon-select"
		name="addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>"
		id="addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>"
		data-restrictions="<?php echo esc_attr( wp_json_encode( $restriction_data ) ); ?>"
		>

		<?php if ( empty( $required ) ) { ?>
			<option value=""><?php esc_html_e( 'None', 'woocommerce-product-addons' ); ?></option>
		<?php } else { ?>
			<option value=""><?php esc_html_e( 'Select an option...', 'woocommerce-product-addons' ); ?></option>
		<?php } ?>

		<?php
		foreach ( $addon['options'] as $i => $option ) {
			++$loop;

			if ( isset( $option['visibility'] ) && 0 === $option['visibility'] ) {
				continue;
			}

			$price        = ! empty( $option['price'] ) ? $option['price'] : '';
			$price_prefix = 0 < $price ? '+' : '';
			$price_type   = ! empty( $option['price_type'] ) ? $option['price_type'] : '';
			$price_raw    = apply_filters( 'woocommerce_product_addons_option_price_raw', $price, $option );
			$label        = ( '0' === $option['label'] ) || ! empty( $option['label'] ) ? $option['label'] : '';

			if ( 'percentage_based' === $price_type ) {
				$add_price_to_value = apply_filters( 'woocommerce_addons_add_product_price_to_value', true, $product );

				$price_for_display = $add_price_to_value ? apply_filters(
					'woocommerce_product_addons_option_price',
					$price_raw ? '(' . $price_prefix . $price_raw . '%)' : '',
					$option,
					$i,
					'select'
				) : '';
			} else {
				$add_price_to_value = apply_filters( 'woocommerce_addons_add_product_price_to_value', true, $product );

				$price_for_display = $add_price_to_value ? apply_filters(
					'woocommerce_product_addons_option_price',
					$price_raw ? '(' . $price_prefix . wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) ) . ')' : '',
					$option,
					$i,
					'select'
				) : '';
			}

			$price_display = WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw );

			if ( 'percentage_based' === $price_type ) {
				$price_display = $price_raw;
			}

			$option_value = sanitize_title( $label ) . '-' . $loop;
			?>
			<option
				<?php selected( $value, $option_value ); ?>
				data-raw-price="<?php echo esc_attr( $price_raw ); ?>"
				data-price="<?php echo esc_attr( $price_display ); ?>"
				data-price-type="<?php echo esc_attr( $price_type ); ?>"
				value="<?php echo esc_attr( $option_value ); ?>"
				data-label="<?php echo esc_attr( wptexturize( $label ) ); ?>"
			>
				<?php echo wp_kses_post( wptexturize( $label ) . ' ' . $price_for_display ); ?>
			</option>
		<?php } ?>
	</select>
</div>
