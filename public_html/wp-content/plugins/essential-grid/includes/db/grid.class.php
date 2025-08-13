<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Db_Grid extends Essential_Grid_Db_Abstract
{
	/**
	 * @inheritdoc 
	 */
	public function get_all( $output = ARRAY_A ) {
		return parent::get_all( $output );
	}

	/**
	 * Get grids
	 *
	 * @param bool|array $order
	 * @param bool $raw
	 *
	 * @return array
	 */
	public function get_grids($order = false, $raw = true) {
		global $wpdb;

		$cache_key = 'all_order_' . md5( serialize( $order ) . serialize( $raw ) );
		$value = wp_cache_get( $cache_key , $this->cache_group, false, $found );
		if ( $found ) {
			return $value;
		}

		$order_fav = false;
		$sql = "SELECT * FROM {$wpdb->esg_grids} ";
		
		if (!empty($order)) {
			$ordertype = key($order);
			$orderby = reset($order);
			if ($ordertype != 'favorite' ) {
				$order_by_str = sanitize_sql_orderby( "{$ordertype} {$orderby}" );
				if ( $order_by_str !== false ) {
					$sql .= ' ORDER BY ' . $order_by_str;
				}
			} else {
				$order_fav = $orderby;
			}
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$grids = $wpdb->get_results( $sql );
		if (!is_array($grids)) $grids = [];

		// check if we order by favorites here
		if ( $order_fav !== false ) {
			// Separate the grids into favorite and non-favorite using array_filter
			$favorites = array_filter($grids, function($grid) {
				$settings = json_decode($grid->settings, true);
				return isset($settings['favorite']) && $settings['favorite'] === 'true';
			});
			$nonFavorites = array_filter($grids, function($grid) {
				$settings = json_decode($grid->settings, true);
				return !isset($settings['favorite']) || $settings['favorite'] === 'false';
			});
			
			if ($order_fav == 'ASC') {
				$grids = array_merge($favorites, $nonFavorites);
			} else {
				$grids = array_merge($nonFavorites, $favorites);
			}
		}

		if ($raw === false && !empty($grids)) {
			foreach ($grids as $k => $grid) {
				$grids[$k] = $this->_decode_params($grid);
			}
		}

		wp_cache_set( $cache_key, $grids, $this->cache_group, $this->cache_expire );

		return $grids;
	}

	/**
	 * get array of $index -> $value from list of essential grids
	 *
	 * @param string $index
	 * @param string $value
	 * @param int|null $exceptID
	 *
	 * @return array
	 */
	public function get_grids_column( $index = 'id', $value = 'name', $exceptID = null) {
		$result = [];
		$grids = $this->get_grids();

		foreach ($grids as $grid) {
			//filter by except
			if (!empty($exceptID) && $exceptID == $grid->$index) continue;

			$result[ $grid->$index ] = $grid->$value;
		}

		return $result;
	}

	/**
	 * @inheritdoc
	 */
	public function get( $id, $output = ARRAY_A ) {
		return parent::get( $id, $output );
	}

	/**
	 * @param string $handle
	 *
	 * @return array|null  Database query result or null on failure.
	 */
	public function get_by_handle( $handle ) {
		global $wpdb;

		if ( empty( $handle ) ) return null;

		$value = wp_cache_get( 'by_handle_' . $handle, $this->cache_group, false, $found );

		if ( ! $found ) {
			// phpcs:ignore
			$value = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE handle = %s", $handle ), ARRAY_A );
			if ( !empty( $value ) ) {
				wp_cache_set( 'by_handle_' . $handle, $value, $this->cache_group, $this->cache_expire );
			}
		}

		return $value;
	}

	/**
	 * Get Essential Grid ID by alias
	 *
	 * @param string $alias
	 *
	 * @return int
	 */
	public function get_id_by_alias( $alias ) {
		$grid = $this->get_by_handle( $alias );
		return empty( $grid['id'] ) ? 0 : $grid['id'];
	}

	/**
	 * Get Essential Grid alias by ID
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public function get_alias_by_id( $id ) {
		$grid = $this->get( $id );
		return empty($grid['handle']) ? '' : $grid['handle'];
	}

	/**
	 * Get Grid by ID from Database
	 *
	 * @param int $id
	 * @param bool $raw
	 *
	 * @return bool|array
	 */
	public function get_grid_by_id( $id, $raw = false ) {

		if ( empty( $id ) ) return false;

		$cache_key = 'by_id_' . $id . '_' . md5( serialize( $raw ) );
		$grid = wp_cache_get( $cache_key, $this->cache_group, false, $found );
		if ( $found ) {
			return $grid;
		}
		
		$grid = $this->get( $id );
		if ( empty( $grid ) ) {
			$grid = false;
			wp_cache_set( $cache_key, $grid, $this->cache_group, $this->cache_expire );
			return $grid;
		}

		if (!$raw) $grid = $this->_decode_params($grid);
		
		wp_cache_set( $cache_key, $grid, $this->cache_group, $this->cache_expire );

		return $grid;
	}
	
	/**
	 * Get Grid by Handle from Database
	 *
	 * @param string $handle
	 * @param bool $raw
	 *
	 * @return bool|array
	 */
	public function get_grid_by_handle( $handle, $raw = false ) {

		if ( empty( $handle ) ) return false;

		$cache_key = 'by_handle_' . $handle . '_' . md5( serialize( $raw ) );
		$grid = wp_cache_get( $cache_key, $this->cache_group, false, $found );
		if ( $found ) {
			return $grid;
		}
		
		$grid = $this->get_by_handle( $handle );
		if ( empty( $grid ) ) {
			$grid = false;
			wp_cache_set( $cache_key, $grid, $this->cache_group, $this->cache_expire );
			return $grid;
		}

		if (!$raw) $grid = $this->_decode_params($grid);
		
		wp_cache_set( $cache_key, $grid, $this->cache_group, $this->cache_expire );

		return $grid;
	}

	/**
	 * @return int
	 */
	public function get_max_id() {
		global $wpdb;

		// phpcs:ignore
		$value = $wpdb->get_row( "SELECT id FROM {$this->table} ORDER BY id DESC LIMIT 1", ARRAY_A );
		if ( !empty( $value ) ) return $value['id'];
		
		return 0;
	}

	/**
	 * @param array|object $grid
	 *
	 * @return array|object
	 */
	protected function _decode_params($grid) {
		$isObj = is_object($grid);
		if ($isObj) {
			$grid = (array)$grid;
		}

		if ( is_array( $grid['params'] ) ) return $grid;

		$keys = ['postparams', 'params', 'layers', 'settings'];
		foreach ($keys as $k) {
			$v = empty($grid[$k]) ? '' : json_decode($grid[$k], true);
			$grid[$k] = (json_last_error() === JSON_ERROR_NONE && !empty($v)) ? $v : [];
		}

		return $isObj ? (object)$grid : $grid;
	}

}
