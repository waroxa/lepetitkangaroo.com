<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

wp_enqueue_style('esg-admin-settings-styles', ESG_PLUGIN_URL.'public/assets/css/settings.css', [], ESG_REVISION );
?>
<div id="eg-element-settings-wrap">
	 <form id="">
		<div class="eg-pbox esg-box"><h3><span><?php esc_html_e('Element Settings', 'essential-grid'); ?></span><span class="eg-pbox-arrow"></span></h3>
			<div class="inside padding-10">
				<div id="eg-element-settings-tabs">
					<ul>
						<li><a href="#eg-element-source"><?php esc_html_e('Source', 'essential-grid'); ?></a></li>
						<li><a href="#eg-element-style"><?php esc_html_e('Style', 'essential-grid'); ?></a></li>
						<li><a href="#eg-element-animation"><?php esc_html_e('Animation', 'essential-grid'); ?></a></li>
					</ul>
					<!-- THE ELEMENT SOURCE SETTING -->
					<div id="eg-element-source">
						<div id="dz-source" data-sort="5">
							<p>
								<label><?php esc_html_e('Source', 'essential-grid'); ?></label>
								<select name="element-source" class="esg-w-180">
									<?php foreach($element_type as $el_cat => $el_type){ ?>
										<option value="<?php echo esc_attr($el_cat); ?>"><?php echo esc_html(ucwords($el_cat)); ?></option>
									<?php } ?>
								</select>
							 </p>
							 <p>
								<label><?php esc_html_e('Element', 'essential-grid'); ?></label>
								<?php foreach($element_type as $el_cat => $el_type){ ?>
									<select name="element-source-<?php echo esc_attr($el_cat); ?>" class="elements-select-wrap esg-w-180">
										<?php foreach($el_type as $ty_name => $ty_values){ ?>
											<option value="<?php echo esc_attr($ty_name); ?>"><?php echo esc_html($ty_values['name']); ?></option>
										<?php } ?>
									</select>
								<?php } ?>
							 </p>
							</div>
					</div>
					
					<!-- THE ELEMENT STYLE SETTINGS -->
					<div id="eg-element-style">
						<p id="dz-float" data-sort="10">
							<label><?php esc_html_e('Float Element', 'essential-grid'); ?></label>
							<input class="input-settings-small element-setting firstinput" type="checkbox" name="element-float" />
						</p>
						<p id="dz-font-size" data-sort="20">
							<label><?php esc_html_e('Font Size', 'essential-grid'); ?></label>
							<span id="element-font-size" class="slider-settings"></span>
							<input class="input-settings-small element-setting" type="text" name="element-font-size" value="6" /> px
						</p>
						<p id="dz-background-color" data-sort="30">
							<label><?php esc_html_e('Background Color', 'essential-grid'); ?></label>
							<input class="element-setting" name="element-background-color" type="text" id="element-background-color" value="" data-default-color="#ffffff">
						</p>
						<p id="dz-padding" data-sort="40">
							<label><?php esc_html_e('Paddings', 'essential-grid'); ?></label>
							<span id="element-padding" class="slider-settings"></span>
							<input class="input-settings-small element-setting" type="text" name="element-padding" value="0" /> px
						</p>
						<p id="dz-margin" data-sort="60">
							<label><?php esc_html_e('Margin', 'essential-grid'); ?></label>
							<span id="element-margin" class="slider-settings"></span>
							<input class="input-settings-small element-setting" type="text" name="element-margin" value="0" /> px
						</p>
						<p id="dz-border" data-sort="70">
							<label><?php esc_html_e('Border', 'essential-grid'); ?></label>
							<span id="element-border" class="slider-settings"></span>
							<input class="input-settings-small element-setting" type="text" name="element-border" value="0" /> px
						</p>
						<p id="dz-height" data-sort="80">
							<label><?php esc_html_e('Height', 'essential-grid'); ?></label>
							<span id="element-height" class="slider-settings"></span>
							<input class="input-settings-small element-setting" type="text" name="element-height" value="0" /> px
						</p>
						<p id="dz-hideunder" data-sort="90">
							<label><?php esc_html_e('Hide Under Width', 'essential-grid'); ?></label>
							<input class="input-settings-small element-setting firstinput" type="text" name="element-hideunder" value="0" /> px
						</p>
						
						<p id="dz-shadow" data-sort="100">
							<label><?php esc_html_e('Shadow', 'essential-grid'); ?></label>
						</p>
					</div>
					
					<!-- THE ELEMENT ANIMATION SETTINGS -->
					<div id="eg-element-animation">
						<p id="dz-delay" data-sort="50">
							<label><?php esc_html_e('Delay', 'essential-grid'); ?></label>
							<span id="element-delay" class="slider-settings"></span>
							<input class="input-settings-small element-setting" type="text" name="element-delay" value="0" />
						</p>
						<p id="dz-transition" data-sort="90">
							<label><?php esc_html_e('Transition', 'essential-grid'); ?></label>
							<select name="element-transition">
								<?php foreach($transitions as $handle => $name){ ?>
									<option value="<?php echo esc_attr($handle); ?>"><?php echo esc_html($name); ?></option>
								<?php } ?>
							</select>
						</p>
					</div>
				</div>
				
					<p id="dz-delete" data-sort="9999">
					<a id="element-delete-button" class="esg-btn esg-red" href="javascript:void(0);"><i class="eg-icon-trash"></i> <?php esc_html_e('Delete', 'essential-grid'); ?></a>
					<a id="element-save-as-button" class="esg-btn esg-green" href="javascript:void(0);"><i class="eg-icon-save"></i> <?php esc_html_e('Save', 'essential-grid'); ?></a>
				<p>
			</div>
		</div>
	</form>
</div>
