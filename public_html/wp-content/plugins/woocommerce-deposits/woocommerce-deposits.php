<?php
/**
 * Plugin Name: WooCommerce Deposits
 * Requires Plugins: woocommerce
 * Plugin URI: https://woocommerce.com/products/woocommerce-deposits/
 * Description: Mark items as deposit items which customers can then place deposits on, rather than paying in full.
 * Version: 2.3.9
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text Domain: woocommerce-deposits
 * Domain Path: /languages
 * Requires at least: 6.6
 * Tested up to: 6.8
 * WC tested up to: 9.9
 * WC requires at least: 9.7
 * Requires PHP: 7.4
 * PHP tested up to: 8.3
 *
 * Copyright: © 2023 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 977087:de192a6cf12c4fd803248da5db700762
 *
 * @package woocommerce-deposits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_DEPOSITS_VERSION', '2.3.9' ); // WRCS: DEFINED_VERSION.
define( 'WC_DEPOSITS_FILE', __FILE__ );

/**
 * Activation hooks.
 */
register_activation_hook( WC_DEPOSITS_FILE, 'wc_deposits_activate' );

/**
 * Install deposits upon activation.
 */
function wc_deposits_activate() {
	require_once __DIR__ . '/includes/class-wc-deposits.php';
	WC_Deposits::get_instance()->install();
}


// Plugin init hook.
add_action( 'plugins_loaded', 'wc_deposits_init', 1 );

// Automatic translations.
add_filter( 'woocommerce_translations_updates_for_woocommerce-deposits', '__return_true' );

/**
 * Initialize plugin.
 */
function wc_deposits_init() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_deposits_woocommerce_deactivated' );
		return;
	}

	require_once __DIR__ . '/includes/class-wc-deposits.php';
	WC_Deposits::get_instance();
}

/**
 * WooCommerce Deactivated Notice.
 */
function wc_deposits_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Deposits requires %s to be installed and active.', 'woocommerce-deposits' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}
