<?php
// Add plugin-specific colors and fonts to the custom CSS
if ( ! function_exists( 'kidscare_tribe_events_get_css' ) ) {
	add_filter( 'kidscare_filter_get_css', 'kidscare_tribe_events_get_css', 10, 2 );
	function kidscare_tribe_events_get_css( $css, $args ) {
		if ( isset( $css['fonts'] ) && isset( $args['fonts'] ) ) {
			$fonts         = $args['fonts'];
			$css['fonts'] .= <<<CSS
			
.tribe-events-list .tribe-events-list-event-title {
	{$fonts['h3_font-family']}
}

#tribe-events .tribe-events-button,
.tribe-events-button,
.tribe-events-cal-links a,
.tribe-events-sub-nav li a {
	{$fonts['button_font-family']}
	{$fonts['button_font-size']}
	{$fonts['button_font-weight']}
	{$fonts['button_font-style']}
	{$fonts['button_line-height']}
	{$fonts['button_text-decoration']}
	{$fonts['button_text-transform']}
	{$fonts['button_letter-spacing']}
}
#tribe-bar-form button, #tribe-bar-form a,
.tribe-events-read-more {
	{$fonts['button_font-family']}
	{$fonts['button_letter-spacing']}
}
#tribe-events-footer .tribe-events-sub-nav li a {
    {$fonts['button_font-family']}
}
.tooltipster-base.tribe-events-tooltip-theme .tooltipster-box .tooltipster-content,
.tribe-events .datepicker .datepicker-switch,
.tribe-common .tribe-common-h6.tribe-events-calendar-list__event-title,
#tribe-bar-views .tribe-bar-views-list,
.tribe-events-list .tribe-events-list-separator-month,
.tribe-events-schedule, .tribe-events-schedule h2 {
	{$fonts['h5_font-family']}
}
.tooltipster-base.tribe-events-tooltip-theme .tooltipster-box .tooltipster-content,
.tribe-events .datepicker .day, .tribe-events .datepicker .dow,
.tribe-events .tribe-events-c-subscribe-dropdown .tribe-events-c-subscribe-dropdown__list-item a,
.tribe-events .tribe-events-calendar-list__event-date-tag-weekday,
#tribe-bar-form input, #tribe-events-content.tribe-events-month,
#tribe-events-content .tribe-events-calendar div[id*="tribe-events-event-"] h3.tribe-events-month-event-title,
#tribe-mobile-container .type-tribe_events,
.tribe-events-list-widget ol li .tribe-event-title {
	{$fonts['p_font-family']}
}
.tribe-events-loop .tribe-event-schedule-details,
.single-tribe_events #tribe-events-content .tribe-events-event-meta dt,
#tribe-mobile-container .type-tribe_events .tribe-event-date-start {
	{$fonts['info_font-family']};
}

/* Fonts for new table of the Events Calendar */
.tribe-events-c-top-bar__datepicker button.ribe-common-h3,
.tribe-common button.tribe-common-h3,
.tribe-events-c-top-bar__today-button,
.tribe-events .tribe-events-c-view-selector__list-item-text,
.tribe-common .tribe-common-c-btn,
.tribe-common a.tribe-common-c-btn,
.tribe-common .tribe-common-c-btn-border,
.tribe-events .tribe-events-c-nav__prev:disabled, .tribe-events button.tribe-events-c-nav__prev:disabled,
.tribe-events .tribe-events-c-nav__next:disabled, .tribe-events button.tribe-events-c-nav__next:disabled,
.tribe-common a.tribe-common-c-btn-border,
.tribe-common--breakpoint-medium.tribe-events .tribe-events-c-search__button,
.tribe-common .tribe-common-c-btn-border-small,
.tribe-common a.tribe-common-c-btn-border-small {
    {$fonts['h5_font-family']}
}
.tribe-common .tribe-common-h4,
.tribe-common .tribe-common-b3,
.tribe-common .tribe-common-b2,
.tribe-common .tribe-common-h7,
.tribe-common .tribe-common-h8,
.tribe-common .tribe-common-h2,
.tribe-common .tribe-common-h3,
.tribe-common .tribe-common-h5,
.tribe-common .tribe-common-h6 {
	{$fonts['p_font-family']}
}

CSS;
		}

		if ( isset( $css['vars'] ) && isset( $args['vars'] ) ) {
			$vars         = $args['vars'];
			$css['vars'] .= <<<CSS


CSS;
		}

		if ( isset( $css['colors'] ) && isset( $args['colors'] ) ) {
			$colors         = $args['colors'];
			$css['colors'] .= <<<CSS

#tribe-events-content table.tribe-events-calendar {
    border-color: {$colors['alter_bd_hover']};
}

