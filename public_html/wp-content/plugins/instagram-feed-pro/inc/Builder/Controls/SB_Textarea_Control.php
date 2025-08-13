<?php

namespace InstagramFeed\Builder\Controls;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Customizer Builder
 * TextArea Field Control
 *
 * @since 6.0
 */
class SB_Textarea_Control extends SB_Controls_Base
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
		return 'textarea';
	}

	/**
	 * Output Control
	 *
	 * @param string $controlEditingTypeModel Control Editing Type Modal.
	 * @since 6.0
	 * @access public
	 */
	public function get_control_output($controlEditingTypeModel)
	{
		?>
		<div class="sb-control-textarea-ctn sbi-fb-fs">
			<textarea class="sb-control-input-textrea sbi-fb-fs"
					  v-model="<?php echo $controlEditingTypeModel; ?>[control.id]"
					  :placeholder="control.placeholder ? control.placeholder : ''"
					  @focusout.prevent.default="changeSettingValue(false,false,false, control.ajaxAction ? control.ajaxAction : false)"></textarea>
		</div>
		<?php
	}
}
