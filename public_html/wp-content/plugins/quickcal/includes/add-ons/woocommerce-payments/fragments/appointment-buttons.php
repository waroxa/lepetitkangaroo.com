<?php

if ( !isset($appointment_id) || !is_numeric($appointment_id) ) {
	return;
}

$appointment = QuickCal_WC_Appointment::get($appointment_id);
$awaiting_status = QUICKCAL_WC_PLUGIN_PREFIX . 'awaiting';

// add buttons only on appointments with products
if ( !$appointment->products ) {
	return;
}

$current_time = current_time('timestamp');

// check if the date has been passed
// if so, hide the edit button
if ( $current_time > $appointment->timestamp ) {
	return;
}


if ( !$appointment->is_paid && $appointment->payment_status == 'awaiting_checkout' ): ?>
	<a href="#" data-appt-id="<?php echo $appointment_id ?>" class="pay"><?php _e('Pay', 'booked'); ?></a>
<?php endif ?>
