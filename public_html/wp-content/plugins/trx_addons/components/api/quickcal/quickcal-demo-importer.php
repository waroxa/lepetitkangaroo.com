<?php
/**
 * Plugin support: QuickCal Appointments (Importer support)
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}


// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_quickcal_importer_required_plugins' ) ) {
	add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_quickcal_importer_required_plugins', 10, 2 );
	function trx_addons_quickcal_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'quickcal')!==false && !trx_addons_exists_quickcal() )
			$not_installed .= '<br>' . esc_html__('QuickCal Appointments', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_quickcal_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'trx_addons_quickcal_importer_set_options', 10, 1 );
	function trx_addons_quickcal_importer_set_options($options=array()) {
		if ( trx_addons_exists_quickcal() && in_array('quickcal', $options['required_plugins']) ) {
			$options['additional_options'][] = 'quickcal_%';			// Add slugs to export options of this plugin
			$options['additional_options'][] = 'booked_%';				// Attention! QuickCal use Booked plugin options
		}
		return $options;
	}
}

// Prevent import plugin's specific options if plugin is not installed
if ( ! function_exists( 'trx_addons_quickcal_importer_check_options' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_quickcal_importer_check_options', 10, 4 );
	function trx_addons_quickcal_importer_check_options( $allow, $k, $v, $options ) {
		if ( strpos( $k, 'quickcal_' ) === 0 || strpos( $k, 'booked_' ) === 0 ) {
			$allow = ( trx_addons_exists_quickcal() && in_array( 'quickcal', $options['required_plugins'] ) )
					|| ( function_exists( 'trx_addons_exists_booked' ) && trx_addons_exists_booked() && in_array( 'booked', $options['required_plugins'] ) );
		}
		return $allow;
	}
}

// Check if the row will be imported
if ( ! function_exists( 'trx_addons_quickcal_importer_check_row' ) ) {
	add_filter( 'trx_addons_filter_importer_import_row', 'trx_addons_quickcal_importer_check_row', 9, 4 );
	function trx_addons_quickcal_importer_check_row( $flag, $table, $row, $list ) {
		if ( $flag || strpos( $list, 'quickcal' ) === false ) {
			return $flag;
		}
		if ( trx_addons_exists_quickcal() || ( function_exists( 'trx_addons_exists_booked' ) && trx_addons_exists_booked() ) ) {
			if ( $table == 'posts' ) {
				$flag = in_array( $row['post_type'], array( 'quickcal_appointments', 'booked_appointments' ) );
			}
		}
		return $flag;
	}
}

// Add options while export
if ( ! function_exists( 'trx_addons_quickcal_export_options' ) ) {
	add_filter( 'trx_addons_filter_export_options', 'trx_addons_quickcal_export_options' );
	function trx_addons_quickcal_export_options( $options ) {
		// $options['quickcal_welcome_screen'] = false;
		$options['booked_welcome_screen'] = false;				// Attention! QuickCal use Booked plugin options
		return $options;
	}
}
