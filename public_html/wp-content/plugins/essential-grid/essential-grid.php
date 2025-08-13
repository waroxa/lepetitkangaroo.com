<?php
/**
 * Essential Grid
 *
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 *
 * @wordpress-plugin
 * Plugin Name: Essential Grid
 * Plugin URI: https://www.essential-grid.com
 * Description: Essential Grid - Inject life into your websites using the most impressive WordPress gallery plugin
 * Version: 3.1.9
 * Requires at least: 6.0
 * Requires PHP: 7.0
 * Author: ThemePunch
 * Author URI: https://themepunch.com
 * Text Domain: essential-grid
 * Domain Path: /languages
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( class_exists( 'Essential_Grid' ) ) {
	die( 'ERROR: It looks like you have more than one instance of Essential Grid installed. Please remove additional instances for this plugin to work again.' );
}

define( 'ESG_REVISION', '3.1.9' );
define( 'ESG_TP_TOOLS', '6.7.26' );

define( 'ESG_PLUGIN_SLUG', 'essential-grid' );
define( 'ESG_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'ESG_PLUGIN_SLUG_PATH', plugin_basename( __FILE__ ) );
define( 'ESG_PLUGIN_ADMIN_PATH', ESG_PLUGIN_PATH . 'admin' );
define( 'ESG_PLUGIN_PUBLIC_PATH', ESG_PLUGIN_PATH . 'public' );
define( 'ESG_PLUGIN_URL', get_esg_plugin_url() );

global $esg_dev_mode,
       $esg_wc_is_localized,
       $esg_loadbalancer;

$esg_dev_mode        = file_exists( ESG_PLUGIN_PATH . 'public/assets/js/dev/esg.js' );
$esg_wc_is_localized = false; //used to determinate if already done for cart button on this skin

require_once( ESG_PLUGIN_ADMIN_PATH . '/includes/loadbalancer.class.php' );
$esg_loadbalancer = new Essential_Grid_LoadBalancer();
$esg_loadbalancer->refresh_server_list();

require_once( ESG_PLUGIN_PATH . 'includes/db/abstract.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/db/navigation_skin.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/db/item_elements.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/db/skin.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/db/grid.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/db.class.php' );
Essential_Grid_Db::define_tables();

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/
require_once( ESG_PLUGIN_PATH . 'includes/Detection/Exception/MobileDetectException.php' );
require_once( ESG_PLUGIN_PATH . 'includes/Detection/MobileDetect.php' );
require_once( ESG_PLUGIN_PATH . 'includes/base.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/post-type.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/addons/addon.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/addons.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/image-optimization.class.php' );
require_once( ESG_PLUGIN_PATH . 'public/essential-grid.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/global-css.class.php' );
include_once( ESG_PLUGIN_PATH . 'includes/coloreasing.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/colorpicker.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/navigation.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.areas.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.abstract.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.cart.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.filter.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.pagination.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.pagination-left.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.pagination-right.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/widgets/grids-widget.sorting.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/item-skin.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/item-element.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/wpml.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/woocommerce.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/meta.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/meta-link.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/fonts.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/search.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/aq_resizer.class.php' );
require_once( ESG_PLUGIN_PATH . 'includes/wordpress-update-fix.class.php' );
require_once( ESG_PLUGIN_ADMIN_PATH . '/includes/builders.class.php' );
require_once( ESG_PLUGIN_ADMIN_PATH . '/includes/builders/gutenberg.class.php' );
require_once( ESG_PLUGIN_ADMIN_PATH . '/includes/builders/wpbakery.class.php' );
require_once( ESG_PLUGIN_ADMIN_PATH . '/includes/builders/elementor.class.php' );

new Essential_Grid_Post_Type();
new Essential_Grid_Wpml();
Essential_Grid_Woocommerce::add_hooks();
Essential_Grid_Builders::get_instance();

register_activation_hook( __FILE__, [ 'Essential_Grid', 'activation_hooks' ] );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/
if ( is_admin() ) {
	require_once( ESG_PLUGIN_PATH . 'admin/includes/assets.class.php' );
	require_once( ESG_PLUGIN_PATH . 'admin/includes/license.class.php' );
	require_once( ESG_PLUGIN_PATH . 'admin/includes/favorite.class.php' );
	require_once( ESG_PLUGIN_PATH . 'admin/essential-grid-admin.class.php' );
	require_once( ESG_PLUGIN_PATH . 'admin/includes/update.class.php' );
	require_once( ESG_PLUGIN_PATH . 'admin/includes/dialogs.class.php' );
	require_once( ESG_PLUGIN_PATH . 'admin/includes/import.class.php' );
	require_once( ESG_PLUGIN_PATH . 'admin/includes/export.class.php' );
	require_once( ESG_PLUGIN_PATH . 'admin/includes/import-port.class.php' );
	require_once( ESG_PLUGIN_PATH . 'admin/includes/import-post.class.php' );
	require_once( ESG_PLUGIN_PATH . 'admin/includes/plugin-update.class.php' );
	require_once( ESG_PLUGIN_PATH . 'admin/includes/newsletter.class.php' );
	require_once( ESG_PLUGIN_PATH . 'admin/includes/library.class.php' );

	add_action( 'plugins_loaded', [ 'Essential_Grid_Db', 'create_tables' ], 5 );
	add_action( 'plugins_loaded', [ 'Essential_Grid_Assets', 'get_instance' ], 5 );
	add_action( 'plugins_loaded', [ 'Essential_Grid_Admin', 'do_update_checks' ], 5 );
	add_action( 'plugins_loaded', [ 'Essential_Grid_Admin', 'get_instance' ], 10 );
}

// Essential Grid init after_setup_theme
// to allow users add filters / actions in theme functions.php
add_action( 'after_setup_theme', 'esg_after_theme_setup', 10 );
function esg_after_theme_setup() {
	Essential_Grid::get_instance();
	add_action( 'widgets_init', [ 'Essential_Grid', 'register_custom_sidebars' ] );
	add_action( 'widgets_init', [ 'Essential_Grid', 'register_custom_widget' ] );

	add_filter( 'the_content', [ 'Essential_Grid', 'fix_shortcodes' ] );
	add_filter( 'post_thumbnail_html', [ 'Essential_Grid', 'post_thumbnail_replace' ], 20, 5 );

	add_shortcode( 'ess_grid', [ 'Essential_Grid', 'register_shortcode' ] );
	add_shortcode( 'ess_grid_ajax_target', [ 'Essential_Grid', 'register_shortcode_ajax_target' ] );
	add_shortcode( 'ess_grid_nav', [ 'Essential_Grid', 'register_shortcode_filter' ] );
	add_shortcode( 'ess_grid_search', [ 'Essential_Grid_Search', 'register_shortcode_search' ] );

	if ( ! is_admin() ) {
		new Essential_Grid_Search();
	}
}

function get_esg_plugin_url() {
	$url = str_replace( 'index.php', '', plugins_url( 'index.php', __FILE__ ) );
	if ( empty( wp_parse_url( $url, PHP_URL_SCHEME ) ) ) {
		$url = untrailingslashit( get_site_url() ) . $url;
	}

	return str_replace( [ "\n", "\r" ], '', $url );
}
