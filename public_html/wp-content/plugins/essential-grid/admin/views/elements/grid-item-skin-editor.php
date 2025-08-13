<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

//force the js file to be included
global $esg_dev_mode;
if($esg_dev_mode) {
	wp_enqueue_script('esg-item-editor-script', plugins_url('../../assets/js/modules/dev/grid-editor.js', __FILE__ ), ['jquery'], ESG_REVISION, [ 'in_footer' => false ] );
} else {
	wp_enqueue_script('esg-item-editor-script', plugins_url('../../assets/js/modules/grid-editor.min.js', __FILE__ ), ['jquery'], ESG_REVISION, [ 'in_footer' => false ] );
}

$base = new Essential_Grid_Base();
$item_elements = new Essential_Grid_Item_Element();
$meta = new Essential_Grid_Meta();
$meta_link = new Essential_Grid_Meta_Linking();

//check if id exists and get data from database if so.
$skin = ['name' => ''];
$skin_id = false;

$isCreate = $base->getGetVar('create', 'true');

$title = esc_attr__('Create New Item Skin', 'essential-grid');
$save = esc_attr__('Save Item Skin', 'essential-grid');

if(intval($isCreate) > 0){ 
	//currently editing
	$skin = Essential_Grid_Item_Skin::get_essential_item_skin_by_id(intval($isCreate));
	if(!empty($skin)){
		$title = esc_attr__('Change Item Skin', 'essential-grid');
		$save = esc_attr__('Change Item Skin', 'essential-grid');
		$skin_id = intval($isCreate);
	}
}

$style_attributes = $item_elements->get_existing_elements(true);
$element_type = $item_elements->getElementsForDropdown();
$fonts_full = apply_filters('essgrid_item_skin_editor_fonts_full', []);
$meta_keys = $meta->get_all_meta_handle();
$meta_link_keys = $meta_link->get_all_link_meta_handle();
$meta_keys = array_merge($meta_keys, $meta_link_keys);

$transitions_cover = $base->get_hover_animations();
$transitions_media = $base->get_media_animations();

/* 2.1.6 - for the new home-image option */
$transitions_hover = array_slice($transitions_cover, 0, count($transitions_cover), true);
if(isset($transitions_hover['turn'])) unset($transitions_hover['turn']);
if(isset($transitions_hover['covergrowup'])) unset($transitions_hover['covergrowup']);

/* 2.2.4.2 */
$transitions_elements = array_slice($transitions_cover, 0, count($transitions_cover), true);
if(isset($transitions_elements['rotatescale'])) unset($transitions_elements['rotatescale']);
if(isset($transitions_elements['covergrowup'])) unset($transitions_elements['covergrowup']);

if(!isset($skin['params'])) $skin['params'] = []; //fallback if skin does not exist
if(!isset($skin['layers'])) $skin['layers'] = []; //fallback if skin does not exist
?>

<div id="eg-tool-panel">
	<div id="eg-global-css-dialog" class="esg-purple eg-side-buttons">
		<i>&lt;/&gt;</i><?php esc_html_e('CSS Editor', 'essential-grid'); ?>
	</div>
	<div id="eg-global-change" class="esg-green eg-side-buttons">
		<i class="rs-icon-save-light"></i><?php esc_html_e('Save Skin', 'essential-grid'); ?>
	</div>
	<a href="<?php echo esc_url($base->getViewUrl("","",'essential-'.Essential_Grid_Admin::VIEW_SUB_ITEM_SKIN_OVERVIEW)); ?>" id="eg-global-back-to-overview" class="esg-blue eg-side-buttons">
		<i class="eg-icon-th"></i><?php esc_html_e('Skin Overview', 'essential-grid'); ?>
	</a>
</div>