/* Filters bar */
#tribe-bar-form {
	color: {$colors['text_dark']};
}
#tribe-bar-form input[type="text"] {
	color: {$colors['input_text']};
	border-color: {$colors['input_bd_color']} !important;
}
#tribe-bar-form input[type="text"]:focus {
    color: {$colors['input_dark']};
    border-color: {$colors['input_bd_hover']} !important;
}

.datepicker thead tr:first-child th:hover, .datepicker tfoot tr th:hover {
	color: {$colors['text_link']};
	background: {$colors['text_dark']};
}

/* Content */
.tribe-events-calendar thead th {
	color: {$colors['extra_dark']};
	background: {$colors['extra_bg_color']} !important;
}
.tribe-events-calendar thead th + th:before {
	background: {$colors['extra_bd_color']};
}
#tribe-events-content .tribe-events-calendar td,
#tribe-events-content .tribe-events-calendar th {
	border-color: {$colors['text_dark_01']} !important;
}
.tribe-events-calendar td div[id*="tribe-events-daynum-"],
.tribe-events-calendar td div[id*="tribe-events-daynum-"] > a {
	color: {$colors['text_dark']};
}
.tribe-events-calendar td.tribe-events-othermonth {
	color: {$colors['alter_light']};
}
.tribe-events-calendar td.tribe-events-othermonth div[id*="tribe-events-daynum-"],
.tribe-events-calendar td.tribe-events-othermonth div[id*="tribe-events-daynum-"] > a {
	color: {$colors['text']};
}
.tribe-events-calendar td.tribe-events-past div[id*="tribe-events-daynum-"], .tribe-events-calendar td.tribe-events-past div[id*="tribe-events-daynum-"] > a {
	color: {$colors['text_light']};
}
.tribe-events-calendar td.tribe-events-present div[id*="tribe-events-daynum-"],
.tribe-events-calendar td.tribe-events-present div[id*="tribe-events-daynum-"] > a {
	color: {$colors['text_link']};
}
.tribe-events-calendar td.tribe-events-present:before {
	border-color: {$colors['text_link']};
}
.tribe-events-calendar .tribe-events-has-events:after {
	background-color: {$colors['text']};
}
.tribe-events-calendar .mobile-active.tribe-events-has-events:after {
	background-color: {$colors['bg_color']};
}
#tribe-events-content .tribe-events-calendar td,
#tribe-events-content .tribe-events-calendar div[id*="tribe-events-event-"] h3.tribe-events-month-event-title a {
	color: {$colors['text']};
}
#tribe-events-content .tribe-events-calendar div[id*="tribe-events-event-"] h3.tribe-events-month-event-title a:hover {
	color: {$colors['text_hover']};
}
#tribe-events-content .tribe-events-calendar td.mobile-active,
#tribe-events-content .tribe-events-calendar td.mobile-active:hover {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
}
#tribe-events-content .tribe-events-calendar td.mobile-active div[id*="tribe-events-daynum-"] {
	background-color: {$colors['bg_color']};
}
#tribe-events-content .tribe-events-calendar td.tribe-events-othermonth.mobile-active div[id*="tribe-events-daynum-"] a,
.tribe-events-calendar .mobile-active div[id*="tribe-events-daynum-"] a {
	background-color: transparent;
	color: {$colors['bg_color']};
}
.events-archive.events-gridview #tribe-events-content table .type-tribe_events {
	border-color: {$colors['alter_bd_color']};
}
#tribe-mobile-container .type-tribe_events~.type-tribe_events {
    border-color: {$colors['bd_color']};
}

