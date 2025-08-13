<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class Essential_Grid_Rightclick_Addon extends Essential_Grid_Abstract_Addon
{
	protected string $_handle = 'esg-rightclick-addon';

	/**
	 * addon is missing if original rightclick option still in database
	 *
	 * @return bool
	 */
	public function is_missing(): bool {
		if ($this->is_installed_active()) return false;
		
		$options = get_option('tp_eg_rightclick', []);
		return isset($options['rightclick-enabled']) && $options['rightclick-enabled'] === 'true';
	}
}
