<?php
/* Date Time Picker Field support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'kidscare_date_time_picker_field_feed_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'kidscare_date_time_picker_field_theme_setup9', 9 );
	function kidscare_date_time_picker_field_theme_setup9() {
		add_filter( 'kidscare_filter_merge_styles', 'kidscare_date_time_picker_field_merge_styles' );
		if ( is_admin() ) {
			add_filter( 'kidscare_filter_tgmpa_required_plugins', 'kidscare_date_time_picker_field_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'kidscare_date_time_picker_field_tgmpa_required_plugins' ) ) {
	
	function kidscare_date_time_picker_field_tgmpa_required_plugins( $list = array() ) {
		if ( kidscare_storage_isset( 'required_plugins', 'date-time-picker-field' ) ) {
			$list[] = array(

				'name'     => kidscare_storage_get_array( 'required_plugins', 'date-time-picker-field', 'title' ),
				'slug'     => 'date-time-picker-field',
				'required' => false,
			);
		}
		return $list;
	}
}
// Check if this plugin installed and activated
if ( ! function_exists( 'kidscare_exists_date_time_picker_field' ) ) {
	function kidscare_exists_date_time_picker_field() {
		return class_exists( 'CMoreira\\Plugins\\DateTimePicker\\Init' );
	}
}
// Merge custom styles
if ( ! function_exists( 'kidscare_date_time_picker_field_merge_styles' ) ) {
	function kidscare_date_time_picker_field_merge_styles( $list ) {
		if ( kidscare_exists_date_time_picker_field() ) {
			$list[] = 'plugins/date-time-picker-field/_date-time-picker-field.scss';
		}
		return $list;
	}
}

// Add plugin-specific colors and fonts to the custom CSS
if ( kidscare_exists_date_time_picker_field() ) {
	require_once KIDSCARE_THEME_DIR . 'plugins/date-time-picker-field/date-time-picker-field-styles.php'; 
}

// Set plugin's specific importer options
if ( !function_exists( 'kidscare_date_time_picker_field_importer_set_options' ) ) {
    if (is_admin()) add_filter( 'trx_addons_filter_importer_options',    'kidscare_date_time_picker_field_importer_set_options' );
    function kidscare_date_time_picker_field_importer_set_options($options=array()) {   
        if ( kidscare_exists_date_time_picker_field() && in_array('date-time-picker-field', $options['required_plugins']) ) {
            $options['additional_options'][]    = 'dtpicker';                    // Add slugs to export options for this plugin
        }
        return $options;
    }
}