/* Tooltip */
.recurring-info-tooltip,
.tribe-events-calendar .tribe-events-tooltip,
.tribe-events-week .tribe-events-tooltip,
.tribe-events-shortcode.view-week .tribe-events-tooltip,
.tribe-events-tooltip .tribe-events-arrow {
	color: {$colors['alter_text']};
	background: {$colors['alter_bg_color']};
	border-color: {$colors['alter_bd_color']};
}
#tribe-events-content .tribe-events-tooltip .summary { 
	color: {$colors['bg_color']};
	background: {$colors['extra_bg_color']};
}
.tribe-events-tooltip .tribe-event-duration {
	color: {$colors['extra_dark']};
}
.tribe-events-calendar td.tribe-events-present div[id*="tribe-events-daynum-"] {
    background-color: {$colors['text_hover']} !important;
    color: {$colors['inverse_link']} !important;
}

/* Events list */
.tribe-events-list-separator-month {
	color: {$colors['text_dark']};
}
.tribe-events-list-separator-month:after {
	border-color: {$colors['bd_color']};
}
.tribe-events-list .type-tribe_events + .type-tribe_events,
.tribe-events-day .tribe-events-day-time-slot + .tribe-events-day-time-slot + .tribe-events-day-time-slot {
	border-color: {$colors['bd_color']};
}
.tribe-events-list-separator-month span {
	background-color: {$colors['bg_color']};	
}
.tribe-events-list .tribe-events-event-cost span {
	color: {$colors['inverse_link']};
	border-color: {$colors['text_link']};
	background: {$colors['text_link']};
}
.tribe-mobile .tribe-events-loop .tribe-events-event-meta {
	color: {$colors['alter_text']};
	border-color: {$colors['alter_bd_color']};
	background-color: {$colors['alter_bg_color']};
}
.tribe-mobile .tribe-events-loop .tribe-events-event-meta a {
	color: {$colors['alter_link']};
}
.tribe-mobile .tribe-events-loop .tribe-events-event-meta a:hover {
	color: {$colors['alter_hover']};
}
.tribe-mobile .tribe-events-list .tribe-events-venue-details {
	border-color: {$colors['alter_bd_color']};
}

.single-tribe_events #tribe-events-footer,
.tribe-events-day #tribe-events-footer,
.events-list #tribe-events-footer,
.tribe-events-map #tribe-events-footer,
.tribe-events-photo #tribe-events-footer {
	border-color: {$colors['bd_color']};	
}

/* Events day */
.tribe-events-day .tribe-events-day-time-slot h5,
.tribe-events-day .tribe-events-day-time-slot .tribe-events-day-time-slot-heading {
	color: {$colors['extra_dark']};
	background: {$colors['extra_bg_color']};
}



/* Single Event */
.single-tribe_events .tribe-events-venue-map {
	color: {$colors['alter_text']};
	border-color: {$colors['alter_bd_hover']};
}
.single-tribe_events .tribe-events-schedule .tribe-events-cost {
	color: {$colors['text_dark']};
}
.single-tribe_events .type-tribe_events {
	border-color: {$colors['bd_color']};
}

.tribe-events-meta-group .tribe-events-single-section-title {
	color: {$colors['text_dark']};
}



