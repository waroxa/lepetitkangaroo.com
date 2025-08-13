<?php
/**
 * Plugin support: QuickCal Appointments (WPBakery support)
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Add [cff] in the VC shortcodes list
if (!function_exists('trx_addons_sc_quickcal_add_in_vc')) {
	function trx_addons_sc_quickcal_add_in_vc() {

		if (!trx_addons_exists_vc() || !trx_addons_exists_quickcal()) return;
		
		vc_lean_map( "quickcal-appointments", 'trx_addons_sc_quickcal_add_in_vc_params_ba');
		class WPBakeryShortCode_QuickCal_Appointments extends WPBakeryShortCode {}

		vc_lean_map( "quickcal-calendar", 'trx_addons_sc_quickcal_add_in_vc_params_bc');
		class WPBakeryShortCode_QuickCal_Calendar extends WPBakeryShortCode {}
		
		vc_lean_map( "quickcal-profile", 'trx_addons_sc_quickcal_add_in_vc_params_bp');
		class WPBakeryShortCode_QuickCal_Profile extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_quickcal_add_in_vc', 20);
}



// Params for QuickCal Appointments
if (!function_exists('trx_addons_sc_quickcal_add_in_vc_params_ba')) {
	function trx_addons_sc_quickcal_add_in_vc_params_ba() {
		return array(
				"base" => "quickcal-appointments",
				"name" => __("QuickCal Appointments", "trx_addons"),
				"description" => __("Display the currently logged in user's upcoming appointments", "trx_addons"),
				"category" => __('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_quickcal_appointments',
				"class" => "trx_sc_single trx_sc_quickcal_appointments",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => false,
				"params" => array()
			);
	}
}


// Params for QuickCal Profile
if (!function_exists('trx_addons_sc_quickcal_add_in_vc_params_bp')) {
	function trx_addons_sc_quickcal_add_in_vc_params_bp() {
		return array(
				"base" => "quickcal-profile",
				"name" => __("QuickCal Profile", "trx_addons"),
				"description" => __("Display the currently logged in user's profile", "trx_addons"),
				"category" => __('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_quickcal_profile',
				"class" => "trx_sc_single trx_sc_quickcal_profile",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => false,
				"params" => array()
			);
	}
}


// Params for QuickCal Calendar
if (!function_exists('trx_addons_sc_quickcal_add_in_vc_params_bc')) {
	function trx_addons_sc_quickcal_add_in_vc_params_bc() {
		return array(
				"base" => "quickcal-calendar",
				"name" => __("QuickCal Calendar", "trx_addons"),
				"description" => __("Insert quickcal calendar", "trx_addons"),
				"category" => __('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_quickcal_calendar',
				"class" => "trx_sc_single trx_sc_quickcal_calendar",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => esc_html__("Layout", "trx_addons"),
						"description" => esc_html__("Select style of the quickcal calendar", "trx_addons"),
						"admin_label" => true,
						"std" => "0",
						"value" => array_flip(array(
											'calendar' => esc_html__('Calendar', 'trx_addons'),
											'list' => esc_html__('List', 'trx_addons')
											)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "calendar",
						"heading" => esc_html__("Calendar", "trx_addons"),
						"description" => esc_html__("Select quickcal calendar to display", "trx_addons"),
						"admin_label" => true,
						"std" => "0",
						"value" => array_flip(trx_addons_array_merge(array(0 => esc_html__('- Select calendar -', 'trx_addons')), trx_addons_get_list_terms(false, 'booked_custom_calendars'))),
						"type" => "dropdown"
					),
					array(
						"param_name" => "year",
						"heading" => esc_html__("Year", "trx_addons"),
						"description" => esc_html__("Year to display on calendar by default", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-6',
						"admin_label" => true,
						"std" => date_i18n("Y"),
						"value" => date_i18n("Y"),
						"type" => "textfield"
					),
					array(
						"param_name" => "month",
						"heading" => esc_html__("Month", "trx_addons"),
						"description" => esc_html__("Month to display on calendar by default", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-6',
						"admin_label" => true,
						"std" => date_i18n("m"),
						"value" => date_i18n("m"),
						"type" => "textfield"
					)
				)
			);
	}
}
