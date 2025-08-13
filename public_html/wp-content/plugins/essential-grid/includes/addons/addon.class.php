<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

abstract class Essential_Grid_Abstract_Addon
{
	protected string $_handle;
	
	protected string $_import_keys;
	
	public function __construct()
	{
	}

	/**
	 * @return string
	 */
	public function get_handle(): string {
		return $this->_handle;
	}

	/**
	 * @return array
	 */
	public function get_options(): array {
		return get_option($this->_handle . '_options', []);
	}
	
	/**
	 * is addon installed and activated
	 *
	 * @return bool
	 */
	public function is_installed_active(): bool {
		$esg_addons = Essential_Grid_Addons::instance();
		$addons = $esg_addons->get_addons_list();
		return !empty($addons[$this->_handle]) && $addons[$this->_handle]->installed && $addons[$this->_handle]->active;
	}

	/**
	 * is addon used by any grid
	 * 
	 * @return bool
	 */
	public function is_used_in_grids(): bool {
		$arrGrids = Essential_Grid_Db::get_entity('grids')->get_grids(false, false);
		foreach ($arrGrids as $grid) {
			if (isset($grid->params['addons'][$this->_handle])) return true;
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function get_import_keys(): string {
		return $this->_import_keys;
	}

	/**
	 * check if import key can be processed by addon
	 *
	 * @param array $keys
	 *
	 * @return bool
	 */
	public function check_import_keys( array $keys): bool {
		return false;
	}

	/**
	 * is addon missing
	 *
	 * @return bool
	 */
	public function is_missing(): bool {
		if ($this->is_installed_active()) return false;
		return $this->is_used_in_grids();
	}
}