.tribe-bar-submit:before,
.tribe-bar-mini .tribe-bar-submit:before {
    color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
}
.tribe-bar-submit:hover:before,
.tribe-bar-mini .tribe-bar-submit:hover:before {
    color: {$colors['inverse_hover']};
	background-color: {$colors['text_dark']};
}


#tribe-bar-views-toggle {
    color: {$colors['inverse_link']} !important;
	background-color: {$colors['text_link']} !important;
}
#tribe-bar-views .tribe-bar-views-option {
    color: {$colors['inverse_link']} !important;
	background-color: {$colors['text_link2']} !important;
}
#tribe-bar-views-toggle:hover,
#tribe-bar-views .tribe-bar-views-option:hover {
    color: {$colors['inverse_link']} !important;
    background-color: {$colors['text_hover']} !important;
}

#tribe-events-content .tribe-events-calendar td {
    background-color: {$colors['alter_bg_hover']};
}

.tribe-events-calendar td.tribe-events-othermonth.tribe-events-future div[id*="tribe-events-daynum-"],
.tribe-events-calendar td.tribe-events-othermonth.tribe-events-future div[id*="tribe-events-daynum-"] > a,
.tribe-events-calendar td div[id*="tribe-events-daynum-"], 
.tribe-events-calendar td div[id*="tribe-events-daynum-"] > a, 
.tribe-events-calendar td.tribe-events-present div[id*="tribe-events-daynum-"], 
.tribe-events-calendar td.tribe-events-present div[id*="tribe-events-daynum-"] > a, 
.tribe-events-calendar td.tribe-events-past div[id*="tribe-events-daynum-"], 
.tribe-events-calendar td.tribe-events-past div[id*="tribe-events-daynum-"] > a {
    background-color: {$colors['alter_bg_color']};
    color: {$colors['text_dark']};
}
.tribe-events-calendar td.tribe-events-othermonth.tribe-events-future div[id*="tribe-events-daynum-"] > a {
     color: {$colors['text']};
}

.tribe-events-calendar td.tribe-events-past div[id*="tribe-events-daynum-"] {
    color: {$colors['text']};
}

.single-tribe_events #tribe-events-content .tribe-events-event-meta dt,
.single-tribe_events .tribe-events-single .tribe-events-event-meta {
    color: {$colors['text']};
}

.tribe-event-tags {
    color: {$colors['text_link']};
}


#tribe-bar-form.tribe-bar-collapse #tribe-bar-collapse-toggle {
    color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
}
#tribe-bar-form.tribe-bar-collapse #tribe-bar-collapse-toggle:hover {
    color: {$colors['inverse_hover']};
	background-color: {$colors['text_dark']};
}

.datepicker table tr td.active.active:hover, .datepicker table tr td span.active.active:hover {
    color: {$colors['inverse_link']};
}
.datepicker table tr td span.active:hover, .datepicker table tr td span.active:hover.active {
    background-color: {$colors['text_link']};
}
.datepicker .datepicker-switch:hover, .datepicker .next:hover, .datepicker .prev:hover, .datepicker tfoot tr th:hover {
    background-color: {$colors['text_hover']};
}

#tribe-events-content .tribe-events-calendar td.tribe-events-othermonth.mobile-active div[id*=tribe-events-daynum-] {
    color: {$colors['text_dark']};
}
.tribe-events-calendar td.tribe-events-othermonth.mobile-active.mobile-active.tribe-events-has-events:after {
    background-color: {$colors['text_link']};
}

#tribe-bar-form.tribe-bar-collapse .tribe-bar-filters {
    background-color: {$colors['bg_color']};
}

/* New styles for Events Calendsr */

