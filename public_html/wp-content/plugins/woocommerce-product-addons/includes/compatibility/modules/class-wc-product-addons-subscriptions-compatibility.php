<?php
/**
 * WC_Product_Addons_WC_Subscriptions_Compatibility class
 *
 * @package  WooCommerce Product Add-Ons
 * @since    7.9.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Subscriptions Integration.
 *
 * @version  7.9.1
 */
class WC_Product_Addons_Subscriptions_Compatibility {

	/**
	 * Constructor.
	 */
	public static function init() {
		add_filter( 'woocommerce_product_addons_display_editing_in_order_button', array( __CLASS__, 'show_if_subscription' ), 10, 3 );
	}

	/**
	 * Display the "Configure/Edit" button in admin for subscription products.
	 *
	 * @param  bool          $display_editing_in_order_button  Whether to display the "Configure/Edit" button.
	 * @param  WC_Order_Item $item                             Order item object.
	 * @param  WC_Product    $product                          Product object.
	 *
	 * @return boolean
	 */
	public static function show_if_subscription( $display_editing_in_order_button, $item, $product ) {

		if ( WC_Subscriptions_Product::is_subscription( $product ) ) {
			return true;
		}

		return $display_editing_in_order_button;
	}
}

WC_Product_Addons_Subscriptions_Compatibility::init();
