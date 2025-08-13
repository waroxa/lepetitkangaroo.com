<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.essential-grid.com/
 * @copyright 2025 ThemePunch
 */

if (!defined('ABSPATH')) exit();

class Essential_Grid_Db_Item_Elements extends Essential_Grid_Db_Abstract
{
	/**
	 * @inheritdoc 
	 */
	public function get_all( $output = ARRAY_A ) {
		return parent::get_all( $output );
	}
	
	/**
	 * @inheritdoc 
	 */
	public function get( $id, $output = ARRAY_A ) {
		$value = wp_cache_get( 'by_id_' . $id, $this->cache_group, false, $found );

		if ( ! $found ) {
			$value = parent::get( $id, $output );
			if (!empty($value)) {
				try {
					$value['settings'] = json_decode( $value['settings'], true );
				} catch (Exception $e) {
					$value['settings'] = '';
				}
				wp_cache_set( 'by_id_' . $id, $value, $this->cache_group, $this->cache_expire );
			}
		}

		return $value;
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
			if (!empty($value)) {
				try {
					$value['settings'] = json_decode( $value['settings'], true );
				} catch (Exception $e) {
					$value['settings'] = '';
				}
				wp_cache_set( 'by_handle_' . $handle, $value, $this->cache_group, $this->cache_expire );
			}
		}

		return $value;
	}
	
	/**
	 * @param string $name
	 *
	 * @return array|null  Database query result or null on failure.
	 */
	public function get_by_name( $name ) {
		global $wpdb;

		if ( empty( $name ) ) return null;

		$value = wp_cache_get( 'by_name_' . $name, $this->cache_group, false, $found );

		if ( ! $found ) {
			// phpcs:ignore
			$value = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE name = %s", $name ), ARRAY_A );
			if (!empty($value)) {
				try {
					$value['settings'] = json_decode( $value['settings'], true );
				} catch (Exception $e) {
					$value['settings'] = '';
				}
				wp_cache_set( 'by_name_' . $name, $value, $this->cache_group, $this->cache_expire );
			}
		}

		return $value;
	}

	/**
	 * @param array  $data
	 * @param string $handle
	 *
	 * @return int|false  The number of rows updated, or false on error.
	 */
	public function update_by_handle($data, $handle) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$result = $wpdb->update( $this->table, $data, ['handle' => $handle] );

		$this->clear_cache();

		return $result;
	}

	/**
	 * @param string $handle
	 *
	 * @return int|false  The number of rows deleted, or false on error.
	 */
	public function delete_by_handle( $handle ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$result = $wpdb->delete( $this->table, ['handle' => $handle] );

		$this->clear_cache();

		return $result;
	}
}
