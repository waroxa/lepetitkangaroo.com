<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'kidscare_quickcal_get_css' ) ) {
	add_filter( 'kidscare_filter_get_css', 'kidscare_quickcal_get_css', 10, 2 );
	function kidscare_quickcal_get_css( $css, $args ) {

		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS

.booked-calendar-wrap .booked-appt-list .timeslot .timeslot-people button,
body #booked-profile-page input[type="submit"],
body #booked-profile-page button,
body .booked-list-view input[type="submit"],
body .booked-list-view button,
body table.booked-calendar input[type="submit"],
body table.booked-calendar button,
body .booked-modal input[type="submit"],
body .booked-modal button,
body #booked-profile-page .appt-block .booked-cal-buttons .google-cal-button,
 body #booked-profile-page .booked-profile-appt-list .appt-block .cancel, 
 body #booked-profile-page .booked-profile-appt-list .appt-block .booked-cal-buttons a {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}

body div.booked-calendar-wrap div.booked-calendar .bc-head .bc-row .bc-col .monthName,
table.booked-calendar thead tr:first-child th {
    {$fonts['h5_font-family']}
}

CSS;
		}

		if ( isset( $css['colors'] ) && isset( $args['colors'] ) ) {
			$colors         = $args['colors'];
			$css['colors'] .= <<<CSS

/* Form fields */
#booked-page-form {
	color: {$colors['text']};
	border-color: {$colors['bd_color']};
}

#booked-profile-page .booked-profile-header {
	background-color: {$colors['bg_color']} !important;
	border-color: transparent !important;
	color: {$colors['text']};
}
#booked-profile-page .booked-user h3 {
	color: {$colors['text_dark']};
}
#booked-profile-page .booked-profile-header .booked-logout-button:hover {
	color: {$colors['text_link']};
}

#booked-profile-page .booked-tabs {
	border-color: {$colors['alter_bd_color']} !important;
}

.booked-modal .bm-window p.booked-title-bar {
	color: {$colors['bg_color']} !important;
	background-color: {$colors['text_dark']} !important;
}
.booked-modal .bm-window .close i {
	color: {$colors['bg_color']};
}
.booked-modal .bm-window .booked-scrollable {
	color: {$colors['text']};
	background-color: {$colors['bg_color']} !important;
}
.booked-modal .bm-window .booked-scrollable em {
	color: {$colors['text_link']};
}
.booked-modal .bm-window #customerChoices {
	background-color: {$colors['extra_bg_hover']};
	border-color: {$colors['extra_bd_hover']};
}
.booked-modal .bm-window #customerChoices label {
	color: {$colors['text_light']};
}
.booked-form .booked-appointments {
	color: {$colors['text']};
	background-color: {$colors['alter_bg_color']} !important;	
}
.booked-modal .bm-window p.appointment-title {
	color: {$colors['alter_dark']};	
}
.booked-modal .bm-window .close:hover i {
    color: {$colors['text_link']};
}

/* Profile page and tabs */
.booked-calendarSwitcher.calendar,
.booked-calendarSwitcher.calendar select,
#booked-profile-page .booked-tabs {
	background-color: {$colors['alter_bg_color']} !important;
}
#booked-profile-page .booked-tabs li a {
	background-color: {$colors['extra_bg_hover']};
	color: {$colors['extra_dark']};
}
#booked-profile-page .booked-tabs li a i {
	color: {$colors['extra_dark']};
}
#booked-profile-page .booked-tabs li.active a,
#booked-profile-page .booked-tabs li.active a:hover,
#booked-profile-page .booked-tabs li a:hover {
	color: {$colors['extra_dark']} !important;
	background-color: {$colors['extra_bg_color']} !important;
}
#booked-profile-page .booked-tab-content {
	background-color: {$colors['bg_color']};
	border-color: {$colors['alter_bd_color']};
}

/* Calendar */
table.booked-calendar td .date {
    background-color: {$colors['alter_bg_color']} !important;
}
table.booked-calendar td:hover .date {
    background-color: {$colors['text_hover']} !important;
}
table.booked-calendar td:hover .date span {
    color: {$colors['inverse_link']} !important;
}

