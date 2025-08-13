<?php
/**
 * Represents the view for the metabox in post / pages
 *
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

if(!isset($post)) return false; //not called as it should be

if (!isset($disable_advanced)) $disable_advanced = false;

$base = new Essential_Grid_Base();
$item_skin = new Essential_Grid_Item_Skin();
$item_elements = new Essential_Grid_Item_Element();
$meta = new Essential_Grid_Meta();

$values = get_post_custom($post->ID);

$eg_sources_html5_mp4 = isset($values['eg_sources_html5_mp4']) ? esc_attr($values['eg_sources_html5_mp4'][0]) : "";
$eg_sources_html5_ogv = isset($values['eg_sources_html5_ogv']) ? esc_attr($values['eg_sources_html5_ogv'][0]) : "";
$eg_sources_html5_webm = isset($values['eg_sources_html5_webm']) ? esc_attr($values['eg_sources_html5_webm'][0]) : "";
$eg_vimeo_ratio = isset($values['eg_vimeo_ratio']) ? esc_attr($values['eg_vimeo_ratio'][0]) : "1";
$eg_youtube_ratio = isset($values['eg_youtube_ratio']) ? esc_attr($values['eg_youtube_ratio'][0]) : "1";
$eg_wistia_ratio = isset($values['eg_wistia_ratio']) ? esc_attr($values['eg_wistia_ratio'][0]) : "1";
$eg_html5_ratio = isset($values['eg_html5_ratio']) ? esc_attr($values['eg_html5_ratio'][0]) : "1";
$eg_soundcloud_ratio = isset($values['eg_soundcloud_ratio']) ? esc_attr($values['eg_soundcloud_ratio'][0]) : "1";
$eg_sources_youtube = isset($values['eg_sources_youtube']) ? esc_attr($values['eg_sources_youtube'][0]) : "";
$eg_sources_wistia = isset($values['eg_sources_wistia']) ? esc_attr($values['eg_sources_wistia'][0]) : "";
$eg_sources_vimeo = isset($values['eg_sources_vimeo']) ? esc_attr($values['eg_sources_vimeo'][0]) : "";
$eg_sources_image = isset($values['eg_sources_image']) ? esc_attr($values['eg_sources_image'][0]) : "";
$eg_sources_iframe = isset($values['eg_sources_iframe']) ? esc_attr($values['eg_sources_iframe'][0]) : "";
$eg_sources_soundcloud = isset($values['eg_sources_soundcloud']) ? esc_attr($values['eg_sources_soundcloud'][0]) : "";
$eg_sources_essgrid = isset($values['eg_sources_essgrid']) ? esc_attr($values['eg_sources_essgrid'][0]) : "";

$eg_featured_grid = isset($values['eg_featured_grid']) ? esc_attr($values['eg_featured_grid'][0]) : "";

$eg_image_fit = isset($values['eg_image_fit']) ? esc_attr($values['eg_image_fit'][0]) : "";
$eg_image_align_h = isset($values['eg_image_align_h']) ? esc_attr($values['eg_image_align_h'][0]) : "";
$eg_image_align_v = isset($values['eg_image_align_v']) ? esc_attr($values['eg_image_align_v'][0]) : "";
$eg_image_repeat = isset($values['eg_image_repeat']) ? esc_attr($values['eg_image_repeat'][0]) : "";

$eg_sources_image_url = '';
if(intval($eg_sources_image) > 0){
	//get URL to Image
	$img = wp_get_attachment_image_src($eg_sources_image, 'full');
	if($img !== false){
		$eg_sources_image_url = $img[0];
	}else{
		$eg_sources_image = '';
	}
}

$eg_settings_custom_meta_skin = isset($values['eg_settings_custom_meta_skin']) ? unserialize($values['eg_settings_custom_meta_skin'][0]) : "";
$eg_settings_custom_meta_element = isset($values['eg_settings_custom_meta_element']) ? unserialize($values['eg_settings_custom_meta_element'][0]) : "";
$eg_settings_custom_meta_setting = isset($values['eg_settings_custom_meta_setting']) ? unserialize($values['eg_settings_custom_meta_setting'][0]) : "";
$eg_settings_custom_meta_style = isset($values['eg_settings_custom_meta_style']) ? unserialize($values['eg_settings_custom_meta_style'][0]) : "";

$eg_meta = [];

if(!empty($eg_settings_custom_meta_skin)){
	foreach($eg_settings_custom_meta_skin as $key => $val){
		$eg_meta[$key]['skin'] = @$val;
		$eg_meta[$key]['element'] = $base->getVar($eg_settings_custom_meta_element, $key);
		$eg_meta[$key]['setting'] = $base->getVar($eg_settings_custom_meta_setting, $key);
		$eg_meta[$key]['style'] = $base->getVar($eg_settings_custom_meta_style, $key);
	}
}

$advanced = [];
$eg_skins = $item_skin->get_essential_item_skins();
foreach($eg_skins as $skin){
	if(!empty($skin['layers'])){
		$advanced[$skin['id']]['name'] = $skin['name'];
		$advanced[$skin['id']]['handle'] = $skin['handle'];
		foreach($skin['layers'] as $layer){
			if(empty($layer)) continue; //some layers may be NULL...
			
			//check if special, ignore special elements
			$settings = $layer['settings'];
			if(!empty($settings) && isset($settings['special']) && $settings['special'] == 'true') continue;
			
			/* 2.1.6 */
			if(isset($layer['id'])) $advanced[$skin['id']]['layers'][] = $layer['id'];
		}
	}
}