.tribe-common--breakpoint-medium.tribe-events .tribe-events-c-search__button {
	color: {$colors['inverse_link']} !important;
	background-color: {$colors['text_link']} !important;
}
.tribe-common--breakpoint-medium.tribe-events .tribe-events-c-search__button:hover {
	color: {$colors['inverse_link']} !important;
	background-color: {$colors['text_hover']} !important;
}
.tribe-common .tribe-events-header .tribe-events-c-events-bar__views .tribe-events-c-view-selector__list .tribe-events-c-view-selector__list-item a .tribe-events-c-view-selector__list-item-text {
	color: {$colors['inverse_link']} !important;
}
.tribe-common .tribe-events-header .tribe-events-c-events-bar__views .tribe-events-c-view-selector__list .tribe-events-c-view-selector__list-item a .tribe-common-c-svgicon .tribe-common-c-svgicon__svg-fill {
	fill: {$colors['inverse_link']} !important;
}
.tribe-common .tribe-events-header .tribe-events-c-events-bar__views .tribe-events-c-view-selector__list .tribe-events-c-view-selector__list-item a {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_link']};
}
.tribe-common .tribe-events-header .tribe-events-c-events-bar__views .tribe-events-c-view-selector__list .tribe-events-c-view-selector__list-item.tribe-events-c-view-selector__list-item--active a,
.tribe-common .tribe-events-header .tribe-events-c-events-bar__views .tribe-events-c-view-selector__list .tribe-events-c-view-selector__list-item a:hover {
	color: {$colors['inverse_link']};
	background-color: {$colors['text_hover']};
}
.tribe-common .tribe-events-header .tribe-events-header__events-bar .tribe-events-c-events-bar__search-filters-container .tribe-common-form-control-text__input {
	border-color: {$colors['input_bd_color']};
}
/* Top bar */
.tribe-events-c-top-bar .tribe-events-c-top-bar__nav .tribe-events-c-top-bar__nav-list .tribe-events-c-top-bar__nav-list-item .tribe-common-c-btn-icon  {
	color: {$colors['inverse_link']};
	background: {$colors['text_link']};
}
.tribe-events-c-top-bar .tribe-events-c-top-bar__nav .tribe-events-c-top-bar__nav-list .tribe-events-c-top-bar__nav-list-item .tribe-common-c-btn-icon:hover {
	color: {$colors['inverse_link']};
	background: {$colors['text_hover']};
}
.tribe-events .tribe-events-c-subscribe-dropdown .tribe-events-c-subscribe-dropdown__button,
.tribe-common .tribe-events-header .tribe-events-c-top-bar .tribe-events-c-top-bar__datepicker button,
.tribe-common .tribe-events-header .tribe-events-c-top-bar .tribe-events-c-top-bar__today-button {
	color: {$colors['inverse_link']} !important;
	background: {$colors['text_link']} !important;
}
.tribe-events .tribe-events-c-subscribe-dropdown .tribe-events-c-subscribe-dropdown__button:hover,
.tribe-common .tribe-events-header .tribe-events-c-top-bar .tribe-events-c-top-bar__datepicker button:hover,
.tribe-common .tribe-events-header .tribe-events-c-top-bar .tribe-events-c-top-bar__today-button:hover {
	color: {$colors['inverse_link']} !important;
	background: {$colors['text_hover']} !important;
}
.tribe-events .datepicker .day.active,
.tribe-events .datepicker .day.active.focused,
.tribe-events .datepicker .day.active:focus,
.tribe-events .datepicker .day.active:hover,
.tribe-events .datepicker .month.active,
.tribe-events .datepicker .month.active.focused,
.tribe-events .datepicker .month.active:focus,
.tribe-events .datepicker .month.active:hover,
.tribe-events .datepicker .year.active,
.tribe-events .datepicker .year.active.focused,
.tribe-events .datepicker .year.active:focus,
.tribe-events .datepicker .year.active:hover {
	color: {$colors['inverse_link']} !important;
	background: {$colors['text_link']} !important;
}
.tribe-events .datepicker table tr td span.active.active,
.tribe-events .datepicker table tr td span.focused,
.tribe-events .datepicker table tr td span:hover {
	color: {$colors['inverse_link']} !important;
	background: {$colors['text_link']} !important;
}
.tribe-events table.table-condensed thead th {
	color: {$colors['text_dark']} !important;
	background: transparent !important;
}
.tribe-events table.table-condensed thead th:hover {
	color: {$colors['text_link']} !important;
	background: transparent !important;
}
/* Nevigation */
.tribe-events-c-ical .tribe-events-c-ical__link,
.tribe-events-c-nav [class*="tribe-events-c-nav"] li a,
.tribe-events-c-nav [class*="tribe-events-c-nav"] li button:not(:disabled) {
	color: {$colors['inverse_link']} !important;
	background: {$colors['text_link']} !important;
}
.tribe-events-c-ical .tribe-events-c-ical__link:hover,
.tribe-events-c-nav [class*="tribe-events-c-nav"] li a:hover,
.tribe-events-c-nav [class*="tribe-events-c-nav"] li button:not(:disabled):hover {
	color: {$colors['inverse_link']} !important;
	background: {$colors['text_hover']} !important;
}
.tribe-events-calendar-month > [class*="__body"] [class*="__week"] [class*="__day"] [class*="-month__events"] [class*="__multiday-event-wrapper"] [class*="__multiday-event-bar"] [class*="__multiday-event-bar-inner"] {
	background-color: {$colors['text_link']} !important;
}
.tribe-events-calendar-month > [class*="__body"] [class*="__week"] [class*="__day"] [class*="-month__events"] [class*="__multiday-event-wrapper"] [class*="__multiday-event-bar"] [class*="__multiday-event-bar-inner"] h3 {
	color: {$colors['inverse_link']} !important;
}
.tribe-common--breakpoint-medium.tribe-events .tribe-events-calendar-month__day:hover:after {
	background-color: {$colors['text_link']} !important;
}
.tribe-common .tribe-common-anchor-thin:active, .tribe-common .tribe-common-anchor-thin:focus, .tribe-common .tribe-common-anchor-thin:hover {
	border-color: {$colors['text_link']} !important;
}
.tribe-common .tribe-common-anchor-thin:active,
.tribe-common .tribe-common-anchor-thin:focus,
.tribe-common .tribe-common-anchor-thin:hover {
    color: {$colors['text_link']} !important;
}
.tribe-events .tribe-events-calendar-month__day--current .tribe-events-calendar-month__day-date, .tribe-events .tribe-events-calendar-month__day--current .tribe-events-calendar-month__day-date-link {
	color: {$colors['text_link']} !important;
}
.tribe-events-calendar-month > [class*="__header"] [class*="__header-row"] > [class*="__header-column"] {
    color: {$colors['inverse_link']} !important;
    background-color: {$colors['text_link']} !important;
}
.tribe-events .tribe-events-calendar-month__header-column-title {
    color: {$colors['inverse_link']} !important;
}
.tribe-events-calendar-month > [class*="__body"] [class*="__week"] [class*="__day"] h3[class*="__day-date"] {
    background-color: {$colors['alter_bg_color']} !important;
    color: {$colors['text_dark']} !important;
}
.tribe-events .tribe-events-calendar-month__day-cell--selected,
.tribe-events .tribe-events-calendar-month__day-cell--selected:focus,
.tribe-events .tribe-events-calendar-month__day-cell--selected:hover {
    background-color: {$colors['text_link']};
}

