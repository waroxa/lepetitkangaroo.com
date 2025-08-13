<?php

namespace InstagramFeed\Builder\Controls;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Customizer Builder
 * CheckBox List Control
 *
 * @since 6.0
 */
class SB_Checkboxlist_Control extends SB_Controls_Base
{
	/**
	 * Get control type.
	 *
	 * Getting the Control Type
	 *
	 * @return string
	 * @since 6.0
	 * @access public
	 */
	public function get_type()
	{
		return 'checkboxlist';
	}

	/**
	 * Output Control
	 *
	 * @param string $controlEditingTypeModel Control Editing Type Model.
	 * @since 6.0
	 * @access public
	 */
	public function get_control_output($controlEditingTypeModel)
	{
		?>
		<div class="sb-control-checkbox-ctn sbi-fb-fs" v-for="option in control.options"
			 @click.prevent.default="changeCheckboxListValue(control.id, option.value)">
			<div class="sb-control-checkbox"
				 :data-active="<?php echo $controlEditingTypeModel; ?>[control.id].includes(option.value)"></div>
			<div class="sb-control-label sb-small-p sb-dark-text" v-html="option.label"></div>
		</div>
		<?php
	}
}
