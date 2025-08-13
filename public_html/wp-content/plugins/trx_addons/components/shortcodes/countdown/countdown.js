/**
 * Shortcode Countdown
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.4.3
 */

/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */

(function() {

	"use strict";

	jQuery(document).on('action.init_hidden_elements', trx_addons_sc_countdown_init);
	
	// Countdown init
	function trx_addons_sc_countdown_init(e, container) {
	
		if (container === undefined) container = jQuery('body');

		container.find('.sc_countdown:not(.inited)').each(function () {
			jQuery(this).addClass('inited');
	
			var id = jQuery(this).attr('id');
			var curDate = new Date(); 
			var curDateTimeStr = curDate.getFullYear()
									+ '-' + (curDate.getMonth()<9 ? '0' : '') + (curDate.getMonth()+1) 
									+ '-' + (curDate.getDate()<10 ? '0' : '') + curDate.getDate()
									+ ' ' + (curDate.getHours()<10 ? '0' : '') + curDate.getHours()
									+ ':' + (curDate.getMinutes()<10 ? '0' : '') + curDate.getMinutes()
									+ ':' + (curDate.getSeconds()<10 ? '0' : '') + curDate.getSeconds();
			var interval = 1;	//jQuery(this).data('interval');
			var endDateStr = jQuery(this).data('date');
			var endDateParts = endDateStr.split('-');
			var endTimeStr = jQuery(this).data('time');
			var endTimeParts = endTimeStr.split(':');
			if (endTimeParts.length < 3) endTimeParts[2] = '00';
			var endDateTimeStr = endDateStr+' '+endTimeStr;
			if (curDateTimeStr < endDateTimeStr) {
				jQuery(this).find('.sc_countdown_placeholder').countdown({
					until: new Date(endDateParts[0], endDateParts[1]-1, endDateParts[2], endTimeParts[0], endTimeParts[1], endTimeParts[2]), 
					tickInterval: interval,
					onTick: trx_addons_sc_countdown
				}); 
			} else {
				jQuery(this).find('.sc_countdown_placeholder').countdown({
					since: new Date(endDateParts[0], endDateParts[1]-1, endDateParts[2], endTimeParts[0], endTimeParts[1], endTimeParts[2]), 
					tickInterval: interval,
					onTick: trx_addons_sc_countdown
				}); 
			}
		});
	}
	
	
	// Countdown update
	function trx_addons_sc_countdown(dt) {
		var counter = jQuery(this).parent();
		for (var i=3; i < dt.length; i++) {
			var v = (dt[i]<10 ? '0' : '') + dt[i];
			var item = counter.find('.sc_countdown_item').eq(i-3);
			var digits = item.find('.sc_countdown_digits span').addClass('hide');
			for (var ch=v.length-1; ch >= 0; ch--) {
				digits.eq(ch+(i==3 && v.length<3 ? 1 : 0)).removeClass('hide').text(v.substr(ch, 1));
			}
			trx_addons_draw_arc_on_canvas(item, dt[i]);
		}
	}

})();