$eg_elements = $item_elements->get_allowed_meta();
$custom_meta = $meta->get_all_meta(false);

if ($disable_advanced) { 
	//only show if we are in preview mode
	?>
	<form id="eg-form-post-meta-settings">
		<input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>" />
	<?php
}
wp_nonce_field('eg_meta_box_nonce', 'essential_grid_meta_box_nonce');
?>

<ul id="eg-option-tabber-post-meta" class="eg-option-tabber-wrapper">
	<?php 
	$selectedtab = "selected";
	
	$result = apply_filters(
		'essgrid_grid_meta_box_tabs_before', 
		[
			'html' => '',
			'selectedtab' => $selectedtab,
		],
		$post,
		$disable_advanced
	);
	$selectedtab = $result['selectedtab'];
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $result['html'];
	
	if ($disable_advanced) { 
		//only show if we are in preview mode
		?><li class="eg-option-tabber <?php echo esc_attr($selectedtab); ?>" data-target="#eg-my-cobbles-options"><span class="dashicons dashicons-align-center"></span><?php esc_html_e('Item Settings', 'essential-grid'); ?></li>
		<?php
		$selectedtab = "";
	} 
	?>
	<li class="eg-option-tabber <?php echo esc_attr($selectedtab); ?>" data-target="#eg-custommeta-options"><span class="dashicons dashicons-list-view"></span><?php esc_html_e('Custom Meta', 'essential-grid'); ?></li>
	<li class="eg-option-tabber" data-target="#eg-source-options"><span class="dashicons dashicons-admin-media"></span><?php esc_html_e('Alternative Sources', 'essential-grid'); ?></li>
	<li class="eg-option-tabber" data-target="#eg-skin-options"><span class="dashicons dashicons-admin-appearance"></span><?php esc_html_e('Skin Modifications', 'essential-grid'); ?></li>
	<li class="eg-option-tabber" data-target="#eg-featured-grid-options"><span class="dashicons dashicons-screenoptions"></span><?php esc_html_e('Featured Grid', 'essential-grid'); ?></li>
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo apply_filters('essgrid_grid_meta_box_tabs_after', '', $post, $selectedtab, $disable_advanced); 
	?>
</ul>
<?php
$selectedtab = "selected";
$displaytab = "display:block";

$result = apply_filters(
	'essgrid_grid_meta_box_tabs_content_before',
	[
		'html' => '',
		'selectedtab' => $selectedtab,
		'displaytab' => $displaytab,
	],
	$post,
	$disable_advanced
);
$selectedtab = $result['selectedtab'];
$displaytab = $result['displaytab'];
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $result['html'];

