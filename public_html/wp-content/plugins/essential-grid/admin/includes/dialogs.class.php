<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */
 
if( !defined( 'ABSPATH') ) exit();

class Essential_Grid_Dialogs {

	/**
	 * Insert Pages Dialog
	 * @since    1.0.0
	 */
	public static function pages_select_dialog(){
		$pages = apply_filters('essgrid_pages_select_dialog', get_pages(['sort_column' => 'post_name']));
		?>
		<div id="pages-select-dialog-wrap" class="essential-dialog-wrap essential-dialog-padding esg-display-none" title="<?php esc_attr_e('Choose Pages', 'essential-grid'); ?>">
			<label><?php esc_html_e('Choose Pages', 'essential-grid'); ?></label><!--
			--><div class="esg-dialog-page-wrap"><input type="checkbox" id="check-uncheck-pages"><div class="space18"></div><?php esc_html_e('Select All', 'essential-grid'); ?></div><div class="div5"></div>
				<?php foreach($pages as $page) { $p = str_replace('"', '', $page->post_title) . ' (ID: ' . $page->ID . ')'; ?>
					<label></label><div class="esg-dialog-page-wrap"><input type="checkbox" value="<?php echo esc_attr($p); ?>" name="selected-pages"><div class="space18"></div><?php echo esc_html($p); ?></div><div class="div5"></div>
				<?php } ?>
			<?php do_action('essgrid_pages_select_dialog_post', $pages); ?>
		</div>
		<?php
	}
	
	/**
	 * Insert global CSS Dialog
	 * @since    1.0.0
	 */
	public static function global_css_edit_dialog(){
		$global_css = apply_filters('essgrid_global_css_edit_dialog', Essential_Grid_Global_Css::get_global_css_styles());
		?>
		<div id="global-css-edit-dialog-wrap" class="essential-dialog-wrap esg-display-none" title="<?php esc_attr_e('Global Custom CSS', 'essential-grid'); ?>">
			<textarea id="eg-global-css-editor"><?php echo esc_textarea($global_css); ?></textarea>
			<?php do_action('essgrid_global_css_edit_dialog_post', $global_css); ?>
		</div>
		<?php
	}

	/**
	 * Insert Open Imported Grid Dialog
	 * @since    1.0.0
	 */
	public static function open_imported_grid(){
		?>
		<div id="open_imported_grid" class="essential-dialog-wrap essential-dialog-padding esg-display-none" title="<?php esc_attr_e('Open Imported Grid', 'essential-grid'); ?>">
			<label class="esg-w-100p"><?php esc_html_e('Do you want to edit the imported Grid ?', 'essential-grid'); ?></label>
			<?php do_action('essgrid_open_imported_grid_post'); ?>
		</div>
		<?php
	}
	
	/**
	 * Insert Error Import Grid Dialog
	 * @since    3.1.0
	 */
	public static function error_import_grid(){
		?>
		<div id="error_imported_grid" class="essential-dialog-wrap essential-dialog-padding esg-display-none" title="<?php esc_attr_e('Grid Import Error', 'essential-grid'); ?>">
			<p class="esg-w-100p esg-text-center"></p>
		</div>
		<?php
	}
	
	/**
	 * Insert navigation skin CSS Dialog
	 * @since    1.0.0
	 */
	public static function navigation_skin_css_edit_dialog(){
		?>
		<div id="navigation-skin-css-edit-dialog-wrap" class="essential-dialog-wrap esg-display-none" title="<?php esc_attr_e('Navigation Skin CSS', 'essential-grid'); ?>">
			<textarea id="eg-navigation-skin-css-editor"></textarea>
			<?php do_action('essgrid_navigation_skin_css_edit_dialog_post'); ?>
		</div>
		<?php
	}

	/**
	 * Insert navigation skin CSS Dialog
	 * @since    1.0.0
	 */
	public static function navigation_skin_css_selector_dialog(){
		?>
		<div id="navigation-skin-css-selector-dialog-wrap" class="essential-dialog-wrap esg-display-none" title="<?php esc_attr_e('Navigation Skin CSS Selector', 'essential-grid'); ?>">
			<div class="esg-simp-row"><label class="eg-tooltip-wrap tooltipstered"><?php esc_html_e('Navigation Template', 'essential-grid'); ?></label><select id="navigation-skin-css-selector"></select></div>
			<div class="esg-simp-row"><label class="eg-tooltip-wrap tooltipstered"><?php esc_html_e('Skin Name', 'essential-grid'); ?></label><input type="text" id="navigation-skin-css-name"></div>
			<div class="esg-simp-row"><label class="eg-tooltip-wrap tooltipstered"><?php esc_html_e('Class Name', 'essential-grid'); ?></label><span id="navigation-skin-css-class-name"></span></div>
			<?php do_action('essgrid_navigation_skin_css_selector_dialog_post'); ?>
		</div>
		<?php
	}
	