table.booked-calendar td.prev-date .date {
    background-color: {$colors['alter_bg_color']} !important;
}
table.booked-calendar td.prev-date:hover .date {
    background-color: {$colors['text_hover']} !important;
}
table.booked-calendar td.prev-date:hover .date span {
    color: {$colors['inverse_link']} !important;
    border-color: {$colors['inverse_link']} !important;
}

table.booked-calendar td.next-month .date {
    background-color: {$colors['alter_bg_color']} !important;
}
table.booked-calendar td.prev-month:hover .date,
table.booked-calendar td.next-month:hover .date {
    background-color: {$colors['text_hover_08']} !important;
}
table.booked-calendar td.prev-date:hover .date span,
table.booked-calendar td.prev-month:hover .date span {
    background-color: transparent !important;
    color: {$colors['inverse_link']} !important;
}

table.booked-calendar td.today .date {
    background-color: {$colors['alter_bg_color']} !important;
}
table.booked-calendar td.today:hover .date {
    background-color: {$colors['text_hover']} !important;
}
table.booked-calendar td.today:hover .date span {
    color: {$colors['inverse_link']} !important;
}

table.booked-calendar .booked-appt-list {
    background-color: {$colors['bg_color']} !important;
}



table.booked-calendar thead tr {
	background-color: {$colors['extra_bg_color']} !important;
}
table.booked-calendar thead tr th {
	color: {$colors['inverse_link']} !important;
	border-color: rgba(255,255,255,0.2) !important;
}
table.booked-calendar thead tr th {
	border-bottom-color: {$colors['bg_color']} !important;
}
table.booked-calendar thead th i {
	color: {$colors['text_dark']} !important;
}
table.booked-calendar thead th i:hover {
	color: {$colors['text_link']} !important;
}
table.booked-calendar thead th .monthName a {
	color: {$colors['text_link']};
}
table.booked-calendar thead th .monthName a:hover {
	color: {$colors['text_hover']};
}

table.booked-calendar tbody tr {
	background-color: {$colors['alter_bg_color']} !important;
}
table.booked-calendar tbody tr td {
	color: {$colors['alter_text']} !important;
	border-color: {$colors['alter_bd_color']} !important;
}
table.booked-calendar tbody tr td:hover {
	color: {$colors['alter_dark']} !important;
}
table.booked-calendar tbody tr td.today .date {
	color: {$colors['text_dark']} !important;
	background-color: {$colors['alter_bg_color']} !important;
}


table.booked-calendar tbody td:hover .date span {
	border-color: {$colors['bg_color']} !important;
	background-color: {$colors['bg_color']} !important;
	color: {$colors['text_dark']} !important;
}


table.booked-calendar tbody td.today .date span {
	border-color: {$colors['text_link']};
}
table.booked-calendar tbody td.today:hover .date span {
	border-color: {$colors['bg_color']} !important;
	background-color: {$colors['bg_color']} !important;
	color: {$colors['text_dark']} !important;
}

.booked-calendar-wrap .booked-appt-list h2 {
	color: {$colors['text_dark']};
}
.booked-calendar-wrap .booked-appt-list .timeslot {
	border-color: {$colors['alter_bd_color']};	
}
.booked-calendar-wrap .booked-appt-list .timeslot .timeslot-title {
	color: {$colors['text_link']};
}
.booked-calendar-wrap .booked-appt-list .timeslot .timeslot-time {
	color: {$colors['text_dark']};
}
.booked-calendar-wrap .booked-appt-list .timeslot .spots-available {
	color: {$colors['text']};
}

body table.booked-calendar td.today.prev-date .date span,
table.booked-calendar thead tr:first-child th {
    color: {$colors['text_dark']} !important;
}

body div.booked-calendar-wrap div.booked-calendar .bc-head .bc-row .bc-col .page-right,
body div.booked-calendar-wrap div.booked-calendar .bc-head .bc-row .bc-col .page-left,
div.booked-calendar-wrap .bc-head .bc-row .bc-col .monthName {
    color: {$colors['text_dark']} !important;
}

body div.booked-calendar-wrap div.booked-calendar .bc-head .bc-row .bc-col .page-right:hover,
body div.booked-calendar-wrap div.booked-calendar .bc-head .bc-row .bc-col .page-left:hover {
    color: {$colors['text_link']} !important;
}

