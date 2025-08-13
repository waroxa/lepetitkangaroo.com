<?php
/**
 * The Template for displaying start of field.
 *
 * @version 7.3.0
 * @package woocommerce-product-addons
 */

$price_display          = '';
$title_format           = ! empty( $addon['title_format'] ) ? $addon['title_format'] : '';
$addon_type             = ! empty( $addon['type'] ) ? $addon['type'] : '';
$addon_display          = ! empty( $addon['display'] ) ? $addon['display'] : '';
$addon_price            = ! empty( $addon['price'] ) ? $addon['price'] : '';
$addon_price_type       = ! empty( $addon['price_type'] ) ? $addon['price_type'] : '';
$adjust_price           = ! empty( $addon['adjust_price'] ) ? $addon['adjust_price'] : '';
$required               = ! empty( $addon['required'] ) ? $addon['required'] : '';
$has_per_person_pricing = ( isset( $addon['wc_booking_person_qty_multiplier'] ) && 1 === $addon['wc_booking_person_qty_multiplier'] ) ? true : false;
$has_per_block_pricing  = ( ( isset( $addon['wc_booking_block_qty_multiplier'] ) && 1 === $addon['wc_booking_block_qty_multiplier'] ) || ( isset( $addon['wc_accommodation_booking_block_qty_multiplier'] ) && 1 === $addon['wc_accommodation_booking_block_qty_multiplier'] ) ) ? true : false;
$product_title          = $product->get_name();
$is_taxable             = $product->is_taxable();

if ( 'checkbox' !== $addon_type && 'multiple_choice' !== $addon_type && 'custom_price' !== $addon_type ) {
	$price_prefix = 0 < $addon_price ? '+' : '';
	$price_type   = $addon_price_type;
	$price_raw    = apply_filters( 'woocommerce_product_addons_price_raw', $addon_price, $addon );

	if ( 'percentage_based' === $price_type ) {
		$price_display = $adjust_price && $price_raw ? '(' . $price_prefix . $price_raw . '%)' : '';
	} else {
		$price_display = $adjust_price && $price_raw ? '(' . $price_prefix . wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) ) . ')' : '';
	}
}
?>

<div class="wc-pao-addon-container <?php echo $required ? 'wc-pao-required-addon' : ''; ?> wc-pao-addon wc-pao-addon-<?php echo esc_attr( sanitize_title( $addon['field_name'] ) ); ?> <?php echo isset( $addon['id'] ) ? 'wc-pao-addon-id-' . esc_attr( sanitize_title( $addon['id'] ) ) : ''; ?>" data-product-name="<?php echo esc_attr( $product_title ); ?>" data-product-tax-status="<?php echo $is_taxable ? 'taxable' : 'none'; ?>">

	<?php do_action( 'wc_product_addon_start', $addon ); ?>

	<?php
	if ( $name ) {
		if ( 'heading' === $addon_type ) {
			?>
			<h2 class="wc-pao-addon-heading"><?php echo wp_kses_post( wptexturize( $name ) ); ?></h2>
			<?php
		} else {
			$for_html = '';
			if (
				( 'radiobutton' !== $addon_display || 'multiple_choice' !== $addon_type )
				&& ( 'select' !== $addon_display || 'checkbox' !== $addon_type )
			) {
				$for_html = 'for="addon-' . esc_attr( wptexturize( $addon['field_name'] ) ) . '"';
			}

			?>
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<label <?php echo $for_html; ?> class="wc-pao-addon-name" data-addon-name="<?php echo esc_attr( wptexturize( $name ) ); ?>" data-has-per-person-pricing="<?php echo esc_attr( $has_per_person_pricing ); ?>" data-has-per-block-pricing="<?php echo esc_attr( $has_per_block_pricing ); ?>"><?php echo wp_kses_post( wptexturize( $name ) ); ?> <?php echo ! empty( $price_display ) ? '<span class="wc-pao-addon-price">' . wp_kses_post( $price_display ) . '</span>' : ''; ?> <?php echo $required ? '<em class="required" title="' . esc_attr__( 'Required field', 'woocommerce-product-addons' ) . '">*</em>' : ''; ?></label>
			<?php
		}
	}
	?>
	<?php
	if ( $display_description ) {
		?>
		<?php echo '<div class="wc-pao-addon-description">' . wp_kses_post( wpautop( wptexturize( $description ) ) ) . '</div>'; ?>
	<?php } ?>

	<?php do_action( 'wc_product_addon_options', $addon ); ?>