	/**
	 * Fontello Icons
	 * @since    1.0.0
	 */
	public static function fontello_icons_dialog(){
		?>
		<div id="eg-fontello-icons-dialog-wrap" class="eg-fontello-icons-dialog-wrap">
			<div class="search">
				<input type="text" id="eg-fontello-icons-search" placeholder="<?php esc_attr_e('Search Icon', 'essential-grid'); ?>" />
				<i class="eg-icon-cancel" id="eg-fontello-icons-search-cancel"></i>
			</div>
			<div class="font_headline">Fontello Icons</div>
			<div class="eg-icon-chooser eg-icon-soundcloud"></div><!--
			--><div class="eg-icon-chooser eg-icon-music"></div><!--
			--><div class="eg-icon-chooser eg-icon-color-adjust"></div><!--
			--><div class="eg-icon-chooser eg-icon-mail"></div><!--
			--><div class="eg-icon-chooser eg-icon-mail-alt"></div><!--
			--><div class="eg-icon-chooser eg-icon-heart"></div><!--
			--><div class="eg-icon-chooser eg-icon-heart-empty"></div><!--
			--><div class="eg-icon-chooser eg-icon-star"></div><!--
			--><div class="eg-icon-chooser eg-icon-star-empty"></div><!--
			--><div class="eg-icon-chooser eg-icon-user"></div><!--
			--><div class="eg-icon-chooser eg-icon-male"></div><!--
			--><div class="eg-icon-chooser eg-icon-female"></div><!--
			--><div class="eg-icon-chooser eg-icon-video"></div><!--
			--><div class="eg-icon-chooser eg-icon-videocam"></div><!--
			--><div class="eg-icon-chooser eg-icon-picture-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-camera"></div><!--
			--><div class="eg-icon-chooser eg-icon-camera-alt"></div><!--
			--><div class="eg-icon-chooser eg-icon-th-large"></div><!--
			--><div class="eg-icon-chooser eg-icon-th"></div><!--
			--><div class="eg-icon-chooser eg-icon-ok"></div><!--
			--><div class="eg-icon-chooser eg-icon-ok-circled2"></div><!--
			--><div class="eg-icon-chooser eg-icon-ok-squared"></div><!--
			--><div class="eg-icon-chooser eg-icon-cancel"></div><!--
			--><div class="eg-icon-chooser eg-icon-plus"></div><!--
			--><div class="eg-icon-chooser eg-icon-plus-circled"></div><!--
			--><div class="eg-icon-chooser eg-icon-plus-squared"></div><!--
			--><div class="eg-icon-chooser eg-icon-minus"></div><!--
			--><div class="eg-icon-chooser eg-icon-minus-circled"></div><!--
			--><div class="eg-icon-chooser eg-icon-minus-squared"></div><!--
			--><div class="eg-icon-chooser eg-icon-minus-squared-alt"></div><!--
			--><div class="eg-icon-chooser eg-icon-info-circled"></div><!--
			--><div class="eg-icon-chooser eg-icon-info"></div><!--
			--><div class="eg-icon-chooser eg-icon-home"></div><!--
			--><div class="eg-icon-chooser eg-icon-link"></div><!--
			--><div class="eg-icon-chooser eg-icon-unlink"></div><!--
			--><div class="eg-icon-chooser eg-icon-link-ext"></div><!--
			--><div class="eg-icon-chooser eg-icon-lock"></div><!--
			--><div class="eg-icon-chooser eg-icon-lock-open"></div><!--
			--><div class="eg-icon-chooser eg-icon-eye"></div><!--
			--><div class="eg-icon-chooser eg-icon-eye-off"></div><!--
			--><div class="eg-icon-chooser eg-icon-tag"></div><!--
			--><div class="eg-icon-chooser eg-icon-thumbs-up"></div><!--
			--><div class="eg-icon-chooser eg-icon-thumbs-up-alt"></div><!--
			--><div class="eg-icon-chooser eg-icon-download"></div><!--
			--><div class="eg-icon-chooser eg-icon-upload"></div><!--
			--><div class="eg-icon-chooser eg-icon-reply"></div><!--
			--><div class="eg-icon-chooser eg-icon-forward"></div><!--
			--><div class="eg-icon-chooser eg-icon-export-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-print"></div><!--
			--><div class="eg-icon-chooser eg-icon-gamepad"></div><!--
			--><div class="eg-icon-chooser eg-icon-trash"></div><!--
			--><div class="eg-icon-chooser eg-icon-doc-text"></div><!--
			--><div class="eg-icon-chooser eg-icon-doc-inv"></div><!--
			--><div class="eg-icon-chooser eg-icon-folder-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-folder-open"></div><!--
			--><div class="eg-icon-chooser eg-icon-folder-open-empty"></div><!--
			--><div class="eg-icon-chooser eg-icon-rss"></div><!--
			--><div class="eg-icon-chooser eg-icon-rss-squared"></div><!--
			--><div class="eg-icon-chooser eg-icon-phone"></div><!--
			--><div class="eg-icon-chooser eg-icon-menu"></div><!--
			--><div class="eg-icon-chooser eg-icon-cog-alt"></div><!--
			--><div class="eg-icon-chooser eg-icon-wrench"></div><!--
			--><div class="eg-icon-chooser eg-icon-basket-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-calendar"></div><!--
			--><div class="eg-icon-chooser eg-icon-calendar-empty"></div><!--
			--><div class="eg-icon-chooser eg-icon-lightbulb"></div><!--
			--><div class="eg-icon-chooser eg-icon-resize-full-alt"></div><!--
			--><div class="eg-icon-chooser eg-icon-move"></div><!--
			--><div class="eg-icon-chooser eg-icon-down-dir"></div><!--
			--><div class="eg-icon-chooser eg-icon-up-dir"></div><!--
			--><div class="eg-icon-chooser eg-icon-left-dir"></div><!--
			--><div class="eg-icon-chooser eg-icon-right-dir"></div><!--
			--><div class="eg-icon-chooser eg-icon-down-open"></div><!--
			--><div class="eg-icon-chooser eg-icon-left-open"></div><!--
			--><div class="eg-icon-chooser eg-icon-right-open"></div><!--
			--><div class="eg-icon-chooser eg-icon-angle-left"></div><!--
			--><div class="eg-icon-chooser eg-icon-angle-right"></div><!--
			--><div class="eg-icon-chooser eg-icon-angle-double-left"></div><!--
			--><div class="eg-icon-chooser eg-icon-angle-double-right"></div><!--
			--><div class="eg-icon-chooser eg-icon-left-big"></div><!--
			--><div class="eg-icon-chooser eg-icon-right-big"></div><!--
			--><div class="eg-icon-chooser eg-icon-up-hand"></div><!--
			--><div class="eg-icon-chooser eg-icon-ccw-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-shuffle-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-play"></div><!--
			--><div class="eg-icon-chooser eg-icon-play-circled"></div><!--
			--><div class="eg-icon-chooser eg-icon-stop"></div><!--
			--><div class="eg-icon-chooser eg-icon-pause"></div><!--
			--><div class="eg-icon-chooser eg-icon-fast-fw"></div><!--
			--><div class="eg-icon-chooser eg-icon-desktop"></div><!--
			--><div class="eg-icon-chooser eg-icon-laptop"></div><!--
			--><div class="eg-icon-chooser eg-icon-tablet"></div><!--
			--><div class="eg-icon-chooser eg-icon-mobile"></div><!--
			--><div class="eg-icon-chooser eg-icon-flight"></div><!--
			--><div class="eg-icon-chooser eg-icon-font"></div><!--
			--><div class="eg-icon-chooser eg-icon-bold"></div><!--
			--><div class="eg-icon-chooser eg-icon-italic"></div><!--
			--><div class="eg-icon-chooser eg-icon-text-height"></div><!--
			--><div class="eg-icon-chooser eg-icon-text-width"></div><!--
			--><div class="eg-icon-chooser eg-icon-align-left"></div><!--
			--><div class="eg-icon-chooser eg-icon-align-center"></div><!--
			--><div class="eg-icon-chooser eg-icon-align-right"></div><!--
			--><div class="eg-icon-chooser eg-icon-search"></div><!--
			--><div class="eg-icon-chooser eg-icon-indent-left"></div><!--
			--><div class="eg-icon-chooser eg-icon-indent-right"></div><!--
			--><div class="eg-icon-chooser eg-icon-ajust"></div><!--
			--><div class="eg-icon-chooser eg-icon-tint"></div><!--
			--><div class="eg-icon-chooser eg-icon-chart-bar"></div><!--
			--><div class="eg-icon-chooser eg-icon-magic"></div><!--
			--><div class="eg-icon-chooser eg-icon-sort"></div><!--
			--><div class="eg-icon-chooser eg-icon-sort-alt-up"></div><!--
			--><div class="eg-icon-chooser eg-icon-sort-alt-down"></div><!--
			--><div class="eg-icon-chooser eg-icon-sort-name-up"></div><!--
			--><div class="eg-icon-chooser eg-icon-sort-name-down"></div><!--
			--><div class="eg-icon-chooser eg-icon-coffee"></div><!--
			--><div class="eg-icon-chooser eg-icon-food"></div><!--
			--><div class="eg-icon-chooser eg-icon-medkit"></div><!--
			--><div class="eg-icon-chooser eg-icon-puzzle"></div><!--
			--><div class="eg-icon-chooser eg-icon-apple"></div><!--
			--><div class="eg-icon-chooser eg-icon-facebook"></div><!--
			--><div class="eg-icon-chooser eg-icon-gplus"></div><!--
			--><div class="eg-icon-chooser eg-icon-tumblr"></div><!--
			--><div class="eg-icon-chooser eg-icon-twitter-squared"></div><!--
			--><div class="eg-icon-chooser eg-icon-twitter"></div><!--
			--><div class="eg-icon-chooser eg-icon-vimeo-squared"></div><!--
			--><div class="eg-icon-chooser eg-icon-youtube"></div><!--
			--><div class="eg-icon-chooser eg-icon-youtube-squared"></div><!--
			--><div class="eg-icon-chooser eg-icon-youtube-play"></div><!--
			--><div class="eg-icon-chooser eg-icon-picture"></div><!--
			--><div class="eg-icon-chooser eg-icon-check"></div><!--
			--><div class="eg-icon-chooser eg-icon-back"></div><!--
			--><div class="eg-icon-chooser eg-icon-thumbs-up-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-thumbs-down"></div><!--
			--><div class="eg-icon-chooser eg-icon-download-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-upload-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-reply-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-forward-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-export"></div><!--
			--><div class="eg-icon-chooser eg-icon-folder"></div><!--
			--><div class="eg-icon-chooser eg-icon-rss-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-cog"></div><!--
			--><div class="eg-icon-chooser eg-icon-tools"></div><!--
			--><div class="eg-icon-chooser eg-icon-basket"></div><!--
			--><div class="eg-icon-chooser eg-icon-login"></div><!--
			--><div class="eg-icon-chooser eg-icon-logout"></div><!--
			--><div class="eg-icon-chooser eg-icon-resize-full"></div><!--
			--><div class="eg-icon-chooser eg-icon-popup"></div><!--
			--><div class="eg-icon-chooser eg-icon-arrow-combo"></div><!--
			--><div class="eg-icon-chooser eg-icon-left-open-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-right-open-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-left-open-mini"></div><!--
			--><div class="eg-icon-chooser eg-icon-right-open-mini"></div><!--
			--><div class="eg-icon-chooser eg-icon-left-open-big"></div><!--
			--><div class="eg-icon-chooser eg-icon-right-open-big"></div><!--
			--><div class="eg-icon-chooser eg-icon-left"></div><!--
			--><div class="eg-icon-chooser eg-icon-right"></div><!--
			--><div class="eg-icon-chooser eg-icon-ccw"></div><!--
			--><div class="eg-icon-chooser eg-icon-cw"></div><!--
			--><div class="eg-icon-chooser eg-icon-arrows-ccw"></div><!--
			--><div class="eg-icon-chooser eg-icon-level-down"></div><!--
			--><div class="eg-icon-chooser eg-icon-level-up"></div><!--
			--><div class="eg-icon-chooser eg-icon-shuffle"></div><!--
			--><div class="eg-icon-chooser eg-icon-palette"></div><!--
			--><div class="eg-icon-chooser eg-icon-list-add"></div><!--
			--><div class="eg-icon-chooser eg-icon-back-in-time"></div><!--
			--><div class="eg-icon-chooser eg-icon-monitor"></div><!--
			--><div class="eg-icon-chooser eg-icon-paper-plane"></div><!--
			--><div class="eg-icon-chooser eg-icon-brush"></div><!--
			--><div class="eg-icon-chooser eg-icon-droplet"></div><!--
			--><div class="eg-icon-chooser eg-icon-clipboard"></div><!--
			--><div class="eg-icon-chooser eg-icon-megaphone"></div><!--
			--><div class="eg-icon-chooser eg-icon-key"></div><!--
			--><div class="eg-icon-chooser eg-icon-github"></div><!--
			--><div class="eg-icon-chooser eg-icon-github-circled"></div><!--
			--><div class="eg-icon-chooser eg-icon-flickr"></div><!--
			--><div class="eg-icon-chooser eg-icon-flickr-circled"></div><!--
			--><div class="eg-icon-chooser eg-icon-vimeo"></div><!--
			--><div class="eg-icon-chooser eg-icon-vimeo-circled"></div><!--
			--><div class="eg-icon-chooser eg-icon-twitter-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-twitter-circled"></div><!--
			--><div class="eg-icon-chooser eg-icon-facebook-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-facebook-circled"></div><!--
			--><div class="eg-icon-chooser eg-icon-facebook-squared"></div><!--
			--><div class="eg-icon-chooser eg-icon-gplus-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-gplus-circled"></div><!--
			--><div class="eg-icon-chooser eg-icon-gplus-squared"></div><!--
			--><div class="eg-icon-chooser eg-icon-gplus-2"></div><!--
			--><div class="eg-icon-chooser eg-icon-pinterest"></div><!--
			--><div class="eg-icon-chooser eg-icon-pinterest-circled"></div><!--
			--><div class="eg-icon-chooser eg-icon-tumblr-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-tumblr-circled"></div><!--
			--><div class="eg-icon-chooser eg-icon-linkedin"></div><!--
			--><div class="eg-icon-chooser eg-icon-linkedin-circled"></div><!--
			--><div class="eg-icon-chooser eg-icon-dribbble"></div><!--
			--><div class="eg-icon-chooser eg-icon-dribbble-circled"></div><!--
			--><div class="eg-icon-chooser eg-icon-picasa"></div><!--
			--><div class="eg-icon-chooser eg-icon-instagram"></div><!--
			--><div class="eg-icon-chooser eg-icon-telegram"></div><!--
			--><div class="eg-icon-chooser eg-icon-ok-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-doc"></div><!--
			--><div class="eg-icon-chooser eg-icon-left-open-outline"></div><!--
			--><div class="eg-icon-chooser eg-icon-left-open-2"></div><!--
			--><div class="eg-icon-chooser eg-icon-right-open-outline"></div><!--
			--><div class="eg-icon-chooser eg-icon-right-open-2"></div><!--
			--><div class="eg-icon-chooser eg-icon-equalizer"></div><!--
			--><div class="eg-icon-chooser eg-icon-layers-alt"></div><!--
			--><div class="eg-icon-chooser eg-icon-pencil-1"></div><!--
			--><div class="eg-icon-chooser eg-icon-align-justify"></div>
			<?php
				$enable_fontello = get_option('tp_eg_global_enable_fontello', 'backfront');
				$enable_font_awesome = get_option('tp_eg_global_enable_font_awesome', 'false');
				$enable_pe7 = get_option('tp_eg_global_enable_pe7', 'false');
				if($enable_font_awesome!="false") include(ESG_PLUGIN_PATH . "admin/views/skin-font-awesome-list.php");
				if($enable_pe7!="false") include(ESG_PLUGIN_PATH . "admin/views/skin-pe-icon-7-stroke-list.php");
			
			do_action('essgrid_fontello_icons_dialog_post');
			?>
		</div>
		<?php
	}
	
