<?php
/** @var Essential_Grid_Library $this */
?>
<div id="library_bigoverlay"></div>
<?php
if (!empty($tp_grids)) {
	$favorites = new Essential_Grid_Favorite();
	$fav_grids = $favorites->get_favorite_type('grid');
	foreach ($tp_grids as $grid) {
		$isnew = false;
		$classes = ['esg_group_wrappers', 'not-imported-wrapper', 'template_premium'];

		if ($favorites->is_favorite('grid', $grid['id'])) $classes[] = 'esg-lib-favorite-grid';

		if (!empty($grid['filter']) && is_array($grid['filter'])) {
			foreach ($grid['filter'] as $f => $v) {
				if ($v === 'newupdate') {
					$isnew = true;
				}
				$classes[] = $grid['filter'][$f] = 'temp_' . esc_attr($v);
			}
		}
		?>
		<div class="<?php echo esc_attr(implode(' ', $classes)); ?>"
		     data-date="<?php echo esc_attr($grid['id']); ?>"
		     data-title="<?php echo esc_attr(Essential_Grid_Base::sanitize_utf8_to_unicode($grid['title'])); ?>">
			<?php if ($isnew) { ?>
				<span class="library_new"><?php esc_html_e("New", 'essential-grid'); ?></span>
			<?php } ?>
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function output already escaped html markup
			echo $this->write_import_template_markup($grid); 
			?>
			<div class="library_thumb_title">
				<?php echo esc_html($grid['title']); ?>
				<a href="javascript:void(0)" data-id="<?php echo esc_attr($grid['id']); ?>"><i class="material-icons">star</i></a>
			</div>
		</div>
		<?php
	}
} else {
	echo '<span class="esg_library_notice">';
	esc_html_e('No data could be retrieved from the servers. Please make sure that your website can connect to the themepunch servers.', 'essential-grid');
	echo '</span>';
}
?>
<div class="esg-clearfix"></div>