if ($disable_advanced) {
	//only show if we are in preview mode
	$cobbles = '1:1';
	$raw_cobbles = isset($values['eg_cobbles']) ? json_decode($values['eg_cobbles'][0], true) : '';
	if (isset($grid_id) && isset($raw_cobbles[$grid_id]['cobbles']))
		$cobbles = $raw_cobbles[$grid_id]['cobbles'];
	?>
	<div id="eg-my-cobbles-options" class="eg-options-tab <?php echo esc_attr($selectedtab); ?> <?php echo !empty($displaytab) ? 'esg-display-block' : ''; ?>">
		<div>
			<label><?php esc_html_e('Cobbles Element Size', 'essential-grid'); ?></label><!--
			--><select name="eg_cobbles_size" id="eg_cobbles_size">
				<option value="1:1"<?php selected($cobbles, '1:1'); ?>><?php esc_html_e('width 1, height 1', 'essential-grid'); ?></option>
				<option value="1:2"<?php selected($cobbles, '1:2'); ?>><?php esc_html_e('width 1, height 2', 'essential-grid'); ?></option>
				<option value="1:3"<?php selected($cobbles, '1:3'); ?>><?php esc_html_e('width 1, height 3', 'essential-grid'); ?></option>
				<option value="1:4"<?php selected($cobbles, '1:4'); ?>><?php esc_html_e('width 1, height 4', 'essential-grid'); ?></option>
				<option value="2:1"<?php selected($cobbles, '2:1'); ?>><?php esc_html_e('width 2, height 1', 'essential-grid'); ?></option>
				<option value="2:2"<?php selected($cobbles, '2:2'); ?>><?php esc_html_e('width 2, height 2', 'essential-grid'); ?></option>
				<option value="2:3"<?php selected($cobbles, '2:3'); ?>><?php esc_html_e('width 2, height 3', 'essential-grid'); ?></option>
				<option value="2:4"<?php selected($cobbles, '2:4'); ?>><?php esc_html_e('width 2, height 4', 'essential-grid'); ?></option>
				<option value="3:1"<?php selected($cobbles, '3:1'); ?>><?php esc_html_e('width 3, height 1', 'essential-grid'); ?></option>
				<option value="3:2"<?php selected($cobbles, '3:2'); ?>><?php esc_html_e('width 3, height 2', 'essential-grid'); ?></option>
				<option value="3:3"<?php selected($cobbles, '3:3'); ?>><?php esc_html_e('width 3, height 3', 'essential-grid'); ?></option>
				<option value="3:4"<?php selected($cobbles, '3:4'); ?>><?php esc_html_e('width 3, height 4', 'essential-grid'); ?></option>
				<option value="4:1"<?php selected($cobbles, '4:1'); ?>><?php esc_html_e('width 4, height 1', 'essential-grid'); ?></option>
				<option value="4:2"<?php selected($cobbles, '4:2'); ?>><?php esc_html_e('width 4, height 2', 'essential-grid'); ?></option>
				<option value="4:3"<?php selected($cobbles, '4:3'); ?>><?php esc_html_e('width 4, height 3', 'essential-grid'); ?></option>
				<option value="4:4"<?php selected($cobbles, '4:4'); ?>><?php esc_html_e('width 4, height 4', 'essential-grid'); ?></option>
			</select>
			<div class="div13"></div>
			<?php
			$skins = Essential_Grid_Item_Skin::get_essential_item_skins('all', false);
			$use_skin = -1;
			$raw_skin = isset($values['eg_use_skin']) ? json_decode($values['eg_use_skin'][0], true) : '';
			if(isset($grid_id) && isset($raw_skin[$grid_id]['use-skin']))
				$use_skin = $raw_skin[$grid_id]['use-skin'];
			?>
			<label><?php esc_html_e('Choose Specific Skin', 'essential-grid'); ?></label><!--
			--><select name="eg_use_skin">
				<option value="-1"><?php esc_html_e('-- Default Skin --', 'essential-grid'); ?></option>
				<?php
				if(!empty($skins)){
					foreach($skins as $skin){
						echo '<option value="'.esc_attr($skin['id']).'"'.selected($use_skin, $skin['id']).'>'.esc_html($skin['name']).'</option>'."\n";
					}
				}
				?>
			</select>
			<div class="div13"></div>
			<label><?php esc_html_e('Media Fit', 'essential-grid'); ?></label><!--
			--><select name="eg_image_fit">
				<option value="-1"><?php esc_html_e('-- Default Fit --', 'essential-grid'); ?></option>
				<option value="contain" <?php selected($eg_image_fit, 'contain'); ?>><?php esc_html_e('Contain', 'essential-grid'); ?></option>
				<option value="cover" <?php selected($eg_image_fit, 'cover'); ?>><?php esc_html_e('Cover', 'essential-grid'); ?></option>
			</select>
			<div class="div13"></div>
			<label><?php esc_html_e('Media Repeat', 'essential-grid'); ?></label><!--
			--><select name="eg_image_repeat">
				<option value="-1"><?php esc_html_e('-- Default Repeat --', 'essential-grid'); ?></option>
				<option value="no-repeat" <?php selected($eg_image_repeat, 'no-repeat'); ?>><?php esc_html_e('no-repeat', 'essential-grid'); ?></option>
				<option value="repeat" <?php selected($eg_image_repeat, 'repeat'); ?>><?php esc_html_e('repeat', 'essential-grid'); ?></option>
				<option value="repeat-x" <?php selected($eg_image_repeat, 'repeat-x'); ?>><?php esc_html_e('repeat-x', 'essential-grid'); ?></option>
				<option value="repeat-y" <?php selected($eg_image_repeat, 'repeat-y'); ?>><?php esc_html_e('repeat-y', 'essential-grid'); ?></option>
			</select>
			<div class="div13"></div>
			<label><?php esc_html_e('Media Align', 'essential-grid'); ?></label><!--
			--><select name="eg_image_align_h">
				<option value="-1"><?php esc_html_e('-- Horizontal Align --', 'essential-grid'); ?></option>
				<option value="left" <?php selected($eg_image_align_h, 'left'); ?>><?php esc_html_e('Left', 'essential-grid'); ?></option>
				<option value="center" <?php selected($eg_image_align_h, 'center'); ?>><?php esc_html_e('Center', 'essential-grid'); ?></option>
				<option value="right" <?php selected($eg_image_align_h, 'right'); ?>><?php esc_html_e('Right', 'essential-grid'); ?></option>
			</select><div class="space18"></div><!--
			--><select name="eg_image_align_v">
				<option value="-1"><?php esc_html_e('-- Vertical Align --', 'essential-grid'); ?></option>
				<option value="top" <?php selected($eg_image_align_v, 'top'); ?>><?php esc_html_e('Top', 'essential-grid'); ?></option>
				<option value="center" <?php selected($eg_image_align_v, 'center'); ?>><?php esc_html_e('Center', 'essential-grid'); ?></option>
				<option value="bottom" <?php selected($eg_image_align_v, 'bottom'); ?>><?php esc_html_e('Bottom', 'essential-grid'); ?></option>
			</select>
		</div>
	</div>
	<?php
	$selectedtab ="";
	$displaytab = "";
}
?>