	/**
	 * Insert custom meta Dialog
	 * @since    1.0.0
	 */
	public static function custom_meta_dialog(){
		?>
		<div id="custom-meta-dialog-wrap" class="essential-dialog-wrap essential-dialog-padding esg-display-none" title="<?php esc_attr_e('Custom Meta', 'essential-grid'); ?>">
			<label><?php esc_html_e('Name', 'essential-grid'); ?></label><input type="text" name="eg-custom-meta-name" value="" />
			<div class="div13"></div>
			<label><?php esc_html_e('Handle', 'essential-grid'); ?></label><strong class="esg-custom-meta-name-prefix">eg-</strong><input type="text" name="eg-custom-meta-handle" value="" />
			<div class="div20"></div>
			<strong class="esg-font-size-14"><?php esc_html_e('SETTINGS', 'essential-grid'); ?></strong>
			<div class="div13"></div>
			<label><?php esc_html_e('Default', 'essential-grid'); ?></label><input type="text" name="eg-custom-meta-default" value="" />
			<div class="div13"></div>
			<label><?php esc_html_e('Type', 'essential-grid'); ?></label><select name="eg-custom-meta-type"><option value="text"><?php esc_html_e('Text', 'essential-grid'); ?></option><option value="multi-select"><?php esc_html_e('Multi Select', 'essential-grid'); ?></option><option value="select"><?php esc_html_e('Select', 'essential-grid'); ?></option><option value="image"><?php esc_html_e('Image', 'essential-grid'); ?></option></select>
			<div id="eg-custom-meta-select-wrap" class="esg-display-none">
				<div class="div13"></div>
				<?php esc_html_e('Comma Seperated List of Elements', 'essential-grid'); ?>
				<textarea name="eg-custom-meta-select" class="esg-custom-meta-select"></textarea>
			</div>			
			<div class="div13"></div>
			<label><?php esc_html_e('Sort Type', 'essential-grid'); ?></label><select name="eg-custom-meta-sort-type"><option value="alphabetic"><?php esc_html_e('Alphabetic', 'essential-grid'); ?></option><option value="numeric"><?php esc_html_e('Numeric', 'essential-grid'); ?></option></select>
			<?php do_action('essgrid_custom_meta_dialog_post'); ?>
		</div>
		<?php
	}
	
	/**
	 * Insert link meta Dialog
	 * @since    1.5.0
	 */
	public static function custom_meta_linking_dialog(){
		?>
		<div id="link-meta-dialog-wrap" class="essential-dialog-wrap essential-dialog-padding esg-display-none" title="<?php esc_attr_e('Meta References', 'essential-grid'); ?>">
			<label><?php esc_html_e('Name:', 'essential-grid'); ?></label><input type="text" name="eg-link-meta-name" value="" />
			<div class="div20"></div>
			<strong class="esg-font-size-14"><?php esc_html_e('HANDLES', 'essential-grid'); ?></strong>
			<div class="div13"></div>
			<label><?php esc_html_e('Internal:', 'essential-grid'); ?></label><strong class="esg-link-meta-prefix">egl-</strong><input type="text" name="eg-link-meta-handle" value="" />
			<div class="div13"></div>
			<label><?php esc_html_e('Original:', 'essential-grid'); ?></label><input type="text" name="eg-link-meta-original" value="" />
			<div class="div20"></div>
			<strong class="esg-font-size-14"><?php esc_html_e('SORTING', 'essential-grid'); ?></strong>
			<div class="div13"></div>
			<label><?php esc_html_e('Sort Type:', 'essential-grid'); ?></label><select name="eg-link-meta-sort-type"><option value="alphabetic"><?php esc_html_e('Alphabetic', 'essential-grid'); ?></option><option value="numeric"><?php esc_html_e('Numeric', 'essential-grid'); ?></option></select>
			<?php do_action('essgrid_custom_meta_linking_dialog_post'); ?>
		</div>
		<?php
	}
	
	/**
	 * Insert Widget Areas Dialog
	 * @since    1.0.0
	 */
	public static function widget_areas_dialog(){
		?>
		<div id="widget-areas-dialog-wrap" class="essential-dialog-wrap essential-dialog-padding esg-display-none" title="<?php esc_attr_e('New Widget Area', 'essential-grid'); ?>">
			<div class="eg-cus-row-l"><label><?php esc_html_e('Handle:', 'essential-grid'); ?></label><span class="esg-widget-areas-dialog-prefix"><strong>eg-</strong></span><input type="text" name="eg-widget-area-handle" value="" /></div>
			<div class="eg-cus-row-l"><label><?php esc_html_e('Name:', 'essential-grid'); ?></label><input type="text" name="eg-widget-area-name" class="esg-widget-areas-name" value="" /></div>
			<?php do_action('essgrid_widget_areas_dialog_post'); ?>
		</div>
		<?php
	}
	
