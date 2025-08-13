<?php if (!Essential_Grid_Db::check_table_exist_and_version()) { ?>
	<div class="esg_info_box esg-notice-table-exists">
		<div class="esg-red esg_info_box_decor"><i class="eg-icon-cancel"></i></div>
		<div class="validation-label">
			<?php esc_html_e('The Essential Grid tables could not be updated/created.', 'essential-grid'); ?><br /><?php esc_html_e('Please check that the database user is able to create and modify tables.', 'essential-grid'); ?>
			<a class="esg-btn esg-green" href="?page=essential-grid&esg_recreate_database=<?php echo esc_attr(wp_create_nonce("Essential_Grid_recreate_db")); ?>"><?php esc_html_e('Create Again', 'essential-grid'); ?></a>
		</div>
	</div>
	<div class="div50"></div>
<?php
}