<div id="eg-custommeta-options" class="eg-options-tab eg-custommeta-options-tab <?php echo esc_attr($selectedtab); ?> <?php echo !empty($displaytab) ? 'esg-display-block' : ''; ?>" >
	<div>
		<?php
		if(!empty($custom_meta)){
			foreach($custom_meta as $cmeta){
				//check if post already has a value set
				$val = isset($values['eg-'.$cmeta['handle']]) ? esc_attr($values['eg-'.$cmeta['handle']][0]) : $base->getVar($cmeta, 'default');
				?>
					<label><?php echo esc_html($cmeta['name']); ?></label><!--
					--><?php
					switch($cmeta['type']){
						case 'text':
							echo '<input type="text" name="eg-'.esc_attr($cmeta['handle']).'" value="'.esc_attr($val).'" /><div class="div13"></div>';
							break;
						case 'select':
						case 'multi-select':
							$do_array = ($cmeta['type'] == 'multi-select') ? '[]' : '';
							$el = $meta->prepare_select_by_string($cmeta['select']);
							echo '<select name="eg-'.esc_attr($cmeta['handle'].$do_array).'"';
							if($cmeta['type'] == 'multi-select') echo ' multiple="multiple" size="5"';
							echo '>';
							if(!empty($el) && is_array($el)){
								if($cmeta['type'] != 'multi-select'){
									echo '<option value="">'.esc_attr__('---', 'essential-grid').'</option>';
								}else{
									$val = json_decode(str_replace('&quot;', '"', $val), true);
									$val = (is_array($val)) ? $val : [$val];
									$val = array_map('trim', $val);
								}
								foreach($el as $ele){
									if(is_array($val)){
										$sel = (in_array($ele, $val)) ? ' selected' : '';
									}else{
										$sel = ($ele == $val) ? ' selected' : '';
									}
									echo '<option value="'.esc_attr($ele).'"'.esc_attr($sel).'>'.esc_html($ele).'</option>';
								}
							}
							echo '</select><div class="div13"></div>';
							break;
						case 'image':
							$var_src = '';
							if(intval($val) > 0){
								//get URL to Image
								$img = wp_get_attachment_image_src($val, 'full');
								if($img !== false){
									$var_src = $img[0];
								}else{
									$val = '';
								}
							}else{
								$val = '';
							}
						?><input type="hidden" value="<?php echo esc_attr($val); ?>" name="eg-<?php echo esc_attr($cmeta['handle']); ?>" id="eg-<?php echo esc_attr($cmeta['handle']); ?>" />
							<div class="esg-btn esg-purple eg-cm-image-add esg-margin-b-0" data-setto="eg-<?php echo esc_attr($cmeta['handle']); ?>"><?php esc_html_e('Choose Image', 'essential-grid'); ?></div><div class="space18"></div><!--
							--><div class="esg-btn esg-red eg-cm-image-clear esg-margin-b-0" data-setto="eg-<?php echo esc_attr($cmeta['handle']); ?>"><?php esc_html_e('Remove Image', 'essential-grid'); ?></div>
							<div class="esg-line-height-0 esg-text-center">
								<img id="eg-<?php echo esc_attr($cmeta['handle']); ?>-img" class="image-holder-wrap-div<?php echo ($var_src == '') ? ' esg-display-none' : ''; ?>" src="<?php echo esc_url($var_src); ?>"  alt=""/>
							</div>
							<div class="div13"></div>
							<?php
							break;
					}
					?>
				<?php
			}
		} else {
			esc_html_e('No metas available yet. Add some through the Custom Meta menu of Essential Grid.', 'essential-grid');
			echo '<div class="div13"></div>';
		}
		?>

		<a href="<?php echo esc_url(Essential_Grid_Admin::getSubViewUrl(Essential_Grid_Admin::VIEW_SUB_CUSTOM_META_AJAX)); ?>#esg-custommeta-settings" class="esg-btn esg-btn-create-new-meta esg-purple" target="_blank"><?php esc_html_e('Create New Meta Keys', 'essential-grid'); ?></a>
	</div>
