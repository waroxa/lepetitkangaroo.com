<div class="error below-h2 esg-margin-l-0 esg-dismiss-notice">
	<a href="javascript:void(0);" class="esg-f-right esg-margin-t-5 " id="esg-dismiss-notice-close">
		<span class="esg-addon-dismiss-notice dashicons dashicons-dismiss"></span>
	</a>
	<p>
		<?php esc_attr_e('Please activate your copy of the Essential Grid to receive live updates, premium support and the template library.', 'essential-grid'); ?>
	</p>
</div>
<script type="text/javascript">
jQuery(function(){
	jQuery('#esg-dismiss-notice-close').on('click',function(){
		jQuery.ajax({
			type:'post',
			url:ajaxurl,
			dataType:'json',
			data: {
				action: 'Essential_Grid_request_ajax',
				client_action: 'dismiss_notice',
				token: '<?php echo esc_js(wp_create_nonce('Essential_Grid_actions')); ?>',
				data: ''
			}
		});

		jQuery('.esg-dismiss-notice').hide();
	});
});
</script>
