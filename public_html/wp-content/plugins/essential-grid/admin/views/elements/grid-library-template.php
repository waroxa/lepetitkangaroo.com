<div class="library_item_import library_item"
	 data-src="<?php echo esc_attr($grid['img'] . "?time=" . time()); ?>"
	 data-zipname="<?php echo esc_attr($grid['zip']); ?>"
	 data-uid="<?php echo esc_attr($grid['uid']); ?>"
	 data-title="<?php echo esc_attr($grid['title']); ?>"
	 data-versionneed="<?php echo esc_attr($grid['required']); ?>"
	 data-addons="<?php echo esc_attr(!empty($grid['addons']) ? wp_json_encode($grid['addons']) : ''); ?>"
>
	<div class="library_thumb_overview"></div>
	<div class="library_preview_add_wrapper">
		<?php if (isset($grid['preview']) && $grid['preview'] !== '') { ?>
			<a class="preview_library_grid" href="<?php echo esc_url($grid['preview']); ?>" target="_blank"><i class="eg-icon-search"></i></a>
		<?php } ?>
		<span class="show_more_library_grid"><i class="eg-icon-plus"></i></span>
	</div>
</div>

<div class="library_thumb_more">
	<span class="ttm_label"><?php echo esc_html($grid['title']); ?></span>
	<?php
	if (isset($grid['description'])) {
		echo wp_kses_post($grid['description']);
	}
	if (!empty($grid['setup_notes'])) { ?>
		<span class="ttm_space"></span>
		<span class="ttm_label"><?php esc_html_e('Setup Notes', 'essential-grid'); ?></span>
		<?php echo wp_kses_post($grid['setup_notes']); ?>
	<?php } ?>
	<span class="ttm_space"></span>
	<span class="ttm_label"><?php esc_html_e('Requirements', 'essential-grid'); ?></span>
	<ul class="ttm_requirements">
		<li><?php
			if (version_compare(ESG_REVISION, $grid['required'], '>=')) {
				?><i class="eg-icon-check"></i><?php
			} else {
				?><i class="eg-icon-cancel"></i><?php
				$allow_install = false;
			}
			esc_html_e('Essential Grid Version', 'essential-grid');
			echo ' ' . esc_html($grid['required']);
			?></li>
	</ul>
	<span class="ttm_space"></span>
	<span class="ttm_label_direct"><?php esc_html_e('Available Version', 'essential-grid'); ?></span>
	<span class="ttm_label_half ttm_available"><?php echo esc_html($grid['version']); ?></span>
	<span class="ttm_space"></span>
	<?php if ($deny == '' && $allow_install) { ?>
		<div class="install_library_grid<?php echo esc_attr($deny); ?>" data-zipname="<?php echo esc_attr($grid['zip']); ?>"
		     data-uid="<?php echo esc_attr($grid['uid']); ?>" data-title="<?php echo esc_attr($grid['title']); ?>">
			<i class="eg-icon-download"></i>
			<?php esc_html_e('Install Grid', 'essential-grid'); ?>
		</div>
	<?php } else { ?>
		<div class="dontadd_library_grid_item"><i class="icon-not-registered"></i><?php esc_html_e('Requirements not met', 'essential-grid'); ?></div>
	<?php } ?>
	<span class="esg-clearfix esg-margin-b-5"></span>
</div>