body div.booked-calendar-wrap div.booked-calendar .bc-head .bc-row.top .bc-col {
	background-color: {$colors['bg_color']} !important;
}

div.booked-calendar-wrap .bc-head .bc-row .bc-col .monthName a {
    color: {$colors['text_link']} !important;
}
div.booked-calendar-wrap .bc-head .bc-row .bc-col .monthName a:hover {
    color: {$colors['text_hover']} !important;
}
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col .date,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.prev-date .date,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.blur .date,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.next-month .date,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.prev-month .date,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.prev-date .date span,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.blur .date span {
    background-color: {$colors['alter_bg_color']} !important;
}

body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col:hover .date,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.prev-date:hover .date,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.blur:hover .date,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.next-month:hover .date,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.prev-month:hover .date,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.prev-date:hover .date span,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.blur:hover .date span {
    background-color: {$colors['text_hover']} !important;
}

body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col:hover .date span {
	border-color: {$colors['inverse_link']};
}
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col {
	color: {$colors['text_dark']};
}
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.next-month:hover .date span,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col:hover .date span  {
	color: {$colors['inverse_dark']} !important;
	background-color: {$colors['inverse_link']} !important;
}

body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.prev-month.prev-date .date span {
    color: {$colors['text_light']} !important;
}

body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.next-month .date span,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.prev-date .date span {
    color: {$colors['text_dark']} !important;
}

body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.prev-month.prev-date:hover .date span,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.prev-date:hover .date span {
	color: {$colors['inverse_link']} !important;
}

body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.entryBlock,
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col {
	border-color: {$colors['bg_color']};
}

body div.booked-calendar-wrap div.booked-calendar .bc-head .bc-row.days .bc-col {
	border-color: rgba(255,255,255,0.2) !important;
}

body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.today .date span {
	border-color: {$colors['text_link']};
}
body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col.today:hover .date span {
	border-color: {$colors['inverse_link']};
}

body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.entryBlock {
	background-color: {$colors['alter_bg_color']};
}

body div.booked-calendar-wrap div.booked-calendar .bc-body .bc-row.week .bc-col:last-child {
	border-color-right: {$colors['alter_bg_color']};
}

/* List view */
body .booked-list-view a.booked_list_date_picker_trigger.booked-dp-active, 
body .booked-list-view a.booked_list_date_picker_trigger.booked-dp-active:hover {
	background: {$colors['bg_color_0']};
}
#ui-datepicker-div.booked_custom_date_picker table.ui-datepicker-calendar tbody td a,
#ui-datepicker-div.booked_custom_date_picker table.ui-datepicker-calendar tbody, 
#ui-datepicker-div.booked_custom_date_picker table.ui-datepicker-calendar tbody td {
	color: {$colors['text']};
}
#ui-datepicker-div.booked_custom_date_picker table.ui-datepicker-calendar tbody td.ui-datepicker-unselectable span {
	color: {$colors['text_light']};
}
#ui-datepicker-div.booked_custom_date_picker table.ui-datepicker-calendar tbody td a:hover {
	color: {$colors['inverse_link']};
	background: {$colors['text_hover']};
}
#ui-datepicker-div.booked_custom_date_picker table.ui-datepicker-calendar tbody td.ui-datepicker-today a,
#ui-datepicker-div.booked_custom_date_picker table.ui-datepicker-calendar tbody td a.ui-state-active {
	color: {$colors['inverse_link']};
}
body div.booked-calendar-wrap.booked-list-view .booked-appt-list .timeslot .spots-available {
	color: {$colors['text']}!important;
}

body .booked-appt-list {
	background: {$colors['bg_color']};
}

.booked-calendar-wrap .booked-appt-list .timeslot .timeslot-people button[disabled],
.booked-calendar-wrap .booked-appt-list .timeslot .timeslot-people button[disabled]:hover {
	background: {$colors['text_light']} !important;
	color: {$colors['text']} !important;
}

body #booked-profile-page .booked-profile-header .booked-logout-button {
	color: {$colors['text_dark']};
}
body #booked-profile-page .booked-tabs li a .counter {
	background: {$colors['text_link2']};
	color: {$colors['text_dark']};
}

CSS;
		}

		return $css;
	}
}

