(function ($) {
    'use strict';
    $(function () {
        $('.booked-dismiss-notice-forever').on('click', '.notice-dismiss', function (event, el) {
            var $notice = $(this).parent('.notice.is-dismissible');
            var dismiss_action = $notice.attr('data-dismiss-action');
            var dismiss_key = $notice.attr('data-key');
            if (dismiss_action && dismiss_key) {
               $.post(ajaxurl, {'action': dismiss_action, 'key': dismiss_key}, function(response) {
		});
            }
        });
    });
})(jQuery);