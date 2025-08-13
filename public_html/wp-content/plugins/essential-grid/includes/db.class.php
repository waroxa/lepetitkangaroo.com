<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Db
{
	const OPTION_VERSION = 'tp_eg_grids_version';
	
	const TABLE_GRID = 'eg_grids';
	const TABLE_ITEM_SKIN = 'eg_item_skins';
	const TABLE_ITEM_ELEMENTS = 'eg_item_elements';
	const TABLE_NAVIGATION_SKINS = 'eg_navigation_skins';

	const CACHE_GROUP_PREFIX = 'esg_';
	const CACHE_EXPIRE = 3600;

	/**
	 * @var array 
	 */
	protected static $entities = [];

	/**
	 * define ESG tables in global wpdb var
	 * 
	 * @return void
	 */
	public static function define_tables() {
		global $wpdb;

		$wpdb->esg_grids     = $wpdb->prefix . self::TABLE_GRID;
		$wpdb->esg_skins     = $wpdb->prefix . self::TABLE_ITEM_SKIN;
		$wpdb->esg_elements  = $wpdb->prefix . self::TABLE_ITEM_ELEMENTS;
		$wpdb->esg_nav_skins = $wpdb->prefix . self::TABLE_NAVIGATION_SKINS;

		do_action('essgrid_db_define_tables');
	}

	/**
	 * get ESG entity object
	 *
	 * @param string $type
	 *
	 * @return Essential_Grid_Db_Abstract
	 * @throws Exception
	 */
	public static function get_entity( $type ) {
		global $wpdb;

		if ( isset( self::$entities[ $type ] ) ) return self::$entities[ $type ];
		
		switch ( $type ) {
			case 'grids':
				self::$entities[ $type ] = new Essential_Grid_Db_Grid(
					$wpdb->esg_grids,
					self::CACHE_GROUP_PREFIX . $type,
					self::CACHE_EXPIRE
				);
				break;
				
			case 'skins':
				self::$entities[ $type ] = new Essential_Grid_Db_Skin(
					$wpdb->esg_skins,
					self::CACHE_GROUP_PREFIX . $type,
					self::CACHE_EXPIRE
				);
				break;
				
			case 'elements':
				self::$entities[ $type ] = new Essential_Grid_Db_Item_Elements(
					$wpdb->esg_elements,
					self::CACHE_GROUP_PREFIX . $type,
					self::CACHE_EXPIRE
				);
				break;
				
			case 'nav_skins':
				self::$entities[ $type ] = new Essential_Grid_Db_Navigation_Skin(
					$wpdb->esg_nav_skins,
					self::CACHE_GROUP_PREFIX . $type,
					self::CACHE_EXPIRE
				);
				break;
				
			default:
				throw new Exception( 'Unknown ESG entity type!' );
		}

		return self::$entities[ $type ];
	}

	/**
	 * get table with wp prefix
	 *
	 * @param string $table
	 * @param bool $withBackticks
	 *
	 * @return string
	 * @throws Exception
	 * @deprecated 3.1.6
	 */
	public static function get_table($table, $withBackticks = false) {
		
		_deprecated_function( __METHOD__, '3.1.6', '$wpdb->esg_grids' );
		
		global $wpdb;
		
		$t = '';
		$backticks = $withBackticks ? '`': '';
		
		switch ($table) {
			case 'grids':
				$t = self::TABLE_GRID;
				break;
			case 'skins':
				$t = self::TABLE_ITEM_SKIN;
				break;
			case 'elements':
				$t = self::TABLE_ITEM_ELEMENTS;
				break;
			case 'nav_skins':
				$t = self::TABLE_NAVIGATION_SKINS;
				break;
			default:
				Essential_Grid_Base::throw_error(esc_attr__('Unknown table name!', 'essential-grid'));
		}
		
		return $backticks . $wpdb->prefix . $t . $backticks;
	}

	/**
	 * @param string $table
	 *
	 * @return bool
	 */
	public static function is_table_exists($table) {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
		return !is_null($result) && strtolower($result) === strtolower($table);
	}
	
	/**
	 * get db version
	 * @return string
	 */
	public static function get_version() {
		return get_option(self::OPTION_VERSION, '0.99');
	}
	
	/**
	 * update db version
	 *
	 * @param string $new_version
	 *
	 * @return void
	 */
	public static function update_version($new_version)
	{
		update_option(self::OPTION_VERSION, $new_version);
	}

	/**
	 * Check if the tables could be properly created, by checking if TABLE_GRID exists AND table version is latest!
	 * 
	 * @return bool
	 */
	public static function check_table_exist_and_version() {
		global $wpdb;
		
		if (version_compare(ESG_REVISION, self::get_version(), '>')) return false;
		return self::is_table_exists($wpdb->esg_grids);
	}

	/**
	 * Create/Update Database Tables
	 * @return bool
	 */
	public static function create_tables($networkwide = false) {
		global $wpdb;

		$created = false;

		if (function_exists('is_multisite') && is_multisite() && $networkwide) { //do for each existing site
			// Get all blog ids and create tables
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$created = self::_create_tables();
				if ($created === false) {
					return false;
				}
				// 2.2.5
				restore_current_blog();
			}
		} else { //no multisite, do normal installation
			$created = self::_create_tables();
		}

		return $created;
	}

	/**
	 * Create Tables
	 * @return bool
	 */
	public static function _create_tables() {
		global $wpdb;

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$charset_collate = $wpdb->get_charset_collate();
		//Create/Update Grids Database
		$esg_recreate_database = isset($_GET['esg_recreate_database']) ? sanitize_text_field(wp_unslash($_GET['esg_recreate_database'])) : '';
		$force = wp_verify_nonce($esg_recreate_database, 'Essential_Grid_recreate_db');
		$grid_ver = $force ? '0.99' : self::get_version();
		
		if (version_compare($grid_ver, '1', '<')) {
			if (!self::is_table_exists($wpdb->esg_grids)) {
				$sql = "CREATE TABLE {$wpdb->esg_grids} (
					id mediumint(6) NOT NULL AUTO_INCREMENT,
					name VARCHAR(191) NOT NULL,
					handle VARCHAR(191) NOT NULL,
					postparams TEXT NOT NULL,
					params TEXT NOT NULL,
					layers TEXT NOT NULL,
					UNIQUE KEY id (id),
					UNIQUE (handle)
					) $charset_collate;";

				dbDelta($sql);
			}

			if (!self::is_table_exists($wpdb->esg_skins)) {
				$sql = "CREATE TABLE {$wpdb->esg_skins} (
					id mediumint(6) NOT NULL AUTO_INCREMENT,
					name VARCHAR(191) NOT NULL,
					handle VARCHAR(191) NOT NULL,
					params TEXT NOT NULL,
					layers TEXT NOT NULL,
					settings TEXT,
					UNIQUE KEY id (id),
					UNIQUE (name),
					UNIQUE (handle)
					) $charset_collate;";

				dbDelta($sql);
			}

			if (!self::is_table_exists($wpdb->esg_elements)) {
				$sql = "CREATE TABLE {$wpdb->esg_elements} (
					id mediumint(6) NOT NULL AUTO_INCREMENT,
					name VARCHAR(191) NOT NULL,
					handle VARCHAR(191) NOT NULL,
					settings TEXT NOT NULL,
					UNIQUE KEY id (id),
					UNIQUE (handle)
					) $charset_collate;";

				dbDelta($sql);
			}

			if (!self::is_table_exists($wpdb->esg_nav_skins)) {
				$sql = "CREATE TABLE {$wpdb->esg_nav_skins} (
					id mediumint(6) NOT NULL AUTO_INCREMENT,
					name VARCHAR(191) NOT NULL,
					handle VARCHAR(191) NOT NULL,
					css TEXT NOT NULL,
					UNIQUE KEY id (id),
					UNIQUE (handle)
					) $charset_collate;";

				dbDelta($sql);
			}

			//check if a table was created, if not return false and return an error
			if (!self::is_table_exists($wpdb->esg_grids)) {
				return false;
			}

			if($force === false) self::update_version('1');
			$grid_ver = '1';
		}

		//Change database on certain release? No Problem, use the following:
		//change layers to MEDIUMTEXT from TEXT so that more layers can be added (fix for limited entries on custom grids)
		if (version_compare($grid_ver, '1.02', '<')) {
			$sql = "CREATE TABLE {$wpdb->esg_grids} (
				layers MEDIUMTEXT NOT NULL
				) $charset_collate;";

			dbDelta($sql);

			//check if a table was created, if not return false and return an error
			if (!self::is_table_exists($wpdb->esg_grids)) {
				return false;
			}

			if($force === false) self::update_version('1.02');
			$grid_ver = '1.02';
		}

		//change more entries to MEDIUMTEXT so that can be stored to prevent loss of data/errors
		if (version_compare($grid_ver, '1.03', '<')) {
			$sql = "CREATE TABLE {$wpdb->esg_skins} (
				layers MEDIUMTEXT NOT NULL
				) $charset_collate;";

			dbDelta($sql);

			$sql = "CREATE TABLE {$wpdb->esg_nav_skins} (
				css MEDIUMTEXT NOT NULL
				) $charset_collate;";

			dbDelta($sql);

			$sql = "CREATE TABLE {$wpdb->esg_elements} (
				settings MEDIUMTEXT NOT NULL
				) $charset_collate;";

			dbDelta($sql);

			//check if a table was created, if not return false and return an error
			if (!self::is_table_exists($wpdb->esg_skins)) {
				return false;
			}

			if($force === false) self::update_version('1.03');
			$grid_ver = '1.03';
		}

		//Add new column settings, as for 2.0 you can add favorite grids
		if (version_compare($grid_ver, '2.1', '<')) {
			$sql = "CREATE TABLE {$wpdb->esg_grids} (
				settings TEXT NULL
				last_modified DATETIME
				) $charset_collate;";

			dbDelta($sql);

			//check if a table was created, if not return false and return an error
			if (!self::is_table_exists($wpdb->esg_grids)) {
				return false;
			}

			if($force === false) self::update_version('2.1');
			$grid_ver = '2.1';
		}

		if (version_compare($grid_ver, '2.2', '<')) {
			$sql = "CREATE TABLE {$wpdb->esg_nav_skins} (
				navversion VARCHAR(191)
				) $charset_collate;";

			dbDelta($sql);

			//check if a table was created, if not return false and return an error
			if (!self::is_table_exists($wpdb->esg_nav_skins)) {
				return false;
			}

			if($force === false) self::update_version('2.2');
			$grid_ver = '2.2';
		}

		do_action('essgrid__create_tables', $grid_ver);

		return true;
	}

	/**
	 * Get Essential Grid ID by alias
	 * 
	 * @param string $alias
	 * 
	 * @return int
	 * @deprecated 3.1.6
	 */
	public static function get_id_by_alias( $alias ) {
		_deprecated_function( __METHOD__, '3.1.6', "Essential_Grid_Db::get_entity('grids')->get_id_by_alias()" );
		return Essential_Grid_Db::get_entity('grids')->get_id_by_alias( $alias );
	}

	/**
	 * Get Essential Grid alias by ID
	 * 
	 * @param int $id
	 *
	 * @return string
	 * @deprecated 3.1.6
	 */
	public static function get_alias_by_id( $id ) {
		_deprecated_function( __METHOD__, '3.1.6', "Essential_Grid_Db::get_entity('grids')->get_alias_by_id()" );
		return Essential_Grid_Db::get_entity('grids')->get_alias_by_id( $id );
	}

	/**
	 * Get Grid by ID from Database
	 * 
	 * @param int $id
	 * @param bool $raw
	 * 
	 * @return bool|array
	 * @deprecated 3.1.6
	 */
	public static function get_essential_grid_by_id( $id, $raw = false ) {
		_deprecated_function( __METHOD__, '3.1.6', "Essential_Grid_Db::get_entity('grids')->get_grid_by_id()" );
		return Essential_Grid_Db::get_entity('grids')->get_grid_by_id( $id, $raw );
	}

	/**
	 * Get Grid by Handle from Database
	 * 
	 * @param string $handle
	 * @param bool $raw
	 *
	 * @return bool|array
	 * @deprecated 3.1.6
	 */
	public static function get_essential_grid_by_handle( $handle, $raw = false ) {
		_deprecated_function( __METHOD__, '3.1.6', "Essential_Grid_Db::get_entity('grids')->get_grid_by_handle()" );
		return Essential_Grid_Db::get_entity('grids')->get_grid_by_handle( $handle, $raw );
	}

	/**
	 * Get all Grids in Database
	 *
	 * @param bool|array $order
	 * @param bool $raw
	 * 
	 * @return array
	 * @deprecated 3.1.6
	 */
	public static function get_essential_grids($order = false, $raw = true) {
		_deprecated_function( __METHOD__, '3.1.6', "Essential_Grid_Db::get_entity('grids')->get_grids()" );
		return Essential_Grid_Db::get_entity('grids')->get_grids( $order, $raw );
	}

}