	/**
	 * Meta Dialog
	 * @since    1.0.0
	 */
	public static function meta_dialog(){
		$m = new Essential_Grid_Meta();
		$item_ele = new Essential_Grid_Item_Element();
		
		$post_items = $item_ele->getPostElementsArray();
		$metas = $m->get_all_meta();
		if (!is_array($post_items)) $post_items = [];
		if (!is_array($metas)) $metas = [];
		?>
		<div id="meta-dialog-wrap" class="essential-dialog-wrap esg-display-none" title="<?php esc_attr_e('Meta Key Picker', 'essential-grid'); ?>">
			<table>
				<tr class="eg-table-title"><td><?php esc_html_e('Meta Handle', 'essential-grid'); ?></td><td><?php esc_html_e('Description', 'essential-grid'); ?>
			<?php
			foreach($post_items as $phandle => $pitem){
				echo '<tr class="eg-add-meta-to-textarea"><td>%'.esc_html($phandle).'%</td><td>'.esc_html($pitem['name']).'</td></tr>';
			}
			

			foreach($metas as $meta){
				if($meta['m_type'] == 'link'){
					echo '<tr class="eg-add-meta-to-textarea"><td>%egl-'.esc_html($meta['handle']).'%</td><td>'.esc_html($meta['name']).'</td></tr>';
				}else{
					echo '<tr class="eg-add-meta-to-textarea"><td>%eg-'.esc_html($meta['handle']).'%</td><td>'.esc_html($meta['name']).'</td></tr>';
				}
			}
			
			do_action('essgrid_meta_dialog_post');
			?>
			</table>
		</div>
		<?php
	}
	
	/**
	 * Post Meta Dialog
	 * @since    1.0.0
	 */
	public static function post_meta_dialog(){
		?>
		<div id="post-meta-dialog-wrap" class="essential-dialog-wrap esg-display-none" title="<?php esc_attr_e('Post Meta Editor', 'essential-grid'); ?>">
			<div id="eg-meta-box">
			</div>
			<?php do_action('essgrid_post_meta_dialog_post'); ?>
		</div>
		<?php
	}
	
	/**
	 * Custom Element Image Dialog
	 * @since    1.0.1
	 */
	public static function custom_element_image_dialog(){
		?>
		<div id="custom-element-image-dialog-wrap" class="essential-dialog-wrap esg-display-none" title="<?php esc_attr_e('Add Images to Grid', 'essential-grid'); ?>">
			<?php esc_html_e('Please choose how you would like to add image(s) to the grid.', 'essential-grid'); ?><br>
			<?php esc_html_e('Single or bulk upload is possible', 'essential-grid'); ?>
			<div class="div13"></div><!--
			--><label class="esg-w-120"><?php esc_html_e('Add Filter(s)', 'essential-grid'); ?></label><input class="custom-filter-hiddeninput-bulk esg-display-none" type="text" name="custom-filter" value="" /><!--
			--><select class="custom-filter-list-bulk esg-w-260" multiple="multiple"></select>
			<div class="div13"></div>
			<label class="esg-w-120"></label><div class="esg-btn esg-purple add_new_custom_category_quick esg-font-size-12"><i class="material-icons">add</i><?php esc_html_e('New Filter', 'essential-grid'); ?></div>
			<?php do_action('essgrid_custom_element_image_dialog_post'); ?>
		</div>
		<?php
	}
	
	/**
	 * Advanced Rules Dialog for Item Skin Editor
	 * @since    1.5.0
	 */
	public static function edit_advanced_rules_dialog(){
		$base = new Essential_Grid_Base();
		$types = $base->get_media_source_order();
		?>
		<div id="advanced-rules-dialog-wrap" class="essential-dialog-wrap esg-display-none" title="<?php esc_attr_e('Advanced Rules', 'essential-grid'); ?>">
			<form id="ar-form-wrap">
				<div class="ad-rules-main">
					<label><?php esc_html_e('Show/Hide if rules are true:', 'essential-grid'); ?></label><div class="space18"></div><!--
					--><input class="ar-show-field" type="radio" value="show" name="ar-show" checked="checked" /> <?php esc_html_e('Show', 'essential-grid'); ?><div class="space18"></div><input class="ar-show-field" type="radio" value="hide" name="ar-show" /> <?php esc_html_e('Hide', 'essential-grid'); ?>
				</div>
				<div class="ar-form-table-wrapper ar-form-table-heading-wrapper">
					<table>
						<tr>
							<td class="esg-w-145"><?php esc_html_e('Type', 'essential-grid'); ?></td>
							<td class="esg-w-140"><?php esc_html_e('Meta', 'essential-grid'); ?></td>
							<td class="esg-w-85"><?php esc_html_e('Operator', 'essential-grid'); ?></td>
							<td class="esg-w-105"><?php esc_html_e('Value', 'essential-grid'); ?></td>
							<td class="esg-w-105"><?php esc_html_e('Value', 'essential-grid'); ?></td>
						</tr>
					</table>
				</div>
				<div class="ar-form-table-wrapper ar-form-table-data-wrapper">
				<?php
				$num = 0;
				for($i=0;$i<=2;$i++){
					?>
						<table>	
							<?php 
							for($g=0;$g<=2;$g++){
								?>
								<tr>
									<td class="esg-text-center">
										<select class="ar-type-field esg-w-145" id="ar-field-<?php echo esc_attr($num - 1); ?>" name="ar-type[]">
											<option value="off"><?php esc_html_e('--- Choose ---', 'essential-grid'); ?></option>
											<?php
											if(!empty($types)){
												foreach($types as $handle => $val){ ?>
													<option value="<?php echo esc_attr($handle); ?>"><?php echo esc_html($val['name']); ?></option>
												<?php }
											}
											?>
											<option value="meta"><?php esc_html_e('Meta', 'essential-grid'); ?></option>
										</select>
									</td>
									<td class="esg-padding-r-0">
										<input class="ar-meta-field esg-w-145" type="text" name="ar-meta[]" value="" disabled="disabled" /><div class="ar-open-meta"><i class="material-icons">get_app</i></div>
									</td>
									<td class="esg-text-center">
										<select class="ar-operator-field esg-w-78" name="ar-operator[]">
											<option value="isset"><?php esc_html_e('isset', 'essential-grid'); ?></option>
											<option value="empty"><?php esc_html_e('empty', 'essential-grid'); ?></option>
											<option class="ar-opt-meta" value="lt"><</option>
											<option class="ar-opt-meta" value="gt">></option>
											<option class="ar-opt-meta" value="equal">==</option>
											<option class="ar-opt-meta" value="notequal">!=</option>
											<option class="ar-opt-meta" value="lte"><=</option>
											<option class="ar-opt-meta" value="gte">>=</option>
											<option class="ar-opt-meta" value="between"><?php esc_html_e('between', 'essential-grid'); ?></option>
										</select>
									</td>
									<td>
										<input class="ar-value-field esg-w-100" type="text" name="ar-value[]" value="" />
									</td>
									<td class="esg-padding-r-15">
										<input class="esg-w-110" type="text"  name="ar-value-2[]" value="" disabled="disabled" />
									</td>
									
								</tr>
								<?php
								if($g !== 2){
									?>
									<tr>
										<td colspan="5" class="esg-text-center">
											<select class="ar-logic-field esg-w-100" id="ar-field-<?php echo esc_attr($num); ?>-logic" name="ar-logic[]">
												<option value="and"><?php esc_html_e('and', 'essential-grid'); ?></option>
												<option value="or"><?php esc_html_e('or', 'essential-grid'); ?></option>
											</select>
										</td>
									</tr>
									<?php
								}
								$num++;
							}
							?>
						</table>
					
					<?php
					if($i !== 2){
						?>
						<div class="esg-text-center">
							<select class="ar-logic-glob-field esg-w-100" name="ar-logic-glob[]">
								<option value="and"><?php esc_html_e('and', 'essential-grid'); ?></option>
								<option value="or"><?php esc_html_e('or', 'essential-grid'); ?></option>
							</select>
						</div>
						<?php
					}
				}
				?></div><?php 
				do_action('essgrid_edit_advanced_rules_dialog_post');
				?>
			</form>
		</div>
		<?php
	}
	