.tribe-events .tribe-events-calendar-month__day-cell--selected .tribe-events-calendar-month__day-date-daynum,
.tribe-events .tribe-events-calendar-month__day-cell--selected .tribe-events-calendar-month__day-date-daynum:focus,
.tribe-events .tribe-events-calendar-month__day-cell--selected .tribe-events-calendar-month__day-date-daynum:hover{
    color: {$colors['inverse_link']};
}

.tribe-events .tribe-events-calendar-month__mobile-events-icon--event {
    background-color: {$colors['text_dark']};
}
.tribe-events .tribe-events-c-view-selector__button:before,
.tribe-events .tribe-events-c-events-bar__search-button:before {
    background-color: {$colors['text_link']};
}
.tribe-events-single .tribe-events-sub-nav .tribe-events-nav-next a,
.tribe-events-single .tribe-events-sub-nav .tribe-events-nav-previous a {
	color: {$colors['inverse_link']};
}
.tribe-common .tribe-common-b2,
.tribe-events .tribe-events-calendar-list__event-date-tag-weekday,
 .tribe-events .tribe-events-calendar-month__calendar-event-tooltip-datetime,
.tribe-common .tribe-common-b3,
.tribe-common.tribe-events .tribe-events-calendar-day__event-datetime,
.tribe-events .tribe-events-c-subscribe-dropdown .tribe-events-c-subscribe-dropdown__list-item a {
    color: {$colors['text']};
}

