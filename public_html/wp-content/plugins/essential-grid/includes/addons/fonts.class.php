<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class Essential_Grid_Fonts_Addon extends Essential_Grid_Abstract_Addon
{
	protected string $_handle = 'esg-fonts-addon';
	
	protected string $_import_keys = 'punch-fonts';
	
	/**
	 * addon is missing if original fonts option still in database
	 *
	 * @return bool
	 */
	public function is_missing(): bool {
		if ($this->is_installed_active()) return false;

		$option = get_option('tp-google-fonts', []);
		return !empty($option);
	}

	/**
	 * @parentDoc 
	 */
	public function check_import_keys( array $keys): bool {
		return in_array($this->_import_keys, $keys);
	}
}