	/**
	 * Edit ShortCode Dialog V3.0.0
	 * @since 3.0.0
	 */
	public static function essgrid_add_shortcode_builder() {
		global $post;
		
		$post_type = '';
		if (isset($post->ID)) $post_type = get_post_type( $post->ID );
		
		$base = new Essential_Grid_Base();
		$skins_c = new Essential_Grid_Item_Skin();
		$grids = Essential_Grid_Db::get_entity('grids')->get_grids_column('name', 'handle');
		?>
		<script type="text/javascript">
			var token = '<?php echo esc_js(wp_create_nonce("Essential_Grid_actions")); ?>';
		</script>
		<div id="eesgShortCodeBuilder" class="essential-dialog-wrap esg-display-none" title="<?php esc_attr_e('Shortcode Builder', 'essential-grid'); ?>">
			<div class="esg-box">
				<div id="esg-shortcode-settings" class="esg-settings-container active-esc">
					<div> <!-- BASIC SETTINGS -->
						<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Grid Source', 'essential-grid'); ?></span></esg-llabel></div>
						<div class="eg-cs-tbc eg-cs-tbc-no-border">
							<div class="esg-settings-grid-wrap">
								<label for="esg-sc-pregrid" class="eg-tooltip-wrap tooltipstered"><?php esc_html_e('Grid Base', 'essential-grid'); ?></label><!--
								--><select id="esg-sc-pregrid" class="esg-sc-pregrid" name="esg-sc-pregrid">
									<option value="-1"><?php esc_html_e('Select Grid', 'essential-grid'); ?></option>
									<?php
									foreach($grids as $title => $alias){
										echo '<option value="'.esc_attr($alias).'">'.esc_html($title).'</option>'."\n";
									}
									?>
								</select>
							</div>
							<div class="esg-settings-exset-wrap">
								<label for="esg-sc-existing-settings"><?php esc_html_e('Grid Base', 'essential-grid'); ?></label><!--
								--><select id="esg-sc-existing-settings" class="esg-sc-existing-settings" name="esg-sc-existing-settings">
									<option value="-1"><?php esc_html_e('Custom Settings', 'essential-grid'); ?></option>
									<?php
									foreach($grids as $title => $alias){
										echo '<option value="'.esc_attr($alias).'">'.esc_html($title).'</option>'."\n";
									}
									?>
								</select>
							</div>	
							<div class="div13"></div>
							<label for="esg-sc-source" class="eg-tooltip-wrap tooltipstered"><?php esc_html_e('Content From', 'essential-grid'); ?></label><!--
							--><select id="esg-sc-source" name="esg-sc-source" class="esg-sc-source">
								<option value="pre"><?php esc_html_e('Predefined', 'essential-grid'); ?></option>
								<option value="custom"><?php esc_html_e('Custom Grid', 'essential-grid'); ?></option>
								<option value="popular"><?php esc_html_e('Popular Post', 'essential-grid'); ?></option>
								<option value="recent"><?php esc_html_e('Recent Post', 'essential-grid'); ?></option>
								<?php if ($post_type === 'post'){ ?>
									<option value="related"><?php esc_html_e('Related Post', 'essential-grid'); ?></option>
								<?php } ?>
							</select>
							<div id="eg-related-post-based-details">
								<div class="div13"></div>
								<label for="esg-sc-relatedbased" class="eg-tooltip-wrap tooltipstered"><?php esc_html_e('Related Post based on', 'essential-grid'); ?></label><!--
								--><select id="esg-sc-relatedbased" name="esg-sc-relatedbased" class="esg-sc-relatedbased">
									<option value="both"><?php esc_html_e('Tags & Categories', 'essential-grid'); ?></option>
									<option value="tags"><?php esc_html_e('Tags', 'essential-grid'); ?></option>
									<option value="categories"><?php esc_html_e('Categories', 'essential-grid'); ?></option>
								</select>
							</div>
							<div class="esgsettings-max-entries">
								<div class="div13"></div>
								<label for="esg-sc-max-entries"><?php esc_html_e('Maximum Entries', 'essential-grid'); ?></label><!--
								--><input type="text" id="esg-sc-max-entries" name="esg-sc-max-entries" value="20" />
							</div>	
						</div>
					</div>
					<div id="esg_shortcode_custom_settings_bottom">
						<div class="eg-cs-tbc-left"><esg-llabel><span><?php esc_html_e('Custom Settings', 'essential-grid'); ?></span></esg-llabel></div>
						<div class="eg-cs-tbc eg-cs-tbc-shortcode-custom-settings-bottom">
							<label for="esg-sc-entry-skin"><?php esc_html_e('Grid Skin', 'essential-grid'); ?></label><!--
							--><select id="esg-sc-entry-skin" name="esg-sc-entry-skin">
								<?php
								$skins = Essential_Grid_Item_Skin::get_essential_item_skins('all', false);
								
								if(!empty($skins)){
									foreach($skins as $skin){
										echo '<option value="'.esc_attr($skin['id']).'">'.esc_html($skin['name']).'</option>'."\n";
									}
								}
								?>
							</select>
							<div class="div13"></div>
							<label for="esg-sc-layout-sizing"><?php esc_html_e('Layout', 'essential-grid'); ?></label><!--
							--><select id="esg-sc-layout-sizing" name="esg-sc-layout-sizing">
								<option value="boxed"><?php esc_html_e('Boxed', 'essential-grid'); ?></option>
								<option value="fullwidth"><?php esc_html_e('Fullwidth', 'essential-grid'); ?></option>
							</select>
							<div class="div13"></div>
							<label for="esg-sc-grid-layout"><?php esc_html_e('Grid Layout', 'essential-grid'); ?></label><!--
							--><select id="esg-sc-grid-layout" name="esg-sc-grid-layout">
								<option value="even"><?php esc_html_e('Even', 'essential-grid'); ?></option>
								<option value="masonry"><?php esc_html_e('Masonry', 'essential-grid'); ?></option>
								<option value="cobbles"><?php esc_html_e('Cobbles', 'essential-grid'); ?></option>
							</select>
							<div class="div13"></div>
							<label for="esg-sc-spacings"><?php esc_html_e('Item Spacing', 'essential-grid'); ?></label><!--
							--><input type="text" id="esg-sc-spacings" name="esg-sc-spacings" value="0" />
							<div class="div13"></div>
							<label for="esg-sc-rows-unlimited"><?php esc_html_e('Pagination', 'essential-grid'); ?></label><!--
							--><input type="radio" class="esg-margin-l-0" id="esg-sc-rows-unlimited" name="esg-sc-rows-unlimited" value="on" /><?php esc_html_e('Disable', 'essential-grid'); ?><div class="space18"></div><!--
							--><input type="radio" name="esg-sc-rows-unlimited" checked="checked" value="off" /><?php esc_html_e('Enable', 'essential-grid'); ?> 
							<div class="div13"></div>
							<label for="esg-sc-columns"><?php esc_html_e('Columns', 'essential-grid'); ?></label><!--
							--><input type="text" id="esg-sc-columns" name="esg-sc-columns" value="5" />
							<div class="div13"></div>
							<label for="esg-sc-rows"><?php esc_html_e('Max. Visible Rows', 'essential-grid'); ?></label><!--
							--><input type="text" id="esg-sc-rows" name="esg-sc-rows" value="3" />
							<div class="div13"></div>
							<label for="grid-animation-select"><?php esc_html_e('Start and Filter Animations', 'essential-grid'); ?></label><!--
							<?php
							$anims = Essential_Grid_Base::get_grid_animations();
							?>
							--><select class="eg-tooltip-wrap tooltipstered" name="esg-sc-grid-animation" id="grid-animation-select">
								<?php
								foreach($anims as $value => $name){
									echo '<option value="'.esc_attr($value).'">'.esc_html($name).'</option>'."\n";
								}
								?>
							</select>
							<div class="div13"></div>
							<label for="use_spinner"><?php esc_html_e('Choose Spinner', 'essential-grid'); ?></label><!--
							--><select class="eg-tooltip-wrap tooltipstered" name="esg-sc-use-spinner" id="use_spinner">
								<option value="-1"><?php esc_html_e('off', 'essential-grid'); ?></option>
								<option value="0" selected="selected">0</option>
								<option value="1">1</option>
								<option value="2">2</option>
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
							</select>
						</div>
					</div><!-- END PF SHORTCODE CUSTOM SETTINGS BOTTOM -->
					<div id="esg_shortcode_custom_settings_add_elements">
						<div class="">
							<div class="eg-cs-tbc-left">
								<esg-llabel><span><?php esc_html_e('Items', 'essential-grid'); ?></span></esg-llabel>
							</div>
							<div class="eg-cs-tbc eg-cs-tbc-shortcode-custom-items" id="esg_shortcode_custom_items"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- SHORTCODE READER -->
		<div id="esg_read_old_shortcode_dialog" class="essential-dialog-wrap essential-dialog-padding esg-display-none" title="<?php esc_attr_e('Read Shortcode', 'essential-grid'); ?>">
			<label for="esg_import_shortcode_text"><?php esc_html_e('Insert Shortcode', 'essential-grid'); ?></label><textarea class="esg-import-shortcode-text" id="esg_import_shortcode_text"></textarea>
		</div>
		
		<!-- CUSTOM SHORTCODE ITEM EDITOR -->
		<div id="edit-custom-element-quick-dialog-wrap" class="essential-dialog-wrap essential-dialog-padding esg-display-none" title="<?php esc_attr_e('Custom Element Settings', 'essential-grid'); ?>">
			<input type="hidden" name="custom-type" value="" />
			<div class="esg-item-skin-elements">
				<label for="esg-csi-title"><?php esc_html_e('Title', 'essential-grid'); ?></label><input type="text" id="esg-csi-title" value="" />
				<div class="esg-csi-itemtype-image">
					<div class="div13"></div>
					<label><?php esc_html_e('Image/Poster', 'essential-grid'); ?></label><!--
					--><input type="hidden" value="" id="esg-csi-image" name="esg-csi-image"><!--
					--><div class="esg-btn esg-purple ess-grid-select-image"  data-setto="esg-custom-image"><?php esc_html_e('Choose Image', 'essential-grid'); ?></div><!--
					--><div id="custom-image-wrapper" class="esg-custom-image-wrapper"><img id="esg-custom-image-img" class="esg-custom-image-img-1" src="" alt="" /></div> <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
				</div>
				<div class="esg-csi-itemtype-html5">
					<div class="div13"></div>
					<label for="esg-csi-mp4"><?php esc_html_e('MP4 URL', 'essential-grid'); ?></label><input type="text" id="esg-csi-mp4" value="" />
				</div>
				<div class="esg-csi-itemtype-vimeo">
					<div class="div13"></div>
					<label for="esg-csi-vimeo"><?php esc_html_e('Vimeo ID', 'essential-grid'); ?></label><input type="text" id="esg-csi-vimeo" value="" />
				</div>
				<div class="esg-csi-itemtype-youtube">
					<div class="div13"></div>
					<label for="esg-csi-youtube"><?php esc_html_e('YouTube ID', 'essential-grid'); ?></label><input type="text" id="esg-csi-youtube" value="" />
				</div>
				<div class="esg-csi-itemtype-soundcloud">
					<div class="div13"></div>
					<label for="esg-csi-soundcloud"><?php esc_html_e('SoundCloud ID', 'essential-grid'); ?></label><input type="text" id="esg-csi-soundcloud" value="" />
				</div>
				<div class="esg-csi-itemtype-video">
					<div class="div13"></div>
					<label for="esg-csi-ratio"><?php esc_html_e('Video Ratio', 'essential-grid'); ?></label><!--
					--><select id="esg-csi-ratio">
						<option value="1"><?php esc_html_e('16:9', 'essential-grid'); ?></option>
						<option value="0"><?php esc_html_e('4:3', 'essential-grid'); ?></option>
					</select>
				</div>
				<div class="esg-csi-itemtype-cobbles">
					<div class="div13"></div>
					<label for="esg-csi-cobbles-size"><?php esc_html_e('Cobbles Element Size', 'essential-grid'); ?></label><!--
					--><select id="esg-csi-cobbles-size" class="esg-csi-cobbles-size">
							<option value="1:1"><?php esc_html_e('width 1, height 1', 'essential-grid'); ?></option>
							<option value="1:2"><?php esc_html_e('width 1, height 2', 'essential-grid'); ?></option>
							<option value="1:3"><?php esc_html_e('width 1, height 3', 'essential-grid'); ?></option>
							<option value="2:1"><?php esc_html_e('width 2, height 1', 'essential-grid'); ?></option>
							<option value="2:2"><?php esc_html_e('width 2, height 2', 'essential-grid'); ?></option>
							<option value="2:3"><?php esc_html_e('width 2, height 3', 'essential-grid'); ?></option>
							<option value="3:1"><?php esc_html_e('width 3, height 1', 'essential-grid'); ?></option>
							<option value="3:2"><?php esc_html_e('width 3, height 2', 'essential-grid'); ?></option>
							<option value="3:3"><?php esc_html_e('width 3, height 3', 'essential-grid'); ?></option>
						</select>
				</div>
				<div class="div13"></div>
				<label for="esg-csi-skin"><?php esc_html_e('Specific Skin', 'essential-grid'); ?></label><!--
				--><select id="esg-csi-skin" name="esg-csi-skin">
					<option value="-1"><?php esc_html_e('-- Default Skin --', 'essential-grid'); ?></option>
					<?php
					$skins = Essential_Grid_Item_Skin::get_essential_item_skins('all', false);
					
					if(!empty($skins)){
						foreach($skins as $skin){
							echo '<option value="'.esc_attr($skin['id']).'">'.esc_html($skin['name']).'</option>'."\n";
						}
					}
					?>
				</select>
				<div class="div13"></div>
			</div>
		</div>

		<script type="text/javascript">
				<?php
				$skin_layers = [];
				
				$all_skins = $skins_c->get_essential_item_skins();
				
				if(!empty($all_skins)){
					foreach($all_skins as $cskin){
						$custom_layer_elements = [];
						if(isset($cskin['layers'])){
							foreach($cskin['layers'] as $layer){
								if(isset($layer['settings']['source'])){
									switch($layer['settings']['source']){
										case 'post':
											$custom_layer_elements[$base->getVar($layer, ['settings', 'source-post'])] = '';
											break;
										case 'woocommerce':
											$custom_layer_elements[$base->getVar($layer, ['settings', 'source-woocommerce'])] = '';
											break;
									}
									
								}
							}
						}
						$skin_layers[$cskin['id']] = $custom_layer_elements;
					}
				}
				
				?>
				
				var esg_tiny_skin_layers = <?php echo wp_json_encode($skin_layers); ?>;
				
				<?php do_action('essgrid_edit_custom_element_dialog_script'); ?>
			</script>
		<?php
		do_action('essgrid_add_shortcode_builder'); 
	}
	
