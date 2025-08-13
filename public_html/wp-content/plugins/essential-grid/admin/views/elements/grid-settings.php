<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

if(!isset($base)) $base = new Essential_Grid_Base();
$eg_meta = new Essential_Grid_Meta();
$f_meta = $eg_meta->get_all_meta_handle_name('meta-');

$custom_filter = $base->getVar($grid, ['params', 'custom-filter'], []);
if (!is_array($custom_filter) && !is_object($custom_filter)) $custom_filter = explode(',', $custom_filter);

// INIT LIGHTBOX SOURCE ORDERS
if (intval($isCreate) > 0) {//currently editing, so default can be empty
	$lb_source_order = $base->getVar($grid, ['params', 'lb-source-order']);
	$lb_button_order = $base->getVar($grid, ['params', 'lb-button-order'], []);
} else {
	$lb_source_order = $base->getVar($grid, ['params', 'lb-source-order'], ['featured-image']);
	$lb_button_order = $base->getVar($grid, ['params', 'lb-button-order'], ['share', 'thumbs', 'close']);
}

$lb_source_list = $base->get_lb_source_order();
$lb_button_list = $base->get_lb_button_order();

// INIT AJAX SOURCE ORDERS
if (intval($isCreate) > 0) //currently editing, so default can be empty
	$aj_source_order = $base->getVar($grid, ['params', 'aj-source-order']);
else
	$aj_source_order = $base->getVar($grid, ['params', 'aj-source-order'], ['post-content']);

$aj_source_list = $base->get_aj_source_order();

