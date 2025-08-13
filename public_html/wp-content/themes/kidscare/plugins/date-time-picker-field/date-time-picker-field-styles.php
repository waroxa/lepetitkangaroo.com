<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'kidscare_date_time_picker_field_get_css' ) ) {
	add_filter( 'kidscare_filter_get_css', 'kidscare_date_time_picker_field_get_css', 10, 2 );
	function kidscare_date_time_picker_field_get_css( $css, $args ) {

		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS

/* FONTS */
.xdsoft_datetimepicker .xdsoft_label,
.xdsoft_datetimepicker {
	{$fonts['p_font-family']}
}

CSS;
		}

		if ( isset( $css['colors'] ) && isset( $args['colors'] ) ) {
			$colors         = $args['colors'];
			$css['colors'] .= <<<CSS

/* CSS */
.xdsoft_datetimepicker .xdsoft_calendar td,
.xdsoft_datetimepicker .xdsoft_calendar th {
	color: {$colors['alter_dark']};
	border-color: {$colors['bd_color']};
}
.xdsoft_datetimepicker .xdsoft_calendar th {
	color: {$colors['extra_dark']};
	background-color: {$colors['extra_bg_color']};
	border-color: {$colors['alter_bd_hover']};
}
.xdsoft_datetimepicker .xdsoft_label>.xdsoft_select>div>.xdsoft_option.xdsoft_current,
.xdsoft_datetimepicker .xdsoft_calendar td:hover,
.xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_current,
.xdsoft_datetimepicker .xdsoft_calendar td.xdsoft_today {
	color: {$colors['inverse_hover']}!important;
}
.xdsoft_datetimepicker .xdsoft_calendar td:hover,
.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box>div>div:not(.xdsoft_current):hover {
	color: {$colors['inverse_hover']}!important;
}
.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box>div>div,
.xdsoft_datetimepicker .xdsoft_calendar td:hover,
.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box>div>div:hover {
	background-color: {$colors['extra_dark']}!important;
}
.xdsoft_datetimepicker .xdsoft_timepicker .xdsoft_time_box>div>div.xdsoft_current {
	background-color: {$colors['alter_bg_hover']}!important;
	color: {$colors['alter_dark']}!important;
}
]

CSS;
		}

		return $css;
	}
}