<div id="skin-editor-wrapper">
	<?php if ($skin_id !== false) { ?><input type="hidden" value="<?php echo esc_attr($skin_id); ?>" name="eg-item-skin-id" /><?php } ?>

	<h2 class="topheader">
		<?php esc_html_e('Item Skin Editor', 'essential-grid'); ?>
		<div class="space100"></div>
		<div class="esg-item-skin-name">
			<input type="text" name="item-skin-name" value="<?php echo esc_attr($skin['name']); ?>"/>
			<div class="div5"></div>
			<span class="esg-item-skin-name-notice">
				<?php esc_html_e('Class Prefix = ', 'essential-grid'); ?> .eg-<span class="eg-tooltip-wrap" title="<?php esc_attr_e('Each element in the Skin becomes this CSS Prefix', 'essential-grid'); ?>" id="eg-item-skin-slug"></span>-
			</span>
		</div>
	</h2>

	<div class="esg-padding-t-15">
		<div class="esg-item-skin-form-wrapper">
			<!-- START OF SETTINGS ON THE LEFT SIDE  border: 2px solid #27AE60; -->
			<form id="eg-form-item-skin-layout-settings">
				<input type="hidden" value="<?php echo esc_attr($base->getVar($skin, ['params', 'eg-item-skin-element-last-id'], 0, 'i')); ?>" name="eg-item-skin-element-last-id" />
				<div class="eg-pbox esg-box">
					<div class="esg-box-title"><i class="material-icons">menu</i><?php esc_html_e('Layout Composition', 'essential-grid'); ?></div>
					<div class="esg-box-inside">

						<div class="eg-lc-menu-wrapper">
							<div class="eg-lc-vertical-menu">
								<ul>
									<li class="selected-lc-setting" data-toshow="eg-lc-layout"><i class="eg-icon-th-large"></i><?php esc_html_e('Layout', 'essential-grid'); ?></li>
									<li data-toshow="eg-lc-cover"><i class="eg-icon-stop"></i><?php esc_html_e('Cover', 'essential-grid'); ?></li>
									<li data-toshow="eg-lc-spaces"><i class="eg-icon-indent-right"></i><?php esc_html_e('Spaces', 'essential-grid'); ?></li>
									<li data-toshow="eg-lc-content-shadow"><i class="eg-icon-picture"></i><?php esc_html_e('Shadow', 'essential-grid'); ?></li>
									<li data-toshow="eg-lc-content-animation"><i class="eg-icon-star"></i><?php esc_html_e('Animation', 'essential-grid'); ?></li>
									<li data-toshow="eg-lc-content-link-seo"><i class="eg-icon-link"></i><?php esc_html_e('Link/SEO', 'essential-grid'); ?></li>
								</ul>
							</div>

							<!-- THE LAYOUT SETTINGS -->
							<div id="eg-lc-layout" class="esg-lc-settings-container active-esc">
								<label for="choose-preset" class="eg-group-setter"><?php esc_html_e('Grid Layout', 'essential-grid'); ?></label><!--
								--><span class="esg-display-inline-block"><input type="radio" name="choose-layout" value="even"  <?php checked($base->getVar($skin, ['params', 'choose-layout'], 'even'), 'even'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Each item gets Same Height. Width and Height are Item Ratio dependent.', 'essential-grid'); ?>"><?php esc_html_e('Even', 'essential-grid'); ?></span></span><div class="space18"></div><!--
								--><span><input type="radio" name="choose-layout" value="masonry" <?php checked($base->getVar($skin, ['params', 'choose-layout'], 'even'), 'masonry'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Items height are depending on Media height and Content height.', 'essential-grid'); ?>"><?php esc_html_e('Masonry', 'essential-grid'); ?></span></span>
								<!-- MASONRY SETTINGS-->
								<div id="eg-show-content">
									<div class="div13"></div>
									<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Position of Fixed Content', 'essential-grid'); ?>"><?php esc_html_e('Content', 'essential-grid'); ?></label><!--
									--><select name="show-content">
										<option value="bottom" <?php selected($base->getVar($skin, ['params', 'show-content'], 'none'), 'bottom'); ?>><?php esc_html_e('Bottom', 'essential-grid'); ?></option>
										<option value="top" <?php selected($base->getVar($skin, ['params', 'show-content'], 'none'), 'top'); ?>><?php esc_html_e('Top', 'essential-grid'); ?></option>
										<option value="none" <?php selected($base->getVar($skin, ['params', 'show-content'], 'none'), 'none'); ?>><?php esc_html_e('Hide', 'essential-grid'); ?></option>
									</select><div class="space18"></div><!--
									--><select name="content-align">
										<option value="left" <?php selected($base->getVar($skin, ['params', 'content-align'], 'left'), 'left'); ?>><?php esc_html_e('Left', 'essential-grid'); ?></option>
										<option value="center" <?php selected($base->getVar($skin, ['params', 'content-align'], 'left'), 'center'); ?>><?php esc_html_e('Center', 'essential-grid'); ?></option>
										<option value="right" <?php selected($base->getVar($skin, ['params', 'content-align'], 'left'), 'right'); ?>><?php esc_html_e('Right', 'essential-grid'); ?></option>
									</select>
								</div>
								<div class="div13"></div>
								<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Media Repeat', 'essential-grid'); ?>"><?php esc_html_e('Media Repeat', 'essential-grid'); ?></label><!--
								--><select name="image-repeat">
										<option value="no-repeat" <?php selected($base->getVar($skin, ['params', 'image-repeat'], 'no-repeat'), 'no-repeat'); ?>><?php esc_html_e('no-repeat', 'essential-grid'); ?></option>
										<option value="repeat" <?php selected($base->getVar($skin, ['params', 'image-repeat'], 'no-repeat'), 'repeat'); ?>><?php esc_html_e('repeat', 'essential-grid'); ?></option>
										<option value="repeat-x" <?php selected($base->getVar($skin, ['params', 'image-repeat'], 'no-repeat'), 'repeat-x'); ?>><?php esc_html_e('repeat-x', 'essential-grid'); ?></option>
										<option value="repeat-y" <?php selected($base->getVar($skin, ['params', 'image-repeat'], 'no-repeat'), 'repeat-y'); ?>><?php esc_html_e('repeat-y', 'essential-grid'); ?></option>
								</select>
								<div class="div13"></div>
								<label  class="eg-tooltip-wrap" title="<?php esc_attr_e('Media Fit', 'essential-grid'); ?>"><?php esc_html_e('Media Fit', 'essential-grid'); ?></label><!--
								--><select name="image-fit">
									<option value="contain" <?php selected($base->getVar($skin, ['params', 'image-fit'], 'cover'), 'contain'); ?>><?php esc_html_e('Contain', 'essential-grid'); ?></option>
									<option value="cover" <?php selected($base->getVar($skin, ['params', 'image-fit'], 'cover'), 'cover'); ?>><?php esc_html_e('Cover', 'essential-grid'); ?></option>
								</select>
								<div class="div13"></div>
								<label  class="eg-tooltip-wrap" title="<?php esc_attr_e('Media Align horizontal and vertical', 'essential-grid'); ?>"><?php esc_html_e('Media Align', 'essential-grid'); ?></label><!--
								--><select name="image-align-horizontal">
									<option value="left" <?php selected($base->getVar($skin, ['params', 'image-align-horizontal'], 'center'), 'left'); ?>><?php esc_html_e('Hor. Left', 'essential-grid'); ?></option>
									<option value="center" <?php selected($base->getVar($skin, ['params', 'image-align-horizontal'], 'center'), 'center'); ?>><?php esc_html_e('Hor. Center', 'essential-grid'); ?></option>
									<option value="right" <?php selected($base->getVar($skin, ['params', 'image-align-horizontal'], 'center'), 'right'); ?>><?php esc_html_e('Hor. Right', 'essential-grid'); ?></option>
								</select><div class="space18"></div><!--
								--><select name="image-align-vertical">
									<option value="top" <?php selected($base->getVar($skin, ['params', 'image-align-vertical'], 'center'), 'top'); ?>><?php esc_html_e('Ver. Top', 'essential-grid'); ?></option>
									<option value="center" <?php selected($base->getVar($skin, ['params', 'image-align-vertical'], 'center'), 'center'); ?>><?php esc_html_e('Ver. Center', 'essential-grid'); ?></option>
									<option value="bottom" <?php selected($base->getVar($skin, ['params', 'image-align-vertical'], 'center'), 'bottom'); ?>><?php esc_html_e('Ver. Bottom', 'essential-grid'); ?></option>
								</select>
								<!-- EVEN SETTINGS -->
								<div id="eg-show-ratio" >
									<div class="div13"></div>
									<label class="eg-group-setter"><?php esc_html_e('Ratio X', 'essential-grid'); ?></label><span id="element-x-ratio" class="slider-settings eg-tooltip-wrap" title="<?php esc_attr_e('Width Ratio of Item.', 'essential-grid'); ?>"></span><!--
									--><div class="space18"></div><input class="input-settings-small element-setting" type="text" name="element-x-ratio" value="<?php echo esc_attr($base->getVar($skin, ['params', 'element-x-ratio'], 4, 'i')); ?>" />
									<div class="div13"></div>
									<label class="eg-group-setter"><?php esc_html_e('Ratio Y', 'essential-grid'); ?></label><span id="element-y-ratio" class="slider-settings eg-tooltip-wrap" title="<?php esc_attr_e('Height Ratio of Item.', 'essential-grid'); ?>"></span><!--
									--><div class="space18"></div><input class="input-settings-small element-setting" type="text" name="element-y-ratio" value="<?php echo esc_attr($base->getVar($skin, ['params', 'element-y-ratio'], 3, 'i')); ?>" />
								</div>

								<div class="div13"></div>

								<!-- 2.1.6 -->
								<!-- SPLITTED ITEMS -->
								<label  class="eg-tooltip-wrap" title="<?php esc_attr_e('Display Media and Content side-by-side', 'essential-grid'); ?>"><?php esc_html_e('Split Item', 'essential-grid'); ?></label><!--
								--><select name="splitted-item">
									<option value="none" <?php selected($base->getVar($skin, ['params', 'splitted-item'], 'none'), 'none'); ?>><?php esc_html_e('No Split', 'essential-grid'); ?></option>
									<option value="left" <?php selected($base->getVar($skin, ['params', 'splitted-item'], 'none'), 'left'); ?>><?php esc_html_e('Media Left', 'essential-grid'); ?></option>
									<option value="right" <?php selected($base->getVar($skin, ['params', 'splitted-item'], 'none'), 'right'); ?>><?php esc_html_e('Media Right', 'essential-grid'); ?></option>
								</select>
							</div>
							
							<!-- THE COVER SETTINGS -->
							<div id="eg-lc-cover" class="esg-lc-settings-container">
								<!-- COVER LAYOUT -->
								<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Dynamic Covering Content Type. Show Cover Background on full Media, or only under Cover Contents ?', 'essential-grid'); ?>"><?php esc_html_e('Cover Type', 'essential-grid'); ?></label><!--
								--><select id="cover-type" name="cover-type">
									<option value="full" <?php selected($base->getVar($skin, ['params', 'cover-type'], 'full'), 'full'); ?>><?php esc_html_e('Full', 'essential-grid'); ?></option>
									<option value="content" <?php selected($base->getVar($skin, ['params', 'cover-type'], 'full'), 'content'); ?>><?php esc_html_e('Content Based', 'essential-grid'); ?></option>
								</select>
								<div class="div13"></div>
								<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Add a CSS mix-blend-mode filter', 'essential-grid'); ?>"><?php esc_html_e('Blend Mode', 'essential-grid'); ?></label><!--
								--><select id="cover-blend-mode" name="cover-blend-mode">
									<option value="normal" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'normal'); ?>><?php esc_html_e('Normal', 'essential-grid'); ?></option>
									<option value="multiply" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'multiply'); ?>><?php esc_html_e('Multiply', 'essential-grid'); ?></option>
									<option value="screen" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'screen'); ?>><?php esc_html_e('Screen', 'essential-grid'); ?></option>
									<option value="overlay" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'overlay'); ?>><?php esc_html_e('Overlay', 'essential-grid'); ?></option>
									<option value="darken" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'darken'); ?>><?php esc_html_e('Darken', 'essential-grid'); ?></option>
									<option value="lighten" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'lighten'); ?>><?php esc_html_e('Lighten', 'essential-grid'); ?></option>
									<option value="color-dodge" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'color-dodge'); ?>><?php esc_html_e('Color Dodge', 'essential-grid'); ?></option>
									<option value="color-burn" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'color-burn'); ?>><?php esc_html_e('Color Burn', 'essential-grid'); ?></option>
									<option value="hard-light" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'hard-light'); ?>><?php esc_html_e('Hard Light', 'essential-grid'); ?></option>
									<option value="soft-light" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'soft-light'); ?>><?php esc_html_e('Soft Light', 'essential-grid'); ?></option>
									<option value="difference" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'difference'); ?>><?php esc_html_e('Difference', 'essential-grid'); ?></option>
									<option value="exclusion" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'exclusion'); ?>><?php esc_html_e('Exclusion', 'essential-grid'); ?></option>
									<option value="hue" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'hue'); ?>><?php esc_html_e('Hue', 'essential-grid'); ?></option>
									<option value="saturation" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'saturation'); ?>><?php esc_html_e('Saturation', 'essential-grid'); ?></option>
									<option value="color" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'color'); ?>><?php esc_html_e('Color', 'essential-grid'); ?></option>
									<option value="luminosity" <?php selected($base->getVar($skin, ['params', 'cover-blend-mode'], 'normal'), 'luminosity'); ?>><?php esc_html_e('Luminosity', 'essential-grid'); ?></option>
								</select>
								<div class="div13"></div>
								<label class="eg-cover-setter eg-tooltip-wrap" title="<?php esc_attr_e('Background Color of Covers', 'essential-grid'); ?>"><?php esc_html_e('Background Color', 'essential-grid'); ?></label><!--
								--><input class="element-setting" type="text" name="container-background-color" id="container-background-color" value="<?php echo esc_attr($base->getVar($skin, ['params', 'container-background-color'], '#363839', 's')); ?>" />
								<div class="div13"></div>

								<label class="eg-cover-setter eg-tooltip-wrap" title="<?php esc_attr_e('Show without a Hover on Desktop', 'essential-grid'); ?>"><?php esc_html_e('Always Visible on Desktop', 'essential-grid'); ?></label><!--
								--><input type="checkbox" name="cover-always-visible-desktop" <?php checked($base->getVar($skin, ['params', 'cover-always-visible-desktop']), 'true'); ?> />
								<div class="div13"></div>
								<label class="eg-cover-setter eg-tooltip-wrap" title="<?php esc_attr_e('Show without a Tap on Mobile', 'essential-grid'); ?>"><?php esc_html_e('Always Visible on Mobile', 'essential-grid'); ?></label><!--
								--><input type="checkbox" name="cover-always-visible-mobile" <?php checked($base->getVar($skin, ['params', 'cover-always-visible-mobile']), 'true'); ?> />

								<div class="esg-display-none">
									<label class="eg-group-setter"><?php esc_html_e('Background Fit', 'essential-grid'); ?></label><!--
									--><select name="cover-background-size">
										<option value="cover" <?php selected($base->getVar($skin, ['params', 'cover-background-size'], 'cover'), 'cover'); ?>><?php esc_html_e('Cover', 'essential-grid'); ?></option>
										<option value="contain" <?php selected($base->getVar($skin, ['params', 'cover-background-size'], 'cover'), 'contain'); ?>><?php esc_html_e('Contain', 'essential-grid'); ?></option>
										<option value="auto" <?php selected($base->getVar($skin, ['params', 'cover-background-size'], 'cover'), 'auto'); ?>><?php esc_html_e('Normal', 'essential-grid'); ?></option>
									</select>
								</div>
								<div class="esg-display-none">
									<label class="eg-group-setter"><?php esc_html_e('Background Repeat', 'essential-grid'); ?></label><!--
									--><select name="cover-background-repeat">
										<option value="no-repeat" <?php selected($base->getVar($skin, ['params', 'cover-background-repeat'], 'no-repeat'), 'auto'); ?>><?php esc_html_e('no-repeat', 'essential-grid'); ?></option>
										<option value="repeat" <?php selected($base->getVar($skin, ['params', 'cover-background-repeat'], 'no-repeat'), 'repeat'); ?>><?php esc_html_e('repeat', 'essential-grid'); ?></option>
										<option value="repeat-x" <?php selected($base->getVar($skin, ['params', 'cover-background-repeat'], 'no-repeat'), 'repeat-x'); ?>><?php esc_html_e('repeat-x', 'essential-grid'); ?></option>
										<option value="repeat-y" <?php selected($base->getVar($skin, ['params', 'cover-background-repeat'], 'no-repeat'), 'repeat-y'); ?>><?php esc_html_e('repeat-y', 'essential-grid'); ?></option>
									</select>
								</div>
								<div class="esg-display-none">
									<?php
									$cover_image_url = false;
									$cover_image_id = $base->getVar($skin, ['params', 'cover-background-image'], '0', 'i');
									if($cover_image_id > 0){
										$cover_image_url = wp_get_attachment_image_src($cover_image_id, 'full');
									}
									?><input type="hidden" value="<?php echo esc_attr($base->getVar($skin, ['params', 'cover-background-image'], '0', 'i')); ?>" name="cover-background-image"><!--
									--><input type="hidden" value="<?php echo ($cover_image_url !== false) ? esc_attr($cover_image_url[0]) : ''; ?>" name="cover-background-image-url"><!--
									--><div id="cover-background-image-wrap"<?php echo ($cover_image_url !== false) ? ' style="background-image: url('.esc_attr($cover_image_url[0]).'); background-size: 100% 100%;"' : ''; ?>><?php esc_html_e("Click to<br>Select<br>Image", 'essential-grid'); ?></div><!--
									--><i class="eg-icon-trash" id="remove-cover-background-image-wrap"><?php esc_html_e('Remove', 'essential-grid'); ?></i>
								</div>
							</div>

							<!-- SPACES -->
							<div id="eg-lc-spaces" class="esg-lc-settings-container">
								<ul class="eg-submenu">
									<li data-toshow="eg-style-full" class="selected-submenu-setting eg-tooltip-wrap" title="<?php esc_attr_e('Padding and border of the full item', 'essential-grid'); ?>"><i class="eg-icon-stop"></i><?php esc_html_e('Full Item', 'essential-grid'); ?></li><!--
									--><li data-toshow="eg-style-content" class="eg-tooltip-wrap" title="<?php esc_attr_e('Padding and border of the Fixed Content', 'essential-grid'); ?>"><i class="eg-icon-doc-text"></i><?php esc_html_e('Content', 'essential-grid'); ?></li>
								</ul>

								<!-- FULL STYLING -->
								<div id="eg-style-full">
									<!-- THE PADDING, BORDER AND BG COLOR -->
									<div class="div13"></div>
									<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Background Color of Full Item', 'essential-grid'); ?>"><?php esc_html_e('Item BG Color', 'essential-grid'); ?></label><!--
									--><input class="element-setting" name="full-bg-color" type="text" id="full-bg-color" value="<?php echo esc_attr($base->getVar($skin, ['params', 'full-bg-color'], '#ffffff')); ?>">
									<div class="div13"></div>
									<?php
									$padding = $base->getVar($skin, ['params', 'full-padding']);
									?>
									<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Top,Right,Bottom,Left Padding of Item', 'essential-grid'); ?>"><?php esc_html_e('Item Paddings', 'essential-grid'); ?></label><!--
									--><input class="input-settings-small element-setting " type="text" name="full-padding[]" value="<?php echo (isset($padding[0])) ? esc_attr($padding[0]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="text" name="full-padding[]" value="<?php echo (isset($padding[1])) ? esc_attr($padding[1]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="text" name="full-padding[]" value="<?php echo (isset($padding[2])) ? esc_attr($padding[2]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="text" name="full-padding[]" value="<?php echo (isset($padding[3])) ? esc_attr($padding[3]) : 0; ?>" /><div class="space18"></div>
									<div class="div13"></div>
									<?php
									$border = $base->getVar($skin, ['params', 'full-border']);
									?>
									<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Top,Right,Bottom,Left Border of Item', 'essential-grid'); ?>"><?php esc_html_e('Item Border', 'essential-grid'); ?></label><!--
									--><input class="input-settings-small element-setting " type="text" name="full-border[]" value="<?php echo (isset($border[0])) ? esc_attr($border[0]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="text" name="full-border[]" value="<?php echo (isset($border[1])) ? esc_attr($border[1]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="text" name="full-border[]" value="<?php echo (isset($border[2])) ? esc_attr($border[2]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="text" name="full-border[]" value="<?php echo (isset($border[3])) ? esc_attr($border[3]) : 0; ?>" />
									<div class="div13"></div>
									<?php
									$radius = $base->getVar($skin, ['params', 'full-border-radius']);
									?>
									<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Top Left,Top Right,Bottom Right, Bottom Left Border Radius of Item', 'essential-grid'); ?>"><?php esc_html_e('Border Radius', 'essential-grid'); ?></label><!--
									--><input class="input-settings-small element-setting " type="text" name="full-border-radius[]" value="<?php echo (isset($radius[0])) ? esc_attr($radius[0]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="text" name="full-border-radius[]" value="<?php echo (isset($radius[1])) ? esc_attr($radius[1]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="text" name="full-border-radius[]" value="<?php echo (isset($radius[2])) ? esc_attr($radius[2]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="text" name="full-border-radius[]" value="<?php echo (isset($radius[3])) ? esc_attr($radius[3]) : 0; ?>" /><div class="space18"></div><!--
									--><select name="full-border-radius-type" class="esg-w-50">
										<option value="px" <?php selected($base->getVar($skin, ['params', 'full-border-radius-type'], 'px'), 'px'); ?>>px</option>
										<option value="%" <?php selected($base->getVar($skin, ['params', 'full-border-radius-type'], 'px'), '%'); ?>>%</option>
									</select>
									<div class="div13"></div>
									<label><?php esc_html_e('Border Color', 'essential-grid'); ?></label><!--
									--><input class="element-setting" name="full-border-color" type="text" id="full-border-color" value="<?php echo esc_attr($base->getVar($skin, ['params', 'full-border-color'], 'transparent')); ?>" data-mode="single">
									<div class="div13"></div>
									<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Border Line Style', 'essential-grid'); ?>"><?php esc_html_e('Border Style', 'essential-grid'); ?></label><!--
									--><select name="full-border-style">
										<option value="none" <?php selected($base->getVar($skin, ['params', 'full-border-style'], 'none'), 'none'); ?>><?php esc_html_e('none', 'essential-grid'); ?></option>
										<option value="solid" <?php selected($base->getVar($skin, ['params', 'full-border-style'], 'none'), 'solid'); ?>><?php esc_html_e('solid', 'essential-grid'); ?></option>
										<option value="dotted" <?php selected($base->getVar($skin, ['params', 'full-border-style'], 'none'), 'dotted'); ?>><?php esc_html_e('dotted', 'essential-grid'); ?></option>
										<option value="dashed" <?php selected($base->getVar($skin, ['params', 'full-border-style'], 'none'), 'dashed'); ?>><?php esc_html_e('dashed', 'essential-grid'); ?></option>
										<option value="double" <?php selected($base->getVar($skin, ['params', 'full-border-style'], 'none'), 'double'); ?>><?php esc_html_e('double', 'essential-grid'); ?></option>
									</select>
									<div class="div13"></div>
									<label><?php esc_html_e('Overflow Hidden', 'essential-grid'); ?></label><!--
									--><span class="esg-display-inline-block"><input type="radio" name="full-overflow-hidden" value="true"  <?php checked($base->getVar($skin, ['params', 'full-overflow-hidden'], 'false'), 'true'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Hide Overflow (fix border radius issues)', 'essential-grid'); ?>"><?php esc_html_e('On', 'essential-grid'); ?></span></span><div class="space18"></div><!--
									--><span><input type="radio" name="full-overflow-hidden" value="false" <?php checked($base->getVar($skin, ['params', 'full-overflow-hidden'], 'false'), 'false'); ?>><span class="eg-tooltip-wrap" title="<?php esc_attr_e('Show Overflowed content', 'essential-grid'); ?>"><?php esc_html_e('Off', 'essential-grid'); ?></span></span>
								</div>
								<div id="eg-style-content" class="esg-display-none">
									<!-- THE PADDING, BORDER AND BG COLOR -->
									<div class="div13"></div>
									<label><?php esc_html_e('Content BG Color', 'essential-grid'); ?></label><!--
									--><input class="element-setting" name="content-bg-color" type="text" id="content-bg-color" value="<?php echo esc_attr($base->getVar($skin, ['params', 'content-bg-color'], '#ffffff')); ?>">
									<div class="div13"></div>
									<?php
									$padding = $base->getVar($skin, ['params', 'content-padding']);
									?>
									<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Top, Right, Bottom, Left Padding of Fix Content', 'essential-grid'); ?>"><?php esc_html_e('Content Paddings', 'essential-grid'); ?></label><!--
									--><input class="input-settings-small element-setting " type="number" name="content-padding[]" value="<?php echo (isset($padding[0])) ? esc_attr($padding[0]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="number" name="content-padding[]" value="<?php echo (isset($padding[1])) ? esc_attr($padding[1]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="number" name="content-padding[]" value="<?php echo (isset($padding[2])) ? esc_attr($padding[2]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="number" name="content-padding[]" value="<?php echo (isset($padding[3])) ? esc_attr($padding[3]) : 0; ?>" />
									<div class="div13"></div>
									<?php
									$border = $base->getVar($skin, ['params', 'content-border']);
									?>
									<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Top, Right, Bottom, Left Padding of Fix Content', 'essential-grid'); ?>"><?php esc_html_e('Content Border', 'essential-grid'); ?></label><!--
									--><input class="input-settings-small element-setting " type="number" name="content-border[]" value="<?php echo (isset($border[0])) ? esc_attr($border[0]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="number" name="content-border[]" value="<?php echo (isset($border[1])) ? esc_attr($border[1]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="number" name="content-border[]" value="<?php echo (isset($border[2])) ? esc_attr($border[2]) : 0; ?>" /><div class="space18"></div><!--
									--><input class="input-settings-small element-setting" type="number" name="content-border[]" value="<?php echo (isset($border[3])) ? esc_attr($border[3]) : 0; ?>" />
									<div class="div13"></div>
									<?php
									$radius = $base->getVar($skin, ['params', 'content-border-radius']);
									?>
									<label  class="eg-tooltip-wrap" title="<?php esc_attr_e('Top Left, Top Right, Bottom Right, Bottom Left Border Radius of Fix Content', 'essential-grid'); ?>"><?php esc_html_e('Border Radius', 'essential-grid'); ?></label><!--
									--><input  class="input-settings-small element-setting " type="text" name="content-border-radius[]" value="<?php echo (isset($radius[0])) ? esc_attr($radius[0]) : 0; ?>" /><div class="space18"></div><!--
									--><input  class="input-settings-small element-setting" type="text" name="content-border-radius[]" value="<?php echo (isset($radius[1])) ? esc_attr($radius[1]) : 0; ?>" /><div class="space18"></div><!--
									--><input  class="input-settings-small element-setting" type="text" name="content-border-radius[]" value="<?php echo (isset($radius[2])) ? esc_attr($radius[2]) : 0; ?>" /><div class="space18"></div><!--
									--><input  class="input-settings-small element-setting" type="text" name="content-border-radius[]" value="<?php echo (isset($radius[3])) ? esc_attr($radius[3]) : 0; ?>" /><div class="space18"></div><!--
									--><select name="content-border-radius-type" class="esg-w-50">
										<option value="px" <?php selected($base->getVar($skin, ['params', 'content-border-radius-type'], 'px'), 'px'); ?>>px</option>
										<option value="%" <?php selected($base->getVar($skin, ['params', 'content-border-radius-type'], 'px'), '%'); ?>>%</option>
									</select>
									<div class="div13"></div>
									<label><?php esc_html_e('Border Color', 'essential-grid'); ?></label><!--
									--><input class="element-setting" name="content-border-color" type="text" id="content-border-color" value="<?php echo esc_attr($base->getVar($skin, ['params', 'content-border-color'], 'transparent')); ?>" data-mode="single">
									<div class="div13"></div>
									<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Border Line Style', 'essential-grid'); ?>" ><?php esc_html_e('Border Style', 'essential-grid'); ?></label><!--
									--><select name="content-border-style">
										<option value="none" <?php selected($base->getVar($skin, ['params', 'content-border-style'], 'none'), 'none'); ?>><?php esc_html_e('none', 'essential-grid'); ?></option>
										<option value="solid" <?php selected($base->getVar($skin, ['params', 'content-border-style'], 'none'), 'solid'); ?>><?php esc_html_e('solid', 'essential-grid'); ?></option>
										<option value="dotted" <?php selected($base->getVar($skin, ['params', 'content-border-style'], 'none'), 'dotted'); ?>><?php esc_html_e('dotted', 'essential-grid'); ?></option>
										<option value="dashed" <?php selected($base->getVar($skin, ['params', 'content-border-style'], 'none'), 'dashed'); ?>><?php esc_html_e('dashed', 'essential-grid'); ?></option>
										<option value="double" <?php selected($base->getVar($skin, ['params', 'content-border-style'], 'none'), 'double'); ?>><?php esc_html_e('double', 'essential-grid'); ?></option>
									</select>
								</div>
							</div>

							<!-- THE CONTENT SHADOW SETTINGS -->
							<div id="eg-lc-content-shadow" class="esg-lc-settings-container ">
								<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Drop Shadow of Element(s)', 'essential-grid'); ?>" ><?php esc_html_e('Use Shadow', 'essential-grid'); ?></label><!--
									--><?php
									$shadow_type = $base->getVar($skin, ['params', 'all-shadow-used'], 'none');
									?><select id="all-shadow-used" name="all-shadow-used">
										<option<?php selected($shadow_type, 'none'); ?> value="none"><?php esc_html_e('none', 'essential-grid'); ?></option>
										<option<?php selected($shadow_type, 'cover'); ?> value="cover"><?php esc_html_e('cover (inset)', 'essential-grid'); ?></option>
										<option<?php selected($shadow_type, 'media'); ?> value="media"><?php esc_html_e('media', 'essential-grid'); ?></option>
										<option<?php selected($shadow_type, 'content'); ?> value="content"><?php esc_html_e('content', 'essential-grid'); ?></option>
										<option<?php selected($shadow_type, 'both'); ?> value="both"><?php esc_html_e('media/content', 'essential-grid'); ?></option>
									</select>
								<div class="div13"></div>
								<label><?php esc_html_e('Shadow Color', 'essential-grid'); ?></label><!--
								--><input class="element-setting" name="content-shadow-color" type="text" id="content-shadow-color" value="<?php echo esc_attr($base->getVar($skin, ['params', 'content-shadow-color'], '#000000')); ?>" data-mode="single">
								<div class="div13"></div>
								<?php
									$shadow = $base->getVar($skin, ['params', 'content-box-shadow']);
									?>
								<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Position of horizontal shadow(Negative values possible)', 'essential-grid'); ?>, <?php esc_html_e('blur distance', 'essential-grid'); ?>, <?php esc_html_e('size of shadow', 'essential-grid'); ?>"><?php esc_html_e('Shadow', 'essential-grid'); ?></label><!--
								--><input class="input-settings-small element-setting " type="text" name="content-box-shadow[]" value="<?php echo (isset($shadow[0])) ? esc_attr($shadow[0]) : 0; ?>" /><div class="space18"></div><!--
								--><input class="input-settings-small element-setting" type="text" name="content-box-shadow[]" value="<?php echo (isset($shadow[1])) ? esc_attr($shadow[1]) : 0; ?>" /><div class="space18"></div><!--
								--><input class="input-settings-small element-setting" type="text" name="content-box-shadow[]" value="<?php echo (isset($shadow[2])) ? esc_attr($shadow[2]) : 0; ?>" /><div class="space18"></div><!--
								--><input class="input-settings-small element-setting" type="text" name="content-box-shadow[]" value="<?php echo (isset($shadow[3])) ? esc_attr($shadow[3]) : 0; ?>" />

								<div id="content-box-shadow-inset">
									<div class="div13"></div>
									<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Display the shadow inside the container', 'essential-grid'); ?>">Inset Style</label><!--
									--><input type="checkbox" id="content-shadow-inset" name="content-box-shadow-inset" <?php checked($base->getVar($skin, ['params', 'content-box-shadow-inset'], 'false'), 'true'); ?>>
								</div>

								<div id="content-box-shadow-hover">
									<div class="div13"></div>
									<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Animate the Shadow on Hover', 'essential-grid'); ?>">Animate onHover</label><!--
									--><input type="checkbox" name="content-box-shadow-hover" <?php checked($base->getVar($skin, ['params', 'content-box-shadow-hover'], 'false'), 'true'); ?>>
								</div>
							</div>

							<!-- THE CONTENT ANIMATION SETTINGS -->
							<div id="eg-lc-content-animation" class="esg-lc-settings-container ">
								<!-- COVER ANIMATION -->
								<div id="eg-cover-animation-top">
									<div class="div13"></div>
									<label><?php esc_html_e('Cover Top', 'essential-grid'); ?></label><!--
									--><select class="cover-animation-select" name="cover-animation-top">
										<?php foreach($transitions_cover as $handle => $name){ ?>
											<option value="<?php echo esc_attr($handle); ?>" <?php selected($base->getVar($skin, ['params', 'cover-animation-top'], 'fade'), $handle); ?>><?php echo esc_html($name); ?></option>
											<?php
										}
										?>
									</select><div class="space5"></div><!--
									--><select name="cover-animation-duration-top" class="eg-tooltip-wrap esg-w-70" title="<?php esc_attr_e('The animation duration (ms)', 'essential-grid'); ?>">
										<option value="default" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-top'], 'default'), 'default'); ?>>default</option>
										<option value="200" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-top'], 'default'), '200'); ?>>200</option>
										<option value="300" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-top'], 'default'), '300'); ?>>300</option>
										<option value="400" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-top'], 'default'), '400'); ?>>400</option>
										<option value="500" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-top'], 'default'), '500'); ?>>500</option>
										<option value="750" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-top'], 'default'), '750'); ?>>750</option>
										<option value="1000" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-top'], 'default'), '1000'); ?>>1000</option>
										<option value="1500" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-top'], 'default'), '1500'); ?>>1500</option>
									</select><div class="space5"></div><!--
									--><select name="cover-animation-top-type" class="esg-w-60" title="<?php esc_attr_e('Show or Hide on hover. In = Show on Hover, Out = Hide on hover', 'essential-grid'); ?>">
										<option value="" <?php selected($base->getVar($skin, ['params', 'cover-animation-top-type']), ''); ?>><?php esc_html_e('in', 'essential-grid'); ?></option>
										<option value="out" <?php selected($base->getVar($skin, ['params', 'cover-animation-top-type']), 'out'); ?>><?php esc_html_e('out', 'essential-grid'); ?></option>
									</select><div class="space5"></div><!--
									--><input class="input-settings-small element-setting eg-tooltip-wrap input-animation-delay" title="<?php esc_attr_e('Delay before the Animation starts', 'essential-grid'); ?>" type="text" name="cover-animation-delay-top" value="<?php echo esc_attr($base->getVar($skin, ['params', 'cover-animation-delay-top'], '0', 'i')); ?>" /><div class="space5"></div><!--
									--><input class="element-setting cover-animation-color" type="hidden" data-mode="single" name="cover-animation-color-top" id="cover-animation-color-top" value="<?php echo esc_attr($base->getVar($skin, ['params', 'cover-animation-color-top'], '#FFFFFF', 's')); ?>" />
								</div>
								
								<div id="eg-cover-animation-center">
									<div class="div13"></div>
									<label><?php esc_html_e('Cover (Center)', 'essential-grid'); ?></label><!--
									--><select class="cover-animation-select" name="cover-animation-center">
										<?php foreach($transitions_cover as $handle => $name){ ?>
										<option value="<?php echo esc_attr($handle); ?>" <?php selected($base->getVar($skin, ['params', 'cover-animation-center'], 'fade'), $handle); ?>><?php echo esc_html($name); ?></option>
										<?php } ?>
									</select><div class="space5"></div><!--
									--><select name="cover-animation-duration-center" class="eg-tooltip-wrap esg-w-70" title="<?php esc_attr_e('The animation duration (ms)', 'essential-grid'); ?>">
										<option value="default" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-center'], 'default'), 'default'); ?>>default</option>
										<option value="200" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-center'], 'default'), '200'); ?>>200</option>
										<option value="300" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-center'], 'default'), '300'); ?>>300</option>
										<option value="400" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-center'], 'default'), '400'); ?>>400</option>
										<option value="500" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-center'], 'default'), '500'); ?>>500</option>
										<option value="750" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-center'], 'default'), '750'); ?>>750</option>
										<option value="1000" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-center'], 'default'), '1000'); ?>>1000</option>
										<option value="1500" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-center'], 'default'), '1500'); ?>>1500</option>
									</select><div class="space5"></div><!--
									--><select name="cover-animation-center-type" class="eg-tooltip-wrap esg-w-60" title="<?php esc_attr_e('Show or Hide on hover. In = Show on Hover, Out = Hide on hover', 'essential-grid'); ?>">
										<option value="" <?php selected($base->getVar($skin, ['params', 'cover-animation-center-type']), ''); ?>><?php esc_html_e('in', 'essential-grid'); ?></option>
										<option value="out" <?php selected($base->getVar($skin, ['params', 'cover-animation-center-type']), 'out'); ?>><?php esc_html_e('out', 'essential-grid'); ?></option>
									</select><div class="space5"></div><!--
									--><input class="input-settings-small element-setting eg-tooltip-wrap input-animation-delay" title="<?php esc_attr_e('Delay before the Animation starts', 'essential-grid'); ?>" type="text" name="cover-animation-delay-center" value="<?php echo esc_attr($base->getVar($skin, ['params', 'cover-animation-delay-center'], '0', 'i')); ?>" /><div class="space5"></div><!--
									--><input class="element-setting cover-animation-color" type="hidden" data-mode="single" name="cover-animation-color-center" id="cover-animation-color-center" value="<?php echo esc_attr($base->getVar($skin, ['params', 'cover-animation-color-center'], '#FFFFFF', 's')); ?>" />
								</div>

								<div id="eg-cover-animation-bottom">
									<div class="div13"></div>
									<label><?php esc_html_e('Cover Bottom', 'essential-grid'); ?></label><!--
									--><select class="cover-animation-select" name="cover-animation-bottom">
										<?php foreach($transitions_cover as $handle => $name){ ?>
										<option value="<?php echo esc_attr($handle); ?>" <?php selected($base->getVar($skin, ['params', 'cover-animation-bottom'], 'fade'), $handle); ?>><?php echo esc_html($name); ?></option>
										<?php } ?>
									</select><div class="space5"></div><!--
									--><select name="cover-animation-duration-bottom" class="eg-tooltip-wrap esg-w-70" title="<?php esc_attr_e('The animation duration (ms)', 'essential-grid'); ?>">
										<option value="default" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-bottom'], 'default'), 'default'); ?>>default</option>
										<option value="200" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-bottom'], 'default'), '200'); ?>>200</option>
										<option value="300" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-bottom'], 'default'), '300'); ?>>300</option>
										<option value="400" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-bottom'], 'default'), '400'); ?>>400</option>
										<option value="500" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-bottom'], 'default'), '500'); ?>>500</option>
										<option value="750" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-bottom'], 'default'), '750'); ?>>750</option>
										<option value="1000" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-bottom'], 'default'), '1000'); ?>>1000</option>
										<option value="1500" <?php selected($base->getVar($skin, ['params', 'cover-animation-duration-bottom'], 'default'), '1500'); ?>>1500</option>
									</select><div class="space5"></div><!--
									--><select name="cover-animation-bottom-type" class="eg-tooltip-wrap esg-w-60" title="<?php esc_attr_e('Show or Hide on hover. In = Show on Hover, Out = Hide on hover', 'essential-grid'); ?>">
										<option value="" <?php selected($base->getVar($skin, ['params', 'cover-animation-bottom-type']), ''); ?>><?php esc_html_e('in', 'essential-grid'); ?></option>
										<option value="out" <?php selected($base->getVar($skin, ['params', 'cover-animation-bottom-type']), 'out'); ?>><?php esc_html_e('out', 'essential-grid'); ?></option>
									</select><div class="space5"></div><!--
									--><input class="input-settings-small element-setting eg-tooltip-wrap input-animation-delay" title="<?php esc_attr_e('Delay before the Animation starts', 'essential-grid'); ?>" type="text" name="cover-animation-delay-bottom" value="<?php echo esc_attr($base->getVar($skin, ['params', 'cover-animation-delay-bottom'], '0', 'i')); ?>" /><div class="space5"></div><!--
									--><input class="element-setting cover-animation-color" type="hidden" data-mode="single" name="cover-animation-color-bottom" id="cover-animation-color-bottom" value="<?php echo esc_attr($base->getVar($skin, ['params', 'cover-animation-color-bottom'], '#FFFFFF', 's')); ?>" />
								</div>

								<!-- GROUP ANIMATION -->
								<div class="div13"></div>
								<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Animation Effect on Cover and on All Cover elements Grouped. This will not replace the Animation but add a global animation extra.', 'essential-grid'); ?>"><?php esc_html_e('Group Animation', 'essential-grid'); ?></label><!--
								--><select name="cover-group-animation">
									<?php foreach($transitions_cover as $handle => $name){
										if(preg_match('/collapse|line|circle|spiral/', $handle)) continue;
									?>
									<option value="<?php echo esc_attr($handle); ?>" <?php selected($base->getVar($skin, ['params', 'cover-group-animation'], 'none'), $handle); ?>><?php echo esc_html($name); ?></option>
									<?php } ?>
								</select><div class="space5"></div><!--
								--><select name="cover-group-animation-duration" class="eg-tooltip-wrap esg-w-70" title="<?php esc_attr_e('The animation duration (ms)', 'essential-grid'); ?>">
									<option value="default" <?php selected($base->getVar($skin, ['params', 'cover-group-animation-duration'], 'default'), 'default'); ?>>default</option>
									<option value="200" <?php selected($base->getVar($skin, ['params', 'cover-group-animation-duration'], 'default'), '200'); ?>>200</option>
									<option value="300" <?php selected($base->getVar($skin, ['params', 'cover-group-animation-duration'], 'default'), '300'); ?>>300</option>
									<option value="400" <?php selected($base->getVar($skin, ['params', 'cover-group-animation-duration'], 'default'), '400'); ?>>400</option>
									<option value="500" <?php selected($base->getVar($skin, ['params', 'cover-group-animation-duration'], 'default'), '500'); ?>>500</option>
									<option value="750" <?php selected($base->getVar($skin, ['params', 'cover-group-animation-duration'], 'default'), '750'); ?>>750</option>
									<option value="1000" <?php selected($base->getVar($skin, ['params', 'cover-group-animation-duration'], 'default'), '1000'); ?>>1000</option>
									<option value="1500" <?php selected($base->getVar($skin, ['params', 'cover-group-animation-duration'], 'default'), '1500'); ?>>1500</option>
								</select><div class="space5"></div><!--
								--><input class="input-settings-small element-setting eg-tooltip-wrap input-animation-delay" type="text" name="cover-group-animation-delay" title="<?php esc_attr_e('Delay before the Animation starts', 'essential-grid'); ?>" value="<?php echo esc_attr($base->getVar($skin, ['params', 'cover-group-animation-delay'], '0', 'i')); ?>" />
								<div class="div13"></div>

								<!-- MEDIA ANIMATION -->
								<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Animation of Media on Hover. All Media animation hide, or partly hide the Media on hover.', 'essential-grid'); ?>"><?php esc_html_e('Media Animation', 'essential-grid'); ?></label><!--
								--><select id="media-animation" name="media-animation">
									<?php foreach($transitions_media as $handle => $name){ ?>
										<option value="<?php echo esc_attr($handle); ?>" <?php selected($base->getVar($skin, ['params', 'media-animation'], 'fade'), $handle); ?>><?php echo esc_html($name); ?></option>
									<?php } ?>
								</select><div class="space5"></div><!--
								--><select name="media-animation-duration" class="eg-tooltip-wrap esg-w-70" title="<?php esc_attr_e('The animation duration (ms)', 'essential-grid'); ?>">
									<option value="default" <?php selected($base->getVar($skin, ['params', 'media-animation-duration'], 'default'), 'default'); ?>>default</option>
									<option value="200" <?php selected($base->getVar($skin, ['params', 'media-animation-duration'], 'default'), '200'); ?>>200</option>
									<option value="300" <?php selected($base->getVar($skin, ['params', 'media-animation-duration'], 'default'), '300'); ?>>300</option>
									<option value="400" <?php selected($base->getVar($skin, ['params', 'media-animation-duration'], 'default'), '400'); ?>>400</option>
									<option value="500" <?php selected($base->getVar($skin, ['params', 'media-animation-duration'], 'default'), '500'); ?>>500</option>
									<option value="750" <?php selected($base->getVar($skin, ['params', 'media-animation-duration'], 'default'), '750'); ?>>750</option>
									<option value="1000" <?php selected($base->getVar($skin, ['params', 'media-animation-duration'], 'default'), '1000'); ?>>1000</option>
									<option value="1500" <?php selected($base->getVar($skin, ['params', 'media-animation-duration'], 'default'), '1500'); ?>>1500</option>
								</select><div class="space5"></div><!--
								--><input class="input-settings-small element-setting eg-tooltip-wrap input-animation-delay" type="text" name="media-animation-delay" title="<?php esc_attr_e('Delay before the Animation starts', 'essential-grid'); ?>" value="<?php echo esc_attr($base->getVar($skin, ['params', 'media-animation-delay'], '0', 'i')); ?>" /><div class="space5"></div><!--
								--><div id="media-animation-blur" class="esg-display-inline-block"><!--
									--><select name="media-animation-blur" class="eg-tooltip-wrap esg-w-70" title="<?php esc_attr_e('Blur Amount', 'essential-grid'); ?>">
										<option value="2">2px</option>
										<option value="3">3px</option>
										<option value="4">4px</option>
										<option value="5" selected>5px</option>
										<option value="10">10px</option>
										<option value="15">15px</option>
										<option value="20">20px</option>
									</select>
								</div>
								<div class="div13"></div>

								<!-- 2.1.6 -->
								<!-- SHOW ALTERNATIVE IMAGE ON HOVER -->
								<?php
									$hoverImg = $base->getVar($skin, ['params', 'element-hover-image']);
									$hoverImg = !empty($hoverImg) && $hoverImg !== 'false' ? ' checked' : '';
								?>
								<div class="div13"></div>
								<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Show the item\'s Alternative Image on mouse hover', 'essential-grid'); ?>"><?php esc_html_e('Alt Image on Hover', 'essential-grid'); ?></label><!--
								--><input type="checkbox" name="element-hover-image" id="element-hover-image" class="element-setting"<?php echo esc_attr($hoverImg); ?> />

								<!-- ALTERNATIVE IMAGE ANIMATION -->
								<?php $hoverImgActive = empty($hoverImg) ? 'none' : 'block'; ?>
								<div id="eg-hover-img-animation" class="esg-display-<?php echo esc_attr($hoverImgActive); ?>">
									<div class="div13"></div>
									<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Animation of Alt Image on Hover.', 'essential-grid'); ?>"><?php esc_html_e('Alt Image Animation', 'essential-grid'); ?></label><!--
									--><select name="hover-image-animation">
											<?php foreach($transitions_hover as $handle => $name){ ?>
												<option value="<?php echo esc_attr($handle); ?>" <?php selected($base->getVar($skin, ['params', 'hover-image-animation'], 'fade'), $handle); ?>><?php echo esc_html($name); ?></option>
											<?php } ?>
									</select><div class="space5"></div><!--
									--><select name="hover-image-animation-duration" class="eg-tooltip-wrap esg-w-70" title="<?php esc_attr_e('The animation duration (ms)', 'essential-grid'); ?>">
										<option value="default" <?php selected($base->getVar($skin, ['params', 'hover-image-animation-duration'], 'default'), 'default'); ?>>default</option>
										<option value="200" <?php selected($base->getVar($skin, ['params', 'hover-image-animation-duration'], 'default'), '200'); ?>>200</option>
										<option value="300" <?php selected($base->getVar($skin, ['params', 'hover-image-animation-duration'], 'default'), '300'); ?>>300</option>
										<option value="400" <?php selected($base->getVar($skin, ['params', 'hover-image-animation-duration'], 'default'), '400'); ?>>400</option>
										<option value="500" <?php selected($base->getVar($skin, ['params', 'hover-image-animation-duration'], 'default'), '500'); ?>>500</option>
										<option value="750" <?php selected($base->getVar($skin, ['params', 'hover-image-animation-duration'], 'default'), '750'); ?>>750</option>
										<option value="1000" <?php selected($base->getVar($skin, ['params', 'hover-image-animation-duration'], 'default'), '1000'); ?>>1000</option>
										<option value="1500" <?php selected($base->getVar($skin, ['params', 'hover-image-animation-duration'], 'default'), '1500'); ?>>1500</option>
									</select><div class="space5"></div><!--
									--><input class="input-settings-small element-setting eg-tooltip-wrap input-animation-delay"  type="text" name="hover-image-animation-delay" value="<?php echo esc_attr($base->getVar($skin, ['params', 'hover-image-animation-delay'], '0', 'i')); ?>" />
									<div class="esg-hover-image-animation-none"><?php echo esc_html_e('If "None" selected as animation - alt image wont be shown!', 'essential-grid'); ?></div>
								</div>
							</div>

							<!-- GENERAL LINK/SEO SETTINGS -->
							<div id="eg-lc-content-link-seo" class="esg-lc-settings-container">
									<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Choose where the following link should be appended to.', 'essential-grid'); ?>"><?php esc_html_e('Add Link To', 'essential-grid'); ?></label><!--
									--><?php $link_set_to = $base->getVar($skin, ['params', 'link-set-to'], 'none'); ?><select name="link-set-to">
										<option value="none" <?php selected($link_set_to, 'none'); ?>><?php esc_html_e('None', 'essential-grid'); ?></option>
										<option value="media" <?php selected($link_set_to, 'media'); ?>><?php esc_html_e('Media', 'essential-grid'); ?></option>
										<option value="cover" <?php selected($link_set_to, 'cover'); ?>><?php esc_html_e('Cover', 'essential-grid'); ?></option>
									</select>
									<div class="add-link-to-wrapper" class="esg-display-none">
										<div class="div13"></div>
										<label><?php esc_html_e('Link To', 'essential-grid'); ?></label><!--
										--><?php $link_link_type = $base->getVar($skin, ['params', 'link-link-type'], 'none'); ?><select name="link-link-type">
											<option <?php selected($link_link_type, 'none'); ?> value="none"><?php esc_html_e('None', 'essential-grid'); ?></option>
											<option <?php selected($link_link_type, 'post'); ?> value="post"><?php esc_html_e('Post', 'essential-grid'); ?></option>
											<option <?php selected($link_link_type, 'url'); ?> value="url"><?php esc_html_e('URL', 'essential-grid'); ?></option>
											<option <?php selected($link_link_type, 'meta'); ?> value="meta"><?php esc_html_e('Meta', 'essential-grid'); ?></option>
											<option <?php selected($link_link_type, 'javascript'); ?> value="javascript"><?php esc_html_e('JavaScript', 'essential-grid'); ?></option>
											<option <?php selected($link_link_type, 'lightbox'); ?> value="lightbox"><?php esc_html_e('Lightbox', 'essential-grid'); ?></option>
											<option <?php selected($link_link_type, 'ajax'); ?> value="ajax"><?php esc_html_e('Ajax', 'essential-grid'); ?></option>
										</select>
										<div id="eg-link-target-wrap" class="esg-display-none">
											<div class="div13"></div>
											<label ><?php esc_html_e('Link Target', 'essential-grid'); ?></label><!--
											--><?php $link_target = $base->getVar($skin, ['params', 'link-target'], '_self'); ?><select name="link-target">
												<option <?php selected($link_target, 'disabled'); ?> value="disabled"><?php esc_html_e('disabled', 'essential-grid'); ?></option>
												<option <?php selected($link_target, '_self'); ?> value="_self"><?php esc_html_e('_self', 'essential-grid'); ?></option>
												<option <?php selected($link_target, '_blank'); ?> value="_blank"><?php esc_html_e('_blank', 'essential-grid'); ?></option>
												<option <?php selected($link_target, '_parent'); ?> value="_parent"><?php esc_html_e('_parent', 'essential-grid'); ?></option>
												<option <?php selected($link_target, '_top'); ?> value="_top"><?php esc_html_e('_top', 'essential-grid'); ?></option>
											</select>
										</div>
										<div id="eg-link-post-url-wrap" class="esg-display-none">
											<div class="div13"></div>
											<label><?php esc_html_e('Link To URL', 'essential-grid'); ?></label><input class="element-setting" type="text" name="link-url-link" value="<?php echo esc_attr($base->getVar($skin, ['params', 'link-url-link'])); ?>" />
										</div>
										<div id="eg-link-post-meta-wrap" class="esg-display-none">
											<div class="div13"></div>
											<label><?php esc_html_e('Meta Key', 'essential-grid'); ?></label><input class="element-setting" type="text" name="link-meta-link" value="<?php echo esc_attr($base->getVar($skin, ['params', 'link-meta-link'])); ?>" readonly /><div class="space18"></div><!--
											--><div class="esg-btn esg-purple" id="button-open-link-link-meta-key"><i class="eg-icon-down-open"></i><?php esc_html_e('Choose Meta Key', 'essential-grid'); ?></a></div>
											<?php include(ESG_PLUGIN_ADMIN_PATH . '/views/elements/meta-key-comment.php'); ?>
										<div id="eg-link-post-javascript-wrap" class="esg-display-none">
											<div class="div13"></div>
											<label><?php esc_html_e('Link JavaScript', 'essential-grid'); ?></label><input class="element-setting" type="text" name="link-javascript-link" value="<?php echo esc_attr($base->getVar($skin, ['params', 'link-javascript-link'])); ?>" />
										</div>
										
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>

			<!-- ELEMENT EDITOR -->
			<div class="eg-pbox esg-box" id="eg-layersettings-box-wrapper">
				<div class="esg-box-title" id="layer-settings-header"><i class="material-icons">star</i><span class="eg-element-setter eg-tor-250"><?php esc_html_e('Layer Settings', 'essential-grid'); ?></span><div class="space18"></div><!--
					--><select class="esg-layer-settings-name" id="element-settings-current-name"></select><div class="space18"></div><!--
					--><span class="eg-element-class-setter esg-layer-settings-name-text"></span>
				</div>
				<div class="esg-box-inside">
					<div id="element-setting-wrap-top" class="esg-display-none">
						<form id="eg-item-element-settings-wrap">
							<div id="settings-dz-elements-wrapper" class="eg-ul-tabs">
								<ul>
									<li class="selected-el-setting eg-source-li"><a href="#eg-element-source"><i class="eg-icon-folder-open-empty esg-margin-r-5"></i><?php esc_html_e('Source', 'essential-grid'); ?></a></li><!--
									--><li class="eg-hide-on-special eg-hide-on-blank-element"><a href="#eg-element-style"><i class="eg-icon-droplet esg-margin-r-5"></i><?php esc_html_e('Style', 'essential-grid'); ?></a></li><!--
									--><li><a href="#eg-element-hide"><i class="eg-icon-tablet esg-margin-r-5"></i><?php esc_html_e('Show/Hide', 'essential-grid'); ?></a></li><!--
									--><li><a href="#eg-element-animation"><i class="eg-icon-gamepad esg-margin-r-5"></i><?php esc_html_e('Animation', 'essential-grid'); ?></a></li><!--
									--><li class="eg-hide-on-special eg-hide-on-blank-element"><a href="#eg-element-link"><i class="eg-icon-link esg-margin-r-5"></i><?php esc_html_e('Link/SEO', 'essential-grid'); ?></a></li>
								</ul>
								<!-- SOURCE -->
								<div id="eg-element-source">
									<div id="dz-source">
										<div class="eg-hide-on-special eg-hide-on-blank-element">
											<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Select The Source of this Element', 'essential-grid'); ?>"><?php esc_html_e('Source', 'essential-grid'); ?></label><!--
											--><select name="element-source">
													<?php foreach($element_type as $el_cat => $el_type){ ?>
														<option value="<?php echo esc_attr($el_cat); ?>"><?php echo esc_html(ucwords($el_cat)); ?></option>
													<?php } ?>
													<option value="icon"><?php esc_html_e('Icon', 'essential-grid'); ?></option>
													<option value="text"><?php esc_html_e('Text/HTML', 'essential-grid'); ?></option>
												</select></div>
										<div class="div13"></div>
										<div id="eg-source-element-drops" class="eg-hide-on-special eg-hide-on-blank-element">
											<!-- DROP DOWNS FOR ELEMENTS -->
											<label><?php esc_html_e('Element', 'essential-grid'); ?></label>
											<?php foreach($element_type as $el_cat => $el_type){ ?>
												<select id="element-source-<?php echo esc_attr($el_cat); ?>" name="element-source-<?php echo esc_attr($el_cat); ?>" class="elements-select-wrap">
													<?php foreach($el_type as $ty_name => $ty_values){ ?>
														<option value="<?php echo esc_attr($ty_name); ?>"><?php echo esc_html($ty_values['name']); ?></option>
													<?php } ?>
												</select>
											<?php } ?>
											<!-- CAT & TAG SEPERATOR -->
											<div id="eg-source-seperate-wrap" class="esg-cat-tag-settings">
												<div class="div13"></div>
												<label  class="eg-tooltip-wrap" title="<?php esc_attr_e('Separator Char in the Listed element', 'essential-grid'); ?>"><?php esc_html_e('Separate By', 'essential-grid'); ?></label><!--
												--><input type="text" value="" name="element-source-separate" class="input-settings-small element-setting ">
											</div>

											<!-- CAT & TAG MAX -->
											<div id="eg-source-catmax-wrap" class="esg-cat-tag-settings" >
												<div class="div13"></div>
												<label  class="eg-tooltip-wrap" title="<?php esc_attr_e('Max Categories/Tags to show (use -1 for unlimited)', 'essential-grid'); ?>"><?php esc_html_e('Max Items', 'essential-grid'); ?></label><!--
												--><input type="text" value="" name="element-source-catmax" class="input-settings-small element-setting ">
											</div>

											<!-- CAT & TAG CHOOSE TYPE -->
											<div id="eg-source-functonality-wrap" >
												<div class="div13"></div>
												<label  class="eg-tooltip-wrap" title="<?php esc_attr_e('Narrow down your selection', 'essential-grid'); ?>"><?php esc_html_e('On Click', 'essential-grid'); ?></label><!--
												--><select name="element-source-function" class="elements-select-wrap esg-w-180">
														<option value="none"><?php esc_html_e('None', 'essential-grid'); ?></option>
														<option value="link"><?php esc_html_e('Link', 'essential-grid'); ?></option>
														<option value="filter"><?php esc_html_e('Trigger Filter', 'essential-grid'); ?></option>
												</select>
											</div>

											<!-- CHOOSE TAX -->
											<div id="eg-source-taxonomy-wrap" class="eg-layer-toolbar-box" >
												<div class="div13"></div>
												<label  class="eg-tooltip-wrap" title="<?php esc_attr_e('Choose from all Taxonomies available', 'essential-grid'); ?>"><?php esc_html_e('Taxonomy', 'essential-grid'); ?></label><!--
												--><select name="element-source-taxonomy">
													<?php
														$args = [
															'public' => true
														];
														$taxonomies = get_taxonomies($args,'objects');
														foreach ($taxonomies as $taxonomy_name => $taxonomy) {
															echo '<option value="'.esc_attr($taxonomy_name).'">'.esc_html($taxonomy->labels->name).' ('.esc_html($taxonomy_name).')</option>';
														}
													?>
												</select>
											</div>

											<!-- META TAG -->
											<div id="eg-source-meta-wrap" >
												<div class="div13"></div>
												<label class="eg-tooltip-wrap" title="<?php esc_attr_e('The Handle or ID of Meta Key', 'essential-grid'); ?>" ><?php esc_html_e('Meta Key', 'essential-grid'); ?></label><!--
												--><input type="text" value="" name="element-source-meta" class="input-settings element-setting " readonly><div class="space18"></div><!--
												--><div id="button-open-meta-key" class="esg-btn esg-purple"><i class="eg-icon-down-open"></i><?php esc_html_e('Choose Meta Key', 'essential-grid'); ?></div>
												<?php include(ESG_PLUGIN_ADMIN_PATH . '/views/elements/meta-key-comment.php'); ?>
											</div>

											<!-- WORD LIMITATION -->
											<div id="eg-source-limit-wrap" >
												<div class="div13"></div>
												<label ><?php esc_html_e('Limit By', 'essential-grid'); ?></label><!--
												--><select name="element-limit-type">
													<option value="none"><?php esc_html_e('None', 'essential-grid'); ?></option>
													<option value="words"><?php esc_html_e('Words', 'essential-grid'); ?></option>
													<option value="chars"><?php esc_html_e('Characters', 'essential-grid'); ?></option>
													<option value="sentence"><?php esc_html_e('End Sentence Words', 'essential-grid'); ?></option>
												</select><!--
												--><input type="text" value="" name="element-limit-num" class="input-settings-small element-setting ">
												<div class="div13"></div>
												<label ><?php esc_html_e('Min Height', 'essential-grid'); ?></label><!--
												--><input type="text" value="0" name="element-min-height" class="input-settings-small element-setting  eg-tooltip-wrap" title="<?php esc_attr_e('Optional CSS min-height (px)', 'essential-grid'); ?>">
												<div class="div13"></div>
												<label ><?php esc_html_e('Max Height', 'essential-grid'); ?></label><!--
												--><input type="text" value="none" name="element-max-height" class="input-settings-small element-setting  eg-tooltip-wrap" title="<?php esc_attr_e("Optional CSS max-height (px). Enter 'none' for no max-height", 'essential-grid'); ?>">
											</div>
										</div>

										<!-- ICON SELECTOR -->
										<div id="eg-source-icon-wrap" class="elements-select-wrap">
											<label><?php esc_html_e('Pick an Icon', 'essential-grid'); ?></label><!--
											--><div id="show-fontello-dialog"><div id="eg-preview-icon"></div></div>
											<input type="hidden" value="" name="element-source-icon" />
										</div>

										<!-- HTML TEXT SOURCE -->
										<div id="eg-source-text-style-disable-wrap" class="elements-select-wrap eg-hide-on-special eg-hide-on-blank-element">
											<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Stylings will not be written', 'essential-grid'); ?>" ><?php esc_html_e('Disable Styling', 'essential-grid'); ?></label><!--
											--><input type="checkbox"  name="element-source-text-style-disable" value="on" class="input-settings element-setting ">
										</div>

										<div id="eg-source-text-wrap" class="elements-select-wrap">
											<div class="div13"></div>
											<label></label><!--
											--><textarea name="element-source-text" class="esg-element-source-text"></textarea>
											<div class="div13"></div>
											<label></label><div class="esg-btn esg-purple" id="eg-show-meta-keys-dialog"><?php esc_html_e('Meta Key List', 'essential-grid'); ?> </div>
										</div>
									</div>
								</div>

								<!-- STYLING -->
								<div id="eg-element-style">
									<div id="eg-styling-idle-hover-tab" class="eg-ul-tabs">
										<ul class="eg-submenu">
											<li class="selected-submenu-setting eg-tooltip-wrap" title="<?php esc_attr_e('Style of Element in Idle State', 'essential-grid'); ?>" data-toshow="eg-style-idle"><i class="eg-icon-star-empty"></i><?php esc_html_e('Idle', 'essential-grid'); ?></li><!--
											--><li class="eg-tooltip-wrap" title="<?php esc_attr_e('Style of Element in Hover state (only if Hover Box Checked)', 'essential-grid'); ?>" data-toshow="eg-style-hover"><i class="eg-icon-star"></i><?php esc_html_e('Hover', 'essential-grid'); ?><input class="esg-margin-l-10" type="checkbox" name="element-enable-hover" /></li>
										</ul>
										
										<!-- IDLE STYLING -->
										<div id="eg-style-idle">
											<div class="eg-small-vertical-menu">
												<ul>
													<li class="selected-el-setting" data-toshow="eg-el-font"><i class="eg-icon-font" ></i><?php esc_html_e('Style', 'essential-grid'); ?></li>
													<li  data-toshow="eg-el-pos"><i class="eg-icon-align-left"></i><?php esc_html_e('Spacing', 'essential-grid'); ?></li>
													<li  data-toshow="eg-el-border"><i class="eg-icon-minus-squared-alt"></i><?php esc_html_e('Border', 'essential-grid'); ?></li>
													<li  data-toshow="eg-el-bg"><i class="eg-icon-picture-1"></i><?php esc_html_e('BG', 'essential-grid'); ?></li>
													<li  data-toshow="eg-el-shadow"><i class="eg-icon-picture"></i><?php esc_html_e('Shadow', 'essential-grid'); ?></li>
												</ul>
											</div>
											
											<!-- FONT -->
											<div id="eg-el-font" class="esg-el-settings-container active-esc">
												<label><?php esc_html_e('Font Size', 'essential-grid'); ?></label><span id="element-font-size" class="slider-settings"></span><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-font-size" value="6" /> px
												<div class="div13"></div>
												<label><?php esc_html_e('Line Height', 'essential-grid'); ?></label><span id="element-line-height" class="slider-settings"></span><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-line-height" value="8" /> px
												<div class="div13"></div>
												<label><?php esc_html_e('Font Color', 'essential-grid'); ?></label><!--
												--><input class="element-setting" name="element-color" type="text" id="element-color" value="" data-mode="single">
												<div class="div13"></div>
												<label><?php esc_html_e('Font Family', 'essential-grid'); ?></label><!--
												--><input class="element-setting" name="element-font-family" type="text" value=""><div class="space18"></div><div id="button-open-font-family" class="esg-btn esg-purple"><i class="material-icons">font_download</i><?php esc_html_e('Font Families', 'essential-grid'); ?></div>
												<div class="div13"></div>
												<label ><?php esc_html_e('Font Weight', 'essential-grid'); ?></label><!--
												--><select name="element-font-weight">
													<option value="400"><?php esc_html_e('400', 'essential-grid'); ?></option>
													<option value="100"><?php esc_html_e('100', 'essential-grid'); ?></option>
													<option value="200"><?php esc_html_e('200', 'essential-grid'); ?></option>
													<option value="300"><?php esc_html_e('300', 'essential-grid'); ?></option>
													<option value="500"><?php esc_html_e('500', 'essential-grid'); ?></option>
													<option value="600"><?php esc_html_e('600', 'essential-grid'); ?></option>
													<option value="700"><?php esc_html_e('700', 'essential-grid'); ?></option>
													<option value="800"><?php esc_html_e('800', 'essential-grid'); ?></option>
													<option value="900"><?php esc_html_e('900', 'essential-grid'); ?></option>
												</select>
												<div class="div13"></div>
												<label ><?php esc_html_e('Text Decoration', 'essential-grid'); ?></label><!--
												--><select name="element-text-decoration">
													<option value="none"><?php esc_html_e('None', 'essential-grid'); ?></option>
													<option value="underline"><?php esc_html_e('Underline', 'essential-grid'); ?></option>
													<option value="overline"><?php esc_html_e('Overline', 'essential-grid'); ?></option>
													<option value="line-through"><?php esc_html_e('Line Through', 'essential-grid'); ?></option>
												</select>
												<div class="div13"></div>
												<label><?php esc_html_e('Font Style', 'essential-grid'); ?></label><!--
												--><input type="checkbox" name="element-font-style" value="true" /> <?php esc_html_e('Italic', 'essential-grid'); ?>
												<div class="div13"></div>
												<label ><?php esc_html_e('Text Transform', 'essential-grid'); ?></label><!--
												--><select name="element-text-transform">
													<option value="none"><?php esc_html_e('None', 'essential-grid'); ?></option>
													<option value="capitalize"><?php esc_html_e('Capitalize', 'essential-grid'); ?></option>
													<option value="uppercase"><?php esc_html_e('Uppercase', 'essential-grid'); ?></option>
													<option value="lowercase"><?php esc_html_e('Lowercase', 'essential-grid'); ?></option>
												</select>
												<div class="div13"></div>
												<label><?php esc_html_e('Letter Spacing', 'essential-grid'); ?></label><!--
												--><input type="text" class="letter-spacing " name="element-letter-spacing" value="normal">
												<div class="drop-to-stylechange eg-tooltip-wrap" title="<?php esc_attr_e('Drop Element from Layer Templates here to overwrite Styling of Current Element', 'essential-grid'); ?>">
													<?php
													printf(
														/* translators: 1:br tag */
														esc_html__('Drop for%1$sStyle%1$sChange ', 'essential-grid'),
														'<br>'
													);
													?>
												</div>
											</div>

											<!-- POSITION -->
											<div id="eg-el-pos" class="esg-el-settings-container">
												<label ><?php esc_html_e('Position', 'essential-grid'); ?></label><!--
												--><select name="element-position">
													<option value="relative"><?php esc_html_e('Relative', 'essential-grid'); ?></option>
													<option value="absolute"><?php esc_html_e('Absolute', 'essential-grid'); ?></option>
												</select>
												<div id="eg-show-on-absolute">
													<div class="div13"></div>
													<label ><?php esc_html_e('Align', 'essential-grid'); ?></label><!--
													--><select name="element-align">
														<option value="t_l"><?php esc_html_e('Top/Left', 'essential-grid'); ?></option>
														<option value="t_r"><?php esc_html_e('Top/Right', 'essential-grid'); ?></option>
														<option value="b_l"><?php esc_html_e('Bottom/Left', 'essential-grid'); ?></option>
														<option value="b_r"><?php esc_html_e('Bottom/Right', 'essential-grid'); ?></option>
													</select><div class="space18"></div><!--
													--><select class="esg-w-50" name="element-absolute-unit">
														<option value="px">px</option>
														<option value="%">%</option>
													</select>
													<div class="div13"></div>
													<label id="eg-t_b_align"><?php esc_html_e('Top', 'essential-grid'); ?></label><input class="input-settings-small element-setting" type="text" name="element-top-bottom" value="0" />
													<div class="div13"></div>
													<label id="eg-l_r_align"><?php esc_html_e('Left', 'essential-grid'); ?></label><input class="input-settings-small element-setting" type="text" name="element-left-right" value="0" />
												</div>
												<div id="eg-show-on-relative">
													<div class="div13"></div>
													<label ><?php esc_html_e('Display', 'essential-grid'); ?></label><!--
													--><select name="element-display">
														<option value="block"><?php esc_html_e('block', 'essential-grid'); ?></option>
														<option value="inline-block"><?php esc_html_e('inline-block', 'essential-grid'); ?></option>
													</select>
													<div id="element-text-align-wrap">
														<div class="div13"></div>
														<label ><?php esc_html_e('Text Align', 'essential-grid'); ?></label><!--
														--><select name="element-text-align">
															<option value="center"><?php esc_html_e('center', 'essential-grid'); ?></option>
															<option value="left"><?php esc_html_e('left', 'essential-grid'); ?></option>
															<option value="right"><?php esc_html_e('right', 'essential-grid'); ?></option>
														</select>
													</div>
													<div id="element-float-wrap">
														<div class="div13"></div>
														<label ><?php esc_html_e('Float Element', 'essential-grid'); ?></label><!--
														--><select name="element-float">
															<option value="none"><?php esc_html_e('none', 'essential-grid'); ?></option>
															<option value="left"><?php esc_html_e('left', 'essential-grid'); ?></option>
															<option value="right"><?php esc_html_e('right', 'essential-grid'); ?></option>
														</select>
													</div>
													<div class="div13"></div>
													<label ><?php esc_html_e('Clear', 'essential-grid'); ?></label><!--
													--><select name="element-clear">
														<option value="none"><?php esc_html_e('none', 'essential-grid'); ?></option>
														<option value="left"><?php esc_html_e('left', 'essential-grid'); ?></option>
														<option value="right"><?php esc_html_e('right', 'essential-grid'); ?></option>
														<option value="both"><?php esc_html_e('both', 'essential-grid'); ?></option>
													</select>
												</div>
												<div class="div13"></div>
												<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Top', 'essential-grid'); ?>, <?php esc_html_e('Right', 'essential-grid'); ?>, <?php esc_html_e('Bottom', 'essential-grid'); ?>, <?php esc_html_e('Left', 'essential-grid'); ?>"><?php esc_html_e('Margin', 'essential-grid'); ?></label><!--
												--><input class="input-settings-small element-setting " type="text" name="element-margin[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-margin[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-margin[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-margin[]" value="0" />
												<div class="div13"></div>
												<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Top', 'essential-grid'); ?>, <?php esc_html_e('Right', 'essential-grid'); ?>, <?php esc_html_e('Bottom', 'essential-grid'); ?>, <?php esc_html_e('Left', 'essential-grid'); ?>"><?php esc_html_e('Paddings', 'essential-grid'); ?></label><!--
												--><input class="input-settings-small element-setting " type="text" name="element-padding[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-padding[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-padding[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-padding[]" value="0" />
												<div class="drop-to-stylechange eg-tooltip-wrap" title="<?php esc_attr_e('Drop Element from Layer Templates here to overwrite Styling of Current Element', 'essential-grid'); ?>">
													<?php
													printf(
													/* translators: 1:br tag */
														esc_html__('Drop for%1$sStyle%1$sChange ', 'essential-grid'),
														'<br>'
													);
													?>
												</div>
											</div>
											
											<!-- BG -->
											<div id="eg-el-bg" class="esg-el-settings-container">
												<label><?php esc_html_e('Background Color', 'essential-grid'); ?></label><!--
												--><input class="element-setting" name="element-background-color" type="text" id="element-background-color" value="">
												<div class="drop-to-stylechange eg-tooltip-wrap" title="<?php esc_attr_e('Drop Element from Layer Templates here to overwrite Styling of Current Element', 'essential-grid'); ?>">
													<?php
													printf(
														/* translators: 1:br tag */
														esc_html__('Drop for%1$sStyle%1$sChange ', 'essential-grid'),
														'<br>'
													);
													?>
												</div>
											</div>

											<!-- SHADOW -->
											<div id="eg-el-shadow" class="esg-el-settings-container">
												<label><?php esc_html_e('Shadow Color', 'essential-grid'); ?></label><input class="element-setting" name="element-shadow-color" type="text" id="element-shadow-color" value="" data-mode="single">
												<div class="div13"></div>
												<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Position of horizontal shadow(Negative values possible)', 'essential-grid') ?>, <?php esc_html_e('blur distance', 'essential-grid') ?>, <?php esc_html_e('size of shadow', 'essential-grid') ?>"><?php esc_html_e('Shadow', 'essential-grid'); ?></label><!--
												--><input class="input-settings-small element-setting " type="text" name="element-box-shadow[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-box-shadow[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-box-shadow[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-box-shadow[]" value="0" />
												<div class="drop-to-stylechange eg-tooltip-wrap" title="<?php esc_attr_e('Drop Element from Layer Templates here to overwrite Styling of Current Element', 'essential-grid'); ?>">
													<?php
													printf(
														/* translators: 1:br tag */
														esc_html__('Drop for%1$sStyle%1$sChange ', 'essential-grid'),
														'<br>'
													);
													?>
												</div>
											</div>

											<!-- BORDER -->
											<div id="eg-el-border" class="esg-el-settings-container">
												<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Top Border Width', 'essential-grid') ?>, <?php esc_html_e('Right Border Width', 'essential-grid') ?>, <?php esc_html_e('Bottom Border Width', 'essential-grid') ?>, <?php esc_html_e('Left Border Width', 'essential-grid') ?>"><?php esc_html_e('Border', 'essential-grid'); ?></label><!--
												--><input class="input-settings-small element-setting " type="text" name="element-border[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-border[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-border[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-border[]" value="0" />
												<div class="div13"></div>
												<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Top Left Radius', 'essential-grid') ?>, <?php esc_html_e('Top Right Radius', 'essential-grid') ?>, <?php esc_html_e('Bottom Right Radius', 'essential-grid') ?>, <?php esc_html_e('Bottom Left Radius', 'essential-grid') ?>"><?php esc_html_e('Border Radius', 'essential-grid'); ?></label><!--
												--><input class="input-settings-small element-setting " type="text" name="element-border-radius[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-border-radius[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-border-radius[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-border-radius[]" value="0" /><div class="space18"></div><!--
												--><select class="esg-w-50" name="element-border-radius-unit">
													<option value="px">px</option>
													<option value="%">%</option>
												</select>
												<div class="div13"></div>
												<label><?php esc_html_e('Border Color', 'essential-grid'); ?></label><!--
												--><input class="element-setting" name="element-border-color" type="text" id="element-border-color" value="" data-mode="single">
												<div class="div13"></div>

												<label ><?php esc_html_e('Border Style', 'essential-grid'); ?></label><!--
												--><select name="element-border-style">
													<option value="none"><?php esc_html_e('none', 'essential-grid'); ?></option>
													<option value="solid"><?php esc_html_e('solid', 'essential-grid'); ?></option>
													<option value="dotted"><?php esc_html_e('dotted', 'essential-grid'); ?></option>
													<option value="dashed"><?php esc_html_e('dashed', 'essential-grid'); ?></option>
													<option value="double"><?php esc_html_e('double', 'essential-grid'); ?></option>
												</select>

												<div class="drop-to-stylechange eg-tooltip-wrap" title="<?php esc_attr_e('Drop Element from Layer Templates here to overwrite Styling of Current Element', 'essential-grid'); ?>">
													<?php
													printf(
														/* translators: 1:br tag */
														esc_html__('Drop for%1$sStyle%1$sChange ', 'essential-grid'),
														'<br>'
													);
													?>
												</div>
											</div>
										</div>

										<!-- HOVER STYLING -->
										<div id="eg-style-hover">
											<div class="eg-small-vertical-menu">
												<ul>
													<li class="selected-el-setting" data-toshow="eg-el-font-hover"><i class="eg-icon-font" ></i><?php esc_html_e('Style', 'essential-grid'); ?></li><!--
													--><li data-toshow="eg-el-border-hover"><i class="eg-icon-minus-squared-alt"></i><?php esc_html_e('Border', 'essential-grid'); ?></li><!--
													--><li data-toshow="eg-el-bg-hover"><i class="eg-icon-picture-1"></i><?php esc_html_e('BG', 'essential-grid'); ?></li><!--
													--><li data-toshow="eg-el-shadow-hover"><i class="eg-icon-picture"></i><?php esc_html_e('Shadow', 'essential-grid'); ?></li>
												</ul>
											</div>
											
											<!-- FONT ON HOVER -->
											<div id="eg-el-font-hover" class="esg-el-settings-container active-esc">
												<label><?php esc_html_e('Font Size', 'essential-grid'); ?></label><span id="element-font-size-hover" class="slider-settings"></span><div class="space18"></div><input class="input-settings-small element-setting" type="text" name="element-font-size-hover" value="6" /> px
												<div class="div13"></div>

												<label><?php esc_html_e('Line Height', 'essential-grid'); ?></label><span id="element-line-height-hover" class="slider-settings"></span><div class="space18"></div><input class="input-settings-small element-setting" type="text" name="element-line-height-hover" value="8" /> px
												<div class="div13"></div>

												<label><?php esc_html_e('Font Color', 'essential-grid'); ?></label><input class="element-setting" name="element-color-hover" type="text" id="element-color-hover" value="" data-mode="single">
												<div class="div13"></div>

												<label><?php esc_html_e('Font Family', 'essential-grid'); ?></label><input class="element-setting" name="element-font-family-hover" type="text" value=""><div class="space18"></div><div id="button-open-font-family-hover" class="esg-btn esg-purple"><i class="material-icons">font_download</i><?php esc_html_e('Font Families', 'essential-grid'); ?></div>
												<div class="div13"></div>
												<label ><?php esc_html_e('Font Weight', 'essential-grid'); ?></label><select name="element-font-weight-hover">
													<option value="400"><?php esc_html_e('400', 'essential-grid'); ?></option>
													<option value="100"><?php esc_html_e('100', 'essential-grid'); ?></option>
													<option value="200"><?php esc_html_e('200', 'essential-grid'); ?></option>
													<option value="300"><?php esc_html_e('300', 'essential-grid'); ?></option>
													<option value="500"><?php esc_html_e('500', 'essential-grid'); ?></option>
													<option value="600"><?php esc_html_e('600', 'essential-grid'); ?></option>
													<option value="700"><?php esc_html_e('700', 'essential-grid'); ?></option>
													<option value="800"><?php esc_html_e('800', 'essential-grid'); ?></option>
													<option value="900"><?php esc_html_e('900', 'essential-grid'); ?></option>
												</select>
												<div class="div13"></div>
												<label ><?php esc_html_e('Text Decoration', 'essential-grid'); ?></label><select name="element-text-decoration-hover">
													<option value="none"><?php esc_html_e('None', 'essential-grid'); ?></option>
													<option value="underline"><?php esc_html_e('Underline', 'essential-grid'); ?></option>
													<option value="overline"><?php esc_html_e('Overline', 'essential-grid'); ?></option>
													<option value="line-through"><?php esc_html_e('Line Through', 'essential-grid'); ?></option>
												</select>
												<div class="div13"></div>
												<label><?php esc_html_e('Font Style', 'essential-grid'); ?></label><input type="checkbox" name="element-font-style-hover" value="true" /> <?php esc_html_e('Italic', 'essential-grid'); ?>

												<div class="div13"></div>
												<label ><?php esc_html_e('Text Transform', 'essential-grid'); ?></label><select name="element-text-transform-hover">
													<option value="none"><?php esc_html_e('None', 'essential-grid'); ?></option>
													<option value="capitalize"><?php esc_html_e('Capitalize', 'essential-grid'); ?></option>
													<option value="uppercase"><?php esc_html_e('Uppercase', 'essential-grid'); ?></option>
													<option value="lowercase"><?php esc_html_e('Lowercase', 'essential-grid'); ?></option>
												</select>
												<div class="div13"></div>
												<label><?php esc_html_e('Letter Spacing', 'essential-grid'); ?></label><input type="text" class="letter-spacing" name="element-letter-spacing-hover" value="normal">
												<div class="esg-purple drop-to-stylereset esg-btn"><i class="eg-icon-ccw-1"></i><?php esc_html_e("Reset from Idle", 'essential-grid'); ?></div>
											</div>

											<!-- BG ON HOVER -->
											<div id="eg-el-bg-hover" class="esg-el-settings-container">
												<label><?php esc_html_e('Background Color', 'essential-grid'); ?></label><input class="element-setting" name="element-background-color-hover" type="text" id="element-background-color-hover" value="">
												<div class="esg-purple drop-to-stylereset esg-btn"><i class="eg-icon-ccw-1"></i><?php esc_html_e("Reset from Idle", 'essential-grid'); ?></div>
											</div>

											<!-- SHADOW ON HOVER -->
											<div id="eg-el-shadow-hover" class="esg-el-settings-container">
												<label><?php esc_html_e('Shadow Color', 'essential-grid'); ?></label><input class="element-setting" name="element-shadow-color-hover" type="text" id="element-shadow-color-hover" value="" data-mode="single">
												<div class="div13"></div>
												<label class=" eg-tooltip-wrap" title="<?php esc_attr_e('Position horizontal shadow(Negative values possible)', 'essential-grid') ?>, <?php esc_html_e('blur distance', 'essential-grid') ?>, <?php esc_html_e('Shadow size', 'essential-grid') ?>"><?php esc_html_e('Shadow', 'essential-grid'); ?></label><!--
												--><input class="input-settings-small element-setting " type="text" name="element-box-shadow-hover[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-box-shadow-hover[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-box-shadow-hover[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-box-shadow-hover[]" value="0" />
												<div class="esg-purple drop-to-stylereset esg-btn"><i class="eg-icon-ccw-1"></i><?php esc_html_e("Reset from Idle", 'essential-grid'); ?></div>
											</div>

											<!-- BORDER ON HOVER -->
											<div id="eg-el-border-hover" class="esg-el-settings-container">
												<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Top', 'essential-grid') ?>, <?php esc_html_e('Right', 'essential-grid') ?>, <?php esc_html_e('Bottom', 'essential-grid') ?>, <?php esc_html_e('Left Border Width', 'essential-grid') ?>"><?php esc_html_e('Border', 'essential-grid'); ?></label><!--
												--><input class="input-settings-small element-setting " type="text" name="element-border-hover[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-border-hover[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-border-hover[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-border-hover[]" value="0" />
												<div class="div13"></div>
												<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Top Left Radius', 'essential-grid') ?>, <?php esc_html_e('Top Right Radius', 'essential-grid') ?>, <?php esc_html_e('Bottom Right Radius', 'essential-grid') ?>, <?php esc_html_e('Bottom Left Radius', 'essential-grid') ?>"><?php esc_html_e('Border Radius', 'essential-grid'); ?></label><!--
												--><input class="input-settings-small element-setting " type="text" name="element-border-radius-hover[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-border-radius-hover[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-border-radius-hover[]" value="0" /><div class="space18"></div><!--
												--><input class="input-settings-small element-setting" type="text" name="element-border-radius-hover[]" value="0" /><div class="space18"></div><!--
												--><select class="esg-w-50" name="element-border-radius-unit-hover">
													<option value="px">px</option>
													<option value="%">%</option>
												</select>
												<div class="div13"></div>
												<label><?php esc_html_e('Border Color', 'essential-grid'); ?></label><input class="element-setting" name="element-border-color-hover" type="text" id="element-border-color-hover" value="" data-mode="single">
												<div class="div13"></div>
												<label ><?php esc_html_e('Border Style', 'essential-grid'); ?></label><!--
												--><select name="element-border-style-hover">
													<option value="none"><?php esc_html_e('none', 'essential-grid'); ?></option>
													<option value="solid"><?php esc_html_e('solid', 'essential-grid'); ?></option>
													<option value="dotted"><?php esc_html_e('dotted', 'essential-grid'); ?></option>
													<option value="dashed"><?php esc_html_e('dashed', 'essential-grid'); ?></option>
													<option value="double"><?php esc_html_e('double', 'essential-grid'); ?></option>
												</select>
												<div class="esg-purple drop-to-stylereset esg-btn"><i class="eg-icon-ccw-1"></i><?php esc_html_e("Reset from Idle", 'essential-grid'); ?></div>
											</div>
										</div>
									</div>
								</div>

								<!-- HIDE UNDER -->
								<div id="eg-element-hide">
									<div id="always-visible-options">
										<label class="eg-tooltip-wrap esg-w-250" title="<?php esc_attr_e('Show the Element by default without a Mouse Hover', 'essential-grid'); ?>"><?php esc_html_e('Show without Hover on Desktop', 'essential-grid'); ?></label><input type="checkbox" name="element-always-visible-desktop" value="true" />
										<div class="div13"></div>
										<label class="eg-tooltip-wrap esg-w-250" title="<?php esc_attr_e('Show the Element by default without a Screen-Touch/Tap', 'essential-grid'); ?>"><?php esc_html_e('Show without Tap on Mobile', 'essential-grid'); ?></label><input type="checkbox" name="element-always-visible-mobile" value="true" />
										<div class="div13"></div>
									</div>
									<label class="eg-tooltip-wrap esg-w-250" title="<?php esc_attr_e('Dont Show Element if Item Width is smaller than:', 'essential-grid'); ?>"><?php esc_html_e('Hide Under Item Width', 'essential-grid'); ?></label><input class="input-settings-small element-setting " type="text" name="element-hideunder" value="0" /> px
									<div class="esg-display-none esg-color-blue esg-warning-element-hideunder"><?php esc_html_e('This option will override "Show without..." settings', 'essential-grid'); ?></div>
									<div class="div13"></div>
									<label class="eg-tooltip-wrap esg-w-250" title="<?php esc_attr_e('Dont Show Element on mobile if Item height is smaller than:', 'essential-grid'); ?>"><?php esc_html_e('Hide Under Item Height', 'essential-grid'); ?></label><input class="input-settings-small element-setting " type="text" name="element-hideunderheight" value="0" /> px
									<div class="esg-display-none esg-color-blue esg-warning-element-hideunderheight"><?php esc_html_e('This option will override "Show without..." settings', 'essential-grid'); ?></div>
									<div class="div13"></div>
									<label class="esg-w-250"><?php esc_html_e('Hide Under Type', 'essential-grid'); ?></label><!--
									--><select name="element-hidetype">
										<option value="visibility"><?php esc_html_e('visibility', 'essential-grid'); ?></option>
										<option value="display"><?php esc_html_e('display', 'essential-grid'); ?></option>
									</select>
									<div class="div13"></div>
									<label class="esg-w-250" title="<?php esc_attr_e('Show/Hide Element if the Media this Entry gains is a Video', 'essential-grid'); ?>"><?php esc_html_e('If Media is Video', 'essential-grid'); ?></label><!--
									--><select name="element-hide-on-video">
										<option value="false"><?php esc_html_e('-- Do Nothing --', 'essential-grid'); ?></option>
										<option value="true"><?php esc_html_e('Hide', 'essential-grid'); ?></option>
										<option value="show"><?php esc_html_e('Show', 'essential-grid'); ?></option>
									</select>
									<div class="div13"></div>
									<label class="esg-w-250" class="eg-tooltip-wrap" title="<?php esc_attr_e('Show/Hide Element only if the LightBox is a Video', 'essential-grid'); ?>"><?php esc_html_e('If LightBox is Video', 'essential-grid'); ?></label><!--
									--><select name="element-show-on-lightbox-video">
										<option value="false"><?php esc_html_e('-- Do Nothing --', 'essential-grid'); ?></option>
										<option value="true"><?php esc_html_e('Show', 'essential-grid'); ?></option>
										<option value="hide"><?php esc_html_e('Hide', 'essential-grid'); ?></option>
									</select>

									<?php
									if(!Essential_Grid_Woocommerce::is_woo_exists()){
										echo '<div class="esg-display-none">';
									}
									?>
										<div class="div13"></div>
										<label class="eg-tooltip-wrap esg-w-250" title="<?php esc_attr_e('Show the Element only if it is on Sale. This is a WooCommerce setting', 'essential-grid'); ?>"><?php esc_html_e('Show if Product is on Sale', 'essential-grid'); ?></label><input type="checkbox" name="element-show-on-sale" value="true" />
										<div class="div13"></div>
										<label class="eg-tooltip-wrap esg-w-250" title="<?php esc_attr_e('Show the Element only if it is featured. This is a WooCommerce setting', 'essential-grid'); ?>"><?php esc_html_e('Show if Product is featured', 'essential-grid'); ?></label><input type="checkbox" name="element-show-if-featured" value="true" />

									<?php
									if(!Essential_Grid_Woocommerce::is_woo_exists()){
										echo '</div>';
									}
									?>
									<div class="div13"></div>
									<div id="esg-advanced-rules-edit" class="esg-btn esg-purple"><?php esc_html_e('Advanced Rules', 'essential-grid'); ?></div>
								</div>

								<!-- ANIMATION -->
								<div id="eg-element-animation">
										<label><?php esc_html_e('Transition', 'essential-grid'); ?></label><!--
										--><select name="element-transition" class="eg-tooltip-wrap" title="<?php esc_attr_e('Select Animation of Element on Hover', 'essential-grid'); ?>" >
											<?php foreach($transitions_elements as $handle => $name){
												if(preg_match('/collapse|line|circle|spiral/', $handle)) continue;
											?>
											<option value="<?php echo esc_attr($handle); ?>"><?php echo esc_html($name); ?></option>
											<?php } ?>
										</select><div class="space18"></div><!--
										--><select name="element-transition-type" class="eg-tooltip-wrap" title="<?php esc_attr_e('Hide or Show element on hover. In = Show, Out = Hide', 'essential-grid'); ?>" >
											<option value=""><?php esc_html_e('in', 'essential-grid'); ?></option>
											<option value="out"><?php esc_html_e('out', 'essential-grid'); ?></option>
										</select><div class="space18"></div><!--
										-->
									<div class="div13"></div>

									<div class="eg-hideable-no-transition">
										<label><?php esc_html_e('Duration', 'essential-grid'); ?></label><!--
										--><select name="element-duration" class="eg-tooltip-wrap" title="<?php esc_attr_e('The animation duration (ms)', 'essential-grid'); ?>">
											<option value="default">Default</option>
											<option value="200">200</option>
											<option value="300">300</option>
											<option value="400">400</option>
											<option value="500">500</option>
											<option value="750">750</option>
											<option value="1000">1000</option>
											<option value="1500">1500</option>
										</select>
										<div class="div13"></div>

										<label><?php esc_html_e('Delay', 'essential-grid'); ?></label><!--
										--><span id="element-delay" class="slider-settings eg-tooltip-wrap" title="<?php esc_attr_e('Delay before Element Animation starts', 'essential-grid') ?>" ></span><div class="space18"></div><!--
										--><input class="input-settings-small element-setting" type="text" name="element-delay" value="0" />
									</div>

								</div>
								
								<!-- LINK TO -->
								<div id="eg-element-link">
									<label ><?php esc_html_e('Link To', 'essential-grid'); ?></label><!--
									--><select name="element-link-type">
										<option value="none"><?php esc_html_e('None', 'essential-grid'); ?></option>
										<option value="post"><?php esc_html_e('Post', 'essential-grid'); ?></option>
										<option value="url"><?php esc_html_e('URL', 'essential-grid'); ?></option>
										<option value="meta"><?php esc_html_e('Meta', 'essential-grid'); ?></option>
										<option value="ajax"><?php esc_html_e('Ajax', 'essential-grid'); ?></option>
										<option value="javascript"><?php esc_html_e('JavaScript', 'essential-grid'); ?></option>
										<option value="lightbox"><?php esc_html_e('Lightbox', 'essential-grid'); ?></option>
										<option value="embedded_video"><?php esc_html_e('Play Embedded Video', 'essential-grid'); ?></option>
										<option value="sharefacebook"><?php esc_html_e('Share on Facebook', 'essential-grid'); ?></option>
										<option value="sharetwitter"><?php esc_html_e('Share on Twitter', 'essential-grid'); ?></option>
										<option value="sharepinterest"><?php esc_html_e('Share on Pinterest', 'essential-grid'); ?></option>
										<option value="likepost"><?php esc_html_e('Like Post', 'essential-grid'); ?></option>
									</select>
									<div class="div13"></div>

									<div id="eg-element-post-url-wrap" class="esg-display-none">
										<label><?php esc_html_e('Link To URL', 'essential-grid'); ?></label><input class="element-setting" type="text" name="element-url-link" value="" />
										<div class="div13"></div>
									</div>
									<div id="eg-element-post-meta-wrap" class="esg-display-none">
										<label><?php esc_html_e('Meta Key', 'essential-grid'); ?></label><input class="element-setting" type="text" name="element-meta-link" value="" readonly /><div class="space18"></div><!--
										--><div class="esg-btn esg-purple" id="button-open-link-meta-key"><i class="eg-icon-down-open"></i>Choose Meta Key</div>
										<?php include(ESG_PLUGIN_ADMIN_PATH . '/views/elements/meta-key-comment.php'); ?>
										<div class="div13"></div>
									</div>
									<div id="eg-element-post-javascript-wrap" class="esg-display-none">
										<label><?php esc_html_e('Link JavaScript', 'essential-grid'); ?></label><input class="element-setting" type="text" name="element-javascript-link" value="" />
										<div class="div13"></div>
									</div>
									<div id="eg-element-link-details-wrap">
										<label ><?php esc_html_e('Link Target', 'essential-grid'); ?></label><!--
										--><select name="element-link-target">
											<option value="disabled"><?php esc_html_e('disabled', 'essential-grid'); ?></option>
											<option value="_self"><?php esc_html_e('_self', 'essential-grid'); ?></option>
											<option value="_blank"><?php esc_html_e('_blank', 'essential-grid'); ?></option>
											<option value="_parent"><?php esc_html_e('_parent', 'essential-grid'); ?></option>
											<option value="_top"><?php esc_html_e('_top', 'essential-grid'); ?></option>
										</select>
										<div class="div13"></div>

										<label ><?php esc_html_e('Use Tag', 'essential-grid'); ?></label><!--
										--><select name="element-tag-type">
											<option value="div"><?php esc_html_e('DIV', 'essential-grid'); ?></option>
											<option value="p"><?php esc_html_e('P', 'essential-grid'); ?></option>
											<option value="h2"><?php esc_html_e('H2', 'essential-grid'); ?></option>
											<option value="h3"><?php esc_html_e('H3', 'essential-grid'); ?></option>
											<option value="h4"><?php esc_html_e('H4', 'essential-grid'); ?></option>
											<option value="h5"><?php esc_html_e('H5', 'essential-grid'); ?></option>
											<option value="h6"><?php esc_html_e('H6', 'essential-grid'); ?></option>
										</select>
										<div class="div13"></div>
									</div>

									<!-- Facebook Fields -->
									<div class="eg-element-facebook-wrap" id="eg-element-facebook-wrap">
										<label ><?php esc_html_e('Link Target', 'essential-grid'); ?></label><!--
										--><select name="element-facebook-sharing-link">
											<option value="site"><?php esc_html_e("Parent Site URL",'essential-grid'); ?></option>
											<option value="post"><?php esc_html_e("Post URL",'essential-grid'); ?></option>
											<option value="custom"><?php esc_html_e("Custom URL",'essential-grid'); ?></option>
										</select>
										<div class="div13"></div>
										<div class="eg-element-facebook_link_custom">
											<label ><?php esc_html_e("URL",'essential-grid'); ?></label><input type="text" class="esg-w-250" name="element-facebook-link-url" value="">
											<div class="div13"></div>
										</div>
									</div>

									<!-- Gplus Fields -->
									<div class="eg-element-gplus-wrap" id="eg-element-gplus-wrap">
										<label><?php esc_html_e('Link Target', 'essential-grid'); ?></label><!--
										--><select name="element-gplus-sharing-link">
											<option value="site"><?php esc_html_e("Parent Site URL",'essential-grid'); ?></option>
											<option value="post"><?php esc_html_e("Post URL",'essential-grid'); ?></option>
											<option value="custom"><?php esc_html_e("Custom URL",'essential-grid'); ?></option>
										</select>
										<div class="div13"></div>
										<div class="eg-element-gplus_link_custom">
											<label ><?php esc_html_e("URL",'essential-grid'); ?></label><input type="text" class="esg-w-250" name="element-gplus-link-url" value="">
											<div class="div13"></div>
										</div>
									</div>
									
									<!-- Pinterest Fields -->
									<div class="eg-element-pinterest-wrap" id="eg-element-pinterest-wrap">
										<label ><?php esc_html_e('Link Target', 'essential-grid'); ?></label><!--
										--><select name="element-pinterest-sharing-link">
											<option value="site"><?php esc_html_e("Parent Site URL",'essential-grid'); ?></option>
											<option value="post"><?php esc_html_e("Post URL",'essential-grid'); ?></option>
											<option value="custom"><?php esc_html_e("Custom URL",'essential-grid'); ?></option>
										</select>
										<div class="div13"></div>

										<div class="eg-element-pinterest_link_custom">
											<label ><?php esc_html_e("URL",'essential-grid'); ?></label><input type="text" class="esg-w-250" name="element-pinterest-link-url" value="">
											<div class="div13"></div>
										</div>
										<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Use placeholder &#37;title&#37;, &#37;excerpt&#37; for replacement', 'essential-grid'); ?>"><?php esc_html_e("Description",'essential-grid'); ?></label><!--
										--><textarea type="text" name="element-pinterest-description" class="eg-tooltip-wrap esg-w-250" title="<?php esc_attr_e('Use placeholder &#37;title&#37;, &#37;excerpt&#37; for replacement', 'essential-grid'); ?>"></textarea>
										<div class="div13"></div>
									</div>
									
									<!-- Twitter Fields -->
									<div class="eg-element-twitter-wrap" id="eg-element-twitter-wrap">
										<label class="eg-tooltip-wrap" title="<?php esc_attr_e('Use placeholder &#37;title&#37;, &#37;excerpt&#37; for replacement', 'essential-grid'); ?>"><?php esc_html_e("Text before Link",'essential-grid'); ?></label><!--
										--><input type="text" name="element-twitter-text-before" value="" class="eg-tooltip-wrap esg-w-250" title="<?php esc_attr_e('Use placeholder &#37;title&#37;, &#37;excerpt&#37; for replacement', 'essential-grid'); ?>">
										<div class="div13"></div>

										<label ><?php esc_html_e('Link Target', 'essential-grid'); ?></label><!--
										--><select name="element-twitter-sharing-link">
											<option value="site"><?php esc_html_e("Parent Site URL",'essential-grid'); ?></option>
											<option value="post"><?php esc_html_e("Post URL",'essential-grid'); ?></option>
											<option value="custom"><?php esc_html_e("Custom URL",'essential-grid'); ?></option>
										</select>
										<div class="div13"></div>
										<div class="eg-element-twitter_link_custom">
											<label ><?php esc_html_e("URL",'essential-grid'); ?></label><input type="text" class="esg-w-250" name="element-twitter-link-url" value="">
											<div class="div13"></div>
										</div>
										<input type="hidden" name="element-twitter-text-after" value="" class="eg-tooltip-wrap esg-w-250" title="<?php esc_attr_e('Use placeholder &#37;title&#37;, &#37;excerpt&#37; for replacement', 'essential-grid'); ?>">
									</div>

									<!-- rel nofollow -->
									<label for="element-rel-nofollow"><?php esc_html_e('Add "nofollow"', 'essential-grid'); ?></label><!--
									--><input type="checkbox" name="element-rel-nofollow" value="true" /> <?php esc_html_e('Add rel=”nofollow” attribute to link', 'essential-grid'); ?>
									<div class="div13"></div>

									<!-- !important fix -->
									<label for="element-force-important"><?php esc_html_e('Fix: !important', 'essential-grid'); ?></label><!--
									--><input type="checkbox" name="element-force-important" value="true" /> <?php esc_html_e('Force !important in styles', 'essential-grid'); ?>
								</div>
							</div>
							<div id="dz-delete" class="eg-delete-wrapper esg-text-right">
								<div id="element-save-as-button" class="esg-btn esg-purple" ><i class="eg-icon-login"></i> <?php esc_html_e('Save as Template', 'essential-grid'); ?></div>
								<div id="element-delete-button" class="esg-btn esg-red"><i class="eg-icon-trash"></i> <?php esc_html_e('Remove', 'essential-grid'); ?></div>
							</div>
						</form>
					</div>

					<div id="element-setting-wrap-alternative">
						<div class="esg-note"><i class="material-icons">info</i><?php esc_html_e("Please Drop some Element from the Layer Templates into the ITEM LAYOUT drop zone to be able to edit any Elements here", 'essential-grid'); ?></div>
					</div>
				</div>
			</div>
		</div><!-- 
		
		THE ITEM LAYOUT --><div class="eg-pbox esg-box pinneable inunpinneablerange" id="eg-it-layout-wrap">
			<div class="esg-box-title"><i class="material-icons">menu</i><?php esc_html_e('Item Layout', 'essential-grid'); ?><div class="esg-pinme"><i class="material-icons">push_pin</i></div></div>

			<div class="esg-box-inside esg-box-inside-item-layout">
				<div class="esg-display-none">
					<?php esc_html_e('Show at Width:', 'essential-grid'); ?> <span id="element-item-skin-width-check" class="slider-settings"></span>
					<span id="currently-at-pixel">400px</span>
				</div>
				<div class="esg-btn esg-green" id="eg-preview-item-skin"><i class="eg-icon-play"></i><?php esc_html_e('Preview', 'essential-grid'); ?></div>
				<div class="esg-btn esg-red" id="eg-preview-stop-item-skin"><i class="eg-icon-stop"></i><?php esc_html_e('Stop', 'essential-grid'); ?></div>
				<div class="esg-btn esg-blue esg-f-right esg-margin-r-0" id="make-3d-map" ><?php esc_html_e('Show Schematic', 'essential-grid'); ?></div>
			</div>
			<div class="esg-box-inside">
				<div class="eg-editor-inside-wrapper">
					<div id="eg-dz-padding-wrapper" class="esg-media-cover-wrapper">
						<div id="eg-dz-hover-wrap">
							<!-- MEDIA -->
							<div id="skin-dz-media-bg-wrapper" class="esg-entry-media-wrapper esg-entry-media-wrapper-item-layout">
								<div id="skin-dz-media-bg"></div>
							</div>

							<!-- OVERLAYS -->
							<div id="skin-dz-wrapper">
								<div class="esg-cc eec" id="skin-dz-c-wrap">
									<div class="eg-element-cover"></div>
									<div id="eg-element-centerme-c">
										<div class="dropzonetext eg-drop-2">
											<div class="dropzonebg"></div>
											<div class="dropzoneinner"><?php esc_html_e('DROP ZONE', 'essential-grid'); ?></div>
										</div>
										<div id="skin-dz-c"></div>
									</div>
								</div>
								<div class="esg-tc eec" id="skin-dz-tl-wrap">
									<div class="dropzonetext eg-drop-1">
										<div class="dropzonebg"></div>
										<div class="dropzoneinner"><?php esc_html_e('DROP ZONE', 'essential-grid'); ?></div>
									</div>
									<div id="skin-dz-tl"><div class="eg-element-cover"></div></div>
								</div>
								<div class="esg-bc eec" id="skin-dz-br-wrap">
									<div class="dropzonetext eg-drop-3">
										<div class="dropzonebg"></div>
										<div class="dropzoneinner"><?php esc_html_e('DROP ZONE', 'essential-grid'); ?></div>
									</div>
									<div id="skin-dz-br"><div class="eg-element-cover"></div></div>
								</div>
							</div>

							<!-- CONTENT -->
							<div id="skin-dz-m-wrap" class="esg-entry-content">
								<div class="dropzonetext eg-drop-4">
									<div class="dropzonebg"></div>
									<div class="dropzoneinner"><?php esc_html_e('DROP ZONE', 'essential-grid'); ?></div>
								</div>

								<div id="skin-dz-m"></div>
							</div>

							<div class="clear"></div>
						</div>
					</div>
				</div>

				<!-- 3D MAP -->
				<div id="eg-3dpp" class="eg-3dpp esg-hidden">
					<div id="eg-3dpp-inner" class="esg-relative">
						<div class="eg-3dmc">
							<div class="eg-3d-bg"></div>
							<div class="eg-3d-cover"></div>
							<div class="eg-3d-elements">
								<div class="eg-3d-element"><i class="eg-icon-link esg-margin-r-10"></i><i class="eg-icon-search"></i></div>
								<div class="eg-3d-element eg-3d-element-title">LOREM IPSUM DOLOR</div>
								<div class="eg-3d-element-spacer"></div>
								<div class="eg-3d-element eg-3d-element-date">sed do ediusmod 09.06.2021</div>
							</div>
						</div>

						<div class="eg-3dcc">
							<div class="eg-3d-ccbg"></div>
							<div class="eg-3d-element 3d-cont eg-3d-element-title-cc">Lorem Ipsum Dolor</div>
							<div class="eg-3d-element 3d-cont eg-3d-element-desc-cc">Sit amet, consectetur adipisicing elit, sed ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exerci.</div>
							<div class="eg-3d-element 3d-cont eg-3d-element-lorem-cc">LOREM</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				<div id="eg-3d-description" class="esg-hidden">
					<span id="eg-3d-cstep1"><?php esc_html_e("Layers", 'essential-grid'); ?></span>
					<span id="eg-3d-cstep2"><?php esc_html_e("Covers", 'essential-grid'); ?></span>
					<span id="eg-3d-cstep3"><?php esc_html_e("Media", 'essential-grid'); ?></span>
					<span id="eg-3d-cstep4"><?php esc_html_e("Content & Layers", 'essential-grid'); ?></span>
				</div>
			</div>
			<div class="esg-box-inside esg-box-inside-p-20">
				<div class="esg-display-none">
					<?php esc_html_e('Show at Width:', 'essential-grid'); ?> <span id="element-item-skin-width-check" class="slider-settings"></span>
					<span id="currently-at-pixel">400px</span>
				</div>
				<div class="esg-btn esg-purple" id="layertotop"><i class="eg-icon-up-dir esg-margin-r-0"></i></div>
				<div class="esg-btn esg-purple" id="layertobottom"><i class="eg-icon-down-dir esg-margin-r-0"></i></div>
				<div class="esg-btn esg-purple esg-f-right esg-margin-r-0" id="drop-1"><?php esc_html_e('Hide DropZones', 'essential-grid'); ?></div>
			</div>
		</div>
	</div>


	<!--******************************
	-	THE ELEMENTS GRID	-
	******************************** -->
	<div class="eg-pbox esg-box fullwidtheg-pbox2 eg-transbg esg-w-670"><div class="esg-box-title"><i class="material-icons">folder</i><?php esc_html_e('Layer Templates', 'essential-grid'); ?></div>
		<div class="esg-box-inside esg-margin-0 esg-padding-0">

			<!-- GRID WRAPPER FOR CONTAINER SIZING   HERE YOU CAN SET THE CONTAINER SIZE AND CONTAINER SKIN-->
			<article id="eg-elements-container-grid-wrap" class="backend-flat myportfolio-container eg-startheight">

				<!-- THE GRID ITSELF WITH FILTERS, PAGINATION,  SORTING ETC... -->
				<div id="eg-elements-container-grid" class="esg-grid esg-text-center">

					<!-- THE FILTERING,  SORTING AND WOOCOMMERCE BUTTONS -->
					<article class="esg-filters esg-singlefilters "> <!-- Use esg-multiplefilters for Mixed Filtering, and esg-singlefilters for Single Filtering -->
						<!-- THE FILTER BUTTONS -->
						<div class="esg-filter-wrapper">
							<div class="esg-filterbutton selected esg-allfilter" data-filter="filterall"><span><?php esc_html_e('Filter - All', 'essential-grid'); ?></span></div>
							<div class="esg-filterbutton" data-filter="filter-icon"><span><?php esc_html_e('Icons', 'essential-grid'); ?></span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>
							<div class="esg-filterbutton" data-filter="filter-text"><span><?php esc_html_e('Texts', 'essential-grid'); ?></span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>
							<div class="esg-filterbutton" data-filter="filter-default"><span><?php esc_html_e('Default', 'essential-grid'); ?></span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>
						</div>

						<div class="clear"></div>

					</article><!-- END OF FILTERING, SORTING AND  CART BUTTONS -->

					<div class="clear"></div>

					<!-- ############################ -->
					<!-- THE GRID ITSELF WITH ENTRIES -->
					<!-- ############################ -->
					<ul id="" data-kriki="">
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return already escaped html markup
						echo $item_elements->prepareDefaultElementsForEditor();
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return already escaped html markup
						echo $item_elements->prepareTextElementsForEditor(); 
						?>
					</ul>

					<!-- The Pagination Container. Page Buttons will be added on demand Automatically !! -->
					<article class="esg-pagination"></article>
				</div>
			</article>

			<div class="clear"></div>
			<div class="eg-special">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return already escaped html markup
				echo $item_elements->prepareSpecialElementsForEditor();
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- function return already escaped html markup
				echo $item_elements->prepareAdditionalElementsForEditor();
				?>
			</div>
			<div class="eg-trashdropzone eg-tooltip-wrap" title="<?php esc_attr_e('Move element template over to Remove from layer templates', 'essential-grid'); ?>"><i class="eg-icon-trash"></i><div class="esg-text-container"><span><?php esc_html_e('DROP HERE', 'essential-grid'); ?></span></div></div>
		</div>
	</div>

	<div id="eg-inline-style-wrapper"></div>
	<?php
	Essential_Grid_Dialogs::fontello_icons_dialog();
	Essential_Grid_Dialogs::global_css_edit_dialog();
	Essential_Grid_Dialogs::meta_dialog();
	Essential_Grid_Dialogs::edit_advanced_rules_dialog();
	?>
</div>

<script type="text/javascript">
	window.ESG ??={};
	ESG.F ??= {};
	ESG.E ??= {};
	ESG.V ??= {};
	ESG.S ??= {};
	ESG.SC ??= {};
	ESG.C ??= {};
	ESG.LIB ??= {nav_skins: [], item_skins: {}, nav_originals: {}};
	ESG.CM ??= {apiJS: null, ajaxCSS: null, navCSS: null};
	ESG.WIN ??=jQuery(window);
	ESG.DOC ??=jQuery(document);
	ESG.LIB.COLOR_PRESETS = <?php echo wp_json_encode(ESGColorpicker::get_color_presets()); ?>;

	try {
		jQuery('.mce-notification-error').remove();
		jQuery('#wpbody-content >.notice').remove();
	} catch (e) {
	}

	ESG.E.waitTptFunc ??= [];
	ESG.E.waitTptFunc.push(function(){
		jQuery(function () {
			GridEditorEssentials.initFieldReferences();
			GridEditorEssentials.refreshInitElements(<?php echo wp_json_encode($item_elements->getElementsForJavascript()); ?>);
			GridEditorEssentials.setInitFontsJson(<?php echo wp_json_encode($fonts_full); ?>);
			GridEditorEssentials.setInitAllAttributesJson(<?php echo wp_json_encode($item_elements->get_existing_elements()); ?>);
			GridEditorEssentials.setInitMetaKeysJson(<?php echo wp_json_encode($meta_keys); ?>);
			GridEditorEssentials.initGridEditor(<?php echo wp_json_encode($skin_id ? 'update_item_skin' : ''); ?>);

			<?php if(!empty($skin['layers'])){ ?>
			GridEditorEssentials.setInitLayersJson(<?php echo wp_json_encode($skin['layers']); ?>);
			GridEditorEssentials.create_elements_by_data();
			<?php } ?>

			GridEditorEssentials.initDraggable();
			AdminEssentials.initSmallMenu();
			AdminEssentials.atDropStop();
			AdminEssentials.eg3dtakeCare();
			AdminEssentials.initSideButtons();

			jQuery('body').on("click", ".skin-dz-elements", function () {
				var bw = jQuery('#eg-layersettings-box-wrapper'),
					lsh = jQuery('#layer-settings-header');

				_tpt.gsap.to(bw, 0.3, {borderColor: "#8E44A9"});
				_tpt.gsap.to(bw, 0.3, {borderColor: "#ccc", delay: 0.5});
				if (lsh.hasClass("box-closed")) lsh.trigger('click');
			});
		});
	});
	
</script>