$all_metas = $eg_meta->get_all_meta();
?>
<!-- SETTINGS -->
<form id="eg-form-create-settings">
	<!-- GRID SETTINGS -->
	<div id="esg-settings-grid-settings" class="esg-settings-container">
	<div>
		<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Layout', 'essential-grid'); ?></span></esg-llabel></div>
		<div class="eg-cs-tbc">
			<label for="navigation-container" class="eg-tooltip-wrap" title="<?php esc_attr_e('Choose layout type of the grid', 'essential-grid'); ?>"><?php esc_html_e("Layout", 'essential-grid'); ?></label><!--
			--><input type="radio" name="layout-sizing" value="boxed" <?php checked($base->getVar($grid, ['params', 'layout-sizing'], 'boxed'), 'boxed'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Grid always stays within the wrapping container', 'essential-grid'); ?>"><?php esc_html_e("Boxed", 'essential-grid'); ?></span><div class="space18"></div><!--
			--><input type="radio" name="layout-sizing" value="fullwidth" <?php checked($base->getVar($grid, ['params', 'layout-sizing'], 'boxed'), 'fullwidth'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Force Fullwidth. Grid will fill complete width of the window', 'essential-grid'); ?>"><?php esc_html_e("Fullwidth", 'essential-grid'); ?></span><div class="space18"></div><!--
			--><input type="radio" name="layout-sizing" value="fullscreen" <?php checked($base->getVar($grid, ['params', 'layout-sizing'], 'boxed'), 'fullscreen'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Fullscreen Layout. !! Hides not needed options !! Grid Width = Window Width, Grid Height = Window Height - Offset Containers.', 'essential-grid'); ?>"><?php esc_html_e("Fullscreen", 'essential-grid'); ?></span>
			<div id="eg-fullscreen-container-wrap" class="esg-display-none" >
				<div class="div13"></div>
				<label for="fullscreen-offset-container"><?php esc_html_e('Offset Container', 'essential-grid'); ?></label><!--
				--><input type="text" name="fullscreen-offset-container" value="<?php echo esc_attr($base->getVar($grid, ['params', 'fullscreen-offset-container'], '')); ?>" />
			</div>
			<div id="eg-even-masonry-wrap">
				<div class="div13"></div>
				<label for="layout" class="eg-tooltip-wrap" title="<?php esc_attr_e('Select Grid Layout', 'essential-grid'); ?>"><?php esc_html_e('Grid Layout', 'essential-grid'); ?></label><!--
				--><div id="eg-grid-layout-wrapper" class="esg-display-inline-block"><!--
				--><input type="radio" name="layout" value="even" <?php checked($base->getVar($grid, ['params', 'layout'], 'even'), 'even'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Even - Each item has same height. Width and height are item ratio dependent', 'essential-grid'); ?>"><?php esc_html_e('Even', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="layout" value="masonry" <?php checked($base->getVar($grid, ['params', 'layout'], 'even'), 'masonry'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Individual item height depends on media height and content height', 'essential-grid'); ?>"><?php esc_html_e('Masonry', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="layout" value="cobbles" <?php checked($base->getVar($grid, ['params', 'layout'], 'even'), 'cobbles'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Even Grid with Width / Height Multiplications', 'essential-grid'); ?>"><?php esc_html_e('Cobbles', 'essential-grid'); ?></span>           
				</div>
			</div>
			
			<div id="eg-content-push-wrap">
				<div class="div13"></div>
				<label for="columns" class="eg-tooltip-wrap" title="<?php esc_attr_e('Content Push', 'essential-grid'); ?>"><?php esc_html_e('Content Push', 'essential-grid'); ?></label><!--
				--><input type="radio" name="content-push" value="on" <?php checked($base->getVar($grid, ['params', 'content-push'], 'off'), 'on'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Content will push the website down on Even Grids with content in the Masonry Content area for the last row', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="content-push" value="off" <?php checked($base->getVar($grid, ['params', 'content-push'], 'off'), 'off'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Content will overflow elements on Even Grids with content in the Masonry Content area for the last row', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
			</div>
			<div id="eg-items-ratio-wrap">
				<div class="div13"></div>
				<label for="x-ratio" class="eg-tooltip-wrap" title="<?php esc_attr_e('Media width/height ratio, Width ratio of Media:Height ratio of Media', 'essential-grid'); ?>"><?php esc_html_e('Media Ratio X:Y', 'essential-grid'); ?></label><!--
				--><div id="eg-ratio-wrapper" <?php echo ($base->getVar($grid, ['params', 'auto-ratio'], 'true') === 'true' && $base->getVar($grid, ['params', 'layout'], 'even') === 'masonry') ? 'class="esg-display-none"' : ''; ?>><input class="input-settings-small " type="text" name="x-ratio" value="<?php echo esc_attr($base->getVar($grid, ['params', 'x-ratio'], '4', 'i')); ?>" />&nbsp;:&nbsp;<input class="input-settings-small " type="text" name="y-ratio" value="<?php echo esc_attr($base->getVar($grid, ['params', 'y-ratio'], '3', 'i')); ?>" /><div class="space18"></div></div><!--
				--><span id="eg-masonry-options"><input type="checkbox" name="auto-ratio" <?php checked($base->getVar($grid, ['params', 'auto-ratio'], 'true'), 'true'); ?> /> <?php esc_html_e('Auto', 'essential-grid'); ?></span>
			</div>
			<div class="div13"></div>
			<div>
				<label for="rtl" class="eg-tooltip-wrap" title="<?php esc_attr_e('Right To Left option. This will change the direction of the Grid Items from right to left instead of left to right', 'essential-grid'); ?>"><?php esc_html_e('RTL', 'essential-grid'); ?></label><!--
				--><input type="radio" name="rtl" value="on" <?php checked($base->getVar($grid, ['params', 'rtl'], 'off'), 'on'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Grid Items will be sorted and ordered from right to left', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="rtl" value="off" <?php checked($base->getVar($grid, ['params', 'rtl'], 'off'), 'off'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Grid Items will be sorted and ordered from left to right', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
			</div>
		</div>
	</div>

	<div id="eg-cobbles-options">
		<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Cobbles', 'essential-grid'); ?></span></esg-llabel></div>
		<div class="eg-cs-tbc">
			<div class="cobbles-mobile-wrap">
				<label for="load-more" class="eg-tooltip-wrap" title="<?php esc_attr_e('Show Even under Device Size', 'essential-grid'); ?>"><?php esc_html_e('Even Layout on Device', 'essential-grid'); ?></label><!--
			--><select name="show-even-on-device" >
					<option value="0"<?php selected($base->getVar($grid, ['params', 'show-even-on-device'], '0'), '0'); ?>><?php esc_html_e('Keep Cobbles Always', 'essential-grid'); ?></option>
					<option value="2"<?php selected($base->getVar($grid, ['params', 'show-even-on-device'], '0'), '2'); ?>><?php esc_html_e('Desktop Small and smaller', 'essential-grid'); ?></option>
					<option value="3"<?php selected($base->getVar($grid, ['params', 'show-even-on-device'], '0'), '3'); ?>><?php esc_html_e('Tablet Landscape and smaller', 'essential-grid'); ?></option>
					<option value="4"<?php selected($base->getVar($grid, ['params', 'show-even-on-device'], '0'), '4'); ?>><?php esc_html_e('Tablet and smaller', 'essential-grid'); ?></option>
					<option value="5"<?php selected($base->getVar($grid, ['params', 'show-even-on-device'], '0'), '5'); ?>><?php esc_html_e('Mobile Landscape and smaller', 'essential-grid'); ?></option>
					<option value="6"<?php selected($base->getVar($grid, ['params', 'show-even-on-device'], '0'), '6'); ?>><?php esc_html_e('Mobile', 'essential-grid'); ?></option>
				</select>
			</div>
			<div class="div13"></div>
			<label for="use-cobbles-pattern" class="eg-tooltip-wrap" title="<?php esc_attr_e('Use cobbles pattern and overwrite the cobbles that is set sepcifically in the entries', 'essential-grid'); ?>"><?php esc_html_e('Use Cobbles Pattern', 'essential-grid'); ?></label><!--
		--><input type="radio" name="use-cobbles-pattern" value="on" <?php checked($base->getVar($grid, ['params', 'use-cobbles-pattern'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('User cobbles pattern', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
		--><input type="radio" name="use-cobbles-pattern" value="off" <?php checked($base->getVar($grid, ['params', 'use-cobbles-pattern'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('User specific set cobbles setting from entries', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
			<div class="eg-cobbles-pattern-wrap<?php echo ($base->getVar($grid, ['params', 'use-cobbles-pattern'], 'off') == 'off') ? ' esg-display-none' : ''; ?>">
				<?php
				$cobbles_pattern = $base->getVar($grid, ['params', 'cobbles-pattern'], []);
				if (!empty($cobbles_pattern)) {
					$cob_sort_count = 0;
					foreach ($cobbles_pattern as $pattern) {
						$cob_sort_count++;
						?>
						<div class="eg-cobbles-drop-wrap">
							<span class="cob-sort-order"><?php echo esc_html($cob_sort_count); ?>.</span>
							<select name="cobbles-pattern[]">
								<option <?php selected($pattern, '1x1'); ?> value="1x1">1:1</option>
								<option <?php selected($pattern, '1x2'); ?> value="1x2">1:2</option>
								<option <?php selected($pattern, '1x3'); ?> value="1x3">1:3</option>
								<option <?php selected($pattern, '1x4'); ?> value="1x4">1:4</option>
								<option <?php selected($pattern, '2x1'); ?> value="2x1">2:1</option>
								<option <?php selected($pattern, '2x2'); ?> value="2x2">2:2</option>
								<option <?php selected($pattern, '2x3'); ?> value="2x3">2:3</option>
								<option <?php selected($pattern, '2x4'); ?> value="2x4">2:4</option>
								<option <?php selected($pattern, '3x1'); ?> value="3x1">3:1</option>
								<option <?php selected($pattern, '3x2'); ?> value="3x2">3:2</option>
								<option <?php selected($pattern, '3x3'); ?> value="3x3">3:3</option>
								<option <?php selected($pattern, '3x4'); ?> value="3x4">3:4</option>
								<option <?php selected($pattern, '4x1'); ?> value="4x1">4:1</option>
								<option <?php selected($pattern, '4x2'); ?> value="4x2">4:2</option>
								<option <?php selected($pattern, '4x3'); ?> value="4x3">4:3</option>
								<option <?php selected($pattern, '4x4'); ?> value="4x4">4:4</option>
							</select><a class="esg-btn  eg-delete-cobbles" href="javascript:void(0);"><i class="eg-icon-trash"></i></a>
						</div>
						<?php
					}
				}
				?>
			</div>
			<div>
				<a class="esg-btn esg-purple eg-add-new-cobbles-pattern eg-tooltip-wrap<?php echo ($base->getVar($grid, ['params', 'use-cobbles-pattern'], 'off') == 'off') ? ' esg-display-none' : ''; ?>" title="<?php esc_attr_e('Add your custom cobbles pattern here', 'essential-grid'); ?>" href="javascript:void(0);"><i class="eg-icon-plus"></i><?php esc_html_e("Cobbles Pattern", 'essential-grid'); ?></a>
				<a class="esg-refresh-preview-button eg-refresh-cobbles-pattern esg-btn esg-blue<?php echo ($base->getVar($grid, ['params', 'use-cobbles-pattern'], 'off') == 'off') ? ' esg-display-none' : ''; ?>"><i class="eg-icon-arrows-ccw"></i><?php esc_html_e('Refresh Preview', 'essential-grid'); ?></a>
			</div>
		</div>
	</div>

	<div id="media-playbackingrid-wrap">
		<div>
			<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Media Playback', 'essential-grid'); ?></span></esg-llabel></div>
			<div class="eg-cs-tbc">             
				<label for="videoplaybackingrid" class="eg-tooltip-wrap" title="<?php esc_attr_e('Play Video directly in Grid', 'essential-grid'); ?>"><?php esc_html_e('Video Playback in Grid', 'essential-grid'); ?></label><!--
				--><input type="radio" name="videoplaybackingrid" value="on" <?php checked($base->getVar($grid, ['params', 'videoplaybackingrid'], 'on'), 'on'); ?>><!--
				--><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Allow Video Playback directly in Grid', 'essential-grid'); ?>"><?php esc_html_e('Enable', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio"  name="videoplaybackingrid" value="off" <?php checked($base->getVar($grid, ['params', 'videoplaybackingrid'], 'on'), 'off'); ?>><!--
				--><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Disable Video Playback directly in Grid', 'essential-grid'); ?>"><?php esc_html_e('Disable', 'essential-grid'); ?></span>
				
				<div class="esg-videoplaybackingrid-wrapper esg-display-none">
					<div class="div13"></div>
					<label for="videoloopingrid" class="eg-tooltip-wrap" title="<?php esc_attr_e('Loop Video directly in Grid', 'essential-grid'); ?>"><?php esc_html_e('Loop Video in Grid', 'essential-grid'); ?></label><!--
					--><input type="radio" name="videoloopingrid" value="on" <?php checked($base->getVar($grid, ['params', 'videoloopingrid'], 'off'), 'on'); ?>><!--
					--><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Allow Loop Video in Grid', 'essential-grid'); ?>"><?php esc_html_e('Enable', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio"  name="videoloopingrid" value="off" <?php checked($base->getVar($grid, ['params', 'videoloopingrid'], 'off'), 'off'); ?>><!--
					--><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Disable Loop Video in Grid', 'essential-grid'); ?>"><?php esc_html_e('Disable', 'essential-grid'); ?></span>
					<div><label></label><?php esc_html_e('Looped video(s) will be muted to allow autoplay.', 'essential-grid'); ?></div>
					
					<div class="div13"></div>
					<label for="videoplaybackonhover" class="eg-tooltip-wrap" title="<?php esc_attr_e('Video Playback directly in Grid on Hover', 'essential-grid'); ?>"><?php esc_html_e('Video Playback on Hover', 'essential-grid'); ?></label><!--
					--><input type="radio" name="videoplaybackonhover" value="on" <?php checked($base->getVar($grid, ['params', 'videoplaybackonhover'], 'off'), 'on'); ?>><!--
					--><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Allow Video Playback on hover', 'essential-grid'); ?>"><?php esc_html_e('Enable', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio"  name="videoplaybackonhover" value="off" <?php checked($base->getVar($grid, ['params', 'videoplaybackonhover'], 'off'), 'off'); ?>><!--
					--><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Disable Video Playback on Hover', 'essential-grid'); ?>"><?php esc_html_e('Disable', 'essential-grid'); ?></span> 
		
					<div class="div13"></div>
					<label for="videocontrolsinline" class="eg-tooltip-wrap" title="<?php esc_attr_e('Use / Hide Controls if possible', 'essential-grid'); ?>"><?php esc_html_e('Use Controls Inline', 'essential-grid'); ?></label><!--
					--><input type="radio" name="videocontrolsinline" value="on" <?php checked($base->getVar($grid, ['params', 'videocontrolsinline'], 'off'), 'on'); ?>><!--
					--><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Show Inline Controls', 'essential-grid'); ?>"><?php esc_html_e('Enable', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio"  name="videocontrolsinline" value="off" <?php checked($base->getVar($grid, ['params', 'videocontrolsinline'], 'off'), 'off'); ?>><!--
					--><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Hide Inline Controls', 'essential-grid'); ?>"><?php esc_html_e('Disable', 'essential-grid'); ?></span> 
		
					<div class="div13"></div>
					<label for="videomuteinline" class="eg-tooltip-wrap" title="<?php esc_attr_e('Mute / Unmute Video Inline', 'essential-grid'); ?>"><?php esc_html_e('Mute Inline Video', 'essential-grid'); ?></label><!--
					--><input type="radio" name="videomuteinline" value="on" <?php checked($base->getVar($grid, ['params', 'videomuteinline'], 'on'), 'on'); ?>><!--
					--><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Mute Video Inline', 'essential-grid'); ?>"><?php esc_html_e('Enable', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio"  name="videomuteinline" value="off" <?php checked($base->getVar($grid, ['params', 'videomuteinline'], 'on'), 'off'); ?>><!--
					--><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Unmute Video inline', 'essential-grid'); ?>"><?php esc_html_e('Disable', 'essential-grid'); ?></span> 
		
					<div class="div13"></div>
					<label for="keeplayersovermedia" class="eg-tooltip-wrap" title="<?php esc_attr_e('Keep the Layers over the Video', 'essential-grid'); ?>"><?php esc_html_e('Keep Layers on Playback', 'essential-grid'); ?></label><!--
					--><input type="radio" name="keeplayersovermedia" value="on" <?php checked($base->getVar($grid, ['params', 'keeplayersovermedia'], 'off'), 'on'); ?>><!--
					--><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Allow Video Playback on hover', 'essential-grid'); ?>"><?php esc_html_e('Enable', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio"  name="keeplayersovermedia" value="off" <?php checked($base->getVar($grid, ['params', 'keeplayersovermedia'], 'off'), 'off'); ?>><!--
					--><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Disable Video Playback on Hover', 'essential-grid'); ?>"><?php esc_html_e('Disable', 'essential-grid'); ?></span> 
					<div><label></label><?php esc_html_e('Layers and/or the cover element may overlay the full video. In that case you need to adjust the skin to make sure the video is visible under the overlaying layers/cover.', 'essential-grid'); ?></div>
				</div>
			</div>
		</div>
	</div>
	
	<div>
		<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Columns', 'essential-grid'); ?></span></esg-llabel></div>
		<div class="eg-cs-tbc">
			<table id="grid-columns-table"><!--
			--><tr id="eg-col-0"><!--
				--><td class="eg-tooltip-wrap" title="<?php esc_attr_e('Display normal settings or get advanced', 'essential-grid'); ?>"><label for="columns"><?php esc_html_e('Setting Mode', 'essential-grid'); ?></label></td>
				<td><input type="radio" name="columns-advanced" value="on" <?php checked($base->getVar($grid, ['params', 'columns-advanced'], 'off'), 'on'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Advanced min heights, columns, custom media queries and custom columns (in rows pattern)', 'essential-grid'); ?>"><?php esc_html_e('Advanced', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="columns-advanced" value="off" <?php checked($base->getVar($grid, ['params', 'columns-advanced'], 'off'), 'off'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Simple columns. Each row with same column, default media query levels.', 'essential-grid'); ?>"><?php esc_html_e('Simple', 'essential-grid'); ?></span>
				</td>
				<td></td>
			</tr>

			<tr id="eg-col-00" class="columns-height columns-width">
				<td></td>
				<td class="esg-adv-row-title">
					<span class="esg-adv-row-title-span"><?php
					printf(
						/* translators: 1:br tag */
						esc_html__('Min Height of%1$sGrid at Start', 'essential-grid'),
						'<br>'
					);
					?></span>
					<span class="esg-adv-row-title-span"><?php
					printf(
						/* translators: 1:br tag */
						esc_html__('Breakpoint at%1$sGrid Width', 'essential-grid'),
						'<br>'
					);
					?></span>
					<span class="esg-adv-row-title-span"><?php
					printf(
						/* translators: 1:br tag */
						esc_html__('Min Masonry%1$sContent Height', 'essential-grid'),
						'<br>'
					);
					?></span>
				</td>
				<?php
				$ca_steps = 0;
				if (!empty($columns_advanced[0])) {
					foreach ($columns_advanced[0] as $col)
						if(!empty($col)) $ca_steps = count($columns_advanced) + 1;
				}
				?>
				<td class="columns-adv-first esg-adv-row-title-span-small">
				<?php esc_html_e('Rows:', 'essential-grid'); ?><br><?php
				if ($ca_steps > 0) {
					echo 1; echo ',';
					echo esc_html(1 + 1 * $ca_steps); echo ',';
					echo esc_html(1 + 2 * $ca_steps);
				} else {
				?>
					<div class="esg-adv-row-actions">
						<a class="esg-btn esg-purple" href="javascript:void(0);" id="eg-add-column-advanced"><i class="material-icons">add</i></a>
					</div>
				<?php
				}
				?>
				</td>
				<?php
					if (!empty($columns_advanced)) {
					foreach ($columns_advanced as $adv_key => $adv) {
						if(empty($adv)) continue;
						?>
						<td class="columns-adv-<?php echo esc_attr($adv_key); ?> columns-adv-rows columns-adv-head esg-adv-row-title-span-small">
						<?php esc_html_e('Rows:', 'essential-grid'); ?><br><?php
						$at = $adv_key + 2;
						echo esc_html($at); echo ',';
						echo esc_html($at + 1 * $ca_steps); echo ',';
						echo esc_html($at + 2 * $ca_steps);
						if($ca_steps == $adv_key + 1){
							?>
							<div class="esg-adv-row-actions">
								<a class="esg-btn esg-purple" href="javascript:void(0);" id="eg-add-column-advanced"><i class="material-icons">add</i></a>
								<a class="esg-btn esg-red" href="javascript:void(0);" id="eg-remove-column-advanced"><i class="material-icons">remove</i></a>
							</div>
							<?php
						}
						?>
						</td>
						<?php
					}
				}
				?>
			</tr>
			<?php
			$devices = $base->get_basic_devices();
			foreach ($devices as $k => $v) {
				$title = [];
				$title['label'] = esc_attr(sprintf(
					/* translators: %s: device name */
					__( 'Items per Row for %s.', 'essential-grid' ),
					$v['plural']
				));
				$title['height'] = esc_attr(sprintf(
					/* translators: %s: device name */
					__( 'Start height for Grid on %s.', 'essential-grid' ),
					$v['plural']
				));
				$title['width'] = esc_attr(sprintf(
					/* translators: %s: device name */
					__( 'Min. browser width for %s.', 'essential-grid' ),
					$v['plural']
				));
				$title['columns'] = esc_attr(sprintf(
					/* translators: %s: device name */
					__( 'Number of items in rows on %s.', 'essential-grid' ),
					$v['plural']
				));
				
				?>
				<tr id="eg-col-<?php echo esc_attr($k+1); ?>">
					<td class="eg-tooltip-wrap" title="<?php echo esc_attr($title['label']); ?>"><label><?php echo esc_html($v['label']); ?></label></td>
					<td><!--
					--><input class="input-settings-small grid-columns-input columns-height eg-tooltip-wrap" title="<?php echo esc_attr($title['height']); ?>" type="text" name="columns-height[]" value="<?php echo esc_attr($columns_height[$k]); ?>"><!--
					--><input class="input-settings-small grid-columns-input columns-width eg-tooltip-wrap" title="<?php echo esc_attr($title['width']); ?>" type="text" name="columns-width[]" value="<?php echo esc_attr($columns_width[$k]); ?>"><!--
					--><input class="input-settings-small grid-columns-input columns-width" type="text" name="mascontent-height[]" value="<?php echo esc_attr($mascontent_height[$k]); ?>"><!--
					--><span id="slider-columns-<?php echo esc_attr($k+1); ?>" data-num="<?php echo esc_attr($k+1); ?>" class="slider-settings columns-sliding"></span>
					</td>
					<td class="eg-tooltip-wrap esg-adv-row-title-span-small" title="<?php echo esc_attr($title['columns']); ?>"><input class="input-settings-small" type="text" id="columns-<?php echo esc_attr($k+1); ?>" name="columns[]" value="<?php echo esc_attr($columns[$k]); ?>" /></td>
					<?php
					if (!empty($columns_advanced)) {
						foreach ($columns_advanced as $adv_key => $adv) {
							if(empty($adv)) continue;
							?>
							<td class="esg-adv-row-title-span-small columns-adv-<?php echo esc_attr($adv_key); ?> columns-adv-rows eg-tooltip-wrap" title="<?php echo esc_attr($title['columns']); ?>"><input class="input-settings-small" type="text" name="columns-advanced-rows-<?php echo esc_attr($adv_key); ?>[]" value="<?php echo esc_attr($adv[$k]); ?>" /></td>
							<?php
						}
					}
					?>
				</tr>
				<?php
			}
			?>
			</table>
		</div>
	</div>
		
	<div class=" eg-blankitem-hideable">
		<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Blank Items', 'essential-grid'); ?></span></esg-llabel></div>
		<div class="eg-cs-tbc">         
			<label for="sorting-order-type" class="eg-tooltip-wrap" title="<?php esc_attr_e('Hide Blank Items at a certain break-point', 'essential-grid'); ?>"><?php esc_html_e('Hide Blank Items At', 'essential-grid'); ?></label><!--
			--><?php $blank_breakpoint = $base->getVar($grid, ['params', 'blank-item-breakpoint'], 'none'); ?><select class="eg-tooltip-wrap" name="blank-item-breakpoint" title="<?php esc_attr_e('Hide Blank Items at a certain break-point', 'essential-grid'); ?>">
				<option value="1"<?php selected($blank_breakpoint, '1'); ?>><?php esc_html_e('Desktop Medium', 'essential-grid'); ?></option>
				<option value="2"<?php selected($blank_breakpoint, '2'); ?>><?php esc_html_e('Desktop Small', 'essential-grid'); ?></option>
				<option value="3"<?php selected($blank_breakpoint, '3'); ?>><?php esc_html_e('Tablet Landscape', 'essential-grid'); ?></option>
				<option value="4"<?php selected($blank_breakpoint, '4'); ?>><?php esc_html_e('Tablet', 'essential-grid'); ?></option>
				<option value="5"<?php selected($blank_breakpoint, '5'); ?>><?php esc_html_e('Mobile Landscape', 'essential-grid'); ?></option>
				<option value="6"<?php selected($blank_breakpoint, '6'); ?>><?php esc_html_e('Mobile', 'essential-grid'); ?></option>
				<option value="none"<?php selected($blank_breakpoint, 'none'); ?>><?php esc_html_e('Always Show Blank Items', 'essential-grid'); ?></option>
			</select>
		</div>
	</div>

	<div>
		<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Pagination', 'essential-grid'); ?></span></esg-llabel></div>
		<div class="eg-cs-tbc">
			<div id="eg-pagination-wrap">
				<label for="rows-unlimited"><?php esc_html_e('Pagination', 'essential-grid'); ?></label><!--
				--><input type="radio" name="rows-unlimited" value="on" <?php checked($base->getVar($grid, ['params', 'rows-unlimited'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Pagination deactivated. Load More Option is available.', 'essential-grid'); ?>"><?php esc_html_e('Disable (Load More Available)', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="rows-unlimited" value="off" <?php checked($base->getVar($grid, ['params', 'rows-unlimited'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Pagination Activated. Load More Option is disabled. Dont Forget to add The Navigation Module "Pagination" to your Grid !', 'essential-grid'); ?>"><?php esc_html_e('Enable', 'essential-grid'); ?></span>
			</div>
			<div class="rows-num-wrap<?php echo ($base->getVar($grid, ['params', 'rows-unlimited'], 'off') == 'on') ? ' esg-display-none' : ''; ?>">           
				<div class="div13"></div>
				<label for="rows" class="eg-tooltip-wrap" title="<?php esc_attr_e('Amount of Rows shown (max) when Pagination Activated.', 'essential-grid'); ?>"><?php esc_html_e('Max Visible Rows', 'essential-grid'); ?></label><!--
				--><span id="slider-rows" class="slider-settings"></span><div class="space18"></div><input class="input-settings-small" type="text" name="rows" value="<?php echo esc_attr($base->getVar($grid, ['params', 'rows'], '3', 'i')); ?>" />
				<div class="div13"></div>           
				<label for="enable-rows-mobile" class="eg-tooltip-wrap" title="<?php esc_attr_e('Set a custom rows amount for mobile devices', 'essential-grid'); ?>"><?php esc_html_e('Max Rows Mobile', 'essential-grid'); ?></label><!--
				--><input type="radio" class=" enable-mobile-rows" name="enable-rows-mobile" value="on" <?php checked($base->getVar($grid, ['params', 'enable-rows-mobile'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Enable custom rows amount for mobile devices', 'essential-grid'); ?>"><?php esc_html_e('Enable', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" class="enable-mobile-rows" name="enable-rows-mobile" value="off" <?php checked($base->getVar($grid, ['params', 'enable-rows-mobile'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Disable custom rows amount for mobile devices', 'essential-grid'); ?>"><?php esc_html_e('Disable', 'essential-grid'); ?></span>           
				<?php $enable_mobile_rows = $base->getVar($grid, ['params', 'enable-rows-mobile'], 'off') === 'off' ? 'none' : 'block'; ?>
				<div id="rows-mobile-wrap" class="esg-display-<?php echo esc_attr($enable_mobile_rows); ?>">
					<div class="div13"></div>
					<label for="rows-mobile" class="eg-tooltip-wrap" title="<?php esc_attr_e('Set a custom rows amount for mobile devices.', 'essential-grid'); ?>"><?php esc_html_e('Max Visible Rows Mobile', 'essential-grid'); ?></label><span id="slider-rows-mobile" class="slider-settings"></span><div class="space18"></div><!--
					--><input class="input-settings-small" type="text" name="rows-mobile" value="<?php echo esc_attr($base->getVar($grid, ['params', 'rows-mobile'], '3', 'i')); ?>" />
				</div>
				<div class="div13"></div>
				<label for="pagination-autoplay" class="eg-tooltip-wrap" title="<?php esc_attr_e('Enable/Disable Autoplay for Pagination', 'essential-grid'); ?>"><?php esc_html_e('Autoplay', 'essential-grid'); ?></label><!--
				--><input type="radio" class="pagination-autoplay" name="pagination-autoplay" value="on" <?php checked($base->getVar($grid, ['params', 'pagination-autoplay'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Enable Autoplay for Pagination', 'essential-grid'); ?>"><?php esc_html_e('Enable', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" class="pagination-autoplay" name="pagination-autoplay" value="off" <?php checked($base->getVar($grid, ['params', 'pagination-autoplay'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Disable Autoplay for Pagination', 'essential-grid'); ?>"><?php esc_html_e('Disable', 'essential-grid'); ?></span>
				<div class="pagination-autoplay-notice">
					<label></label>
					<?php esc_html_e('Autoplay allowed only if Pagination or Navigation Arrows control located inside grid. It can be adjusted in ', 'essential-grid'); ?>
					<a id="esg-filterandco-trigger" href="#"><?php esc_html_e('Nav-Filter-Sort', 'essential-grid'); ?></a>
				</div>
				
				<div id="pagination-autoplay-speed"<?php echo ($base->getVar($grid, ['params', 'pagination-autoplay'], 'off') == 'off') ? ' class="esg-display-none" ' : ''; ?>>
					<div class="div13"></div>
					<label for="pagination-autoplay-speed" class="eg-tooltip-wrap" title="<?php esc_attr_e('Timing in milliseconds for the Pagination autoplay', 'essential-grid'); ?>"><?php esc_html_e('Autoplay Timing', 'essential-grid'); ?></label><!--
					--><input class="input-settings-small " type="text" name="pagination-autoplay-speed" value="<?php echo esc_attr($base->getVar($grid, ['params', 'pagination-autoplay-speed'], '5000', 'i')); ?>" /> ms
				</div>
				<div class="div13"></div>
				<label for="pagination-touchswipe" class="eg-tooltip-wrap" title="<?php esc_attr_e('Allow pagination swipe on mobile', 'essential-grid'); ?>"><?php esc_html_e('Touch Swipe', 'essential-grid'); ?></label><!--
				--><input type="radio" class="pagination-touchswipe " name="pagination-touchswipe" value="on" <?php checked($base->getVar($grid, ['params', 'pagination-touchswipe'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Enable TouchSwipe for Pagination', 'essential-grid'); ?>"><?php esc_html_e('Enable', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" class="pagination-touchswipe" name="pagination-touchswipe" value="off" <?php checked($base->getVar($grid, ['params', 'pagination-touchswipe'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Disable TouchSwipe for Pagination', 'essential-grid'); ?>"><?php esc_html_e('Disable', 'essential-grid'); ?></span>                      
				<div id="pagination-touchswipe-settings"<?php echo ($base->getVar($grid, ['params', 'pagination-touchswipe'], 'off') == 'off') ? ' class="esg-display-none" ' : ''; ?>>
					<div class="div13"></div>
					<label for="pagination-dragvertical" class="eg-tooltip-wrap" title="<?php esc_attr_e('Allows the page to be scrolled vertically', 'essential-grid'); ?>"><?php esc_html_e('Allow Vertical Dragging', 'essential-grid'); ?></label><!--
					--><input type="radio" class="pagination-dragvertical " name="pagination-dragvertical" value="on" <?php checked($base->getVar($grid, ['params', 'pagination-dragvertical'], 'on'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Allow Vertical Dragging', 'essential-grid'); ?>"><?php esc_html_e('Enable', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio" class="pagination-dragvertical" name="pagination-dragvertical" value="off" <?php checked($base->getVar($grid, ['params', 'pagination-dragvertical'], 'on'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Prevent Vertical Dragging', 'essential-grid'); ?>"><?php esc_html_e('Disable', 'essential-grid'); ?></span>               
					<div class="div13"></div>
					<label for="pagination-swipebuffer" class="eg-tooltip-wrap" title="<?php esc_attr_e('Amount the finger moves before a swipe is honored', 'essential-grid'); ?>"><?php esc_html_e('Swipe Threshold', 'essential-grid'); ?></label><!--
					--><input class="input-settings-small " type="text" name="pagination-swipebuffer" value="<?php echo esc_attr($base->getVar($grid, ['params', 'pagination-swipebuffer'], '30', 'i')); ?>" /> px             
				</div>
			</div>
		</div>
	</div>

	<div>
		<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Smart Loading', 'essential-grid'); ?></span></esg-llabel></div>
		<div class="eg-cs-tbc">
			<div class="load-more-wrap<?php echo ($base->getVar($grid, ['params', 'rows-unlimited'], 'off') == 'off') ? ' esg-display-none' : ''; ?>">
				<label for="load-more" class="eg-tooltip-wrap" title="<?php esc_attr_e('Choose the Load More type', 'essential-grid'); ?>"><?php esc_html_e('Load More', 'essential-grid'); ?></label><!--
				--><select name="load-more" >
					<option value="none"<?php selected($base->getVar($grid, ['params', 'load-more'], 'none'), 'none'); ?>><?php esc_html_e('None', 'essential-grid'); ?></option>
					<option value="button"<?php selected($base->getVar($grid, ['params', 'load-more'], 'none'), 'button'); ?>><?php esc_html_e('More Button', 'essential-grid'); ?></option>
					<option value="scroll"<?php selected($base->getVar($grid, ['params', 'load-more'], 'none'), 'scroll'); ?>><?php esc_html_e('Infinite Scroll', 'essential-grid'); ?></option>
				</select>
			</div>
			<div class="load-more-wrap load-more-hide-wrap<?php echo ($base->getVar($grid, ['params', 'rows-unlimited'], 'off') == 'off' || $base->getVar($grid, ['params', 'load-more'], 'none') !== 'scroll') ? ' esg-display-none' : ''; ?>">
				<div class="div13"></div>
				<label for="load-more-hide"><?php esc_html_e('Hide Load More Button', 'essential-grid'); ?></label><!--
				--><input type="radio" name="load-more-hide" value="on" <?php checked($base->getVar($grid, ['params', 'load-more-hide'], 'off'), 'on'); ?>><?php esc_html_e('On', 'essential-grid'); ?><div class="space18"></div><!--
				--><input type="radio" name="load-more-hide" value="off" <?php checked($base->getVar($grid, ['params', 'load-more-hide'], 'off'), 'off'); ?>><?php esc_html_e('Off', 'essential-grid'); ?>
			</div>
			<div class="load-more-wrap<?php echo ($base->getVar($grid, ['params', 'rows-unlimited'], 'off') == 'off') ? ' esg-display-none' : ''; ?>">
				<div class="div13"></div>
				<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Set the Load More text here', 'essential-grid'); ?>" for="load-more-text" ><?php esc_html_e('Load More Text', 'essential-grid'); ?></label><!--
				--><input type="text" name="load-more-text" value="<?php echo esc_attr($base->getVar($grid, ['params', 'load-more-text'], esc_attr__('Load More', 'essential-grid'))); ?>" />
			</div>
			<div class="load-more-wrap<?php echo ($base->getVar($grid, ['params', 'rows-unlimited'], 'off') == 'off') ? ' esg-display-none' : ''; ?>">
				<div class="div13"></div>
				<div><label for="load-more-error" class="eg-tooltip-wrap" title="<?php esc_attr_e('Optional custom message to show for offline LoadMore errors', 'essential-grid'); ?>"><?php esc_html_e('Loading Error Message', 'essential-grid'); ?></label><input type="text" name="load-more-error" value="<?php echo esc_attr($base->getVar($grid, ['params', 'load-more-error'], '')); ?>" /></div>
			</div>
			<div class="load-more-wrap<?php echo ($base->getVar($grid, ['params', 'rows-unlimited'], 'off') == 'off') ? ' esg-display-none' : ''; ?>">
				<div class="div13"></div>
				<label for="load-more-show-number"><?php esc_html_e('Item No. Remaining', 'essential-grid'); ?></label><!--
				--><input type="radio" name="load-more-show-number" value="on" <?php checked($base->getVar($grid, ['params', 'load-more-show-number'], 'on'), 'on'); ?>><?php esc_html_e('On', 'essential-grid'); ?><div class="space18"></div><!--
				--><input type="radio" name="load-more-show-number" value="off" <?php checked($base->getVar($grid, ['params', 'load-more-show-number'], 'on'), 'off'); ?>><?php esc_html_e('Off', 'essential-grid'); ?>
			</div>
			<div class="load-more-wrap<?php echo ($base->getVar($grid, ['params', 'rows-unlimited'], 'off') == 'off') ? ' esg-display-none' : ''; ?>">
				<div class="div13"></div>
				<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Display how many items at start?', 'essential-grid'); ?>" for="load-more-start" ><?php esc_html_e('Item No. at Start', 'essential-grid'); ?></label><!--
				--><span id="slider-load-more-start" class="slider-settings"></span><div class="space18"></div><!--
				--><input class="input-settings-small" type="text" name="load-more-start" value="<?php echo esc_attr($base->getVar($grid, ['params', 'load-more-start'], '3', 'i')); ?>" />
			</div>
			<div class="load-more-wrap<?php echo ($base->getVar($grid, ['params', 'rows-unlimited'], 'off') == 'off') ? ' esg-display-none' : ''; ?>">
				<div class="div13"></div>
				<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Display how many items after loading?', 'essential-grid'); ?>" for="load-more-amount"><?php esc_html_e('Item No. Added', 'essential-grid'); ?></label><!--
				--><span id="slider-load-more-amount" class="slider-settings"></span><div class="space18"></div><!--
				--><input class="input-settings-small" type="text" name="load-more-amount" value="<?php echo esc_attr($base->getVar($grid, ['params', 'load-more-amount'], '3', 'i')); ?>" />
			</div>
			<div class="div13"></div>
			<label for="wait-for-viewport"><?php esc_html_e('Wait for Viewport', 'essential-grid'); ?></label><!--
			--><input type="radio" name="wait-for-viewport" value="on" <?php checked($base->getVar($grid, ['params', 'wait-for-viewport'], 'on'), 'on'); ?>><span class=" eg-tooltip-wrap" title="<?php esc_attr_e('Essential Grid initialization will wait till it appear in viewport', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
			--><input type="radio" name="wait-for-viewport" value="off" <?php checked($base->getVar($grid, ['params', 'wait-for-viewport'], 'on'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Essential Grid will be iinitialized on document load', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
			<div class="div13"></div>
			<label for="lazy-loading"><?php esc_html_e('Lazy Load', 'essential-grid'); ?></label><!--
			--><input type="radio" name="lazy-loading" value="on" <?php checked($base->getVar($grid, ['params', 'lazy-loading'], 'off'), 'on'); ?>><span class=" eg-tooltip-wrap" title="<?php esc_attr_e('Enable Lazy Load of Items', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
			--><input type="radio" name="lazy-loading" value="off" <?php checked($base->getVar($grid, ['params', 'lazy-loading'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Disable Lazy Loading (All Item except the - Load more items -  on first page will be preloaded once)', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
			<div class="div13"></div>
			<label for="lazy-loading-blur"><?php esc_html_e('Lazy Load Blurred Image', 'essential-grid'); ?></label><!--
			--><input type="radio" name="lazy-loading-blur" value="on" <?php checked($base->getVar($grid, ['params', 'lazy-loading-blur'], 'on'), 'on'); ?>><span class=" eg-tooltip-wrap" title="<?php esc_attr_e('Enable Lazy Load Blurred Images, that will be shown before the selected image is loaded', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
			--><input type="radio" name="lazy-loading-blur" value="off" <?php checked($base->getVar($grid, ['params', 'lazy-loading-blur'], 'on'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Disabled Lazy Load Blurred Images, that will be shown before the selected image is loaded', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>         
			<div class="lazy-load-wrap">
				<div class="div13"></div>
				<label for="lazy-load-color" class="eg-tooltip-wrap" title="<?php esc_attr_e('Background color of media during the lazy loading progress', 'essential-grid'); ?>"><?php esc_html_e('Lazy Load Color', 'essential-grid'); ?></label><!--
				--><input name="lazy-load-color" type="text" id="lazy-load-color" value="<?php echo esc_attr($base->getVar($grid, ['params', 'lazy-load-color'], '#FFFFFF')); ?>">
			</div>
		</div>
	</div>

	<div>
		<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Spacings', 'essential-grid'); ?></span></esg-llabel></div>
		<div class="eg-cs-tbc">
			<label class="eg-tooltip-wrap" for="spacings" title="<?php esc_attr_e('Spaces between items vertical and horizontal', 'essential-grid'); ?>"><?php esc_html_e('Item Spacing', 'essential-grid'); ?></label><!--
			--><input class="input-settings-small " type="text" name="spacings" value="<?php echo esc_attr($base->getVar($grid, ['params', 'spacings'], '0', 'i')); ?>" /> px
			<div class="div13"></div>
			<?php
			$grid_padding = $base->getVar($grid, ['params', 'grid-padding'], '0');
			if(!is_array($grid_padding)) $grid_padding = ['0', '0', '0', '0'];
			?>
			<label for="grid-padding"><?php esc_html_e('Grid Padding', 'essential-grid'); ?></label><!--
			--><span class="esg-display-inline-block"><input class="eg-tooltip-wrap input-settings-small" title="<?php esc_attr_e('Grid Padding Top', 'essential-grid'); ?>" type="text" name="grid-padding[]" value="<?php echo esc_attr($base->getVar($grid_padding, 0)); ?>" /> px</span><div class="space18"></div><!--          
			--><span class="esg-display-inline-block"><input class="eg-tooltip-wrap input-settings-small" title="<?php esc_attr_e('Grid Padding Right', 'essential-grid'); ?>" type="text" name="grid-padding[]" value="<?php echo esc_attr($base->getVar($grid_padding, 1)); ?>" /> px</span><div class="space18"></div><!--      
			--><span class="esg-display-inline-block"><input class="eg-tooltip-wrap input-settings-small" title="<?php esc_attr_e('Grid Padding Bottom', 'essential-grid'); ?>" type="text" name="grid-padding[]" value="<?php echo esc_attr($base->getVar($grid_padding, 2)); ?>" /> px</span><div class="space18"></div><!--
			--><span class="esg-display-inline-block"><input class="eg-tooltip-wrap input-settings-small" title="<?php esc_attr_e('Grid Padding Left', 'essential-grid'); ?>" type="text" name="grid-padding[]" value="<?php echo esc_attr($base->getVar($grid_padding, 3)); ?>" /> px</span><div class="space18"></div>
		</div>
	</div>
	</div>

	<!-- SKIN SETTINGS -->
	<div id="esg-settings-skins-settings" class="esg-settings-container">
		<div>
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Background', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc">
				<label for="main-background-color" class="eg-tooltip-wrap" title="<?php esc_attr_e('Background Color of the Grid. Type', 'essential-grid'); ?>"><?php esc_html_e('Main Background Color', 'essential-grid'); ?></label><!--
				--><input data-visible="true" name="main-background-color" type="text" data-editing="Background Color" id="main-background-color" value="<?php echo esc_attr($base->getVar($grid, ['params', 'main-background-color'], 'transparent')); ?>">           
				</div>
			</div>

			<div>
				<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Navigation', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc">
					<label for="navigation-skin" class="eg-tooltip-wrap" title="<?php esc_attr_e('Select the skin/color of the Navigation', 'essential-grid'); ?>"><?php esc_html_e('Choose Skin', 'essential-grid'); ?></label><!--
					--><select id="navigation-skin-select" name="navigation-skin">
						<?php foreach($navigation_skins as $skin){ ?>
						<option value="<?php echo esc_attr($skin['handle']); ?>"<?php selected($nav_skin_choosen, $skin['handle']); ?>><?php echo esc_html($skin['name']); ?></option>
						<?php } ?>
					</select><div class="space18"></div><!--
					--><div id="eg-edit-navigation-skin" class="esg-btn esg-purple eg-tooltip-wrap" title="<?php esc_attr_e('Edit the selected Navigation Skin Style', 'essential-grid'); ?>" ><?php esc_html_e('Edit Skin', 'essential-grid'); ?></div><!--
					--><div id="eg-create-navigation-skin" class="esg-btn esg-blue eg-tooltip-wrap" title="<?php esc_attr_e('Create a new Navigation Skin Style', 'essential-grid'); ?>"><?php esc_html_e('Create Skin', 'essential-grid'); ?></div><!--
					--><div id="eg-delete-navigation-skin" class="esg-btn  esg-red eg-tooltip-wrap" title="<?php esc_attr_e('Delete the selected Navigation Skin', 'essential-grid'); ?>"><?php esc_html_e('Delete Skin', 'essential-grid'); ?></div>
					<div>
						<div class="div10"></div>
						<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Preview Background for better Visibility', 'essential-grid'); ?>"><?php esc_html_e('Preview BG', 'essential-grid'); ?></label><!--
						--><select id="navigation-preview-bg-select" name="navigation-preview-bg">
							<option value="dark"><?php esc_html_e('Dark', 'essential-grid'); ?></option>
							<option value="light"><?php esc_html_e('Light', 'essential-grid'); ?></option>
							<option value="transparent"><?php esc_html_e('Transparent', 'essential-grid'); ?></option>
							<option value="mainbg"><?php esc_html_e('Main BG Color', 'essential-grid'); ?></option>
						</select>
					</div>
					<div id="nav_skin_preview_wrap"><div id="nav_skin_preview"><div id="nav_skin_preview_content"></div><div id="nav_skin_preview_colored"></div></div></div>
				</div>
			</div>

			<div>
				<div class="eg-cs-tbc-left">
					<esg-llabel><span><?php esc_html_e('Item Skins', 'essential-grid'); ?></span></esg-llabel>
				</div>
				<div class="eg-cs-tbc eg-photoshop-bg">
					<div id="eg-selected-skins-wrapper">
						<div id="eg-selected-skins-default">
						<?php
						$skins_c = new Essential_Grid_Item_Skin();
						$navigation_c = new Essential_Grid_Navigation();
						$grid_c = new Essential_Grid();

						$grid_skin_sel['id'] = 'even';
						$grid_skin_sel['name'] = esc_attr__('Skin Selector', 'essential-grid');
						$grid_skin_sel['handle'] = 'skin-selector';
						$grid_skin_sel['postparams'] = [];
						$grid_skin_sel['layers'] = [];
						$grid_skin_sel['params'] = [
							'navigation-skin' => '', // leave empty, we use no skin
							'wait-for-viewport' => 'off',
						]; 

						$skins_html = '';
						$skins_css = '';
						$filters = [];

						$skins = $skins_c->get_essential_item_skins();

						$demo_img = [];
						for ($i=1; $i<11; $i++) {
							$demo_img[] = 'demoimage'.$i.'.jpg';
						}
						shuffle($demo_img);
						
						if (!empty($skins) && is_array($skins)) {
							$do_only_first = false;
							if($entry_skin_choosen == '0') $do_only_first = true; //only add the selected on the first element if we create a new grid, so we select the firs skin
							$counter = 0;
							foreach ($skins as $skin) {
								$counter++;
								if ($counter===10) $counter = 0;
								// 2.2.6
								if(is_array($skin) && array_key_exists('handle', $skin) && $skin['handle'] === 'esgblankskin') continue;

								$item_skin = new Essential_Grid_Item_Skin();
								$item_skin->init_by_data($skin);

								//set filters
								$item_skin->set_skin_choose_filter();
								$item_skin->set_image($demo_img[$counter]);
								
								$item_filter = $item_skin->get_filter_array();
			
								$filters = array_merge($item_filter, $filters);
			
								//add skin specific css
								$item_skin->register_skin_css();
			
								ob_start();
								if ($do_only_first) {
									$item_skin->output_item_skin('skinchoose', '-1'); //-1 = will do select
									$do_only_first = false;
								} else {
									$item_skin->output_item_skin('skinchoose', $entry_skin_choosen);
								}
			
								$current_skin_html = ob_get_clean();
								
								//2.3.7 display html of item skin preview
								$skins_html .= htmlspecialchars_decode($current_skin_html);
								$skins_html = str_replace(
									['%favorites%' , '%eg-clients-icon%', '%eg-clients-icon-dark%', '%author_name%' , '%likes_short%' , '%date%' , '%retweets%' , '%likes%' , '%views_short%' , '%dislikes_short%' , '%duration%' , '%num_comments%','Likes (Facebook,YouTube,Vimeo,Instagram)','Likes Short (Facebook,YouTube,Vimeo,Instagram)' , 'Date Modified', 'Views (flickr,YouTube, Vimeo)' , 'Views Short (flickr,YouTube, Vimeo)', 'Cat. List' , 'Excerpt'],
									['314' , ESG_PLUGIN_URL . '/admin/assets/images/client.png' , ESG_PLUGIN_URL . '/admin/assets/images/client_dark.png' , 'Author' , '1.2K' , '2020-06-28' , '35' , '123' , '54' , '13' , '9:32' , '12' , '231' , '1.2K' , '2020-06-28', '231' , '1.2K' , 'News, Journey, Company', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt.'],
									$skins_html
								);

								ob_start();
								$item_skin->generate_element_css('skinchoose');
								$skins_css.= ob_get_clean();
							}
						}
						$grid_c->init_by_data($grid_skin_sel);

						echo '<div id="esg-grid-'.esc_attr($handle).'-1-wrapper">';

						$grid_c->output_wrapper_pre();

						$filters = array_map("unserialize", array_unique(array_map("serialize", $filters))); //filter to unique elements

						$navigation_c->set_filter($filters);
						$navigation_c->set_style('padding', '10px 0 0 0');

						echo '<div id="main_skin_selector_nav_area" class="esg-text-center">';
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
						echo $navigation_c->output_filter('skinchoose');
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
						echo $navigation_c->output_pagination();
						echo '</div>';

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
						$grid_c->output_grid_pre();

						// output elements
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- var contain escaped HTML
						echo $skins_html;

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
						$grid_c->output_grid_post();
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return escaped HTML
						$grid_c->output_wrapper_post();

						echo '</div>';

						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- var contain escaped HTML
						echo $skins_css;
						?>
						</div>
						<script type="text/javascript">
							window.ESG ??={};
							window.ESG.inits ??={};
							window.ESG.inits.v_demo = { state:false,
							call : () => {							
								jQuery('#esg-grid-even-1').tpessential({
									layout:"masonry",
									forceFullWidth:"off",
									row:3,
									space:20,
									responsiveEntries: [
										{width:1400, amount:3},
										{width:1170, amount:3},
										{width:1024, amount:3},
										{width:960, amount:3},
										{width:778, amount:2},
										{width:640, amount:2},
										{width:480, amount:2}
									],
									pageAnimation:"scale",
									startAnimation:"none",
									startAnimationSpeed: 0,
									startAnimationDelay: 0,
									animSpeed:800,
									animDelay:"on",
									delayBasic:0.4,
									aspectratio:"4:3",
									rowItemMultiplier : "",
								});
							}};
							if (window.jQuery?.fn?.tpessential) {
								window.ESG.inits.v_demo.state = true;
								window.ESG.inits.v_demo.call();
							}
						</script>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- ANIMATION SETTINGS -->
	<div id="esg-settings-animations-settings" class="esg-settings-container">
		<div>
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Start Animation', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc">
					<label for="grid-start-animation" class="eg-tooltip-wrap" title="<?php esc_attr_e('Select the Animation for the Start Effect', 'essential-grid'); ?>"><?php esc_html_e('Animation Type', 'essential-grid'); ?></label><!--
					--><select id="grid-start-animation" name="grid-start-animation">
						<?php foreach ($start_animations as $handle => $name) { ?>
							<option value="<?php echo esc_attr($handle); ?>"<?php selected($grid_start_animation_choosen, $handle); ?>><?php echo esc_html($name); ?></option>
						<?php } ?>
					</select><input type="hidden" id="hide-markup-before-load" name="hide-markup-before-load" value="<?php echo esc_attr($base->getVar($grid, ['params', 'hide-markup-before-load'], 'off')); ?>">
					
					<div id="start-animation-speed-wrap">
						<div class="div13"></div>
						<label for="grid-start-animation-speed" class="eg-tooltip-wrap" title="<?php esc_attr_e('Animation Speed (per item)', 'essential-grid'); ?>"><?php esc_html_e('Transition Speed', 'essential-grid'); ?></label><!--
						--><span id="slider-grid-start-animation-speed" class="slider-settings"></span><div class="space18"></div><input class="input-settings-small" type="text" id="grid-start-animation-speed" name="grid-start-animation-speed" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-start-animation-speed'], '1000', 'i')); ?>" readonly="true" /> ms
					</div>
					<div id="start-animation-delay-wrap">
						<div class="div13"></div>
						<label for="grid-start-animation-delay" class="eg-tooltip-wrap" title="<?php esc_attr_e('Create staggered animations by adding a delay value', 'essential-grid'); ?>"><?php esc_html_e('Transition Delay', 'essential-grid'); ?></label><!--
						--><span id="slider-grid-start-animation-delay" class="slider-settings"></span><div class="space18"></div><input class="input-settings-small" type="text" id="grid-start-animation-delay" name="grid-start-animation-delay" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-start-animation-delay'], '100', 'i')); ?>" readonly="true" />
						<div class="div13"></div>             
						<label for="grid-start-animation-type" class="eg-tooltip-wrap" title="<?php esc_attr_e('Animate columns, rows or items individually', 'essential-grid'); ?>"><?php esc_html_e('Apply Delay to', 'essential-grid'); ?></label><!--
						--><?php $start_animation_type = $base->getVar($grid, ['params', 'grid-start-animation-type'], 'item'); ?><input type="radio" name="grid-start-animation-type" value="item" <?php checked($start_animation_type, 'item'); ?>><span class="eg-tooltip-wrap tooltipstered">Items</span><div class="space18"></div><!--
						--><input type="radio" name="grid-start-animation-type" value="col" <?php checked($start_animation_type, 'col'); ?>><span class="eg-tooltip-wrap tooltipstered">Columns</span><div class="space18"></div><!--
						--><input type="radio" name="grid-start-animation-type" value="row" <?php checked($start_animation_type, 'row'); ?>><span class="eg-tooltip-wrap tooltipstered">Rows</span>             
					</div>
				</div>
			</div>

		<div>
			<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Filter/Pagination Animation', 'essential-grid'); ?></span></esg-llabel></div>
			<div class="eg-cs-tbc">
				<label for="grid-animation" class="eg-tooltip-wrap" title="<?php esc_attr_e('Select the Animation for the Filter Page Change Effects', 'essential-grid'); ?>"><?php esc_html_e('Animation Type', 'essential-grid'); ?></label><!--
				--><select id="grid-animation-select" name="grid-animation">
					<?php foreach($grid_animations as $handle => $name){ ?>
						<option value="<?php echo esc_attr($handle); ?>"<?php selected($grid_animation_choosen, $handle); ?>><?php echo esc_html($name); ?></option>
					<?php } ?>
				</select>
				<div class="div13"></div>
				<label for="grid-animation-speed" class="eg-tooltip-wrap" title="<?php esc_attr_e('Filter Animation Speed (per item)', 'essential-grid'); ?>"><?php esc_html_e('Transition Speed', 'essential-grid'); ?></label><!--
				--><span id="slider-grid-animation-speed" class="slider-settings"></span><div class="space18"></div><input class="input-settings-small" type="text" name="grid-animation-speed" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-animation-speed'], '1000', 'i')); ?>" readonly="true" /> ms
				<div class="div13"></div>
				<label for="grid-animation-delay" class="eg-tooltip-wrap" title="<?php esc_attr_e('Create staggered animations by adding a delay value', 'essential-grid'); ?>"><?php esc_html_e('Transition Delay', 'essential-grid'); ?></label><!--
				--><span id="slider-grid-animation-delay" class="slider-settings"></span><div class="space18"></div><input class="input-settings-small" type="text" name="grid-animation-delay" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-animation-delay'], '1', 'i')); ?>" readonly="true" />
				<div id="animation-delay-type-wrap">
					<div class="div13"></div>
					<label for="grid-animation-type" class="eg-tooltip-wrap" title="<?php esc_attr_e('Animate columns, rows or items individually', 'essential-grid'); ?>"><?php esc_html_e('Apply Delay to', 'essential-grid'); ?></label><!--
					--><?php $animation_type = $base->getVar($grid, ['params', 'grid-animation-type'], 'item'); ?><input type="radio" name="grid-animation-type" value="item" <?php checked($animation_type, 'item'); ?>><span class="eg-tooltip-wrap tooltipstered">Items</span><div class="space18"></div><!--
					--><input type="radio" name="grid-animation-type" value="col" <?php checked($animation_type, 'col'); ?>><span class="eg-tooltip-wrap tooltipstered">Columns</span><div class="space18"></div><!--
					--><input type="radio" name="grid-animation-type" value="row" <?php checked($animation_type, 'row'); ?>><span class="eg-tooltip-wrap tooltipstered">Rows</span>
				</div>
			</div>
		</div>

		<div>
			<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Hover Animations', 'essential-grid'); ?></span></esg-llabel></div>
			<div class="eg-cs-tbc"> 
				<div class="esg-showhidegroup">
					<label for="grid-item-animation" class="eg-tooltip-wrap" title="<?php esc_attr_e('Animate the entire Grid Item on Hover', 'essential-grid'); ?>"><?php esc_html_e('Grid Item Hover Animation', 'essential-grid'); ?></label><!--
					--><select id="grid-item-animation" class="grid-item-anime-select" name="grid-item-animation" >
					<?php foreach ($grid_item_animations as $handle => $name) { ?>
						<option value="<?php echo esc_attr($handle); ?>"<?php selected($grid_item_animation_choosen, $handle); ?>><?php echo esc_html($name); ?></option>
					<?php } ?>
					</select><div class="space18"></div><!--
					--><span class="grid-item-anime-wrap-zoomin grid-item-anime-option"><input id="grid-item-animation-zoomin" class="input-settings-small eg-tooltip-wrap" type="text" name="grid-item-animation-zoomin" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-item-animation-zoomin'], '125')); ?>" title="<?php esc_attr_e('Zoom In Percentage (100-200)', 'essential-grid'); ?>" /> %</span><!--
					--><span class="grid-item-anime-wrap-zoomout grid-item-anime-option"><input id="grid-item-animation-zoomout" class="input-settings-small eg-tooltip-wrap" type="text" name="grid-item-animation-zoomout" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-item-animation-zoomout'], '75')); ?>" title="<?php esc_attr_e('Zoom Out Percentage (0-100)', 'essential-grid'); ?>" /> %</span><!--
					--><span class="grid-item-anime-wrap-fade grid-item-anime-option"><input id="grid-item-animation-fade" class="input-settings-small eg-tooltip-wrap" type="text" name="grid-item-animation-fade" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-item-animation-fade'], '75')); ?>" title="<?php esc_attr_e('Fade Percentage (0-100)', 'essential-grid'); ?>" /> %</span><!--
					--><span class="grid-item-anime-wrap-blur grid-item-anime-option"><input id="grid-item-animation-blur" class="input-settings-small eg-tooltip-wrap" type="text" name="grid-item-animation-blur" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-item-animation-blur'], '5')); ?>" title="<?php esc_attr_e('Blur Amount (0-20)', 'essential-grid'); ?>" /> px</span><!--
					--><span class="grid-item-anime-wrap-rotate grid-item-anime-option"><input id="grid-item-animation-rotate" class="input-settings-small eg-tooltip-wrap" type="text" name="grid-item-animation-rotate" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-item-animation-rotate'], '30')); ?>" title="<?php esc_attr_e('Blur Amount (0-360)', 'essential-grid'); ?>" /> deg</span><!--
					--><span class="grid-item-anime-wrap-shift grid-item-anime-option"><select name="grid-item-animation-shift" >
						<?php $grid_item_anime_shift = $base->getVar($grid, ['params', 'grid-item-animation-shift'], 'up'); ?>
						<option value="up"<?php selected($grid_item_anime_shift, 'up'); ?>><?php esc_html_e('Up', 'essential-grid'); ?></option>
						<option value="down"<?php selected($grid_item_anime_shift, 'down'); ?>><?php esc_html_e('Down', 'essential-grid'); ?></option>
						<option value="left"<?php selected($grid_item_anime_shift, 'left'); ?>><?php esc_html_e('Left', 'essential-grid'); ?></option>
						<option value="right"<?php selected($grid_item_anime_shift, 'right'); ?>><?php esc_html_e('Right', 'essential-grid'); ?></option>
					</select><div class="space18"></div><input id="grid-item-animation-shift-amount" class="input-settings-small eg-tooltip-wrap" type="text" name="grid-item-animation-shift-amount" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-item-animation-shift-amount'], '10')); ?>" title="<?php esc_attr_e('Shift Amount in pixels', 'essential-grid'); ?>" /> px
					</span>
				</div>
				<div class="div13"></div>
				<div class="esg-showhidegroup">
					<label for="grid-item-animation-other" class="eg-tooltip-wrap" title="<?php esc_attr_e('Animate other Grid Items on Hover', 'essential-grid'); ?>"><?php esc_html_e('Grid Item Other Animation', 'essential-grid'); ?></label><!--
					--><select id="grid-item-animation-other" class="grid-item-anime-select" name="grid-item-animation-other" >
						<?php foreach($grid_item_animations as $handle => $name){ ?>
							<option value="<?php echo esc_attr($handle); ?>"<?php selected($grid_item_animation_other, $handle); ?>><?php echo esc_html($name); ?></option>
						<?php } ?>
					</select><div class="space18"></div><!--
					--><span class="grid-item-anime-wrap-zoomin grid-item-anime-option"><input id="grid-item-other-zoomin" class="input-settings-small eg-tooltip-wrap" type="text" name="grid-item-other-zoomin" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-item-other-zoomin'], '125')); ?>" title="<?php esc_attr_e('Zoom In Percentage (100-200)', 'essential-grid'); ?>" /> %</span><!--
					--><span class="grid-item-anime-wrap-zoomout grid-item-anime-option"><input id="grid-item-other-zoomout" class="input-settings-small eg-tooltip-wrap" type="text" name="grid-item-other-zoomout" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-item-other-zoomout'], '75')); ?>" title="<?php esc_attr_e('Zoom Out Percentage (0-100)', 'essential-grid'); ?>" /> %</span><!--
					--><span class="grid-item-anime-wrap-fade grid-item-anime-option"><input id="grid-item-other-fade" class="input-settings-small eg-tooltip-wrap" type="text" name="grid-item-other-fade" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-item-other-fade'], '75')); ?>" title="<?php esc_attr_e('Fade Percentage (0-100)', 'essential-grid'); ?>" /> %</span><!--
					--><span class="grid-item-anime-wrap-blur grid-item-anime-option"><input id="grid-item-other-blur" class="input-settings-small eg-tooltip-wrap" type="text" name="grid-item-other-blur" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-item-other-blur'], '5')); ?>" title="<?php esc_attr_e('Blur Amount (0-20)', 'essential-grid'); ?>" /> px</span><!--
					--><span class="grid-item-anime-wrap-rotate grid-item-anime-option"><input id="grid-item-other-rotate" class="input-settings-small eg-tooltip-wrap" type="text" name="grid-item-other-rotate" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-item-other-rotate'], '30')); ?>" title="<?php esc_attr_e('Blur Amount (0-360)', 'essential-grid'); ?>" /> deg</span><!--
					--><span class="grid-item-anime-wrap-shift grid-item-anime-option"><select name="grid-item-other-shift" >
						<?php $grid_item_other_shift = $base->getVar($grid, ['params', 'grid-item-other-shift'], 'up'); ?>
						<option value="up"<?php selected($grid_item_other_shift, 'up'); ?>><?php esc_html_e('Up', 'essential-grid'); ?></option>
						<option value="down"<?php selected($grid_item_other_shift, 'down'); ?>><?php esc_html_e('Down', 'essential-grid'); ?></option>
						<option value="left"<?php selected($grid_item_other_shift, 'left'); ?>><?php esc_html_e('Left', 'essential-grid'); ?></option>
						<option value="right"<?php selected($grid_item_other_shift, 'right'); ?>><?php esc_html_e('Right', 'essential-grid'); ?></option>
						</select><div class="space18"></div><input id="grid-item-other-shift-amount" class="input-settings-small eg-tooltip-wrap" type="text" name="grid-item-other-shift-amount" value="<?php echo esc_attr($base->getVar($grid, ['params', 'grid-item-other-shift-amount'], '10')); ?>" title="<?php esc_attr_e('Shift Amount in pixels', 'essential-grid'); ?>" /> px
					</span>
				</div>
			</div>
		</div>
		</div>
	</div>

	<!-- NAVIGATION SETTINGS -->
	<div id="esg-settings-filterandco-settings" class="esg-settings-container">
		<div>
		<div id="es-ng-layout-wrapper">
			<div>
			<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Navigation Positions', 'essential-grid'); ?></span></esg-llabel></div>
			<!-- 2.1.5 added overflow: auto -->
			<div class="eg-cs-tbc eg-cs-tbc-nav-pos">
				<?php
				$layout = $base->getVar($grid, ['params', 'navigation-layout'], []);
				$navig_special_class = $base->getVar($grid, ['params', 'navigation-special-class'], []);
				$navig_special_skin = $base->getVar($grid, ['params', 'navigation-special-skin'], []);
				?>
				<script type="text/javascript">
					var eg_nav_special_class = <?php echo wp_json_encode($navig_special_class); ?>;
					var eg_nav_special_skin = <?php echo wp_json_encode($navig_special_skin); ?>;
				</script>
				<div class="esg-msow-inner">
					<div>
						<div><?php esc_html_e('Available Modules:', 'essential-grid'); ?></div>
						<div class="div13"></div>
						<div class="eg-navigation-cons-wrapper eg-tooltip-wrap eg-navigation-default-wrap" title="<?php esc_attr_e('Drag and Drop Navigation Modules into the Available Drop Zones', 'essential-grid'); ?>">
							<div data-navtype="left" class="eg-navigation-cons-left eg-navigation-cons"<?php echo (isset($layout['left']) && $layout['left'] !== '') ? ' data-putin="'.esc_attr(current(array_keys($layout['left']))).'" data-sort="'.esc_attr($layout['left'][current(array_keys($layout['left']))]).'"' : ''; ?>><i class="eg-icon-left-open"></i></div>
							<div data-navtype="right" class="eg-navigation-cons-right eg-navigation-cons"<?php echo (isset($layout['right']) && $layout['right'] !== '') ? ' data-putin="'.esc_attr(current(array_keys($layout['right']))).'" data-sort="'.esc_attr($layout['right'][current(array_keys($layout['right']))]).'"' : ''; ?>><i class="eg-icon-right-open"></i></div>
							<div data-navtype="pagination" class="eg-navigation-cons-pagination eg-navigation-cons"<?php echo (isset($layout['pagination']) && $layout['pagination'] !== '') ? ' data-putin="'.esc_attr(current(array_keys($layout['pagination']))).'" data-sort="'.esc_attr($layout['pagination'][current(array_keys($layout['pagination']))]).'"' : ''; ?>><i class="eg-icon-doc-inv"></i><?php esc_html_e("Pagination", 'essential-grid'); ?></div>
							<div data-navtype="filter" class="eg-navigation-cons-filter eg-navigation-cons"<?php echo (isset($layout['filter']) && $layout['filter'] !== '') ? ' data-putin="'.esc_attr(current(array_keys($layout['filter']))).'" data-sort="'.esc_attr($layout['filter'][current(array_keys($layout['filter']))]).'"' : ''; ?>><i class="eg-icon-megaphone"></i><?php esc_html_e("Filter 1", 'essential-grid'); ?></div>
							<?php if (Essential_Grid_Woocommerce::is_woo_exists()) { ?>
							<div data-navtype="cart" class="eg-navigation-cons-cart eg-navigation-cons"<?php echo (isset($layout['cart']) && $layout['cart'] !== '') ? ' data-putin="'.esc_attr(current(array_keys($layout['cart']))).'" data-sort="'.esc_attr($layout['cart'][current(array_keys($layout['cart']))]).'"' : ''; ?>><i class="eg-icon-basket"></i><?php esc_html_e("Cart", 'essential-grid'); ?></div>
							<?php }

							//add extra filters
							if (!empty($layout)) {
								foreach ($layout as $key => $val) {
									if (strpos($key, 'filter-') !== false) {
										$nr = esc_attr(str_replace('filter-', '', $key));
										?>
										<div data-navtype="filter-<?php echo esc_attr($nr); ?>" class="eg-navigation-cons-filter-<?php echo esc_attr($nr); ?> eg-nav-cons-filter eg-navigation-cons"<?php echo ' data-putin="'.esc_attr(current(array_keys($layout[$key]))).'" data-sort="'.esc_attr($layout[$key][current(array_keys($layout[$key]))]).'"'; ?>><i class="eg-icon-megaphone"></i><?php esc_html_e("Filter", 'essential-grid'); echo ' '.esc_html($nr); ?></div>
										<?php
									}
								}
							}
							?>
							<div data-navtype="sort" class="eg-navigation-cons-sort eg-navigation-cons"<?php echo (isset($layout['sorting']) && $layout['sorting'] !== '') ? ' data-putin="'.esc_attr(current(array_keys($layout['sorting']))).'" data-sort="'.esc_attr($layout['sorting'][current(array_keys($layout['sorting']))]).'"' : ''; ?>><i class="eg-icon-sort-name-up"></i><?php esc_html_e("Sort", 'essential-grid'); ?></div>
							<div data-navtype="search-input" class="eg-navigation-cons-search-input eg-navigation-cons"<?php echo (isset($layout['search-input']) && $layout['search-input'] !== '') ? ' data-putin="'.esc_attr(current(array_keys($layout['search-input']))).'" data-sort="'.esc_attr($layout['search-input'][current(array_keys($layout['search-input']))]).'"' : ''; ?>><i class="eg-icon-search"></i><?php esc_html_e("Search", 'essential-grid'); ?></div>
		
							<div class="eg-stay-last-element esg-clearfix"></div>
						</div>
					</div>
	
					<div id="eg-navigations-drag-wrap">
						<div class="div13"></div>
						<div><?php esc_html_e('Controls inside Grid:', 'essential-grid'); ?></div>
						<div class="div5"></div>
						<div id="eg-navigations-sort-top-1" class="eg-navigation-drop-wrapper eg-tooltip-wrap" title="<?php esc_attr_e('Move the Navigation Modules to define the Order of Buttons', 'essential-grid'); ?>"><?php esc_html_e('DROPZONE - TOP - 1', 'essential-grid'); ?><div class="eg-navigation-drop-inner"></div></div>
						<div id="eg-navigations-sort-top-2" class="eg-navigation-drop-wrapper eg-tooltip-wrap" title="<?php esc_attr_e('Move the Navigation Modules to define the Order of Buttons', 'essential-grid'); ?>"><?php esc_html_e('DROPZONE - TOP - 2', 'essential-grid'); ?><div class="eg-navigation-drop-inner"></div></div>
						<div id="eg-navigations-items-bg" >
							<div class="eg-navconstrctor-pi1"></div>
							<div class="eg-navconstrctor-pi2"></div>
							<div class="eg-navconstrctor-pi3"></div>
							<div class="eg-navconstrctor-pi4"></div>
							<div class="eg-navconstrctor-pi5"></div>
							<div class="eg-navconstrctor-pi6"></div>
							<div id="eg-navigations-sort-left" class="eg-navigation-drop-wrapper"><?php
								printf(
									/* translators: 1:br tag */
									esc_html__('DROPZONE %1$s LEFT', 'essential-grid'),
									'<br>'
								);
								?><div class="eg-navigation-drop-inner"></div></div>
							<div id="eg-navigations-sort-right" class="eg-navigation-drop-wrapper"><?php
								printf(
									/* translators: 1:br tag */
									esc_html__('DROPZONE %1$s RIGHT', 'essential-grid'),
									'<br>'
								);
								?><div class="eg-navigation-drop-inner"></div></div>
						</div>
						<div id="eg-navigations-sort-bottom-1" class="eg-navigation-drop-wrapper eg-tooltip-wrap" title="<?php esc_attr_e('Move the Navigation Modules to define the Order of Buttons', 'essential-grid'); ?>"><?php esc_html_e('DROPZONE - BOTTOM - 1', 'essential-grid'); ?><div class="eg-navigation-drop-inner"></div></div>
						<div id="eg-navigations-sort-bottom-2" class="eg-navigation-drop-wrapper eg-tooltip-wrap" title="<?php esc_attr_e('Move the Navigation Modules to define the Order of Buttons', 'essential-grid'); ?>"><?php esc_html_e('DROPZONE - BOTTOM - 2', 'essential-grid'); ?><div class="eg-navigation-drop-inner"></div></div>
					</div>
					<div id="eg-external-drag-wrap">
						<div class="div13"></div>
						<div><?php esc_html_e('Controls anywhere on Page (through ShortCode):', 'essential-grid'); ?></div>
						<div class="div5"></div>
						<div id="eg-navigation-external-description">
							<div class="eg-ext-nav-desc"><?php esc_html_e('Button', 'essential-grid'); ?></div>
							<div class="eg-ext-nav-desc"><?php esc_html_e('ShortCode', 'essential-grid'); ?></div>
							<div class="eg-ext-nav-desc"><?php esc_html_e('Additional Class', 'essential-grid'); ?></div>
							<div class="eg-ext-nav-desc"><?php esc_html_e('Skin', 'essential-grid'); ?></div>
						</div>
						<div id="eg-navigations-sort-external" class="eg-navigation-drop-wrapper">
							<?php esc_html_e('DROPZONE - EXTERNAL', 'essential-grid'); ?><div class="eg-navigation-drop-inner"></div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
				<div class="esg-dropzone-spacer"></div>
			</div>
		</div>

		<div>
			<div class="eg-cs-tbc-left">
				<esg-llabel class="box-closed"><span><?php esc_html_e('Grid Internal Controls Layout', 'essential-grid'); ?></span></esg-llabel>
			</div>
			<div class="eg-cs-tbc">

				<!--  DROPZONE 1 ALIGN -->
				
				<label for="navigation-container"><?php esc_html_e("Dropzone Top 1", 'essential-grid'); ?></label><!--
				--><input type="radio" name="top-1-align" value="left" <?php checked($base->getVar($grid, ['params', 'top-1-align'], 'center'), 'left'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('All Buttons in this Zone Align to the left', 'essential-grid'); ?>" ><?php esc_html_e("Left", 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="top-1-align" value="center" <?php checked($base->getVar($grid, ['params', 'top-1-align'], 'center'), 'center'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('All Buttons in this Zone Align to Center', 'essential-grid'); ?>" ><?php esc_html_e("Center", 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="top-1-align" value="right" <?php checked($base->getVar($grid, ['params', 'top-1-align'], 'center'), 'right'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('All Buttons in this Zone Align to the Right', 'essential-grid'); ?>"><?php esc_html_e("Right", 'essential-grid'); ?></span><div class="space36"></div><!--
				--><span class="eg-tooltip-wrap esg-dropzone-margin-label" title="<?php esc_attr_e('Space under the Zone', 'essential-grid'); ?>"><?php esc_html_e('Margin Bottom', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input class="input-settings-small " type="text" name="top-1-margin-bottom" value="<?php echo esc_attr($base->getVar($grid, ['params', 'top-1-margin-bottom'], '0', 'i')); ?>"> px
				<div class="div13"></div>
				
				<!--  DROPZONE 2 ALIGN -->
				<label for="navigation-container"><?php esc_html_e("Dropzone Top 2", 'essential-grid'); ?></label><!--
				--><input type="radio" name="top-2-align" value="left" <?php checked($base->getVar($grid, ['params', 'top-2-align'], 'center'), 'left'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('All Buttons in this Zone Align to the left', 'essential-grid'); ?>"><?php esc_html_e("Left", 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="top-2-align" value="center" <?php checked($base->getVar($grid, ['params', 'top-2-align'], 'center'), 'center'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('All Buttons in this Zone Align to Center', 'essential-grid'); ?>"><?php esc_html_e("Center", 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="top-2-align" value="right" <?php checked($base->getVar($grid, ['params', 'top-2-align'], 'center'), 'right'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('All Buttons in this Zone Align to the Right', 'essential-grid'); ?>"><?php esc_html_e("Right", 'essential-grid'); ?></span><div class="space36"></div><!--
				--><span class="eg-tooltip-wrap esg-dropzone-margin-label" title="<?php esc_attr_e('Space under the Zone', 'essential-grid'); ?>"><?php esc_html_e('Margin Bottom', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input class="input-settings-small " type="text" name="top-2-margin-bottom" value="<?php echo esc_attr($base->getVar($grid, ['params', 'top-2-margin-bottom'], '0', 'i')); ?>"> px
				<div class="div13"></div>
				<!--  DROPZONE 3 ALIGN -->
				
				<label for="navigation-container"><?php esc_html_e("Dropzone Bottom 1", 'essential-grid'); ?></label><!--
				--><input type="radio" name="bottom-1-align" value="left" <?php checked($base->getVar($grid, ['params', 'bottom-1-align'], 'center'), 'left'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('All Buttons in this Zone Align to the left', 'essential-grid'); ?>"><?php esc_html_e("Left", 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="bottom-1-align" value="center" <?php checked($base->getVar($grid, ['params', 'bottom-1-align'], 'center'), 'center'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('All Buttons in this Zone Align to Center', 'essential-grid'); ?>"><?php esc_html_e("Center", 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="bottom-1-align" value="right" <?php checked($base->getVar($grid, ['params', 'bottom-1-align'], 'center'), 'right'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('All Buttons in this Zone Align to the Right', 'essential-grid'); ?>"><?php esc_html_e("Right", 'essential-grid'); ?></span><div class="space36"></div><!--
				--><span class="eg-tooltip-wrap esg-dropzone-margin-label" title="<?php esc_attr_e('Space above the Zone', 'essential-grid'); ?>"><?php esc_html_e('Margin Top', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input class="input-settings-small " type="text" name="bottom-1-margin-top" value="<?php echo esc_attr($base->getVar($grid, ['params', 'bottom-1-margin-top'], '0', 'i')); ?>"> px
				<div class="div13"></div>
	
				<!--  DROPZONE 4 ALIGN -->
				<label for="navigation-container"><?php esc_html_e("Dropzone Bottom 2", 'essential-grid'); ?></label><!--
				--><input type="radio" name="bottom-2-align" value="left" <?php checked($base->getVar($grid, ['params', 'bottom-2-align'], 'center'), 'left'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('All Buttons in this Zone Align to the left', 'essential-grid'); ?>"><?php esc_html_e("Left", 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="bottom-2-align" value="center" <?php checked($base->getVar($grid, ['params', 'bottom-2-align'], 'center'), 'center'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('All Buttons in this Zone Align to Center', 'essential-grid'); ?>"><?php esc_html_e("Center", 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="bottom-2-align" value="right" <?php checked($base->getVar($grid, ['params', 'bottom-2-align'], 'center'), 'right'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('All Buttons in this Zone Align to the Right', 'essential-grid'); ?>"><?php esc_html_e("Right", 'essential-grid'); ?></span><div class="space36"></div><!--
				--><span class="eg-tooltip-wrap esg-dropzone-margin-label" title="<?php esc_attr_e('Space above the Zone', 'essential-grid'); ?>"><?php esc_html_e('Margin Top', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input class="input-settings-small " type="text" name="bottom-2-margin-top" value="<?php echo esc_attr($base->getVar($grid, ['params', 'bottom-2-margin-top'], '0', 'i')); ?>"> px
				<div class="div13"></div>
	
				<!--  DROPZONE LEFT  -->
				<label for="navigation-container"><?php esc_html_e("Dropzone Left", 'essential-grid'); ?></label><!--
				--><input class="input-settings-small " type="text" name="left-margin-left" value="<?php echo esc_attr($base->getVar($grid, ['params', 'left-margin-left'], '0', 'i')); ?>"> px
				<div class="div13"></div>
	
				<!--  DROPZONE RIGHT -->
				<label for="navigation-container"><?php esc_html_e("Dropzone Right", 'essential-grid'); ?></label><!--
				--><input class="input-settings-small " type="text" name="right-margin-right" value="<?php echo esc_attr($base->getVar($grid, ['params', 'right-margin-right'], '0', 'i')); ?>"> px
				<div class="div13"></div>
			</div>
		</div>

	</div>

		<div>
			<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Module Spaces', 'essential-grid'); ?></span></esg-llabel></div>
			<div class="eg-cs-tbc">
				<!--  MODULE SPACINGS -->
				<label for="navigation-container" class="eg-tooltip-wrap" title="<?php esc_attr_e('Spaces horizontal between the Navigation Modules', 'essential-grid'); ?>"><?php esc_html_e("Module Spacing", 'essential-grid'); ?></label><!--
				--><input class="input-settings-small " type="text" name="module-spacings" value="<?php echo esc_attr($base->getVar($grid, ['params', 'module-spacings'], '5', 'i')); ?>"> px            
			</div>
		</div>

		<div>
			<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Pagination Settings', 'essential-grid'); ?></span></esg-llabel></div>
			<div class="eg-cs-tbc">
				<label for="pagination-numbers"><?php esc_html_e('Page Number Option', 'essential-grid'); ?></label><!--
				--><input type="radio" name="pagination-numbers" value="smart" <?php checked($base->getVar($grid, ['params', 'pagination-numbers'], 'smart'), 'smart'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Will show pagination like: 1 2 ... 5 6', 'essential-grid'); ?>"><?php esc_html_e('Smart', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="pagination-numbers" value="full" <?php checked($base->getVar($grid, ['params', 'pagination-numbers'], 'smart'), 'full'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Will show full pagination like: 1 2 3 4 5 6', 'essential-grid'); ?>"><?php esc_html_e('Full', 'essential-grid'); ?></span>
				<div class="div13"></div>
				<label for="pagination-scroll"><?php esc_html_e('Scroll To Top', 'essential-grid'); ?></label><!--
				--><input type="radio" name="pagination-scroll" value="on" <?php checked($base->getVar($grid, ['params', 'pagination-scroll'], 'off'), 'on'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Scroll to top if pagination is clicked', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="pagination-scroll" value="off" <?php checked($base->getVar($grid, ['params', 'pagination-scroll'], 'off'), 'off'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Do nothing if pagination is clicked', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
				
				<div id="pagination_scroll_offset"<?php echo ($base->getVar($grid, ['params', 'pagination-scroll'], 'off') == 'off') ? ' class="esg-display-none" ' : ''; ?>>
					<div class="div13"></div>
					<label for="pagination-scroll-offset" class="eg-tooltip-wrap" title="<?php esc_attr_e('Define an offset for the scrolling position', 'essential-grid'); ?>"><?php esc_html_e('Scroll To Offset', 'essential-grid'); ?></label><!--
					--><input class="input-settings-small " type="text" name="pagination-scroll-offset" value="<?php echo esc_attr($base->getVar($grid, ['params', 'pagination-scroll-offset'], '0', 'i')); ?>"> px
				</div>
			</div>
		</div>

		<div class="filter_groups">
			<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Filter Groups', 'essential-grid'); ?></span></esg-llabel></div>
			<div class="eg-cs-tbc">
				<label for="filter-arrows"><?php esc_html_e('Filter Type', 'essential-grid'); ?></label><!--
				--><input type="radio" name="filter-arrows" value="single" <?php checked($base->getVar($grid, ['params', 'filter-arrows'], 'single'), 'single'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Filter is based on 1 Selected Filter in same time.', 'essential-grid'); ?>"><?php esc_html_e('Single', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="filter-arrows" value="multi" <?php checked($base->getVar($grid, ['params', 'filter-arrows'], 'single'), 'multi'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Filter is based on 1 or more Filters in same time.', 'essential-grid'); ?>"><?php esc_html_e('Multiple', 'essential-grid'); ?></span>           
				<div class="eg-filter-logic esg-display-none">
					<div class="div13"></div>
					<label for="filter-logic"><?php esc_html_e('Filter Logic', 'essential-grid'); ?></label><!--
					--><input type="radio" name="filter-logic" value="and" <?php checked($base->getVar($grid, ['params', 'filter-logic'], 'or'), 'and'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Shows all elements that meet ONE OR MORE of the selected filters', 'essential-grid'); ?>"><?php esc_html_e('AND', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio" name="filter-logic" value="or" <?php checked($base->getVar($grid, ['params', 'filter-logic'], 'or'), 'or'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Shows all elements that meet ALL of the selected filters', 'essential-grid'); ?>"><?php esc_html_e('OR', 'essential-grid'); ?></span>
				</div>
				<div id="add_filters_by">
					<div class="div13"></div>
					<label for="add-filters-by"><?php esc_html_e('Add Filters By', 'essential-grid'); ?></label><!--
					--><input type="radio" name="add-filters-by" value="default" <?php checked($base->getVar($grid, ['params', 'add-filters-by'], 'default'), 'default'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Combine tags and categories', 'essential-grid'); ?>"><?php esc_html_e('Default', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio" name="add-filters-by" value="categories" <?php checked($base->getVar($grid, ['params', 'add-filters-by'], 'default'), 'categories'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Only categories', 'essential-grid'); ?>"><?php esc_html_e('Categories', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio" name="add-filters-by" value="tags" <?php checked($base->getVar($grid, ['params', 'add-filters-by'], 'default'), 'tags'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Only tags', 'essential-grid'); ?>"><?php esc_html_e('Tags', 'essential-grid'); ?></span>
				</div>

				<div class="eg-filter-start">
					<div class="div13"></div>
					<label for="filter-start" class="eg-tooltip-wrap" title="<?php esc_attr_e('Grid starts with this filter(filters comma separated) active. Take slug from below or leave empty to disable.', 'essential-grid'); ?>"><?php esc_html_e('Start with Filter', 'essential-grid'); ?></label><!--
					--><input type="text" name="filter-start" value="<?php echo esc_attr($base->getVar($grid, ['params', 'filter-start'], '')); ?>" >
				</div>
			
				<div class="div13"></div>
				<label for="filter-deep-link" class="eg-tooltip-wrap" title="<?php esc_attr_e('Deep Link to select filter by adding # plus the slug to the loading URL', 'essential-grid'); ?>"><?php esc_html_e('Deep Linking', 'essential-grid'); ?></label><!--
				--><input type="radio" name="filter-deep-link" value="on" <?php checked($base->getVar($grid, ['params', 'filter-deep-link'], 'off'), 'on'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Deep Linking with #slug possible', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="filter-deep-link" value="off" <?php checked($base->getVar($grid, ['params', 'filter-deep-link'], 'off'), 'off'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('No Deep Linking with #slug', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
			
				<div class="div13"></div>
				<label for="filter-show-on"><?php esc_html_e('Dropdown Elements on', 'essential-grid'); ?></label><!--
				--><input type="radio" name="filter-show-on" value="click" <?php checked($base->getVar($grid, ['params', 'filter-show-on'], 'hover'), 'click'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Filter in Dropdown will be shown on click', 'essential-grid'); ?>"><?php esc_html_e('Click', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="filter-show-on" value="hover" <?php checked($base->getVar($grid, ['params', 'filter-show-on'], 'hover'), 'hover'); ?>> <span class="eg-tooltip-wrap" title="<?php esc_attr_e('Filter in Dropdown will be shown on hover', 'essential-grid'); ?>"><?php esc_html_e('Hover', 'essential-grid'); ?></span>
			
				<div id="convert_mobile_filters">
					<div class="div13"></div>
					<label for="convert-mobile-filters"><?php esc_html_e('Mobile Filter Conversion', 'essential-grid'); ?></label><!--
					--><input type="radio" class="convert-mobile-filters" name="convert-mobile-filters" value="on" <?php checked($base->getVar($grid, ['params', 'convert-mobile-filters'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Choose to convert "Inline" filter layouts to "Dropdown" on mobile', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio" class="convert-mobile-filters" name="convert-mobile-filters" value="off" <?php checked($base->getVar($grid, ['params', 'convert-mobile-filters'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Choose to convert "Inline" filter layouts to "Dropdown" on mobile', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
				</div>

				<div id="convert_mobile_filters_width"<?php echo ($base->getVar($grid, ['params', 'convert-mobile-filters'], 'off') == 'off') ? ' class="esg-display-none" ' : ''; ?>>
					<div class="div13"></div>
					<label for="convert-mobile-filters-width" class="eg-tooltip-wrap" title="<?php esc_attr_e('Screen Width in px to trigger Mobile Filter Conversion', 'essential-grid'); ?>"><?php esc_html_e('Width', 'essential-grid'); ?></label><!--
					--><input class="input-settings-small " type="text" name="convert-mobile-filters-width" value="<?php echo esc_attr($base->getVar($grid, ['params', 'convert-mobile-filters-width'], '768', 'i')); ?>" /> px
				</div>
			
				<div class="eg-original-filter-options-holder">

					<?php
					$filter_startup = !empty($base->getVar($grid, ['params', 'filter-selected'], ''));
					$filter_counter = 1;

					eg_filter_tab_function($filter_counter, $grid, $base, 'eg-original-filter-options-wrap');
					
					//check if we have more than one filter area
					if ( !empty($grid['params']) ) {
						foreach ($grid['params'] as $key => $params) {
							if (strpos($key, 'filter-selected-') !== 0) continue;
							
							$n = str_replace('filter-selected-', '', $key);
							eg_filter_tab_function($n, $grid, $base);
							if($filter_counter < $n) $filter_counter = $n;
						}
					}
					
					function eg_filter_tab_function($id, $grid, $base, $wrap_class = 'eg-filter-options-wrap-clone')
					{
						$f_id = $id == 1 ? '' : esc_attr('-' . $id);

						$filterallon = $base->getVar( $grid, ['params', 'filter-all-visible' . $f_id], 'on' );
						$filtervisible = $base->getVar( $grid, ['params', 'filter-all-visible' . $f_id], 'on' ) === 'on' ? 'block' : 'none';
						$filter_all_text = $base->getVar( $grid, ['params', 'filter-all-text' . $f_id], esc_attr__( 'Filter - All', 'essential-grid' ) );
						$filter_listing = $base->getVar($grid, ['params', 'filter-listing' . $f_id], 'list');
						$filter_dropdown_text = $base->getVar($grid, ['params', 'filter-dropdown-text' . $f_id], esc_attr__('Filter Categories', 'essential-grid'));
						$filter_counter = $base->getVar($grid, ['params', 'filter-counter' . $f_id], 'off');
						$filter_sort_alpha = $base->getVar($grid, ['params', 'filter-sort-alpha' . $f_id], 'off');
						$filter_sort_alpha_dir = $base->getVar($grid, ['params', 'filter-sort-alpha-dir' . $f_id], 'asc');
						$filter_selected = $base->getVar($grid, ['params', 'filter-selected' . $f_id], []);

						?>
						<div class="eg-filter-options-wrap <?php echo esc_attr($wrap_class); ?>">
							<div class="eg-filter-header-block">
								<i class="eg-icon-megaphone"></i><?php esc_html_e('Filter -', 'essential-grid'); ?>
								<span class="filter-header-id"><?php echo esc_attr($id);?></span>
							</div>

							<div class="eg-filter-label"><?php esc_html_e('Show/Hide Filter "All" Button', 'essential-grid'); ?></div>
							<div class="eg-filter-option-field">
								<input type="radio" name="filter-all-visible<?php echo esc_attr($f_id); ?>" data-origname="filter-all-visible-#NR" value="on" class=" filtervisible filter-all-first" <?php checked($filterallon, 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Show the Filter All button', 'essential-grid'); ?>"><?php esc_html_e('Show', 'essential-grid'); ?></span><div class="space18"></div><!--
								--><input type="radio" name="filter-all-visible<?php echo esc_attr($f_id); ?>" data-origname="filter-all-visible-#NR" value="off" class="filtervisible" <?php checked($filterallon, 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Hide the Filter All button', 'essential-grid'); ?>"><?php esc_html_e('Hide', 'essential-grid'); ?></span>
							</div>

							<div class="eg-filter-visible esg-display-<?php echo esc_attr($filtervisible); ?>">
								<div class="eg-filter-label"><?php esc_html_e('Filter "All" Text', 'essential-grid'); ?></div>
								<div class="eg-filter-option-field eg-tooltip-wrap" title="<?php esc_attr_e('Visible Title for the ALL Filter Button', 'essential-grid'); ?>">
									<input type="text" name="filter-all-text<?php echo esc_attr($f_id); ?>" data-origname="filter-all-text-#NR" value="<?php echo esc_attr($filter_all_text); ?>" >
									<span class="eg-remove-filter-tab esg-display-none"><i class="eg-icon-cancel"></i></span>
								</div>
							</div>
		
							<div class="eg-filter-label"><?php esc_html_e('Layout Option', 'essential-grid'); ?></div>
							<div class="eg-filter-option-field">
								<select class="filter-listing-type" name="filter-listing<?php echo esc_attr($f_id); ?>" data-origname="filter-listing-#NR">
									<option value="list" <?php selected($filter_listing, 'list'); ?>><?php esc_html_e('In Line', 'essential-grid'); ?></option>
									<option value="dropdown" <?php selected($filter_listing, 'dropdown'); ?>><?php esc_html_e('Dropdown', 'essential-grid'); ?></option>
								</select>
							</div>
							<div class="filter-only-if-dropdown">
								<div class="eg-filter-label"><?php esc_html_e('Dropdown Start Text', 'essential-grid'); ?></div>
								<div class="eg-filter-option-field eg-tooltip-wrap" title="<?php esc_attr_e('Default Text on the Filter Dropdown List.', 'essential-grid'); ?>">
									<input type="text" name="filter-dropdown-text<?php echo esc_attr($f_id); ?>" data-origname="filter-dropdown-text-#NR" value="<?php echo esc_attr($filter_dropdown_text); ?>" />
								</div>
							</div>
							<div class="eg-filter-label"><?php esc_html_e('Show Number of Elements', 'essential-grid'); ?></div>
							<div class="eg-filter-option-field">
								<select name="filter-counter<?php echo esc_attr($f_id); ?>" data-origname="filter-counter-#NR">
									<option value="on" <?php selected($filter_counter, 'on'); ?>><?php esc_html_e('On', 'essential-grid'); ?></option>
									<option value="off" <?php selected($filter_counter, 'off'); ?>><?php esc_html_e('Off', 'essential-grid'); ?></option>
								</select>
							</div>
							<div class="eg-filter-label"><?php esc_html_e('Sort Filters Alphabetically', 'essential-grid'); ?></div>
							<div class="eg-filter-option-field">
								<select class="filter-sort-alpha" name="filter-sort-alpha<?php echo esc_attr($f_id); ?>" data-origname="filter-sort-alpha-#NR">
									<option value="on" <?php selected($filter_sort_alpha, 'on'); ?>><?php esc_html_e('On', 'essential-grid'); ?></option>
									<option value="off" <?php selected($filter_sort_alpha, 'off'); ?>><?php esc_html_e('Off', 'essential-grid'); ?></option>
								</select>
							</div>
							<div class="filter-only-if-sort">
								<div class="eg-filter-label"><?php esc_html_e('Sort Order', 'essential-grid'); ?></div>
								<div class="eg-filter-option-field">
									<select name="filter-sort-alpha-dir<?php echo esc_attr($f_id); ?>" data-origname="filter-sort-alpha-dir-#NR">
										<option value="asc" <?php selected($filter_sort_alpha_dir, 'asc'); ?>><?php esc_html_e('Ascending', 'essential-grid'); ?></option>
										<option value="desc" <?php selected($filter_sort_alpha_dir, 'desc'); ?>><?php esc_html_e('Descending', 'essential-grid'); ?></option>
									</select>
								</div>
							</div>
							<div class="eg-filter-label available-filters-in-group">
								<?php esc_html_e('Available Filters in Group', 'essential-grid'); ?>
								<div class="esg-f-right">
									<div class="eg-tooltip-wrap esg-display-inline-block esg-margin-r-5" title="<?php esc_attr_e('Select All', 'essential-grid'); ?>">
										<input data-items=".eg-filter-selected<?php echo esc_attr($f_id); ?>" class="eg-get-val eg-filter-input select-all-available-filters-in-group" type="checkbox" checked />
									</div>
									<div class="eg-tooltip-wrap esg-display-inline-block esg-margin-r-5" title="<?php esc_attr_e('Deselect All', 'essential-grid'); ?>">
										<input data-items=".eg-filter-selected<?php echo esc_attr($f_id); ?>" class="eg-get-val eg-filter-input deselect-all-available-filters-in-group" type="checkbox" />
									</div>
								</div>
							</div>
							<div class="esg-margin-t-10">
								<div class="eg-media-source-order-wrap eg-filter-selected-order-wrap<?php echo esc_attr($f_id); ?>">
								<?php foreach ($filter_selected as $fs) { ?>
									<div class="eg-media-source-order esg-blue esg-btn">
										<span><?php echo esc_html($fs); ?></span>
										<input class="eg-get-val eg-filter-input eg-filter-selected<?php echo esc_attr($f_id); ?>" type="checkbox" name="filter-selected<?php echo esc_attr($f_id); ?>[]" data-origname="filter-selected-#NR[]" checked="checked" value="<?php echo esc_attr($fs); ?>" />                              
									</div>
								<?php } ?>
								</div>
							</div>
							<div class="eg-filter-option-field eg-filter-option-top-m">
								<div class="eg-filter-add-custom-filter"><i class="eg-icon-plus"></i></div>
							</div>
						</div>
					<?php
					}
					?>
				</div>
			<div class="eg-add-filter-box"><i class="eg-icon-plus"></i></div>
			<script type="text/javascript">
				var eg_filter_counter = <?php echo esc_js($filter_counter); ?>,
					filter_startup = <?php echo $filter_startup ? 'true' : 'false'; ?>,
					eg_meta_handles = <?php echo (!empty($f_meta) ? wp_json_encode($f_meta) : '{}'); ?>,
					eg_filter_handles = {},
					eg_filter_handles_selected = <?php echo (!empty($custom_filter) ? wp_json_encode($custom_filter) : '{}'); ?>,
					eg_custom_filter_handles = {};

				//fill up custom filter dialog with entries
				jQuery('select[name="post_category"] option').each(function(){
					eg_filter_handles[jQuery(this).val()] = jQuery(this).text();
				});
			</script>
			<div class="esg-clearfix"></div>
			</div>
		</div>

		<div>
			<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Sorting', 'essential-grid'); ?></span></esg-llabel></div>
			<div class="eg-cs-tbc">
				<label for="sort-by-text" class="eg-tooltip-wrap" title="<?php esc_attr_e('Visible Sort By text on the sort dropdown.', 'essential-grid'); ?>"><?php esc_html_e('Sort By Text', 'essential-grid'); ?></label><!---
				--><input type="text" name="sort-by-text" class="esg-w-305" value="<?php echo esc_attr($base->getVar($grid, ['params', 'sort-by-text'], esc_attr__('Sort By ', 'essential-grid'))); ?>" >
				<div class="div13"></div>
				<label for="sorting-order-by" class="eg-tooltip-wrap" title="<?php esc_attr_e('Select Sorting Definitions (multiple available)', 'essential-grid'); ?>"><?php esc_html_e('Available Sortings', 'essential-grid'); ?></label><!--
				--><?php $order_by = explode(',', $base->getVar($grid, ['params', 'sorting-order-by'], 'date')); ?><select name="sorting-order-by" multiple="true" size="9" >
				<?php
				if (Essential_Grid_Woocommerce::is_woo_exists()) {
					$wc_sorts = Essential_Grid_Woocommerce::get_arr_sort_by();
					if (!empty($wc_sorts)) {
						foreach ($wc_sorts as $wc_handle => $wc_name) {
						?>
						<option value="<?php echo esc_attr($wc_handle); ?>"<?php selected(in_array($wc_handle, $order_by), true); ?><?php if(strpos($wc_handle, 'opt_disabled_') !== false) echo ' disabled="disabled"'; ?>><?php echo esc_html($wc_name); ?></option>
						<?php
						}
					}
				}
				?>
					<option value="date"<?php selected(in_array('date', $order_by), true); ?>><?php esc_html_e('Date', 'essential-grid'); ?></option>
					<option value="title"<?php selected(in_array('title', $order_by), true); ?>><?php esc_html_e('Title', 'essential-grid'); ?></option>
					<option value="excerpt"<?php selected(in_array('excerpt', $order_by), true); ?>><?php esc_html_e('Excerpt', 'essential-grid'); ?></option>
					<option value="id"<?php selected(in_array('id', $order_by), true); ?>><?php esc_html_e('ID', 'essential-grid'); ?></option>
					<option value="slug"<?php selected(in_array('slug', $order_by), true); ?>><?php esc_html_e('Slug', 'essential-grid'); ?></option>
					<option value="author"<?php selected(in_array('author', $order_by), true); ?>><?php esc_html_e('Author', 'essential-grid'); ?></option>
					<option value="last-modified"<?php selected(in_array('last-modified', $order_by), true); ?>><?php esc_html_e('Last modified', 'essential-grid'); ?></option>
					<option value="number-of-comments"<?php selected(in_array('number-of-comments', $order_by), true); ?>><?php esc_html_e('Number of comments', 'essential-grid'); ?></option>
					<option value="views"<?php selected(in_array('views', $order_by), true); ?>><?php esc_html_e('Views', 'essential-grid'); ?></option>
					<option value="likespost"<?php selected(in_array('likespost', $order_by), true); ?>><?php esc_html_e('Post Likes', 'essential-grid'); ?></option>
					<option value="likes"<?php selected(in_array('likes', $order_by), true); ?>><?php esc_html_e('Likes', 'essential-grid'); ?></option>
					<option value="dislikes"<?php selected(in_array('dislikes', $order_by), true); ?>><?php esc_html_e('Dislikes', 'essential-grid'); ?></option>
					<option value="retweets"<?php selected(in_array('retweets', $order_by), true); ?>><?php esc_html_e('Retweets', 'essential-grid'); ?></option>
					<option value="favorites"<?php selected(in_array('favorites', $order_by), true); ?>><?php esc_html_e('Favorites', 'essential-grid'); ?></option>
					<option value="duration"<?php selected(in_array('duration', $order_by), true); ?>><?php esc_html_e('Duration', 'essential-grid'); ?></option>
					<option value="itemCount"<?php selected(in_array('itemCount', $order_by), true); ?>><?php esc_html_e('Item Count', 'essential-grid'); ?></option>
				<?php
				if (!empty($all_metas)) {
					?>
					<option value="opt_disabled_99" disabled="disabled"><?php esc_html_e('---- Custom Metas ----', 'essential-grid'); ?></option>
					<?php
					foreach ($all_metas as $c_meta) {
						$type = ($c_meta['m_type'] == 'link') ? 'egl-' : 'eg-';
						?>
						<option value="<?php echo esc_attr($type . $c_meta['handle']); ?>"<?php selected(in_array($type.$c_meta['handle'], $order_by), true); ?>><?php echo esc_html($c_meta['name']);
						echo ($c_meta['m_type'] == 'link') ? ' (' .esc_attr__('Link', 'essential-grid').')' : ''; ?></option>
						<?php
					}
				}
				?>
				</select>
				
				<div class="div13"></div>
				<label for="sorting-order-by-start" class="eg-tooltip-wrap" title="<?php esc_attr_e('Sorting at Loading', 'essential-grid'); ?>"><?php esc_html_e('Start Sorting By', 'essential-grid'); ?></label><!--
				--><?php $order_by_start = $base->getVar($grid, ['params', 'sorting-order-by-start'], 'none'); ?><select class="esg-w-305" name="sorting-order-by-start" >
					<option value="none"<?php selected('none' == $order_by_start, true); ?>><?php esc_html_e('None', 'essential-grid'); ?></option>
					<?php
					if (Essential_Grid_Woocommerce::is_woo_exists()) {
						$wc_sorts = Essential_Grid_Woocommerce::get_arr_sort_by();
						if (!empty($wc_sorts)) {
							foreach ($wc_sorts as $wc_handle => $wc_name) {
							?>
							<option value="<?php echo esc_attr($wc_handle); ?>"<?php selected(in_array($wc_handle, $order_by), true); ?><?php if(strpos($wc_handle, 'opt_disabled_') !== false) echo ' disabled="disabled"'; ?>><?php echo esc_html($wc_name); ?></option>
							<?php
							}
						}
						}
						?>
					<option value="date"<?php selected('date' == $order_by_start, true); ?>><?php esc_html_e('Date', 'essential-grid'); ?></option>
					<option value="title"<?php selected('title' == $order_by_start, true); ?>><?php esc_html_e('Title', 'essential-grid'); ?></option>
					<option value="ID"<?php selected('ID' == $order_by_start, true); ?>><?php esc_html_e('ID', 'essential-grid'); ?></option>
					<option value="name"<?php selected('name' == $order_by_start, true); ?>><?php esc_html_e('Slug', 'essential-grid'); ?></option>
					<option value="author"<?php selected('author' == $order_by_start, true); ?>><?php esc_html_e('Author', 'essential-grid'); ?></option>
					<option value="modified"<?php selected('modified' == $order_by_start, true); ?>><?php esc_html_e('Last modified', 'essential-grid'); ?></option>
					<option value="comment_count"<?php selected('comment_count' == $order_by_start, true); ?>><?php esc_html_e('Number of comments', 'essential-grid'); ?></option>
					<option value="rand"<?php selected('rand' == $order_by_start, true); ?>><?php esc_html_e('Random', 'essential-grid'); ?></option>
					<option value="menu_order"<?php selected('menu_order' == $order_by_start, true); ?>><?php esc_html_e('Menu Order', 'essential-grid'); ?></option>
					<option value="views"<?php selected('views' == $order_by_start, true); ?>><?php esc_html_e('Views', 'essential-grid'); ?></option>
					<option value="likespost"<?php selected('likespost' == $order_by_start, true); ?>><?php esc_html_e('Post Likes', 'essential-grid'); ?></option>
					<option value="likes"<?php selected('likes' == $order_by_start, true); ?>><?php esc_html_e('Likes', 'essential-grid'); ?></option>
					<option value="dislikes"<?php selected('dislikes' == $order_by_start, true); ?>><?php esc_html_e('Dislikes', 'essential-grid'); ?></option>
					<option value="retweets"<?php selected('retweets' == $order_by_start, true); ?>><?php esc_html_e('Retweets', 'essential-grid'); ?></option>
					<option value="favorites"<?php selected('favorites' == $order_by_start, true); ?>><?php esc_html_e('Favorites', 'essential-grid'); ?></option>
					<option value="duration"<?php selected('duration' == $order_by_start, true); ?>><?php esc_html_e('Duration', 'essential-grid'); ?></option>
					<option value="itemCount"<?php selected('itemCount' == $order_by_start, true); ?>><?php esc_html_e('Item Count', 'essential-grid'); ?></option>
					<?php
					if (!empty($all_metas)) {
						?>
						<option value="opt_disabled_99" disabled="disabled"><?php esc_html_e('---- Custom Metas ----', 'essential-grid'); ?></option>
						<?php
						foreach ($all_metas as $c_meta) {
							$type = ($c_meta['m_type'] == 'link') ? 'egl-' : 'eg-';
							?>
							<option value="<?php echo esc_attr($type . $c_meta['handle']); ?>"<?php selected($type.$c_meta['handle'] == $order_by_start, true); ?>><?php echo esc_html($c_meta['name']);
							echo ($c_meta['m_type'] == 'link') ? ' (' .esc_attr__('Link', 'essential-grid').')' : ''; ?></option>
							<?php
						}
					}
					?>
				</select>
				<div class="eg-sorting-order-meta-wrap esg-display-none">
					<div class="div13"></div>
					<label for="sorting-order-by-start-meta" class="eg-tooltip-wrap" title="<?php esc_attr_e('Set meta handle here that will be used as start sorting', 'essential-grid'); ?>"><?php esc_html_e('Start Sorting By Meta', 'essential-grid'); ?></label><!--
					--><input type="text" class="esg-w-305" name="sorting-order-by-start-meta" value="<?php echo esc_attr($base->getVar($grid, ['params', 'sorting-order-by-start-meta'], '')); ?>" ><a class=" sort-meta-selector" href="javascript:void(0);"><i class="eg-icon-down-open"></i></a>
				</div>
				<div class="div13"></div>
				<label for="sorting-order-type" class="eg-tooltip-wrap" title="<?php esc_attr_e('Sorting Order at Loading', 'essential-grid'); ?>"><?php esc_html_e('Sorting Order', 'essential-grid'); ?></label><!--
				--><?php $order_by_type = $base->getVar($grid, ['params', 'sorting-order-type'], 'ASC'); ?><select name="sorting-order-type" class="esg-w-305">
					<option value="DESC"<?php selected('DESC' == $order_by_type, true); ?>><?php esc_html_e('Descending', 'essential-grid'); ?></option>
					<option value="ASC"<?php selected('ASC' == $order_by_type, true); ?>><?php esc_html_e('Ascending', 'essential-grid'); ?></option>
				</select>
			</div>
		</div>

		<div class=" search_settings">
			<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Search Settings', 'essential-grid'); ?></span></esg-llabel></div>
			<div class="eg-cs-tbc">
			<label for="search-text" class="eg-tooltip-wrap" title="<?php esc_attr_e('Placeholder text of input field', 'essential-grid'); ?>"><?php esc_html_e('Search Default Text', 'essential-grid'); ?></label><!--
			--><input type="text" class="esg-w-305" name="search-text" value="<?php echo esc_attr($base->getVar($grid, ['params', 'search-text'], esc_attr__('Search...', 'essential-grid'))); ?>" >
			</div>
		</div>

		</div>
	</div>

	<!-- LIGHTBOX SETTINGS -->
	<div id="esg-settings-lightbox-settings" class="esg-settings-container">
		<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Shown Media Orders', 'essential-grid'); ?></span></esg-llabel></div>
		<div class="eg-cs-tbc">
			<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Set the default order of Shown Content Source', 'essential-grid'); ?>"><?php esc_html_e('Set Source Order', 'essential-grid'); ?></label>                       
			<div id="lbo-list" class="eg-media-source-order-wrap eg-media-source-order-wrap-margin">
				<?php
				if (!empty($lb_source_order)) {
					foreach ($lb_source_order as $lb_handle) {
						if (!isset($lb_source_list[$lb_handle])) continue;
						?>
						<div id="lbo-<?php echo esc_attr($lb_handle); ?>" class="eg-media-source-order esg-blue esg-btn"><i class="eg-icon-<?php echo esc_attr($lb_source_list[$lb_handle]['type']); ?>"></i><span><?php echo esc_html($lb_source_list[$lb_handle]['name']); ?></span><input class="eg-get-val eg-lb-source-list" type="checkbox" name="lb-source-order[]" checked="checked" value="<?php echo esc_attr($lb_handle); ?>" /></div>
						<?php
						unset($lb_source_list[$lb_handle]);
					}
				}
		
				if (!empty($lb_source_list)) {
					foreach ($lb_source_list as $lb_handle => $lb_set) {
					?>
						<div id="lbo-<?php echo esc_attr($lb_handle); ?>" class="eg-media-source-order esg-purple esg-btn"><i class="eg-icon-<?php echo esc_attr($lb_set['type']); ?>"></i><span><?php echo esc_html($lb_set['name']); ?></span><input class="eg-get-val eg-lb-source-list" type="checkbox" name="lb-source-order[]" value="<?php echo esc_attr($lb_handle); ?>" /></div>
					<?php
					}
				}
				?>
			</div>
			<div><label></label><?php esc_html_e('First Ordered Poster Source will be loaded as default. If source not exist, next available Poster source in order will be taken', 'essential-grid'); ?></div>                        
		</div>
		
		<div>
			<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Lightbox Gallery', 'essential-grid'); ?></span></esg-llabel></div>
			<div class="eg-cs-tbc">
				<?php
				$lighbox_mode = $base->getVar($grid, ['params', 'lightbox-mode'], 'single');
				?>
				<label for="lightbox-mode" class="eg-tooltip-wrap" title="<?php esc_attr_e('Choose the Lightbox Mode', 'essential-grid'); ?>"><?php esc_html_e('Gallery Mode', 'essential-grid'); ?></label><!--
				--><select name="lightbox-mode" class="esg-w-305">
					<option value="single"<?php selected($lighbox_mode, 'single'); ?>><?php esc_html_e('Single Mode', 'essential-grid'); ?></option>
					<option value="all"<?php selected($lighbox_mode, 'all'); ?>><?php esc_html_e('All Items', 'essential-grid'); ?></option>
					<option value="filterall"<?php selected($lighbox_mode, 'filterall'); ?>><?php esc_html_e('Filter based all Pages', 'essential-grid'); ?></option>
					<option value="filterpage"<?php selected($lighbox_mode, 'filterpage'); ?>><?php esc_html_e('Filter based current Page', 'essential-grid'); ?></option>
					<option value="content"<?php selected($lighbox_mode, 'content'); ?>><?php esc_html_e('Content based', 'essential-grid'); ?></option>
					<option value="content-gallery"<?php selected($lighbox_mode, 'content-gallery'); ?>><?php esc_html_e('Content Gallery based', 'essential-grid'); ?></option>
					<?php if(Essential_Grid_Woocommerce::is_woo_exists()){ ?>
					<option value="woocommerce-gallery"<?php selected($lighbox_mode, 'woocommerce-gallery'); ?>><?php esc_html_e('WooCommerce Gallery', 'essential-grid'); ?></option>
					<?php } ?>
				</select>
				<div class="lightbox-mode-addition-wrapper<?php echo ($lighbox_mode == 'content' || $lighbox_mode == 'content-gallery' || $lighbox_mode == 'woocommerce-gallery') ? '' : ' esg-display-none'; ?>">
					<div class="div13"></div>
					<label for="lightbox-exclude-media"><?php esc_html_e('Exclude Original Media', 'essential-grid'); ?></label><!--
					--><input type="radio" name="lightbox-exclude-media" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-exclude-media'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Exclude original media from Source Order', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio" name="lightbox-exclude-media" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-exclude-media'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Include original media from Source Order', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
				</div>
				<div class="div13"></div>
				<label><?php esc_html_e('Group Name', 'essential-grid'); ?></label><?php $lightbox_deep_link = $base->getVar($grid, ['params', 'lightbox-deep-link'], 'group'); ?><input type="text" name="lightbox-deep-link" class="esg-w-305" value="<?php echo esc_attr($lightbox_deep_link); ?>" />
				<div class="div13"></div>
				<label class="eg-tooltip-wrap" title="<?php esc_attr_e('AutoPlay Media', 'essential-grid'); ?>"><?php esc_html_e('Videos AutoPlay', 'essential-grid'); ?></label><!--
				--><input type="radio" name="lightbox-videoautoplay" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-videoautoplay'], 'on'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Play videos automatically', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="lightbox-videoautoplay" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-videoautoplay'], 'on'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Do not play videos automatically', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
			</div>

			<div>
				<div>
					<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('UI Colors', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc esg-ui-colors-option-wrapper">

						<div class="esg-lightbox-override-ui-colors">
							<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Override default lightbox UI colors', 'essential-grid'); ?>"><?php esc_html_e('Override UI Colors', 'essential-grid'); ?></label><!--
							--><input type="radio" name="lightbox-override-ui-colors" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-override-ui-colors'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Use Default', 'essential-grid'); ?>"><?php esc_html_e('Use Default', 'essential-grid'); ?></span><div class="space18"></div><!--
							--><input type="radio" name="lightbox-override-ui-colors" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-override-ui-colors'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Override', 'essential-grid'); ?>"><?php esc_html_e('Override', 'essential-grid'); ?></span>
							<div class="div13"></div>
						</div>

						<div class="esg-lightbox-override-ui-colors-container esg-display-none">
							<label for="lightbox-overlay-bg-color" class="eg-tooltip-wrap" title="<?php esc_attr_e('Lightbox Overlay Background Color', 'essential-grid'); ?>"><?php esc_html_e('Overlay Color', 'essential-grid'); ?></label><!--
							--><input type="text" class="inputColorPicker" name="lightbox-overlay-bg-color" value="<?php echo esc_attr($base->getVar($grid, ['params', 'lightbox-overlay-bg-color'], 'rgba(30,30,30,0.9)')); ?>" />
							<div class="div13"></div>

							<label for="lightbox-ui-bg-color" class="eg-tooltip-wrap" title="<?php esc_attr_e('Lightbox Interface Elements Background Color', 'essential-grid'); ?>"><?php esc_html_e('UI Background Color', 'essential-grid'); ?></label><!--
							--><input type="text" class="inputColorPicker" name="lightbox-ui-bg-color" value="<?php echo esc_attr($base->getVar($grid, ['params', 'lightbox-ui-bg-color'], '#28303d')); ?>" />
							<div class="div13"></div>

							<label for="lightbox-ui-color" class="eg-tooltip-wrap" title="<?php esc_attr_e('Lightbox Interface Elements Color', 'essential-grid'); ?>"><?php esc_html_e('UI Color', 'essential-grid'); ?></label><!--
							--><input type="text" class="inputColorPicker" name="lightbox-ui-color" value="<?php echo esc_attr($base->getVar($grid, ['params', 'lightbox-ui-color'], '#ffffff')); ?>" />
							<div class="div13"></div>

							<label for="lightbox-ui-hover-bg-color" class="eg-tooltip-wrap" title="<?php esc_attr_e('Lightbox Interface Elements Hover Background Color', 'essential-grid'); ?>"><?php esc_html_e('UI Hover Background Color', 'essential-grid'); ?></label><!--
							--><input type="text" class="inputColorPicker" name="lightbox-ui-hover-bg-color" value="<?php echo esc_attr($base->getVar($grid, ['params', 'lightbox-ui-hover-bg-color'], '#000000')); ?>" />
							<div class="div13"></div>

							<label for="lightbox-ui-hover-color" class="eg-tooltip-wrap" title="<?php esc_attr_e('Lightbox Interface Elements Hover Color', 'essential-grid'); ?>"><?php esc_html_e('UI Hover Color', 'essential-grid'); ?></label><!--
							--><input type="text" class="inputColorPicker" name="lightbox-ui-hover-color" value="<?php echo esc_attr($base->getVar($grid, ['params', 'lightbox-ui-hover-color'], '#ffffff')); ?>" />
							<div class="div13"></div>

							<label for="lightbox-ui-text-color" class="eg-tooltip-wrap" title="<?php esc_attr_e('Lightbox Text Color', 'essential-grid'); ?>"><?php esc_html_e('Text Color', 'essential-grid'); ?></label><!--
							--><input type="text" class="inputColorPicker" name="lightbox-ui-text-color" value="<?php echo esc_attr($base->getVar($grid, ['params', 'lightbox-ui-text-color'], '#eeeeee')); ?>" />
							<div class="div13"></div>
						</div>
					</div>
				</div>
				
				<div>
					<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Title / Spacings', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc">
						<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Strip tags in title / description', 'essential-grid'); ?>"><?php esc_html_e('Strip Tags', 'essential-grid'); ?></label><!--
								--><input type="radio" name="lightbox-title-strip" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-title-strip'], 'on'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Display above the image', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
							--><input type="radio" name="lightbox-title-strip" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-title-strip'], 'on'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Display under the image', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
						<div class="div13"></div>

						<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Show title / description above or under the image in lightbox', 'essential-grid'); ?>"><?php esc_html_e('Position', 'essential-grid'); ?></label><!--
							--><input type="radio" name="lightbox-title-position" value="top" <?php checked($base->getVar($grid, ['params', 'lightbox-title-position'], 'bottom'), 'top'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Display above the image', 'essential-grid'); ?>"><?php esc_html_e('Top', 'essential-grid'); ?></span><div class="space18"></div><!--
							--><input type="radio" name="lightbox-title-position" value="bottom" <?php checked($base->getVar($grid, ['params', 'lightbox-title-position'], 'bottom'), 'bottom'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Display under the image', 'essential-grid'); ?>"><?php esc_html_e('Bottom', 'essential-grid'); ?></span>
						<div class="div13"></div>
						
						<div class="esg-lightbox-title">
							<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Display a title in the lightbox', 'essential-grid'); ?>"><?php esc_html_e('Show Title', 'essential-grid'); ?></label><!--
							--><input type="radio" name="lightbox-title" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-title'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Display Item Title', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
							--><input type="radio" name="lightbox-title" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-title'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Do not display Item Title', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>              
							<div class="div13"></div>
						</div>

						<div class="esg-lightbox-title-source esg-display-none">
							<?php $lightbox_title_source = $base->getVar($grid, ['params', 'lightbox-title-source'], 'title');?>
							<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Title Source', 'essential-grid'); ?>"><?php esc_html_e('Title Source', 'essential-grid'); ?></label><!--
							--><select name="lightbox-title-source" class="esg-w-305">
								<optgroup label="<?php esc_attr_e('Grid Item', 'essential-grid'); ?>">
									<option value="title"<?php selected($lightbox_title_source, 'title'); ?>><?php esc_html_e('Title', 'essential-grid'); ?></option>
									<option value="caption"<?php selected($lightbox_title_source, 'caption'); ?>><?php esc_html_e('Caption ( for custom items )', 'essential-grid'); ?></option>
								</optgroup>
								<optgroup label="<?php esc_attr_e('Grid Item Media', 'essential-grid'); ?>">
									<option value="media_title"<?php selected($lightbox_title_source, 'media_title'); ?>><?php esc_html_e('Title', 'essential-grid'); ?></option>
									<option value="media_caption"<?php selected($lightbox_title_source, 'media_caption'); ?>><?php esc_html_e('Caption', 'essential-grid'); ?></option>
									<option value="media_alt"<?php selected($lightbox_title_source, 'media_alt'); ?>><?php esc_html_e('Alt', 'essential-grid'); ?></option>
								</optgroup>
							</select><!--
							--><div class="div13"></div>
						</div>

						<div class="esg-lightbox-description">
							<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Display a description in the lightbox', 'essential-grid'); ?>"><?php esc_html_e('Show Description', 'essential-grid'); ?></label><!--
							--><input type="radio" name="lightbox-description" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-description'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Display Item Title', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
							--><input type="radio" name="lightbox-description" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-description'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Do not display Item Title', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>              
							<div class="div13"></div>
						</div>

						<div class="esg-lightbox-description-source esg-display-none">
							<?php $lightbox_description_source = $base->getVar($grid, ['params', 'lightbox-description-source'], 'description');?>
							<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Description Source', 'essential-grid'); ?>"><?php esc_html_e('Description Source', 'essential-grid'); ?></label><!--
							--><select name="lightbox-description-source" class="esg-w-305">
								<optgroup label="<?php esc_attr_e('Grid Item', 'essential-grid'); ?>">
									<option value="description"<?php selected($lightbox_description_source, 'description'); ?>><?php esc_html_e('Description ( for custom items )', 'essential-grid'); ?></option>
									<option value="post_excerpt"<?php selected($lightbox_description_source, 'post_excerpt'); ?>><?php esc_html_e('Post Excerpt', 'essential-grid'); ?></option>
									<option value="post_content"<?php selected($lightbox_description_source, 'post_content'); ?>><?php esc_html_e('Post Content ( max. 140 characters )', 'essential-grid'); ?></option>
								</optgroup>
								<optgroup label="<?php esc_attr_e('Grid Item Media', 'essential-grid'); ?>">
									<option value="media_alt"<?php selected($lightbox_description_source, 'media_alt'); ?>><?php esc_html_e('Alt', 'essential-grid'); ?></option>
									<option value="media_description"<?php selected($lightbox_description_source, 'media_description'); ?>><?php esc_html_e('Description', 'essential-grid'); ?></option>
								</optgroup>
							</select><!--
							--><div class="div13"></div>
						</div>
						
						<?php
						$lbox_padding = $base->getVar($grid, ['params', 'lbox-padding'], '0');
						if (!is_array($lbox_padding)) $lbox_padding = ['0', '0', '0', '0'];
						?>
						<label for="lbox-padding"><?php esc_html_e('Item Margin', 'essential-grid'); ?></label><!--
						--><span class="esg-display-inline-block"><input class="input-settings-small eg-tooltip-wrap" title="<?php esc_attr_e('Padding Top of the LightBox', 'essential-grid'); ?>" type="text" name="lbox-padding[]" value="<?php echo esc_attr($base->getVar($lbox_padding, 0)); ?>" /> px</span><div class="space18"></div><!--
						--><span class="esg-display-inline-block"><input class="input-settings-small eg-tooltip-wrap" title="<?php esc_attr_e('Padding Right of the LightBox', 'essential-grid'); ?>" type="text" name="lbox-padding[]" value="<?php echo esc_attr($base->getVar($lbox_padding, 1)); ?>" /> px</span><div class="space18"></div><!--
						--><span class="esg-display-inline-block"><input class="input-settings-small eg-tooltip-wrap" title="<?php esc_attr_e('Padding Bottom of the LightBox', 'essential-grid'); ?>" type="text" name="lbox-padding[]" value="<?php echo esc_attr($base->getVar($lbox_padding, 2)); ?>" /> px</span><div class="space18"></div><!--
						--><span class="esg-display-inline-block"><input class="input-settings-small eg-tooltip-wrap" title="<?php esc_attr_e('Padding Left of the LightBox', 'essential-grid'); ?>" type="text" name="lbox-padding[]" value="<?php echo esc_attr($base->getVar($lbox_padding, 3)); ?>" /> px</span>
					</div>
				</div>
				<div>
					<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Effects', 'essential-grid'); ?></span></esg-llabel></div>
					<div class="eg-cs-tbc">
						<label for="" class="eg-tooltip-wrap" title="<?php esc_attr_e('Animation Type', 'essential-grid'); ?>,<?php esc_html_e('Animation Speed', 'essential-grid'); ?>"><?php esc_html_e('Open / Close Animation', 'essential-grid'); ?></label><!--
						--><select name="lightbox-effect-open-close" class="esg-lighbox-effect-select">
							<option value="false"<?php selected($base->getVar($grid, ['params', 'lightbox-effect-open-close'], 'fade'), 'false'); ?>><?php esc_html_e('None', 'essential-grid'); ?></option>
							<option value="fade"<?php selected($base->getVar($grid, ['params', 'lightbox-effect-open-close'], 'fade'), 'fade'); ?>><?php esc_html_e('Fade', 'essential-grid'); ?></option>
							<option value="zoom-in-out"<?php selected($base->getVar($grid, ['params', 'lightbox-effect-open-close'], 'fade'), 'zoom-in-out'); ?>><?php esc_html_e('Zoom In Out', 'essential-grid'); ?></option>
						</select><!--
						--><?php
						$lightbox_effect_open_close_speed = $base->getVar($grid, ['params', 'lightbox-effect-open-close-speed'], '500');
						if (!is_numeric($lightbox_effect_open_close_speed)) $lightbox_effect_open_close_speed = '500';
						?><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Animation Speed', 'essential-grid'); ?>">Speed</span><div class="space18"></div><input class="input-settings-small" type="text" name="lightbox-effect-open-close-speed" value="<?php echo esc_attr($lightbox_effect_open_close_speed); ?>" /> ms
						<div class="div13"></div>
						<label for="" class="eg-tooltip-wrap" title="<?php esc_attr_e('Transition Type', 'essential-grid'); ?>,<?php esc_html_e('Transition Speed', 'essential-grid'); ?>"><?php esc_html_e('Next / Prev Animation', 'essential-grid'); ?></label><!--
						--><select name="lightbox-effect-next-prev" class="esg-lighbox-effect-select">
							<option value="none"<?php selected($base->getVar($grid, ['params', 'lightbox-effect-next-prev'], 'fade'), 'false'); ?>><?php esc_html_e('None', 'essential-grid'); ?></option>
							<option value="fade"<?php selected($base->getVar($grid, ['params', 'lightbox-effect-next-prev'], 'fade'), 'fade'); ?>><?php esc_html_e('Fade', 'essential-grid'); ?></option>
							<option value="slide"<?php selected($base->getVar($grid, ['params', 'lightbox-effect-next-prev'], 'fade'), 'slide'); ?>><?php esc_html_e('Slide', 'essential-grid'); ?></option>
							<option value="circular"<?php selected($base->getVar($grid, ['params', 'lightbox-effect-next-prev'], 'fade'), 'circular'); ?>><?php esc_html_e('Circular', 'essential-grid'); ?></option>
							<option value="tube"<?php selected($base->getVar($grid, ['params', 'lightbox-effect-next-prev'], 'fade'), 'tube'); ?>><?php esc_html_e('Tube', 'essential-grid'); ?></option>
							<option value="zoom-in-out"<?php selected($base->getVar($grid, ['params', 'lightbox-effect-next-prev'], 'fade'), 'zoom-in-out'); ?>><?php esc_html_e('Zoom In Out', 'essential-grid'); ?></option>
							<option value="rotate"<?php selected($base->getVar($grid, ['params', 'lightbox-effect-next-prev'], 'fade'), 'rotate'); ?>><?php esc_html_e('Rotate', 'essential-grid'); ?></option>
						</select><!--
						--><?php
							$lightbox_effect_next_prev_speed = $base->getVar($grid, ['params', 'lightbox-effect-next-prev-speed'], '500');
							if(!is_numeric($lightbox_effect_next_prev_speed)) $lightbox_effect_next_prev_speed = '500';
						?><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Transition Speed', 'essential-grid'); ?>">Speed</span><div class="space18"></div><input class="input-settings-small" type="text" name="lightbox-effect-next-prev-speed" value="<?php echo esc_attr($lightbox_effect_next_prev_speed); ?>" /> ms             
					</div>
				</div>
			</div>
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Auto Rotate', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc">
					<label><?php esc_html_e('Auto Rotate Mode', 'essential-grid'); ?></label><!--
					--><input type="radio" name="lightbox-autoplay" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-autoplay'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('AutoPlay Elements in Lightbox.', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio" name="lightbox-autoplay" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-autoplay'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Dont AutoPlay Elements in LightBox.', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
					<div class="div13"></div>
					<label><?php esc_html_e('Auto Rotate Delays:', 'essential-grid'); ?></label><!--
					--><input type="text" name="lbox-playspeed" value="<?php echo esc_attr($base->getVar($grid, ['params', 'lbox-playspeed'], '3000')); ?>">
				</div>
			</div>
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Slideshow', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc">
				<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Show Navigation Arrows.', 'essential-grid'); ?>"><?php esc_html_e('Navigation Arrows', 'essential-grid'); ?></label><!--
				--><span class="esg-display-inline-block"><input type="radio" name="lightbox-arrows" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-arrows'], 'on'), 'on'); ?>><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><span><input type="radio" name="lightbox-arrows" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-arrows'], 'on'), 'off'); ?>><?php esc_html_e('Off', 'essential-grid'); ?></span>
				<div class="div13"></div>
				<label><?php esc_html_e('Loop Items', 'essential-grid'); ?></label><!--
				--><input type="radio" name="lightbox-loop" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-loop'], 'on'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Loop items after last is shown.', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="lightbox-loop" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-loop'], 'on'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Do not loop items.', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
				<div class="div13"></div>
				<label><?php esc_html_e('Item Numbers', 'essential-grid'); ?></label><!--
				--><input type="radio" name="lightbox-numbers" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-numbers'], 'on'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Show numbers such as 1-8, etc.', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="lightbox-numbers" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-numbers'], 'on'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Do not display numbers', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
				<div class="div13"></div>
				<label><?php esc_html_e('Mouse Wheel', 'essential-grid'); ?></label><!--
				--><input type="radio" name="lightbox-mousewheel" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-mousewheel'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Enable mouse wheel to change items when lightbox is open', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
				--><input type="radio" name="lightbox-mousewheel" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-mousewheel'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Do not use mouse wheel', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>           
				</div>
			</div>
	
			<!-- begin buttons -->
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Toolbar Buttons', 'essential-grid'); ?></span></esg-llabel></div>         
				<div class="eg-cs-tbc">
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Choose which buttons to display and set their order', 'essential-grid'); ?>"><?php esc_html_e('Set Button Order', 'essential-grid'); ?></label>         
					<div id="lbo-btn-list" class="eg-media-source-order-wrap">
						<?php
						if (!empty($lb_button_order)) {
							foreach ($lb_button_order as $lb_handle) {
								if(!isset($lb_button_list[$lb_handle])) continue;
								?>
								<div id="lbo-<?php echo esc_attr($lb_handle); ?>" class="eg-media-source-order esg-blue esg-btn"><i class="eg-icon-<?php echo esc_attr($lb_button_list[$lb_handle]['type']); ?>"></i><span><?php echo esc_html($lb_button_list[$lb_handle]['name']); ?></span><input class="eg-get-val" type="checkbox" name="lb-button-order[]" checked="checked" value="<?php echo esc_attr($lb_handle); ?>" /></div>
								<?php
								unset($lb_button_list[$lb_handle]);
							}
						}
			
						if (!empty($lb_button_list)) {
							foreach ($lb_button_list as $lb_handle => $lb_set) {
							?>
								<div id="lbo-button-<?php echo esc_attr($lb_handle); ?>" class="eg-media-source-order esg-purple esg-btn"><i class="eg-icon-<?php echo esc_attr($lb_set['type']); ?>"></i><span ><?php echo esc_html($lb_set['name']); ?></span><input class="eg-get-val" type="checkbox" name="lb-button-order[]" value="<?php echo esc_attr($lb_handle); ?>" /></div>
							<?php
							}
						}
						?>
					</div>
				</div>
			</div><!-- end buttons -->
	
			<div id="eg-post-content-options">
			  <!-- 2.1.6 -->
				<div class="eg-creative-settings">
				<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Post Content', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc">
					<label for="lightbox-post-content-min-width" class="eg-tooltip-wrap" title="<?php esc_attr_e('percentage or pixel based', 'essential-grid'); ?>"><?php esc_html_e('Content Min Width', 'essential-grid'); ?></label>
					<input class="input-settings-small " type="text" name="lightbox-post-content-min-width" value="<?php echo esc_attr($base->getVar($grid, ['params', 'lightbox-post-content-min-width'], '75')); ?>" />
					<input type="radio" name="lightbox-post-content-min-perc" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-post-content-min-perc'], 'on'), 'on'); ?>> <?php esc_html_e('%', 'essential-grid'); ?><div class="space18"></div>
					<input type="radio" name="lightbox-post-content-min-perc" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-post-content-min-perc'], 'on'), 'off'); ?>> <?php esc_html_e('px', 'essential-grid'); ?>
					<div class="div13"></div>
					<label for="lightbox-post-content-max-width" class="eg-tooltip-wrap" title="<?php esc_attr_e('percentage or pixel based', 'essential-grid'); ?>"><?php esc_html_e('Content Max Width', 'essential-grid'); ?></label>
					<input class="input-settings-small " type="text" name="lightbox-post-content-max-width" value="<?php echo esc_attr($base->getVar($grid, ['params', 'lightbox-post-content-max-width'], '75')); ?>" />
					<input type="radio" name="lightbox-post-content-max-perc" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-post-content-max-perc'], 'on'), 'on'); ?>> <?php esc_html_e('%', 'essential-grid'); ?><div class="space18"></div>
					<input type="radio" name="lightbox-post-content-max-perc" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-post-content-max-perc'], 'on'), 'off'); ?>> <?php esc_html_e('px', 'essential-grid'); ?>
					<div class="div13"></div>
					<label for="lightbox-post-content-overflow" class="eg-tooltip-wrap" title="<?php esc_attr_e('allow content scrolling', 'essential-grid'); ?>"><?php esc_html_e('Content Overflow', 'essential-grid'); ?></label>
					<input type="radio" name="lightbox-post-content-overflow" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-post-content-overflow'], 'on'), 'on'); ?>> <?php esc_html_e('On', 'essential-grid'); ?><div class="space18"></div>
					<input type="radio" name="lightbox-post-content-overflow" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-post-content-overflow'], 'on'), 'off'); ?>> <?php esc_html_e('Off', 'essential-grid'); ?>
					<div class="div13"></div>
					<?php
					$lbox_post_img_padding = $base->getVar($grid, ['params', 'lbox-content_padding'], '0');
					if (!is_array($lbox_post_img_padding)) $lbox_post_img_padding = ['0', '0', '0', '0'];
					?>
					<label><?php esc_html_e('Post Content Padding', 'essential-grid'); ?></label>
					<span class="eg-tooltip-wrap" title="<?php esc_attr_e('Padding Top (px)', 'essential-grid'); ?>">Top</span> <input class="input-settings-small esg-margin-r-10" type="text" name="lbox-content_padding[]" value="<?php echo esc_attr($base->getVar($lbox_post_img_padding, 0)); ?>" />
					<span class="eg-tooltip-wrap" title="<?php esc_attr_e('Padding Right (px)', 'essential-grid'); ?>">Right</span> <input class="input-settings-small esg-margin-r-10" type="text" name="lbox-content_padding[]" value="<?php echo esc_attr($base->getVar($lbox_post_img_padding, 1)); ?>" />
					<span class="eg-tooltip-wrap" title="<?php esc_attr_e('Padding Bottom (px)', 'essential-grid'); ?>">Bottom</span> <input class="input-settings-small esg-margin-r-10" type="text" name="lbox-content_padding[]" value="<?php echo esc_attr($base->getVar($lbox_post_img_padding, 2)); ?>" />
					<span class="eg-tooltip-wrap" title="<?php esc_attr_e('Padding Left (px)', 'essential-grid'); ?>">Left</span> <input class="input-settings-small esg-margin-r-10" type="text" name="lbox-content_padding[]" value="<?php echo esc_attr($base->getVar($lbox_post_img_padding, 3)); ?>" />
					<div class="div13"></div>
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Show spinner preloader on item while content loads', 'essential-grid'); ?>"><?php esc_html_e('Show Preloader', 'essential-grid'); ?></label>
					<input type="radio" name="lightbox-post-spinner" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-post-spinner'], 'off'), 'on'); ?>> <?php esc_html_e('On', 'essential-grid'); ?><div class="space18"></div>
					<input type="radio" name="lightbox-post-spinner" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-post-spinner'], 'off'), 'off'); ?>> <?php esc_html_e('Off', 'essential-grid'); ?>
					<div class="div13"></div>
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Include Featured Image from Post', 'essential-grid'); ?>"><?php esc_html_e('Include Featured Image', 'essential-grid'); ?></label>
					<input type="radio" class="lightbox-post-content-img " name="lightbox-post-content-img" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-post-content-img'], 'off'), 'on'); ?>> <?php esc_html_e('On', 'essential-grid'); ?><div class="space18"></div>
					<input type="radio" class="lightbox-post-content-img" name="lightbox-post-content-img" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-post-content-img'], 'off'), 'off'); ?>> <?php esc_html_e('Off', 'essential-grid'); ?>
					<div class="featured-img-hideable">
						<div class="div13"></div>
						<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Featured Image position in relation to the content', 'essential-grid'); ?>"><?php esc_html_e('Featured Image Position', 'essential-grid'); ?></label>
						<select id="lightbox-post-content-img-position" name="lightbox-post-content-img-position">
							<option value="top"<?php selected($base->getVar($grid, ['params', 'lightbox-post-content-img-position'], 'top'), 'top'); ?>><?php esc_html_e('Top', 'essential-grid'); ?></option>
							<option value="right"<?php selected($base->getVar($grid, ['params', 'lightbox-post-content-img-position'], 'top'), 'right'); ?>><?php esc_html_e('Right', 'essential-grid'); ?></option>
							<option value="bottom"<?php selected($base->getVar($grid, ['params', 'lightbox-post-content-img-position'], 'top'), 'bottom'); ?>><?php esc_html_e('Bottom', 'essential-grid'); ?></option>
							<option value="left"<?php selected($base->getVar($grid, ['params', 'lightbox-post-content-img-position'], 'top'), 'left'); ?>><?php esc_html_e('Left', 'essential-grid'); ?></option>
						</select>
					</div>
					<?php
					$lbMaxWidthDisplay = $base->getVar($grid, ['params', 'lightbox-post-content-img-position'], 'top');
					$lbMaxWidthDisplay = $lbMaxWidthDisplay === 'left' || $lbMaxWidthDisplay === 'right' ? 'block' : 'none';
					?>
					<div class="featured-img-hideable">
						<div id="lightbox-post-content-img-width" class="esg-display-<?php echo esc_attr($lbMaxWidthDisplay); ?>">
							<div class="div13"></div>
							<label for="lightbox-post-content-img-width" class="eg-tooltip-wrap" title="<?php esc_attr_e('Percentage based on Lightbox Default Width above', 'essential-grid'); ?>"><?php esc_html_e('Featured Image Max Width', 'essential-grid'); ?></label>
							<input class="input-settings-small " type="text" name="lightbox-post-content-img-width" value="<?php echo esc_attr($base->getVar($grid, ['params', 'lightbox-post-content-img-width'], '50')); ?>" /> %
						</div>
					</div>
					<div class="featured-img-hideable">
						<div class="div13"></div>
						<?php
						$lbox_post_img_margin = $base->getVar($grid, ['params', 'lightbox-post-content-img-margin'], '0');
						if (!is_array($lbox_post_img_margin)) $lbox_post_img_margin = ['0', '0', '0', '0'];
						?>
						<label for="lightbox-post-content-img-margin"><?php esc_html_e('Featured Image Margin', 'essential-grid'); ?></label>
						<span class="eg-tooltip-wrap" title="<?php esc_attr_e('Margin Top of the Featured Image (px)', 'essential-grid'); ?>">Top</span> <input class="input-settings-small esg-margin-r-10" type="text" name="lightbox-post-content-img-margin[]" value="<?php echo esc_attr($base->getVar($lbox_post_img_margin, 0)); ?>" />
						<span class="eg-tooltip-wrap" title="<?php esc_attr_e('Margin Right of the Featured Image (px)', 'essential-grid'); ?>">Right</span> <input class="input-settings-small esg-margin-r-10" type="text" name="lightbox-post-content-img-margin[]" value="<?php echo esc_attr($base->getVar($lbox_post_img_margin, 1)); ?>" />
						<span class="eg-tooltip-wrap" title="<?php esc_attr_e('Margin Bottom of the Featured Image (px)', 'essential-grid'); ?>">Bottom</span> <input class="input-settings-small esg-margin-r-10" type="text" name="lightbox-post-content-img-margin[]" value="<?php echo esc_attr($base->getVar($lbox_post_img_margin, 2)); ?>" />
						<span class="eg-tooltip-wrap" title="<?php esc_attr_e('Margin Left of the Featured Image (px)', 'essential-grid'); ?>">Left</span> <input class="input-settings-small esg-margin-r-10" type="text" name="lightbox-post-content-img-margin[]" value="<?php echo esc_attr($base->getVar($lbox_post_img_margin, 3)); ?>" />
					</div>
					<div class="div13"></div>
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Include Post Title Before Content', 'essential-grid'); ?>"><?php esc_html_e('Prepend Post Title', 'essential-grid'); ?></label>
					<input type="radio" name="lightbox-post-content-title" value="on" <?php checked($base->getVar($grid, ['params', 'lightbox-post-content-title'], 'off'), 'on'); ?>> <?php esc_html_e('On', 'essential-grid'); ?><div class="space18"></div>
					<input type="radio" name="lightbox-post-content-title" value="off" <?php checked($base->getVar($grid, ['params', 'lightbox-post-content-title'], 'off'), 'off'); ?>> <?php esc_html_e('Off', 'essential-grid'); ?>
					<div class="div13"></div>
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('The tag for the Post Title', 'essential-grid'); ?>"><?php esc_html_e('Post Title HTML Tag', 'essential-grid'); ?></label>
					<select name="lightbox-post-content-title-tag">
						<option value="h1"<?php selected($base->getVar($grid, ['params', 'lightbox-post-content-title-tag'], 'h2'), 'h1'); ?>><?php esc_html_e('h1', 'essential-grid'); ?></option>
						<option value="h2"<?php selected($base->getVar($grid, ['params', 'lightbox-post-content-title-tag'], 'h2'), 'h2'); ?>><?php esc_html_e('h2', 'essential-grid'); ?></option>
						<option value="h3"<?php selected($base->getVar($grid, ['params', 'lightbox-post-content-title-tag'], 'h2'), 'h3'); ?>><?php esc_html_e('h3', 'essential-grid'); ?></option>
						<option value="h4"<?php selected($base->getVar($grid, ['params', 'lightbox-post-content-title-tag'], 'h2'), 'h4'); ?>><?php esc_html_e('h4', 'essential-grid'); ?></option>
						<option value="p"<?php selected($base->getVar($grid, ['params', 'lightbox-post-content-title-tag'], 'h2'), 'p'); ?>><?php esc_html_e('p', 'essential-grid'); ?></option>
					</select>
				</div><!-- END of esg-cs-tbc -->
				</div><!-- END of eg-creative-settings -->
			</div><!-- END of eg-post-content-options -->   
		</div><!-- END of eg-hide-if-social-gallery-is-enabled -->
	</div><!-- END esg-settings-lightbox-settings -->

	<!-- AJAX SETTINGS -->
	<div id="esg-settings-ajax-settings" class="esg-settings-container">
		<div>
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Shown Ajax Orders', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc eg-cs-tbc-padding-top">
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Set the default order of Shown Content at ajax loading', 'essential-grid'); ?>"><?php esc_html_e('Set Source Order', 'essential-grid'); ?></label>
					<div id="ajo-list" class="eg-media-source-order-wrap">
					<?php
					if (!empty($aj_source_order)) {
						foreach ($aj_source_order as $aj_handle) {
							if (!isset($aj_source_list[$aj_handle])) continue;
							?>
							<div id="ajo-<?php echo esc_attr($aj_handle); ?>" class="eg-media-source-order esg-blue esg-btn"><i class="eg-icon-<?php echo esc_attr($aj_source_list[$aj_handle]['type']); ?>"></i><span><?php echo esc_html($aj_source_list[$aj_handle]['name']); ?></span><input class="eg-get-val" type="checkbox" name="aj-source-order[]" checked="checked" value="<?php echo esc_attr($aj_handle); ?>" /></div>
							<?php
							unset($aj_source_list[$aj_handle]);
						}
					}
		
					if (!empty($aj_source_list)) {
						foreach ($aj_source_list as $aj_handle => $aj_set) {
						 ?>
							<div id="ajo-<?php echo esc_attr($aj_handle); ?>" class="eg-media-source-order esg-purple esg-btn"><i class="eg-icon-<?php echo esc_attr($aj_set['type']); ?>"></i><span><?php echo esc_html($aj_set['name']); ?></span><input class="eg-get-val" type="checkbox" name="aj-source-order[]" value="<?php echo esc_attr($aj_handle); ?>" /></div>
						<?php
						}
					}
					?>
					</div>
					<div><?php esc_html_e('First Ordered Source will be loaded as default. If source not exist, next available source in order will be taken', 'essential-grid'); ?></div>                       
				</div>
			</div>
			
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Ajax Container', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc">
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Define ID of the container (without #)', 'essential-grid'); ?>, <?php esc_html_e('Insert a valid CSS ID here', 'essential-grid'); ?>"><?php esc_html_e('Container ID', 'essential-grid'); ?></label><!--
					--><input type="text" name="ajax-container-id" class="esg-w-305" value="<?php echo esc_attr($base->getVar($grid, ['params', 'ajax-container-id'], 'ess-grid-ajax-container-')); ?>" >
					<div class="div13"></div>
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Define the position of the ajax content container', 'essential-grid'); ?>"><?php esc_html_e('Container Position', 'essential-grid'); ?></label><!--
					--><input type="radio" name="ajax-container-position" value="top" <?php checked($base->getVar($grid, ['params', 'ajax-container-position'], 'top'), 'top'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Visible above the Grid', 'essential-grid'); ?>"><?php esc_html_e('Top', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio" name="ajax-container-position" value="bottom" <?php checked($base->getVar($grid, ['params', 'ajax-container-position'], 'top'), 'bottom'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Visible under the Grid', 'essential-grid'); ?>"><?php esc_html_e('Bottom', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio" name="ajax-container-position" value="shortcode" <?php checked($base->getVar($grid, ['params', 'ajax-container-position'], 'top'), 'shortcode'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Insert somewhere as ShortCode', 'essential-grid'); ?>"><?php esc_html_e('As ShortCode', 'essential-grid'); ?></span>           
					<div id="eg-ajax-shortcode-wrapper">
						<div class="div13"></div>
						<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Use this ShortCode somewhere on the page to insert the ajax content container', 'essential-grid'); ?>"><?php esc_html_e('Container ShortCode', 'essential-grid'); ?></label><!--
						--><input type="text" readonly="readonly" value="" name="ajax-container-shortcode" class="esg-w-305">
					</div>
					<div class="div13"></div>
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Define if browser should scroll to content after it is loaded via ajax', 'essential-grid'); ?>"><?php esc_html_e('Scroll on load', 'essential-grid'); ?></label><!--
					--><input type="radio" name="ajax-scroll-onload" value="on" <?php checked($base->getVar($grid, ['params', 'ajax-scroll-onload'], 'on'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Scroll to content.', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio" name="ajax-scroll-onload" value="off" <?php checked($base->getVar($grid, ['params', 'ajax-scroll-onload'], 'on'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Do not scroll to content.', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
					<div class="div13"></div>
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Define offset of scrolling in px (-500 - 500)', 'essential-grid'); ?>"><?php esc_html_e('Scroll Offset', 'essential-grid'); ?></label><!--
					--><input type="text" name="ajax-scrollto-offset" value="<?php echo esc_attr($base->getVar($grid, ['params', 'ajax-scrollto-offset'], '0')); ?>" >
				</div>
			</div>

			<div>
				<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Ajax Navigation', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc">
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Define the content container should have a close button', 'essential-grid'); ?>"><?php esc_html_e('Show Close Button', 'essential-grid'); ?></label><!--
					--><span class="esg-display-inline-block"><input type="radio" name="ajax-close-button" value="on" class=" eg-tooltip-wrap" <?php checked($base->getVar($grid, ['params', 'ajax-close-button'], 'off'), 'on'); ?>><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><span class="esg-display-inline-block"><input type="radio" name="ajax-close-button" value="off" class="eg-tooltip-wrap" <?php checked($base->getVar($grid, ['params', 'ajax-close-button'], 'off'), 'off'); ?>><?php esc_html_e('Off', 'essential-grid'); ?></span>
					
					<div class="eg-close-button-settings-wrap">
						<div class="eg-button-text-wrap">
							<div class="div13"></div>
							<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Define the button text here', 'essential-grid'); ?>"><?php esc_html_e('Close Button Text', 'essential-grid'); ?></label><!--
							--><input type="text" name="ajax-button-text" value="<?php echo esc_attr($base->getVar($grid, ['params', 'ajax-button-text'], esc_attr__('Close', 'essential-grid'))); ?>">
						</div>
					</div>
		
					<div class="div13"></div>
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Define the content container should have navigation buttons', 'essential-grid'); ?>"><?php esc_html_e('Show Navigation Button', 'essential-grid'); ?></label><!--
					--><span class="esg-display-inline-block"><input type="radio" name="ajax-nav-button" value="on" class=" eg-tooltip-wrap" <?php checked($base->getVar($grid, ['params', 'ajax-nav-button'], 'off'), 'on'); ?>><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><span class="esg-display-inline-block"><input type="radio" name="ajax-nav-button" value="off" <?php checked($base->getVar($grid, ['params', 'ajax-nav-button'], 'off'), 'off'); ?>><?php esc_html_e('Off', 'essential-grid'); ?></span>
					
					<div class="eg-close-nav-button-settings-wrap">
						<div class="div13"></div>
						<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Define the Skin of the buttons', 'essential-grid'); ?>"><?php esc_html_e('Button Skin', 'essential-grid'); ?></label><!--
						--><select name="ajax-button-skin" class="eg-tooltip-wrap" title="<?php esc_attr_e('Define the Skin of the buttons', 'essential-grid'); ?>">
							<option value="light"<?php selected($base->getVar($grid, ['params', 'ajax-button-skin'], 'light'), 'light'); ?>><?php esc_html_e('Light', 'essential-grid'); ?></option>
							<option value="dark"<?php selected($base->getVar($grid, ['params', 'ajax-button-skin'], 'light'), 'dark'); ?>><?php esc_html_e('Dark', 'essential-grid'); ?></option>
						</select>
						<div class="div13"></div>
						<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Switch between button or text', 'essential-grid'); ?>"><?php esc_html_e('Button Type', 'essential-grid'); ?></label><!--
						--><input type="radio" name="ajax-button-type" value="type1" class=" eg-tooltip-wrap" <?php checked($base->getVar($grid, ['params', 'ajax-button-type'], 'type1'), 'type1'); ?>><?php esc_html_e('Type 1', 'essential-grid'); ?><div class="space18"></div><!--
						--><input type="radio" name="ajax-button-type" value="type2" <?php checked($base->getVar($grid, ['params', 'ajax-button-type'], 'type1'), 'type2'); ?>><?php esc_html_e('Type 2', 'essential-grid'); ?>
						<div class="div13"></div>
						<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Define if the button should be visible inside of the ajax container or outside of it', 'essential-grid'); ?>"><?php esc_html_e('Button Container Pos.', 'essential-grid'); ?></label><!--
						--><input type="radio" name="ajax-button-inner" value="true" class=" eg-tooltip-wrap" <?php checked($base->getVar($grid, ['params', 'ajax-button-inner'], 'false'), 'true'); ?>> <?php esc_html_e('Inner', 'essential-grid'); ?><div class="space18"></div><!--
						--><input type="radio" name="ajax-button-inner" value="false" <?php checked($base->getVar($grid, ['params', 'ajax-button-inner'], 'false'), 'false'); ?>><?php esc_html_e('Outer', 'essential-grid'); ?>
						<div class="div13"></div>
						<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Define the horizontal positioning of the buttons', 'essential-grid'); ?>"><?php esc_html_e('Horizontal Pos.', 'essential-grid'); ?></label><!--
						--><input type="radio" name="ajax-button-h-pos" value="l" <?php checked($base->getVar($grid, ['params', 'ajax-button-h-pos'], 'r'), 'l'); ?>><?php esc_html_e('Left', 'essential-grid'); ?><div class="space18"></div><!--
						--><input type="radio" name="ajax-button-h-pos" value="c" <?php checked($base->getVar($grid, ['params', 'ajax-button-h-pos'], 'r'), 'c'); ?>><?php esc_html_e('Center', 'essential-grid'); ?><div class="space18"></div><!--
						--><input type="radio" name="ajax-button-h-pos" value="r" <?php checked($base->getVar($grid, ['params', 'ajax-button-h-pos'], 'r'), 'r'); ?>><?php esc_html_e('Right', 'essential-grid'); ?>              
						<div class="div13"></div>
						<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Define the vertical positioning of the buttons', 'essential-grid'); ?>"><?php esc_html_e('Vertical Pos.', 'essential-grid'); ?></label><!--
						--><input type="radio" name="ajax-button-v-pos" value="t" <?php checked($base->getVar($grid, ['params', 'ajax-button-v-pos'], 't'), 't'); ?>><?php esc_html_e('Top', 'essential-grid'); ?><div class="space18"></div><!--
						--><input type="radio" name="ajax-button-v-pos" value="b" <?php checked($base->getVar($grid, ['params', 'ajax-button-v-pos'], 't'), 'b'); ?>><?php esc_html_e('Bottom', 'essential-grid'); ?>             
					</div>
				</div>
			</div>

			<div>
				<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Prepend Content', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc eg-cs-tbc-padding eg-cs-tbc-no-border">
				<?php
				$settings = ['textarea_name' => 'ajax-container-pre'];
				wp_editor($base->getVar($grid, ['params', 'ajax-container-pre'], ''), 'ajax-container-pre', $settings);
				?>
				</div>
			</div>

			<div>
				<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Append Content', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc eg-cs-tbc-padding eg-cs-tbc-no-border">
				<?php
				$settings = ['textarea_name' => 'ajax-container-post'];
				wp_editor($base->getVar($grid, ['params', 'ajax-container-post'], ''), 'ajax-container-post', $settings);
				?>
				</div>
			</div>

			<div>
				<div class="eg-cs-tbc-left">
					<esg-llabel class="box-closed"><span><?php esc_html_e('Ajax Container Custom CSS', 'essential-grid'); ?></span></esg-llabel>
				</div>
				<div class="eg-cs-tbc eg-cs-tbc-padding">
					<textarea name="ajax-container-css" id="eg-ajax-custom-css"><?php echo esc_textarea(stripslashes($base->getVar($grid, ['params', 'ajax-container-css'], ''))); ?></textarea>
					<div class="eg-cs-tbc-notice"><?php esc_html_e('Please only add styles directly here without any class/id declaration.', 'essential-grid'); ?></div>
				</div>
			</div>

			<div>
				<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Advanced', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc">           
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Define a JavaScript callback here. This will be called every time when Content is loaded ! You can also define arguments by using callbackname(arg1, arg2, ...., esg99)', 'essential-grid'); ?>"><?php esc_html_e('JavaScript Callback', 'essential-grid'); ?></label><!--
					--><input type="text" name="ajax-callback" value="<?php echo esc_attr(stripslashes($base->getVar($grid, ['params', 'ajax-callback'], ''))); ?>">
					<div class="div13"></div>
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Append Essential Grid argument to the callback to the end', 'essential-grid'); ?>, <?php esc_html_e('Append return argument from Essential Grid with object containing posttype, postsource and ajaxcontainterid', 'essential-grid'); ?>"><?php esc_html_e('Append Argument', 'essential-grid'); ?></label><!--
					--><input type="checkbox" name="ajax-callback-arg" value="on" <?php checked($base->getVar($grid, ['params', 'ajax-callback-arg'], 'on'), 'on'); ?>>
					<div class="div13"></div>
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Define a CSS URL to load when First time Ajax Container has beed created. ', 'essential-grid'); ?>"><?php esc_html_e('Extend CSS URL', 'essential-grid'); ?></label><!--
					--><input type="text" name="ajax-css-url" value="<?php echo esc_attr($base->getVar($grid, ['params', 'ajax-css-url'], '')); ?>" >
					<div class="div13"></div>
					<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Define a JavaScript File URL to load which is run 1 time at first Ajax Content loading', 'essential-grid'); ?>"><?php esc_html_e('Extend JavaScript URL', 'essential-grid'); ?></label><!--
					--><input type="text" name="ajax-js-url" value="<?php echo esc_attr($base->getVar($grid, ['params', 'ajax-js-url'], '')); ?>" >
				</div>
			</div>
			
		</div>
	</div>

	<!-- SPINNER SETTINGS -->
	<div id="esg-settings-spinner-settings" class="esg-settings-container">
		<div>
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Spinner Settings', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc">
					<div id="use_spinner_row">
						<div class="div13"></div>
						<label for="cart-arrows" class="eg-tooltip-wrap" title="<?php esc_attr_e('Choose Loading Spinner', 'essential-grid'); ?>"><?php esc_html_e('Choose Spinner', 'essential-grid'); ?></label><!--
						--><?php
						$use_spinner = $base->getVar($grid, ['params', 'use-spinner'], '0');
						?><select id="use_spinner" name="use-spinner">
							<option value="-1"<?php selected($use_spinner, '-1'); ?>><?php esc_html_e('off', 'essential-grid'); ?></option>
							<option value="0"<?php selected($use_spinner, '0'); ?>>0</option>
							<option value="1"<?php selected($use_spinner, '1'); ?>>1</option>
							<option value="2"<?php selected($use_spinner, '2'); ?>>2</option>
							<option value="3"<?php selected($use_spinner, '3'); ?>>3</option>
							<option value="4"<?php selected($use_spinner, '4'); ?>>4</option>
							<option value="5"<?php selected($use_spinner, '5'); ?>>5</option>
						</select>
					</div>
					
					<div id="spinner_color_row">
						<div class="div13"></div>
						<label for="cart-arrows" class="eg-tooltip-wrap" title="<?php esc_attr_e('Sorting at Loading', 'essential-grid'); ?>"><?php esc_html_e('Choose Spinner Color', 'essential-grid'); ?></label><!--
						--><input type="text" class="inputColorPicker" id="spinner_color" name="spinner-color" value="<?php echo esc_attr($base->getVar($grid, ['params', 'spinner-color'], '#FFFFFF')); ?>" />
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- API / CUSTOM JAVASCRIPT SETTINGS -->
	<div id="esg-settings-api-settings" class="esg-settings-container">
		<div>
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Custom Javascript', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc eg-cs-tbc-padding">
					<textarea name="custom-javascript" id="eg-api-custom-javascript"><?php echo esc_textarea(stripslashes($base->getVar($grid, ['params', 'custom-javascript'], ''))); ?></textarea>               
				</div>
			</div>
	
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('API Methods', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc">
				<?php if ($grid !== false) { ?>
					<label><?php esc_html_e('Redraw Grid:', 'essential-grid'); ?></label><input class="eg-api-inputs" type="text" name="do-not-save" value="essapi_<?php echo esc_attr($grid['id']); ?>.esredraw();" readonly="true" />
					<div class="div13"></div>
					<label><?php esc_html_e('Quick Redraw Grid:', 'essential-grid'); ?></label><input class="eg-api-inputs" type="text" name="do-not-save" value="essapi_<?php echo esc_attr($grid['id']); ?>.esquickdraw();" readonly="true" />
				<?php } else { ?>
					<p>
					<?php esc_html_e('API Methods will be available after this Grid is saved for the first time.', 'essential-grid'); ?>
					</p>
				<?php } ?>
				</div>
			</div>
	
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Code Examples', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc">
				<label><?php esc_html_e('Visual Composer Tab fix:', 'essential-grid'); ?></label><!--
				--><div class="esg-prewrap">
	<pre><code>jQuery('body').on('click', '.wpb_tabs_nav a', function() {
	  setTimeout(function(){
		jQuery(window).trigger('resize');
	  }, 500); //change 500 to your needs
	});</code></pre>
				</div>
				
				<div class="div13"></div>
				<label><?php esc_html_e('Lightbox Custom Options:', 'essential-grid'); ?></label><!--
				--><div class="esg-prewrap">
	<pre><code>// http://fancyapps.com/fancybox/3/docs/
	lightboxOptions.hideScrollbar = true;
	lightboxOptions.hash = false;
	</code></pre>
					</div>
				</div>
			</div>

		</div>
	</div>

	<!-- COOKIE SETTINGS -->
	<div id="esg-settings-cookie-settings" class="esg-settings-container">
		<div>
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Timing', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc">
				<label for="filter-arrows" class="eg-tooltip-wrap" title="<?php esc_attr_e('The amount of time before the cookies expire (in minutes).', 'essential-grid'); ?>" ><?php esc_html_e('Save for', 'essential-grid'); ?></label><!--
				--><input type="text" name="cookie-save-time" class="input-settings-small " value="<?php echo intval($base->getVar($grid, ['params', 'cookie-save-time'], '30')); ?>"><div class="space18"></div><?php esc_html_e('Minutes', 'essential-grid'); ?>
				</div>
			</div>
			<div>
				<div class="eg-cs-tbc-left"><esg-llabel class="box-closed"><span><?php esc_html_e('Settings', 'essential-grid'); ?></span></esg-llabel></div>
				<div class="eg-cs-tbc">
					<label for="filter-arrows"><?php esc_html_e('Search', 'essential-grid'); ?></label><!--
					--><input type="radio" name="cookie-save-search" value="on" <?php checked($base->getVar($grid, ['params', 'cookie-save-search'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Remember user\'s last search.', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio" name="cookie-save-search" value="off" <?php checked($base->getVar($grid, ['params', 'cookie-save-search'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Do not apply cookie for search.', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
					<div class="div13"></div>
					<label for="filter-arrows"><?php esc_html_e('Filter', 'essential-grid'); ?></label><!--
					--><input type="radio" name="cookie-save-filter" value="on" <?php checked($base->getVar($grid, ['params', 'cookie-save-filter'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Remember Grid\'s last filter state.', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio" name="cookie-save-filter" value="off" <?php checked($base->getVar($grid, ['params', 'cookie-save-filter'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Do not apply cookie for filter.', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
					<div class="div13"></div>
					<label for="filter-arrows"><?php esc_html_e('Pagination', 'essential-grid'); ?></label><!--
					--><input type="radio" name="cookie-save-pagination" value="on" <?php checked($base->getVar($grid, ['params', 'cookie-save-pagination'], 'off'), 'on'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Remember Grid\'s last pagination state.', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><input type="radio" name="cookie-save-pagination" value="off" <?php checked($base->getVar($grid, ['params', 'cookie-save-pagination'], 'off'), 'off'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Do not apply cookie for pagination.', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span>
					<div class="div13"></div>
					<div class="esg-note"><?php
					printf(
						/* translators: 1:open b tag 2:close b tag 3:open link tag to cookie law 4:close link tag */
						esc_html__('%1$sSpecial Note:%2$s %3$sEU Law%4$s requires that a notification be shown to the user when cookies are being used.', 'essential-grid'),
						'<b>',
						'</b>',
						'<a href="//www.cookielaw.org/the-cookie-law/" target="_blank">',
						'</a>'
					);
					?></div>
				</div>
			</div>
		</div>
	</div>

	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo apply_filters('essgrid_grid_create_settings', '', $grid); 
	?>

</form>

<?php
Essential_Grid_Dialogs::pages_select_dialog();
Essential_Grid_Dialogs::navigation_skin_css_edit_dialog();
Essential_Grid_Dialogs::navigation_skin_css_selector_dialog();
Essential_Grid_Dialogs::filter_select_dialog();
Essential_Grid_Dialogs::filter_custom_dialog();
Essential_Grid_Dialogs::filter_delete_dialog();
