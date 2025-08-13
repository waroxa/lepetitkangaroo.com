;(function($, window, document, undefined) {
	var $win = $(window);
	var $doc = $(document);
	var $field_container;

	$doc.ready(function() {

		$(document).on("booked-on-new-app", function(event) {
			$field_container = $('.field.field-paid-service');
			booked_wc_products_field($field_container);
		});

		booked_wc_btn_pay_appointment_shortcode();

		$(document).on("booked-on-requested-appointment", function(event,redirectObj) {
			redirectObj.redirect = booked_wc_redirect_to_checkout_if_product_option();
		});

	});

	function booked_wc_products_field(field_container) {

		var $dropdown = $('select', field_container);

		if ( $dropdown.find( 'option' ).length < 3 ){
			$dropdown.find( 'option:first-child' ).remove();
			var calendar_id = parseInt( $dropdown.data('calendar-id') ),
				product_id = $dropdown.val(),
				field_name = $dropdown.attr('name'),
				$variations_container = $dropdown.parent().find('.paid-variations');
				booked_wc_load_variations(product_id, field_name, calendar_id, $variations_container);
		}

		$dropdown.on('change', function() {
			var $this = $(this),
				calendar_id = parseInt( $this.data('calendar-id') ),
				product_id = $this.val(),
				field_name = $this.attr('name'),
				$variations_container = $this.parent().find('.paid-variations');

			booked_wc_load_variations(product_id, field_name, calendar_id, $variations_container);
		});
	}

	function booked_wc_load_variations( product_id, field_name, calendar_id, variations_container ) {

		if ( !product_id ) {
			variations_container.html('');
			return;
		};

		var data = {
			'action': booked_wc_variables.prefix + 'load_variations',
			'product_id': parseInt(product_id),
			'calendar_id': calendar_id,
			'field_name': field_name
		};

		$.post(
			booked_wc_variables.ajaxurl,
			data,
			function(response) {
				variations_container.html(response);
				resize_booked_modal();
			}
		);
	}


	function booked_wc_btn_pay_appointment_shortcode() {
		$('.booked-profile-appt-list .appt-block .pay').on('click', function(event) {

			event.preventDefault();

			var $button = $(this),
				appt_id = $button.attr('data-appt-id');

			confirm_edit = confirm(booked_wc_variables.i18n_pay);
			if ( confirm_edit===true ){

				var data = {
					'action': booked_wc_variables.prefix + 'add_to_cart',
					'app_id': appt_id
				};

				jQuery.post(booked_wc_variables.ajaxurl, data, function(response) {
					if ( response.status === 'success' ) {
						window.location.href = booked_wc_variables.checkout_page;
					} else {
						alert( response.messages[0] );
					};
				}, 'json');
			}

			return false;
		});
	}

	

	function booked_wc_redirect_to_checkout_if_product_option() {

		var redirect = false,
			$form = $('form#newAppointmentForm');

		$('.field-paid-service', $form).each(function() {
			var $this = $(this);

			$('select', $this).each(function() {
				var $this_select = $(this);

				if ( $this_select.val()!=='' ) {
					redirect = true;
				};
			});
		});

		if ( redirect ) {
			window.location = booked_wc_variables.checkout_page;
			return true;
		}

		return false;

	}


})(jQuery, window, document);