.tribe-events .tribe-events-c-subscribe-dropdown .tribe-events-c-subscribe-dropdown__list-item a:hover {
    color: {$colors['text_link']};
}
.tribe-events .tribe-events-calendar-month__day-date-daynum,
.tribe-common .tribe-common-h5,
.tribe-events .tribe-events-calendar-list__event-title-link,
.tribe-events .tribe-events-calendar-list__event-datetime,
.tribe-common .tribe-common-h7,
 .tribe-events .datepicker .datepicker-switch,
.tribe-events .datepicker .day,
.tribe-events .datepicker .month,
.tribe-events .datepicker .year,
.tribe-events-calendar-month__calendar-event-tooltip-title-link,
.tribe-events-calendar-month__calendar-event-tooltip-title-link:visited,
.tribe-common.tribe-events .tribe-events-calendar-month__day-date-link,
.tribe-common.tribe-events .tribe-events-calendar-list__event-title,
.tribe-common.tribe-events .tribe-events-calendar-day__event-title,
.tribe-common.tribe-events .tribe-events-calendar-day__event-title a{
    color: {$colors['text_dark']};
}

.tribe-events .datepicker .past,
.tribe-events .datepicker .past.day,
.tribe-events .datepicker .past.month,
.tribe-events .datepicker .past.year {
    color: {$colors['text_light']};
}

.tribe-events .tribe-events-c-subscribe-dropdown .tribe-events-c-subscribe-dropdown__list {
    background-color: {$colors['alter_bg_color']};
}

.tribe-events .datepicker .day.focused,
.tribe-events .datepicker .day:focus,
.tribe-events .datepicker .day:hover,
.tribe-events .datepicker .month.focused,
.tribe-events .datepicker .month:focus,
.tribe-events .datepicker .month:hover,
.tribe-events .datepicker .year.focused,
.tribe-events .datepicker .year:focus,
 .tribe-events .datepicker .year:hover {
    background-color: {$colors['text_link']} !important;
    color: {$colors['inverse_link']} !important;
}

.tribe-events-header__messages.tribe-events-c-messages a{
    color: {$colors['text_link']} !important;
}

.tribe-events-header__messages.tribe-events-c-messages a:hover{
    color: {$colors['text_hover']} !important;
}

.tribe-events button.tribe-events-c-top-bar__nav-link--next:disabled,
.tribe-events button.tribe-events-c-top-bar__nav-link--prev:disabled {
	background: {$colors['text_light']} !important;
	color: {$colors['text']} !important;
}

.tribe-common a:not(.tribe-common-anchor--unstyle), .tribe-common a:not(.tribe-common-anchor--unstyle):active, .tribe-common a:not(.tribe-common-anchor--unstyle):focus, .tribe-common a:not(.tribe-common-anchor--unstyle):hover, .tribe-common a:not(.tribe-common-anchor--unstyle):visited {
	color: {$colors['text_dark']};
}

CSS;
		}

		return $css;
	}
}