</div> <!-- END OF EG OPTION TAB -->

<div id="eg-featured-grid-options" class="eg-options-tab">
	<label class="eg-mb-label eg-tooltip-wrap" title="<?php esc_attr_e('Choose the grid to display', 'essential-grid'); ?>"><?php esc_html_e('Essential Grid to Feature', 'essential-grid'); ?></label><!--
	--><select id="eg-featured-grid" name="eg_featured_grid">
		<option value=""><?php esc_html_e("No Featured Essential Grid",'essential-grid'); ?></option>
		<?php 
		$arrGrids = Essential_Grid_Db::get_entity('grids')->get_grids();
		foreach ( $arrGrids as $grid ) {
			echo '<option value="' . esc_attr($grid->handle) . '" ' . selected( $eg_featured_grid, $grid->handle, false ) . '>' . esc_html($grid->name) . '</option>';
		}
		?>
	</select>
	<div class="div13"></div>
	<div class="esg-note-b">
		<div class="dashicons dashicons-lightbulb"></div>
		<?php esc_html_e('The selected grid will be displayed instead of the featured image on the single post and in the blog overviews.', 'essential-grid'); ?><br/>
		<?php printf(
			/* translators: 1:open link tag to FAQ 2:close link tag */
			esc_html__('If this feature does not work in your theme please check out this %1$sshort tutorial%2$s to code in manually.', 'essential-grid'),
			'<a href="https://www.themepunch.com/revslider-doc/add-on-featured-slider/#theme_not_support">',
			'</a>'
		);
		?>
	</div>