	/**
	 * Edit Custom Element Dialog
	 * @since    1.0.0
	 */
	public static function edit_custom_element_dialog(){
		$meta = new Essential_Grid_Meta();
		$item_elements = new Essential_Grid_Item_Element();
		
		?>
		<div id="edit-custom-element-dialog-wrap" class="essential-dialog-wrap essential-dialog-padding-15 esg-display-none" title="<?php esc_attr_e('Element Settings', 'essential-grid'); ?>">
			<ul class="eg-option-tabber-wrapper">
				<li id="custom_media_settings_menu_media" class="eg-option-tabber selected" data-target="#esg-item-skin-elements-media"><span class="dashicons dashicons-admin-media"></span><?php esc_html_e('Media', 'essential-grid'); ?></li><!--
				--><li id="custom_media_settings_menu_itemsettings" class="eg-option-tabber" data-target="#esg-item-skin-elements-settings"><span class="dashicons dashicons-list-view"></span><?php esc_html_e('Item Settings', 'essential-grid'); ?></li><!--
				--><li class="eg-option-tabber" data-target="#esg-item-skin-elements-datas"><span class="dashicons dashicons-align-center"></span><?php esc_html_e('Item Datas', 'essential-grid'); ?></li><!--
				--><li class="eg-option-tabber" data-target="#esg-item-skin-elements-other"><span class="dashicons dashicons-admin-appearance"></span><?php esc_html_e('Other', 'essential-grid'); ?></li>
			</ul>
			<form id="edit-custom-element-form">
				<div id="esg-item-skin-elements-media" class="eg-options-tab esg-display-block">
					<input type="hidden" name="custom-type" value="" />
					<div class="esg-item-skin-elements" id="esg-item-skin-elements-media-image">
						<label for="custom-image"><?php esc_html_e('Main Image', 'essential-grid'); ?></label><!--
						--><input type="hidden" value="" id="esg-custom-image" name="custom-image"><!--
						--><div id="eg-custom-choose-from-image-library" class="esg-btn esg-purple"  data-setto="esg-custom-image"><?php esc_html_e('Choose Image', 'essential-grid'); ?></div><!--
						--><div id="eg-custom-clear-from-image-library" class="esg-btn esg-red eg-custom-remove-custom-meta-field" ><?php esc_html_e('Remove Image', 'essential-grid'); ?></div>
						<div id="custom-image-wrapper" class="esg-custom-image-wrapper"><img id="esg-custom-image-img" class="esg-custom-image-img" src="" alt=""></div> <?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>
					</div>
					<div class="esg-item-skin-elements" id="esg-item-skin-elements-media-sound">
						<div class="div13"></div>
						<label  for="custom-soundcloud"><?php esc_html_e('SoundCloud Track ID', 'essential-grid'); ?></label><input name="custom-soundcloud" type="text" value="" />
					</div>
					<div class="esg-item-skin-elements" id="esg-item-skin-elements-media-youtube">
						<div class="div13"></div>
						<label  for="custom-soundcloud"><?php esc_html_e('YouTube ID', 'essential-grid'); ?></label><input name="custom-youtube" type="text" value="" />
					</div>
					<div class="esg-item-skin-elements" id="esg-item-skin-elements-media-vimeo">
						<div class="div13"></div>
						<label  for="custom-soundcloud"><?php esc_html_e('Vimeo ID', 'essential-grid'); ?></label><input name="custom-vimeo" type="text" value="" />
					</div>
					<div class="esg-item-skin-elements" id="esg-item-skin-elements-media-html5">
						<div class="div13"></div>
						<label  for="custom-html5-mp4"><?php esc_html_e('MP4', 'essential-grid'); ?></label><input name="custom-html5-mp4" type="text" value="" />
						<div class="div13"></div>
						<label  for="custom-html5-ogv"><?php esc_html_e('OGV', 'essential-grid'); ?></label><input name="custom-html5-ogv" type="text" value="" />
						<div class="div13"></div>
						<label  for="custom-html5-webm"><?php esc_html_e('WEBM', 'essential-grid'); ?></label><input name="custom-html5-webm" type="text" value="" />
						
					</div>
					
					<div class="esg-item-skin-elements" id="esg-item-skin-elements-media-ratio">
						<div class="div13"></div>
						<label  for="custom-ratio"><?php esc_html_e('Video Ratio', 'essential-grid'); ?></label><!--
						--><select name="custom-ratio">
							<option value="1"><?php esc_html_e('16:9', 'essential-grid'); ?></option>
							<option value="0"><?php esc_html_e('4:3', 'essential-grid'); ?></option>
						</select>
					</div>
				</div>
				<div id="eg-custom-item-options">
					<div id="esg-item-skin-elements-settings" class="eg-options-tab for-blank">
						<label for="post-link"><?php esc_html_e('Link To', 'essential-grid'); ?></label><input type="text" name="post-link" value="" />
						<div id="eg-custom-for-blank-wrap">
							<div class="div13"></div>
							<label><?php esc_html_e('Filter(s)', 'essential-grid'); ?></label><!--
							--><input class="custom-filter-hiddeninput esg-display-none" type="text" name="custom-filter" value="" /><!--
							--><select name="custom-filter-list" class="custom-filter-list" multiple="multiple"></select><!--
							--><div class="div13"></div><!--
							--><label></label><div class="esg-btn esg-purple add_new_custom_category_quick"><i class="material-icons">add</i><?php esc_html_e('Add New Filter', 'essential-grid'); ?></div>

						</div>

						<div class="for-blank">
							<div class="div13"></div>
							<label><?php esc_html_e('Cobbles Element Size', 'essential-grid'); ?></label><!--
							--><select name="cobbles-size">
									<option value="1:1"><?php esc_html_e('width 1, height 1', 'essential-grid'); ?></option>
									<option value="1:2"><?php esc_html_e('width 1, height 2', 'essential-grid'); ?></option>
									<option value="1:3"><?php esc_html_e('width 1, height 3', 'essential-grid'); ?></option>
									<option value="2:1"><?php esc_html_e('width 2, height 1', 'essential-grid'); ?></option>
									<option value="2:2"><?php esc_html_e('width 2, height 2', 'essential-grid'); ?></option>
									<option value="2:3"><?php esc_html_e('width 2, height 3', 'essential-grid'); ?></option>
									<option value="3:1"><?php esc_html_e('width 3, height 1', 'essential-grid'); ?></option>
									<option value="3:2"><?php esc_html_e('width 3, height 2', 'essential-grid'); ?></option>
									<option value="3:3"><?php esc_html_e('width 3, height 3', 'essential-grid'); ?></option>
								</select>
							<div class="div13"></div>
						</div>
						<?php $skins = Essential_Grid_Item_Skin::get_essential_item_skins('all', false);?>
						<label><?php esc_html_e('Alternate Item Skin', 'essential-grid'); ?></label><!--
						--><select name="use-skin">
								<option value="-1"><?php esc_html_e('-- Default Skin --', 'essential-grid'); ?></option>
								<?php
								if(!empty($skins)){
									foreach($skins as $skin){
										echo '<option value="'.esc_attr($skin['id']).'">'.esc_html($skin['name']).'</option>'."\n";
									}
								}
								?>
						</select>
						<div class="div13"></div>
						<div>
						<label><?php esc_html_e('Item Skin Modifications', 'essential-grid'); ?></label><!--
						--><div class="esg-btn esg-purple eg-add-custom-meta-field" id="eg-add-custom-meta-field-custom"><?php esc_html_e('Add New Custom Skin Rule', 'essential-grid'); ?></div>
						</div>
						<div class="eg-advanced-param" id="eg-advanced-param-custom"></div>
					</div>
					<div id="esg-item-skin-elements-datas" class="eg-options-tab">
						<?php 
						
						$elements = $item_elements->getElementsForDropdown();
						$p_lang = ['post' => esc_attr__('Item Data', 'essential-grid'), 'woocommerce' => esc_attr__('WooCommerce', 'essential-grid')];
						
						foreach($elements as $type => $element){
							?>
							<strong class="esg-font-size-14"><?php echo esc_html($p_lang[$type]); ?></strong>
							<div id="esg-item-skin-elements-<?php echo esc_attr($type); ?>">
							<?php
							foreach($element as $handle => $itm){

								if(!isset($itm['type'])) $itm['type'] = "empty";
								
								switch($itm['type']) {
										
									case 'image';
										echo '<div>';
										echo '<label for="'.esc_attr($handle).'">'.esc_html($itm['name']).'</label>';
										echo '<input type="hidden" value="" name="eg-' . esc_attr($handle) . '" id="eg-' . esc_attr($handle) . '-cm" />';
										echo '<div class="esg-btn esg-purple eg-image-add" data-setto="eg-' . esc_attr($handle) . '-cm">' . esc_attr__('Choose Image', 'essential-grid') . '</div> ';
										echo '<div class="esg-btn esg-red eg-image-clear" data-setto="eg-' . esc_attr($handle) . '-cm">' . esc_attr__('Remove Image', 'essential-grid') . '</div>';
										echo '<div class="esg-custom-image-wrapper"><img id="eg-' . esc_attr($handle) . '-cm-img" src="" class="esg-custom-image-img" alt=""></div>';
										echo '</div><div class="div13"></div>';
									break;
									
									case 'revslider';

										if(class_exists('RevSliderSlider')) {
											
											$rev_slider = new RevSliderSlider();
											if(method_exists($rev_slider, 'get_slider_for_admin_menu')) {
											
												$sliders = $rev_slider->get_slider_for_admin_menu();
												if(!empty($sliders)) {
													echo '<div>';	
													echo '<label for="'.esc_attr($handle).'">'.esc_html($itm['name']).'</label>';
													echo '<select name="' . esc_attr($handle) . '">';
													echo '<option value="">--- Choose Slider ---</option>';
													
													foreach($sliders as $id => $val) {
														if(!empty($val['title'])) {
															echo '<option value="' . esc_attr($id) . '">' . esc_html($val['title']) . '</option>';
														}
													}
													echo '</select></div><div class="div13"></div>';
												}
											}	
										}
									
									break;
									
									case 'essgrid':
										$grids = Essential_Grid_Db::get_entity('grids')->get_grids();
										if(!empty($grids)) {
											echo '<div>';
											echo '<label  for="'.esc_attr($handle).'">'.esc_html($itm['name']).'</label>';
											echo '<select name="' . esc_attr($handle) . '">';
											echo '<option value="">--- Choose Grid ---</option>';
											foreach($grids as $grid) {
												echo '<option value="' . esc_attr($grid->handle) . '">' . esc_html($grid->name) . '</option>';
											}
											echo '</select></div><div class="div13"></div>';
										}
									
									break;
									
									default:
									
										echo '<div><label for="'.esc_attr($handle).'">'.esc_html($itm['name']).'</label><input type="text" name="'.esc_attr($handle).'" value="" /></div><div class="div13"></div>';
									
								}
								
							}
							?>
							</div>
							<?php
						}

						$custom_meta = $meta->get_all_meta(false);
						if(!empty($custom_meta)){
							
							foreach($custom_meta as $cmeta){
								?>
								<label ><?php echo esc_html($cmeta['name']); ?></label>
								<?php
									switch($cmeta['type']){
										case 'text':
											echo '<input type="text" name="eg-'.esc_attr($cmeta['handle']).'" value="" />';
										break;
										case 'select':
										case 'multi-select':
											$select_name = 'eg-' . $cmeta['handle'] . ($cmeta['type'] == 'multi-select' ? '[]' : '');
											$el = $meta->prepare_select_by_string($cmeta['select']);
											echo '<select name="' . esc_attr($select_name) . '"';
											if($cmeta['type'] == 'multi-select') echo ' multiple="multiple" size="5"';
											echo '>';
											if(!empty($el) && is_array($el)){
												if($cmeta['type'] == 'multi-select'){
													echo '<option value="">'.esc_attr__('---', 'essential-grid').'</option>';
												}
												foreach($el as $ele){
													echo '<option value="'.esc_attr($ele).'">'.esc_html($ele).'</option>';
												}
											}
											echo '</select>';
										break;
										case 'image':
											$var_src = '';
											?>
											<input type="hidden" value="" name="eg-<?php echo esc_attr($cmeta['handle']); ?>" id="eg-<?php echo esc_attr($cmeta['handle']).'-cm'; ?>" />
											<div class="esg-btn esg-purple eg-image-add" data-setto="eg-<?php echo esc_attr($cmeta['handle']).'-cm'; ?>"><?php esc_html_e('Choose Image', 'essential-grid'); ?></div>
											<div class="esg-btn esg-red eg-image-clear" data-setto="eg-<?php echo esc_attr($cmeta['handle']).'-cm'; ?>"><?php esc_html_e('Remove Image', 'essential-grid'); ?></div>
											<div class="esg-custom-image-wrapper"><img id="eg-<?php echo esc_attr($cmeta['handle']).'-cm'; ?>-img" src="<?php echo esc_url($var_src); ?>" <?php echo ($var_src == '') ? 'class="esg-custom-image-img"' : ''; ?> alt=""></div>
											<?php
										break;
									}
									?>
								<div class="div13"></div>
								<?php
							}
						}
					?>
					</div>
					<div id="esg-item-skin-elements-other" class="eg-options-tab">
						<label for="image-fit"><?php esc_html_e('Image Fit', 'essential-grid'); ?></label><!--
						--><select name="image-fit">
							<option value="-1"><?php esc_html_e('-- Default Fit --', 'essential-grid'); ?></option>
							<option value="contain"><?php esc_html_e('Contain', 'essential-grid'); ?></option>
							<option value="cover"><?php esc_html_e('Cover', 'essential-grid'); ?></option>
						</select>
						<div class="div13"></div>
						
						<label for="image-repeat"><?php esc_html_e('Image Repeat', 'essential-grid'); ?></label><!--
						--><select name="image-repeat">
							<option value="-1"><?php esc_html_e('-- Default Repeat --', 'essential-grid'); ?></option>
							<option value="no-repeat"><?php esc_html_e('no-repeat', 'essential-grid'); ?></option>
							<option value="repeat"><?php esc_html_e('repeat', 'essential-grid'); ?></option>
							<option value="repeat-x"><?php esc_html_e('repeat-x', 'essential-grid'); ?></option>
							<option value="repeat-y"><?php esc_html_e('repeat-y', 'essential-grid'); ?></option>
						</select>
						<div class="div13"></div>
						<label for="image-align-horizontal"><?php esc_html_e('Horizontal Align', 'essential-grid'); ?></label><!--
						--><select name="image-align-horizontal">
							<option value="-1"><?php esc_html_e('-- Horizontal Align --', 'essential-grid'); ?></option>
							<option value="left"><?php esc_html_e('Left', 'essential-grid'); ?></option>
							<option value="center"><?php esc_html_e('Center', 'essential-grid'); ?></option>
							<option value="right"><?php esc_html_e('Right', 'essential-grid'); ?></option>
						</select>
						<div class="div13"></div>
						<label for="image-align-vertical"><?php esc_html_e('Vertical Align', 'essential-grid'); ?></label><!--
						--><select name="image-align-vertical">
							<option value="-1"><?php esc_html_e('-- Vertical Align --', 'essential-grid'); ?></option>
							<option value="top"><?php esc_html_e('Top', 'essential-grid'); ?></option>
							<option value="center"><?php esc_html_e('Center', 'essential-grid'); ?></option>
							<option value="bottom"><?php esc_html_e('Bottom', 'essential-grid'); ?></option>
						</select>
					</div>
				</div>
				<?php
				do_action('essgrid_edit_custom_element_dialog_post');
				?>
			</form>
			<script type="text/javascript">
				
				<?php 
				
					$advanced = [];
					$base = new Essential_Grid_Base();
					$item_skin = new Essential_Grid_Item_Skin();
					$item_elements = new Essential_Grid_Item_Element();
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
					
				?>
				
				AdminEssentials.setInitSkinsJson(<?php echo (!empty($advanced) ? wp_json_encode($advanced) : '{}'); ?>);
				AdminEssentials.setInitStylingJson(<?php echo (!empty($eg_elements) ? wp_json_encode($eg_elements) : '{}'); ?>);
			
				jQuery('.eg-image-add').on('click',function(e) {
					e.preventDefault();
					AdminEssentials.upload_image_img(jQuery(this).data('setto'));
					
					return false; 
				});
				
				jQuery('.eg-image-clear').on('click',function(e) {
					e.preventDefault();
					var setto = jQuery(this).data('setto');
					jQuery('#'+setto).val('');
					jQuery('#'+setto+'-img').attr("src","").hide();
					return false; 
				});
				
				jQuery('#eg-custom-choose-from-image-library').on('click',function(e) {
					e.preventDefault();
					AdminEssentials.upload_image_img(jQuery(this).data('setto'));

					return false; 
				});
				
				jQuery('#eg-custom-clear-from-image-library').on('click',function(e) {
					e.preventDefault();
					
					jQuery('#esg-custom-image-src').val('');
					jQuery('#esg-custom-image').val('');
					jQuery('#esg-custom-image-img').attr("src","").hide();
					return false; 
				});

				<?php
				do_action('essgrid_edit_custom_element_dialog_script');
				?>
			</script>
		</div>
		<?php
	}
			