</div> <!-- END OF EG FEATURED TAB -->

<div id="eg-source-options" class="eg-options-tab eg-source-options-tab">	
	<strong class="esg-font-size-14"><?php esc_html_e('HTML5 Video & Audio Source`s', 'essential-grid'); ?></strong>
	<div class="div13"></div>
	<label><?php esc_html_e('MP4 / Audio', 'essential-grid'); ?></label><input type="text" name="eg_sources_html5_mp4" id="eg_sources_html5_mp4" value="<?php echo esc_attr($eg_sources_html5_mp4); ?>" />
	<div class="div13"></div>
	<label class="eg-mb-label eg-tooltip-wrap" title="<?php esc_attr_e('Choose the Video Ratio', 'essential-grid'); ?>"><?php esc_html_e('Video Ratio', 'essential-grid'); ?></label><!--
	--><select id="eg-html5-ratio" name="eg_html5_ratio">
		<option value="1"<?php selected($eg_html5_ratio, '1'); ?>>16:9</option>	
		<option value="0"<?php selected($eg_html5_ratio, '0'); ?>>4:3</option>
	</select>

	<div class="div30"></div>
	<div class="esg-custom-iblock-src-wrapper" >
		<strong class="esg-font-size-14"><?php esc_html_e('YouTube Video Source`s', 'essential-grid'); ?></strong>
		<div class="div13"></div>
		<label for="eg_sources_youtube"><?php esc_html_e('YouTube ID', 'essential-grid'); ?></label><input type="text" name="eg_sources_youtube" id="eg_sources_youtube" value="<?php echo esc_attr($eg_sources_youtube); ?>" />
		<div class="div13"></div>
		<label  class="eg-tooltip-wrap" title="<?php esc_attr_e('Choose the Video Ratio', 'essential-grid'); ?>"><?php esc_html_e('Video Ratio', 'essential-grid'); ?></label><!--
		--><select id="eg-youtube-ratio" name="eg_youtube_ratio">
			<option value="1"<?php selected($eg_youtube_ratio, '1'); ?>>16:9</option>
			<option value="0"<?php selected($eg_youtube_ratio, '0'); ?>>4:3</option>
		</select>
		<div class="div30"></div>
	</div>
	<div class="esg-custom-iblock-src-wrapper">
		<strong class="esg-font-size-14"><?php esc_html_e('Vimeo Video Source`s', 'essential-grid'); ?></strong>
		<div class="div13"></div>
		<label  for="eg_sources_vimeo"><?php esc_html_e('Vimeo ID', 'essential-grid'); ?></label><input type="text" name="eg_sources_vimeo" id="eg_sources_vimeo" value="<?php echo esc_attr($eg_sources_vimeo); ?>" />
		<div class="div13"></div>
		<label class="eg-mb-label eg-tooltip-wrap" title="<?php esc_attr_e('Choose the Video Ratio', 'essential-grid'); ?>"><?php esc_html_e('Video Ratio', 'essential-grid'); ?></label><!--
		--><select id="eg-vimeo-ratio" name="eg_vimeo_ratio">
			<option value="1"<?php selected($eg_vimeo_ratio, '1'); ?>>16:9</option>	
			<option value="0"<?php selected($eg_vimeo_ratio, '0'); ?>>4:3</option>
		</select>
		<div class="div30"></div>
	</div>
	<div class="esg-custom-iblock-src-wrapper">
		<strong class="esg-font-size-14"><?php esc_html_e('Wistia Video Source`s', 'essential-grid'); ?></strong>
		<div class="div13"></div>
		<label  for="eg_sources_wistia"><?php esc_html_e('Wistia ID', 'essential-grid'); ?></label><input type="text" name="eg_sources_wistia" id="eg_sources_wistia" value="<?php echo esc_attr($eg_sources_wistia); ?>" />
		<div class="div13"></div>
		<label class="eg-mb-label eg-tooltip-wrap" title="<?php esc_attr_e('Choose the Video Ratio', 'essential-grid'); ?>"><?php esc_html_e('Video Ratio', 'essential-grid'); ?></label><!--
		--><select id="eg-wistia-ratio" name="eg_wistia_ratio" >
			<option value="1"<?php selected($eg_wistia_ratio, '1'); ?>>16:9</option>
			<option value="0"<?php selected($eg_wistia_ratio, '0'); ?>>4:3</option>
		</select>
		<div class="div30"></div>
	</div>
	<div class="esg-custom-iblock-src-wrapper">
		<strong class="esg-font-size-14"><?php esc_html_e('Sound Cloud Source`s', 'essential-grid'); ?></strong>
		<div class="div13"></div>
		<label for="eg_sources_soundcloud"><?php esc_html_e('SoundCloud Track ID', 'essential-grid'); ?></label><input type="text" name="eg_sources_soundcloud" id="eg_sources_soundcloud" value="<?php echo esc_attr($eg_sources_soundcloud); ?>" />
		<div class="div13"></div>
		<label class="eg-mb-label eg-tooltip-wrap" title="<?php esc_attr_e('Choose the SoundCloud iFrame Ratio', 'essential-grid'); ?>"><?php esc_html_e('Frame Ratio', 'essential-grid'); ?></label><!--
		--><select id="eg-soundcloud-ratio" name="eg_soundcloud_ratio">
			<option value="1"<?php selected($eg_soundcloud_ratio, '1'); ?>>16:9</option>
			<option value="0"<?php selected($eg_soundcloud_ratio, '0'); ?>>4:3</option>
		</select>
		
		<div class="div30"></div>
	</div>
	<div class="esg-meta-box-spacer"></div>
	<strong class="esg-font-size-14"><?php esc_html_e('Image Source`s', 'essential-grid'); ?></strong>
	<div class="div13"></div>
	<label  for="eg_sources_image"><?php esc_html_e('Alt. Image', 'essential-grid'); ?></label><input type="text" name="eg_sources_image" id="eg_sources_image" class="esg-display-none" value="<?php echo esc_attr($eg_sources_image); ?>" /><!--
	--><div id="eg-choose-from-image-library" class="esg-btn esg-purple" data-setto="eg_sources_image"><?php esc_html_e('Choose Image', 'essential-grid'); ?></div><div class="space18"></div><!--
	--><div id="eg-clear-from-image-library" class="esg-btn esg-red eg-remove-custom-meta-field"><?php esc_html_e('Remove Image', 'essential-grid'); ?></div>
	<div id="eg_sources_image-wrapper">
		<div class="div13"></div>
		<img id="eg_sources_image-img" src="<?php echo esc_url($eg_sources_image_url); ?>" alt=""> <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
	</div>
	
	<div class="div30"></div>
	<strong class="esg-font-size-14"><?php esc_html_e('iFrame HTML Markup', 'essential-grid'); ?></strong>
	<div class="div13"></div>
	<textarea type="text" name="eg_sources_iframe" id="eg_sources_iframe" class="eg-sources-iframe"><?php echo esc_textarea($eg_sources_iframe); ?></textarea>
	<div class="div30"></div>
	<div class="esg-custom-iblock-src-wrapper">
		<strong class="esg-font-size-14"><?php esc_html_e('Choose Essential Grid', 'essential-grid'); ?></strong>
		<div class="div13"></div>
		<select id="eg_sources_essgrid" name="eg_sources_essgrid">
			<option value="">--- Choose Grid ---</option>
			<?php 
				$_grids = Essential_Grid_Db::get_entity('grids')->get_grids();
				foreach($_grids as $_grid) {
					$_alias = $_grid -> handle;
					$_shortcode = '[ess_grid alias="' . $_alias . '"][/ess_grid]';
					$_shortcode = str_replace('"', '', $_shortcode);
					?><option <?php selected($eg_sources_essgrid, $_alias); ?> value="<?php echo esc_attr($_alias); ?>"><?php echo esc_html($_shortcode); ?></option>
				<?php } ?>
		</select><div class="div13"></div>
	</div>
	<div class="esg-custom-iblock-src-wrapper">
		<?php do_action('essgrid_add_meta_options', $values); ?>
	</div>
	<div class="div13"></div>