	/**
	 * Filter Dialog Box
	 * @since    2.1.0
	 */
	public static function filter_select_dialog(){
		?>
		<div id="filter-select-dialog-wrap" class="essential-dialog-wrap essential-dialog-padding esg-display-none" title="<?php esc_attr_e('Select Filter', 'essential-grid'); ?>">
			<select id="eg-filter-select-box" class="esg-w-350" name="custom-filter-select" multiple="multiple" size="10">
			</select>
			<?php do_action('essgrid_filter_select_dialog'); ?>
		</div>
		<?php
	}

	/**
	 * CUSTOM Filter Dialog Box
	 * @since    2.1.0
	 */
	public static function filter_custom_dialog(){
		?>
		<div id="filter-custom-dialog-wrap" class="essential-dialog-wrap essential-dialog-padding esg-display-none" title="<?php esc_attr_e('Custom Filter', 'essential-grid'); ?>">
			<label><?php esc_html_e('Custom category', 'essential-grid'); ?></label><!--
			--><input type="text" class="esg-custom-category-name-editor" name="esg-custom-category-name-editor" value="New Category" />
			<?php do_action('essgrid_filter_custom_dialog'); ?>
		</div>
		<?php
	}

	/**
	 * CUSTOM Filter Dialog Box
	 * @since    2.1.0
	 */
	public static function filter_delete_dialog(){
		?>
		<div id="filter-delete-dialog-wrap" class="essential-dialog-wrap essential-dialog-padding esg-display-none" title="<?php esc_attr_e('Delete Custom Filter', 'essential-grid'); ?>">
			<?php esc_html_e('Are you sure to delete the', 'essential-grid'); ?><!--
			--><div class="esg-custom-category-todelete"></div><!--
			--><?php esc_html_e('Custom Category?', 'essential-grid'); ?>
			<div class="div13"></div>
			<?php esc_html_e('This will remove this Category from all Items !', 'essential-grid'); ?>
			<?php do_action('essgrid_filter_delete_dialog'); ?>
		</div>
		<?php
	}
}