</div>

<div id="eg-skin-options" class="eg-options-tab">
	<div id="eg-advanced-param-wrap">
		<div class="eg-advanced-param" id="eg-advanced-param-post"></div>
		<div class="esg-btn esg-purple eg-add-custom-meta-field" id="eg-add-custom-meta-field-post"><?php esc_html_e('Add New Custom Skin Rule', 'essential-grid'); ?></div>
		<div class="div13"></div>
		<div class="esg-note-b">
			<div class="dashicons dashicons-lightbulb"></div>
			<?php esc_html_e("For default Skin Settings please use the Essential Grid Skin Editor.", 'essential-grid'); ?><br>
			<?php esc_html_e("Only add Rules here to change the Skin Element Styles only for this Post !", 'essential-grid'); ?><br>
			<?php esc_html_e("Every rule defined here will overwrite the Global Skin settings explicit for this Post in the Grid where the Skin is used. ", 'essential-grid'); ?>
		</div>
	</div>
</div>
		
<?php
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo apply_filters('essgrid_grid_meta_box_tabs_content_after', '', $post);

if ($disable_advanced) {
	//only show if we are in preview mode
?>
</form>
<?php } ?>

<!-- ESG 2.1.6 -->
<?php
	$eg_custom_meta_216 = isset($values['eg_custom_meta_216']) ? esc_attr($values['eg_custom_meta_216'][0]) : 'false';
	if($eg_custom_meta_216 != 'true') { ?>
	<script type="text/javascript">
		var eg_skin_color_values = {
		<?php
			$skins = Essential_Grid_Item_Skin::get_essential_item_skins('all', false);
			foreach($skins as $skin) {
				if(!empty($skin['params']) && is_string($skin['params'])) {
					$params = json_decode($skin['params'], true);
					if(!empty($params) && !empty($params['container-background-color'])) {
						echo '"' . esc_attr($skin['id']) . '": "' . esc_attr($params['container-background-color']) . '",';
					}
				}
			}
		?>
};
	</script>
	<?php } ?>
<input type="hidden" name="eg_custom_meta_216" value="true" />

<script type="text/javascript">	
	jQuery(function(){		
		jQuery(document).on('click','#eg-option-tabber-post-meta .eg-option-tabber',function() {
			var t = jQuery(this),
				mbox =t.closest('#eg-meta-box'),
				s = mbox.find('.eg-option-tabber.selected');

			s.removeClass("selected");
			t.addClass("selected");
			jQuery(s.data('target')).hide();
			jQuery(t.data('target')).show();
		});
		
		jQuery('#eg-choose-from-image-library').on('click',function(e) {
			e.preventDefault();
			AdminEssentials.upload_image_img(jQuery(this).data('setto'));
			return false; 
		});
		
		jQuery('#eg-clear-from-image-library').on('click',function(e) {
			e.preventDefault();
			jQuery('#eg_sources_image').val('');
			jQuery('#eg_sources_image-img').attr("src","").hide();
			return false; 
		});
		
		jQuery('.eg-cm-image-add').on('click',function(e) {
			e.preventDefault();
			AdminEssentials.upload_image_img(jQuery(this).data('setto'));
			return false; 
		});
		
		jQuery('.eg-cm-image-clear').on('click',function(e) {
			e.preventDefault();
			var setto = jQuery(this).data('setto');
			jQuery('#'+setto).val('');
			jQuery('#'+setto+'-img').attr("src","").hide();
			return false; 
		});
		
		AdminEssentials.setInitSkinsJson(<?php echo wp_json_encode($advanced); ?>);
		AdminEssentials.setInitElementsJson(<?php echo wp_json_encode($eg_meta); ?>);
		AdminEssentials.setInitStylingJson(<?php echo wp_json_encode($eg_elements); ?>);
		AdminEssentials.initMetaBox('post');

		if(jQuery('#eg_sources_image-img').attr('src') !== '')
			jQuery('#eg_sources_image-img').show();
		else
			jQuery('#eg_sources_image-img').hide();
	});
</script>
